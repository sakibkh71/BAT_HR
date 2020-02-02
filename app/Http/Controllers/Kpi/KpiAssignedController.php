<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Kpi\KpiConfig;
use App\Models\Kpi\KpiConfigDetails;
use App\Models\Kpi\KpiProperties;
use App\Models\Kpi\BatCats;
use App\Models\Kpi\BatProducts;
use App\Models\Kpi\KpiAssignedEmployee;
use App\Models\Kpi\KpiAssignedEmpDetails;
use App\Models\Kpi\KpiAssignTemp;
use App\Models\HR\Employee;
use Validator;
use Auth;
use URL;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

//use excelHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Delegation\DelegationProcess;
use exportHelper;

class KpiAssignedController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function index(Request $request,$type=''){
        // ini_set('memory_limit','246M');
        // ini_set('max_execution_time','400');
        // echo ini_get("memory_limit")."\n";
        // echo ini_get("max_execution_time")."\n";

        // $info = DB::table('bat_kpi_assigned_employee')->where('target_month', '2019-07')->where('bat_kpi_properties_id', 1)->first();

//         dd($request->all());

        $month_ary =[];
        $product_ary = [];
        $product_ary_property = [];
        $product_ary_property2 = [];
        $kpi_ary = [];
        $product_ary_property_for_marge = [];
        $details = '';
        $employee_ary = [];
        $property_id = empty($request->property_id)?'':$request->property_id;
        // $property_id = 2;
        $target_month = empty($request->target_month)?'':$request->target_month;
        // $target_month = '2019-08';
        $PRIVILEGE_POINT = explode(",",$request->session()->get('PRIVILEGE_POINT', '0'));
        $point = !empty($request->change_point)?$request->change_point:$PRIVILEGE_POINT[0];
        $designation_id = !empty($request->change_designation_id)?$request->change_designation_id:152;

        $info = DB::table('bat_kpi_target_detail')->orderBy('bat_kpi_target_detail_id', 'DESC')->groupBy('target_month')->get();
        $kpis = DB::table('bat_kpis')->select('bat_kpi_id', 'bat_kpi_name')->get();
        $all_products = BatProducts::pluck('name', 'products_id');
        $all_segment_family = BatCats::whereIn('parent', [1, 168])->pluck('slug', 'id');

//       dd($info, $kpis, $all_products, $all_segment_family);

        if(count($info)){

            foreach($info as $val){
                $month_ary[] = $val->target_month;
            }

            $property_id = !empty($property_id)?$property_id:$info[0]->bat_kpi_id;
            $target_month = !empty($target_month)?$target_month:$info[0]->target_month;

            $specificInfo = DB::table('bat_kpi_target_detail')->where('bat_kpi_id', $property_id)->where('target_month', $target_month)->first();

            if(!empty($specificInfo))
            {
//                $limit = DB::table('bat_kpi_assigned_emp_details')->where('bat_kpi_assigned_employee_id', $info[0]->bat_kpi_assigned_employee_id)->count();

                $qury = DB::table('bat_kpi_target_detail as target_detail')
                    ->join('sys_users', 'target_detail.user_code', '=', 'sys_users.user_code')
                    ->join('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id')
                    ->join('bat_kpis', 'target_detail.bat_kpi_id', '=', 'bat_kpis.bat_kpi_id')
                    ->leftjoin('hr_emp_monthly_variable_salary','hr_emp_monthly_variable_salary.sys_users_id','=','sys_users.id')
                    ->join('bat_distributorspoint','sys_users.bat_dpid','=','bat_distributorspoint.id')
                    ->join('bat_locations as region','bat_distributorspoint.region','=','region.id')
                    ->join('bat_locations as area','bat_distributorspoint.area','=','area.id')
                    ->join('bat_locations as territory','bat_distributorspoint.territory','=','territory.id')
                    ->leftJoin('bat_kpi_target', function ($join){
                        $join->on('target_detail.bat_kpi_id', '=', 'bat_kpi_target.bat_kpi_id');
                        $join->on('target_detail.user_code', '=', 'bat_kpi_target.user_code');
                        $join->on('target_detail.target_month', '=', 'bat_kpi_target.target_month');
                    })
                    ->join('bat_kpi_config_details', function($join){
                        $join->on('target_detail.bat_kpi_id', '=', 'bat_kpi_config_details.bat_kpi_id');
                        $join->on('bat_kpi_target.bat_kpi_configs_id', '=', 'bat_kpi_config_details.bat_kpi_configs_id');
                    });

                $qury->select(
                    'sys_users.id as sys_users_id',
                    'sys_users.max_variable_salary',
                    'sys_users.user_code',
                    'sys_users.name',
                    'sys_users.bat_dpid',
                    'bat_distributorspoint.name as point_name',
                    'region.id as region_id',
                    'region.slug as region_name',
                    'area.id as area_id',
                    'area.slug as area_name',
                    'territory.id as territory_id',
                    'territory.slug as territory_name',
                    'bat_company.company_name',
                    'bat_kpi_target.avg_achivement_ratio',
                    'target_detail.target_set',
                    'target_detail.target_achive',
                    'target_detail.target_type',
                    'target_detail.target_ref_id','hr_emp_monthly_variable_salary.variable_salary_amount',
                    'bat_kpis.bat_kpi_name', 'bat_kpi_config_details.weight');

                $session_con = (sessionFilter('url','attendance-entry'));
                $session_con = trim(trim(strtolower($session_con)),'and');
                if($session_con){
                    $qury->whereRaw($session_con);
                }

                $details = $qury->where('target_detail.target_month', $target_month)
                                ->where('sys_users.bat_dpid', $point)
                                ->where('sys_users.designations_id', $designation_id)
                                ->get();

          //   dd($details); die();
                foreach($details as $key=>$val){

                    $product_ary[$val->target_ref_id] = $val;
                    $kpi_ary[$val->bat_kpi_name] = $val->bat_kpi_name;
//                    $product_ary_property[$val->bat_kpi_name]['weight'] = $val->weight;
//                    $product_ary_property[$val->bat_kpi_name][$val->target_ref_id] = $val;

                    $product_ary_property[$val->bat_kpi_name]['weight'] = $val->weight;
                    $product_ary_property[$val->bat_kpi_name][$val->target_type][$val->target_ref_id] = $val;

                    $product_ary_property_for_marge[$val->bat_kpi_name][$val->target_ref_id]['product_name'] = $val->target_ref_id;
                    $product_ary_property_for_marge[$val->bat_kpi_name][$val->target_ref_id]['target'] = 0;
                    $product_ary_property_for_marge[$val->bat_kpi_name][$val->target_ref_id]['achievement'] = 0;

                    $employee_ary[$val->user_code]['hr_emp_variable_salary']=$val->variable_salary_amount;
                    $employee_ary[$val->user_code]['variable_salary']=$val->max_variable_salary;
                    $employee_ary[$val->user_code]['sys_users_id']=$val->sys_users_id;
                    $employee_ary[$val->user_code]['user_code'] = $val->user_code;
                    $employee_ary[$val->user_code]['user_name'] = $val->name;
                    $employee_ary[$val->user_code]['company_name'] = $val->company_name;
                    $employee_ary[$val->user_code]['total_achivement'] = $val->avg_achivement_ratio;
                    $employee_ary[$val->user_code]['weight'] = '100';
                    $employee_ary[$val->user_code]['point_name'] = $val->point_name;
                    $employee_ary[$val->user_code]['region_name'] = $val->region_name;
                    $employee_ary[$val->user_code]['area_name'] = $val->area_name;
                    $employee_ary[$val->user_code]['territory_name'] = $val->territory_name;

                    $employee_ary[$val->user_code][$val->bat_kpi_name]['total_achievement'] = $val->avg_achivement_ratio;
                    $employee_ary[$val->user_code][$val->bat_kpi_name]['target_type'] = $val->target_type;

                    $employee_ary[$val->user_code][$val->bat_kpi_name][$val->target_ref_id]['product_name'] = $val->target_ref_id;
                    $employee_ary[$val->user_code][$val->bat_kpi_name][$val->target_ref_id]['target'] = $val->target_set;
                    $employee_ary[$val->user_code][$val->bat_kpi_name][$val->target_ref_id]['achievement'] = $val->target_achive;
                }

            }
        }


