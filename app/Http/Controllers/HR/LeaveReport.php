<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Redirect;
use Auth;
use Response;
use App\Helpers\PdfHelper;

class LeaveReport extends Controller
{
    public $data = [];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function leaveReportList()
    {
        $data = [];
        return view('HR.leave_report.reports', $data);
    }

    public function employeeLeaveReport(){
        $data=[];
        return view('HR.leave_report.employee_leave_report',$data);
    }

    public function allEmployeeLeaveReport( Request $request,$type=''){
             $current_year=date('Y');



                $sql=DB::table('sys_users')->select('sys_users.id AS user_id','sys_users.user_code','sys_users.name AS user_name',
                    'designations.designations_name','designations.designations_id','bat_distributorspoint.name AS point',
                    'sys_users.bat_dpid',
                    DB::raw('SUM( DISTINCT hr_yearly_leave_balances.policy_days ) AS entitled_leave'),
                    DB::raw('SUM( DISTINCT hr_yearly_leave_balances.balance_leaves ) AS balance_leaves'),
                    DB::raw('SUM( hr_yearly_leave_balances.enjoyed_leaves ) AS leave_taken '))
                    ->leftJoin('hr_yearly_leave_balances',function($join){
                        $current_year=date('Y');
                        $join->on('sys_users.id','=','hr_yearly_leave_balances.sys_users_id');
                        $join->on('hr_yearly_leave_balances.hr_yearly_leave_balances_year','=',DB::raw("'$current_year'"));
                    })
                    ->leftJoin("designations",'sys_users.designations_id','=','designations.designations_id')
                    ->join("bat_distributorspoint","sys_users.bat_dpid","=","bat_distributorspoint.id")
                    ->where("sys_users.status",'Active')->where("is_employee",1)
                    ->groupBy('sys_users.id')->orderBy('balance_leaves', 'ASC');

                if(isset($request->bat_dpid)){
                  $data['bat_dpid']=$request->bat_dpid;

                    $sql->whereIn("sys_users.bat_dpid",$request->bat_dpid);

                }
                if(isset($request->bat_designation)){
                 $data['bat_designation']=$request->bat_designation;

                    $sql->whereIn("designations.designations_id",$request->bat_designation);
                }
//                if(isset($request->bat_users)){
//
//               $data['bat_users']=$request->bat_users;
//
//                    $sql->where('sys_users.user_code',$request->bat_users);
//                }

                $session_con = (sessionFilter('url','get-emp-leave-history'));
                $session_con = trim(trim(strtolower($session_con)),'and');
                if($session_con){
                    $sql->whereRaw($session_con);
                }

                $data['user_info'] =$sql->get();

            if($type=='pdf'){
                $data['report_title'] = ' Leave Report - ' .$current_year;
                $data['filename'] = ' Leave Report - ' .$current_year;
                $data['orientation'] = "L";
                $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
//                $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
                $view = 'HR.leave_report.all_employee_leave_report_print';
             //  $data['row'] =$data['user_info'];
              //  dd($data);
              PdfHelper::exportPdf($view, $data);
            }
        return view('HR.leave_report.all_employee_leave_report',$data);
    }


    function yearlyLeaveRecord($user_id, $year, $pdf = false)
    {
        $data = [];
        if ($user_id && $year) {
            $leaveManager = new LeaveManager();
            $empLeavePolicy = $leaveManager->getLeavePolicy($year, $user_id);
            $sql = DB::table('hr_leave_records');
            $sql->select(DB::raw('MONTH(hr_leave_records.start_date) month'), 'leave_types');
            $sql->addselect(DB::raw('SUM(leave_days) total_leave'));
            $sql->where('hr_leave_records.sys_users_id', $user_id);
            $sql->whereYear('hr_leave_records.start_date', '=', $year);
            $sql->where('hr_leave_records.status', '=', 'Active');
            $sql->groupBy('leave_types');
            $sql->groupBy(DB::raw('MONTH(hr_leave_records.start_date)'));
            $leave_records = $sql->get();
            $records = [];
            $all_records = [];
            foreach ($leave_records as $record) {
                $records[$record->leave_types] = $record->total_leave;
                $all_records[$record->month] = $records;
            }
        }
        $data['emp_info'] = employeeInfo($user_id, $pdf);
        $data['emp_code'] = $data['emp_info']->user_code;
        $data['report_year'] = $year;
        $data['sys_users_id'] = $user_id;
        $data['leave_policys'] = $empLeavePolicy;
        $data['leave_records'] = $all_records;
        return $data;

    }

