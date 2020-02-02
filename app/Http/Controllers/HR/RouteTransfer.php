<?php
namespace App\Http\Controllers\HR;

use App\Events\AuditTrailEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;
use Session;
use App\Helpers\PdfHelper;
use Symfony\Component\Routing\Tests\Fixtures\AnnotationFixtures\RequirementsWithoutPlaceholderNameController;

class RouteTransfer extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function routeListWithFF(Request $request, $dpid = null, $designation_id = null, $change_date=null){
//        dd(Session::all());
//        dd($request->all());
        $PRIVILEGE_POINT = explode(",",$request->session()->get('PRIVILEGE_POINT', '0'));
        $point = !empty($dpid)?$dpid:$PRIVILEGE_POINT[0];
        $designation_id = !empty($designation_id)?$designation_id:152;
        $change_date = !empty($change_date)?$change_date:date('Y-m-d');

        if(isset($_POST['submit'])){
            $designation_id = $request->change_designation_id;
            $point = $request->change_point;
            $change_date = $request->change_date;
        }

        if($designation_id == 151){
            //ss
            $route_sql =DB::select("SELECT
            number,sys_users_id,aten_user.bat_dpid,aten_user.emp_name,aten_user.bat_company_id,bat_distributorspoint.name as point_name
            FROM
                bat_routes
                LEFT JOIN (SELECT sys_users_id,sys_users.name as emp_name,designations_id,hr_emp_attendance.route_number,hr_emp_attendance.bat_dpid,
                hr_emp_attendance.bat_company_id 
                FROM hr_emp_attendance,sys_users 
                WHERE sys_users.id=hr_emp_attendance.sys_users_id 
                AND hr_emp_attendance.day_is = '$change_date' AND designations_id=$designation_id 
                AND hr_emp_attendance.bat_dpid = '$point') as aten_user 
                ON find_in_set(bat_routes.number,aten_user.route_number) 
                JOIN bat_distributorspoint on bat_routes.dpid = bat_distributorspoint.id
            WHERE
                bat_routes.stts =1
                and bat_routes.dpid=$point
                GROUP BY number ORDER BY number");

        }
        else{
            //sr
            $route_sql =DB::select("SELECT
                number,sys_users_id,aten_user.bat_dpid,aten_user.emp_name,aten_user.bat_company_id,bat_distributorspoint.name as point_name  
            FROM
                bat_routes
                LEFT JOIN (SELECT sys_users_id,sys_users.name as emp_name,designations_id,hr_emp_attendance.route_number,hr_emp_attendance.bat_dpid,
                hr_emp_attendance.bat_company_id 
                FROM hr_emp_attendance,sys_users
                WHERE sys_users.id=hr_emp_attendance.sys_users_id 
                AND hr_emp_attendance.day_is = '$change_date' AND designations_id=$designation_id AND hr_emp_attendance.bat_dpid = '$point') as aten_user 
                ON aten_user.route_number = bat_routes.number 
                JOIN bat_distributorspoint on bat_routes.dpid = bat_distributorspoint.id
            WHERE
                bat_routes.stts =1
                and bat_routes.dpid=$point
                GROUP BY number ORDER BY number");

        }
        $data['route_list'] = $route_sql;

        //--active ss sr emp list fetch with permission logic--
        $qury = DB::table('sys_users')->select('sys_users.name', 'sys_users.bat_dpid', 'sys_users.id', 'sys_users.designations_id');
        $session_con = (sessionFilter('url','emp-list-sr-ss'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qury->whereRaw($session_con);
        }
        $qury->where('sys_users.designations_id', $designation_id)
            ->where('is_employee', 1)
            ->where('bat_dpid', $point)
            ->where('status', 'Active');
        $data['emp_sr_ss_list'] = $qury->get();


        $data['emp_not_assign_list'] = [];

        $data['designation_id'] = $designation_id;
        $data['point'] = $point;
        $data['change_date'] = $change_date;
        $data['user_id'] = 555;

//        dd($data);

        return view('HR.route_transfer.route_list_with_ff', $data);
    }

    public function emptyRoute(Request $request){
//        dd($request->all());
        $change_date = $request->change_date;
        $route_number = $request->route_number;
        $dp_id = $request->dp_id;
        $company_id = $request->company_id;
        $user_id = $request->user_id;
        $designation_id = $request->designation_id;
        $route_ary = [];
        $emp_route_ary = [];

        DB::beginTransaction();

        try{
            if($designation_id == 151)
            {
                //ss
                $user_route_number = DB::table('hr_emp_attendance')->select('route_number')->whereRaw("FIND_IN_SET('$route_number',route_number)")
                    ->where('bat_dpid', $dp_id)->where('sys_users_id', $user_id)->where('day_is', '>=', $change_date)->first();


                if(!empty($user_route_number->route_number)){
                    $route_ary = explode(",", $user_route_number->route_number);

                    if(count($route_ary) > 0){
                        $ary_loc = array_search($route_number, $route_ary);

                        if($ary_loc >= 0 ){
                            unset($route_ary[$ary_loc]);
                            $route_string = implode (",", $route_ary);

                            DB::table('hr_emp_attendance')->select('route_number')
                                ->where('bat_dpid', $dp_id)->where('sys_users_id', $user_id)->where('day_is', '>=', $change_date)->update(['route_number' => $route_string]);
                        }
                    }
                }

                //update User table
                $update_emp = DB::table('sys_users')->select('route_number')->where('id', $user_id)->first();

                if(!empty($update_emp->route_number)){
                    $emp_route_ary = explode(",", $update_emp->route_number);

                    if(count($emp_route_ary) > 0){
                        $ary_loc = array_search($route_number, $emp_route_ary);

                        if($ary_loc >= 0 ){
                            unset($emp_route_ary[$ary_loc]);
                            $route_string_emp = implode (",", $emp_route_ary);

                            DB::table('sys_users')->where('id', $user_id)->update(['route_number' => $route_string_emp]);
                        }
                    }
                }
            }
            else{
                DB::table('hr_emp_attendance')->where('route_number', $route_number)
                    ->where('bat_dpid', $dp_id)->where('sys_users_id', $user_id)->where('day_is', '>=', $change_date)->update(['route_number'=>null]);

                DB::table('sys_users')->where('id', $user_id)->update(['route_number'=>null]);
            }

            $chk_route = DB::table('bat_route_log')->where('bat_route_number', $route_number)->where('bat_dpid', $dp_id)->where('inactive_emp_id', $user_id)
                ->where('active_emp_id', 0)->first();

            if(!$chk_route){
                $sav['bat_route_number'] = $route_number;
                $sav['inactive_emp_id'] = $user_id;
                $sav['bat_company_id'] = $company_id;
                $sav['bat_dpid'] = $dp_id;
                $sav['active_emp_id'] = 0;
                $sav['designation_id'] = $designation_id;
                $sav['date'] = $change_date;
                $sav['created_by'] = $request->session()->get('USER_ID', '0');
                $sav['created_at'] = date("Y-m-d h:i:sa");
                DB::table('bat_route_log')->insert($sav);
            }


            DB::commit();
            $data['status'] = 200;

        }catch (\Exception $e) {
            DB::rollback();
            $data['status'] = 500;
        }

        $data['dpid'] = $dp_id;
        $data['designation_id'] = $designation_id;
        $data['change_date'] = $change_date;

        return $data;
    }

    public function assignRoute(Request $request){

        $dpid = $request->hdn_dpid;
        $designation_id = $request->hdn_designation_id;
        $number = $request->hdn_route_number;
        $assign_emp_id = $request->change_emp_id;
        $change_date = $request->hdn_date;
        $route_ary = [];
        $emp_route_ary = [];

        DB::beginTransaction();

        try{
            if($designation_id == 151)
            {
                $user_route_number = DB::table('hr_emp_attendance')->select('route_number')
                    ->where('bat_dpid', $dpid)->where('sys_users_id', $assign_emp_id)->where('day_is', '>=', $change_date)->first();

                if(!empty($user_route_number->route_number)){
                    $route_ary = explode(",", $user_route_number->route_number);
                }

                array_push($route_ary, $number);
                $route_string = implode (",", $route_ary);

                DB::table('hr_emp_attendance')
                    ->where('bat_dpid', $dpid)->where('sys_users_id', $assign_emp_id)->where('day_is', '>=', $change_date)
                    ->update(['route_number' => $route_string]);


                //update User table
                $update_emp = DB::table('sys_users')->select('route_number')->where('id', $assign_emp_id)->first();

                if(!empty($update_emp->route_number)){
                    $emp_route_ary = explode(",", $user_route_number->route_number);

                    $ary_loc = array_search($number, $emp_route_ary);

                    if($ary_loc >= 0 ){
                        unset($emp_route_ary[$ary_loc]);
                    }
                }

                array_push($emp_route_ary, $number);
                $route_string_emp = implode (",", $emp_route_ary);
                DB::table('sys_users')->where('id', $assign_emp_id)->update(['route_number' => $route_string_emp]);

            }
            else{
                $t_aten = DB::table('hr_emp_attendance')->where('sys_users_id', $assign_emp_id)->where('day_is', '>=', $change_date)
                    ->where('bat_dpid', $dpid)->update(['route_number' => $number]);
                $t_user = DB::table('sys_users')->where('id', $assign_emp_id)->update(['route_number'=>$number]);

//                dd($request->all(), $assign_emp_id, $number, $t_aten, $t_user);
                if($t_aten == 0){
                    $data['code'] = 500;
                    $data['msg'] = "This user have no attendance.";
                    $data['dpid'] = $dpid;
                    $data['designation_id'] = $designation_id;
                    $data['change_date'] = $change_date;
                    return $data;
                }
            }

            $sav['active_emp_id'] = $assign_emp_id;
            $updated_by = $request->session()->get('USER_ID', '0');
            $updated_at = date("Y-m-d h:i:sa");
            $update_route = DB::table('bat_route_log')->where('bat_route_number', $number)->where('bat_dpid', $dpid)->where('active_emp_id', 0)
                ->update(['active_emp_id'=>$assign_emp_id, 'designation_id'=>$designation_id, 'date'=>$change_date, 'updated_by'=>$updated_by, 'updated_at'=>$updated_at]);

            if($update_route == 0){
                $sav['bat_route_number'] = $number;
                $sav['inactive_emp_id'] = $assign_emp_id;
                $sav['bat_dpid'] = $dpid;
                $sav['active_emp_id'] = 0;
                $sav['designation_id'] = $designation_id;
                $sav['date'] = $change_date;
                $sav['created_by'] = $request->session()->get('USER_ID', '0');
                $sav['created_at'] = date("Y-m-d h:i:sa");
                DB::table('bat_route_log')->insert($sav);
            }

            DB::commit();
            $data['code'] = 200;
            $data['msg'] = 'FF Assigned Successfully !';

        }catch (\Exception $e) {
            DB::rollback();
            $data['code'] = 500;
            $data['msg'] = 'FF Not Assigned !';
        }

        $data['dpid'] = $dpid;
        $data['designation_id'] = $designation_id;
        $data['change_date'] = $change_date;

        return $data;
    }

    public function routeLog(){

        $log_qury = DB::table('bat_route_log')->selectRaw(" bat_route_log.date,bat_route_log.bat_route_number, sys_users.`name` as inactive_emp_name, 
              active_user_tbl.name as active_emp_name, bat_distributorspoint.`name` as point_name, bat_route_log.created_at, bat_route_log.updated_at")
            ->leftjoin('sys_users', 'bat_route_log.inactive_emp_id', '=', 'sys_users.id')
            ->leftjoin('sys_users as active_user_tbl', 'bat_route_log.active_emp_id', '=', 'active_user_tbl.id')
            ->join('bat_distributorspoint', 'bat_route_log.bat_dpid', '=', 'bat_distributorspoint.id');
        $session_con = (sessionFilter('url','emp-list-sr-ss'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $log_qury->whereRaw($session_con);
        }
        $data['logs'] =   $log_qury->get();

        return view('HR.route_transfer.route_log', $data);
    }
}