        if(count($employee_ary) > 0){
            foreach($employee_ary as $key => $val){
                foreach($kpi_ary as $k){
                    if(!array_key_exists($k, $val)){
                        $employee_ary[$key][$k] = $product_ary_property_for_marge[$k];
                    }
                }
            }
        }

//        return $employee_ary;
//        return $product_ary_property;
//        dd("----------",$kpi_ary, $product_ary_property, $product_ary_property_for_marge, $employee_ary);

//        foreach($product_ary_property as $key=>$val){
//            if(!empty($val)){
//                foreach($val as $p_id => $info){
//                    if($p_id != 'weight'){
//                        if($info->target_type == 'Brand'){
//                            $product_ary_property[$key][$p_id]->product_name = $all_products[$p_id];
//                        }
//                        else{
//                            $product_ary_property[$key][$p_id]->product_name = $all_segment_family[$p_id];
//                        }
//                    }
//                }
//            }
//        }

        foreach($product_ary_property as $target_key=>$target_val){
            if(!empty($target_val)){
                foreach($target_val as $p_key=>$p_val){
                    if($p_key != 'weight'){
                        foreach($p_val as $p_id => $info){
                            if($info->target_type == 'Brand'){
                                $product_ary_property[$target_key][$p_key][$p_id]->product_name = $all_products[$p_id];
                            }
                            else{
                                $product_ary_property[$target_key][$p_key][$p_id]->product_name = $all_segment_family[$p_id];
                            }
                        }
                    }
                }
            }
        }

//        dd($product_ary_property, $product_ary_property2);
//        dd($employee_ary);

        if($type=='excel'){
            $file_name = 'Assigned KPI List.xlsx';
            $header_array=[
                [
                    'text'=>'SL',
                    'row'=>4
                ],
                [
                    'text'=>'Region',
                    'row'=>4
                ],
                [
                   'text'=>'Area',
                   'row'=>4
                ],
                [
                    'text'=>'House',
                    'row'=>4
                ],
                [
                    'text'=>'Territory',
                    'row'=>4
                ],
                [
                    'text'=>'Point',
                    'row'=>4
                ],
                [
                    'text'=>'Employee Code',
                    'row'=>4
                ],
                [
                    'text'=>'Employee Name',
                    'row'=>4
                ],

                [
                    'text'=>'PFP Salary',
                    'row'=>4
                ],
//                [
//                    'text'=>'Weight',
//                    'row'=>3
//                ],
                [
                    'text'=>'Total Achievement',
                    'row'=>4
                ]
            ];

            $last_stage_header=[
                [
                    'text'=>'Target'
                ],
                [
                    'text'=>'Achivement'
                ]
            ];
            $count_row_kpi = 0;

            foreach ($product_ary_property as $target_key=>$target_val){
                $count_row_kpi = 0;
                $temp_sub = array();
                foreach ($target_val as $prod_key=>$pro_val) {

                    if($prod_key != 'weight'){
                        $count_row_kpi += count($pro_val);
                        $temp_level_3 = array(
                            'text'=>$prod_key,
                            'col'=>count($pro_val)*2,
//                            'sub'=>array(
//                                [
//                                    'text'=>$val->product_name,
//                                    'col'=>2,
//                                    'sub'=>$last_stage_header
//                                ],
//                            ),
                        );
                        $temp_level_4=array();
                        foreach ($pro_val as $key=>$val){
                            $temp_level_5 =array(
                                'text'=>$val->product_name,
                                'col'=>2,
                                'sub'=>$last_stage_header
                            );
                            array_push($temp_level_4,$temp_level_5);
                        }
                        $temp_level_3['sub']=$temp_level_4;
                        array_push($temp_sub,$temp_level_3);
                    }


                  //  array_push($temp_array['sub'],$temp_sub_array);
                }
                $temp_array = array(
                    'text'=>$target_key."(".number_format($target_val['weight'])."%)",
                    'col'=>$count_row_kpi*2,
                    'sub'=>$temp_sub
                );
                array_push($header_array,$temp_array);
            }


          $excel_array=array();
            $sl=1;
          $total_pfp_salary = 0;
          $total_target = array();
          $total_achievement = array();

            foreach($employee_ary as $info){
                $temp=array();
                $temp['sl']=$sl;
                $temp['region_name']=$info['region_name'];
                $temp['area_name']=$info['area_name'];
                $temp['company_name']=$info['company_name'];
                $temp['territory_name']=$info['territory_name'];
                $temp['point_name']=$info['point_name'];
                $temp['user_code']=$info['user_code'];
                $temp['user_name']=$info['user_name'];

                $temp['pfp_salary']=!empty($info['hr_emp_variable_salary'])? $info['hr_emp_variable_salary']: $info['variable_salary'];
                $total_pfp_salary+=!empty($info['hr_emp_variable_salary'])? $info['hr_emp_variable_salary']: $info['variable_salary'];
              //  $temp['weight']=$info['weight'];
                $total_achievement_sum = 0;
                foreach($product_ary_property as $key=>$prows){
                    if(!empty($info[$key])){
                        foreach($info[$key] as $key=>$val){
                            if($key == 'total_achievement'){
                                $total_achievement_sum = $total_achievement_sum + (float)$val;
                            }
                        }
                    }
                }


                $temp['total_achievement']=number_format($total_achievement_sum*100,2);
                $i=0;
                $j=0;

                foreach($product_ary_property as $key=>$prows){
                    if(!empty($info[$key])){

                        foreach($info[$key] as $key=>$val){
                            if($key != 'total_achievement' && $key != 'target_type'){
                               $temp['target'.$i]= $val['target'];
                                $total_target['target'.$i] = isset($total_target['target'.$i])? $total_target['target'.$i]+$val['target']:$val['target'];
                               $temp['achievement'.$i]=empty($val['achievement'])?0:$val['achievement'];
                               $total_achievement['achievement'.$i] = isset($total_achievement['achievement'.$i])?$total_achievement['achievement'.$i]+empty($val['achievement'])?0:$val['achievement']:empty($val['achievement'])?0:$val['achievement'];

                                $i++;
                            }
                        }
                    }
                }
                array_push($excel_array,$temp);
                $sl++;
            }

            $temp_total = array(
                'sl'=>null,
                'region_name'=>null,
                'area_name'=>null,
                'company_name'=>null,
                'territory_name'=>null,
                'point_name'=>null,
                'user_code'=>null,
                'user_name'=>"TOTAL",
                'pfp_salary'=>$total_pfp_salary,
                'total_achievement'=>null,

            );


           // dd($total_target);
            foreach($total_target as $i=>$target){
                $temp_total[$i]=$target;
            }
            foreach ($total_achievement as $i=>$achi){
                $temp_total[$i]=$achi;
            }

            array_push($excel_array,$temp_total);
           // dd($excel_array);
            //exit;
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

        $data['employee_ary'] = $employee_ary;
        $data['details'] = $details;
        $data['product_rows'] = $product_ary;
        $data['product_ary_property'] = $product_ary_property;
        $data['month_ary'] = $month_ary;
        $data['property_ary'] = $kpis;
        $data['property_id'] = $property_id;
        $data['target_month_val'] = $target_month;
        $data['point'] = $point;
        $data['designation_id'] = $designation_id;

//        dd($data);

    	return view('HR.kpi.assigned_kpi_list', $data);
    }

