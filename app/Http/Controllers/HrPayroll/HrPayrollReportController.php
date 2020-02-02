<?php

namespace App\Http\Controllers\HrPayroll;

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
use App\Http\Controllers\HR\LeaveManager;
//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;
//pdf library
use App\Helpers\PdfHelper;

class HrPayrollReportController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
     * employee-salary-requisition
     */

    public function empSalaryRequisition(Request $request, $type = '') {
        $post = $request->all();
        $status = $request->status ? $request->status : 'Active';
        $salary_month = isset($post['salary_month']) ? $post['salary_month'] : date('Y-m');

        $monthQ = DB::table('hr_emp_salary_sheet');
        $monthQ->where('salary_month',$salary_month);
        $session_con = (sessionFilter('url', 'emp-salary-month-check'));
        $session_con = trim(trim(strtolower($session_con)), 'and');
        if($session_con){
            $monthQ->whereRaw($session_con);
        }
        $currentMonthSalary = $monthQ->get();
        $sheetInfo = [];
        if(($salary_month == date('Y-m')) && ($currentMonthSalary->isEmpty())== true){
            $month = date('Y-m');
            $sheetHead = DB::table('hr_emp_salary_components')
                ->where('record_type', 'default')
                ->where('auto_applicable', 'YES')
                ->groupBy('component_slug');
            $data['salary_component'] = $sheetHead->get();

            $q = DB::table('sys_users')
                ->selectRaw("
                    `hr_emp_attendance`.`sys_users_id`,
                    sys_users.name,
                    sys_users.user_code,
                    sys_users.pf_applicable,
                    sys_users.gf_applicable,
                    sys_users.date_of_join,
                    sys_users.insurance_applicable,
                    sys_users.status,
                    sys_users.separation_date,
                    sys_users.late_deduction_applied,
                    sys_users.bat_company_id,
                    sys_users.bat_dpid,
                    sys_users.designations_id,
                    designations.designations_name,
                    sys_users.departments_id,
                    sys_users.branchs_id,
                    sys_users.hr_emp_grades_id,
                    sys_users.hr_emp_units_id,
                    sys_users.hr_emp_categorys_id,
                    sys_users.hr_emp_sections_id,
                    sys_users.salary_account_no,
                    bat_distributorspoint.name point_name,
                    sys_users.basic_salary,
                    sys_users.min_gross as gross,
                    ifnull(sys_users.pf_amount_employee,0) as pf_amount_employee,
                    ifnull(sys_users.pf_amount_company,0) as pf_amount_company,
                    ifnull(sys_users.gf_amount,0) as gf_amount,
                    ifnull(sys_users.insurance_amount,0) as insurance_amount,
                    ifnull(func_get_variable_salary(sys_users.user_code,'$month'),0) as pfp_achievement,
                    IFNULL(COALESCE((select sum(variable_salary_amount) from hr_emp_monthly_variable_salary where sys_users_id=sys_users.id and vsalary_month='$month' AND hr_emp_monthly_variable_salary.status='Active'),max_variable_salary),0) as target_variable_salary,
                    IFNULL((select sum(monthly_payment) from hr_emp_loan where sys_users_id=sys_users.id and hr_emp_loan.status='Active'),0) as due_loan_amount,
                    IFNULL((select sum(conveyance_amount) from hr_other_conveyances where sys_users_id=sys_users.id),0) as other_conveyance,
                    substr(hr_emp_attendance.day_is,1,7) AS `hr_salary_month_name`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'R') then 1 else 0 end)) AS `number_of_working_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in ('P','HP','WP','L','EO')) then 1 else 0 end)) AS `present_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'A') then 1 else 0 end)) AS `absent_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'Lv') then 1 else 0 end)) AS `number_of_leave`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'W') then 1 else 0 end)) AS `number_of_weekend`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'H') then 1 else 0 end)) AS `number_of_holidays`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in('L','EO')) then 1 else 0 end)) AS `late_days`");
            if (!empty($data['salary_component'])) {
                foreach ($data['salary_component'] as $component) {
                    $q->selectRaw("func_get_salary_component(sys_users.id,'$month','$component->component_slug') as $component->component_slug");
                }
            }
            $q->LeftJoin('hr_emp_attendance', 'sys_users.id', 'hr_emp_attendance.sys_users_id');
            $q->LeftJoin('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id');
            $q->LeftJoin('designations','sys_users.designations_id','designations.designations_id');
            $q->whereRaw("sys_users.id NOT IN(SELECT sys_users_id FROM hr_monthly_salary_wages WHERE hr_salary_month_name='$month')
                    AND substr(hr_emp_attendance.day_is,1,7)='$month'
                    AND sys_users.is_employee=1
                    AND sys_users.status IN('Active','Separated')");

            if (!empty($post['bat_company_id'])) {
                $q->whereIn('sys_users.bat_company_id', $post['bat_company_id']);
            }
            if (!empty($post['bat_dpid'])) {
                $q->whereIn('sys_users.bat_dpid', $post['bat_dpid']);
            }
            if (!empty($post['hr_emp_grades_list'])) {
                $q->whereIn('sys_users.hr_emp_grades_id', $post['hr_emp_grades_list']);
            }
            if (!empty($post['hr_emp_salary_designations'])) {
                $q->whereIn('sys_users.designations_id', $post['hr_emp_salary_designations']);
            }

            $session_con = (sessionFilter('url', 'emp-salary-statement'));
            $session_con = trim(trim(strtolower($session_con)), 'and');
            if($session_con){
                $q->whereRaw($session_con);
            }

            $q->groupBy('sys_users_id');
            $data['employeeList'] = $q->get();

        }else{
            $sheetHead = DB::table('hr_emp_salary_components')
                ->where('record_type', 'default')
                ->where('auto_applicable', 'YES')
                ->groupBy('component_slug');
            $data['salary_component'] = $sheetHead->get();

            $employeeInfo = DB::table('hr_monthly_salary_wages');
            $employeeInfo->select('sys_users.id', 'sys_users.name', 'sys_users.user_code', 'hr_emp_grades.hr_emp_grade_name', 'designations.designations_name', 'bat_company.company_name', 'bat_distributorspoint.name as point_name', 'hr_emp_pfp_salary.pfp_target_amount', 'hr_emp_pfp_salary.pfp_achieve_ratio', 'hr_emp_pfp_salary.pfp_earn_amount', 'hr_monthly_salary_wages.*');
            if (!empty($data['salary_component'])) {
                foreach ($data['salary_component'] as $component) {
                    $employeeInfo->selectRaw("func_get_salary_component(sys_users.id,hr_salary_month_name,'$component->component_slug') as $component->component_slug");
                }
            }
            $employeeInfo->join('sys_users', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id');
            $employeeInfo->leftJoin('hr_emp_pfp_salary', function ($join) {
                $join->on('hr_emp_pfp_salary.salary_month', '=', 'hr_monthly_salary_wages.hr_salary_month_name');
                $join->on('hr_emp_pfp_salary.sys_users_id', '=', 'hr_monthly_salary_wages.sys_users_id');
            });
            $employeeInfo->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_monthly_salary_wages.hr_emp_grades_id');
            $employeeInfo->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'hr_monthly_salary_wages.bat_company_id');
            $employeeInfo->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'hr_monthly_salary_wages.bat_dpid');
            $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'hr_monthly_salary_wages.designations_id');

            $session_con = (sessionFilter('url', 'employee-salary-requisition'));
            $session_con = trim(trim(strtolower($session_con)), 'and');

            if ($session_con) {
                $employeeInfo->whereRaw($session_con);
            }

            $employeeInfo->where('sys_users.status', $status);

            //        $employeeInfo->where('hr_monthly_salary_wages.hr_emp_salary_sheet_id', $sheet_id);
            if (!empty($salary_month)) {
                $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_name', $salary_month);
            }
            if (!empty($post['bat_company_id'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.bat_company_id', $post['bat_company_id']);
            }
            if (!empty($post['bat_dpid'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.bat_dpid', $post['bat_dpid']);
            }
            if (!empty($post['hr_emp_grades_list'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_grades_id', $post['hr_emp_grades_list']);
            }
            if (!empty($post['hr_emp_salary_designations'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.designations_id', $post['hr_emp_salary_designations']);
            }


            if ($type == 'pdf') {
                $data['employeeList'] = $employeeInfo->get();
            } elseif ($type == 'excel') {
                $data['employeeList'] = $employeeInfo->get();
            } else {
                $data['employeeList'] = $employeeInfo->get();
//            $data['employeeList'] = $employeeInfo->paginate(30);
            }
            $data['salary_sheet_exist'] = 1;
        }
        $data['hr_emp_grades_list'] = $request->hr_emp_grades_list ? $request->hr_emp_grades_list : '';
        $data['salary_month'] = $salary_month;
        $data['status'] = $status;
        $data['bat_dpid'] = $request->bat_dpid ? $request->bat_dpid : '';
        $data['hr_emp_salary_designations'] = $request->hr_emp_salary_designations ? $request->hr_emp_salary_designations : '';

        if ($type == 'pdf') {
            $data['report_title'] = ' Salary Statement - ' . date("F, Y", strtotime(@$salary_month));
            $data['filename'] = 'salary_statement-' . @$salary_month;
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'Hr_payroll.report.salary_sheet_pdf';
            $data['row'] = $data['employeeList'];
            $designation_array=array();
            $designation_type=array();
          //  dd($data['employeeList']);
            foreach ($data['employeeList'] as $employee) {
                $designation_array[$employee->designations_id][]=$employee;
                $designation_type[$employee->designations_id]=$employee->designations_name;
            }
            $data['designation_wise_array']=$designation_array;
            $data['designation_type_array']=$designation_type;

            PdfHelper::exportPdf($view, $data);
        } elseif ($type == 'excel') {
            $file_name = 'Salary Sheet Requisition.xlsx';
            $header_array = [
                [
                    'text' => 'SL No.',
                    'row' => 2
                ],
                [
                    'text' => 'Distributor Point',
                    'row' => 2
                ],
                [
                    'text' => 'Employee Name',
                    'row' => 2
                ],
                [
                    'text' => 'Employee Code',
                    'row' => 2
                ],
                [
                    'text' => 'Attendance',
                    'col' => 3,
                    'sub' => [
                        [
                            'text' => 'Present Days',
                        ],
                        [
                            'text' => 'Leave Days'
                        ],
                        [
                            'text' => 'Absent Days'
                        ]
                    ]
                ]
            ];
            $salary_temp = ['Basic'];
            foreach ($data['salary_component'] as $list) {
                array_push($salary_temp, $list->component_name);
            }
            array_push($salary_temp, 'Total');
            array_push($salary_temp, '(-)PF');
            array_push($salary_temp, 'Earn Salary');
            $length = count($salary_temp);
            $fixed_salary_array = [
                'text' => 'Fixed Salary',
                'col' => $length,
                'sub' => [
                ]
            ];
            foreach ($salary_temp as $salary) {
                $arr_temp = [
                    'text' => $salary
                ];
                array_push($fixed_salary_array['sub'], $arr_temp);
            }

            array_push($header_array, $fixed_salary_array);

            $pfp_salary_array = [
                'text' => 'PFP Salary',
                'col' => 3,
                'sub' => [
                    [
                        'text' => 'PFP Target'
                    ],
                    [
                        'text' => 'PFP Achieve'
                    ],
                    [
                        'text' => 'PFP Earn'
                    ]
                ]
            ];
            array_push($header_array, $pfp_salary_array);
            $net_salary_array = [
                'text' => 'Net Salary',
                'row' => 2
            ];
            array_push($header_array, $net_salary_array);
            $excel_array_data = array();
            if (!empty($data['employeeList'])) {
                foreach ($data['employeeList'] as $i => $emp) {
                    $excel_array['sl_no'] = $i + 1;
                    $excel_array['point_name'] = $emp->point_name;
                    $excel_array['name'] = $emp->name;
                    $excel_array['user_code'] = $emp->user_code;
                    $excel_array['present_days'] = $emp->present_days;
                    $excel_array['leave_days'] = $emp->number_of_leave;
                    $excel_array['absent_days'] = $emp->absent_days;
                    $excel_array['basic_salary'] = $emp->basic_salary;
                    if (!empty($data['salary_component'])) {
                        foreach ($data['salary_component'] as $component) {
                            $slug_name = $component->component_slug;
                            $excel_array[$slug_name] = $emp->$slug_name;
                        }
                    }
                    $excel_array['gross'] = $emp->gross??0;
                    $excel_array['pf_amount_employee'] = $emp->pf_amount_employee??0;
                    $excel_array['net_payable'] = $emp->net_payable??0;
                    $excel_array['pfp_target_amount'] = $emp->pfp_target_amount??0;
                    $excel_array['pfp_achieve_ratio'] = isset($emp->pfp_achieve_ratio) && $emp->pfp_achieve_ratio != null ? $emp->pfp_achieve_ratio . '%' : $emp->pfp_achieve_ratio??0;
                    $excel_array['pfp_earn_amount'] = $emp->pfp_earn_amount??0;
                    $excel_array['net_salary'] = floatval($emp->net_payable??0) +  floatval($emp->pfp_earn_amount??0);
                    $excel_array_data[] = $excel_array;
                }
            }
            $excel_array_to_send = [
                'header_array' => $header_array,
                'data_array' => $excel_array_data,
                'file_name' => $file_name
            ];
            $fileName = exportExcel($excel_array_to_send);

            //$fileName=exportExcel($excel_array_data,$header_array,$file_name);
            return response()->json(['status' => 'success', 'file' => $fileName]);
        }

       // dd($data);
        return view('Hr_payroll.report.empSalaryData', $data);
    }






    /*
     * Employee-Final-Statement
     */

    public function finalSettlement(Request $request) {
        $emp_id = $request->emp_id;
        $salary_month = $request->salary_month;

        $sql = DB::table('hr_monthly_salary_wages')
            ->select(
                'sys_users.user_code', 'sys_users.name', 'designations.designations_name', 'bat_distributorspoint.name as point_name', 'hr_emp_grades.hr_emp_grade_name', 'departments.departments_name', 'hr_emp_sections.hr_emp_section_name', 'sys_users.date_of_join', 'sys_users.applicable_date', 'sys_users.other_conveyance', 'hr_emp_pfp_salary.pfp_earn_amount', 'hr_monthly_salary_wages.*'
            )
            ->leftJoin('sys_users', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id')
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('hr_emp_pfp_salary', function ($join) {
                $join->on('hr_emp_pfp_salary.salary_month', '=', 'hr_monthly_salary_wages.hr_salary_month_name');
                $join->on('hr_emp_pfp_salary.sys_users_id', '=', 'hr_monthly_salary_wages.sys_users_id');
            })
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('sys_users.is_employee', 1)
            ->where('hr_monthly_salary_wages.hr_salary_month_name', $salary_month)
            ->where('hr_monthly_salary_wages.sys_users_id', $emp_id);

        $report_data = $sql->first();
        $sql2 = DB::table('hr_emp_salary_components')
            ->where('sys_users_id', '=', $emp_id)
            ->where('record_ref', '=', $salary_month);
        $salary_components = $sql2->get();

        if (!empty($salary_components)) {
            $component_addition = $component_deduction = $component_variable = [];
            foreach ($salary_components as $component) {
                $component_item = (array) $component;
                if ($component->component_type == 'Variable') {
                    $component_variable[] = $component_item;
                } elseif ($component->component_type == 'Deduction') {
                    $component_deduction[] = $component_item;
                } else {
                    $component_addition[] = $component_item;
                }
            }
            $data['salary_components_addition'] = $component_addition;
            $data['salary_components_deduction'] = $component_deduction;
            $data['salary_components_variable'] = $component_variable;
        }

        if ($report_data) {
            $data['report_title'] = ' Final Settlement Bill - ' . date("F, Y", strtotime($report_data->hr_salary_month_name)); // toDated();
            $data['filename'] = 'active_employee_list_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Accounts & Finance	'];
            $view = 'Hr_payroll.report.final_settlement';
            $data['row'] = $report_data;
            PdfHelper::exportPdf($view, $data);
        } else {
            return redirect()->route('hr-salary-wages-emp-list');
        }
    }

    public function salaryPaySlip(Request $request) {
        $emp_id = $request->emp_id;
        $salary_month = $request->salary_month;

        $sql = DB::table('hr_monthly_salary_wages')
            ->select(
                'sys_users.user_code', 'sys_users.name', 'designations.designations_name', 'bat_distributorspoint.name as point_name', 'hr_emp_grades.hr_emp_grade_name', 'departments.departments_name', 'hr_emp_sections.hr_emp_section_name', 'sys_users.date_of_join', 'sys_users.applicable_date', 'sys_users.other_conveyance', 'hr_monthly_salary_wages.*'
            )
            ->leftJoin('sys_users', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id')
            ->leftJoin('hr_emp_categorys', 'hr_monthly_salary_wages.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'hr_monthly_salary_wages.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('designations', 'hr_monthly_salary_wages.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'hr_monthly_salary_wages.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'hr_monthly_salary_wages.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'hr_monthly_salary_wages.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('sys_users.is_employee', 1)
            ->where('hr_monthly_salary_wages.hr_salary_month_name', $salary_month)
            ->where('hr_monthly_salary_wages.sys_users_id', $emp_id);

        $report_data = $sql->first();
        $sql2 = DB::table('hr_emp_salary_components')
            ->where('sys_users_id', '=', $emp_id)
            ->where('record_ref', '=', $salary_month);
        $salary_components = $sql2->get();
        if (!empty($salary_components)) {
            $component_addition = $component_deduction = $component_variable = [];
            foreach ($salary_components as $component) {
                $component_item = (array) $component;
                if ($component->component_type == 'Variable') {
                    $component_variable[] = $component_item;
                } elseif ($component->component_type == 'Deduction') {
                    $component_deduction[] = $component_item;
                } else {
                    $component_addition[] = $component_item;
                }
            }
            $data['salary_components_addition'] = $component_addition;
            $data['salary_components_deduction'] = $component_deduction;
            $data['salary_components_variable'] = $component_variable;
        }

        if ($report_data) {
            $data['report_title'] = ' Fixed Salary Pay Slip - ' . date("F, Y", strtotime($report_data->hr_salary_month_name)); // toDated();
            $data['filename'] = 'salary_pay_slip';
            $data['orientation'] = "P";
            $data['signatures'] = ['Prepared by', 'Checked by', 'Accounts & Finance	'];
            $view = 'Hr_payroll.report.salary_pay_slip';
            $data['row'] = $report_data;
            PdfHelper::exportPdf($view, $data);
        } else {
            return redirect()->route('hr-salary-wages-emp-list');
        }
    }

    public function PFPSalaryPaySlip(Request $request) {
        $emp_id = $request->emp_id;
        $salary_month = $request->salary_month;

        $sql = DB::table('hr_emp_pfp_salary')
            ->select(
                'sys_users.user_code', 'sys_users.name', 'designations.designations_name', 'bat_distributorspoint.name as point_name', 'hr_emp_grades.hr_emp_grade_name', 'hr_emp_sections.hr_emp_section_name', 'sys_users.date_of_join', 'sys_users.applicable_date', 'hr_emp_pfp_salary.*'
            )
            ->leftJoin('sys_users', 'sys_users.id', '=', 'hr_emp_pfp_salary.sys_users_id')
            ->leftJoin('hr_emp_categorys', 'hr_emp_pfp_salary.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'hr_emp_pfp_salary.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('designations', 'hr_emp_pfp_salary.designations_id', '=', 'designations.designations_id')
            ->leftJoin('hr_emp_grades', 'hr_emp_pfp_salary.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'hr_emp_pfp_salary.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('sys_users.is_employee', 1)
            ->where('hr_emp_pfp_salary.salary_month', $salary_month)
            ->where('hr_emp_pfp_salary.sys_users_id', $emp_id);

        $report_data = $sql->first();

        if ($report_data) {
            $data['report_title'] = ' PFP Salary Pay Slip - ' . date("F, Y", strtotime($report_data->salary_month)); // toDated();
            $data['filename'] = 'salary_pay_slip';
            $data['orientation'] = "P";
            $data['signatures'] = ['Prepared by', 'Checked by', 'Accounts & Finance	'];
            $view = 'Hr_payroll.report.pfp_pay_slip';
            $data['row'] = $report_data;
            PdfHelper::exportPdf($view, $data);
        } else {
            return redirect()->route('hr-salary-wages-emp-list');
        }
    }

    /*
     * Active Employee list for insurance
     */

    public function empInsurance(Request $request, $type = null) {
        $posts = $request->all();
        $data['posted'] = $posts;

        $isdate = !empty($request->date_is) ? $request->date_is : date('Y-m-d');
        $data['posted']['date_is'] = $isdate;

        $status = !empty($request->status) ? $request->status : array('Active');
        $data['posted']['status'] = $status;

        $sql = DB::table('sys_users')
            ->select(
                'sys_users.user_code', 'sys_users.name', 'designations.designations_name', 'departments.departments_name', 'hr_emp_sections.hr_emp_section_name', 'sys_users.date_of_join', 'sys_users.date_of_confirmation'
            )
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('sys_users.is_employee', 1)
            ->where('sys_users.insurance_applicable', 1);



        $company_ids = session('HOUSE_ID');
        if ($company_ids) {
            $sql->where('sys_users.bat_company_id', $company_ids);
        }
        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_sections_id', $posts['hr_emp_sections_id']);
        }

        if (isset($posts['departments_id']) && $posts['departments_id'][0] != null) {
            $sql->whereIn('sys_users.departments_id', $posts['departments_id']);
        }

        if (isset($posts['designations_id']) && $posts['designations_id'][0] != null) {
            $sql->whereIn('sys_users.designations_id', $posts['designations_id']);
        }

        if (isset($posts['branchs_id']) && $posts['branchs_id'][0] != null) {
            $sql->whereIn('sys_users.branchs_id', $posts['branchs_id']);
        }

        if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
        }

        if (isset($posts['status']) && $posts['status'][0] != null) {
            $sql->whereIn('sys_users.status', $posts['status']);
        }

        if ($type == 'pdf') {
            $report_data = $sql->get();
        } elseif ($type == 'excel') {
            $report_data = $sql->get();
        } else {

            $report_data = $sql->paginate(20);
        }




        if (!empty($report_data)) {
            $sl = 0;
            foreach ($report_data as $row) {
                $row->date_of_join = toDated($row->date_of_join);
                $row->date_of_confirmation = toDated($row->date_of_confirmation);
                $row->remarks = '';
            }
        }

        $data['report_data'] = $report_data;

        //dd( DB::getQueryLog() );
        $data['complex_header'] = array(
            array(
                'text' => 'Id No'
            ), array(
                'text' => 'Name'
            ), array(
                'text' => 'Designation'
            ), array(
                'text' => 'Department'
            ), array(
                'text' => 'Section'
            ), array(
                'text' => 'Date of Join'
            ), array(
                'text' => 'Date of Confirmation'
            ), array(
                'text' => 'Remarks'
            )
        );
        $data['table_header'] = array();

        if ($type == 'pdf') {
            $data['report_title'] = implode(" ,", $status) . ' Employee list for insurance - ' . toDated($isdate);
            $data['filename'] = 'employee_list_for_insurance_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'HR.pdf_report_template';
            PdfHelper::exportPdf($view, $data);
        } elseif ($type == 'excel') {
            $file_name = 'Active Employee list for Insurance.xlsx';
            $header_array = [
                [
                    'text' => 'Id No'
                ],
                [
                    'text' => 'Name'
                ],
                [
                    'text' => 'Designation'
                ],
                [
                    'text' => 'Department'
                ],
                [
                    'text' => 'Section'
                ],
                [
                    'text' => 'Date of Join'
                ],
                [
                    'text' => 'Date of Confirmation'
                ],
                [
                    'text' => 'Remarks'
                ]
            ];

//            $pre_header=[
//                [
//                    'text'=>'Automatically System Generated Excel for BATB',
//                    'col'=>8,
//                    'sub'=>[
//                        [
//                            'text'=>'Active Employee List for Insurance',
//                            'col'=>8,
//
//                        ]
//                    ]
//                ]
//            ];

            $excelArrayToSend = array(
                'header_array' => $header_array,
                'data_array' => $report_data,
                'file_name' => $file_name
            );

            // $fileName=exportExcel($report_data,$header_array,$file_name);
            $fileName = exportExcel($excelArrayToSend);
            //return $fileName;
            return response()->json(['status' => 'success', 'file' => $fileName]);
        } else {

            $data['report_data_html'] = view('HR.report_template', $data);
            return view('Hr_payroll.report.employee_for_insurance', $data);
        }
    }

    /*
     * Active Employee list for insurance
     */

    public function earnLeavePaymentSheet(Request $request, $type = null) {
        $posts = $request->all();
        $data['posted'] = $posts;

        $isdate = !empty($request->date_is) ? $request->date_is : date('Y-m-d');
        $data['posted']['date_is'] = $isdate;

        $status = !empty($request->status) ? $request->status : array('Active');
        $data['posted']['status'] = $status;

        DB::connection()->enableQueryLog();

        $sql = DB::table('hr_leave_encashments')
            ->select(
                'sys_users.name', 'sys_users.user_code', 'designations.designations_name', 'sys_users.date_of_join', 'hr_emp_grades.hr_emp_grade_name', 'sys_users.min_gross as gross', 'hr_leave_encashments.encashment_days', 'hr_leave_encashments.encashment_amount', DB::raw("CONCAT('0.00') AS stamp"), DB::raw("(hr_leave_encashments.encashment_amount - 0) AS net_payable"), DB::raw("CONCAT('') AS signature")
            )
            ->leftJoin('sys_users', 'sys_users.id', '=', 'hr_leave_encashments.sys_users_id')
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('sys_users.is_employee', 1);


        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_sections_id', $posts['hr_emp_sections_id']);
        }

        if (isset($posts['departments_id']) && $posts['departments_id'][0] != null) {
            $sql->whereIn('sys_users.departments_id', $posts['departments_id']);
        }

        if (isset($posts['designations_id']) && $posts['designations_id'][0] != null) {
            $sql->whereIn('sys_users.designations_id', $posts['designations_id']);
        }

        if (isset($posts['branchs_id']) && $posts['branchs_id'][0] != null) {
            $sql->whereIn('sys_users.branchs_id', $posts['branchs_id']);
        }

        if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
        }

        if (isset($posts['status']) && $posts['status'][0] != null) {
            $sql->whereIn('hr_leave_encashments.status', $posts['status']);
        }

        if ($type == 'pdf') {
            $report_data = $sql->get();
        } else {
            $report_data = $sql->paginate(30);
        }

        if (!empty($report_data)) {
            $sl = 0;
            foreach ($report_data as $row) {
                $row->date_of_join = toDated($row->date_of_join);
                $row->net_payable = floatval($row->encashment_amount - $row->stamp);
            }
        }

        $data['report_data'] = $report_data;

        //dd( DB::getQueryLog() );
        $data['complex_header'] = array(
            array(
                'text' => 'Name'
            ), array(
                'text' => 'Id No'
            ), array(
                'text' => 'Designation'
            ), array(
                'text' => 'Date of Join'
            ), array(
                'text' => 'Grade'
            ), array(
                'text' => 'Gross'
            ), array(
                'text' => 'Payable Days'
            ), array(
                'text' => 'Payable amounte'
            ), array(
                'text' => 'Stamp'
            ), array(
                'text' => 'Net Payable amount'
            ), array(
                'text' => 'Signature'
            )
        );
        $data['table_header'] = array();
        if ($type == 'pdf') {
            $data['report_title'] = implode(" ,", $status) . ' Earn Leave Payment Sheet - ' . toDated($isdate);
            $data['filename'] = 'earn_leave_payment_sheet_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'HR.pdf_report_template';
            PdfHelper::exportPdf($view, $data);
        } else {
            $data['report_data_html'] = view('HR.report_template', $data);
            return view('Hr_payroll.report.earn_leave_payment_sheet', $data);
        }
    }

    /*
     * Active Employee list for insurance
     */

    public function employeeSalarySheet(Request $request, $type = null) {
        $posts = $request->all();
        $data['posted'] = $posts;

        $status = !empty($request->status) ? $request->status : array('Active');
        $data['posted']['status'] = $status;
        $data['posted']['page'] = isset($request->page) ? $request->page : '';

        $hr_salary_month_name = isset($request->hr_salary_month_name) ? $request->hr_salary_month_name : date('Y-m');
        $data['posted']['hr_salary_month_name'] = $hr_salary_month_name;

        $sql = DB::table('hr_monthly_salary_wages')
            ->select(
                'sys_users.id', 'sys_users.name', 'sys_users.user_code', 'sys_users.date_of_join', 'designations.designations_name', 'hr_emp_grades.hr_emp_grade_name', 'hr_monthly_salary_wages.number_of_working_days', 'hr_monthly_salary_wages.number_of_holidays', 'hr_monthly_salary_wages.present_days', 'hr_monthly_salary_wages.gross', 'hr_monthly_salary_wages.basic_salary', 'hr_monthly_salary_wages.net_payable', 'hr_monthly_salary_wages.ot_hours', 'hr_monthly_salary_wages.ot_rate', 'hr_monthly_salary_wages.ot_payable', 'hr_monthly_salary_wages.attendance_bonus', 'hr_monthly_salary_wages.arrear', 'hr_monthly_salary_wages.pf_amount_employee', 'hr_monthly_salary_wages.insurance_amount', 'hr_monthly_salary_wages.absent_deduction', 'hr_monthly_salary_wages.card_lost_deduction', 'hr_monthly_salary_wages.advance_deduction', 'hr_monthly_salary_wages.other_deduction', 'hr_monthly_salary_wages.hr_salary_month_name', 'hr_monthly_salary_wages.absent_days', 'hr_monthly_salary_wages.payable_days', 'hr_monthly_salary_wages.other_conveyance', 'hr_monthly_salary_wages.earned_salary'
            )
            ->leftJoin('sys_users', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id')
            //->leftJoin('hr_salary_month_configs', 'hr_monthly_salary_wages.hr_salary_month_configs_id', '=', 'hr_salary_month_configs.hr_salary_month_configs_id')
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('sys_users.is_employee', 1);

        $session_con = (sessionFilter('url', 'employee-salary-sheet'));
        $session_con = trim(trim(strtolower($session_con)), 'and');
        if ($session_con) {
            $sql->whereRaw($session_con);
        }
        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0] != null) {
            $sql->whereIn('sys_users.hr_emp_sections_id', $posts['hr_emp_sections_id']);
        }

        if (isset($posts['departments_id']) && $posts['departments_id'][0] != null) {
            $sql->whereIn('sys_users.departments_id', $posts['departments_id']);
        }

        if (isset($posts['designations_id']) && $posts['designations_id'][0] != null) {
            $sql->whereIn('sys_users.designations_id', $posts['designations_id']);
        }

        if (isset($posts['branchs_id']) && $posts['branchs_id'][0] != null) {
            $sql->whereIn('sys_users.branchs_id', $posts['branchs_id']);
        }

        if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'] != null) {
            $sql->where('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
        }
        if (isset($posts['bat_dpid']) && $posts['bat_dpid'] != null) {
            $sql->whereIn('sys_users.bat_dpid', $posts['bat_dpid']);
        }

        if (isset($posts['status']) && $posts['status'][0] != null) {
            $sql->whereIn('sys_users.status', $posts['status']);
        }

        //if (isset($posts['hr_salary_month_name']) && !empty($posts['hr_salary_month_name'])){
        $sql->where('hr_monthly_salary_wages.hr_salary_month_name', $hr_salary_month_name);
        //}

        if ($type == 'pdf') {
            $report_data = $sql->get();
        } else {
            $report_data = $sql->paginate(10);
            $data['report_data_paginate'] = $report_data;
        }


        if ($hr_salary_month_name) {
            $year = date("Y", strtotime($hr_salary_month_name));
            $month = date("m", strtotime($hr_salary_month_name));

            $leaveManager = new LeaveManager();
            $report_data2 = [];

            foreach ($report_data as $key => $report_item) {
                $item_data['Sl_No'] = $key + 1;
                $item_data['Name'] = $report_item->name;
                $item_data['Id_No'] = $report_item->user_code;
                $item_data['Designation'] = $report_item->designations_name;
                $item_data['Date_of_Join'] = toDated($report_item->date_of_join);
//                $item_data['Grade'] = $report_item->hr_emp_grade_name;
//                $item_data['working_days'] = !empty($report_item->number_of_working_days)?$report_item->number_of_working_days:0;
//                $item_data['present_days'] = !empty($report_item->present_days)?$report_item->present_days:0;
//                $item_data['holidays'] = !empty($report_item->number_of_holidays)?$report_item->number_of_holidays:0;

                $leave_policy = $leaveManager->getLeavePolicy($hr_salary_month_name, $report_item->id);
                foreach ($leave_policy as $policy) {
                    $pp = $policy->hr_yearly_leave_policys_name;
                    $item_data[$pp] = !empty($policy->total_elapsed) ? $policy->total_elapsed : 0;
                }

//                $item_data['Payable_Days'] = $report_item->payable_days;
                $item_data['fixed_salary'] = number_format($report_item->gross, 2);
//                $item_data['Basic'] = $report_item->basic_salary;
//                $item_data['House_Rant'] = $report_item->house_rent_amount;
//                $item_data['Food_Allowance'] = $report_item->food;
//                $item_data['Transport_Allowance'] = $report_item->tada;
//                $item_data['Medical_Allowance'] = $report_item->medical;
                $item_data['PF_Amount'] = number_format($report_item->pf_amount_employee, 2);
                $item_data['right_Payable_Salary'] = number_format($report_item->gross - $report_item->pf_amount_employee, 2);
//                $item_data['OT_Hour'] = $report_item->ot_hours;
//                $item_data['OT_Rate'] = $report_item->ot_rate;
//                $item_data['OT_Amount'] = $report_item->ot_payable;
//                $item_data['Attendance_Bonus'] = $report_item->attendance_bonus;
//                $item_data['Arrear_Bill'] = $report_item->arrear;
//                $item_data['Insurance_Amount'] = $report_item->insurance_amount;
//                $item_data['Absent_Deduction'] = $report_item->absent_deduction;
//                $item_data['Id_Card_Lost_Deduction'] = $report_item->card_lost_deduction;
//                $item_data['Advance_Deduction'] = $report_item->advance_deduction;
//                $item_data['Total_Deduction'] = floatval($report_item->absent_deduction + $report_item->advance_deduction + $report_item->other_deduction + $report_item->card_lost_deduction );
//                $item_data['right_Earned_Salary'] = number_format($report_item->earned_salary-$report_item->pf_amount_employee,2);
//                $item_data['absent_days'] = $report_item->absent_days;
                $item_data['right_net_payable'] = number_format($report_item->net_payable, 2);
                $item_data['signature'] = '';

                $report_data2[$key] = (object) $item_data;
            }

            $data['report_data'] = (object) $report_data2;
        }

        if ($type == 'pdf') {
            $data['report_title'] = implode(" ,", $status) . ' Employee Salary Sheet';
            $data['filename'] = 'employee_salary_sheet_pdf';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'HR.pdf_report_template';
            PdfHelper::exportPdf($view, $data);
        } else {
            $data['report_data_html'] = view('HR.report_template', $data);
            return view('Hr_payroll.report.employee_salary_sheet', $data);
        }
    }

    function pfReportSheet(Request $request, $type = '') {
        $post = $request->all();
        $employeeInfo = DB::table('hr_monthly_salary_wages');
        $employeeInfo->select('sys_users.id', 'sys_users.name', 'sys_users.user_code', 'hr_emp_grades.hr_emp_grade_name', 'designations.designations_name', 'bat_company.company_name', 'bat_distributorspoint.name as point_name', 'hr_monthly_salary_wages.*');
        $employeeInfo->join('sys_users', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id');
        $employeeInfo->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_monthly_salary_wages.hr_emp_grades_id');
        $employeeInfo->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'hr_monthly_salary_wages.bat_company_id');
        $employeeInfo->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'hr_monthly_salary_wages.bat_dpid');
        $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'hr_monthly_salary_wages.designations_id');

        $session_con = (sessionFilter('url', 'hr-pf-report'));
        $session_con = trim(trim(strtolower($session_con)), 'and');
        if ($session_con) {
            $employeeInfo->whereRaw($session_con);
        }
//        $employeeInfo->where('hr_monthly_salary_wages.hr_emp_salary_sheet_id', $sheet_id);
        $salary_month = isset($post['salary_month']) ? $post['salary_month'] : date('Y-m');
        if (!empty($salary_month)) {
            $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_name', $salary_month);
        }
        if (!empty($post['bat_company_id'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.bat_company_id', $post['bat_company_id']);
        }
        if (!empty($post['bat_dpid'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.bat_dpid', $post['bat_dpid']);
        }
        if (!empty($post['hr_emp_grades_list'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_grades_id', $post['hr_emp_grades_list']);
        }
        if (!empty($post['hr_emp_salary_designations'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.designations_id', $post['hr_emp_salary_designations']);
        }

        $data['hr_emp_grades_list'] = $request->hr_emp_grades_list ? $request->hr_emp_grades_list : '';
        $data['salary_month'] = $salary_month;
        $data['bat_dpid'] = $request->bat_dpid ? $request->bat_dpid : '';
        $data['hr_emp_salary_designations'] = $request->hr_emp_salary_designations ? $request->hr_emp_salary_designations : '';
        if ($type == 'pdf') {
            $data['employeeList'] = $employeeInfo->get();
        } elseif ($type == 'excel') {
            $data['employeeList'] = $employeeInfo->get();
        } else {
            $data['employeeList'] = $employeeInfo->paginate(30);
        }
        //dd($data['employeeList']);
        if ($type == 'pdf') {
            $data['report_title'] = ' Monthly PF Sheet - ' . date("F, Y", strtotime(@$salary_month));
            $data['filename'] = 'monthly_pf_sheet-' . @$salary_month;
            $data['orientation'] = "P";
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'Hr_payroll.report.monthly_pf_sheet_pdf';
            $data['row'] = $data['employeeList'];
            PdfHelper::exportPdf($view, $data);
        } elseif ($type == 'excel') {
            $file_name = 'Employee Provident Fund(PF).xlsx';
            $header_array = [
                [
                    'text' => 'SL No.',
                    'row' => 2
                ],
                [
                    'text' => 'Distributors Point',
                    'row' => 2
                ],
                [
                    'text' => 'Employee Name',
                    'row' => 2
                ],
                [
                    'text' => 'Employee Code',
                    'row' => 2
                ],
                [
                    'text' => 'Basic Salary',
                    'row' => 2
                ],
                [
                    'text' => 'PF Amount',
                    'col' => 2,
                    'sub' => [
                        [
                            'text' => 'Employee Amount'
                        ],
                        [
                            'text' => 'Company Amount'
                        ]
                    ]
                ],
                [
                    'text' => 'Total PF Amount',
                    'row' => 2
                ]
            ];

            $sl_no = 1;
            $excel_array = array();
            foreach ($data['employeeList'] as $list) {
                $temp = array();
                $temp['sl_no'] = $sl_no;
                $temp['point_name'] = $list->point_name;
                $temp['name'] = $list->name;
                $temp['user_code'] = $list->user_code;
                $temp['basic_salary'] = $list->basic_salary;
                $temp['pf_amount_employee'] = $list->pf_amount_employee;
                $temp['pf_amount_company'] = $list->pf_amount_company;
                $temp['pf_total'] = $list->pf_amount_employee + $list->pf_amount_company;
                $excel_array[] = $temp;
                $sl_no++;
            }
            $excel_array_to_send = [
                'header_array' => $header_array,
                'data_array' => $excel_array,
                'file_name' => $file_name
            ];
            $fileName = exportExcel($excel_array_to_send);
            return response()->json(['status' => 'success', 'file' => $fileName]);
        }

        return view('Hr_payroll.report.monthly_pf_sheet', $data);
    }

    function monthlyEmployeeSalaryList(Request $request, $type = '') {
        $post = $request->all();

        if ($request->salary_sheet_type == 'PFP') {
            $employeeInfo = DB::table('hr_emp_pfp_salary');
            $employeeInfo->select('hr_emp_pfp_salary.*', 'bat_distributorspoint.name as point_name', 'sys_users.name', 'sys_users.user_code', 'salary_sheet_status', 'designations.designations_name');

            $employeeInfo->join('sys_users', 'sys_users.id', '=', 'hr_emp_pfp_salary.sys_users_id');
            $employeeInfo->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_emp_pfp_salary.hr_emp_grades_id');
            $employeeInfo->leftJoin('hr_emp_salary_sheet', 'hr_emp_salary_sheet.hr_emp_salary_sheet_id', '=', 'hr_emp_pfp_salary.hr_emp_salary_sheet_id');
            $employeeInfo->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'hr_emp_pfp_salary.bat_company_id');
            $employeeInfo->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'hr_emp_pfp_salary.bat_dpid');
            $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'hr_emp_pfp_salary.designations_id');

            $employeeInfo->where('hr_emp_salary_sheet.salary_sheet_type', 'PFP');
            $employeeInfo->where('hr_emp_pfp_salary.status', 'Active');
            if (!empty($post['salary_month'])) {
                $employeeInfo->where('hr_emp_pfp_salary.salary_month', '=', $post['salary_month']);
            } else {
                $employeeInfo->where('hr_emp_pfp_salary.salary_month', '=', date('Y-m'));
            }

            if (!empty($post['bat_dpid'])) {
                $employeeInfo->whereIn('hr_emp_pfp_salary.bat_dpid', $post['bat_dpid']);
            }
            if (!empty($post['hr_emp_grades_list'])) {
                $employeeInfo->whereIn('hr_emp_pfp_salary.hr_emp_grades_id', $post['hr_emp_grades_list']);
            }
            if (!empty($post['hr_emp_salary_designations'])) {
                $employeeInfo->whereIn('hr_emp_pfp_salary.designations_id', $post['hr_emp_salary_designations']);
            }
            $data['employeeList'] = $employeeInfo->get();
//            dd( $data['employeeList']);
        } else {
            $sheetHead = DB::table('hr_emp_salary_components')
                ->where('record_type', 'default')
                ->where('auto_applicable', 'YES')
                ->groupBy('component_slug');
            $data['salary_component'] = $sheetHead->get();
            //dd($data['salary_component']);
            $employeeInfo = DB::table('hr_monthly_salary_wages');
            $employeeInfo->select('sys_users.id', 'sys_users.name', 'sys_users.user_code', 'hr_emp_grades.hr_emp_grade_name', 'salary_sheet_status', 'designations.designations_name', 'bat_company.company_name', 'bat_distributorspoint.name as point_name', 'hr_monthly_salary_wages.*');
            if (!empty($data['salary_component'])) {
                foreach ($data['salary_component'] as $component) {
                    $employeeInfo->selectRaw("func_get_salary_component(sys_users.id,hr_salary_month_name,'$component->component_slug') as $component->component_slug");
                }
            }

            $employeeInfo->join('sys_users', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id');
            $employeeInfo->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_monthly_salary_wages.hr_emp_grades_id');
            $employeeInfo->leftJoin('hr_emp_salary_sheet', 'hr_emp_salary_sheet.hr_emp_salary_sheet_id', '=', 'hr_monthly_salary_wages.hr_emp_salary_sheet_id');
            $employeeInfo->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'hr_monthly_salary_wages.bat_company_id');
            $employeeInfo->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'hr_monthly_salary_wages.bat_dpid');
            $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'hr_monthly_salary_wages.designations_id');

            $employeeInfo->where('hr_emp_salary_sheet.salary_sheet_type', 'Fixed');
            $employeeInfo->where('hr_monthly_salary_wages.status', 'Active');

            if (!empty($post['salary_month'])) {
                $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_name', '=', $post['salary_month']);
            } else {
                $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_name', '=', date('Y-m'));
            }
            if (!empty($post['bat_dpid'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.bat_dpid', $post['bat_dpid']);
            }
            if (!empty($post['hr_emp_grades_list'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_grades_id', $post['hr_emp_grades_list']);
            }
            if (!empty($post['hr_emp_salary_designations'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.designations_id', $post['hr_emp_salary_designations']);
            }
            $session_con = (sessionFilter('url', 'hr-salary-wages-emp-list'));
            $session_con = trim(trim(strtolower($session_con)), 'and');
            if ($session_con) {
                $employeeInfo->whereRaw($session_con);
            }


            $data['employeeList'] = $employeeInfo->get();
        }
        $data['hr_emp_grades_list'] = $request->hr_emp_grades_list ? $request->hr_emp_grades_list : '';
        $data['salary_sheet_type'] = $request->salary_sheet_type ? $request->salary_sheet_type : 'statement';
        $data['salary_month'] = $request->salary_month ? $request->salary_month : date('Y-m');
        $data['bat_dpid'] = $request->bat_dpid ? $request->bat_dpid : '';
        $data['hr_emp_salary_designations'] = $request->hr_emp_salary_designations ? $request->hr_emp_salary_designations : '';


        if ($type == 'pdf') {
            if ($request->salary_sheet_type == 'PFP') {
                $data['report_title'] = ' PFP Salary Sheet - ' . date("F, Y", strtotime(@$request->salary_month));
                $data['filename'] = 'salary_sheet-' . @$request->salary_month;
                $data['orientation'] = "L";
                $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
                $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
                $view = 'Hr_payroll.report.pfp_salary_sheet_pdf';
                $data['row'] = $data['employeeList'];
                PdfHelper::exportPdf($view, $data);
            } else {
                $data['report_title'] = ' Fixed Salary Sheet - ' . date("F, Y", strtotime(@$request->salary_month));
                $data['filename'] = 'salary_sheet-' . @$request->salary_month;
                $data['orientation'] = "L";
                $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
                $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
                $view = 'Hr_payroll.report.salary_sheet_pdf';
                $data['row'] = $data['employeeList'];
                PdfHelper::exportPdf($view, $data);
            }
        } elseif ($type == 'excel') {
            $file_name = "Salary Sheet.xlsx";
            $header_array = [
                [
                    'text' => 'SL No.',
                    'row' => 2
                ],
                [
                    'text' => 'Distributor Point',
                    'row' => 2
                ],
                [
                    'text' => 'Employee Name',
                    'row' => 2
                ],
                [
                    'text' => 'Employee Code',
                    'row' => 2
                ],
                [
                    'text' => 'Present Days',
                    'row' => 2
                ],
                [
                    'text' => 'Leave Days',
                    'row' => 2
                ],
                [
                    'text' => 'Absent Days',
                    'row' => 2
                ]
            ];

            $salary_temp = ['Basic'];
            foreach ($data['salary_component'] as $list) {
                array_push($salary_temp, $list->component_name);
            }
            array_push($salary_temp, 'Total');
            $length = count($salary_temp);
            $fixed_salary_array = [
                'text' => 'Fixed Salary',
                'col' => $length,
                'sub' => [
                ]
            ];
            foreach ($salary_temp as $salary) {
                $arr_temp = [
                    'text' => $salary
                ];
                array_push($fixed_salary_array['sub'], $arr_temp);
            }

            array_push($header_array, $fixed_salary_array);
            array_push($header_array, ['text' => 'PF Amount', 'row' => 2]);
            array_push($header_array, ['text' => 'Net Salary', 'row' => 2]);

            $excel_array = array();
            if (!empty($data['employeeList'])) {
                foreach ($data['employeeList'] as $i => $emp) {
                    $temp_arr = array();
                    $temp_arr['sl_no'] = $i + 1;
                    $temp_arr['point_name'] = $emp->point_name;
                    $temp_arr['name'] = $emp->name;
                    $temp_arr['user_code'] = $emp->user_code;
                    $temp_arr['present_days'] = $emp->present_days;
                    $temp_arr['leave_days'] = $emp->number_of_leave;
                    $temp_arr['absent_days'] = $emp->absent_days;
                    $temp_arr['basic_salary'] = $emp->basic_salary;
                    foreach ($data['salary_component'] as $component) {
                        $slug_name = $component->component_slug;
                        $temp_arr[$slug_name] = $emp->$slug_name;
                    }
                    $temp_arr['total'] = $emp->gross;
                    $temp_arr['pf_amount'] = $emp->pf_amount_employee;
                    $temp_arr['net_salary'] = $emp->net_payable;
                    $excel_array[] = $temp_arr;
                }
            }
            $excel_array_to_send = [
                'header_array' => $header_array,
                'data_array' => $excel_array,
                'file_name' => $file_name
            ];
            $fileName = exportExcel($excel_array_to_send);
            return response()->json(['status' => 'success', 'file' => $fileName]);
        }

        //dd($data['employeeList']);
        return view('Hr_payroll.report.employee_salary_sheet', $data);
    }

    /*
     * Employee List with all components
     */
    /* public function employeeListAllComponents(Request $request, $reqtype=null){
      $posts = $request->all();
      $data['posted'] = $posts;

      $status = !empty($request->status)?$request->status:array('Active');
      $data['posted']['status'] = $status;

      $sql =  DB::table('sys_users')
      ->select(
      'sys_users.user_code as id_no',
      'sys_users.name',
      'designations.designations_name as designations',
      'hr_emp_grades.hr_emp_grade_name as grade',
      'sys_users.date_of_join as doj',
      'sys_users.blood_group',
      'hr_emp_units.hr_emp_unit_name as unit',
      'departments.departments_name as department',
      'hr_emp_sections.hr_emp_section_name as section',
      'hr_emp_categorys.hr_emp_category_name as staff_category',
      'sys_users.name_bangla as employee_name_bangla',
      'sys_users.date_of_birth',
      'sys_users.gender',
      'sys_users.marital_status',
      'hr_emp_nominees.nominee_name',
      'sys_users.father_name',
      'sys_users.mother_name',
      DB::raw("CONCAT(sys_users.address, '<br>', sys_users.present_village, '<br>', sys_users.present_po, '(', sys_users.present_post_code, ')', sys_users.present_thana, '<br>', sys_users.present_district) AS present_address"),
      DB::raw("CONCAT(sys_users.permanent_village, '<br>', sys_users.permanent_po, '(', sys_users.permanent_post_code, ')', sys_users.permanent_thana, '<br>', sys_users.permanent_district) AS permanent_address"),
      'sys_users.religion',
      'sys_users.nationality',
      'sys_users.nid as nid',
      'sys_users.attendance_bonus',
      'sys_users.mobile as contact_no.',
      'sys_users.basic_salary',
      'sys_users.house_rent_amount as house_rant',
      'sys_users.min_food as food_allounce',
      'sys_users.min_tada as transport_allounce',
      'sys_users.min_medical',
      'hr_emp_bank_accounts.bank_name',
      'hr_emp_bank_accounts.account_number'
      )
      ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
      ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
      ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
      ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
      ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
      ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
      ->leftJoin('hr_emp_nominees', 'sys_users.id', '=', 'hr_emp_nominees.sys_users_id')
      ->leftJoin('hr_emp_bank_accounts', function($join){
      $join->on('sys_users.id', '=', 'hr_emp_bank_accounts.sys_users_id');
      $join->on('hr_emp_bank_accounts.bank_account_types_name', '=', DB::raw("'Salary Account'"));
      })
      ->where('is_employee', 1)
      ->whereIn('sys_users.status', $status);

      if (isset($posts['branchs_id']) && $posts['branchs_id'][0]!= null){
      $sql->whereIn('sys_users.branchs_id', $posts['branchs_id']);
      }

      if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0]!= null){
      $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
      }

      if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0]!= null){
      $sql->whereIn('sys_users.hr_emp_sections_id', $posts['hr_emp_sections_id']);
      }

      if (isset($posts['departments_id']) && $posts['departments_id'][0]!= null){
      $sql->whereIn('sys_users.departments_id', $posts['departments_id']);
      }

      if (isset($posts['designations_id']) && $posts['designations_id'][0]!= null){
      $sql->whereIn('sys_users.designations_id', $posts['designations_id']);
      }

      if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'][0]!= null){
      $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
      }

      if ($reqtype =='pdf' || $reqtype =='xlsx' ){
      $report_data = $sql->groupBy('sys_users.user_code')->get();
      }else{
      $report_data = $sql->groupBy('sys_users.user_code')->paginate(10);
      }

      if(!empty($report_data)){
      foreach ($report_data as $row){
      $row->date_of_birth = toDated($row->date_of_birth);
      $row->doj = toDated($row->doj);
      $row->remarks = '';
      }
      }

      $data['report_data'] = $report_data;
      $data['complex_header'] = array(
      array(
      'text'=>'ID No.'
      ),array(
      'text'=>'Name'
      ),array(
      'text'=>'Designation'
      ),array(
      'text'=>'Grade'
      ),array(
      'text'=>'DoJ'
      ),array(
      'text'=>'Blood Group'
      ),array(
      'text'=>'Unit'
      ),array(
      'text'=>'Department'
      ),array(
      'text'=>'Section'
      ),array(
      'text'=>'Staff Category'
      ),array(
      'text'=>'Employee name Bangla'
      ),array(
      'text'=>'Date of Birth'
      ),array(
      'text'=>'Gender'
      ),array(
      'text'=>'Maritual Status'
      ),array(
      'text'=>'Nominee Name'
      ),array(
      'text'=>'Father Name'
      ),array(
      'text'=>'Mother Name'
      ),array(
      'text'=>'Present address'
      ),array(
      'text'=>'Parmanant address'
      ),array(
      'text'=>'Relagion'
      ),array(
      'text'=>'Nationality'
      ),array(
      'text'=>'NID/Birth Certificate'
      ),array(
      'text'=>'Attendance Bonus'
      ),array(
      'text'=>'Contact No'
      ),array(
      'text'=>'Basic Salary'
      ),array(
      'text'=>'House Rant'
      ),array(
      'text'=>'Food Allounce'
      ),array(
      'text'=>'Transport Allounce'
      ),array(
      'text'=>'Medical Allounce'
      ),array(
      'text'=>'Bank Name'
      ),array(
      'text'=>'Account no'
      ),array(
      'text'=>'Remarks'
      )
      );
      $data['table_header'] = array();

      if($reqtype =='xlsx'){
      $data['filename'] = 'employee_list_all_components'.Auth::user()->id.'.xlsx';
      $data['title'] = 'Employee List';
      $filename = self::excelReport($data);
      return response()->json(['status'=>'success','file'=>$filename]);
      }elseif ($reqtype =='pdf'){
      $data['report_title'] = 'Employee List with all components - '. toDated(date('Y-m-d'));
      $data['filename'] = 'Employee_List_with_all_components_pdf';
      $data['orientation'] = "L";
      $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
      $data['signatures']=['Prepared by','Checked by','Approved by'];
      $view='HR.pdf_report_template';
      PdfHelper::exportPdf($view,$data);
      }else{
      $data['report_data_html'] = view('HR.report_template',$data);
      return view('Employee_report.employee_list_all-components_report', $data);
      }

      } */

    /*
     * Excel Export
     */
    /* private function excelReport($data){
      $filename = $data['filename']; //'employee-list-'.Auth::user()->id.'.xlsx';
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setTitle($data['title']);

      $header = array_map(function ($ar) {
      return !empty($ar['text']) ? $ar['text'] : '';
      }, $data['complex_header']);

      $number = 0;
      $row = 1;
      exportHelper::getCustomCell($sheet, 1, 0, 'SR Chemical Industries LTD.', count($header) - 1, null, 18, 'center');
      exportHelper::getCustomCell($sheet, 2, 0, $data['title'], count($header) - 1, null, 16, 'center');
      //exportHelper::getCustomCell($sheet,3,0,'Filter By',2,null,null,null,true);

      $row = 3;
      exportHelper::get_column_title($number, $row, $header, $sheet);

      $row = 4;
      if (isset($data['report_data'])){
      foreach ($data['report_data'] as $item) {
      $number = 0;
      foreach ($item as $col => $val){
      $sheet->setCellValue(exportHelper::get_letter($number++).$row, str_replace("<br>",", ",$val) );
      }
      $row++;
      }
      }

      exportHelper::excelHeader($filename,$spreadsheet);
      return $filename;
      } */

    function salaryStatement(Request $request, $type = '') {
        $post = $request->all();
        $month = date('Y-m');
        $sheetHead = DB::table('hr_emp_salary_components')
            ->where('record_type', 'default')
            ->where('auto_applicable', 'YES')
            ->groupBy('component_slug');
        $data['salary_component'] = $sheetHead->get();

        $q = DB::table('sys_users')
            ->selectRaw("
                    `hr_emp_attendance`.`sys_users_id`,
                    sys_users.name,
                    sys_users.user_code,
                    sys_users.pf_applicable,
                    sys_users.gf_applicable,
                    sys_users.date_of_join,
                    sys_users.insurance_applicable,
                    sys_users.status,
                    sys_users.separation_date,
                    sys_users.late_deduction_applied,
                    sys_users.bat_company_id,
                    sys_users.bat_dpid,
                    sys_users.designations_id,
                    sys_users.departments_id,
                    sys_users.branchs_id,
                    sys_users.hr_emp_grades_id,
                    sys_users.hr_emp_units_id,
                    sys_users.hr_emp_categorys_id,
                    sys_users.hr_emp_sections_id,
                    sys_users.salary_account_no,
                    bat_distributorspoint.name point_name,
                    sys_users.basic_salary,
                    sys_users.min_gross as gross,
                    ifnull(sys_users.pf_amount_employee,0) as pf_amount_employee,
                    ifnull(sys_users.pf_amount_company,0) as pf_amount_company,
                    ifnull(sys_users.gf_amount,0) as gf_amount,
                    ifnull(sys_users.insurance_amount,0) as insurance_amount,
                    ifnull(func_get_variable_salary(sys_users.user_code,'$month'),0) as pfp_achievement,
                    IFNULL(COALESCE((select sum(variable_salary_amount) from hr_emp_monthly_variable_salary where sys_users_id=sys_users.id and vsalary_month='$month' AND hr_emp_monthly_variable_salary.status='Active'),max_variable_salary),0) as target_variable_salary,
                    IFNULL((select sum(monthly_payment) from hr_emp_loan where sys_users_id=sys_users.id and hr_emp_loan.status='Active'),0) as due_loan_amount,
                    IFNULL((select sum(conveyance_amount) from hr_other_conveyances where sys_users_id=sys_users.id),0) as other_conveyance,
                    substr(hr_emp_attendance.day_is,1,7) AS `hr_salary_month_name`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'R') then 1 else 0 end)) AS `number_of_working_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in ('P','HP','WP','L','EO')) then 1 else 0 end)) AS `present_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'A') then 1 else 0 end)) AS `absent_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'Lv') then 1 else 0 end)) AS `number_of_leave`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'W') then 1 else 0 end)) AS `number_of_weekend`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'H') then 1 else 0 end)) AS `number_of_holidays`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in('L','EO')) then 1 else 0 end)) AS `late_days`");
        if (!empty($data['salary_component'])) {
            foreach ($data['salary_component'] as $component) {
                $q->selectRaw("func_get_salary_component(sys_users.id,'$month','$component->component_slug') as $component->component_slug");
            }
        }
        $q->LeftJoin('hr_emp_attendance', 'sys_users.id', 'hr_emp_attendance.sys_users_id');
        $q->LeftJoin('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id');
        $q->whereRaw("sys_users.id NOT IN(SELECT sys_users_id FROM hr_monthly_salary_wages WHERE hr_salary_month_name='$month')
                    AND substr(hr_emp_attendance.day_is,1,7)='$month'
                    AND sys_users.is_employee=1
                    AND sys_users.status IN('Active','Separated')");

        $session_con = (sessionFilter('url', 'emp-salary-statement'));
        $session_con = trim(trim(strtolower($session_con)), 'and');
        $q->whereRaw($session_con);
        $q->groupBy('sys_users_id');
        $data['employeeList'] = $q->get();



        return view('Hr_payroll.report.employee_salary_statement', $data);
    }
    //Shibly :Individual PF Report.
    public function individualPFReportSheet(Request $request, $pdf = false) {
        $data=array();
        $post = $request->all();
        //dd($post);
        if($post) {
            $start_month = $request->salary_month;
            $end_month = $request->end_month;
            $id = $request->id;
            $employeeWisePfInfo = DB::table('hr_monthly_salary_wages');
            $employeeWisePfInfo->select(
                'hr_monthly_salary_wages.sys_users_id', 'hr_monthly_salary_wages.hr_salary_month_name', 'hr_monthly_salary_wages.hr_salary_month_name', 'hr_monthly_salary_wages.pf_amount_company', 'hr_monthly_salary_wages.pf_amount_employee'
            );
            $employeeWisePfInfo->where('sys_users_id', $id);
            $employeeWisePfInfo->where('hr_salary_month_name', '>=', $start_month);
            $employeeWisePfInfo->where('hr_salary_month_name', '<=', $end_month);
            $data['employeewise_pfinfo'] = $employeeWisePfInfo->get();

            $openingBalance = DB::table('hr_monthly_salary_wages');
            $openingBalance->select(DB::raw("SUM(hr_monthly_salary_wages.pf_amount_company) as amount_company"),
                DB::raw("SUM(hr_monthly_salary_wages.pf_amount_employee) as amount_employee"));
            $openingBalance->where('sys_users_id', $id);
            $openingBalance->where('hr_salary_month_name', '<', $start_month);
            //$openingBalance->sum('hr_monthly_salary_wages.pf_amount_company');
            //$openingBalance->sum('hr_monthly_salary_wages.pf_amount_employee');
            //echo $openingBalance->toSql(); exit;
            $data['opening_balance'] = $openingBalance->first();
            // dd($data['opening_balance']);

            $data['selected_val'] = array(
                'start_month' => $start_month,
                'end_month' => $end_month,
                'use_id' => $id
            );
        }
        if ($pdf) {
            $data['report_title'] = ' Individual Employee PF Report';
            $data['filename'] = 'pf_individual_sheet' ;
            $data['orientation'] = "L";
            $data['emp_info'] = employeeInfo($id, $pdf = 1);
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'Hr_payroll.report.hr_pf_individual_report_pdf';
            // $data['row'] = $data['employeeList'];

            PdfHelper::exportPdf($view, $data);
        }else{
            return view('Hr_payroll.report.hr_pf_individual_report', $data);
        }

        return view('Hr_payroll.report.hr_pf_individual_report');
    }

    function getSeparationSettlementList() {
        return view('Hr_payroll.report.emp_separation_list');
    }

}
