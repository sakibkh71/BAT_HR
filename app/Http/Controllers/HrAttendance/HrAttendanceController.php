<?php

namespace App\Http\Controllers\HrAttendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Psr\Log\NullLogger;
use URL;
use DB;
use Input;
use Redirect;
use Auth;
use Session;
use Validator;
use File;
use DateTime;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class HrAttendanceController extends Controller {

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    private function getNumberOfActiveEmpByHrEmpCategory($categoy_id){
        $sql = DB::table('sys_users');
        $sql->select(DB::raw('count("sys_users.*") as total'));
        $sql->where('sys_users.status', 'Active');
        $sql->where('sys_users.hr_emp_categorys_id', $categoy_id);
        $sql->where('sys_users.is_employee', 1);
        $result = $sql->get()->first();
        if($result->total){
            $output = $result->total;
        }else{
            $output = 0;
        }
        return $output;
    }

    public function hrEmployeeMontlyHolidayCheck(Request $request){
        $post = $request->except('_token');
        $last_date = date("t", strtotime($post['hr_salary_month_name']));
        $total_allocated_days = $post['number_of_working_days'] + $post['number_of_holidays'] + $post['number_of_weekend'];
        if($last_date == $total_allocated_days){
            echo "matched";
        }else{
            echo "not matched";
        }
    }

    /*
     * Attendance Entry
     */
    public function attendanceEntry(Request $request, $id = null){
        $data['title'] = "Attendance Entry";
        if ($id !=null){
            $data['title'] = "Attendance Update";
            $data['attendance'] = DB::table('hr_emp_attendance')->where('hr_emp_attendance_id',$id )->first();
        }else{
            $data['title'] = "Attendance Entry";
            $date = $request->bulk_day_is?$request->bulk_day_is:date('Y-m-d');
            if($request->bulk_day_is){
                DB::select('call proc_attendance_sync(:date)',[':date'=>$date]);
            }
            $sql = DB::table('hr_temporary_emp_attendance');
            $sql->join('sys_users','sys_users.user_code','=','hr_temporary_emp_attendance.user_code');
            $sql->where('hr_temporary_emp_attendance.log_time','LIKE',"%$date%");
            $sql->orderBy('hr_temporary_emp_attendance.log_time','DESC');
            $data['deviceData'] = $sql->paginate(10);
            $data['bulk_day_is'] = $date;
        }

        return view('HrAttendance.attendance_entry', $data);
    }

    public function attendanceEntryFromList(Request $request){
//dd($request->all());
        $PRIVILEGE_POINT = explode(",",$request->session()->get('PRIVILEGE_POINT', '0'));
        $data['title'] = "Attendance Entry List";
        $data['users'] = "";
        $data['search_date'] = date("Y-m-d");
        $data['designation_id'] = null;
        $data['dpid'] = $PRIVILEGE_POINT[0];

        if(isset($_POST['submit'])){
            $data['search_date'] = $request->attendance_date;
            $data['designation_id'] = $request->designations;
            $data['dpid'] = $request->bat_dpid;
//            dd($request->all(), $data['designation_id'], $data['dpid']);
        }

        $employeeInfo = DB::table('hr_emp_attendance')
            ->select('bat_distributorspoint.name as distibutor_point','sys_users.id','sys_users.name', 'hr_emp_attendance.alter_user_code',
                    'sys_users.user_code', 'sys_users.mobile', 'designations.designations_name', 'designations.designations_id', 'hr_emp_attendance.shift_start_time',
                'hr_emp_attendance.route_number','hr_emp_attendance.shift_end_time', 'hr_emp_attendance.daily_status','hr_emp_attendance.day_is')
            ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
            ->join('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->join('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->where('hr_emp_attendance.day_is', $data['search_date']);
            if(!empty($data['dpid'])){
                $employeeInfo->where('sys_users.bat_dpid', $data['dpid']);
            }
            if(!empty($data['designation_id'])){
                $employeeInfo->whereIn('sys_users.designations_id', $data['designation_id']);
            }
            $employeeInfo->where('sys_users.is_employee', 1)->whereIn('sys_users.status', ['Active', 'Probation']);

        $session_con = (sessionFilter('url','attendance-entry'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $employeeInfo->whereRaw($session_con);
        }
        $data['users'] = $employeeInfo->get();

        $data['extra_sr'] = DB::table('sys_users')->select('sys_users.id', 'sys_users.user_code', 'sys_users.name', 'hr_emp_attendance.route_number')
            ->join('hr_emp_attendance', 'sys_users.id', '=', 'hr_emp_attendance.sys_users_id')
            ->where('is_employee', 1)
            ->where('hr_emp_attendance.day_is', $data['search_date'])
            ->where('hr_emp_attendance.daily_status', 'P')
            ->where('status', 'Active')
            ->where('sys_users.bat_dpid', $data['dpid'])->where('designations_id', 543)
            ->get();

//        dd($data['extra_sr']);

        return view('HrAttendance.attendance_entry_list', $data);
    }

    public function getExtraSrList(Request $request){

        $search_date = $request->current_date;
        //$dpid = $request->dpid;
        $dpid = explode(",",session::get('PRIVILEGE_POINT'));
        $designations_ary = $request->designations;


        $employeeInfo = DB::table('hr_emp_attendance')
            ->select('bat_distributorspoint.name as distibutor_point','sys_users.id','sys_users.name', 'hr_emp_attendance.alter_user_code',
                'sys_users.user_code', 'sys_users.mobile', 'designations.designations_name', 'designations.designations_id', 'hr_emp_attendance.shift_start_time',
                'hr_emp_attendance.route_number','hr_emp_attendance.shift_end_time', 'hr_emp_attendance.daily_status','hr_emp_attendance.day_is')
            ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
            ->join('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->join('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->where('hr_emp_attendance.day_is', $search_date);
        if(!$dpid ){
            //if(!empty($data['dpid'])){
            $employeeInfo->whereIn('sys_users.bat_dpid', $dpid);
        }
        if(!empty($designations_ary)){
            $employeeInfo->whereIn('sys_users.designations_id', $designations_ary);
        }
        $employeeInfo->where('sys_users.is_employee', 1)->whereIn('sys_users.status', ['Active', 'Probation']);

        $session_con = (sessionFilter('url','attendance-entry'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $employeeInfo->whereRaw($session_con);
        }
        $data['users'] = $employeeInfo->get();

        $data['all_ex_sr_list'] = DB::table('sys_users')
            ->select('sys_users.id', 'sys_users.user_code', 'sys_users.name')
            ->where('is_employee', 1)
            ->whereIn('status', ['Active', 'Probation'])
            ->whereIn('sys_users.bat_dpid', $dpid)->where('designations_id', 543)
            ->get();
        //echo "<pre>"; print_r($data['all_ex_sr_list']); exit;

        $data['ex_sr_list'] = DB::table('sys_users')->select('sys_users.id', 'sys_users.user_code', 'sys_users.name', 'hr_emp_attendance.route_number')
            ->join('hr_emp_attendance', 'sys_users.id', '=', 'hr_emp_attendance.sys_users_id')
            ->where('is_employee', 1)
            ->where('hr_emp_attendance.day_is', $search_date)
            ->whereRaw("(hr_emp_attendance.route_number = 0 
                    OR hr_emp_attendance.route_number = ''
                    OR hr_emp_attendance.route_number IS NULL)")
            ->where('hr_emp_attendance.daily_status', 'P')
            ->whereIn('status', ['Active', 'Probation'])
            ->whereIn('sys_users.bat_dpid', $dpid)->where('designations_id', 543)
            ->get();

        //echo "<pre>"; print_r($data['ex_sr_list']); exit;

        $data['code'] = 200;

        return $data;
    }

    public function assignRouteForEsr(Request $request){

        $date = $request->present_date;
        $esr_dpid = $request->esr_dpid;
        $esr_route_number = $request->esr_route_number;
        $assign_emp_code = $request->assign_emp_code;
        $current_user_code = $request->current_user_code;

        DB::beginTransaction();

        try{
            if(empty($esr_route_number)){
                $get_alter_sr_route = DB::table('hr_emp_attendance')->select('alter_user_code')->where('user_code', $current_user_code)->where('day_is', $date)->first();

                //applicable when after assign ex sr then change ex sr
                if($get_alter_sr_route){
                    $alter_sr_route = DB::table('hr_emp_attendance')->select('route_number')->where('user_code', $get_alter_sr_route->alter_user_code)
                        ->where('day_is', $date)->first();
                    $esr_route_number = $alter_sr_route->route_number;
                    DB::table('hr_emp_attendance')->where('user_code', $get_alter_sr_route->alter_user_code)
                        ->where('day_is', $date)->update(['route_number' => null]);
                }
            }

            DB::table('hr_emp_attendance')->where('user_code', $current_user_code)->where('day_is', $date)
                ->update(['alter_user_code' => $assign_emp_code, 'route_number'=> null]);

            if(!empty($assign_emp_code)){

                DB::table('hr_emp_attendance')->where('day_is', $date)
                    ->where('user_code', $assign_emp_code)
                    ->update(['route_number'=>$esr_route_number,
                        'bat_dpid'=>$esr_dpid]);
            }

            DB::commit();
            $data['status'] = 200;

        }catch (\Exception $e) {
            DB::rollback();
            $data['status'] = 500;
        }

        $data['current_user_code'] = $current_user_code;
        return $data;
    }

    public function empChangeAttendance($user_id, $date, $status){
//        dd($user_id, $date, $status);

        if(!empty($user_id)){
            $getData = DB::table('hr_emp_attendance')->where('sys_users_id', $user_id)->where('day_is', $date)->first();
            $sys_user = DB::table('sys_users')->select('designations_id')->find($user_id);

//            DB::table('hr_emp_attendance')->where('alter_user_code', $getData->alter_user_code)
//                ->where('day_is', $date)->update(['route_number'=>null]);

//            dd($user_id, $getData->alter_user_code);


            if($status=='Present'){
                $in_time = $date." ".$getData->shift_start_time;
                $out_time = $date." ".$getData->shift_end_time;
                $status = 'P';
                $alter_user_code = 0;
            }
            else{
                $in_time = null;
                $out_time = null;
                $status = 'A';
                $alter_user_code = 0;
            }

//            dd($in_time, $status);

            DB::table('hr_emp_attendance')->where('sys_users_id', $user_id)->where('day_is', $date)
                ->update(['in_time'=>$in_time, 'out_time'=>$out_time, 'daily_status'=>$status, 'alter_user_code'=> $alter_user_code]);

        }

        $data['status'] = 200;
        $data['attendance'] = $status;
        $data['user_code'] = $getData->user_code;
        $data['designations_id'] = $sys_user->designations_id;
        return $data;
    }

    /*
     * store Employee Attendance Manual Entry
     */
    public function HrEmployeeAttendanceManualEntry(Request $request, $id = null){

        $request->validate([
            'sys_users_id' => 'required',
            //'in_time' => 'required',
            //'out_time' => 'required',
        ]);
        if(in_array($request->daily_status,['P','L','EO'])){
            $request->in_time = '09:00';
            $request->out_time = '18:00';
        }

        $in_time = $request->daily_status !='A' ? (isset($request->in_time)? $request->day_is . ' ' . date("H:i:s",strtotime($request->in_time)):null):null;
        $out_time = $request->daily_status !='A' ? (isset($request->out_time)? $request->day_is . ' ' . date("H:i:s",strtotime($request->out_time)):null):null;

        $insert_data=[
            'in_time'                => $in_time,
            'out_time'               => $out_time,
            'daily_status'           => $request->daily_status,
            'record_mode'            => 'ManualEntry',
        ];

        $check_data =   DB::table('hr_emp_attendance')
            ->where('day_is', $request->day_is)
            ->where('sys_users_id', $request->sys_users_id)
            ->first();

        if ($check_data){
            DB::table('hr_emp_attendance')
                ->where('day_is', $request->day_is)
                ->where('sys_users_id', $request->sys_users_id)
                ->update($insert_data);
            return redirect('attendance-entry')->with('success', 'Attendance Update Successfully!');
        }else{
            return redirect('attendance-entry')->with('error', 'Sorry! we can\'t find any data for the date of this employee');
        }
    }


    /*
     * Check Employee Attendance by user id and date
     */
    public  function checkEmployeeAttendance(Request $request){
       // DB::connection()->enableQueryLog();
        $checkData = DB::table('hr_emp_attendance')->where('day_is', $request->date)->where('sys_users_id', $request->user)
            //->where('hr_working_shifts_id', $request->shift)
            ->first();
if(!empty($checkData)){
    $check['day_is']        = $checkData->day_is;
    $check['daily_status']  = $checkData->daily_status;
    $check['is_salary_enabled']  = $checkData->is_salary_enabled;
    $check['in_time']       = !empty($checkData->in_time)? date("H:i:s",strtotime($checkData->in_time)):'';
    $check['out_time']      = !empty($checkData->out_time)? date("H:i:s",strtotime($checkData->out_time)):'';

    // dd(DB::getQueryLog());
    return response()->json([
        'status' => 'success',
        'data' =>  $check,
    ]);
}else{
    return response()->json([
        'status' => 'error',
    ]);
}

    }

    /*
     * Check Employee Attendance by user id and date
     */
    public  function getEmpShift(Request $request){
        $shifts = DB::table('hr_emp_attendance')
            ->join('hr_working_shifts', 'hr_emp_attendance.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
            ->select('hr_working_shifts.hr_working_shifts_id','hr_working_shifts.shift_name')
            ->where('day_is', $request->date)
            ->where('sys_users_id', $request->user)
            ->get();
        return response()->json([
            'status' => 'success',
            'data' =>  $shifts,
        ]);
    }


    public function previewAttendanceHistory(Request $request){
        $document = $request->file('select_file');
        $original_name = $document->getClientOriginalName();
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $current_date_time = strtotime(date('Y-m-d H:m:s'));
        $new_name = $filename.'_'.$current_date_time. '.' . $document->getClientOriginalExtension();
        if (!is_dir(public_path('documents/attendance'))) {
            mkdir(public_path('documents/attendance'), 0777, true);
        }
        $document->move(public_path('documents/attendance'), $new_name);
        if('csv' == $file_extension || ('xlsx' == $file_extension || 'XLSX' ==$file_extension)) {
            if('csv' == $file_extension){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }else{
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            try {
                $path = public_path('documents/attendance/').$new_name;
                $spreadsheet = $reader->load($path);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                if(!empty($sheetData) && (
                    count($sheetData[0]) !=8 ||
                    $sheetData[0][0] !='SL' ||
                    $sheetData[0][1] !='user_code' ||
                    $sheetData[0][2] !='day_is' ||
                    $sheetData[0][3] !='status' ||
                    $sheetData[0][4] !='start_date_time' ||
                    $sheetData[0][5] !='end_date_time' ||
                    $sheetData[0][6] !='break_time' ||
                    $sheetData[0][7] !='ot_hours')
                ){
                    return redirect()->route('attendance-entry')
                        ->with('warning','Please provide correct formatted file');
                }

                if(!empty($sheetData)){
                    //If exist previous temporary data for this user delete all previos informatioon.
                    $is_have_existing_data = DB::table('hr_temporary_emp_attendance')
                        ->select('hr_temporary_emp_attendance.*')
                        ->where('hr_temporary_emp_attendance.created_by','=',Auth::id())
                        ->get()->first();

                    if(!empty($is_have_existing_data)){
                        DB::table('hr_temporary_emp_attendance')->where('hr_temporary_emp_attendance.created_by', '=', Auth::id())->delete();
                    }

                    $prepare_arr = [];

                    array_shift($sheetData);

                    foreach ($sheetData as $k=>$value){

                        $val_4 = !empty($value[4])? date("Y-m-d H:i:s",strtotime(strlen($value[4])>10?$value[4]: $value[2].' '.$value[4])) : null;
                        $val_5 = !empty($value[5])? date("Y-m-d H:i:s",strtotime(strlen($value[5])>10?$value[5]: $value[2].' '.$value[5])): null;

                        $actual_working_hour = self::getTotalWorkingTime($val_4,$val_5,$value[6]?$value[6]:0,$value[7]?$value[7]:0);
                        $prepare_arr[$k]['user_code']  = trim($value[1]);
                        $prepare_arr[$k]['day_is'] = date('Y-m-d',strtotime($value[2]));
                        $prepare_arr[$k]['daily_status'] = $value[3];
                        $prepare_arr[$k]['in_time'] = $val_4;
                        $prepare_arr[$k]['out_time'] =  $val_5;
                        $prepare_arr[$k]['break_time'] = (float)$value[6]?$value[6]:0;
                        $prepare_arr[$k]['ot_hours'] = (float)$value[7]?$value[7]:0;
                        $prepare_arr[$k]['total_work_time'] = (float)$actual_working_hour;
                        $prepare_arr[$k]['created_at'] = date('Y-m-d H:i:s');
                        $prepare_arr[$k]['created_by'] = Auth::id();
                        $prepare_arr[$k]['file_name'] = $current_date_time;
                    }
                    foreach (array_chunk($prepare_arr,1000) as $t){
                        DB::table('hr_temporary_emp_attendance')->insert($t);
                    }
                    /*foreach ($prepare_arr as $t){
                        DB::table('hr_temporary_emp_attendance')
                            ->where('user_code', '=', $t['user_code'])
                            ->where('day_is', '=', $t['day_is'])
                            ->update($t);
                    }*/

                    return redirect()->route('attendance-final-process')
                        ->with('info','Successfully Uploaded!');
                }else{
                    return redirect()->route('attendance-entry')
                        ->with('warning','data is not found');
                }
            }catch (Exception $e) {
                return redirect()->route('attendance-entry')
                    ->with('error','Error occured!');
            }
        }
    }


    private function getUserInfo($user_code){
        $sql  = DB::table('sys_users')
            ->select('sys_users.*')
            ->addSelect('hr_emp_sections.hr_emp_section_name')
            ->join('hr_emp_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id')
            ->where('sys_users.user_code','=',$user_code)
            ->get()->first();
        return $sql;
    }


    private function getTotalWorkingTime($start_hour_min_second,$end_hour_min_second,$break_time,$overtime){
        if ($start_hour_min_second !=null && $end_hour_min_second !=null){
            $start = date("Y-m-d H:i",strtotime($start_hour_min_second));
            $end = date("Y-m-d H:i",strtotime($end_hour_min_second));
            $datetime1 = new DateTime($start);
            $datetime2 = new DateTime($end);
            $interval = $datetime1->diff($datetime2);
            $time_difference = $interval->format('%h').".".$interval->format('%i');
            $actual_working_time = $time_difference -($break_time)+$overtime;
            return $actual_working_time;
        }else{
            return 0;
        }

    }

    public function attendanceFinalProcess(Request $request){
        $post = $request->except('_token');
        $data['title'] = "Draft Attendance List";
        $data['info'] = self::getHrTemporaryEmployeeAttendance();
        return view('HrAttendance.show_hr_employee_attendance_details', $data);
    }


    public function showHrEmployeeAttendanceDetails($id){
        $data['title'] = "Attendance Details";
        $data['info'] = self::getHrTemporaryEmployeeAttendance($id);
        return view('HrAttendance.show_hr_employee_attendance_details', $data);
    }

    public function deleteHrEmployeeAttendanceInfo(Request $request){
        $post = $request->except('_token');
        $table = $post['table'];
        $code = $post['code'];
        $day_is = $post['day_is'];
        $start_date_time = $post['start_date_time'];
        DB::table($table)->where($table.'.user_code', '=', $code)->where($table.'.day_is', '=', $day_is)->where(DB::raw($table.'.start_date_time'), '=', $start_date_time)->delete();
        echo 'successfully deleted!';
    }

    public function HrEmployeeAttendanceProcess(Request $request){
        $post = $request->except('_token');
        $action_type = $post['action_type'];
        $prepare_arr = [];
        $dl_attendance = [];
        if("process" == $action_type){
            $temp_attendance_history = self::getHrTemporaryEmployeeAttendance();

            foreach ($temp_attendance_history as $key=>$val) {
                if($val->duplicate == null){
                    $prepare_arr[$key]['sys_users_id']  = $val->sys_users_id;
                    $prepare_arr[$key]['user_code']  = trim($val->user_code);
                    $prepare_arr[$key]['day_is'] = $val->day_is;
                    $prepare_arr[$key]['daily_status'] = $val->daily_status;
                    $prepare_arr[$key]['hr_emp_categorys_id'] = $val->hr_emp_categorys_id;
                    $prepare_arr[$key]['hr_working_shifts_id'] = $val->hr_working_shifts_id;
                    $prepare_arr[$key]['hr_emp_sections_id'] = $val->hr_emp_sections_id;
                    $prepare_arr[$key]['in_time'] = $val->in_time;
                    $prepare_arr[$key]['out_time'] = $val->out_time;
                    $prepare_arr[$key]['break_time'] = $val->break_time;
                    $prepare_arr[$key]['total_work_time'] = $val->total_work_time;
                    $prepare_arr[$key]['ot_hours'] = $val->ot_hours;
                    $prepare_arr[$key]['created_at'] = $val->created_at;
                    $prepare_arr[$key]['created_by'] = $val->created_by;
                    $prepare_arr[$key]['file_name'] = $val->file_name;
                }
            }

            /*foreach (array_chunk($prepare_arr,1000) as $t){
                DB::table('hr_emp_attendance')->insert($t);
            }*/

            foreach ($prepare_arr as $t){
                DB::table('hr_emp_attendance')
                    ->where('user_code', '=', $t['user_code'])
                    ->where('day_is', '=', $t['day_is'])
                    ->update($t);
            }

            DB::table('hr_temporary_emp_attendance')->where('hr_temporary_emp_attendance.created_by', '=', Auth::id())->delete();
            echo "succeed";

        }else{
            $temp_attendance_history = self::getHrTemporaryEmployeeAttendance();
            foreach ($temp_attendance_history as $key=>$val) {

                if ($val->approved_status != 'locked' && $val->duplicate != null && !in_array($val->hr_emp_attendance_id, $dl_attendance)){
                    $dl_attendance[] = $val->hr_emp_attendance_id;
                }

                if($val->approved_status != 'locked'){
                    $prepare_arr[$key]['sys_users_id'] = trim($val->sys_users_id);
                    $prepare_arr[$key]['user_code']  = trim($val->user_code);
                    $prepare_arr[$key]['day_is'] = $val->day_is;
                    $prepare_arr[$key]['daily_status'] = $val->daily_status;
                    $prepare_arr[$key]['hr_emp_categorys_id'] = $val->hr_emp_categorys_id;
                    $prepare_arr[$key]['hr_working_shifts_id'] = $val->hr_working_shifts_id;
                    $prepare_arr[$key]['hr_emp_sections_id'] = $val->hr_emp_sections_id;
                    $prepare_arr[$key]['in_time'] = $val->in_time;
                    $prepare_arr[$key]['out_time'] = $val->out_time;
                    $prepare_arr[$key]['break_time'] = $val->break_time;
                    $prepare_arr[$key]['total_work_time'] = $val->total_work_time;
                    $prepare_arr[$key]['ot_hours'] = $val->ot_hours;
                    $prepare_arr[$key]['created_at'] = $val->created_at;
                    $prepare_arr[$key]['created_by'] = $val->created_by;
                    $prepare_arr[$key]['file_name'] = $val->file_name;
                }
            }
            /*if (!empty( $dl_attendance)){
                DB::table('hr_emp_attendance')->whereIn('hr_emp_attendance_id', $dl_attendance)->delete();
            }*/

            /*foreach (array_chunk($prepare_arr,1000) as $t){
                DB::table('hr_emp_attendance')->insert($t);
            }*/

            foreach ($prepare_arr as $t){
                DB::table('hr_emp_attendance')
                    ->where('user_code', '=', $t['user_code'])
                    ->where('day_is', '=', $t['day_is'])
                    ->update($t);
            }

            DB::table('hr_temporary_emp_attendance')->where('hr_temporary_emp_attendance.created_by', '=', Auth::id())->delete();
            echo "succeed";
        }
    }


    public function checkPreviousEmployeeAttendance(Request $request){
        $post = $request->except('_token');
        $code = $post['code'];
        if("single" == $post['user_type']){
            $attendance_history = self::getMatchedEmployeeAttendanceDetails($code);
        }else{
            $attendance_history = self::getMatchedEmployeeAttendanceDetails();
        }

        if(!empty($attendance_history)){
            $data['attendance_history'] = $attendance_history;
            echo view('Hr_payroll.matched_attendance_lists', $data);
        }else{

            echo 'not-found';
        }
    }

    private function getHrEmployeeAttendanceRawData($post = NULL){
//        DB::enableQueryLog();
        $sql  = DB::table('hr_emp_attendance');
        $sql->select('hr_emp_attendance.*');
        $sql->addSelect('hr_emp_sections.hr_emp_section_name');
        $sql->addSelect(DB::raw('hr_emp_categorys.hr_emp_category_name'));
        $sql->addSelect(DB::raw('hr_working_shifts.shift_name'));
        $sql->Join('hr_emp_categorys', 'hr_emp_categorys.hr_emp_categorys_id', '=', 'hr_emp_attendance.hr_emp_categorys_id');
        $sql->Join('hr_working_shifts', 'hr_working_shifts.hr_working_shifts_id', '=', 'hr_emp_attendance.hr_working_shifts_id');
        $sql->Join('hr_emp_sections', 'hr_emp_attendance.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id');

        if (!empty($post['date_range'])) {
            $range = explode(" - ", $post['date_range']);
            $from = date('Y-m-d',strtotime($range[0]));
            $to = date('Y-m-d',strtotime($range[1]));
            $sql->whereBetween('hr_emp_attendance.day_is', [$from, $to]);
        }

        $sql->where('hr_emp_attendance.in_time', '!=', null)->where('hr_emp_attendance.in_time', '!=', '')->where('hr_emp_attendance.daily_status', '!=', null);

//        if (!empty($post['sys_users'])  && $post['sys_users'] != '') {
//            $sql->whereIn('hr_emp_attendance.user_code', $post['sys_users']);
//        }

        if (!empty($post['hr_emp_categorys'])) {
            $sql->whereIn('hr_emp_attendance.hr_emp_categorys_id', $post['hr_emp_categorys']);
        }
        $result = $sql->get()->toArray();
//        debug(DB::getQueryLog());
        return $result;
    }

    private function getPreparedEmployeeAttendanceRawData($code = NULL,$process_type = NULL){
        $sql  = DB::table('hr_temporary_emp_attendance');
        $sql->select('hr_temporary_emp_attendance.user_code','hr_temporary_emp_attendance.day_is','hr_temporary_emp_attendance.created_at','hr_temporary_emp_attendance.created_by','hr_temporary_emp_attendance.daily_status','hr_temporary_emp_attendance.in_time','hr_temporary_emp_attendance.out_time','hr_temporary_emp_attendance.break_time','hr_temporary_emp_attendance.total_work_time','hr_temporary_emp_attendance.ot_hours');
        $sql->addSelect(DB::raw('sys_users.id as sys_users_id'));
        $sql->addSelect('sys_users.hr_emp_categorys_id','sys_users.hr_working_shifts_id');
        $sql->addSelect(DB::raw('hr_emp_sections.hr_emp_section_name as section_name'));
        $sql->Join('sys_users', 'sys_users.user_code', '=', 'hr_temporary_emp_attendance.user_code');
        $sql->Join('hr_emp_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id');
        if($code){
            $sql->where('hr_emp_attendance.user_code',$code);
        }
        $result = $sql->get()->toArray();
        $result = array_map(function($item){
            return (array) $item;
        },$result);
        return $result;
    }

    private function getHrTemporaryEmployeeAttendanceRawData($code = NULL){
        $sql  = DB::table('hr_temporary_emp_attendance');
        $sql->select('hr_temporary_emp_attendance.*');
        if($code){
            $sql->where('hr_temporary_emp_attendance.user_code',$code);
        }
        $result = $sql->get()->toArray();
        $result=array_map(function($item){
            return (array) $item;
        },$result);
        return $result;
    }

    private function getMatchedEmployeeAttendanceDetails($code=NULL){
//      DB::enableQueryLog();
        $sql  = DB::table('hr_emp_attendance');
        $sql->select('hr_emp_attendance.*');
        $sql->join('hr_temporary_emp_attendance', 'hr_temporary_emp_attendance.day_is', '=', 'hr_emp_attendance.day_is');
        if($code){
            $sql->where('hr_temporary_emp_attendance.user_code',$code);
        }
        $sql->where(DB::raw('hr_emp_attendance.in_time'), '=', DB::raw('hr_temporary_emp_attendance.in_time'));
        $sql->where(DB::raw('hr_emp_attendance.out_time'), '=', DB::raw('hr_temporary_emp_attendance.out_time'));
        $result = $sql->get();
//      debug(DB::getQueryLog());
        return $result->toArray();
    }
    private function getHrTemporaryEmployeeAttendance($id = NULL,$post=NULL){
        DB::connection()->enableQueryLog();
        $sql  = DB::table('hr_temporary_emp_attendance');
        $sql->select('hr_temporary_emp_attendance.user_code','hr_temporary_emp_attendance.file_name','hr_temporary_emp_attendance.day_is','hr_temporary_emp_attendance.created_at','hr_temporary_emp_attendance.created_by','hr_temporary_emp_attendance.daily_status','hr_temporary_emp_attendance.in_time','hr_temporary_emp_attendance.out_time','hr_temporary_emp_attendance.break_time','hr_temporary_emp_attendance.total_work_time','hr_temporary_emp_attendance.ot_hours');
        $sql->addSelect('hr_emp_attendance.approved_status', 'hr_emp_attendance.hr_emp_attendance_id');
        $sql->addSelect(DB::raw('hr_emp_attendance.user_code as duplicate'));
        $sql->addSelect(DB::raw('sys_users.id as sys_users_id'), 'sys_users.name');
        $sql->addSelect('sys_users.hr_emp_categorys_id','sys_users.hr_working_shifts_id');
        $sql->addSelect(DB::raw('hr_emp_sections.hr_emp_section_name as section_name'), 'hr_emp_sections.hr_emp_sections_id');
        $sql->Join('sys_users', 'sys_users.user_code', '=', 'hr_temporary_emp_attendance.user_code');
        $sql->leftJoin('hr_emp_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id');
        $sql->leftJoin('hr_emp_attendance', function ($join) {
            $join->on('hr_emp_attendance.day_is', '=', 'hr_temporary_emp_attendance.day_is');/*
            $join->on('hr_emp_attendance.in_time', '=', 'hr_temporary_emp_attendance.in_time');
            $join->on('hr_emp_attendance.out_time', '=', 'hr_temporary_emp_attendance.out_time');*/
            $join->on('hr_emp_attendance.user_code', '=', 'hr_temporary_emp_attendance.user_code');
            $join->on('hr_emp_attendance.daily_status', '=', 'hr_temporary_emp_attendance.daily_status');
        });
        $sql->groupBy('hr_temporary_emp_attendance.user_code');
        $sql->orderBy('hr_temporary_emp_attendance.day_is', 'asc');
        $result = $sql->get()->toArray();
       // dd($result);
        //dd( DB::getQueryLog());
        return $result;

    }
    public function approvedAttendanceHistory(Request $request){
        $first_day_this_month = date('Y-m-01');
        $last_day_this_month  = date('Y-m-t');
        $date_range = isset($request->date_range)?$request->date_range: $first_day_this_month .' - '.$last_day_this_month;

        $post = $request->except('_token');
        $post['date_range'] = $date_range;
        $data['posted'] = $post;

        /*$data['posted']['date_range'] = $date_range;

        if(!empty($post)){
            $data['posted'] = $post;
        }*/

        $data['title'] = "Confirmed Attendance List";
        $data['date_range'] = $date_range;
        $data['hr_emp_categorys'] = $request->hr_emp_categorys?$request->hr_emp_categorys:'';
        $data['hr_users'] = $request->hr_users?$request->hr_users:'';
        $data['confirmed_attendance_history'] = self::getHrEmployeeAttendanceRawData($post);
        return view('HrAttendance.confirmed_attendance_lists', $data);
    }
    /*
     * Monthly Attendance Details
     */
    public function monthlyAttendanceDetails(Request $request){
        $post = $request->except('_token');
        $data['posted'] = [];
        if(!empty($post)){
            $data['posted'] = $post;
        }
        $data['title'] = "Attendance List";
        $data['date_range'] = $request->date_range?$request->date_range:'';
        $data['hr_emp_categorys'] = $request->hr_emp_categorys?$request->hr_emp_categorys:'';
        $data['hr_users'] = $request->hr_users?$request->hr_users:'';
        $data['confirmed_attendance_history'] = self::getHrEmployeeAttendanceRawData($post);
        return view('HrAttendance.confirmed_attendance_lists', $data);
    }


    public function getHrEmpAttendanceDetails(Request $request){
        $post = $request->except('_token');
        $info = self::getHrEmployeeAttendance($post['id']);
        $start_time = date('H:i',strtotime($info[0]->start_date_time));
        $start_date = date('Y-m-d',strtotime($info[0]->start_date_time));
        $end_time = date('H:i',strtotime($info[0]->end_date_time));
        $end_date = date('Y-m-d',strtotime($info[0]->end_date_time));
        $info[0]->start_time = $start_time;
        $info[0]->start_date = $start_date;
        $info[0]->end_time = $end_time;
        $info[0]->end_date = $end_date;
        return json_encode($info[0]);
    }

    private function getHrEmployeeAttendance($id = NULL){
        $sql  = DB::table('hr_emp_attendance');
        $sql->select('hr_emp_attendance.*');
        if($id){
            $sql->where('hr_emp_attendance.hr_emp_attendance_id',$id);
        }
        $result = $sql->get()->toArray();
        return $result;
    }


    public function updateAttendanceHistory(Request $request){
        $post = $request->except('_token');
        $id = $post['get_id'];
        $previous_details = self::getHrEmployeeAttendance($id);
        $start_hour_min_second = $post['start_date'].' '.$post['start_time'];
        $end_hour_min_second = $post['end_date'].' '.$post['end_time'];
        $total_working_time = self::getTotalWorkingTime($start_hour_min_second,$end_hour_min_second,$post['break_time'],$post['over_time']);
        $update_arr = array(
            'start_date_time' => $start_hour_min_second,
            'end_date_time' => $end_hour_min_second,
            'break_time' => $post['break_time'],
            'ot_hours' => $post['over_time'],
            'approved_status' => $post['approved_status'],
            'record_mode' => 'ManualEntry',
            'is_salary_enabled' => $post['is_salary_enabled'],
            'total_work_time' => $total_working_time,
            'is_edited' => 1+$previous_details[0]->is_edited
        );
        DB::table('hr_emp_attendance')->where('hr_emp_attendance.hr_emp_attendance_id', '=', $id)
            ->update($update_arr);
        echo 'updated';
    }

    public function lockedSelectedAttendanceHistory(Request $request){

        $post = $request->except('_token');

        $update_arr = array(
            'approved_status' => 'locked'
        );
        $sql = DB::table('hr_emp_attendance');

        if (!empty($post['date_range'])) {
            $range = explode(" - ", $post['date_range']);
            $from = date('Y-m-d',strtotime($range[0]));
            $to = date('Y-m-d',strtotime($range[1]));
            $sql->whereBetween('hr_emp_attendance.day_is', [$from, $to]);
        }
        if (!empty($post['sys_users'])  && $post['sys_users'] != '') {
            $sql->whereIn('hr_emp_attendance.user_code', $post['sys_users']);
        }
        if (!empty($post['hr_emp_categorys']) && count($post['hr_emp_categorys']) > 0) {
            $sql->whereIn('hr_emp_attendance.hr_emp_categorys_id', $post['hr_emp_categorys']);
        }
        if (!empty($request->hr_emp_attendance_ids)){
            $sql->whereIn('hr_emp_attendance.hr_emp_attendance_id', $request->hr_emp_attendance_ids);
        }
        $sql->update($update_arr);

        echo 'updated';
    }

    public function autoVoucherCall(){
        $start =1;
        for($start;$start <= 5000;$start++){
            $is_succeed = autoVoucherProcess('Sales','Sales Order','sales_order','1',NULL,'','Sales Order',NULL,NULL);
        }
        if($is_succeed){
            echo 'Action successfully done!';
        }else{
            echo "Autovoucher error found!!";
        }
    }

}