    //insert in hr_emp_monthly_variable_salary
    public function insertMonthlyVariableSalary(Request $request){
       $sys_users_id=$request->sys_users_id;
       $month=$request->month;
       $pfp_amount=$request->pfp_amount;
       $need_update=0;
       $pfp_array=DB::table('hr_emp_monthly_variable_salary')->where('sys_users_id',$sys_users_id)->where('vsalary_month',$month)->get();
       if(count($pfp_array) != 0){
           $need_update =1;
       }
       if($need_update==0){
           $data=array();

               $data['sys_users_id']=$sys_users_id;
               $data['vsalary_month']=$month;
               $data['variable_salary_amount']=$pfp_amount;
               $data['created_at']=dTime();
               $data['created_by']=Auth::id();

          // return $data;
             DB::table('hr_emp_monthly_variable_salary')->insert($data);
             $return_data=DB::table('hr_emp_monthly_variable_salary')->where('sys_users_id',$sys_users_id)->where('vsalary_month',$month)
                                ->join('sys_users','hr_emp_monthly_variable_salary.sys_users_id','=','sys_users.id')
                                ->select('sys_users.user_code','hr_emp_monthly_variable_salary.variable_salary_amount')->first();
           return response()->json([
               'success'=>true,
               'return_data'=>$return_data,
               'message'=>'Insurance information added successfully',
           ]);

       }else{
           $data=array();
           $data['variable_salary_amount']=$pfp_amount;
           $data['updated_at']=dTime();
           $data['updated_by']=Auth::id();

           DB::table('hr_emp_monthly_variable_salary')->where('sys_users_id',$sys_users_id)->where('vsalary_month',$month)
               ->update($data);
           $return_data=DB::table('hr_emp_monthly_variable_salary')->where('sys_users_id',$sys_users_id)->where('vsalary_month',$month)
               ->join('sys_users','hr_emp_monthly_variable_salary.sys_users_id','=','sys_users.id')
               ->select('sys_users.user_code','hr_emp_monthly_variable_salary.variable_salary_amount')->first();
           return response()->json([
               'success'=>true,
               'return_data'=>$return_data,
               'message'=>'Insurance information added successfully',
           ]);
       }

    }

    public function assignKpi(){

    	$data['configs'] = KpiConfig::where('status', "Active")->get();
        $data['properties'] = KpiProperties::where('status', "Active")->get();
        $data['segment'] = BatCats::where('parent', 1)->get();
        $data['family'] = BatCats::where('parent', 168)->get();
        $data['brand'] = BatProducts::where('stts', 1)->orderBy('sort')->get();
        $data['designations'] = DB::select('SELECT * FROM designations where status="Active"');

    	return view('HR.kpi.kpi_assign_form', $data);
    }

    public function assignKpiXl(){

        $data['configs'] = KpiConfig::where('status', "Active")->get();
        $data['properties'] = KpiProperties::where('status', "Active")->get();
        $data['segment'] = BatCats::where('parent', 1)->get();
        $data['family'] = BatCats::where('parent', 168)->get();
        $data['brand'] = BatProducts::all();
        $data['designations'] = DB::select('SELECT * FROM designations where status="Active"');

        return view('HR.kpi.kpi_assign_form_xl', $data);
    }

