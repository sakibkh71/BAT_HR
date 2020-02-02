<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Kpi\KpiConfig;
// use App\Models\Kpi\KpiConfigDetails;
// use App\Models\Kpi\KpiProperties;
// use App\Models\Kpi\BatCats;
// use App\Models\Kpi\BatProducts;
// use App\Models\Kpi\KpiAssignedEmployee;
// use App\Models\Kpi\KpiAssignedEmpDetails;
// use App\Models\Kpi\KpiAssignTemp;
// use App\Models\HR\Employee;
use Validator;
use Auth;
use URL;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

class KpiAchievementController extends Controller
{
	public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function index(){

    	$data = [];
    	return view('HR.kpi.achievement', $data);	
    }

    public function kpiAchievementGet(Request $request){

    	$return_data = [];
		$config_id = KpiConfig::where('start_month', '<=', $request->target_month)->where('end_month', '>=', $request->target_month)
						->whereRaw("FIND_IN_SET($request->kpi_house_id,scope_market)")->pluck('bat_kpi_configs_id')
						->first(); 
		$house_id = $request->kpi_house_id;
		// $target_month = $request->target_month;
		$target_month = $request->target_month;
		$datas = DB::select(DB::raw("select sys_users.user_code,sys_users.designations_id, assign_emp.bat_kpi_configs_id, assign_emp.bat_kpi_properties_id, 
				assign_emp.target_type, assign_emp.target_month, assign_emp.bat_kpi_assigned_employee_id, 
				assign_details.bat_products_id, assign_details.bat_kpi_assigned_emp_details_id, assign_details.target_set   
				from sys_users 
				join bat_kpi_assigned_employee as assign_emp on sys_users.user_code = assign_emp.user_code 
				join bat_kpi_assigned_emp_details as assign_details 
				on assign_emp.bat_kpi_assigned_employee_id = assign_details.bat_kpi_assigned_employee_id
				where (sys_users.bat_company_id = '$house_id' and assign_emp.bat_kpi_configs_id = '$config_id'
				and assign_emp.target_month = '$target_month')"));
		// dd($request->all(), $datas);

		$q = DB::table('bat_kpi_config_details')->where('bat_kpi_configs_id',$config_id);
		$kpi_weight = $q->get();
		$weights = [];
		foreach ($kpi_weight as $weight){
            $weights[$weight->bat_kpi_properties_id] =$weight->weight;
        }

		$final_post_ary = [];
		$final_core_ary = [];

		if(!empty($datas)){
			$final_post_ary['basic'][] = [
				'target_month' => $target_month,
				'house_id' => $house_id
			];

			foreach($datas as $info){
				$final_post_ary['info'][] = [
					'user_id' => $info->user_code,
					'designation' => $info->designations_id,
					'properties_id' => $info->bat_kpi_properties_id,
                    'assign_emp_id' => $info->bat_kpi_assigned_employee_id,
					'target_type' => $info->target_type,
                    'target_set' => $info->target_set,
					'product_id' => $info->bat_products_id,
					'assign_detail_id' => $info->bat_kpi_assigned_emp_details_id
				];
			}


			$temp = 0;
			$counter = 1;
			$achieve_counter = 0;
			$only_one = 0;

			$array = json_encode($final_post_ary);

            $postData = array(
              'data' => $array
            );

            // dd($postData);

            $handle = curl_init();
 
            $url = "https://newprism.net/batb_hr/achievement/";

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

	        $all_employee = [];
	        foreach($result as $item){
	            $item = (array)$item;
	            $all_employee[$item['user_id']][$item['properties_id']][$item['product_id']] = array_merge($item,array(
	                'achievement_ratio'=>$item['target_set']!=0?($item['achievement']*100)/$item['target_set']:0
	            ));
	        }
	        
	        $achived_users = [];

	        DB::beginTransaction();

            try{
		        foreach ($all_employee as $user_id=>$user){
		            foreach ($user as $properties_id=>$achive){
		                $assign_emp_id = '';
		                $ratio = 0;
		                $sum_achievement = 0;
		                $sum_target = 0;
		                foreach ($achive as $each_achive){
		                    $assign_emp_id = $each_achive['assign_emp_id'];
		                    DB::table('bat_kpi_assigned_emp_details')->where('bat_kpi_assigned_emp_details_id',$each_achive['assign_detail_id'])->update(['target_achive'=>$each_achive['achievement']]);

		                    //previous calculation
//		                    $ratio += $each_achive['achievement_ratio'];
                            //dd($each_achive);
		                    //new calculation method
                            $sum_achievement += $each_achive['achievement'];
                            $sum_target += $each_achive['target_set'];
		                }
		                //previous calculation
//		                $ratio = $ratio!=0?($weights[$properties_id]*($ratio/count($achive))/100):0;

		                //new calculation method
//                        dd($user_id,$sum_achievement, $sum_target, $weights[$properties_id]);
                        $ratio = $sum_achievement!=0?(($sum_achievement/$sum_target)*($weights[$properties_id])):0;


		                DB::table('bat_kpi_assigned_employee')->where('bat_kpi_assigned_employee_id',$assign_emp_id)->update(['total_achivement'=>$ratio]);
		            }

		        }

		    	DB::commit();
                $return_data['msg'] = "Data updated Successfully!";
                $return_data['code'] = 200;

            }catch (\Exception $e) {
               
                DB::rollback(); 
                $return_data['msg'] = "Data not update!";
                $return_data['code'] = 500;
            }
		}
		else{
			DB::rollback(); 
            $return_data['msg'] = "No data found!";
            $return_data['code'] = 500;
		}




	    return $return_data;

    }

    function getManualAchievement(Request $request){
	    $data = [];
	    if($request->all()){
	        $date = $request->achievement_date;
	        $point_ids = $request->point;
	        self::get_achievement_prism($date,$point_ids);
        }
        $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationTree')->searchForm();
        return view('HR.kpi.achievement', $data);
    }
    function get_achievement_prism($date='',$point_ids=''){

        ini_set('memory_limit','1024M');
        ini_set('max_execution_time','1200');
        DB::select('SET SESSION group_concat_max_len = 1000000');

        $today = $date?$date:date('Y-m-d');
        $month = date('Y-m',strtotime($today));
// get today active SR list

        $q = DB::table('hr_emp_attendance');
        $q->selectRaw('hr_emp_attendance.bat_dpid,
                hr_emp_attendance.user_code,
                hr_emp_attendance.route_number');
        $q->join('sys_users','sys_users.id','hr_emp_attendance.sys_users_id');
        $q->where('sys_users.designations_id',152);
        if($point_ids!=''){
            $q->whereIn('hr_emp_attendance.bat_dpid',$point_ids);
        }
      //  $q->whereNotNull('hr_emp_attendance.bat_dpid');
        $q->where('hr_emp_attendance.day_is',$today);
        $assign_employeesSR = $q->get();

        $empRoutesSR = [];
        foreach ($assign_employeesSR as $employee){
            $empRoutesSR[$employee->bat_dpid][$employee->route_number]  = $employee->user_code;
        }

//        $empCodesSR = array_flip($empRoutesSR);

// get today active SS list


        $q2 = DB::table('hr_emp_attendance');
        $q2->selectRaw('hr_emp_attendance.bat_dpid,
            hr_emp_attendance.user_code,
            hr_emp_attendance.route_number');
        $q2->join('sys_users','sys_users.id','hr_emp_attendance.sys_users_id');
        $q2->where('sys_users.designations_id',151);
        if($point_ids!=''){
            $q2->whereIn('hr_emp_attendance.bat_dpid',$point_ids);
        }
      //  $q2->whereNotNull('hr_emp_attendance.bat_dpid');
        $q2->where('hr_emp_attendance.day_is',$today);

        $assign_employeesSS = $q2->get();
        $empRoutesSS = [];
        foreach ($assign_employeesSS as $employee){
            $SSRoutes = explode(',',$employee->route_number);
            if(!empty($SSRoutes)){
                foreach ($SSRoutes as $ss){
                    $empRoutesSS[$employee->bat_dpid][$ss]  = $employee->user_code;
                }
            }
        }

       $sql = DB::table('bat_kpi_configs');
        $sql->selectRaw('bat_company_id,
                group_concat(DISTINCT bat_dpid) bat_dpid,
                bat_kpi_id,
                target_brands,
                target_familys,
                target_segments');
        $sql->join('bat_kpi_config_details','bat_kpi_configs.kpi_config_code','bat_kpi_config_details.kpi_config_code');
        $sql->where('bat_kpi_configs.config_month',$month);
        if($point_ids!=''){
            $sql->whereIn('bat_kpi_configs.bat_dpid',$point_ids);
        }
        $sql->groupBy('bat_kpi_configs.kpi_config_code','bat_kpi_id');
        $config_detail = $sql->get();
//        dd($config_detail);
//dd($assign_employeesSS,$assign_employeesSR);
if(!empty($config_detail)){

    $url = "https://newprism.net/batb_hr/achievement_byPoint";
    $response_data2 = [];
//    DB::beginTransaction();
//    try {
        foreach ($config_detail as $config) {
            DB::table('bat_daily_kpi_achievements')
                ->whereIn('bat_dpid',explode(',',$config->bat_dpid))
                ->where('bat_kpi_id',$config->bat_kpi_id)
                ->where('achievement_date',$today)
                ->delete();
            $postData = array(
                'bat_dpid' => $config->bat_dpid,
                'date' => $today,
                'bat_kpi_id' => $config->bat_kpi_id,
                'target_brands' => $config->target_brands,
                'target_familys' => $config->target_familys,
                'target_segments' => $config->target_segments
            );
            $handle = curl_init();
            curl_setopt_array($handle,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData,
                    CURLOPT_RETURNTRANSFER => true,

                )
            );
            $data = curl_exec($handle);

            curl_close($handle);
            $result = json_decode($data);
            if (!empty($result)) {

                $response_data = [];
                foreach ($result as $each) {
                    if($each->achievement>0){
                        $response_data[] = array(
                            'bat_dpid' => $each->dpid,
                            'sr_user_code' => isset($empRoutesSR[$each->dpid][$each->rt_no]) ? $empRoutesSR[$each->dpid][$each->rt_no] : null,
                            'ss_user_code' => isset($empRoutesSS[$each->dpid][$each->rt_no]) ? $empRoutesSS[$each->dpid][$each->rt_no] : null,
                            'route_number' => $each->rt_no,
                            'achievement_date' => $today,
                            'target_type' => $each->target_type,
                            'bat_kpi_id' => $each->kpi_id,
                            'target_ref_id' => $each->scope,
                            'daily_achievement' => $each->achievement,
                            'created_at' => dTime(),
                            'created_by' => Auth::id()
                        );
                    }

                }

//                DB::table('bat_daily_kpi_achievements')->insert($response_data);
//                return $response_data;
////                dd($response_data);
                foreach (array_chunk($response_data, 1000) as $sub_response) {
                    DB::table('bat_daily_kpi_achievements')->insert($sub_response);
                }

            }
        }
   // dd($response_data2);

//        DB::commit();
//    }
//     catch (\Exception $exception){
//        DB::rollback();
//        throw $exception;
//    } catch (\Throwable $exception){
//        DB::rollback();
//        throw $exception;
//    }

}

    }
}