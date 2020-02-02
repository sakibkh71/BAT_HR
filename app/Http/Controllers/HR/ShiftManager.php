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
use App\Models\Hr_working_shift\Hr_working_shift_vs_emp AS ShiftEmpModel;
use App\Models\Hr_working_shift\Hr_working_shift AS ShiftModel;

class ShiftManager extends Controller {
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function calendarShiftChange($calendar_month='',$emp_id = ''){
        $calendar_month = ($calendar_month=='')?date('Y-m'):$calendar_month;
        $this->data['calendar_month'] = $calendar_month;
        $this->data['calendar_day'] = $calendar_month.'-01';
        $this->data['sys_users_id'] = $emp_id;
        $all_shift = DB::table('hr_working_shifts')
            ->where('is_rotable','=',1)
            ->where('status','=','Active')
            ->get();
        $this->data['shiftList'] = $all_shift;


        return view('HR.shift_manager.shift_change_calendar', $this->data);
    }

    function calendarEmployeeShiftData($sys_users_id){
        $sql = DB::table('hr_emp_attendance')
            ->leftJoin('hr_working_shifts','hr_working_shifts.hr_working_shifts_id','=','hr_emp_attendance.hr_working_shifts_id')
            ->where('sys_users_id',$sys_users_id);
        $eventData = $sql->get();
        $allData = [];
        $row = [];
        if(!empty($eventData)){
            foreach($eventData as $i=>$data){
                if($data->shift_day_status == 'W'){
                    $row['title'] = 'Weekend';
                    $row['className'] = 'bg-warning';
                }elseif($data->shift_day_status == 'H'){
                    $row['title'] = 'Holiday';
                    $row['className'] = 'bg-danger';
                }else{
                    $row['title'] = "$data->shift_name\n ($data->start_time-$data->end_time)";
                    $row['textColor'] = '#FFF';
                    $row['color'] = $data->bg_color;
                    $row['className'] = '';
                }
                $row['resourceID'] = strtotime($data->day_is);
                $row['start'] = $data->day_is;
                $row['end'] = $data->day_is;

                $allData[] = $row;
            }
        }

        return $allData;
    }

    public function shiftChange(Request $request){
        $date = $request->calendar_day==''?date('Y-m-d'):$request->calendar_day;
        $shift_id = $request->working_shift==''?'':$request->working_shift;
        $posted = $request->all();
        $data['posted'] = [];
        $employee_list = [];
        if(!empty($posted)){
            $data['posted'] = $posted;
            $employee_list = self::getEmployeeInfo($posted);
        }
        $sql = DB::table('hr_emp_attendance')
            ->selectRaw('count(*) as total')
            ->addSelect('shift_name','bg_color','day_is','shift_day_status','hr_working_shifts.hr_working_shifts_id')
            ->join('hr_working_shifts','hr_working_shifts.hr_working_shifts_id','=','hr_emp_attendance.hr_working_shifts_id')
            ->whereYear('day_is','=',date('Y'))
            ->groupBy('hr_working_shifts.hr_working_shifts_id')
            ->groupBy('hr_emp_attendance.day_is');
        $daily_employees =$sql->get();

        $allData = [];
        $row = [];
        if(!empty($daily_employees)){
            foreach($daily_employees as $i=>$event){
                $row['title'] = "$event->shift_name (Total = $event->total worker)";
                $row['color'] = $event->bg_color;
                $row['textColor'] = '#FFF';
                $row['resourceID'] = strtotime($event->day_is);
                $row['start'] = $event->day_is;
                $row['end'] = $event->day_is;
                $allData[] = $row;
            }
        }

        $emp_ids = [];
        foreach($employee_list as $emp){
            array_push($emp_ids,$emp->id);
        }
        $shift = DB::table('hr_working_shifts')->where('is_rotable','=',1);
        $data['shiftList'] = $shift->get();
        $data['employeeList'] = $employee_list;
        $data['eventData'] = $allData;
        $data['calendar_day'] = $date;
        $data['config_month'] = $request->config_month;
        $data['shifted'] = $shift_id;
        $data['emp_ids'] = implode(',',$emp_ids);
        return view('HR.shift_manager.shift_change_form', $data);
    }

