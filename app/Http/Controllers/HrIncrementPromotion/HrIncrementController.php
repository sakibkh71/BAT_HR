<?php

namespace App\Http\Controllers\HrIncrementPromotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;
//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use exportHelper;
use App\Helpers\PdfHelper;

class HrIncrementController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    //Increment List
    public function increment(Request $request) {
        $post = $request->all();
        $employeeInfo = DB::table('hr_employee_record_logs');
        $employeeInfo->select(
                'hr_employee_record_logs.*',
                'sys_users.id',
                'sys_users.name',
                'sys_users.user_code',
                'hr_emp_grades.hr_emp_grade_name',
                'designations.designations_name',
                'c.name as creator_name',
                'sys_status_flows.status_flows_name',
                'sys_delegation_conf.step_name',
                'b.name as delegation_person_name',
                'bd.name as point_name'
        );
        $employeeInfo->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id');
        $employeeInfo->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id');
        $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id');
        $employeeInfo->leftJoin('sys_users as b', 'b.id', '=', 'hr_employee_record_logs.delegation_person');
        $employeeInfo->leftJoin('bat_distributorspoint as bd', 'bd.id', '=', 'hr_employee_record_logs.bat_dpid');
        $employeeInfo->leftJoin('sys_users as c', 'c.id', '=', 'hr_employee_record_logs.created_by');
        $employeeInfo->join('sys_status_flows', 'sys_status_flows.status_flows_id', '=', 'hr_employee_record_logs.hr_log_status');
        $employeeInfo->leftJoin('sys_delegation_conf', function ($join) {
            $join->on('hr_employee_record_logs.delegation_for', '=', 'sys_delegation_conf.delegation_for')
                    ->on('hr_employee_record_logs.delegation_ref_event_id', '=', 'sys_delegation_conf.ref_event_id')
                    ->on('hr_employee_record_logs.delegation_version', '=', 'sys_delegation_conf.delegation_version')
                    ->on('hr_employee_record_logs.delegation_step', '=', 'sys_delegation_conf.step_number');
        });
        $employeeInfo->where('hr_employee_record_logs.record_type', '=', 'salary_restructure');
        $employeeInfo->where('hr_employee_record_logs.status', '=', 'Active');
        $employeeInfo->where('hr_employee_record_logs.created_by', '=', Auth::id());
        if (!empty($post['date_range'])) {
            $range = explode(" - ", $post['date_range']);
            $employeeInfo->whereBetween('hr_employee_record_logs.applicable_date', $range);
        }
        if (!empty($post['salary_approval_status'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_log_status', $post['salary_approval_status']);
        }
        if (!empty($post['hr_emp_categorys'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_emp_units_id', $post['hr_emp_categorys']);
        }
        if (!empty($post['hr_emp_grades'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_emp_grades_id', $post['hr_emp_grades']);
        }

        $employeeInfo->orderBy('hr_employee_record_logs.hr_employee_record_logs_id', 'DESC');

        $data['employeeList'] = $employeeInfo->get();
        $data['hr_emp_grades'] = $request->hr_emp_grades ? $request->hr_emp_grades : '';
        $data['hr_emp_categorys'] = $request->hr_emp_categorys ? $request->hr_emp_categorys : '';
        $data['salary_approval_status'] = $request->salary_approval_status ? $request->salary_approval_status : '';
        $data['date_range'] = $request->date_range ? $request->date_range : '';

        return view('HrIncrementPromotion.Increment.incrementList', $data);
    }

    //Increment form for New Increment
    public function incrementForm(Request $request) {
        $data['log_id'] = $request->log_id;
        return view('HrIncrementPromotion.Increment.increment', $data);
    }

    //Get Employee list for increment filter
    public function incrementEmployees(Request $request) {
        $data['employeeInfo'] = self::selectedEmployeeData($request);
        $data['post_data'] = $request->all();       
        $emp_item = view('HrIncrementPromotion.Increment.hr_get_selected_increment_emp', $data)->render();
        return Response::json(['emp_list' => $emp_item]);
    }

    //Store Increment Information
    function storeIncrement(Request $request) {

        $emp_id = $request->emp_id;
        $new_gross_salary = $request->new_gross_salary;
        $based_on = $request->based_on;
        $increment_type = $request->increment_type;
        if (sizeof($request->emp_id) > 0) {
            foreach ($request->emp_id as $i => $item) {
                $user_info = DB::table('sys_users')->where('id', $emp_id[$i])->first();
                $gross_salary = str_replace(',', '', $new_gross_salary[$i]);
                
                $increment_amount = floatval($gross_salary) - floatval($user_info->min_gross);
                $salary_cal = floatval($increment_amount /$user_info->min_gross);
                $basic_salary = floatval( $user_info->basic_salary+($user_info->basic_salary*$salary_cal));

                $user_info_arr = array(
                    'sys_users_id' => $user_info->id,
                    'record_type' => 'salary_restructure',
                    'designations_id' => $user_info->designations_id,
                    'previous_designations_id' => $user_info->designations_id,
                    'hr_emp_grades_id' => $user_info->hr_emp_grades_id,
                    'previous_grades_id' => $user_info->hr_emp_grades_id,
                    'bat_company_id' => $user_info->bat_company_id,
                    'bat_dpid' => $user_info->bat_dpid,
                    'applicable_date' => $request->applicable_date,
                    'basic_salary' => $basic_salary,
                    'increment_type' => $increment_type,
                    'increment_based_on' => $based_on,
                    'increment_amount' => $increment_amount,
                    'gross_salary' => $gross_salary,
                    'previous_gross' => $user_info->min_gross,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d h:i:s'),
                    'hr_log_status' => 48,
                );

                $insert = DB::table('hr_employee_record_logs')->insert($user_info_arr);
                $record_log_last_id = DB::getPdo()->lastInsertId();
                
                $salary_info_array = salary_calculation_arr($user_info->id, $user_info->hr_emp_grades_id, $record_log_last_id, $salary_cal);
                $inserts = DB::table('hr_emp_salary_components')->insert($salary_info_array);
            }
            if ($insert) {
                return response()->json(array('success' => true));
            } else {
                return response()->json(array('success' => false));
            }
        }
        return response()->json(array('success' => false));
    }

    //Edit Increment
    function editIncrementRecord(Request $request) {
        $sql = DB::table('hr_employee_record_logs');
        $sql->select(
            'hr_employee_record_logs.hr_employee_record_logs_id',
            'sys_users.id',
            'sys_users.name',
            'sys_users.date_of_join',
            'hr_employee_record_logs.applicable_date',
            'designations.designations_name',
            'sys_users.basic_salary',
            'sys_users.user_code',
            'sys_users.min_gross',
            'hr_employee_record_logs.previous_gross',
            'hr_employee_record_logs.basic_salary as log_basic_salary',
            'hr_employee_record_logs.gross_salary',
            'hr_employee_record_logs.increment_amount',
            'hr_employee_record_logs.increment_based_on',
            'sys_users.default_salary_applied'
        );
        $sql->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id');
        $sql->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id');
        $sql->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id');
        $sql->whereIn('hr_employee_record_logs.hr_employee_record_logs_id', explode(',', $request->log_id));
        $sql->orderBy('hr_employee_record_logs_id', 'DESC');
        $data['employeeInfo'] = $sql->get();

        $ratio = 0;
        $emp_item = '';
        $existing_selected_emp = [];
        foreach ($data['employeeInfo'] as $key => $item) {
            array_push($existing_selected_emp, $item->id);
        }
        $existing_selected_emp = implode(',', $existing_selected_emp);

        $emp_item = view('HrIncrementPromotion.Increment.hr_get_selected_increment_emp', $data)->render();

        return Response::json([
            'emp_list' => $emp_item,
            'existing_selected_emp' => $existing_selected_emp
        ]);

    }

    //Update Increment
    function updateIncrement(Request $request) {
        $log_id = $request->log_id;
        $emp_id = $request->emp_id;
        $new_gross_salary = $request->new_gross_salary;

        if (sizeof($request->log_id) > 0) {
            foreach ($request->log_id as $i => $item) {

                $log_info = DB::table('hr_employee_record_logs')->select(
                        'hr_employee_record_logs.*',
                        'sys_users.basic_salary',
                        'sys_users.min_gross',
                        'hr_employee_record_logs.basic_salary as log_basic_salary'
                    )
                    ->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id')
                    ->where('hr_employee_record_logs_id', $item)
                    ->first();

                $gross_salary = $new_gross_salary[$i];
                $increment_amount = floatval($gross_salary) - floatval($log_info->min_gross);
                $salary_cal = floatval($increment_amount / $log_info->min_gross);

                $basic_salary = floatval( $log_info->basic_salary + ($log_info->basic_salary * $salary_cal));

                $user_info_arr = array(
                    'record_type' => 'salary_restructure',
                    'designations_id' => $log_info->designations_id,
                    'hr_emp_grades_id' => $log_info->hr_emp_grades_id,
                    'previous_grades_id' => $log_info->previous_grades_id,
                    'previous_designations_id' => $log_info->previous_designations_id,
                    'bat_company_id' => $log_info->bat_company_id,
                    'bat_dpid' => $log_info->bat_dpid,
                    'applicable_date' => $request->applicable_date,
                    'basic_salary' => $basic_salary,
                    'increment_type' => $request->increment_type,
                    'increment_based_on' => $request->based_on,
                    'increment_amount' => $increment_amount,
                    'gross_salary' => $gross_salary,
                    'previous_gross' => $log_info->min_gross,
                    'updated_by' => Auth::id(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'hr_log_status' => 48,
                );

                $update_record = DB::table('hr_employee_record_logs')->where('hr_employee_record_logs_id', $item)->update($user_info_arr);
                DB::table('hr_emp_salary_components')->where('record_type', 'employee_record')->where('record_ref', $item)->delete();
                $salary_info_array = salary_calculation_arr($log_info->sys_users_id, $log_info->hr_emp_grades_id, $item, $salary_cal);
                $update_component = DB::table('hr_emp_salary_components')->insert($salary_info_array);
            }

            if ($update_record) {
                return response()->json(array('success' => true));
            } else {
                return response()->json(array('success' => false));
            }
        }
        return response()->json(array('success' => false));
    }

    //Inactive Employee Increment Record
    function deleteHrRecord(Request $request) {
        $arr = array(
            'status' => 'Inactive',
            'updated_at' => date("Y-m-d h:i:s"),
            'updated_by' => Auth::id()
        );
        $delete = DB::table('hr_employee_record_logs')->whereIn('hr_employee_record_logs_id',  $request->log_id)->update($arr);
        if ($delete) {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }

    //Increment Letter
    function incrementLetter($log_id) {
        $data['emp_log'] = DB::table('hr_employee_record_logs')->select(
                'hr_employee_record_logs.*',
                'sys_users.name',
                'sys_users.user_code',
                'designations.designations_name',
                'bat_company.company_name',
                'bat_distributorspoint.name as point_name'
            )
            ->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id')
            ->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_employee_record_logs.hr_emp_grades_id')
            ->leftJoin('designations', 'designations.designations_id', '=', 'hr_employee_record_logs.designations_id')
            ->where('hr_employee_record_logs.record_type', '=', 'salary_restructure')
            ->where('hr_employee_record_logs.hr_employee_record_logs_id', '=', $log_id)
            ->first();

        $data['report_title'] = 'Salary Increment Letter';
        $data['filename'] = 'increment_letter';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view = 'HrIncrementPromotion.Increment.incrementLetterView';
        PdfHelper::exportPdf($view, $data);
    }

    //Get Employee Data
    function selectedEmployeeData($request) {
        $remove_emp = trim($request->remove_emp, ',');
        $sql = DB::table('sys_users');
        $sql->select('sys_users.id', 'sys_users.name', 'sys_users.date_of_join', 'sys_users.applicable_date', 'sys_users.yearly_increment', 'hr_emp_grades.yearly_increment as grade_yearly_increment', 'sys_users.user_code', 'hr_emp_grades.hr_emp_grade_name', 'designations.designations_name', 'sys_users.basic_salary', 'sys_users.min_gross', 'sys_users.default_salary_applied');
        $sql->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id');
        $sql->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id');
        $sql->where('sys_users.is_employee', '1');
        $sql->where('sys_users.status', 'Active');

        if (!empty($remove_emp)) {
            $sql->whereNotIn('sys_users.id', explode(',', $remove_emp));
        }

        if ($request->emp_list) {
            $sql->whereIn('sys_users.id', $request->emp_list);
        }

        if ($request->hr_emp_grades_list) {
            $sql->whereIn('sys_users.hr_emp_grades_id', $request->hr_emp_grades_list);
        }

        if ($request->designations_id) {
            $sql->whereIn('sys_users.designations_id', $request->designations_id);
        }

        if ($request->bat_dpid) {
            $sql->whereIn('sys_users.bat_dpid', $request->bat_dpid);
        }

        $session_con = (sessionFilter('url','hr-get-selected-increment-emp'));
        $session_con = trim(trim(strtolower($session_con)),'and');

        if (!empty($session_con)){ $sql->whereRaw($session_con); }

        $employeeInfo = $sql->get();

        return $employeeInfo;
    }

    //Go for delegation process
    public function goToHRDelegationProcess(Request $request) {
        $post = $request->all();
        $result = goToDelegationProcess($post);
        $success_count = 0;
        $failed_count = 0;
        $failed_cause = '';

        if (isset($result)) {
            foreach ($result as $data) {
                foreach ($data['data'] as $result_key => $result_code) {
                    if ($result_code['mode'] == 'Success') {
                        $success_count++;
                    } else {
                        $failed_count++;
                        $failed_cause .= $result_key . ' - ' . $result_code['msg'] . '<br/>';
                    }
                }
            }
        }
        $return_result = "Total Success " . $success_count . "<br/>Total Failed " . $failed_count . "<br/>" . $failed_cause;

        return $return_result;
    }

    //Approval List for Delegation Process
    public function salaryApprovalList() {
        $slug = 'hr_inc';
        $data['columns'] = array(
            'hr_employee_record_logs_id',
            'record_type',
            'hr_employee_record_logs.gross_salary',
            'hr_log_status',
            'hr_employee_record_logs.applicable_date',
            'previous_gross',
            'sys_users.name',
            'designations.designations_name',
            'hr_emp_grades.hr_emp_grade_name',
            'hr_employee_record_logs.created_by',
            'hr_employee_record_logs.created_at'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'], array('sys_users', 'designations', 'hr_emp_grades'));
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_employee_record_logs_id;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;
        return view('HrIncrementPromotion.salary_approval_list', $data);
    }

    //Approved form deligation
    function HRsalaryBulkApproved(Request $request) {
        $codes = $request->codes;
        $comments = 'Salary Bulk Approved';
        $request->merge([
            'slug' => 'hr_inc',
            'comments' => $comments,
            'additional_data' => ''
        ]);
        $post = $request->all();
        $result = goToDelegationProcess($post);
        if ($result) {
            $resultArray = json_decode($result, true);
            $sucs_msg = [];
            $fail_msg = [];
            foreach ($resultArray as $item) {
                foreach ($item['data'] as $code => $prc_item) {
                    if ($prc_item['mode'] == 'Success') {

                        $sucs_msg[$code] = $prc_item['msg'];

                        if($item['status_id'] == 50){
                            $increment_info = DB::table('hr_employee_record_logs')->where('hr_employee_record_logs_id', '=', $code)->first();

                            $emp_info = DB::table('sys_users')->where('id',$increment_info->sys_users_id)->first();
                            $inc_amount_ratio = $increment_info->increment_amount/$increment_info->previous_gross;
                            $update_arr = array(
                                'basic_salary' => $increment_info->basic_salary,
                                'min_gross' => $increment_info->gross_salary,
                                'pf_amount_employee' => $emp_info->pf_amount_employee+($emp_info->pf_amount_employee*$inc_amount_ratio),
                                'pf_amount_company' => $emp_info->pf_amount_company+($emp_info->pf_amount_company*$inc_amount_ratio),
                                'applicable_date' => $increment_info->applicable_date,
                                'hr_emp_grades_id' => $increment_info->hr_emp_grades_id,
                                'designations_id' => $increment_info->designations_id
                            );

                            DB::table('sys_users')->where('id', '=', $increment_info->sys_users_id)->update($update_arr);

                            $scomponents = DB::table('hr_emp_salary_components')
                                ->select('component_slug','addition_amount','deduction_amount')
                                ->where('sys_users_id', $increment_info->sys_users_id)
                                ->where('record_type', 'employee_record')
                                ->where('record_ref', $increment_info->hr_employee_record_logs_id)
                                ->get();

                            foreach ($scomponents as $component){
                                $cup_arr = array(
                                    'addition_amount' => $component->addition_amount,
                                    'deduction_amount' => $component->deduction_amount,
                                );

                                DB::table('hr_emp_salary_components')->where('sys_users_id', '=', $increment_info->sys_users_id)
                                    ->where('component_slug', '=', $component->component_slug)
                                    ->where('record_type', '=', 'default')
                                    ->update($cup_arr);
                            }
                        }

                    } else {
                        $fail_msg[$code] = $prc_item['msg'] . ' for ' . $code;
                    }
                }
            }
            return Response::json(['sucs_msg' => $sucs_msg, 'fail_msg' => $fail_msg]);
        } else {
            return 'There is no return from delegation';
        }
    }

    //Employee Promotion History
    public function incrementPromotionLog() {
        $employeeInfo = DB::table('sys_users');
        $employeeInfo->select('sys_users.*', 'sys_users.name', 'sys_users.user_code', 'hr_emp_grades.hr_emp_grade_name', 'departments.departments_name', 'designations.designations_name');
        $employeeInfo->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id');
        $employeeInfo->join('departments', 'departments.departments_id', '=', 'sys_users.departments_id');
        $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id');
        $employeeInfo->where('sys_users.status', '=', 'Active');
        $data['employeeList'] = $employeeInfo->get();
        return view('HrIncrementPromotion.incrementPromotionLog', $data);
    }

    //Employee History Log
    public function incrementPromotionLogView($emp_id) {
        $employeeInfo = DB::table('sys_users');
        $employeeInfo->select('sys_users.*', 'sys_users.name', 'sys_users.user_code', 'hr_emp_grades.hr_emp_grade_name', 'departments.departments_name', 'designations.designations_name');
        $employeeInfo->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id');
        $employeeInfo->join('departments', 'departments.departments_id', '=', 'sys_users.departments_id');
        $employeeInfo->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id');
        $employeeInfo->where('sys_users.id', '=', $emp_id);
        $data['emp_log'] = $employeeInfo->first();

        $sql = DB::table('hr_employee_record_logs');
        $sql->where('hr_employee_record_logs.sys_users_id', '=', $emp_id);
        $sql->where('hr_employee_record_logs.record_type', '=', 'salary_restructure');
        $sql->where('hr_employee_record_logs.status', '=', 'Active');
        $sql->orderBy('hr_employee_record_logs_id', 'DESC');
        $data['incrementLog'] = $sql->get();

        $sql = DB::table('hr_employee_record_logs');
        $sql->select("hr_employee_record_logs.*", 'user_grade.hr_emp_grade_name', 'record_grade.hr_emp_grade_name as record_grade_name', 'designations.designations_name');
        $sql->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id');
        $sql->join('hr_emp_grades as user_grade', 'user_grade.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id');
        $sql->join('hr_emp_grades as record_grade', 'record_grade.hr_emp_grades_id', '=', 'hr_employee_record_logs.hr_emp_grades_id');
        $sql->join('designations', 'designations.designations_id', '=', 'hr_employee_record_logs.designations_id');
        $sql->where('hr_employee_record_logs.sys_users_id', '=', $emp_id);
        $sql->where('hr_employee_record_logs.record_type', '=', 'promotion');
        $sql->where('hr_employee_record_logs.status', '=', 'Active');
        $sql->orderBy('hr_employee_record_logs_id', 'DESC');
        $data['promotionLog'] = $sql->get();

        return view('HrIncrementPromotion.incrementPromotionHistory', $data);
    }

    //Increment report export
    public function incrementListExport(Request $request) {
        $categorys = [];
        $branchs = [];
        $hr_emp_grades_list = [];
        $hr_emp_departments = [];
        if ($request->hr_emp_category_id) {
            $cats = DB::table('hr_emp_categorys')
                    ->whereIn('hr_emp_categorys_id', $request->hr_emp_category_id)
                    ->get();
            foreach ($cats as $item) {
                $categorys[] = $item->hr_emp_category_name;
            }
            $categorys = implode(',', $categorys);
        }
        if ($request->branchs_id) {
            $branch = DB::table('branchs')
                    ->whereIn('branchs_id', $request->branchs_id)
                    ->get();
            foreach ($branch as $item) {
                $branchs[] = $item->branchs_name;
            }
            $branchs = implode(',', $branchs);
        }

        if ($request->hr_emp_grades_list) {
            $grade = DB::table('hr_emp_grades')
                    ->whereIn('hr_emp_grades_id', $request->hr_emp_grades_list)
                    ->get();
            foreach ($grade as $item) {
                $hr_emp_grades_list[] = $item->hr_emp_grade_name;
            }
            $hr_emp_grades_list = implode(',', $hr_emp_grades_list);
        }
        if ($request->hr_emp_departments) {
            $dept = DB::table('departments')
                    ->whereIn('departments_id', $request->hr_emp_departments)
                    ->get();
            foreach ($dept as $item) {
                $hr_emp_departments[] = $item->departments_name;
            }
            $hr_emp_departments = implode(',', $hr_emp_departments);
        }

        $filename = 'employee-increment-list-' . Auth::user()->id . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Increment');

        $number = 0;
        $row = 1;
        exportHelper::getCustomCell($sheet, 1, 0, 'SR Chemical Industries LTD.', 12, null, 18, 'center');
        exportHelper::getCustomCell($sheet, 2, 0, 'Employee Increment List', 12, null, 16, 'center');
        exportHelper::getCustomCell($sheet, 3, 0, 'Filter By', 2, null, null, null, true);

        exportHelper::getCustomCell($sheet, 4, 0, 'Branch', 1, null, 12, null, true);
        exportHelper::getCustomCell($sheet, 4, 2, $branchs ? $branchs : 'N/A', 3);
        exportHelper::getCustomCell($sheet, 4, 7, 'Employee Category', 1, null, 12, null, true);
        exportHelper::getCustomCell($sheet, 4, 9, $categorys ? $categorys : 'N/A', 3);

        exportHelper::getCustomCell($sheet, 5, 0, 'Salary Grade', 1, null, 12, null, true);
        exportHelper::getCustomCell($sheet, 5, 2, $hr_emp_grades_list ? $hr_emp_grades_list : 'N/A', 3);
        exportHelper::getCustomCell($sheet, 5, 7, 'Department', 1, null, 12, null, true);
        exportHelper::getCustomCell($sheet, 5, 9, $hr_emp_departments ? $hr_emp_departments : 'N/A', 3);

        exportHelper::getCustomCell($sheet, 5, 0, 'Eligible Month', 1, null, 12, null, true);
        exportHelper::getCustomCell($sheet, 5, 2, $request->eligible_month ? $request->eligible_month : 'N/A', 3);

        $row = 7;
        $number = 0;
        exportHelper::getCustomCell($sheet, $row, 0, '', 3);
        exportHelper::getCustomCell($sheet, $row, 4, 'Current Salary', 5, null, null, 'center', true);
        exportHelper::getCustomCell($sheet, $row, 10, 'Increment Salary', 2, null, null, 'center', true);
        $row = 8;
        exportHelper::get_column_title($number, $row, array('Employee Name', 'Department', 'Designation', 'Last Increment Date', 'Basic', 'House Rent', 'Medical', 'Food', 'TA DA', 'Gross Total', 'Increment Ratio(%)', 'Increment Amount', 'Gross Total'), $sheet);
        $row = 9;
        $number = 0;

        $employeeList = self::selectedEmployeeData($request);

        foreach ($employeeList as $key => $item) {
            if ($request->increment_ratio == '') {
                if ($item->default_salary_applied == 1) {
                    $ratio = ($item->grade_yearly_increment / 100);
                } elseif ($item->default_salary_applied != 1 && $item->yearly_increment != '') {
                    $ratio = ($item->yearly_increment / 100);
                } else {
                    $ratio = ($item->grade_yearly_increment / 100);
                }
            } else {
                $ratio = ($request->increment_ratio / 100);
            }

            $number = 0;
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, $item->name);
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, $item->departments_name);
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, $item->designations_name);
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, toDated($item->applicable_date));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->basic_salary, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->house_rent_amount, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->min_medical, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->min_food, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->min_tada, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->min_gross, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, ($ratio * 100));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->basic_salary * $ratio, 2));
            $sheet->setCellValue(exportHelper::get_letter($number++) . $row, number_format($item->min_gross + ($item->basic_salary * $ratio)));
            $row++;
        }
        exportHelper::excelHeader($filename, $spreadsheet);
        return response()->json(['status' => 'success', 'file' => $filename]);
    }

    //Increment List PDF
    public function incrementListPDF(Request $request) {
        $categories = [];
        $branchs = [];
        $hr_emp_grades_list = [];
        $hr_emp_departments = [];
        $data['eligible_month'] = $request->eligible_month ? $request->eligible_month : 'N/A';
        $data['increment_ratio'] = $request->increment_ratio ? $request->increment_ratio : '';

        if ($request->hr_emp_category_id) {
            $cats = DB::table('hr_emp_categorys')
                    ->whereIn('hr_emp_categorys_id', $request->hr_emp_category_id)
                    ->get();
            foreach ($cats as $item) {
                $categories[] = $item->hr_emp_category_name;
            }
            $data['categories'] = implode(',', $categories);
        }

        if ($request->branchs_id) {
            $branch = DB::table('branchs')
                    ->whereIn('branchs_id', $request->branchs_id)
                    ->get();
            foreach ($branch as $item) {
                $branchs[] = $item->branchs_name;
            }
            $data['branchs'] = implode(',', $branchs);
        }

        if ($request->hr_emp_grades_list) {
            $grade = DB::table('hr_emp_grades')
                    ->whereIn('hr_emp_grades_id', $request->hr_emp_grades_list)
                    ->get();
            foreach ($grade as $item) {
                $hr_emp_grades_list[] = $item->hr_emp_grade_name;
            }
            $data['hr_emp_grades_list'] = implode(',', $hr_emp_grades_list);
        }

        if ($request->hr_emp_departments) {
            $dept = DB::table('departments')
                    ->whereIn('departments_id', $request->hr_emp_departments)
                    ->get();
            foreach ($dept as $item) {
                $hr_emp_departments[] = $item->departments_name;
            }
            $data['hr_emp_departments'] = implode(',', $hr_emp_departments);
        }
        $data['increment_ratio'] = $request->increment_ratio == null ? '' : $request->increment_ratio;
        $data['employeeList'] = self::selectedEmployeeData($request);
        $data['report_title'] = 'Employee Increment List';
        $data['filename'] = 'employee_increment_list_report';
        $data['orientation'] = "L";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view = 'HrIncrementPromotion.Increment.increment_list_pdf';
        PdfHelper::exportPdf($view, $data);
    }

   // Yearly Increment & Promotion sheet
    public function incrementPromotionReport(Request $request, $type = null) {
        $posts = $request->all();
        $data['posted'] = $posts;

        $status = !empty($request->status) ? $request->status : array('Active');
        $data['posted']['status'] = $status;

        $increment_type = !empty($request->increment_type) ? $request->increment_type : 'Yearly';
        $data['posted']['increment_type'] = $increment_type;

        $year = !empty($request->year) ? $request->year : date('Y');
        $data['posted']['year'] = $year;
        DB::connection()->enableQueryLog();
        $sql = DB::table('hr_employee_record_logs')
                ->select(
                    'sys_users.user_code as id_no', 'sys_users.name', 'designations.designations_name as designations', 'sys_users.date_of_join as doj', 'departments.departments_name as department', 'hr_emp_sections.hr_emp_section_name as section', 'hr_employee_record_logs.basic_salary as basic', 'hr_employee_record_logs.house_rent_amount as house_rant', 'hr_employee_record_logs.min_food as food_allounce', 'hr_employee_record_logs.min_medical as medical_allounce', 'hr_employee_record_logs.min_tada as transport_allounce', 'hr_employee_record_logs.gross_salary as gross_salary', 'hr_employee_record_logs.increment_amount as increment_amount', DB::raw("case when hr_employee_record_logs.status = 'Inactive' then hr_employee_record_logs.increment_amount else '' end AS proposed_increment_amount"), DB::raw("case when hr_employee_record_logs.status = 'Active' then hr_employee_record_logs.increment_amount else '' end AS approved_increment_amount"), 'hr_employee_record_logs.gross_salary as gross_salary_amount', DB::raw("case when hr_employee_record_logs.record_type = 'promotion' then designations.designations_name else '' end AS promotion"), DB::raw("CONCAT('') AS remarks")
                )
                ->where('sys_users.is_employee', 1)
                ->where('hr_employee_record_logs.increment_type', $increment_type)
                ->whereIn('sys_users.status', $status);

        $sql->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id')
                ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
                ->leftJoin('hr_emp_units', 'hr_employee_record_logs.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
                ->leftJoin('designations', 'hr_employee_record_logs.designations_id', '=', 'designations.designations_id')
                ->leftJoin('departments', 'hr_employee_record_logs.departments_id', '=', 'departments.departments_id')
                ->leftJoin('hr_emp_grades', 'hr_employee_record_logs.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
                ->leftJoin('hr_emp_sections', 'hr_employee_record_logs.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
                ->where('sys_users.is_employee', 1)
                ->whereYear('hr_employee_record_logs.applicable_date', $year);

        if (isset($posts['hr_emp_category']) && $posts['hr_emp_category'] != null) {
            $sql->where('sys_users.hr_emp_categorys_id', $posts['hr_emp_category']);
        }

        if (isset($posts['status']) && $posts['status'][0] != null) {
            $sql->whereIn('sys_users.status', $status);
        }

        if (isset($posts['branchs_id']) && $posts['branchs_id'][0] != null) {
            $sql->whereIn('hr_employee_record_logs.branchs_id', $posts['branchs_id']);
        }

        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0] != null) {
            $sql->whereIn('hr_employee_record_logs.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0] != null) {
            $sql->whereIn('hr_employee_record_logs.hr_emp_sections_id', $posts['hr_emp_sections_id']);
        }

        if (isset($posts['departments_id']) && $posts['departments_id'][0] != null) {
            $sql->whereIn('hr_employee_record_logs.departments_id', $posts['departments_id']);
        }

        if (isset($posts['designations_id']) && $posts['designations_id'][0] != null) {
            $sql->whereIn('hr_employee_record_logs.designations_id', $posts['designations_id']);
        }

        $sql->orderBy('hr_employee_record_logs.hr_employee_record_logs_id', 'DESC');

        if ($type == 'pdf' || $type == 'xlsx') {
            $report_data = $sql->get();
        } else {
            $report_data = $sql->paginate(15);
        }

        if (!empty($report_data)) {
            foreach ($report_data as $row) {
                $row->doj = toDated($row->doj);
            }
        }

        $data['report_data'] = $report_data;
        $data['complex_header'][] = array('text' => 'ID No.');
        $data['complex_header'][] = array('text' => 'Name');
        $data['complex_header'][] = array('text' => 'Designation');
        $data['complex_header'][] = array('text' => 'Date of Join');
        $data['complex_header'][] = array('text' => 'Department');
        $data['complex_header'][] = array('text' => 'Section');
        $data['complex_header'][] = array('text' => 'Basic');
        $data['complex_header'][] = array('text' => 'House Rant');
        $data['complex_header'][] = array('text' => 'Food allounce');
        $data['complex_header'][] = array('text' => 'Medical allounce');
        $data['complex_header'][] = array('text' => 'Transport allounce');
        $data['complex_header'][] = array('text' => 'Gross Salary');
        $data['complex_header'][] = array('text' => 'Increment Amount');
        $data['complex_header'][] = array('text' => 'Proposed Increment Amount');
        $data['complex_header'][] = array('text' => 'Approved Increment Amount');
        $data['complex_header'][] = array('text' => 'Gross Salary Amount');
        $data['complex_header'][] = array('text' => 'Proposed Promotion (If any)');
        $data['complex_header'][] = array('text' => 'Remarks');
        $data['table_header'] = array();

        if ($type == 'xlsx') {
            $data['filename'] = $increment_type . 'increment_promotion' . Auth::user()->id . '.xlsx';
            $data['sheetName'] = 'Increment Promotion(' . $year . ')';
            $data['title'] = $increment_type . ' Increment Promotion(' . $year . ')';
            $filename = self::excelReport($data);
            return response()->json(['status' => 'success', 'file' => $filename]);
        } elseif ($type == 'pdf') {
            $data['report_title'] = $increment_type . ' Increment & Promotion (' . $year . ')';
            $data['filename'] = $increment_type . '_increment_promotion_report';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures'] = ['Prepared by', 'Checked by', 'Approved by'];
            $view = 'HR.pdf_report_template';
            PdfHelper::exportPdf($view, $data);
        } else {
            $data['report_data_html'] = view('HR.report_template', $data);
            return view('HrIncrementPromotion.increment_promotion_report', $data);
        }
    }

    //Excel Export
    private function excelReport($data) {
        $filename = $data['filename'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($data['sheetName']);

        $header = array_map(function ($ar) {
            return !empty($ar['text']) ? $ar['text'] : '';
        }, $data['complex_header']);

        $number = 0;
        $row = 1;
        exportHelper::getCustomCell($sheet, 1, 0, 'SR Chemical Industries LTD.', count($header) - 1, null, 18, 'center');
        exportHelper::getCustomCell($sheet, 2, 0, $data['title'], count($header) - 1, null, 16, 'center');

        $row = 3;
        exportHelper::get_column_title($number, $row, $header, $sheet);

        $row = 4;
        if (isset($data['report_data'])) {
            foreach ($data['report_data'] as $item) {
                $number = 0;
                foreach ($item as $col => $val) {
                    $sheet->setCellValue(exportHelper::get_letter($number++) . $row, str_replace("<br>", ", ", $val));
                }
                $row++;
            }
        }

        exportHelper::excelHeader($filename, $spreadsheet);
        return $filename;
    }

}
