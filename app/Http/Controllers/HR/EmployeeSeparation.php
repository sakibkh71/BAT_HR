<?php

namespace App\Http\Controllers\HR;

use App\Events\AuditTrailEvent;
use App\Events\NotifyEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;
use App\Helpers\PdfHelper;

class EmployeeSeparation extends Controller {

    public $data = [];

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function separationFormShow(Request $request){

        $data['user_id'] = '';
        $data['list'] = '';
        $data['date_from'] = '';
        $data['date_to'] = '';

//        if (!empty($_POST)){
////
//            $dateStart = $request->date_from;
//            $dateEnd = $request->date_to;
//            $yearStart = date("Y", strtotime($dateStart));
//            $yearEnd = date("Y", strtotime($dateEnd));
//
//            $val = DB::table('pe_evaluate_employees')
//                ->select('pe_evaluate_employees.pe_evaluate_employees_id','pe_evaluate_employees.year', 'pe_evaluate_employees.achievement','pe_evaluate_employees.created_at',
//                    'sys_users.name as user_name', 'sy_user.name as evaluate_by', 'designations.designations_name')
//                ->join('sys_users', 'sys_users.id', 'pe_evaluate_employees.sys_users_id')
//                ->join('designations', 'designations.designations_id', 'sys_users.designations_id')
//                ->join('sys_users as sy_user', 'sy_user.id', 'pe_evaluate_employees.evaluate_by')
//                ->where('pe_evaluate_employees.sys_users_id', $request->user_id)
//                ->whereBetween('pe_evaluate_employees.created_at', [$dateStart." 00:00:00",$dateEnd." 23:59:59"])
//                ->whereBetween('pe_evaluate_employees.year', [$yearStart,$yearEnd])
//                ->get();
//            $data['list'] = $val;
//            $data['date_from'] = $dateStart;
//            $data['date_to'] = $dateEnd;
//            $data['user_id'] = $request->user_id;
//        }

        $qry = DB::table('sys_users')->whereIn('status', ['Active', 'Probation'])->where('is_employee', 1);
        $session_con = (sessionFilter('url','pe-user-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qry->whereRaw($session_con);
        }

        $data['users'] = $qry->pluck('name', 'id');
//        dd($data['users']);

        return view('HR.separation.emp_separation', $data);
    }

    function getSeparationList() {
        return view('HR.separation.emp_separation_list');
    }

    public function separationUndo($separation_id, $emp_id){

        DB::beginTransaction();
        try{
            $separation_info = DB::table('hr_emp_separation')->where('hr_emp_separation_id', $separation_id)->first();
            $update = DB::table('hr_emp_separation')
            ->where('hr_emp_separation_id', $separation_id)->update(['status'=>'Inactive']);
//            AuditTrailEvent::updateForAudit($update,['status'=>'Inactive']);

            DB::commit();
            $data['status'] = 200;

        }catch (\Exception $e) {

            DB::rollback();
            $data['satatus'] = 500;
        }

        return $data;
    }

    public function getSeparationForm(Request $request,$sys_users_id=null, $separation_date=null, $separation_id = null) {

        if (!empty($_POST)){
//            dd($request->all());
            $sys_users_id = $request->user_id;
            $separation_date = $request->date_from;
        }

        // Shibly Code:
        $leaveInfo = DB::table('hr_yearly_leave_balances');
        $leaveInfo->select(DB::raw("SUM(hr_yearly_leave_balances.balance_leaves) as leave_balance"));
        $leaveInfo->where('sys_users_id', $sys_users_id);
        $leaveInfo->where('is_earn_leave', '=', 1);
        $leave=$leaveInfo->value('leave_balance');
        $data['leave_info']=$leave;
        // Shibly Code End:

        $data['separation_date'] = $separation_date;
        $sheetHead = DB::table('hr_emp_salary_components')
            ->where('record_type', 'default')
            ->where('auto_applicable', 'YES')
            ->where('record_type', 'default')
            ->where('sys_users_id',$sys_users_id)
            ->groupBy('component_slug');
        $data['salary_component'] = $sheetHead->get();
        $data['salary_info'] = self::separatedSalary($sys_users_id, $separation_date);
        $data['chk_in_salary_wages'] = "";

        if(empty($data['salary_info']->basic_salary)){
            $separation_month = date('Y-m', strtotime($separation_date));
            //check already exist in salary sheet
            $chk_in_salary_sheet = DB::table('hr_monthly_salary_wages')
                                        ->where('sys_users_id', $sys_users_id)
                                        ->where('hr_salary_month_name', $separation_month)
                                        ->first();

            if(!empty($chk_in_salary_sheet)){
                $data['chk_in_salary_wages'] = "This employee already available in '".date('F, Y', strtotime($separation_date))."' salary sheet.";
            }
        }

        if ($separation_id) {
            $data['separation_info'] = DB::table('hr_emp_separation')->where('hr_emp_separation_id', $separation_id)->first();
        }

        $data['employee'] = DB::table('sys_users')->where('id', $sys_users_id)->first();
        $pf = DB::table('hr_monthly_salary_wages');
        $pf->selectRaw('sum(pf_amount_company) as pf_amount_company,sum(pf_amount_employee) as pf_amount_employee');
        $pf->where('sys_users_id', $sys_users_id);

        $data['pf_salary'] = $pf->first();
        $data['emp_info'] = employeeInfo($sys_users_id);
        return view('HR.separation.emp_separation_form', $data);
    }

    private function separatedSalary($sys_users_id, $separation_date) {

        $joining_date = DB::table('sys_users')->where('id',$sys_users_id)->first()->date_of_join;
        $current_month_joining = date('Y-m',strtotime($joining_date));
        $last_day = date('t');
        $sp_days = date('d', strtotime($separation_date));

        if($current_month_joining == date("Y-m",strtotime($separation_date))){
            $join_day = date('d',strtotime($joining_date));
            $days_ratio = (($sp_days-$join_day) / $last_day);
        }else{
            $days_ratio = $sp_days / $last_day;
        }



        $month = date('Y-m', strtotime($separation_date));
        $q = DB::table('sys_users')
                ->selectRaw("
                    sys_users.basic_salary*$days_ratio as basic_salary,
                    $days_ratio as days_ratio,
                    sys_users.min_gross*$days_ratio as gross,
                    ifnull(sys_users.pf_amount_employee,0)*$days_ratio as pf_amount_employee,
                    ifnull(sys_users.pf_amount_company,0)*$days_ratio as pf_amount_company,
                    ifnull(sys_users.gf_amount,0)*$days_ratio as gf_amount,
                    ifnull(sys_users.insurance_amount,0) as insurance_amount,
                    ifnull(func_get_variable_salary(sys_users.user_code,'$month'),0) as pfp_achievement,
                    IFNULL(COALESCE((select sum(variable_salary_amount) from hr_emp_monthly_variable_salary where sys_users_id=sys_users.id and vsalary_month='$month' AND hr_emp_monthly_variable_salary.status='Active'),max_variable_salary),0) as target_variable_salary,
                    IFNULL((select sum(due_amount) from hr_emp_loan where sys_users_id=sys_users.id and hr_emp_loan.status='Active'),0) as due_loan_amount,
                    IFNULL((select sum(conveyance_amount) from hr_other_conveyances where sys_users_id=sys_users.id),0) as other_conveyance,
                    substr(hr_emp_attendance.day_is,1,7) AS `hr_salary_month_name`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'R') then 1 else 0 end)) AS `number_of_working_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in ('P','HP','WP','L','EO')) then 1 else 0 end)) AS `present_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'A') then 1 else 0 end)) AS `absent_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'Lv') then 1 else 0 end)) AS `number_of_leave`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'W') then 1 else 0 end)) AS `number_of_weekend`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'H') then 1 else 0 end)) AS `number_of_holidays`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in('L','EO')) then 1 else 0 end)) AS `late_days`");
//        if (!empty($data['salary_component'])) {
//            foreach ($data['salary_component'] as $component) {
//                $q->selectRaw("func_get_salary_component(sys_users.id,'$month','$component->component_slug')*$days_ratio as $component->component_slug");
//            }
//        }
        $q->LeftJoin('hr_emp_attendance', 'sys_users.id', 'hr_emp_attendance.sys_users_id');
        $q->LeftJoin('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id');
        $q->whereRaw("sys_users.id NOT IN(SELECT sys_users_id FROM hr_monthly_salary_wages WHERE hr_salary_month_name='$month')
                    AND substr(hr_emp_attendance.day_is,1,7)='$month'
                    AND sys_users.is_employee=1
                    AND sys_users.id=$sys_users_id");
        $q->where("hr_emp_attendance.day_is", '<=', $separation_date);

        $salaryInfo = $q->first();

        return $salaryInfo;
    }

    function separationSubmit(Request $request) {

        $post_data = $request->except(['_token','hr_emp_separation_id','total_salary_structure','loan_deduction']);

        $employee_info = DB::table('sys_users')->where('id', $request->sys_users_id)->whereIn('status', ['Active', 'Probation'])->first();

        if ($employee_info || $request->hr_emp_separation_id) {

            $post_data['earned_salary'] = ($request->fixed_salary + $request->pfp_salary + $request->pf_total_employee + $request->pf_total_company + $request->other_addition);

            $post_data['net_payable'] = $request->net_payable;

            if ($request->hr_emp_separation_id) {
                $post_data['updated_by'] = Auth::id();
                $post_data['updated_at'] = dTime();
                $update = DB::table('hr_emp_separation')->where('hr_emp_separation_id', $request->hr_emp_separation_id)->update($post_data);

//                AuditTrailEvent::updateForAudit($update,$post_data);

            } else {
                $post_data['bat_dpid'] = $employee_info->bat_dpid;
                $post_data['created_by'] = Auth::id();
                $post_data['created_at'] = dTime();
                $post_data['separation_status'] = 102;
                DB::table('hr_emp_separation')->insert($post_data);
            }

            /*$update_data = array(
                'hr_separation_causes' => $request->hr_separation_causes,
                'separation_date' => $request->separation_date,
                'status' => 'Separated',
            );

            $update = DB::table('sys_users')->where('id', $request->sys_users_id);

            AuditTrailEvent::updateForAudit($update,$update_data);*/

            return redirect('/emp-leaver-list')->with('success', 'Success! Employee Leaver Process Complete.');

        } else {

            return redirect()->back()->with('error', 'Failed! Please Try again');

        }
    }

    function getSeparationSettlement($separation_id) {



        $q = DB::table('hr_emp_separation')
                ->select('designations.designations_name', 'hr_emp_separation.sys_users_id',
                        'hr_emp_separation.pf_total_employee', 'hr_emp_separation.pf_total_company',
                        'hr_emp_separation.advance_deduction', 'hr_emp_separation.other_addition',
                        'hr_emp_separation.other_deduction','hr_emp_separation.absent_deduction','hr_emp_separation.encashment_amount',
                        'sys_users.name', 'sys_users.date_of_join', 'sys_users.date_of_confirmation', 'hr_emp_separation.separation_date',
                        'sys_users.basic_salary', 'bat_distributorspoint.name as point_name', 'sys_users.user_code','hr_emp_separation.hr_separation_causes')
                ->join('sys_users', 'sys_users.id', 'hr_emp_separation.sys_users_id')
                ->join('designations', 'sys_users.designations_id', 'designations.designations_id')
                ->join('bat_distributorspoint', 'sys_users.bat_dpid', 'bat_distributorspoint.id')
                ->where('hr_emp_separation_id', $separation_id);
        $data['row'] = $q->first();

        $sheetHead = DB::table('hr_emp_salary_components')
            ->where('record_type', 'default')
            ->where('auto_applicable', 'YES')
            ->where('sys_users_id', $data['row']->sys_users_id)
            ->groupBy('component_slug');
        $data['salary_component'] = $sheetHead->get();

//        $sql2 = DB::table('hr_emp_salary_components')
//                ->where('sys_users_id', '=', $data['row']->sys_users_id)
//                ->where('record_type', '=', 'default');
//        $salary_components = $sql2->get();
//        if (!empty($salary_components)) {
//            $component_addition = $component_deduction = $component_variable = [];
//            foreach ($salary_components as $component) {
//                $component_item = (array) $component;
//                if ($component->component_type == 'Variable') {
//                    $component_variable[] = $component_item;
//                } elseif ($component->component_type == 'Deduction') {
//                    $component_deduction[] = $component_item;
//                } else {
//                    $component_addition[] = $component_item;
//                }
//            }
//        }
//        $data['salary_components_addition'] = $component_addition;
//        $data['salary_components_deduction'] = $component_deduction;
//        $data['salary_components_variable'] = $component_variable;
//        dd($data);
        $data['salary_info'] = self::separatedSalary($data['row']->sys_users_id, $data['row']->separation_date);

        $data['report_title'] = 'Release Final Settlement';
        $data['filename'] = 'Settlement';
        $data['orientation'] = "P";
        $view = 'HR.separation.separation_settlement_pdf';
        PdfHelper::exportPdf($view, $data);
    }

    function separationConfirm(Request $request) {
        $confirm_ids = $request->hr_emp_separation_id;
        $update_arr = array(
            'is_confirm' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => dTime(),
        );
        $update = DB::table('hr_emp_separation')->whereIn('hr_emp_separation_id', $confirm_ids)->update($update_arr);
//        AuditTrailEvent::updateForAudit($update,$update_arr);
        return response()->json([
                    'success' => true,
        ]);
    }

    public function checkSeparated(Request $request){
       $row = DB::table('hr_emp_separation')->where('sys_users_id', $request->emp_id)->where('status', '=', 'Active')->first();
        if ($row){
            return response()->json([
                'exist' => 'yes',
            ]);
        }else{
            return response()->json([
                'exist' => 'no',
            ]);
        }
    }


    //Go for delegation process
    public function separationDelegationProcess(Request $request) {
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
    public function separationApproveList() {
        $slug = 'hr_sep';
        $data['columns'] = array(
            'hr_emp_separation.hr_emp_separation_id',
            'hr_emp_separation.hr_separation_causes',
            'hr_emp_separation.separation_date',
            'hr_emp_separation.fixed_salary',
            'hr_emp_separation.net_payable',
            'sys_users.name',
            'separation_status',
            'hr_emp_separation.created_by',
            'hr_emp_separation.created_at'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'], array('sys_users'));

        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_emp_separation_id;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;

        return view('HR.separation.approval_list', $data);
    }




    //Approved form deligation
    public function separationBulkApproved(Request $request) {
        $codes = $request->codes;

        $comments = 'Separation Bulk Approved';
        $request->merge([
            'slug' => 'hr_sep',
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
                        if($item['status_id'] == 104){
                            $sep_info = DB::table('hr_emp_separation')->where('hr_emp_separation_id', '=', $code)->first();
                            $update_data = array(
                                'hr_separation_causes' => $sep_info->hr_separation_causes,
                                'separation_date' => $sep_info->separation_date,
                                'status' => 'Separated',
                            );


                            DB::table('hr_emp_separation')->where('hr_emp_separation_id', '=', $code)->update(['is_confirm'=>1]);
                            $update = DB::table('sys_users')->where('id', $sep_info->sys_users_id)->update($update_data);
                            DB::table('hr_emp_attendance')->where('sys_users_id',$sep_info->sys_users_id)
                                ->where('day_is','>',$sep_info->separation_date)->update(['attn_status'=>'Inactive']);

                            $seperated_user = DB::table('sys_users')->where('id',$sep_info->sys_users_id)->first();
                            $notify_to = DB::table('sys_users')->where('bat_company_id',$seperated_user->bat_company_id)->where('bat_dpid',$seperated_user->bat_dpid)
                                ->where('username','!=',null)->where('designations_id',177)->get();
                         //   dd($notify_to);
                            foreach ($notify_to as $notify){
                                $noti_arr = [
                                    'generated_from'=> 'Person',
                                    'generated_source'=> session()->get('USER_ID'),
                                    'notify_to'=>$notify->id, // jar kache notification jabe
                                    'notification_title'=>'Employee Separated',
                                    'event_for'=> 'sep_noti', // event slug / id_logic_slug / approval event slug
                                    'event_id'=> '',
                                    'content'=> 'You have an employee Separated. You can appoint new employee',
                                    'url_ref'=> '#', // Approval module redirect url
                                    'created_at'=> currentDate(),
                                    'priority'=> 3
                                ];
                                $id = DB::table('sys_notifys')->insertGetId($noti_arr);
                                event(new NotifyEvent($id),'dd');
                            }



//                            AuditTrailEvent::updateForAudit($update,$update_data);
                        }
                    } else {
                        $fail_msg[$code] = $prc_item['msg'] . ' for ' . $code;
                    }
                }
            }
            return response()->json(['sucs_msg' => $sucs_msg, 'fail_msg' => $fail_msg]);
        } else {
            return 'There is no return from delegation';
        }
    }
}