    public function setCalendarShift(Request $request)
    {
        $shift_id =$shift_start_time=$shift_end_time= '';
        if($request->event_title == 'Weekend'){
            $day_is = 'W';
        }elseif($request->event_title == 'Holiday'){
            $day_is = 'H';
        }else{
            $day_is = 'R';
            $shift_info = DB::table('hr_working_shifts')->where('shift_name','=',$request->event_title)->get()->first();
            $shift_id = $shift_info->hr_working_shifts_id;
            $shift_start_time = $shift_info->start_time;
            $shift_end_time = $shift_info->end_time;
        }
        $employees = DB::table('sys_users')
            ->select('id','user_code','hr_emp_categorys_id','hr_emp_sections_id','hr_working_shifts_id','start_time','end_time')
            ->whereIn('id',$request->emp_ids)
            ->get();
        $employee_data = [];
        if(!empty($employees)){
            foreach($employees as $emp){
                $employee['sys_users_id'] = $emp->id;
                $employee['user_code'] = $emp->user_code;
                $employee['hr_emp_categorys_id'] = $emp->hr_emp_categorys_id;
                $employee['hr_working_shifts_id'] = $shift_id?$shift_id:$emp->hr_working_shifts_id;
                $employee['hr_emp_sections_id'] = $emp->hr_emp_sections_id;
                $employee['shift_day_status'] = $day_is;
                $employee['day_is'] = $request->start_date;
                $employee['shift_start_time'] = $shift_start_time?$shift_start_time:$emp->start_time;
                $employee['shift_end_time'] = $shift_end_time?$shift_end_time:$emp->end_time;
                $employee_data[] = $employee;
                DB::table('hr_emp_attendance')
                    ->where('sys_users_id','=',$emp->id)
                    ->where('day_is','=',$request->start_date)
                    ->delete();
            }
        }


        DB::table('hr_emp_attendance')->insert($employee_data);
//        debug($employee_data,1);
     //   DB::select("CALL proc_company_calendar_config_worker('$request->start_date','$day_is','$shift_id','$request->emp_ids')");
        return response()->json([
            'success'=>true,
        ]);
    }

    public function saveShiftInfo(Request $request){
        DB::enableQueryLog();
        $post = $request->all();
        $insert_arr = array(
            'hr_working_shifts_id' => $post['working_shift'],
            'previous_shift_id' => $post['previous_shift'],
            'sys_users' => $post['users'],
            'start_date' => $post['shift_date'],
            'created_by' => Auth::user()->id
        );

        $exist = ShiftEmpModel::where('hr_working_shifts_id', $post['working_shift'])
            ->where('previous_shift_id', $post['previous_shift'])
            ->where('shift_status', '55')
            ->whereNotIn('shift_status', [56])
            ->first();
        if($exist == null){
            $insert_id = DB::table('hr_emp_vs_shift_log')->insertGetId($insert_arr);
            $delegation_data = array(
                'slug' => 'hr_shift',
                'code' => [$insert_id],
                'delegation_type' => 'send_for_approval'
            );
            goToDelegationProcess($delegation_data);
            $this->data['ot'] = 'inserted';
        }else{
            $update = DB::table('hr_emp_vs_shift_log')
                ->where('hr_working_shifts_id', $post['working_shift'])
                ->where('shift_status', 55)
                ->update($insert_arr);
//            AuditTrailEvent::updateForAudit($update,$insert_arr);
            $this->data['ot'] = 'updated';
        }
//        debug(DB::getQueryLog());
        $this->data['mode'] = 'success';
        echo json_encode($this->data);
    }

    private function getEmployeeInfo($posted){
//        debug($posted,1);
        $is_rotable = DB::table('hr_working_shifts')->where('hr_working_shifts_id','=',$posted['working_shift'])->get()->first()->is_rotable;

        $sql = DB::table('sys_users');
        $sql->select(
            'sys_users.id',
            'sys_users.user_code',
            'sys_users.name'
        );
        if($is_rotable){
            $sql->Join('hr_emp_attendance', function ($join) {
                $join->on('sys_users.id', '=', 'hr_emp_attendance.sys_users_id');
            });
        }

        $sql->leftJoin('departments', function ($join) {
            $join->on('departments.departments_id', '=', 'sys_users.departments_id');
        });
        $sql->leftJoin('designations', function ($join) {
            $join->on('designations.designations_id', '=', 'sys_users.designations_id');
        });
        $sql->leftJoin('hr_emp_units', function ($join) {
            $join->on('hr_emp_units.hr_emp_units_id', '=', 'sys_users.hr_emp_units_id');
        });
        $sql->leftJoin('hr_working_shifts', function ($join) {
            $join->on('hr_working_shifts.hr_working_shifts_id', '=', 'sys_users.hr_working_shifts_id');
        });
        $sql->leftJoin('hr_emp_sections', function ($join) {
            $join->on('hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id');
        });
        $sql->leftJoin('branchs', function ($join) {
            $join->on('branchs.branchs_id', '=', 'sys_users.branchs_id');
        });
        $sql->where('sys_users.is_employee', '=', 1);

        if(!empty($posted['branchs_id']) && count($posted['branchs_id']) >=0){
            $sql->whereIn('sys_users.branchs_id', $posted['branchs_id']);
        }
        if(!empty($posted['hr_emp_categorys_id']) && count($posted['hr_emp_categorys_id']) >=0){
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posted['hr_emp_categorys_id']);
        }