    function yearlyLeaveReport(Request $request)
    {
        $user_id = $request->sys_users_id;
        $year = $request->year;
        $data = self::yearlyLeaveRecord($user_id, $year);
        return view('HR.leave_report.emp_yearly_leave_report', $data);
    }

    function yearlyLeaveReportPrint($user_id, $year)
    {
        $data = self::yearlyLeaveRecord($user_id, $year, $pdf = 1);
        $data['report_title'] = 'Employee Leave Report - ' . $year;
        $data['filename'] = 'leave_report';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view = 'HR.leave_report.emp_yearly_leave_report_print';
        PdfHelper::exportPdf($view, $data);
    }


    function earlyEarnLeaveReport(Request $request)
    {
        $user_id = $request->sys_users_id;

        $data['yearly_logs'] = self::yearlyEarnLeaveRecord($user_id);
        $data['emp_info'] = employeeInfo($user_id);
        $data['sys_users_id'] = $user_id;
        $data['compensation_days'] = self::compensation_days($user_id);
        return view('HR.leave_report.emp_yearly_earn_leave_report', $data);
    }

    private function compensation_days($user_id)
    {
        $compention_days = 0;
        $sql = DB::table('sys_users')
            ->where('id', $user_id)
            ->get()->first();
        $joining_date = strtotime($sql->date_of_join);
        $today = strtotime(date("Y-m-d"));
        $days = ($today - $joining_date) / (60 * 60 * 24);
        $compention_year = $days / 365;
        if ($compention_year >= 5) {
            $compention_days = (70 + ($compention_year - 5) * 14);
        }
        return number_format($compention_days, 2);
    }

    function yearlyEarnLeaveRecord($user_id)
    {
        $yearly_logs = [];
        if ($user_id) {

            $present_days = DB::table('hr_monthly_salary_wages')
                ->select('hr_salary_month_name',
                    'present_days')
                ->where('hr_monthly_salary_wages.sys_users_id', $user_id)
                ->orderBy('hr_salary_month_name', 'ASC')
                ->get();
            $employee_monthly_log = $present_days;

            $employee_yearly_log = [];
            if (!empty($employee_monthly_log)) {
                foreach ($employee_monthly_log as $month) {
                    $year = substr($month->hr_salary_month_name, 0, 4);
                    $m_name = substr($month->hr_salary_month_name, 6, 2);
                    $employee_log['month'] = $month->hr_salary_month_name;
                    $employee_log['present'] = $month->present_days;
                    $employee_yearly_log[$year][$m_name] = $employee_log;
                }
                $yearly_logs = $employee_yearly_log;
            }
        }

        return $yearly_logs;

    }

    function monthlyLeaveReport(Request $request)
    {
        $user_id = $request->sys_users_id;
        $month = $request->month;
        $leaveManager = new LeaveManager();
        $data['leave_policys'] = $leaveManager->getLeavePolicy($month, $user_id);
        $data['emp_info'] = employeeInfo($user_id);
        $data['report_month'] = $month;
        $data['year'] = date('M-Y',strtotime($month));
        $data['sys_users_id'] = $user_id;
        return view('HR.leave_report.emp_monthly_leave_report', $data);
    }


