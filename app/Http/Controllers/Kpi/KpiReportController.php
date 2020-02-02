<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
// use App\Models\User;
use App\Models\Kpi\KpiConfig;
use App\Models\Kpi\KpiConfigRange;
use App\Models\Kpi\KpiProperties;
use App\Models\Kpi\KpiConfigDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use URL;
use DB;

//use excelHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Delegation\DelegationProcess;
use exportHelper;

class KpiReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function monthly_summery(Request $request,$type=''){

        $data['title'] = "Kpi Monthly Summery";
        $monthlyAry = [];
        $empAry = [];
        $data['month_ary'] = [];
        $data['emp_ary'] = [];
        $month_from = '';
        $month_to = '';
        $sys_users_designations_id = "";
        $sys_users_bat_dpid = "";
        $data['posted'] = "";
        //data will come from slotwise report
        $slot_ary = ['below_70','inside_80','inside_90','upper_90'];
        $range_val = "";

        if(isset( $request->month_from) && isset( $request->month_to)){
           // return "here";

            $month_from = $request->month_from;
            $month_to = $request->month_to;
            $sys_users_designations_id = $request->designations_id;
            $sys_users_bat_dpid = $request->sys_users_bat_dpid;
            $range_val = $request->range_val;

//            dd($request->all());

            $report_sql = DB::table('hr_emp_pfp_salary')
                ->select('hr_emp_pfp_salary.sys_users_id',
                    'hr_emp_pfp_salary.salary_month',
                    'hr_emp_pfp_salary.pfp_target_amount',
                    'hr_emp_pfp_salary.pfp_achieve_ratio',
                    'hr_emp_pfp_salary.pfp_earn_amount',
                    'sys_users.name',
                    'sys_users.user_code',
                    'sys_users.id',
                    'bat_distributorspoint.name as point_name',
                    'designations.designations_name'
                )
                ->join('sys_users', 'hr_emp_pfp_salary.sys_users_id', 'sys_users.id')
                ->join('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id')
                ->join('designations', 'sys_users.designations_id', 'designations.designations_id');
                if(!empty($sys_users_designations_id)){
                    $report_sql->whereIn('sys_users.designations_id', $sys_users_designations_id);
                }
                if(!empty($sys_users_bat_dpid)){
                    $report_sql->whereIn('sys_users.bat_dpid', $sys_users_bat_dpid);
                }
                if(!empty($range_val)){
                    if($range_val == 'below_70'){
                        $report_sql ->where('hr_emp_pfp_salary.pfp_achieve_ratio','<', 70 );
                    }
                    elseif($range_val == 'inside_80'){
                        $report_sql ->where('hr_emp_pfp_salary.pfp_achieve_ratio','>=', 71 )
                         ->where('hr_emp_pfp_salary.pfp_achieve_ratio','<=', 80 );
                    }
                    elseif ($range_val == 'inside_90'){
                        $report_sql ->where('hr_emp_pfp_salary.pfp_achieve_ratio','>=', 81 )
                            ->where('hr_emp_pfp_salary.pfp_achieve_ratio','<=', 90 );
                    }
                    elseif ($range_val == 'upper_90'){
                        $report_sql ->where('hr_emp_pfp_salary.pfp_achieve_ratio','>', 90 );
                    }
                }
                $report_sql->where('hr_emp_pfp_salary.salary_month','>=',$month_from )
                ->where('hr_emp_pfp_salary.salary_month','<=',$month_to )
                ->orderBy('hr_emp_pfp_salary.salary_month');
                $session_con = (sessionFilter('url','attendance-entry'));
                $session_con = trim(trim(strtolower($session_con)),'and');
                if($session_con){
                    $report_sql->whereRaw($session_con);
                }

            $report = $report_sql->get();

            //[$month_from, $month_to]
            if(count($report) > 0){
                foreach($report as $info){
//                    $monthlyAry[$info->salary_month] = $info->salary_month;
                    $empAry[$info->sys_users_id]['emp_name'] = $info->name;
                    $empAry[$info->sys_users_id]['emp_code'] = $info->user_code;
                    $empAry[$info->sys_users_id]['designations_name'] = $info->designations_name;
                    $empAry[$info->sys_users_id]['point_name'] = $info->point_name;
                    $empAry[$info->sys_users_id]['emp_id'] = $info->id;
                    $empAry[$info->sys_users_id][$info->salary_month] = $info;
                }
            }

            $start    = new \DateTime($month_from);
            $start->modify('first day of this month');
            $end      = new \DateTime($month_to);
            $end->modify('first day of next month');
            $interval = \DateInterval::createFromDateString('1 month');
            $period   = new \DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                $monthss = $dt->format("Y-m");
                $monthlyAry[$monthss] = $monthss;
            }

