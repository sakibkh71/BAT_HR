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

class HrPromotionController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
     * Display all Promotion List
     */
    public function promotion(Request $request) {
        $post = $request->all();

        $data['hr_emp_grades'] = !empty($request->hr_emp_grades) ? array_filter($request->hr_emp_grades) : '';
        $data['designations'] = !empty($request->designations) ? array_filter($request->designations) : '';
        $data['salary_approval_status'] = !empty($request->salary_approval_status) ? $request->salary_approval_status : '';
        $data['date_range'] = !empty($request->date_range) ? $request->date_range : '';

        $employeeInfo = DB::table('hr_employee_record_logs')->select(
                'sys_users.id',
                'sys_users.name',
                'sys_users.user_code',
                'hr_emp_grades.hr_emp_grade_name',
                'designations.designations_name',
                'c.name as creator_name',
                'sys_status_flows.status_flows_name',
                'sys_delegation_conf.step_name',
                'b.name as delegation_person_name',
                'hr_employee_record_logs.*',
                'bd.name as point_name',
                'new_designation.designations_name as new_designations_name',
                'new_grades.hr_emp_grade_name as new_hr_emp_grade_name'
            )
            ->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id')
            ->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id')
            ->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id')

            ->leftJoin('hr_emp_grades as new_grades', 'new_grades.hr_emp_grades_id', '=', 'hr_employee_record_logs.hr_emp_grades_id')
            ->leftJoin('designations as new_designation', 'new_designation.designations_id', '=', 'hr_employee_record_logs.designations_id')

            ->leftJoin('sys_users as b', 'b.id', '=', 'hr_employee_record_logs.delegation_person')
            ->leftJoin('bat_distributorspoint as bd', 'bd.id', '=', 'hr_employee_record_logs.bat_dpid')
            ->leftJoin('sys_users as c', 'c.id', '=', 'hr_employee_record_logs.created_by')
            ->join('sys_status_flows', 'sys_status_flows.status_flows_id', '=', 'hr_employee_record_logs.hr_log_status')
            ->leftJoin('sys_delegation_conf', function ($join) {
                $join->on('hr_employee_record_logs.delegation_for', '=', 'sys_delegation_conf.delegation_for')
                    ->on('hr_employee_record_logs.delegation_ref_event_id', '=', 'sys_delegation_conf.ref_event_id')
                    ->on('hr_employee_record_logs.delegation_version', '=', 'sys_delegation_conf.delegation_version')
                    ->on('hr_employee_record_logs.delegation_step', '=', 'sys_delegation_conf.step_number');
            })
            ->where('hr_employee_record_logs.record_type', '=', 'promotion')
            ->where('hr_employee_record_logs.status', '=', 'Active')
            ->where('hr_employee_record_logs.created_by', '=', Auth::id());

        if (!empty($data['date_range'])) {
            $range = explode(" - ", $data['date_range']);
            $employeeInfo->whereBetween('hr_employee_record_logs.applicable_date', $range);
        }

        if (!empty($data['salary_approval_status'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_log_status', $data['salary_approval_status']);
        }

        if(!empty($data['designations'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.designations_id', $data['designations']);
        }

        if (!empty($data['hr_emp_grades'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_emp_grades_id', $data['hr_emp_grades']);
        }

        $employeeInfo->orderBy('hr_employee_record_logs.hr_employee_record_logs_id', 'DESC');

        $data['employeeList'] = $employeeInfo->get();

        return view('HrIncrementPromotion.Promotion.promotionList', $data);
    }


    /*
     * New Promotion Entry
     */
    public function promotionForm(Request $request) {
        $data['log_id'] = $request->log_id;
       // dd($data['log_id']);
        return view('HrIncrementPromotion.Promotion.promotion', $data);
    }

    /*
     * Return Employees for json data
     */
    public function promotionEmployees(Request $request) {
        $data['employeeInfo'] = self::selectedEmployeeData($request);

        $data['post_data'] = $request->all();
        $emp_item = view('HrIncrementPromotion.Promotion.hr_get_selected_promotion_emp', $data)->render();
        return Response::json(['emp_list' => $emp_item]);
    }


    //Get Gross Salary by Grade
    public function grossbyGrade(Request $request){
        $row = DB::table('hr_emp_grades')->where('hr_emp_grades_id', $request->grade_id)->first();
        return response()->json([
            'gross' => $row->gross_salary,
        ]);
    }

    //Grade wise salary information
    function gradeWiseSalary(Request $request, $id){
        $row = DB::table('hr_emp_grades')->where('hr_emp_grades_id', $id)->first();
        return response()->json([
            'gross' => $row,
        ]);
    }


    /*
     * Store Promotion Data
     */
    function storePromotion(Request $request) {
        $emp_id = $request->emp_id;
        $designation_id = $request->designation_id;
        $emp_point_id = $request->emp_point_id;

        $new_gross_salary = $request->new_gross_salary;
        $designations_array = DB::table('designations')->where('status','Active')->get();
        $designationWiseGradeArray = array();
        foreach ($designations_array as $des){
            $designationWiseGradeArray[$des->designations_id]= $des->hr_emp_grade_id;
        }

        $data_arr_log = [];
        if (sizeof($request->emp_id) > 0) {
            foreach ($request->emp_id as $i => $item) {

                $user_info = DB::table('sys_users')->where('id', $emp_id[$i])->first();
                $gross_salary = str_replace(',', '', $new_gross_salary[$i]);

                if($gross_salary == $user_info->min_gross ){
                    $increment_amount = 0;
                    $basic_salary = $user_info->basic_salary;
                    $salary_cal = 0;
                }else{
                    $increment_amount = floatval($gross_salary) - floatval($user_info->min_gross);
                    $salary_cal = floatval($increment_amount / $user_info->min_gross);
                    $basic_salary = $user_info->basic_salary+($user_info->basic_salary*$salary_cal);
                }
                $grade_id = $designationWiseGradeArray[$designation_id[$i]];
              //  dd($grade_id);
                $user_info_arr = array(
                    'sys_users_id' => $user_info->id,
                    'record_type' => 'promotion',
                    'designations_id' => $designation_id[$i],
                    'previous_designations_id' =>$user_info->designations_id,
                    'hr_emp_grades_id' => $grade_id,
                    'previous_grades_id' => $user_info->hr_emp_grades_id,
                    'bat_company_id' => $user_info->bat_company_id,
                    'bat_dpid' => (int)$emp_point_id[$i],
                    'applicable_date' => $request->applicable_date,
                    'basic_salary' => $basic_salary,
                    'increment_amount' => $increment_amount,
                    'gross_salary' => $gross_salary,
                    'previous_gross' => $user_info->min_gross,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d h:i:s'),
                    'hr_log_status' => 48,
                );

             //   dd($user_info_arr) ;

                $insert = DB::table('hr_employee_record_logs')->insert($user_info_arr);
                $record_log_last_id = DB::getPdo()->lastInsertId();

                if($gross_salary != $user_info->min_gross ) {
                    $salary_info_array = salary_calculation_arr($user_info->id, $grade_id, $record_log_last_id, $salary_cal);
                    $inserts = DB::table('hr_emp_salary_components')->insert($salary_info_array);
                }
            }

            if ($insert) {
                return response()->json(array('success' => true));
            } else {
                return response()->json(array('success' => false));
            }
        }
        return response()->json(array('success' => false));
    }


    /*
     * Edit Promotion Record
     */
    function editPromotionRecord(Request $request) {
        $sql = DB::table('hr_employee_record_logs')->select(
            'hr_employee_record_logs.hr_employee_record_logs_id',
            'sys_users.id',
            'sys_users.name',
            'sys_users.user_code',
            'sys_users.date_of_join',
            'designations.designations_name',
            'hr_emp_grades.hr_emp_grade_name',
            'sys_users.designations_id',
            'sys_users.hr_emp_grades_id',
            'hr_employee_record_logs.designations_id as log_designations_id',
            'hr_employee_record_logs.hr_emp_grades_id as log_hr_emp_grades_id',
            'hr_employee_record_logs.bat_dpid as log_bat_dpid',
            'bat_distributorspoint.name as point_name',
            'sys_users.basic_salary',
            'sys_users.min_gross',
            'hr_employee_record_logs.previous_gross',
            'hr_employee_record_logs.basic_salary as log_basic_salary',
            'hr_employee_record_logs.gross_salary',
            'hr_employee_record_logs.increment_amount',
            'sys_users.default_salary_applied',
            'hr_employee_record_logs.applicable_date'
        )
            ->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id')
            ->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id')
            ->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id')
            ->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'sys_users.bat_dpid')
            ->whereIn('hr_employee_record_logs.hr_employee_record_logs_id', explode(',', $request->log_id))
            ->orderBy('hr_employee_record_logs_id', 'DESC');

        $data['employeeInfo']= $sql->get();

        $existing_selected_emp = [];
        foreach ($data['employeeInfo'] as $key => $item) {
            array_push($existing_selected_emp, $item->id);
        }
        $existing_selected_emp = implode(',', $existing_selected_emp);

        $emp_item = view('HrIncrementPromotion.Promotion.hr_get_selected_promotion_emp', $data)->render();


        return Response::json([
            'emp_list' => $emp_item,
            'existing_selected_emp' => $existing_selected_emp
        ]);
    }


    /*
     * Update Promotion Item
     */
    function updatePromotion(Request $request) {

        $new_gross_salary = $request->new_gross_salary;
        $new_increment_amount = $request->increment_amount;
        $new_designation_id = $request->designation_id;
        //$new_emp_grade_id = $request->emp_grade_id;
        $new_emp_dpid= $request->emp_point_id;

        $designations_array = DB::table('designations')->where('status','Active')->get();
        $designationWiseGradeArray = array();
        foreach ($designations_array as $des){
            $designationWiseGradeArray[$des->designations_id]= $des->hr_emp_grade_id;
        }
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

                if($gross_salary == $log_info->min_gross ){
                    $increment_amount = 0;
                    $basic_salary = $log_info->basic_salary;
                    $salary_cal = 0;
                }else{
                    $increment_amount = floatval($gross_salary) - floatval($log_info->min_gross);
                    $salary_cal =  floatval($increment_amount / $log_info->min_gross);
                    $basic_salary = floatval( $log_info->basic_salary+($log_info->basic_salary*$salary_cal));
                }
                $grade_id = $designationWiseGradeArray[$new_designation_id[$i]];
                $user_info_arr = array(
                    'record_type' => 'promotion',
                    'designations_id' => $new_designation_id[$i],
                    'hr_emp_grades_id' => $grade_id,
                    'previous_designations_id' => $log_info->previous_designations_id,
                    'previous_grades_id' => $log_info->previous_grades_id,
                    'bat_company_id' => $log_info->bat_company_id,
                    'bat_dpid' => $new_emp_dpid[$i],
                    'applicable_date' => $request->applicable_date,
                    'basic_salary' => $basic_salary,
                    'increment_amount' => $increment_amount,
                    'gross_salary' => $new_gross_salary[$i],
                    'previous_gross' => $log_info->min_gross,
                    'updated_by' => Auth::id(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'hr_log_status' => 48,
                );

                $update_record = DB::table('hr_employee_record_logs')->where('hr_employee_record_logs_id', $item)->update($user_info_arr);

                DB::table('hr_emp_salary_components')->where('record_type', 'employee_record')->where('record_ref', $item)->delete();
                if($gross_salary != $log_info->min_gross ) {
                    $salary_info_array = salary_calculation_arr($log_info->sys_users_id, $grade_id, $item, $salary_cal);
                    $update_component = DB::table('hr_emp_salary_components')->insert($salary_info_array);
                }
            }

            if ($update_record) {
                return response()->json(array('success' => true));
            } else {
                return response()->json(array('success' => false));
            }
        }
        return response()->json(array('success' => false));
    }

    /*
    * Destroy Hr Record
    */
    function deletePromotionRecord(Request $request) {
        $arr = array(
            'status' => 'Inactive',
            'updated_at' => date("Y-m-d h:i:s"),
            'updated_by' => Auth::id()
        );
        $update = DB::table('hr_employee_record_logs')->whereIn('hr_employee_record_logs_id', $request->log_id)->update($arr);
        if ($update) {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }


    /*
     * Promotion Letter
     */
    function promotionLetter($log_id) {
        $employeeInfo = DB::table('hr_employee_record_logs')->select(
                'hr_employee_record_logs.*',
                'sys_users.id',
                'sys_users.name',
                'sys_users.user_code',
                'hr_emp_grades.hr_emp_grade_name',
                'designations.designations_name',
                'bat_company.company_name',
                'previous_grades.hr_emp_grade_name as old_grade_name',
                'previous_designations.designations_name as old_designation_name'
            )
            ->join('sys_users', 'sys_users.id', '=', 'hr_employee_record_logs.sys_users_id')
            ->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'hr_employee_record_logs.bat_company_id')

            ->leftJoin('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_employee_record_logs.hr_emp_grades_id')
            ->leftJoin('designations', 'designations.designations_id', '=', 'hr_employee_record_logs.designations_id')

            ->leftJoin('hr_emp_grades as previous_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'hr_employee_record_logs.previous_grades_id')
            ->leftJoin('designations as previous_designations', 'designations.designations_id', '=', 'hr_employee_record_logs.previous_designations_id')

            ->where('hr_employee_record_logs.record_type', '=', 'promotion')
            ->where('hr_employee_record_logs.hr_employee_record_logs_id', $log_id)->first();

       // SELECT MAX(salary) FROM Employee WHERE Salary NOT IN ( SELECT Max(Salary) FROM Employee);

        $data['emp_log'] = $employeeInfo;
        $data['report_title'] = 'Promotion Letter';
        $data['filename'] = 'increment_letter';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view = 'HrIncrementPromotion.Promotion.promotionLetterView';

        PdfHelper::exportPdf($view, $data);
    }


    //Promotion List PDF
    public function promotionListPDF(Request $request) {
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


    /*
     * Yearly Increment & Promotion sheet
     */
    public function PromotionReport(Request $request, $type = null) {
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


    /*
     * Get Selected Employee Data
     */
    public static function selectedEmployeeData($request) {
        $remove_emp = trim($request->remove_emp, ',');
        $sql = DB::table('sys_users')->select(
                'sys_users.id',
                'sys_users.name',
                'sys_users.date_of_join',
                'sys_users.applicable_date',
                'sys_users.yearly_increment',
                'sys_users.user_code',
                'sys_users.basic_salary',
                'sys_users.min_gross',
                'sys_users.default_salary_applied',
                'sys_users.hr_emp_grades_id',
                'sys_users.designations_id',
                'hr_emp_grades.yearly_increment as grade_yearly_increment',
                'hr_emp_grades.hr_emp_grade_name',
                'designations.designations_name',
                'bat_distributorspoint.id as bat_dpid',
                'bat_distributorspoint.name as point_name'
            )
            ->join('hr_emp_grades', 'hr_emp_grades.hr_emp_grades_id', '=', 'sys_users.hr_emp_grades_id')
            ->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id')
            ->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'sys_users.bat_dpid')
            ->where('sys_users.is_employee', '1')
            ->where('sys_users.status', 'Active');

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


        $session_con = (sessionFilter('url','hr-get-selected-promotion-emp'));
        $session_con = trim(trim(strtolower($session_con)),'and');

        if (!empty($session_con)){ $sql->whereRaw($session_con); }

        $employeeInfo = $sql->get();

        return $employeeInfo;
    }

}
