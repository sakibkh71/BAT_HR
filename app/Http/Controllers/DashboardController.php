<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
use Config;
class DashboardController extends Controller {
    private $data = [];
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }
    public function noPermission(){
        return view('errors/no_permission');
    }

    public function index(){

        $default_module = session('DEFAULT_MODULE_ID');

//        dd(session('USER_MODULES'));
        $data['selected_module'] = session('SELECTED_MODULE');
        $db_widget = session('DB_WIDGET');
        $data['my_dashboard'] = 0;
        $widgets = [];
        if(isset($db_widget[$data['selected_module']]) && !empty($db_widget[$data['selected_module']])){
            $widgets = $db_widget[$data['selected_module']];
        }
        if(isset($widgets['USERS']) && !empty($widgets['USERS'])){
            $data['my_dashboard'] = 1;
        }else{
            $data['my_dashboard'] = 0;
        }
        return view('Dashboard.home', $data);
    }

    public function getDashboard(){
        $selected_module = session('SELECTED_MODULE');
        $this->data['default_ds_data'] = self::loadDashboardData($selected_module);
        return view('Dashboard.dyn_home', $this->data);
    }
    public function getMyDashboard(){
        $user_id = session::get('USER_ID');
        $selected_module = session('SELECTED_MODULE');
        $this->data['default_ds_data'] = self::loadDashboardData($selected_module, $user_id);
        $positions = DB::table('sys_dashboard_layouts')
            ->select('dashboard_widget_layout')
            ->where('user_id', $user_id)
            ->where('module_id', $selected_module)->first();
        $this->data['layouts'] = !empty($positions) ? $positions->dashboard_widget_layout : '';
        return view('Dashboard.dyn_my_home', $this->data);
    }
    public function loadDashboardData($selected_module, $user_id = ''){
        $db_widget = session('DB_WIDGET');
        $widgets = [];
        $widget_data['userwisewidgets'] = [];
        $widget_data['userwisedata'] = [];
        $widget_data['defaultwidgetdata'] = [];
        if(isset($db_widget[$selected_module]) && !empty($db_widget[$selected_module])){
            $widgets = $db_widget[$selected_module];
        }
        if(isset($widgets['USERS']) && !empty($widgets['USERS'])){
            $widget_data['userwisewidgets'] = explode(',', $widgets['USERS']);
            if(!empty($user_id)){
                $widget_data['userwisedata'] = self::getUserDashboardWidgetData($widget_data['userwisewidgets'], $user_id);
            }
        }
        if(isset($widgets['DEFAULT']) && !empty($widgets['DEFAULT'])){
            $df_widgets = explode(',', $widgets['DEFAULT']);
            $widget_data['defaultwidgetdata'] = self::getDashboardWidgetData($df_widgets);
        }
        return $widget_data;
    }
    public function getUserDashboardWidgetData($widget_ids = [], $user_id){
        return DB::table('sys_dashboard_widget_users')
            ->select()
            ->whereIN('sys_dashboard_widget_id', $widget_ids)
            ->where('user_id', $user_id)
            ->orderBy('order', 'ASC')
            ->get()->toArray();
    }
    public function getDashboardWidgetData($widget_ids = []){
        return DB::table('sys_dashboard_widget')
            ->select()->whereIN('sys_dashboard_widget_id', $widget_ids)
            ->orderBy('order', 'ASC')
            ->get()->toArray();
    }
    public function fetchList(Request $request){
        $post = $request->all();
        $query = $post['query'];
        $result = DB::select(DB::raw($query));
        foreach ($result as $data){
            foreach ($data as $key => $header){
                $this->data['table_header'][] = ucwords(str_replace('_', ' ', $key));
            }
            break;
        }
        $this->data['table_data'] = $result;
        return response()->json($this->data);
    }
    public function fetchPie(Request $request){
        $post = $request->all();
        $query = $post['query'];
        $results = DB::select(DB::raw($query));
        $data = [];
        foreach ($results as $key=>$result){
            $data[$key]['name'] = $result->name;
            $data[$key]['y'] = (float)$result->y;
        }
        return response()->json($data);
    }
    public function fetchSummary(Request $request){
        $post = $request->all();
        $query = $post['query'];
        $results = DB::select(DB::raw($query));
        foreach ($results[0] as $data){
            $result = $data;
        }
        return response()->json($result);
    }
    public function DashboardSetPosition(Request $request, $user_id = '', $module_id = ''){
        $post = $request->all();
        DB::table('sys_dashboard_layouts')
            ->updateOrInsert(['user_id'=>$user_id, 'module_id'=>$module_id], ['dashboard_widget_layout'=>$post['position']]);
        return response()->json();
    }
    public function DashboardSetMyWidgets(Request $request, $user_id = '', $module_id = ''){
        $post = $request->all();
        $db_widgets = session('DB_WIDGET');
        $selected_widgets = json_decode($post['widgets']);
        $db_widgets_for_module = $db_widgets[$module_id];
        $sess_user_widgets = [];
        $resetposition = 0;
        if(isset($db_widgets_for_module['USERS']) && !empty($db_widgets_for_module['USERS'])){
            $sess_user_widgets = explode(',', $db_widgets_for_module['USERS']);
        }
        $toadd = array_diff($selected_widgets, $sess_user_widgets);
        $toremove = array_diff($sess_user_widgets, $selected_widgets);
        if(!empty($toadd)){
            $resetposition = 1;
            $WidgetData = self::getDashboardWidgetData($toadd);
            $insert_data = [];
            foreach ($WidgetData as $key => $new_widgets){
                foreach ($new_widgets as $field => $value){
                    $insert_data[$key][$field] = $value;
                }
                $insert_data[$key]['user_id'] = $user_id;
            }
            DB::table('sys_dashboard_widget_users')->insert($insert_data);
        }
        if(!empty($toremove)){
            $resetposition = 1;
            DB::table('sys_dashboard_widget_users')->where('user_id', $user_id)->where('module_id', $module_id)->whereIN('sys_dashboard_widget_id', $toremove)->delete();
        }
        Session(['DB_WIDGET.'.$module_id.'.USERS' => implode(',',$selected_widgets)]);
        if($resetposition == 1){
            DB::table('sys_dashboard_layouts')
                ->updateOrInsert(['user_id'=>$user_id, 'module_id'=>$module_id], ['dashboard_widget_layout'=> '']);
        }
        return response()->json([]);
    }

    public function fetchC3(Request $request){
        $post = $request->all();
        $query = $post['query'];
        $results = DB::select(DB::raw($query));
        $data = [];
        $labels = array_values(array_unique(array_column((array)$results,'label')));
        $stacks = array_values(array_unique(array_column((array)$results,'stacks')));
//        foreach ($labels as $label){
//            foreach ($results as $result){
//            foreach ($stacks as $stack){
//
//                    if($label == $result->label){
//                        if($stack == $result->stacks){
//                            $data[$stack][$label][] = $result->value;
//                        }else{
//                            $data[$stack][$label][] = 0;
//                        }
//                    }
//                    else{
//                        $data[$stack][$label][] = 0;
//                    }
//                }
//            }
//        }

        foreach ($results as $r){
            $data[$r->stacks][$r->label][] = $r->value;
        }
        foreach ($labels as $k=>$v){
            foreach ($data as $key=>$stacks){
                if (!array_key_exists($v, $stacks)){
                    $data[$key][$v][] = 0;
                }
            }
        }
        $data2 = [];
        $color = ['P'=>'#36ab92','A'=>'#d58e88','Leave'=>'#f4dc90','Lv'=>'#f4dc90'];
        foreach ($data as $key=>$row){
            $data2[] = array(
                'name' => $key,
                'color' => $color[$key],
                'data'=>array_column(array_values($row),0)
            );
        }


        $response['labels'] = $labels;
        $response['data'] = $data2;
//        dd($response);
        return response()->json($response);
    }

    public function getNewJoin(){
        $from_date = date('Y-m-d', strtotime('-30 days'));
        $to_date = date('Y-m-d');
        $string_table = "";

        $data_sql = DB::table('sys_users')
                ->select('sys_users.user_code',
                    'sys_users.name as emp_name',
                    'sys_users.date_of_join',
                    'bat_distributorspoint.name as point_name',
                    'designations.designations_name'
                    )
                ->join('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id')
                ->join('designations', 'sys_users.designations_id', 'designations.designations_id')
                ->where('date_of_join', '>=', $from_date)->where('date_of_join', '<=', $to_date)
                ->where('is_employee', 1);
                $session_con = (sessionFilter('url','attendance-entry'));
                $session_con = trim(trim(strtolower($session_con)),'and');
                if($session_con){
                    $data_sql->whereRaw($session_con);
                }
        $data = $data_sql->get();

        return $data;
    }

    public function getNewInsurance(){
        $current_month=date('m');
        $data=DB::table('hr_insurance_claims')
            ->select('sys_users.name','sys_users.user_code','hr_insurance_claims.claim_type','hr_insurance_claims.claim_date',
                'hr_insurance_claims.claim_amount','hr_insurance_claims.claim_status')
            ->join('sys_users','sys_users.id','hr_insurance_claims.sys_users_id')
            ->where('claim_status','Pending')->whereRaw('MONTH(claim_date) = ?', [$current_month])->get();
        return $data;
    }

    public function pfpTarget(){

        $this_month = date('Y-m');
//        $this_month = '2019-09';

        $data_sql = DB::table('sys_users')
            ->select('sys_users.user_code',
                'sys_users.name as emp_name',
                'bat_distributorspoint.name as point_name',
                'designations.designations_name'
            )
            ->whereRaw("sys_users.user_code NOT IN ( SELECT user_code from bat_kpi_target WHERE `bat_kpi_target`.`target_month` = '$this_month' )")
            ->join('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id')
            ->join('designations', 'sys_users.designations_id', 'designations.designations_id')
            ->where('is_employee', 1);
            $session_con = (sessionFilter('url','attendance-entry'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            if($session_con){
                $data_sql->whereRaw($session_con);
            }

        $data = $data_sql->get();

        return $data;
    }

    public function kpiAchievementCurrentMonth(Request $request){

        $current_month = date('Y-m');
//        $current_month = '2019-10';
        $point_ary = [];
        $result_ary = [];
        $tbl_string = "";
        $key_ary = ['below_70','inside_80','inside_90','upper_90'];

        $data_sql = DB::table('bat_kpi_target_detail')
            ->select('bat_kpi_target_detail.user_code',
                'bat_kpi_target_detail.target_month',
                'bat_kpi_target_detail.bat_kpi_id',
                'bat_kpi_target_detail.target_type',
                'bat_kpi_target_detail.target_ref_id',
                'bat_kpi_target_detail.target_set',
                'bat_kpi_target_detail.target_achive',
                'sys_users.designations_id',
                'sys_users.bat_dpid',
                'designations.designations_name',
                'bat_distributorspoint.name as point_name',
                'bat_distributorspoint.id as point_id',
                'bat_kpis.bat_kpi_name',
                'bat_kpi_target.bat_kpi_configs_id',
                'bat_kpi_config_details.weight'
            )
            ->join('sys_users', 'bat_kpi_target_detail.user_code', 'sys_users.user_code')
            ->join('bat_kpis', 'bat_kpi_target_detail.bat_kpi_id', 'bat_kpis.bat_kpi_id')
            ->join('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id')
            ->join('designations', 'sys_users.designations_id', 'designations.designations_id');

        $data_sql->leftJoin('bat_kpi_target', function ($join){
            $join->on('bat_kpi_target.user_code','=','bat_kpi_target_detail.user_code');
            $join->on('bat_kpi_target.target_month','=','bat_kpi_target_detail.target_month');
            $join->on('bat_kpi_target.bat_kpi_id','=','bat_kpi_target_detail.bat_kpi_id');
        });

        $data_sql->leftJoin('bat_kpi_config_details', function ($join){
            $join->on('bat_kpi_config_details.bat_kpi_id','=','bat_kpi_target.bat_kpi_id');
            $join->on('bat_kpi_config_details.bat_kpi_configs_id','=','bat_kpi_target.bat_kpi_configs_id');
        });

        $session_con = (sessionFilter('url','attendance-entry'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $data_sql->whereRaw($session_con);
        }
            $data_sql->where('sys_users.is_employee', 1)
            ->where('bat_kpi_target_detail.target_month', $current_month);

        $data = $data_sql->get();

        foreach($data as $info){
//          $point_ary[$info->point_id]['point_name'] = $info->point_name;
            $point_ary[$info->point_name][$info->user_code][$info->bat_kpi_name][$info->target_type][] = $info;
        }

        $point_wise_achievement_ary = [];

        foreach($point_ary as $pa_key=>$pa_value){
//            echo $pa_key."<br/>";

            foreach($pa_value as $pav_key=>$pav_value){
//                echo "---User ID:--".$pav_key."<br/>";

                $kpi_weight = 0;
                $ratio_after_weight_cal_sum = 0;
                foreach($pav_value as $kpi_key=>$kpi_value){

//                    echo "--------- KPI Name -------".$kpi_key."<br/>";
                    $target_type_count = count($kpi_value);
                    $achieve_ratio_sum = 0;
                    foreach($kpi_value as $target_type_key=>$target_type_value){

//                        echo "--------------- Target TYpe ------------".$target_type_key."<br>";
                        $sum_target = 0;
                        $sum_achievement = 0;
                        $achieve_ratio = 0;
                        foreach($target_type_value as $prod_key=>$prod_val){
//                            echo "---------------------------product-------------".$prod_val->target_ref_id."<br/>";
//                            echo "---------------------------product-target-------------".$prod_val->target_set."<br/>";
//                            echo "---------------------------product-achive-------------".$prod_val->target_achive."<br/>";
                            $prod_target = $prod_val->target_set > 0 ?$prod_val->target_set:0;
                            $prod_achievement = $prod_val->target_achive > 0 ?$prod_val->target_achive:0;

                            $sum_target += $prod_target;
                            $sum_achievement += $prod_achievement;
                            $kpi_weight = $prod_val->weight;
                        }

//                        echo "=======================================target=================".$sum_target."<br/>";
//                        echo "=======================================achivement=================".$sum_achievement."<br/>";
                        if($sum_achievement > 0 && $sum_target > 0){
                            $achieve_ratio = ($sum_achievement/$sum_target)*100;
                            $achieve_ratio_sum += $achieve_ratio;
                        }

//                        echo "=======================================achivement Ratio=================".round($achieve_ratio, 2)."<br/>";
                    }

                    $full_product_ration = round($achieve_ratio_sum, 2)/$target_type_count;
                    $ratio_after_weight_cal = ($full_product_ration*$kpi_weight)/100;
                    $ratio_after_weight_cal_sum += $ratio_after_weight_cal;
//                    echo "=======================================achivement Ratio Sum===######==============".$full_product_ration."<br/>";
//                    echo "#############".$kpi_weight."=========after=====".$ratio_after_weight_cal."<br/>";
                }
//                echo "#############*****************************=========after=====".$ratio_after_weight_cal_sum."<br/>";

                //********==============***************
                //Aproximate calculation
                //********==============***************

                $current_date = date('d');
                $last_date = date("t");

                $ratio_after_weight_cal_sum = ($ratio_after_weight_cal_sum*$last_date)/$current_date;

                if($ratio_after_weight_cal_sum<70){
                    $point_wise_achievement_ary[$pa_key]['below_70'][$pav_key] = $ratio_after_weight_cal_sum;
                }
                if($ratio_after_weight_cal_sum>70 && $ratio_after_weight_cal_sum<=80){
                    $point_wise_achievement_ary[$pa_key]['inside_80'][$pav_key] = $ratio_after_weight_cal_sum;
                }
                if($ratio_after_weight_cal_sum>=81 && $ratio_after_weight_cal_sum<=90){
                    $point_wise_achievement_ary[$pa_key]['inside_90'][$pav_key] = $ratio_after_weight_cal_sum;
                }
                if($ratio_after_weight_cal_sum>90){
                    $point_wise_achievement_ary[$pa_key]['upper_90'][$pav_key] = $ratio_after_weight_cal_sum;
                }

            }
        }

//       return $point_ary;
//        dd($point_wise_achievement_ary, $point_ary);

        if(!empty($point_wise_achievement_ary)){

            $tbl_string .="<br/><table class='table table-bordered col-md-6'>";
            $tbl_string .="<tr>";
            $tbl_string .="<th>Point Name</th>";
            $tbl_string .="<th>Less Then 70</th>";
            $tbl_string .="<th>70 To 80</th>";
            $tbl_string .="<th>81 To 90</th>";
            $tbl_string .="<th>More Then 90</th>";
            $tbl_string .="</tr>";

            foreach($point_wise_achievement_ary as $key=>$res){
                $tbl_string .="<tr>";
                $tbl_string .="<td>".$key."</td>";

                foreach($key_ary as $val) {
                    if (array_key_exists($val, $res)) {

                        $tbl_string .="<td>".count($res[$val])."</td>";
                    }
                    else{
                        $tbl_string .="<td>--</td>";
                    }
                }
                $tbl_string .="</tr>";
            }
            $tbl_string .="</table>";
        }
        else{
            $tbl_string .="No data found";
        }

        return $tbl_string;
    }


    function getLastMonthSalary(){
        $access_point = Session('PRIVILEGE_POINT');

        $q = DB::table('hr_monthly_salary_wages');
        $q->selectRaw('ifnull(sum(net_payable),0) as total');
        $q->where("hr_salary_month_name",'=',DB::raw("(SELECT MAX(hr_salary_month_name)  
        FROM hr_monthly_salary_wages where hr_monthly_salary_wages.bat_dpid IN($access_point))"));
        $q->whereIn('hr_monthly_salary_wages.bat_dpid',explode(',',$access_point));

        $monthly_fixed_salary = $q->first()->total;

        $q2 = DB::table('hr_emp_pfp_salary');
        $q2->selectRaw('ifnull(sum(pfp_earn_amount),0) as total');
        $q2->where("salary_month",'=',DB::raw("(SELECT MAX(salary_month)  
        FROM hr_emp_pfp_salary where hr_emp_pfp_salary.bat_dpid IN($access_point))"));
        $q2->whereIn('hr_emp_pfp_salary.bat_dpid',explode(',',$access_point));

        $monthly_pfp_salary = $q2->first()->total;
        return array('fixed_salary'=>$monthly_fixed_salary,'pfp_salary'=>$monthly_pfp_salary);
    }

    function getLastMonthPF(){
        $access_point = Session('PRIVILEGE_POINT');

        $q = DB::table('hr_monthly_salary_wages');
        $q->selectRaw('ifnull(sum(pf_amount_company),0) as company_total, ifnull(sum(pf_amount_employee),0) as employee_total');
        $q->where("hr_salary_month_name",'=',DB::raw("(SELECT MAX(hr_salary_month_name)  
        FROM hr_monthly_salary_wages where hr_monthly_salary_wages.bat_dpid IN($access_point))"));
        $q->whereIn('hr_monthly_salary_wages.bat_dpid',explode(',',$access_point));

        $monthly_pf_salary = $q->first();
        return array('company_total'=>$monthly_pf_salary->company_total,'employee_total'=>$monthly_pf_salary->employee_total);
    }

    function dashboard2(){
        $data = [];
        return view('Dashboard.__home', $data);
    }
    /*
     * Display Company Organograms
     */
    public function companyOrganogram(Request $request){
        $house = $request->house?$request->house:[];
        $houseArr = is_array ($house)?$house:array($house);

        $sql = DB::table('bat_routes')->where('bat_routes.ssid', '!=', 0)
            ->select('bat_company.company_name as house','bat_routes.ssid', 'sys_users.name as ss_name', 'bat_routes.srid', 'sr_user.name as sr_name')
            ->leftJoin('bat_company','bat_routes.dsid','=','bat_company.bat_company_id')
            ->leftJoin('sys_users','bat_routes.ssid','=','sys_users.id')
            ->leftJoin('sys_users AS sr_user','bat_routes.srid','=','sr_user.id')
            ->whereIn('bat_routes.dsid', $houseArr)
            ->groupBy('bat_routes.ssid')
            ->groupBy('bat_routes.srid')
            ->get();

        $data = [];
        if (!empty($sql)){
            foreach ($sql as $item) {
                $data[$item->house]['house'] = $item->house;
                if ($item->ss_name != null){
                    $data[$item->house]['ss'][$item->ssid]['name'] = $item->ss_name;
                    if ($item->srid !=0 && $item->sr_name !=null){
                        $data[$item->house]['ss'][$item->ssid]['sr'][]= array(
                            'srid'=>$item->srid,
                            'sr_name'=>$item->sr_name,
                        );
                    }
                }
            }
        }
//dd($data);
        return response()->json([
            'data' => view('Dashboard.organogram')->with('data',$data)->render()
        ]);
    }
}