            $data['posted'] = $request->all();
            $data['month_ary'] = $monthlyAry;
            $data['emp_ary'] = $empAry;
            $data['month_from'] = $month_from;
            $data['month_to'] = $month_to;
           // dd($empAry);
        }

       // dd($data);

       // return $data['month_ary'];

        if($type=='excel'){
            $file_name='KPI Monthly Summary.xlsx';
            $header_array=[
              [
                  'text'=>'Employee Name',
                  'row'=>2
              ],
              [
                  'text'=>'Designation',
                  'row'=>2
              ],
              [
                  'text'=>'Distributor Point',
                  'row'=>2
              ]
            ];

            $last_stage_header=[
                [
                    'text'=>'Target Amount'
                ],
                [
                    'text'=>'Achivement(%)'
                ],
                [
                    'text'=>'PFP'
                ]
            ];

            foreach ($data['month_ary'] as $key=>$prows){
                $temp_array=[
                    'text'=>date('M, Y',strtotime($key)),
                    'col'=>3,
                    'sub'=>$last_stage_header
                ];


                array_push($header_array,$temp_array);
            }



            $excel_array=array();
            foreach($data['emp_ary'] as $key=>$val){
                $temp=array();
                $temp['emp_name']=$val['emp_name'];
                $temp['designations_name']=$val['designations_name'];
                $temp['point_name']=$val['point_name'];
                $i=0;
                foreach($data['month_ary'] as $month_info){
                    if(array_key_exists($month_info, $val)){
                        $temp['pfp_target_amount'.$i]=$val[$month_info]->pfp_target_amount;
                        $temp['pfp_achieve_ratio'.$i]=$val[$month_info]->pfp_achieve_ratio;
                        $temp['pfp_earn_amount'.$i]=$val[$month_info]->pfp_earn_amount;
                        $i++;
                    }else{
                        $temp['pfp_target_amount'.$i]='--';
                        $temp['pfp_achieve_ratio'.$i]='--';
                        $temp['pfp_earn_amount'.$i]='--';
                        $i++;
                    }

                }
                array_push($excel_array,$temp);
            }
            $excel_array_to_send = [
                'header_array' => $header_array,
                'data_array' => $excel_array,
                'file_name' => $file_name,
                'header_color'=>0
            ];

            //return $data['month_ary'];
            $fileName = exportExcel($excel_array_to_send);
            return response()->json(['status' => 'success', 'file' => $fileName]);
        }
        return view('HR.kpi.monthly_summery', $data);
    }

    public function slotwiseKpiReport(Request $request){

        $data = [];
        $designation_wise_ary = [];
        $designations_id = "";
        $sys_users_bat_dpid = "";

        $prev_month = date('Y-m', strtotime(date('Y-m')." -1 month"));

        if(isset($_POST['submit'])){
            $prev_month = $request->prev_month;
            $designations_id = $request->designations_id;
            $sys_users_bat_dpid = $request->sys_users_bat_dpid;
        }

//        dd($request->all());

        $data_sql = DB::table('hr_emp_pfp_salary')
                ->select('hr_emp_pfp_salary.sys_users_id',
                    'hr_emp_pfp_salary.salary_month',
                    'hr_emp_pfp_salary.pfp_achieve_ratio',
                    'sys_users.designations_id',
                    'sys_users.bat_dpid',
                    'designations.designations_name'
                    )
                ->where('hr_emp_pfp_salary.salary_month', $prev_month)
                ->join('sys_users', 'hr_emp_pfp_salary.sys_users_id', 'sys_users.id')
                ->join('designations', 'sys_users.designations_id', 'designations.designations_id');
                if(!empty($designations_id)){
                    $data_sql->whereIn('sys_users.designations_id', $designations_id);
                }
                if(!empty($sys_users_bat_dpid)){
                    $data_sql->whereIn('sys_users.bat_dpid', $sys_users_bat_dpid);
                }
            $session_con = (sessionFilter('url','attendance-entry'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            if($session_con){
                $data_sql->whereRaw($session_con);
            }
        $data = $data_sql->get();


        foreach($data as $info){
            $designation_wise_ary[$info->designations_id]['designation_name'] = $info->designations_name;
            if($info->pfp_achieve_ratio<70){
                $designation_wise_ary[$info->designations_id]['below_70'][] = $info->pfp_achieve_ratio;
            }
            if($info->pfp_achieve_ratio>70 && $info->pfp_achieve_ratio<=80){
                $designation_wise_ary[$info->designations_id]['inside_80'][] = $info->pfp_achieve_ratio;
            }
            if($info->pfp_achieve_ratio>=81 && $info->pfp_achieve_ratio<=90){
                $designation_wise_ary[$info->designations_id]['inside_90'][] = $info->pfp_achieve_ratio;
            }
            if($info->pfp_achieve_ratio>90){
                $designation_wise_ary[$info->designations_id]['upper_90'][] = $info->pfp_achieve_ratio;
            }

        }

        $report['prev_month'] = $prev_month;
        $report['posted'] = $request->all();
        $report['result'] = $designation_wise_ary;
        $report['key_ary'] = ['below_70','inside_80','inside_90','upper_90'];

        return view('HR.kpi.slotwise_kpi_report', $report);
    }

    public function kpiMonthlySummaryExcel(Request $request){

        $result_ary = unserialize($request->value_data);
        $month_ary = unserialize($request->all_month);

        dd($result_ary, $month_ary);
    }

    public function kpiWiseReport(Request $request,$type=''){

        $report = [];
        $emp_ary = [];
        $result_ary = [];
        $property_ary = [];
        $month_from = '';
        $month_to = '';
        $month_ary = [];
        $sys_users_designations_id = "";
        $sys_users_bat_dpid = "";
        $data['posted'] = "";

        if(isset($request->month_from) && isset($request->month_to)) {
            $month_from = $request->month_from;
            $month_to = $request->month_to;
            $sys_users_designations_id = $request->designations_id;
            $sys_users_bat_dpid = $request->sys_users_bat_dpid;

            $data_sql = DB::table('bat_kpi_target_detail as target_detail')
                ->select(
                    'target_detail.bat_kpi_id',
                    'target_detail.target_month',
                    'sys_users.designations_id',
                    'sys_users.bat_dpid',
                    'sys_users.name',
                    'sys_users.user_code',
                    'target_detail.target_ref_id',
                    'target_detail.target_set',
                    'target_detail.target_achive',
                    'bat_kpis.bat_kpi_name',
                    'designations.designations_name',
                    'bat_distributorspoint.name AS point_name'
                )
                ->join('sys_users', 'target_detail.user_code', 'sys_users.user_code')
                ->join('bat_kpis', 'target_detail.bat_kpi_id', 'bat_kpis.bat_kpi_id')
                ->join('designations', 'sys_users.designations_id', 'designations.designations_id')
                ->join('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id');

                if(!empty($sys_users_designations_id)){
                    $data_sql->whereIn('sys_users.designations_id', $sys_users_designations_id);
                }
                if(!empty($sys_users_bat_dpid)){
                    $data_sql->whereIn('sys_users.bat_dpid', $sys_users_bat_dpid);
                }
                $data_sql->where('target_detail.target_month','>=',$month_from )
                    ->where('target_detail.target_month','<=',$month_to );

            $session_con = (sessionFilter('url','attendance-entry'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            if($session_con){
                $data_sql->whereRaw($session_con);
            }
            $data = $data_sql->orderBY('target_detail.target_month')->get();

            foreach($data as $info){

                $emp_ary[$info->user_code]['user_name'] = $info->name;
                $emp_ary[$info->user_code]['point_name'] = $info->point_name;
                $emp_ary[$info->user_code]['designation'] = $info->designations_name;
                $emp_ary[$info->user_code][$info->target_month][$info->bat_kpi_name][] = $info;
            }

//            dd($emp_ary);

//            $property_ary = [
//                'Volume Target' => 1,
//                'Memo' => 2,
//                'Price Compliance' => 3,
//                'Placement' => 4
//            ];

            $property_ary = DB::table('bat_kpis')->pluck('bat_kpi_id', 'bat_kpi_name');

            $start    = new \DateTime($month_from);
            $start->modify('first day of this month');
            $end      = new \DateTime($month_to);
            $end->modify('first day of next month');
            $interval = \DateInterval::createFromDateString('1 month');
            $period   = new \DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                $monthss = $dt->format("Y-m");
                $month_ary[$monthss] = $monthss;
            }

            foreach($emp_ary as $key=>$val){
                $result_ary[$key]['name'] = $val['user_name'];
                $result_ary[$key]['point_name'] = $val['point_name'];
                $result_ary[$key]['designation'] = $val['designation'];

                foreach($month_ary as $month_info){

                    if(key_exists($month_info, $val)){
                        //                    dd($val[$month_info]);
                        if(!empty($property_ary)){
                            foreach($property_ary as $property_key => $property_val){
                                $total_target = 0;
                                $total_achievement = 0;
                                if(key_exists($property_key, $val[$month_info])){
                                    //                                dd($val[$month_info][$property_key]);
                                    if(!empty($val[$month_info][$property_key])){
                                        foreach($val[$month_info][$property_key] as $product_info){
                                            //                                        dd($product_info);
                                            $total_target += $product_info->target_set;
                                            $total_achievement += $product_info->target_achive;
                                        }
                                    }
                                }

                                $result_ary[$key][$month_info][$property_key]['target'] = $total_target;
                                $result_ary[$key][$month_info][$property_key]['achieve'] = $total_achievement;
                                $achieve_ratio = ($total_achievement > 0 && $total_target >0)?($total_achievement*100)/$total_target:0;
                                $result_ary[$key][$month_info][$property_key]['achieve_ratio'] = $achieve_ratio;
                            }
                        }
                    }
                    else{
                        if(!empty($property_ary)){
                            foreach($property_ary as $property_key => $property_val){

                                $result_ary[$key][$month_info][$property_key]['target'] = 0;
                                $result_ary[$key][$month_info][$property_key]['achieve'] = 0;
                                $result_ary[$key][$month_info][$property_key]['achieve_ratio'] = 0;
                            }
                        }
                    }
                }
            }
        }

//        dd($result_ary);

        if($type=='excel'){

            $file_name = 'KPI Wise Achievement.xlsx';
            $header_array=[
              [
                  'text'=>'Employee Name',
                  'row'=>3
              ],
                [
                    'text'=>'Designation',
                    'row'=>3
                ],
                [
                    'text'=>'Distributors Point',
                    'row'=>3
                ]
            ];

            $last_stage_header=[
              [
                  'text'=>'Target'
              ],
              [
                  'text'=>'Achivement'
              ],
              [
                  'text'=>'Achivement Ratio(%)'
              ]
            ];
            $count_p_ary=0;
            if(!empty($month_ary)){
                $count_p_ary=count($property_ary)*3;
            }
            $second_stage_header=array();
            foreach ($month_ary as $key=>$prows){
               $temp_array=[
                   'text'=>date('M, Y',strtotime($key)),
                   'col'=>$count_p_ary,
                   'sub'=>array()
               ];
               $temp_sub_array=array();
                foreach ($property_ary as $key_property_ary=>$val_property_ary) {
                    $temp_sub_array=[
                      'text'=>  $key_property_ary,
                        'col'=>3,
                        'sub'=>$last_stage_header
                    ];
                    array_push($temp_array['sub'],$temp_sub_array);
               }

                array_push($header_array,$temp_array);
            }


            $excel_array=array();
          $k=0;
            foreach ($result_ary as $key=>$val){
                $a=0;
             $temp=array();
             $temp['name']=$val['name'];
             $temp['designation']=$val['designation'];
             $temp['point_name']=$val['point_name'];
             $j=0;
             foreach ($month_ary as $month_info){
                 if(array_key_exists($month_info, $val)){
                     $i=0;
                     foreach ($val[$month_info] as $month_key => $month_val){
                         $temp['target'.$a]=$month_val['target'];
                         $temp['achive'.$a]=$month_val['achieve'];
                         $temp['achive_ratio'.$a]=$month_val['achieve_ratio'];
                        $a++;
                     }
                 }
                 $j++;
             }
             array_push($excel_array,$temp);
             $k++;
            }
            $excel_array_to_send = [
                'header_array' => $header_array,
                'data_array' => $excel_array,
                'file_name' => $file_name,
                'header_color'=>0
            ];
            // return $excel_array;
            $fileName = exportExcel($excel_array_to_send);
         return response()->json(['status' => 'success', 'file' => $fileName]);

        }

        $report['result_ary'] = $result_ary;
        $report['property_ary'] = $property_ary;
        $report['month_ary'] = $month_ary;
        $report['posted'] = $request->all();
        $report['month_from'] = $month_from;
        $report['month_to'] = $month_to;

//        dd($property_ary, $result_ary, $emp_ary);
        return view('HR.kpi.kpiwise_monthly_report', $report);
    }

    public function dailyAchievement(Request $request,$date=''){
        $posted = $request->all();
        $data=array();
        if(isset($posted['achievement_date'])){
           $achievement_date=explode(' - ',$posted['achievement_date']);
           $start_date=$achievement_date[0];
           $end_date=$achievement_date[1];
           $point=$posted['point'];
           $product_array=array();
           $route_wise_achievement_array=array();
           $kpi_name_array=array();
           $header_array=array();
           $general_info_array=array();

            $segment_sql=DB::table('bat_cats')->where('parent',1)->get();
            foreach ($segment_sql as $segment){
                $product_array['Segment'][$segment->id]=$segment->slug;
            }

            $family_sql=DB::table('bat_cats')->where('parent',168)->get();
            foreach ($family_sql as $family){
                $product_array['Family'][$family->id]=$family->slug;
            }

            $brand_sql=DB::table('bat_products')->where('stts',1)->get();
            foreach ($brand_sql as $brand){
                $product_array['Brand'][$brand->products_id]=$brand->name;
            }


            $achievement_sql=DB::table('bat_daily_kpi_achievements')
                                ->select('bat_daily_kpi_achievements.*','bat_distributorspoint.name as point_name','bat_kpis.bat_kpi_name','sr.name as sr_name','ss.name as ss_name')
                                ->leftjoin('bat_distributorspoint','bat_distributorspoint.id','=','bat_daily_kpi_achievements.bat_dpid')
                               ->leftjoin('bat_kpis','bat_kpis.bat_kpi_id','=','bat_daily_kpi_achievements.bat_kpi_id')
                               ->leftjoin('sys_users as sr','sr.user_code','=','bat_daily_kpi_achievements.sr_user_code')
                                ->leftjoin('sys_users as ss','ss.user_code','=','bat_daily_kpi_achievements.ss_user_code')
                                ->whereBetween('bat_daily_kpi_achievements.achievement_date',[$start_date,$end_date])
                                ->whereIn('bat_daily_kpi_achievements.bat_dpid',$point)->get();
           //dd($achievement_sql);
            foreach ($achievement_sql as $val){
                if(!isset($kpi_name_array[$val->bat_kpi_id])){
                    $kpi_name_array[$val->bat_kpi_id]=$val->bat_kpi_name;
                }
                $general_info_temp=array(
                  'point_name'=>$val->point_name,
                  'route_number'=>$val->route_number,
                  'sr_user_name'=>$val->sr_name,
                  'ss_user_name'=>$val->ss_name,
                  'sr_user_code'=>$val->sr_user_code,
                  'ss_user_code'=>$val->ss_user_code
                );
                $general_info_array[$val->bat_dpid][$val->route_number]=$general_info_temp;
                $header_array[$val->bat_kpi_id][$val->target_type][$val->target_ref_id]=isset($product_array[$val->target_type][$val->target_ref_id])?$product_array[$val->target_type][$val->target_ref_id]:'N/A';

                $route_wise_achievement_array[$val->bat_dpid][$val->route_number][$val->bat_kpi_id][$val->target_type][$val->target_ref_id]=isset($route_wise_achievement_array[$val->bat_dpid][$val->route_number][$val->bat_kpi_id][$val->target_type][$val->target_ref_id])?$route_wise_achievement_array[$val->bat_dpid][$val->route_number][$val->bat_kpi_id][$val->target_type][$val->target_ref_id]+$val->daily_achievement:$val->daily_achievement;

            }
          //  dd($header_array);
         $data['route_wise_achievement_array']=$route_wise_achievement_array;
         $data['kpi_name_array']=$kpi_name_array;
         $data['header_array']=$header_array;
         $data['general_info_array']=$general_info_array;
         $data['inside_search']=1;
         $data['selected_achievement_date']=$posted['achievement_date'];
         $data['selected_point']=$posted['point'];
         $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationTree')->searchForm(implode(',',$data['selected_point']));

        }else{
            $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationTree')->searchForm();
        }

            return view('HR.kpi.daily_kpi_achievement', $data);

    }

}