    public function xlDownloadApi(Request $request){

        $ff_type_ids = explode(',', $request->ff_type_name[0]);
        $config_val = KpiConfig::where('bat_kpi_configs_id', $request->kpi_config_id)->first();
        // dd($request->all());

        $postArray = [
            'market_scope' => $config_val->scope_market,
            'property_id' => $request->property_id,
            'target_type' => $request->target_type,
            'target_type_id' => $request->product_name,
            'month_from' => $request->month_from,
            'month_to' => $request->month_to,
            'ff_types' => $request->ff_type_name,
        ];

        $handle = curl_init();
        $url = "https://newprism.net/batb_hr/excel_info";
        $array = json_encode($postArray);

        $postData = array(
          'data' => $array
        );

        // dd($postData, $postArray);

        curl_setopt_array($handle,
          array(
            CURLOPT_URL => $url,
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER     => true,

          )
        );

        $data = curl_exec($handle);

        curl_close($handle);

        $result = json_decode($data);

        // dd($result);


        if($request->target_type == 'brand'){
            $variable_name = 'brand_name';
        }
        elseif($request->target_type == 'family'){
            $variable_name = 'family_name';
        }
        elseif($request->target_type == 'segment'){
            $variable_name = 'segment_name';
        }

        $config_property = str_replace(' ', '_', $request->kpi_config_name)."_N_".str_replace(' ', '_', $request->property_name);
        $config_property = preg_replace('/[^a-zA-Z0-9_.]/', '', $config_property);

        $filename = $config_property.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Assign KPI');

        //getCustomCell($sheet,$row,$col,$val,$colspan=false,$rowspan=false,$font_size=11,$align='left',$bold=false)

        exportHelper::getCustomCell($sheet, 1, 0, 'Configuration Name',2);
        exportHelper::getCustomCell($sheet, 1, 3, $request->kpi_config_name,2);
        exportHelper::getCustomCell($sheet, 2, 0, 'KPI Name',2);
        exportHelper::getCustomCell($sheet, 2, 3, $request->property_name,2);
        exportHelper::getCustomCell($sheet, 3, 0, 'Target Type',2);
        exportHelper::getCustomCell($sheet, 3, 3, $request->target_type,2);
        exportHelper::getCustomCell($sheet, 4, 0, 'Base Start Date',2);
        exportHelper::getCustomCell($sheet, 4, 3, $request->month_from,2);
        exportHelper::getCustomCell($sheet, 5, 0, 'Base End Date',2);
        exportHelper::getCustomCell($sheet, 5, 3, $request->month_to,2);

        exportHelper::getCustomCell($sheet, 7, 0, 'Region',null,1);
        exportHelper::getCustomCell($sheet, 7, 1, 'Area',null,1);
        exportHelper::getCustomCell($sheet, 7, 2, 'House',null,1);
        exportHelper::getCustomCell($sheet, 7, 3, 'Teritory',null,1);
        exportHelper::getCustomCell($sheet, 7, 4, 'Point',null,1);
        exportHelper::getCustomCell($sheet, 7, 5, 'Employee ID',null,1);
        exportHelper::getCustomCell($sheet, 7, 6, 'Employee Name',null,1);

        $increase_col = 7;

        if(!empty($result)){
            foreach($result->ids as $cols){
                $increase_row = $increase_col+1;
                exportHelper::getCustomCell($sheet, 7, $increase_col, $cols->brand, 1, null, null, 'center');
                exportHelper::getCustomCell($sheet, 8, $increase_col, 'Base');
                exportHelper::getCustomCell($sheet, 8, $increase_row, 'Target');
                $increase_col = $increase_col+2;
            }
        }


        $sl = 9;
        if(!empty($result->info)){
            foreach($result->info as $info){
                if(!empty($info->sr_name)){
                    exportHelper::getCustomCell($sheet, $sl, 0, $info->region);
                    exportHelper::getCustomCell($sheet, $sl, 1, $info->area);
                    exportHelper::getCustomCell($sheet, $sl, 2, $info->house);
                    exportHelper::getCustomCell($sheet, $sl, 3, $info->territory);
                    exportHelper::getCustomCell($sheet, $sl, 4, $info->point);
                    exportHelper::getCustomCell($sheet, $sl, 5, $info->employee_id);
                    exportHelper::getCustomCell($sheet, $sl, 6, $info->sr_name);

                    $pro_col = 7;
                    foreach($info->scope as $val){
                        exportHelper::getCustomCell($sheet, $sl, $pro_col, $val->base);
                        $pro_col = $pro_col+2;
                    }
                    $sl++;
                }
            }
        }


        exportHelper::excelHeader($filename,$spreadsheet);

        return response()->json(['status'=>'success','file'=>$filename, 'msg'=>'Download Successfully']);
    }

