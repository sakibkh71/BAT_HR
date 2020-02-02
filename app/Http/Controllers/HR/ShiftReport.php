<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;

use App\Helpers\PdfHelper;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class ShiftReport extends Controller {
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    //Daily Shift Report
    public  function dailyDutyReport(Request $request, $type=null){
        $date = isset($request->calendar_date)?$request->calendar_date:date('Y-m-d');
        $data['calendar_date'] = $date;
        $data['is_rotable']= $request->is_rotable;

        if($date){
            $sql = DB::table('hr_emp_attendance')
                ->select('hr_emp_attendance.*', 'hr_working_shifts.shift_name', 'sys_users.name')
                ->join('hr_working_shifts', 'hr_emp_attendance.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
                ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
                ->where('hr_emp_attendance.day_is', $date);

            if(isset($request->is_rotable) && $request->is_rotable ==1){
                $sql->where('hr_working_shifts.is_rotable',$request->is_rotable);
            }

            $sql->where('sys_users.is_employee', 1);

            $rows = $sql->get();

            $attendance_rows = [];
            if (!empty($rows)){
                foreach ($rows as $item) {
                    $data_array=[
                        'name'=> $item->name,
                        'user_code'=> $item->user_code,
                        'shift_name'=> $item->shift_name,
                        'daily_status'=> $item->daily_status,
                        'shift_start_time'=> $item->shift_start_time,
                        'shift_end_time'=> $item->shift_end_time,
                        'in_time'=> $item->in_time,
                        'out_time'=> $item->out_time,
                    ];
                    $attendance_rows[$item->shift_name][]= $data_array;
                }
                $data['attendance_rows'] = $attendance_rows;
            }
        }

        if ($type =='pdf'){
            $data['report_title'] = 'Daily Duty Report - '. toDated($request->calendar_date);
            $data['filename'] = 'daily_duty_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR..shift_manager.daily_duty_pdf';
            PdfHelper::exportPdf($view,$data);
        }else{
            return view('HR.shift_manager.daily_duty_report', $data);
        }

    }

    //Employee wise Shift Report
    public function employeeWiseDuty(Request $request, $type=null){
        $data['calendar_month']= isset($request->calendar_month)?$request->calendar_month:date('Y-m');
        $data['user_id']= $request->user_id;

        if(isset($request->user_id)){
            DB::connection()->enableQueryLog();
            $sql = DB::table('hr_emp_attendance')
                ->select('hr_emp_attendance.*', 'hr_working_shifts.shift_name', 'sys_users.name')
                ->join('hr_working_shifts', 'hr_emp_attendance.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
                ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
                ->where('hr_emp_attendance.day_is',  'like', $request->calendar_month .'%')
                ->where('sys_users.id', $request->user_id)
                ->where('sys_users.is_employee', 1);
            $data['attendance_rows'] = $sql->get();
            $data['emp_info'] = employeeInfo($request->user_id);
        }
        if ($type =='pdf'){
            $data['emp_info'] = employeeInfo($request->user_id, $pdf = 1);
            $data['report_title'] = 'Employee Wise Duty Report - '. date("M, Y", strtotime($data['calendar_month']));
            $data['filename'] = 'employee_wise_duty_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR..shift_manager.employee_wise_duty_pdf';
            PdfHelper::exportPdf($view,$data);
        }else{
            return view('HR.shift_manager.employee_wise_report', $data);
        }
    }

    //Shift wise Shift Report
    public function shiftWiseDuty(Request $request, $type=null){
        $data['calendar_date']= isset($request->calendar_date)?$request->calendar_date:date('Y-m-d');
        $data['shifts_id']= $request->shifts_id;

        if(isset($request->shifts_id)){
            DB::connection()->enableQueryLog();
            $sql = DB::table('hr_emp_attendance')
                ->select('hr_emp_attendance.*', 'hr_working_shifts.shift_name', 'sys_users.name')
                ->join('hr_working_shifts', 'hr_emp_attendance.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
                ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
                ->where('hr_emp_attendance.day_is', $request->calendar_date)
                ->where('hr_working_shifts.hr_working_shifts_id', $request->shifts_id)
                ->where('sys_users.is_employee', 1);
            $rows = $sql->get();

            $attendance_rows = [];
            if (!empty($rows)){
                foreach ($rows as $item) {
                    $data_array=[
                        'name'=> $item->name,
                        'user_code'=> $item->user_code,
                        'shift_name'=> $item->shift_name,
                        'daily_status'=> $item->daily_status,
                        'shift_start_time'=> $item->shift_start_time,
                        'shift_end_time'=> $item->shift_end_time,
                        'in_time'=> $item->in_time,
                        'out_time'=> $item->out_time,
                    ];
                    $attendance_rows[$item->shift_name][]= $data_array;
                }
                $data['attendance_rows'] = $attendance_rows;
            }
        }

        if ($type =='pdf'){
            $data['report_title'] = 'Shift Wise Report - '. date("M, Y", strtotime($data['calendar_month']));
            $data['filename'] = 'shift_wise_duty_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR..shift_manager.shift_wise_duty_pdf';
            PdfHelper::exportPdf($view,$data);
        }else{
            return view('HR.shift_manager.shift_wise_report', $data);
        }
    }

}