    function monthlyLeaveReportPrint($user_id, $month)
    {
        $leaveManager = new LeaveManager();
        $data['leave_policys'] = $leaveManager->getLeavePolicy($month, $user_id);
        $data['emp_info'] = employeeInfo($user_id, $pdf = 1);

        $data['signatures'] = array('Prepared by', 'Employee Sign', 'Manager-Hr');
        $data['report_title'] = 'Employee Leave Report : ' . $month;
        $data['filename'] = 'leave_report';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view = 'HR.leave_report.emp_monthly_leave_report_print';
        PdfHelper::exportPdf($view, $data);
    }

    public function getLeaveReport(Request $request){

        $users = DB::table('sys_users')->join('hr_leave_records','hr_leave_records.sys_users_id','=','sys_users.id')->where('hr_leave_records.hr_leave_records_id',$request->leave_record_id)->first();

        $user_info_array=DB::table('sys_users')->where('user_code',$users->user_code)->first();
        $user_id=$user_info_array->id;
        $leaveManager = new LeaveManager();
        $year=date('Y');

        $data['leave_policys'] = $leaveManager->getLeavePolicy($year,$user_id);
       // dd($data['leave_policys']);

        $data['emp_info']=DB::table('sys_users')
        ->select(
            'sys_users.*', 'departments.departments_name', 'designations.designations_name', 'hr_emp_units.hr_emp_unit_name', 'hr_emp_categorys.hr_emp_category_name', 'bat_company.company_name as distributor_house', 'bat_distributorspoint.name as distributor_point', 'hr_emp_sections.hr_emp_section_name')
        ->leftJoin('departments', 'departments.departments_id', '=', 'sys_users.departments_id')
        ->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id')
        ->leftJoin('hr_emp_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id')
        ->leftJoin('hr_emp_units', 'hr_emp_units.hr_emp_units_id', '=', 'sys_users.hr_emp_units_id')
        ->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'sys_users.bat_company_id')
        ->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'sys_users.bat_dpid')
        ->leftJoin('hr_emp_categorys', 'hr_emp_categorys.hr_emp_categorys_id', '=', 'sys_users.hr_emp_categorys_id')
        ->where('sys_users.id', $user_id)
        ->first();

        return view('HR.leave_manager.leave_report_modal',$data);

    }

    function leaveReportPrint($user_id){
        $leaveManager = new LeaveManager();
        $year=date('Y');
        $data['leave_policys'] = $leaveManager->getLeavePolicy($year,$user_id);
        $data['emp_info'] = employeeInfo($user_id, $pdf = 1);

        $data['signatures'] = array('Prepared by', 'Employee Sign', 'Manager-Hr');
        $data['report_title'] = 'Employee Leave Report ';
        $data['filename'] = 'leave_report';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
       // dd($data);
        $view = 'HR.leave_report.emp_monthly_leave_report_print';

        //return view($view,$data);
        PdfHelper::exportPdf($view, $data);
    }

    function earnLeaveReportPrint($user_id)
    {
        $data['yearly_logs'] = self::yearlyEarnLeaveRecord($user_id);
        $data['emp_info'] = employeeInfo($user_id, $pdf = 1);
        $data['sys_users_id'] = $user_id;
        $data['compensation_days'] = self::compensation_days($user_id);

        $data['report_title'] = 'Employee Earn Leave Report';
        $data['filename'] = 'leave_report';
        $data['paper_size'] = "A4";
        $data['orientation'] = "L";
        $data['download'] = false;
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view = 'HR.leave_report.emp_earn_leave_report_print';
        PdfHelper::exportPdf($view, $data);
    }

    function leaveEncashmaneBalance($user_id)
    {
        $present_days = DB::table('hr_monthly_salary_wages')
            ->select(DB::raw('sum(present_days) as present_days'))
            ->where('hr_monthly_salary_wages.sys_users_id', $user_id)
            ->get()->first();

        $enjoyLeave = array_sum(yearEarnLeaveEnjoy($user_id));
        $encashLeave = array_sum(year_earn_leave_encash($user_id));
        $encashment_days = ($present_days->present_days/18)-($enjoyLeave+$encashLeave);
        return number_format($encashment_days,2);

    }
}