    public function xlDownload(Request $request){
        // dd($request->ff_type_name[0]);
        $ff_type_ids = explode(',', $request->ff_type_name[0]);
        $config_val = KpiConfig::where('bat_kpi_configs_id', $request->kpi_config_id)->first();

  /*      $all_users = DB::select("SELECT
                    region.slug AS region,
                    area.slug AS area,
                    company.`company_name` AS house,
                    territory.slug AS territory,
                    distributorspoint.`name` AS point,
                    sys_users.name AS sr_name,
                    sys_users.user_code AS employee_id,
                    sys_users.bat_company_id,
                    sys_users.designations_id
                    FROM sys_users
                    INNER JOIN bat_distributorspoint as distributorspoint ON distributorspoint.id = sys_users.bat_dpid
                    INNER JOIN bat_company as company ON distributorspoint.dsid = company.bat_company_id
                    INNER JOIN bat_locations AS region ON distributorspoint.region = region.id
                    INNER JOIN bat_locations AS area ON distributorspoint.area = area.id
                    INNER JOIN bat_locations AS territory ON distributorspoint.territory = territory.id
                    WHERE
                    sys_users.status = 'Active'
                    And sys_users.is_employee = 1
                    AND sys_users.bat_company_id IN (".$config_val->scope_market.")
                    and sys_users.designations_id IN (".$request->ff_type_name[0].")
                    GROUP BY sys_users.user_code
                    ORDER BY
                    company.bat_company_id,
                    distributorspoint.id");
*/

        $q = DB::table('sys_users');
        $q->selectRaw('region.slug AS region,
                    area.slug AS area,
                    company.`company_name` AS house,
                    territory.slug AS territory,
                    bat_distributorspoint.`name` AS point,
                    sys_users.name AS sr_name,
                    sys_users.user_code AS employee_id,
                    sys_users.bat_company_id,
                    sys_users.designations_id');
        $q->join('bat_company as company','company.bat_company_id', '=', 'sys_users.bat_company_id');
        $q->join('bat_distributorspoint','bat_distributorspoint.id', '=', 'sys_users.bat_dpid');
        $q->join('bat_locations as region','region.id', '=', 'bat_distributorspoint.region');
        $q->join('bat_locations as area','area.id', '=', 'bat_distributorspoint.area');
        $q->join('bat_locations as territory','territory.id', '=', 'bat_distributorspoint.territory');
        $q->whereRaw("sys_users.status = 'Active'
                    And sys_users.is_employee = 1
                    AND sys_users.bat_company_id IN (".$config_val->scope_market.")
                    and sys_users.designations_id IN (".$request->ff_type_name[0].")");

        $session_con = (sessionFilter('url','kpi-assign-form-xl'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $q->whereRaw($session_con);
        }


        $q->orderBy('company.bat_company_id','bat_distributorspoint.id','asc');
        $q->groupBy('sys_users.user_code');

        $all_users = $q->get();

        $value_names = "";
        if($request->target_type == 'brand' && !empty($request->brand_name)){
            $variable_name = 'brand_name';
            $value_names = BatProducts::whereIn('products_id', $request->brand_name)->get();
        }
        elseif($request->target_type == 'family' && !empty($request->family_name)){
            $variable_name = 'family_name';
            $value_names = BatCats::whereIn('id', $request->family_name)->get();
        }
        elseif($request->target_type == 'segment' && !empty($request->segment_name)){
            $variable_name = 'segment_name';
            $value_names = BatCats::whereIn('id', $request->segment_name)->get();
        }

        $product_name_ary = [];
        if(!empty($value_names)){
            foreach($value_names as $info){
                if($request->target_type == 'brand'){
                    $product_name_ary[] = $info->name;
                }
                else{
                   $product_name_ary[] = $info->slug;
                }
            }
        }
        else{
            $data['code'] = '500';
            $data['msg'] = "Please select ".$request->target_type." values";

            return $data;
        }


        $config_property = str_replace(' ', '_', $request->kpi_config_name)."_N_".str_replace(' ', '_', $request->property_name);
        $config_property = preg_replace('/[^a-zA-Z0-9_.]/', '', $config_property);

        $filename = $config_property.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Assign KPI');

        //getCustomCell($sheet,$row,$col,$val,$colspan=false,$rowspan=false,$font_size=11,$align='left',$bold=false)

        exportHelper::getCustomCell($sheet, 1, 0, 'Configuration Name',2);
        exportHelper::getCustomCell($sheet, 1, 3, $request->kpi_config_name,2);
        exportHelper::getCustomCell($sheet, 2, 0, 'KPI Name',2);
        exportHelper::getCustomCell($sheet, 2, 3, $request->property_name,2);
        exportHelper::getCustomCell($sheet, 3, 0, 'Target Type',2);
        exportHelper::getCustomCell($sheet, 3, 3, $request->target_type,2);

        exportHelper::getCustomCell($sheet, 5, 0, 'Region',null);
        exportHelper::getCustomCell($sheet, 5, 1, 'Area',null);
        exportHelper::getCustomCell($sheet, 5, 2, 'House',null);
        exportHelper::getCustomCell($sheet, 5, 3, 'Teritory',null);
        exportHelper::getCustomCell($sheet, 5, 4, 'Point',null);
        exportHelper::getCustomCell($sheet, 5, 5, 'Employee ID',null);
        exportHelper::getCustomCell($sheet, 5, 6, 'Employee Name',null);

        $increase_col = 7;

        if(!empty($product_name_ary)){
            foreach($product_name_ary as $cols){
                $increase_row = $increase_col+1;
                exportHelper::getCustomCell($sheet, 5, $increase_col, $cols, null, null, null, 'center');
                $increase_col = $increase_col+1;
            }
        }


        $sl = 6;
        if(!empty($all_users)){
            foreach($all_users as $info){
                if(!empty($info->sr_name)){
                    exportHelper::getCustomCell($sheet, $sl, 0, $info->region);
                    exportHelper::getCustomCell($sheet, $sl, 1, $info->area);
                    exportHelper::getCustomCell($sheet, $sl, 2, $info->house);
                    exportHelper::getCustomCell($sheet, $sl, 3, $info->territory);
                    exportHelper::getCustomCell($sheet, $sl, 4, $info->point);
                    exportHelper::getCustomCell($sheet, $sl, 5, $info->employee_id);
                    exportHelper::getCustomCell($sheet, $sl, 6, $info->sr_name);
                    $sl++;
                }
            }
        }


        exportHelper::excelHeader($filename,$spreadsheet);

        return response()->json(['status'=>'success','file'=>$filename, 'msg'=>'Download Successfully']);
    }

    public function xlUploadApi(Request $request){

        // dd($request->all());

        $document = $request->file('select_file');

        $original_name = $document->getClientOriginalName();
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $current_date_time = strtotime(date('Y-m-d H:m:s'));
        $new_name = $filename.'_'.$current_date_time. '.' . $document->getClientOriginalExtension();

        if (!is_dir(public_path('documents/assign_kpi'))) {
            mkdir(public_path('documents/assign_kpi'), 0777, true);
        }

        $document->move(public_path('documents/assign_kpi'), $new_name);

        if('xlsx' == $file_extension || 'XLSX' ==$file_extension) {

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $path = public_path('documents/assign_kpi/').$new_name;
            $spreadsheet = $reader->load($path);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $product_id_ary = [];

            if(!empty($sheetData)){
                if(!empty($sheetData[0][3]) && !empty($sheetData[1][3]) && !empty($sheetData[2][3]) && !empty($sheetData[3][3]) && !empty($sheetData[4][3])){

                    $config_name = trim($sheetData[0][3]);
                    $property_name = trim($sheetData[1][3]);
                    $target_type = trim($sheetData[2][3]);
                    $base_date_start = trim($sheetData[3][3]);
                    $base_date_end = trim($sheetData[4][3]);

                    $config_data = KpiConfig::where('bat_kpi_configs_name', $config_name)->first();
                    $property_id = KpiProperties::where('bat_kpi_properties_name', $property_name)->pluck('bat_kpi_properties_id')->first();
                    $config_id = $config_data->bat_kpi_configs_id;

                    $start    = new DateTime($config_data->start_month);
                    $start->modify('first day of this month');
                    $end      = new DateTime($config_data->end_month);
                    $end->modify('first day of next month');
                    $interval = DateInterval::createFromDateString('1 month');
                    $period   = new DatePeriod($start, $interval, $end);
                    $month_ary = [];

                    // dd($period);

                    // dd(count($sheetData[6]));
                    // echo $config_id.'--'.$property_id.'--'.$target_type;
                    $limit = count($sheetData[6])-7;
                    $product_start_row = 6;
                    if($limit > 0){
                        $product_id_ary = [];

                        for($i=1; $i<=$limit; $i=$i+2){
                            $product_name = trim($sheetData[6][$i+$product_start_row]);

                            if($target_type == 'brand'){
                                $product_id = BatProducts::where('name', $product_name)->pluck('products_id')->first();

                                // dump($product_id);
                                // echo '--'.$product_id;
                            }
                            else{
                                $product_id = BatCats::where('slug', $product_name)->pluck('id')->first();
                                // echo '--'.$product_id;
                            }
                            array_push($product_id_ary, $product_id);
                        }

                        // dd($target_type, $product_id, $product_id_ary, $product_name);


                        $total_data_rows = count($sheetData)-8;

                        if($total_data_rows > 8){

                            //check user already have save config
                            $chk = 0;
                            for($j=8; $j<count($sheetData); $j++){

                                $chk++;
                                $chk_user = KpiAssignedEmployee::where('user_code', $sheetData[$j][5])->where('bat_kpi_configs_id', $config_id)->where('bat_kpi_properties_id', $property_id)->count();

                                if($chk_user > 0){
                                    // dd('alerady have user');
                                    return redirect('assigned-kpi-list')->with('error', 'Already same user assigned for same configuration!');
                                }

                                if($chk >=4 ){
                                    break;
                                }
                            }


                            DB::beginTransaction();

                            try{
                                for($j=8; $j<count($sheetData); $j++){
                                    foreach ($period as $dt) {

                                        $designation_id = Employee::where('user_code', $sheetData[$j][5])->pluck('designations_id')->first();
                                        DB::table('bat_kpi_assigned_employee')->insert([
                                            'user_code' => $sheetData[$j][5],
                                            'designations_id' => !empty($designation_id)?$designation_id:0,
                                            'bat_kpi_configs_id' => $config_id,
                                            'bat_kpi_properties_id' => $property_id,
                                            'base_date_start' => $base_date_start,
                                            'base_date_end' => $base_date_end,
                                            'target_type' => $target_type,
                                            'target_month' => $dt->format("Y-m"),
                                            'status' => 'Active',
                                            'saved_from' => 'Excel'
                                        ]);

                                        $lastInsertId = DB::getPdo()->lastInsertId();

                                        $sl = 0;
                                        $jjj = 8;
                                        $dataAry = [];
                                        for($i=1; $i<=$limit; $i=$i+2){

                                            $product_target = trim($sheetData[$j][$jjj]);
                                            $jjj = $jjj+2;
                                            $productID = $product_id_ary[$sl];
                                            $sl++;

                                            $dataAry[] = [
                                                'bat_kpi_assigned_employee_id' => $lastInsertId,
                                                'bat_products_id' => $productID,
                                                'increase_percent' => '',
                                                'target_set' => $product_target,
                                                'status' => 'Active'
                                            ];
                                        }

                                        DB::table('bat_kpi_assigned_emp_details')->insert($dataAry);
                                    }
                                }

                                DB::commit();
                                return redirect('assigned-kpi-list')->with('success', 'Data saved!');

                            }catch (\Exception $e) {

                                DB::rollback();
                                return redirect('assigned-kpi-list')->with('error', 'Data not saved!');
                            }
                        }

                        dd('exit');
                    }
                    else{
                        return redirect('assigned-kpi-list')->with('error', 'Problem in Excel file!');
                    }
                    // dd('999');

                }
                else{
                    // dd('core data changed');
                    return redirect('assigned-kpi-list')->with('Error', 'Incurrect Excel file data!');
                }
            }
        }
    }

    public function xlUpload(Request $request){

//         dd(session()->all());

        $document = $request->file('select_file');

        $original_name = $document->getClientOriginalName();
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $current_date_time = strtotime(date('Y-m-d H:m:s'));
        $loged_in_user_id =  $request->session()->get('USER_ID');
        $new_name = $filename.'_UserId_'.$loged_in_user_id.'_'.$current_date_time. '.' . $document->getClientOriginalExtension();

        $month_year = date('M_Y');

        if (!is_dir(public_path("documents/assign_kpi/".$month_year))) {
            mkdir(public_path("documents/assign_kpi/".$month_year), 0777, true);
        }

        $document->move(public_path("documents/assign_kpi/".$month_year), $new_name);

//        dd($new_name, $month_year);

        if('xlsx' == $file_extension || 'XLSX' ==$file_extension) {

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $path = public_path("documents/assign_kpi/".$month_year."/").$new_name;
            $spreadsheet = $reader->load($path);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $product_id_ary = [];

            if(!empty($sheetData)){
                if(!empty($sheetData[0][3]) && !empty($sheetData[1][3]) && !empty($sheetData[2][3]) ){

                    $config_name = trim($sheetData[0][3]);
                    $property_name = trim($sheetData[1][3]);
                    $target_type = trim($sheetData[2][3]);

                    $config_data = KpiConfig::where('bat_kpi_configs_name', $config_name)->first();
                    $property_id = KpiProperties::where('bat_kpi_properties_name', $property_name)->pluck('bat_kpi_properties_id')->first();
                    $config_id = $config_data->bat_kpi_configs_id;

                    $start    = new DateTime($config_data->start_month);
                    $start->modify('first day of this month');
                    $end      = new DateTime($config_data->end_month);
                    $end->modify('first day of next month');
                    $interval = DateInterval::createFromDateString('1 month');
                    $period   = new DatePeriod($start, $interval, $end);
                    $month_ary = [];

                    // dd($period);

                    // dd(count($sheetData[6]));
                    $limit = count($sheetData[6])-7;
                    $product_start_row = 6;
                    if($limit > 0){
                        $product_id_ary = [];

                        for($i=1; $i<=$limit; $i++){
                            $product_name = trim($sheetData[4][$i+$product_start_row]);
                            // dump($product_name."===");

                            if($target_type == 'brand'){
                                $product_id = BatProducts::where('name', $product_name)->pluck('products_id')->first();

                                // dump($product_id);
                                // echo '--'.$product_id;
                            }
                            else{
                                $product_id = BatCats::where('slug', $product_name)->pluck('id')->first();
                                // echo '--'.$product_id;
                            }
                            array_push($product_id_ary, $product_id);
                        }

                        // dd($target_type, $product_id, $product_id_ary, $product_name);
                        // dd($product_id_ary);


                        $total_data_rows = count($sheetData)-4;

                        if($total_data_rows > 4){
                            $exist_user_code_ary = [];
                            //check user already have same config
                            $chk = 0;
                            for($j=5; $j<count($sheetData); $j++){

                                $chk++;
                                $chk_user = KpiAssignedEmployee::where('user_code', $sheetData[$j][5])->where('bat_kpi_configs_id', $config_id)->where('bat_kpi_properties_id', $property_id)->first();

                                if(!empty($chk_user)){
                                    $exist_user_code_ary[] = $chk_user->user_code;
                                }
                            }

                            if(count($exist_user_code_ary) > 0){
                                $user_code_string = implode(', ', $exist_user_code_ary);
                                return redirect('assigned-kpi-list')->with('error', 'User code: '.$user_code_string."- already exist under this configuration");
                            }

                            DB::beginTransaction();

                            try{
                                for($j=5; $j<count($sheetData); $j++){
                                    foreach ($period as $dt) {

                                        $designation_id = Employee::where('user_code', $sheetData[$j][5])->pluck('designations_id')->first();
                                        // dump($sheetData[$j][5], $designation_id);
                                        DB::table('bat_kpi_assigned_employee')->insert([
                                            'user_code' => $sheetData[$j][5],
                                            'designations_id' => !empty($designation_id)?$designation_id:0,
                                            'bat_kpi_configs_id' => $config_id,
                                            'bat_kpi_properties_id' => $property_id,
                                            'target_type' => $target_type,
                                            'target_month' => $dt->format("Y-m"),
                                            'status' => 'Active',
                                            'saved_from' => 'Excel'
                                        ]);

                                        $lastInsertId = DB::getPdo()->lastInsertId();

                                        $sl = 0;
                                        $jjj = 7;
                                        $dataAry = [];

                                        for($i=1; $i<=$limit; $i++){

                                            $product_target = trim($sheetData[$j][$jjj]);
                                            $jjj = $jjj+1;
                                            $productID = $product_id_ary[$sl];
                                            $sl++;

                                            $dataAry[] = [
                                                'bat_kpi_assigned_employee_id' => $lastInsertId,
                                                'bat_products_id' => $productID,
                                                'increase_percent' => '',
                                                'target_set' => $product_target,
                                                'status' => 'Active'
                                            ];
                                        }

                                        DB::table('bat_kpi_assigned_emp_details')->insert($dataAry);
                                    }
                                }

                                DB::commit();
                                return redirect('assigned-kpi-list')->with('success', 'Data saved!');

                            }catch (\Exception $e) {

                                DB::rollback();
                                return redirect('assigned-kpi-list')->with('error', 'Data not saved!');
                            }
                        }

                        dd('exit');
                    }
                    else{
                        return redirect('assigned-kpi-list')->with('error', 'Problem in Excel file!');
                    }
                    // dd('999');

                }
                else{
                    // dd('core data changed');
                    return redirect('assigned-kpi-list')->with('Error', 'Incurrect Excel file data!');
                }
            }
        }
    }

    public function kpiAssignView($config_id, $ff_type_id){

        $ff_type_data = KpiAssignedEmployee::with('property')->groupBy('bat_kpi_properties_id')
            ->where('bat_kpi_configs_id', $config_id)->where('designations_id', $ff_type_id)->get();

        $final_data = '';

        if(!empty($ff_type_data)){


            foreach($ff_type_data as $info){

                $qury = DB::table('bat_kpi_assigned_employee as assign_tbl')
                            ->join('bat_kpi_assigned_emp_details as detail_tbl', 'assign_tbl.bat_kpi_assigned_employee_id', '=', 'detail_tbl.bat_kpi_assigned_employee_id');
                        if($info->target_type == 'Brand')
                        {
                            $qury->join('bat_products', 'detail_tbl.bat_products_id', '=', 'bat_products.products_id')
                            ->select('detail_tbl.bat_products_id', 'detail_tbl.increase_percent', 'detail_tbl.target_set', 'bat_products.name as product_name');
                        }
                        else{
                            $qury->join('bat_cats', 'detail_tbl.bat_products_id', '=', 'bat_cats.id')
                            ->select('detail_tbl.bat_products_id', 'detail_tbl.increase_percent', 'detail_tbl.target_set', 'bat_cats.slug as product_name');
                        }

                $details = $qury->where('assign_tbl.bat_kpi_configs_id', $config_id)->where('assign_tbl.bat_kpi_properties_id', $info->bat_kpi_properties_id)->groupBy('detail_tbl.bat_products_id')->get();

                $final_data .='<div class="col-md-12">';
                    $final_data .='<span ><h3 class="  bg-primary" style="padding: 5px;">'.$info->property->bat_kpi_properties_name.'</h3></span>';
                $final_data .='</div>';
                $final_data .='<div class="form-group col-md-4">';
                    $final_data .='<label class="form-label">Base Start Date: </label>&nbsp;<b>'.$info->base_date_start.'</b>';
                $final_data .='</div>';
                $final_data .='<div class="form-group col-md-4">';
                    $final_data .='<label class="form-label">Base End Date: </label>&nbsp;<b>'.$info->base_date_end.'</b>';
                $final_data .='</div>';
                $final_data .='<div class="col-md-4">';
                    $final_data .='<div class="form-group">';
                        $final_data .='<label class="form-label">Target Type: </label>&nbsp;<b>'.$info->target_type.'</b>';
                    $final_data .='</div>';
                $final_data .='</div>';
                $final_data .='<div class="col-md-12 row ">';
                    $final_data .='<div class="col-md-12 row" style="margin-top: 5px; margin-left: 1px;">';
                        $final_data .='<table class="table">';
                            $final_data .='<tr>';
                                $final_data .='<th>Product Name</th>';
                                $final_data .='<th>Percent</th>';
                                // $final_data .='<th>Target</th>';
                            $final_data .='</tr>';
                            foreach($details as $val){
                                $final_data .='<tr>';
                                    $final_data .='<td>'.$val->product_name.'</td>';
                                    $final_data .='<td>'.$val->increase_percent.'%</td>';
                                    // $final_data .='<td>'.$val->target_set.'</td>';
                                $final_data .='</tr>';
                            }
                        $final_data .='</table>';
                    $final_data .='</div>';
                $final_data .='</div>';
            }
        }


        return $final_data;
    }

    public function configDetails($id, $ff=null){

        $result = DB::select("
            SELECT
            bat_kpi_configs.bat_kpi_configs_id,
            bat_kpi_configs.bat_kpi_configs_name,
            bat_kpi_configs.start_month,
            bat_kpi_configs.end_month,
            GROUP_CONCAT(concat(range_from,'-',range_to,'=',range_value) SEPARATOR '<br/>') as kpi_range,
            (select group_concat(company_name) from bat_company where find_in_set(bat_company_id,scope_market)) market_scope,
            (select group_concat(concat(bat_kpi_properties_name, '-', bat_kpi_config_details.weight) SEPARATOR '<br/>') from bat_kpi_config_details,bat_kpi_properties where bat_kpi_config_details.bat_kpi_properties_id=bat_kpi_properties.bat_kpi_properties_id
            AND bat_kpi_configs_id=bat_kpi_configs.bat_kpi_configs_id) as config_details
            from bat_kpi_configs
            left join bat_kpi_config_ranges on bat_kpi_configs.bat_kpi_configs_id = bat_kpi_config_ranges.bat_kpi_configs_id
            where bat_kpi_configs.bat_kpi_configs_id = $id and bat_kpi_configs.status = 'Active'
            group by bat_kpi_config_ranges.bat_kpi_configs_id");

        $propertyAry = [];
        $propertyString = "";
        $property_name = "";
        $property_id = "";

        $sll = 0;

        $val = KpiConfigDetails::with('propertyName')->where('bat_kpi_configs_id', $id)->get();
        if(count($val) > 0){
            foreach($val as $info){
                $conv_string = str_replace(' ', '_', $info->propertyName->bat_kpi_properties_name);
                $property_div_cls = "property_div_cls_".$conv_string;
                array_push($propertyAry, $property_div_cls);
                $propertyString .= "<option value='".$info->propertyName->bat_kpi_properties_id."'>".$info->propertyName->bat_kpi_properties_name."</option>";
                if($sll == 0){
                    $property_name = $info->propertyName->bat_kpi_properties_name;
                    $property_id = $info->propertyName->bat_kpi_properties_id;
                }

                $sll++;
            }
        }

        $ff_type_option = '';

        if(!empty($ff)){
            //request from assign kpi list
            $ff_type_data = KpiAssignedEmployee::with('designation')->groupBy('designations_id')->where('bat_kpi_configs_id', $id)->get();

            if(!empty($ff_type_data)){
                $ff_type_option .="<option value=''>Select FF type</option>";
                foreach($ff_type_data as $data){
                    // echo $data->designation->designations_name;
                    $ff_type_option .="<option value='".$data->designation->designations_id."'>".$data->designation->designations_name."</option>";
                }
            }
        }

        $mod_result['bat_kpi_configs_id'] = $result[0]->bat_kpi_configs_id;
        $mod_result['bat_kpi_configs_name'] = $result[0]->bat_kpi_configs_name;
        $mod_result['start_month'] = $result[0]->start_month;
        $mod_result['end_month'] = $result[0]->end_month;
        $mod_result['kpi_range'] = $result[0]->kpi_range;
        $mod_result['config_details'] = $result[0]->config_details;
        $mod_result['market_scope'] = $result[0]->market_scope;
        $mod_result['property_ary'] = $propertyAry;
        $mod_result['property_string'] = $propertyString;
        $mod_result['ff_type_option'] = $ff_type_option;
        $mod_result['property_name'] = $property_name;
        $mod_result['property_id'] = $property_id;

        return $mod_result;
    }

    public function store(Request $request){

        ini_set('memory_limit','246M');
        ini_set('max_execution_time','500');
        // echo ini_get("memory_limit")."\n";
        // echo ini_get("max_execution_time")."\n";

        $val = KpiConfigDetails::with('propertyName')->where('bat_kpi_configs_id', $request->kpi_config_id)->get();
        $market_scope = KpiConfig::where('bat_kpi_configs_id', $request->kpi_config_id)->first();
        $config_id = $request->kpi_config_id;

        $start    = new DateTime($market_scope->start_month);
        $start->modify('first day of this month');
        $end      = new DateTime($market_scope->end_month);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $month_ary = [];

        // dd(count($val), $request->all());

        $result_ary = [];
        $result_ary_two = [];

        if(count($val) > 0){

            $result_ary['info'] = [];

            $sl = 0;
            foreach($val as $info){

                $result_ary_two = [];

                $conv_string = str_replace(' ', '_', $info->propertyName->bat_kpi_properties_name);

                $segment_name = "segment_name_".$conv_string;
                $segment_value = "segment_value_".$conv_string;
                $family_name = "family_name_".$conv_string;
                $family_value = "family_value_".$conv_string;
                $brand_name = "brand_name_".$conv_string;
                $brand_value = "brand_value_".$conv_string;
                $target_type_cls = "target_type_cls_".$conv_string;
                $month_from = "month_from_".$conv_string;
                $month_to = "month_to_".$conv_string;

                if(!empty($request->input($target_type_cls))){
                    $result_ary_two["property_id"] = $info->propertyName->bat_kpi_properties_id;
                    $result_ary_two["property_name"] = $info->propertyName->bat_kpi_properties_name;
                    $result_ary_two["month_from"] = $request->input("month_from_".$conv_string);
                    $result_ary_two["month_to"] = $request->input("month_to_".$conv_string);
                    $result_ary_two["target_type"] = $request->input($target_type_cls);
                    if(!empty($request->input($segment_name)) && !empty($request->input($segment_value)) ){
                        $result_ary_two["scope_name"] = $request->input($segment_name);
                        $result_ary_two["scope_value"] = $request->input($segment_value);
                    }
                    else if(!empty($request->input($family_name)) && !empty($request->input($family_value)) ){
                        $result_ary_two["scope_name"] = $request->input($family_name);
                        $result_ary_two["scope_value"] = $request->input($family_value);
                    }
                    else if(!empty($request->input($brand_name)) && !empty($request->input($brand_value)) ){
                        $result_ary_two["scope_name"] = $request->input($brand_name);
                        $result_ary_two["scope_value"] = $request->input($brand_value);
                    }

                    $result_ary['info'][$sl] = $result_ary_two;
                    $sl++;
                }
                else{


                }
            }


            $result_ary['scope_market'] = $market_scope->scope_market;
            $result_ary['ff_type_name'] = $request->ff_type_name;

            if(count($result_ary['info']) != count($val) || empty($request->ff_type_name)){

                $data['code'] = '500';
                $data['msg'] = "Please Provide All Properties Values!";

                return $data;
            }

            $handle = curl_init();

            $url = "https://newprism.net/batb_hr/kpi_data";

            $array = json_encode($result_ary);

            $postData = array(
              'data' => $array
            );

            // dd(count($result_ary['info']));
            $propertyBase = [];
            foreach($result_ary['info'] as $data){
                $propertyBase[$data['property_id']]['from'] = $data['month_from'];
                $propertyBase[$data['property_id']]['to'] = $data['month_to'];
            }

            curl_setopt_array($handle,
              array(
                CURLOPT_URL => $url,
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER     => true,

              )
            );

            $data = curl_exec($handle);

            curl_close($handle);

            $result = json_decode($data);
            $limitLoop = 0;

            if(!empty($result)){
                foreach($result as $key => $info){
                    $limitLoop++;
                    $chk_user = KpiAssignedEmployee::where('user_code', $key)->where('bat_kpi_configs_id', $config_id)->count();

                    if($chk_user > 0){
                        $return_data['msg'] = "Same configuration for same user already available!";
                        $return_data['code'] = 500;

                        return $return_data;
                    }
                    if($limitLoop >= 5 ){
                        break;
                    }
                }

                DB::beginTransaction();

                try{

                    foreach($result as $key => $info){
                        foreach($info as $val){
                            if(!empty($val->emp_id) && !empty($val->scope)){
                                foreach ($period as $dt) {
                                    DB::table('bat_kpi_assigned_employee')->insert([
                                        'user_code' => $val->emp_id,
                                        'designations_id' => $val->designation,
                                        'bat_kpi_configs_id' => $config_id,
                                        'bat_kpi_properties_id' => $val->p_id,
                                        'base_date_start' => $propertyBase[$val->p_id]['from'],
                                        'base_date_end' => $propertyBase[$val->p_id]['to'],
                                        'target_type' => $val->t_type,
                                        'target_month' => $dt->format("Y-m"),
                                        'status' => 'Active',
                                        'saved_from' => 'Manual'
                                    ]);

                                    $lastInsertId = DB::getPdo()->lastInsertId();

                                    $dataAry = [];

                                    foreach($val->scope as $scope){
                                        $dataAry[] = [
                                            'bat_kpi_assigned_employee_id' => $lastInsertId,
                                            'bat_products_id' => $scope->id,
                                            'increase_percent' => $scope->pcent,
                                            'target_set' => $scope->target,
                                            'status' => 'Active'
                                        ];
                                    }

                                    DB::table('bat_kpi_assigned_emp_details')->insert($dataAry);
                                }
                            }
                        }
                    }

                    DB::commit();

                    $return_data['msg'] = "Data saved Successfully";
                    $return_data['code'] = 200;

                }catch (\Exception $e) {

                    DB::rollback();
                    $return_data['msg'] = "Data Not saved";
                    $return_data['code'] = 500;
                }
            }
            else{
                $return_data['msg'] = "Data Not Found";
                $return_data['code'] = 500;
            }

        }

        return $return_data;
    }

    public function assignedManually(){

        $data['configs'] = KpiConfig::where('status', "Active")->get();
        return view('HR.kpi.assigned_manually', $data);
    }

    public function assignedExcel(){

        $data['configs'] = KpiConfig::where('status', "Active")->get();
        return view('HR.kpi.assigned_excel', $data);
    }
}