        if(!empty($posted['hr_emp_units_id']) && count($posted['hr_emp_units_id']) >=0){
            $sql->whereIn('sys_users.hr_emp_units_id', $posted['hr_emp_units_id']);
        }

        if(!empty($posted['departments_id']) && count($posted['departments_id'])>=0){
            $sql->whereIn('sys_users.departments_id', $posted['departments_id']);
        }
        if(!empty($posted['hr_emp_sections_id']) && count($posted['hr_emp_sections_id'])>=0){
            $sql->whereIn('sys_users.hr_emp_sections_id', $posted['hr_emp_sections_id']);
        }

        if(!empty($posted['designations_id']) && count($posted['designations_id'])>=0){
            $sql->whereIn('sys_users.designations_id', $posted['designations_id']);
        }

        if($is_rotable){
            if(!empty($posted['working_shift'])){
                $sql->where('hr_emp_attendance.hr_working_shifts_id', $posted['working_shift']);
            }
            if(!empty($posted['calendar_day'])){
                $sql->where('hr_emp_attendance.day_is', $posted['calendar_day']);
            }
        }else{
            if(!empty($posted['working_shift'])){
                $sql->where('sys_users.hr_working_shifts_id', $posted['working_shift']);
            }
        }

        $sql->groupBy('sys_users.id');
        $this->data['user_info'] = $sql->get();
        return $this->data['user_info'];
    }

    public function shiftApproval(){
        $sql = ShiftEmpModel::select([
            "*",
            DB::raw("(CHAR_LENGTH(sys_users) - CHAR_LENGTH(REPLACE(sys_users, ',', '')) + 1) as total_emps"
            )]);
        $sql->where('hr_emp_vs_shift_log.shift_status', 55);
        $sql->where('hr_emp_vs_shift_log.status', 'Active');
        $this->data['shift_infos'] = $sql->get();

        $shifts = ShiftModel::all('hr_working_shifts_id','shift_name');
        foreach ($shifts as $shift){
            $this->data['shifts'][$shift->hr_working_shifts_id] = $shift->shift_name;
        }
        return view('HR.shift_manager.shift_change_approve', $this->data);
    }

    public function shiftApprovalSubmit(Request $request){
        $post = $request->all();
        $delegation_data = array(
            'slug' => 'hr_shift',
            'code' => $post['id'],
            'delegation_type' => 'approval',
            'additional_data' => '',
            'comments' => 'bulk approve'
        );
//        debug($delegation_data);
        $approval = json_decode(goToDelegationProcess($delegation_data));
        $log_id = [];
        foreach ($approval as $apr_data){
            foreach ($apr_data->data as $log_key => $item_data){
                if($item_data->mode == 'success'){
                    $log_id[] = $log_key;
                }
            }
            $status = $apr_data->status_id;

        }
        $apply = self::shiftChangeApply($log_id, $status);
        if($apply){
            echo json_encode(['mode'=>'success']);
        }
    }

    function shiftChangeApply($log_id = [], $status){
        $logs = ShiftEmpModel::whereIN('hr_emp_vs_shift_log_id', $log_id)->get();
        foreach ($logs as $log){
            $users = explode(',', $log->sys_users);
            $update = DB::table('sys_users')
                ->whereIN('id', $users)
                ->update(['hr_working_shifts_id' => $log->hr_working_shifts_id]);
//            AuditTrailEvent::updateForAudit($update,['hr_working_shifts_id' => $log->hr_working_shifts_id]);
            $update2 = DB::table('hr_emp_vs_shift_log')
                ->where('hr_emp_vs_shift_log_id', $log->hr_emp_vs_shift_log_id)
                ->update(['shift_status' => $status]);
//            AuditTrailEvent::updateForAudit($update,['shift_status' => $status]);
        }
        return true;
    }
}
