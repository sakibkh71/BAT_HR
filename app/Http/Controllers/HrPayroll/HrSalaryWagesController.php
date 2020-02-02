<?php

namespace App\Http\Controllers\HrPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;
//pdf library
use App\Helpers\PdfHelper;
use exportHelper;

class HrSalaryWagesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    function monthlySalarySheet(){
        return view('Hr_payroll.salary_wages.salary_sheet');
    }
    function monthlySalarySheetCreate($hr_emp_salary_sheet_code=null){
        $data = [];
        if($hr_emp_salary_sheet_code){
            $data['sheet_info'] = DB::table('hr_emp_salary_sheet')
                ->select('hr_emp_salary_sheet.*')
                ->selectRaw('group_concat(hr_emp_salary_sheet.bat_dpid) as bat_dpid')
                ->where('hr_emp_salary_sheet_code',$hr_emp_salary_sheet_code)->get()->first();
        }
        return view('Hr_payroll.salary_wages.salary_sheet_create',$data);
    }

    function salarySheetBankAdviceGenerate($sheet_code,$type=''){
        $data=array();
        if($type=='') {
            $data['salary_sheet'] = DB::table('hr_emp_salary_sheet')
                ->selectRaw('hr_emp_salary_sheet_code ,group_concat(hr_emp_salary_sheet_id) as hr_emp_salary_sheet_id,salary_month,salary_sheet_type,
            (select group_concat(designations_name separator ", ") from designations where find_in_set(designations_id,selected_designations)) as selected_designations,
            group_concat(bat_distributorspoint.name) as distributor_points')
                ->join('bat_distributorspoint', 'bat_distributorspoint.id', 'hr_emp_salary_sheet.bat_dpid')
                ->where('hr_emp_salary_sheet_code', $sheet_code)->get()->first();

            $sheet_ids = $data['salary_sheet']->hr_emp_salary_sheet_id;
            if ($data['salary_sheet']->salary_sheet_type == 'PFP') {
                $sql = DB::table('hr_emp_pfp_salary')
                    ->selectRaw('sum(pfp_earn_amount) as net_salary,count(*) as total_employee,designations_name')
                    ->join('designations', 'designations.designations_id', '=', 'hr_emp_pfp_salary.designations_id')
                    ->whereRaw("FIND_IN_SET(hr_emp_salary_sheet_id,'$sheet_ids')")
                    ->groupBy('hr_emp_pfp_salary.designations_id');
                $data['employeeList'] = $sql->get();
            } else {
                $sql = DB::table('hr_monthly_salary_wages')
                    ->selectRaw('sum(net_payable) as net_salary,count(*) as total_employee,designations_name')
                    ->join('designations', 'designations.designations_id', '=', 'hr_monthly_salary_wages.designations_id')
                    ->whereRaw("FIND_IN_SET(hr_emp_salary_sheet_id,'$sheet_ids')")
                    ->groupBy('hr_monthly_salary_wages.designations_id');
                $data['employeeList'] = $sql->get();

            }
            $data['type']='salary';
        }else if($type=='bonus'){
            $data['bonus_sheet']=DB::table('hr_emp_bonus_sheet')
                ->selectRaw('bonus_sheet_code , group_concat(hr_emp_bonus_sheet_id) as hr_emp_bonus_sheet_id, bonus_sheet_name,bonus_type,
                    (select group_concat(designations_name separator ", ") from designations where find_in_set(designations_id,selected_designations)) as selected_designations,
                     group_concat(bat_distributorspoint.name) as distributor_points')
                ->join('bat_distributorspoint', 'bat_distributorspoint.id', 'hr_emp_bonus_sheet.bat_dpid')
                ->where('bonus_sheet_code', $sheet_code)->get()->first();
            $sheet_ids=$data['bonus_sheet']->hr_emp_bonus_sheet_id;
            $sql=DB::table('hr_emp_bonus')
                ->selectRaw('sum(payable_bonus) as net_bonus,count(*) as total_employee, designations_name')
                ->join('designations', 'designations.designations_id', '=', 'hr_emp_bonus.designations_id')
                ->whereRaw("FIND_IN_SET(hr_emp_bonus_sheet_id,'$sheet_ids')")
                ->groupBy('hr_emp_bonus.designations_id');
            $data['employeeList'] = $sql->get();
            $data['type']='bonus';

        }

        return view('Hr_payroll.salary_wages.create_bank_advice',$data);
    }


    function salarySheetBankAdviceSave(Request $request){
        if($request->operation_type == 'salary') {
            $data = $request->except(['_token', 'hr_emp_salary_sheet_id','operation_type']);
            $exists = DB::table('hr_bank_advices')->where('bank_advice_ref', $request->bank_advice_ref)->get()->first();
            if (!empty($exists)) {
                return response()->json([
                    'success' => 'Bank Advice Already Created.',
                ]);
                die();
            }
            DB::table('hr_emp_salary_sheet')->whereIn("hr_emp_salary_sheet_id", [$request->hr_emp_salary_sheet_id])->update(
                ['bank_advice' => 1, 'salary_sheet_status' => 96]
            );
            $data = array_merge($data, array(
                'bank_advice_type' => 'Salary',
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
        else if($request->operation_type == 'bonus'){
            $data=$request->except(['_token', 'hr_emp_bonus_sheet_id','operation_type']);
            $exists = DB::table('hr_bank_advices')->where('bank_advice_ref', $request->bank_advice_ref)->get()->first();
            if (!empty($exists)) {
                return response()->json([
                    'success' => 'Bank Advice Already Created.',
                ]);
                die();
            }
            DB::table('hr_emp_bonus_sheet')->whereIn("hr_emp_bonus_sheet_id", [$request->hr_emp_bonus_sheet_id])->update(
                ['bonus_sheet_status' => 'BANK ADVICE']
            );
            $data = array_merge($data, array(
                'bank_advice_type' => 'Bonus',
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
        DB::table('hr_bank_advices')->insert($data);
        return response()->json([
            'success'=>true,
        ]);
    }

    function salarySheetCreateSave(Request $request){
        $form_data = $request->except(['_token','hr_emp_salary_sheet_id','bat_dpid','selected_designations']);
        $bat_dpid = $request->bat_dpid;
        $selected_designations = $request->selected_designations?implode(',',$request->selected_designations):'All';
        $all_designations = DB::table('designations')->selectRaw('group_concat(designations_id) as designations_id')->get()->first();
        if($selected_designations == $all_designations->designations_id){
            $selected_designations = 'All';
        }


        DB::beginTransaction();
        try {
            if ($request->hr_emp_salary_sheet_code != '') {
                $sheet_code = $request->hr_emp_salary_sheet_code;
                $dataResult = DB::table('hr_emp_salary_sheet')
                    ->join('hr_monthly_salary_wages', 'hr_monthly_salary_wages.hr_emp_salary_sheet_id', '=', 'hr_emp_salary_sheet.hr_emp_salary_sheet_id')
                    ->select('salary_deduction_id')
                    ->where('hr_emp_salary_sheet.hr_emp_salary_sheet_code', $sheet_code)
                    ->get();
                if (!empty($dataResult)){
                    $salary_deduction_ids_arr = [];
                    foreach ($dataResult as $result){
                        if ($result->salary_deduction_id != ''){
                            $salary_deduction_ids_arr[] = $result->salary_deduction_id;
                        }
                    }
                    DB::table('hr_emp_salary_deduction')->whereIn('hr_emp_salary_deduction_id',$salary_deduction_ids_arr)->update(['deduction_status' => 112]);
                }

                DB::table('hr_emp_salary_sheet')->where('hr_emp_salary_sheet_code', $sheet_code)->delete();
            } else {
                $sheet_code = generateId('slry_code');
            }
            foreach ($bat_dpid as $dpid) {
                $data[] = array_merge($form_data, array(
                    'hr_emp_salary_sheet_code' => $sheet_code,
                    'created_by' => Auth::id(),
                    'bat_dpid' => $dpid,
                    'selected_designations' => $selected_designations,
                    'salary_sheet_type' => $request->salary_sheet_type,
                    'salary_sheet_status' => 92,
                    'created_at' => date('Y-m-d H:i:s'),
                ));
            }
            DB::table('hr_emp_salary_sheet')->insert($data);


            $salary_sheet = DB::table('hr_emp_salary_sheet')->where('hr_emp_salary_sheet_code', $sheet_code)->get();
            if (!empty($salary_sheet)) {
                if ($request->salary_sheet_type == 'PFP') {
                    self::pfpSalarySheetGenerate($salary_sheet,$request);
                } else {
                    $monthly_late_policy = getOptionValue('monthly_late_policy');

//                    $old_loan = DB::select("SELECT hr_emp_loan_id, (loan_amount/loan_duration) as monthly_payment,due_amount,
//                    trim( ',' FROM  REPLACE ( payment_history, '$sheet_code', '' ) ) as other_sheet_code
//                    FROM hr_emp_loan  WHERE FIND_IN_SET( '$sheet_code', payment_history )");

                    $old_loan = DB::table('loan_history')
                        ->select('loan_history.*', 'hr_emp_loan.loan_type', 'hr_emp_loan.paid_amount as total_paid_amount', 'hr_emp_loan.loan_duration', 'hr_emp_loan.monthly_payment',
                            'hr_emp_loan.due_amount')
                        ->join('hr_emp_loan', 'loan_history.hr_emp_loan_id', 'hr_emp_loan.hr_emp_loan_id')
                        ->where('salary_sheet_code', $sheet_code)->get();

                    $loan_history_id_ary = [];
                    if(count($old_loan) > 0){
                        foreach($old_loan as $loan){
                            if($loan->loan_type == 'Loan'){
//                                dump($loan);
                                $find_duration = DB::table('loan_history')->where('hr_emp_loan_id', $loan->hr_emp_loan_id)
                                    ->where('salary_sheet_code', '!=','')->count();

                                $loan_due_amount = $loan->due_amount + $loan->paid_amount;
                                $find_duration = ($find_duration==0)?0:$find_duration-1;
                                //in history table this have also a salary code
                                //so this row will counted .. for this reason -1

                                $update_data = [
                                    'paid_amount' => $loan->total_paid_amount - $loan->paid_amount,
                                    'monthly_payment' => $loan_due_amount / ($loan->loan_duration - $find_duration),
                                    'due_amount' => $loan_due_amount,
                                ];

//                                dump($update_data);
                                DB::table('hr_emp_loan')->where('hr_emp_loan_id',$loan->hr_emp_loan_id)->update($update_data);
                                $loan_history_id_ary[] = $loan->loan_history_id;
                            }
                            else{
                                $update_data = [
                                    'paid_amount' => $loan->total_paid_amount - $loan->paid_amount,
                                    'monthly_payment' => $loan->paid_amount,
                                    'due_amount' => $loan->paid_amount,
                                ];

//                                dump($update_data);
                                DB::table('hr_emp_loan')->where('hr_emp_loan_id',$loan->hr_emp_loan_id)->update($update_data);
                                $loan_history_id_ary[] = $loan->loan_history_id;
                            }


                        }

                        //delete row from loan history
                        DB::table('loan_history')->whereIn('loan_history_id', $loan_history_id_ary)->delete();

//                        foreach ($old_loan as $loan){
//                            $update_data = array(
//                                'monthly_payment'=>$loan->monthly_payment,
//                                'due_amount'=>$loan->due_amount+$loan->monthly_payment,
//                                'payment_history'=>$loan->other_sheet_code
//                            );
//                            DB::table('hr_emp_loan')->where('hr_emp_loan_id',$loan->hr_emp_loan_id)->update($update_data);
//                        }
                    }

//                    dd('exit', $loan_history_id_ary);
                    foreach ($salary_sheet as $sheet) {
//                    DB::table('hr_monthly_salary_wages')->where('hr_emp_salary_sheet_id', '=', $sheet->hr_emp_salary_sheet_id)->delete();
                        $month = $sheet->salary_month;
                        $selected_designations = $sheet->selected_designations;
                        $q = DB::table('sys_users')
                            ->selectRaw("
                    `hr_emp_attendance`.`sys_users_id`,
                    $sheet->hr_emp_salary_sheet_id as hr_emp_salary_sheet_id,
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
                    sys_users.basic_salary,
                    sys_users.min_gross as gross,
                    ifnull(sys_users.pf_amount_employee,0) as pf_amount_employee,
                    ifnull(sys_users.pf_amount_company,0) as pf_amount_company,
                    ifnull(sys_users.gf_amount,0) as gf_amount,
                    ifnull(sys_users.insurance_amount,0) as insurance_amount,
                    IFNULL((select sum(monthly_payment) from hr_emp_loan where sys_users_id=sys_users.id and hr_emp_loan.status='Active' AND lock_status=1),0) as due_loan_amount,
                    IFNULL((select sum(conveyance_amount) from hr_other_conveyances where sys_users_id=sys_users.id),0) as other_conveyance,
                    substr(hr_emp_attendance.day_is,1,7) AS `hr_salary_month_name`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'R') then 1 else 0 end)) AS `number_of_working_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in ('P','HP','WP','L','EO')) then 1 else 0 end)) AS `present_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'A') then 1 else 0 end)) AS `absent_days`,
                    sum((case when (`hr_emp_attendance`.`daily_status` = 'Lv') then 1 else 0 end)) AS `number_of_leave`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'W') then 1 else 0 end)) AS `number_of_weekend`,
                    sum((case when (`hr_emp_attendance`.`shift_day_status` = 'H') then 1 else 0 end)) AS `number_of_holidays`,
                    sum((case when (`hr_emp_attendance`.`daily_status` in('L','EO')) then 1 else 0 end)) AS `late_days`");

                        $q->LeftJoin('hr_emp_attendance', 'sys_users.id', 'hr_emp_attendance.sys_users_id');
                        $q->whereRaw("sys_users.bat_dpid=$sheet->bat_dpid
                    AND sys_users.id NOT IN(SELECT sys_users_id FROM hr_monthly_salary_wages WHERE hr_salary_month_name='$month')
                    AND substr(hr_emp_attendance.day_is,1,7)='$month'
                    AND sys_users.is_employee=1
                    AND sys_users.status IN('Active','Probation')");
                        if ($selected_designations != 'All') {
                            $q->whereRaw("FIND_IN_SET(sys_users.designations_id,'$selected_designations')");
                        }
                        $session_con = (sessionFilter('url','hr-salary-wages-emp-list'));
                        $session_con = trim(trim(strtolower($session_con)),'and');
                        if (!empty( $session_con )){
                            $q->whereRaw($session_con);
                        }

                        $q->groupBy('sys_users_id');
                        $emp_list = $q->get();
                        //dd($emp_list);
                        $emp_info = [];
                        if (!empty($emp_list)) {
                            foreach ($emp_list as $emp) {
                                $emp_data = $new_data = [];
                                $emp_data_all = (array)$emp;
                                $emp_data = array_except($emp_data_all, array('pf_applicable', 'gf_applicable', 'date_of_join', 'insurance_applicable', 'gf_amount', 'pf_amount_company','pf_amount_employee', 'late_deduction_applied', 'separation_date', 'status','due_loan_amount'));

                                if ($emp->pf_applicable) {
                                    $emp_data['pf_amount_company'] = $emp->pf_amount_company;
                                    $emp_data['pf_amount_employee'] = $emp->pf_amount_employee;
                                } else {
                                    $emp_data['pf_amount_company'] = 0;
                                    $emp_data['pf_amount_employee'] = 0;
                                }
                                if ($emp->late_deduction_applied) {
                                    $emp_data['late_absent_days'] = floor($emp->late_days / $monthly_late_policy);
                                } else {
                                    $emp_data['late_absent_days'] = 0;
                                }
                                if ($emp->gf_applicable) {
                                    $emp_data['gf_amount'] = $emp->gf_amount;
                                } else {
                                    $emp_data['gf_amount'] = 0;
                                }
                                if ($emp->insurance_applicable) {
                                    $emp_data['insurance_amount'] = $emp->insurance_amount;
                                } else {
                                    $emp_data['insurance_amount'] = 0;
                                }
                                $separated_month = date('Y-m', strtotime($emp->separation_date));
                                $join_month = date('Y-m', strtotime($emp->date_of_join));
                                if ($emp->status == 'Separated' && $separated_month == date('Y-m')) {
                                    $first_date = strtotime($separated_month . '-01');
                                    $separation_date = strtotime($emp->separation_date);
                                    $datediff = ($separation_date - $first_date);
                                    $payable_days = round($datediff / (60 * 60 * 24));
                                    $emp_data['payable_days'] = $payable_days;
                                    $emp_data['employment_type'] = 'Separated';
                                } elseif (($join_month == $month) && ($emp->date_of_join != ($month . '-01'))) {
                                    $last_date = strtotime(date('Y-m-t', strtotime($month))); // last date of salary month
                                    $join_date = strtotime($emp->date_of_join);
                                    $datediff = ($last_date - $join_date);
                                    $payable_days = round($datediff / (60 * 60 * 24));
                                    $emp_data['payable_days'] = $payable_days;
                                    $emp_data['employment_type'] = 'Separated';
                                } else {
                                    $emp_data['employment_type'] = 'Regular';
                                    $emp_data['payable_days'] = ($emp->number_of_working_days + $emp->number_of_holidays + $emp->number_of_weekend) - ($emp_data['late_absent_days'] + $emp->absent_days);
                                }

                                if($emp->due_loan_amount>0){
                                    $emp_data['advance_deduction'] = $emp->due_loan_amount;
                                    self::loanDeduction($sheet_code,$emp->sys_users_id,$emp->due_loan_amount);

                                }else{
                                    $emp_data['advance_deduction'] = 0;
                                }

                                //salary deduction
                                $salaryDeduction = self::salaryDeduction($sheet_code, $emp->sys_users_id);
                                if($salaryDeduction != 'empty'){
                                    $emp_data['salary_deduction_id'] = $salaryDeduction->hr_emp_salary_deduction_id;
                                    $emp_data['other_deduction'] = $salaryDeduction->deduction_amount;
                                    $emp_data['other_deduction_cause'] = $salaryDeduction->note;
                                }else{
                                    $emp_data['salary_deduction_id'] = null;
                                    $emp_data['other_deduction'] = 0;
                                    $emp_data['other_deduction_cause'] = '';
                                }
                                $emp_info[] = $emp_data;
                                DB::table('hr_emp_salary_components')
                                    ->where('record_type', 'salary_wages')
                                    ->where('record_ref', $month)
                                    ->where('sys_users_id', $emp->sys_users_id)
                                    ->delete();
                            }

                            //echo "<pre>"; print_r($emp_info); exit;
                            DB::table('hr_monthly_salary_wages')->insert($emp_info);
                            $sql2 = DB::table('hr_monthly_salary_wages')
                                ->selectRaw('hr_monthly_salary_wages.sys_users_id,
                                        hr_emp_salary_components.hr_emp_grades_id,
                                        hr_emp_salary_components.component_name,
                                        hr_emp_salary_components.component_slug,
                                        hr_emp_salary_components.addition_amount,
                                        hr_emp_salary_components.deduction_amount,
                                        hr_emp_salary_components.component_type,
                                        hr_emp_salary_components.auto_applicable')
                                ->join('hr_emp_salary_components', 'hr_emp_salary_components.sys_users_id', '=', 'hr_monthly_salary_wages.sys_users_id')
                                ->where('record_type', 'default')
                                ->where('hr_emp_salary_sheet_id', $sheet->hr_emp_salary_sheet_id)
                                ->whereRaw("hr_emp_salary_components.auto_applicable='YES'");
                            $salary_components = $sql2->get();
                            if (!empty($salary_components)) {
                                $component_data_all = [];
                                foreach ($salary_components as $component) {
                                    $emp_component = (array)$component;
                                    $component_data_all[] = array_merge($emp_component,
                                        array(
                                            'record_type' => 'salary_wages',
                                            'record_ref' => $month,
                                        ));
                                }
                                DB::table('hr_emp_salary_components')->insert($component_data_all);
                            }
                        }
                    }

//            debug($emp_info,1);
                }
            }
            DB::commit();
        } catch (\Exception $exception){
            DB::rollback();
            throw $exception;
        } catch (\Throwable $exception){
            DB::rollback();
            throw $exception;
        }
        return response()->json([
            'success'=>true,
        ]);
    }

    function loanDeduction($sheet_code,$sys_users_id,$total_due_payment){

        $due_info = DB::table('hr_emp_loan')
            ->where('sys_users_id',$sys_users_id)
            ->where('status','Active')
            ->where('due_amount','>',0)
            ->get();

        if(!empty($due_info)){
            foreach ($due_info as $due){
                if($due->monthly_payment <= $total_due_payment){

                    $paid_amount_1 = !empty($due->paid_amount)?$due->paid_amount:0;
                    $paid_amount = $paid_amount_1 + (($due->due_amount < $due->monthly_payment)?$due->due_amount:$due->monthly_payment);

//                    $update_arr['paid_amount'] = $due->due_amount;
                    $update_arr['paid_amount'] = $paid_amount;
                    if($due->monthly_payment == $due->due_amount){
                        $update_arr['due_amount'] = null;
                        $update_arr['monthly_payment'] = null;
                    }else{
                        $update_arr['due_amount'] = $due->due_amount-($due->due_amount<$due->monthly_payment?$due->due_amount:$due->monthly_payment);
                    }

                    $update_arr['updated_at'] = dTime();
                    $update_arr['payment_history'] = $due->payment_history?$due->payment_history.','.$sheet_code:$sheet_code;
                    $update_arr['updated_by'] = Auth::id();

                    DB::table('hr_emp_loan')->where('hr_emp_loan_id',$due->hr_emp_loan_id)->update($update_arr);

                    $insertLoanHistory = [
                        'hr_emp_loan_id' => $due->hr_emp_loan_id,
                        'sys_users_id' => $due->sys_users_id,
                        'paid_amount' => ($due->due_amount < $due->monthly_payment)?$due->due_amount:$due->monthly_payment,
                        'salary_sheet_code' => $sheet_code,
                        'created_by' => Auth::id(),
                        'created_at' => dTime()
                    ];

                    DB::table('loan_history')->insert($insertLoanHistory);
                }
            }
        }

    }

    function salaryDeduction($sheet_code,$sys_users_id){
        //$sys_users_id = 12;
        $deduction_info = DB::table('hr_emp_salary_deduction')
            ->select('hr_emp_salary_deduction_id','deduction_amount','note')
            ->where('sys_users_id',$sys_users_id)
            ->where('deduction_status',112)
            ->first();
        if(!empty($deduction_info)){
            $update_arr['deduction_status'] = 113;
            $update_arr['updated_at'] = dTime();
            $update_arr['updated_by'] = Auth::id();
            if(DB::table('hr_emp_salary_deduction')->where('hr_emp_salary_deduction_id',$deduction_info->hr_emp_salary_deduction_id)->update($update_arr)){
                $data = $deduction_info;
            }
        }else{
            $data = 'empty';
        }
        return $data;
    }

    function monthlyEmployeeList(Request $request,$sheet_code,$type=null){

        $post = $request->all();
        $sheetInfo = DB::table('hr_emp_salary_sheet')
            ->selectRaw('group_concat(hr_emp_salary_sheet_id) as hr_emp_salary_sheet_id,salary_month,salary_sheet_status,salary_sheet_type,
            group_concat(bat_distributorspoint.name) as distributor_points,is_salary_disbursement')
            ->join('bat_distributorspoint','bat_distributorspoint.id','hr_emp_salary_sheet.bat_dpid')
            ->where('hr_emp_salary_sheet_code',$sheet_code)->get()->first();
        $sheet_ids = explode(',',$sheetInfo->hr_emp_salary_sheet_id);
        if(empty($sheetInfo)){
            return redirect('404');
        }

        if($sheetInfo->salary_sheet_type == 'PFP'){
            $employeeInfo = DB::table('hr_emp_pfp_salary');
            $employeeInfo ->select('hr_emp_pfp_salary.*','bat_distributorspoint.name as point_name','sys_users.name','sys_users.user_code',
                'designations.designations_name');

            $employeeInfo->join('sys_users','sys_users.id','=','hr_emp_pfp_salary.sys_users_id');
            $employeeInfo->leftJoin('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','hr_emp_pfp_salary.hr_emp_grades_id');
            $employeeInfo->leftJoin('bat_company','bat_company.bat_company_id','=','hr_emp_pfp_salary.bat_company_id');
            $employeeInfo->leftJoin('bat_distributorspoint','bat_distributorspoint.id','=','hr_emp_pfp_salary.bat_dpid');
            $employeeInfo->leftJoin('designations','designations.designations_id','=','hr_emp_pfp_salary.designations_id');

            $employeeInfo->whereIn('hr_emp_pfp_salary.hr_emp_salary_sheet_id', $sheet_ids);
            $employeeInfo->where('hr_emp_pfp_salary.status', 'Active');
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
        }else{
            $sheetHead = DB::table('hr_emp_salary_components')
                ->where('record_type','default')
                ->where('auto_applicable','YES')
                ->groupBy('component_slug');
            $data['salary_component'] = $sheetHead->get();

            $employeeInfo=DB::table('hr_monthly_salary_wages');
            $employeeInfo ->select('sys_users.id','sys_users.name','sys_users.user_code','hr_emp_grades.hr_emp_grade_name','sys_users.max_variable_salary',
                'designations.designations_name','bat_company.company_name','bat_distributorspoint.name as point_name','hr_monthly_salary_wages.*');
            if(!empty($data['salary_component'])){
                foreach ($data['salary_component'] as $component){
                    $employeeInfo->selectRaw("func_get_salary_component(sys_users.id,hr_salary_month_name,'$component->component_slug') as $component->component_slug");
                }
            }

            $employeeInfo->join('sys_users','sys_users.id','=','hr_monthly_salary_wages.sys_users_id');
            $employeeInfo->leftJoin('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','hr_monthly_salary_wages.hr_emp_grades_id');
            $employeeInfo->leftJoin('bat_company','bat_company.bat_company_id','=','hr_monthly_salary_wages.bat_company_id');
            $employeeInfo->leftJoin('bat_distributorspoint','bat_distributorspoint.id','=','hr_monthly_salary_wages.bat_dpid');
            $employeeInfo->leftJoin('designations','designations.designations_id','=','hr_monthly_salary_wages.designations_id');

            $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_salary_sheet_id', $sheet_ids);
            $employeeInfo->where('hr_monthly_salary_wages.status', 'Active');

            if (!empty($post['bat_dpid'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.bat_dpid', $post['bat_dpid']);
            }
            if (!empty($post['hr_emp_grades_list'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_grades_id', $post['hr_emp_grades_list']);
            }
            if (!empty($post['hr_emp_salary_designations'])) {
                $employeeInfo->whereIn('hr_monthly_salary_wages.designations_id', $post['hr_emp_salary_designations']);
            }
            $session_con = (sessionFilter('url','hr-salary-wages-emp-list'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            if($session_con){
                $employeeInfo->whereRaw($session_con);
            }

            $data['employeeList'] = $employeeInfo->get();

        }
        $data['hr_emp_grades_list'] = $request->hr_emp_grades_list?$request->hr_emp_grades_list:'';
        $data['bat_dpid'] = $request->bat_dpid?$request->bat_dpid:'';
        $data['hr_emp_salary_designations'] = $request->hr_emp_salary_designations?$request->hr_emp_salary_designations:'';
        $data['distributor_points'] = @$sheetInfo->distributor_points;
        $data['salary_month'] = @$sheetInfo->salary_month;
        $data['salary_sheet_status'] = @$sheetInfo->salary_sheet_status;
        $data['sheet_code'] = $sheet_code;
        $data['salary_disbusement']=$sheetInfo->is_salary_disbursement;
        //dd($data['salary_disbusement']);

        if($type == 'pdf'){
            if($sheetInfo->salary_sheet_type == 'PFP'){
                $data['report_title'] = ' PFP Salary Sheet - '. date("F, Y", strtotime(@$sheetInfo->salary_month));
                $data['filename'] = 'salary_sheet-'.@$sheetInfo->salary_month;
                $data['orientation'] = "L";
                $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
                $data['signatures']=['Prepared by','Checked by','Approved by'];
                $view='Hr_payroll.report.pfp_salary_sheet_pdf';
                $data['row'] = $data['employeeList'];

                $designation_array=array();
                $designation_type=array();
                foreach ($data['employeeList'] as $employee) {
                    $designation_array[$employee->designations_id][]=$employee;
                    $designation_type[$employee->designations_id]=$employee->designations_name;
                }
                $data['sheet_type']='PFP';
                $data['designation_wise_array']=$designation_array;
                $data['designation_type_array']=$designation_type;


                PdfHelper::exportPdf($view,$data);
            }

            else{
                $data['report_title'] = ' Fixed Salary Sheet - '. date("F, Y", strtotime(@$sheetInfo->salary_month));
                $data['filename'] = 'salary_sheet-'.@$sheetInfo->salary_month;
                $data['orientation'] = "L";
                $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
                $data['signatures']=['Prepared by','Checked by','Approved by'];
                $view='Hr_payroll.report.salary_sheet_pdf';
                $data['row'] = $data['employeeList'];
                $data['sheet_type']='Fixed';
                $designation_array=array();
                $designation_type=array();
                foreach ($data['employeeList'] as $employee) {
                    $designation_array[$employee->designations_id][]=$employee;
                    $designation_type[$employee->designations_id]=$employee->designations_name;
                }
                $data['designation_wise_array']=$designation_array;
                $data['designation_type_array']=$designation_type;

                PdfHelper::exportPdf($view,$data);
            }

        }

        else if($type == 'excel'){

            if($sheetInfo->salary_sheet_type == 'PFP'){
                $file_name = 'PFP Salary Sheet.xlsx';
                $header_array =[
                    ['text'=>'SL No.'],
                    ['text'=>'Distributor Point'],
                    ['text'=>'Employee Name'],
                    ['text'=>'Employee Code'],
                    ['text'=>'PFP Target Amount'],
                    ['text'=>'PFP Achieve Ratio'],
                    ['text'=>'PFP Earn Amount']

                ];

                $excel_array =array();
                // dd($data['employeeList']);


                if(!empty($data['employeeList'])){
                    $sl =1;
                    $net_pfp_target_amount=0;
                    $net_pfp_earn_amount=0;
                    foreach ($data['employeeList'] as $i=>$emp){
                        $temp = array();
                        $temp['sl']=$sl;
                        $temp['point_name']= $emp->point_name;
                        $temp['name']=$emp->name;
                        $temp['user_code'] = $emp->user_code;
                        $temp['pfp_target_amount']=$emp->pfp_target_amount;
                        $temp['pfp_achieve_ratio']=$emp->pfp_achieve_ratio;
                        $temp['pfp_earn_amount']=$emp->pfp_earn_amount;
                        $excel_array[]=$temp;

                        $net_pfp_target_amount+=$emp->pfp_target_amount;
                        $net_pfp_earn_amount+=$emp->pfp_earn_amount;
                        $sl++;
                    }

                    $temp2=array();
                    $temp2['sl']='';
                    $temp2['point_name']= '';
                    $temp2['name']='';
                    $temp2['user_code'] ='';
                    $temp2['pfp_target_amount']=$net_pfp_target_amount;
                    $temp2['pfp_achieve_ratio']='';
                    $temp2['pfp_earn_amount']=$net_pfp_earn_amount;
                    $excel_array[]=$temp2;

                }

                $excel_array_to_send = [
                    'header_array' => $header_array,
                    'data_array' => $excel_array,
                    'file_name' => $file_name,
                    'header_color'=>0
                ];


                $fileName = exportExcel($excel_array_to_send);

                return response()->json(['status' => 'success', 'file' => $fileName]);
            }else{
                $file_name = 'Fixed Salary Sheet.xlsx';
                $header_array=[
                    [
                        'text'=>'SL No.',
                        'row'=>2
                    ],
                    [
                        'text'=>'Distributor Point',
                        'row'=>2
                    ],
                    [
                        'text'=>'Employee Name',
                        'row'=>2
                    ],
                    [
                        'text'=>'Employee Code',
                        'row'=>2
                    ],
                    [
                        'text'=>'Present Days',
                        'row'=>2
                    ],
                    [
                        'text'=>'Leave Days',
                        'row'=>2
                    ],
                    [
                        'text'=>'Absent Days',
                        'row'=>2
                    ],
                ];



                $salary_component_header_sub = array();
                $salary_component_header_sub[]=[
                    'text'=>'Basic'
                ];
                $count = 0;
                if(!empty($salary_component)){
                    $count = count($salary_component);
                    foreach ($salary_component as $component){
                        $salary_component_header_sub[]=[
                            'text'=> $component->component_slug
                        ];
                    }

                }

                $salary_component_header_sub[]=[
                    'text'=>'Total'
                ];
                $header_array[]=[
                    'text'=>'Fixed Salary',
                    'col'=>$count+2,
                    'sub'=>  $salary_component_header_sub
                ];

                $header_array[]=[
                  'text'=>'Deduction',
                  'col'=>3,
                  'sub'=>[
                      ['text'=>'PF Amount'],
                      ['text'=>'Advance Loan'],
                      ['text'=>'Salary Deduction']
                  ]
                ];


                $header_array[]=[
                    'text'=>'Net Salary',
                    'row'=>2
                ];


                $excel_array =array();



                if(!empty($data['employeeList'])){
                    $sl =1;
                    $net_fixed_salary = 0;
                    $net_pf_amount = 0;
                    $net_salary = 0;
                    $loan_total = 0;
                    $deduction_total = 0;
                    foreach ($data['employeeList'] as $i=>$emp){
                        $temp = array();
                        $temp['sl']=$sl;
                        $temp['point_name']= $emp->point_name;
                        $temp['name']=$emp->name;
                        $temp['user_code'] = $emp->user_code;
                        $temp['present_days']=$emp->present_days;
                        $temp['leave_days']=$emp->number_of_leave;
                        $temp['absent_days']=$emp->absent_days;
                        $temp['basic_salary']=$emp->basic_salary;
                        if(!empty($salary_component)){

                            foreach ($salary_component as $component){
                                $slug_name = $component->component_slug;
                                $temp['component'.$slug_name]= $emp->$slug_name;
                            }

                        }

                        $temp['gross_salary']= $emp->gross;
                        $temp['pf_amount'] = $emp->pf_amount_employee;
                        $temp['loan_amount'] = $emp->advance_deduction;
                        $temp['salary_deduction']=$emp->other_deduction;
                        $temp['net_salary']= $emp->net_payable;
                        $excel_array[]=$temp;

                        $net_fixed_salary+=$emp->gross;
                        $net_pf_amount+=$emp->pf_amount_employee;
                        $net_salary+=$emp->net_payable;
                        $loan_total+=$emp->advance_deduction;
                        $deduction_total+=$emp->other_deduction;
                        $sl++;
                    }

                    $temp2=array();
                    $temp2['sl']='';
                    $temp2['point_name']= '';
                    $temp2['name']='';
                    $temp2['user_code'] ='';
                    $temp2['present_days']='';
                    $temp2['leave_days']='';
                    $temp2['absent_days']='';
                    $temp2['basic_salary']='';


                    if(!empty($salary_component)){

                        foreach ($salary_component as $component){
                            $slug_name = $component->component_slug;
                            $temp2['component'.$slug_name]= '';
                        }

                    }
                    $temp2['gross_salary']=$net_fixed_salary;
                    $temp2['pf_amount'] = $net_pf_amount;
                    $temp2['loan_amount'] = $loan_total;
                    $temp2['salary_deduction']=$deduction_total;
                    $temp2['net_salary']= $net_salary;
                    $excel_array[]=$temp2;

                }

                $excel_array_to_send = [
                    'header_array' => $header_array,
                    'data_array' => $excel_array,
                    'file_name' => $file_name,
                    'header_color'=>0
                ];


                $fileName = exportExcel($excel_array_to_send);

                return response()->json(['status' => 'success', 'file' => $fileName]);
            }
        }
        if($sheetInfo->salary_sheet_type == 'PFP'){
            return view('Hr_payroll.salary_wages.empSalaryListPFP', $data);
        }else{
            return view('Hr_payroll.salary_wages.empSalaryList',$data);
        }

    }

    function monthlyEmployeeSalaryInfo(Request $request){
        $employeeInfo=DB::table('hr_monthly_salary_wages');
        $employeeInfo ->select('sys_users.id','sys_users.name','sys_users.user_code','hr_emp_grades.hr_emp_grade_name',
            'designations.designations_name','bat_company.company_name','bat_distributorspoint.name as point_name','hr_monthly_salary_wages.*');
        $employeeInfo->join('sys_users','sys_users.id','=','hr_monthly_salary_wages.sys_users_id');
        $employeeInfo->leftJoin('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','hr_monthly_salary_wages.hr_emp_grades_id');
        $employeeInfo->leftJoin('bat_company','bat_company.bat_company_id','=','hr_monthly_salary_wages.bat_company_id');
        $employeeInfo->leftJoin('bat_distributorspoint','bat_distributorspoint.id','=','hr_monthly_salary_wages.bat_dpid');
        $employeeInfo->leftJoin('designations','designations.designations_id','=','hr_monthly_salary_wages.designations_id');
        $employeeInfo->where('hr_monthly_salary_wages.sys_users_id','=',$request->emp_id);
        $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_name','=',$request->salary_month);
        $data['employeeInfo'] = $employeeInfo->get()->first();
        return view('Hr_payroll.salary_wages.empSalaryEdit',$data);
    }

    function monthlyEmployeeSalaryUpdate(Request $request){
        $update_arr = array(
            'ot_hours'=> $request->ot_hours,
            'ot_extra_hours'=> $request->ot_extra_hours,
            'offday_ot_hours'=> $request->offday_ot_hours,
            'arrear'=> $request->arrear,
            'attendance_bonus'=> $request->attendance_bonus,
            'absent_deduction'=> $request->absent_deduction,
            'advance_deduction'=> $request->advance_deduction,
            'other_deduction'=> $request->other_deduction,
            'card_lost_deduction'=> $request->card_lost_deduction,
            'other_deduction'=> $request->other_deduction,
            'other_deduction_cause'=> $request->other_deduction_cause,
            'stamp_amount'=> $request->stamp_amount
        );
        $update = DB::table('hr_monthly_salary_wages')
            ->where('sys_users_id','=',$request->emp_id)
            ->where('hr_salary_month_name','=',$request->salary_month)
            ->update($update_arr);
        if($update){
            return response()->json([
                'success' => true
            ]);
        }else{
            return response()->json([
                'success' => false
            ]);
        }
    }


    function monthlyAttendanceReport(Request $request){
        $post = $request->all();

        $employeeInfo=DB::table('hr_monthly_salary_wages');

        $employeeInfo ->select('sys_users.id','sys_users.name','sys_users.user_code','hr_emp_grades.hr_emp_grade_name',
            'departments.departments_name','designations.designations_name','hr_monthly_salary_wages.*');

        $employeeInfo->join('sys_users','sys_users.id','=','hr_monthly_salary_wages.sys_users_id');

        $employeeInfo->join('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','hr_monthly_salary_wages.hr_emp_grades_id');

        $employeeInfo->join('departments','departments.departments_id','=','hr_monthly_salary_wages.departments_id');

        $employeeInfo->leftJoin('designations','designations.designations_id','=','hr_monthly_salary_wages.designations_id');

        if (!empty($post['hr_salary_month_config'])) {
            $conf_id = $post['hr_salary_month_config'];
            $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_configs_id', $post['hr_salary_month_config']);
        }else{
            $conf_id = DB::select(DB::Raw('select max(hr_salary_month_configs_id) hr_salary_month_configs_id from hr_monthly_salary_wages'));
            $conf_id = isset($conf_id[0]->hr_salary_month_configs_id)?$conf_id[0]->hr_salary_month_configs_id:"";
            $employeeInfo->where('hr_monthly_salary_wages.hr_salary_month_configs_id','=',$conf_id);
        }

        if (!empty($post['hr_emp_categorys'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_units_id', $post['hr_emp_categorys']);
        }
        if (!empty($post['hr_emp_grades_list'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_grades_id', $post['hr_emp_grades_list']);
        }
        if (!empty($post['hr_emp_salary_units'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.hr_emp_units_id', $post['hr_emp_salary_units']);
        }
        if (!empty($post['hr_emp_departments'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.departments_id', $post['hr_emp_departments']);
        }
        if (!empty($post['hr_emp_salary_designations'])) {
            $employeeInfo->whereIn('hr_monthly_salary_wages.designations_id', $post['hr_emp_salary_designations']);
        }

        $data['employeeList'] = $employeeInfo->get();
        $data['hr_emp_grades_list'] = $request->hr_emp_grades_list?$request->hr_emp_grades_list:'';
        $data['hr_emp_departments'] = $request->hr_emp_departments?$request->hr_emp_departments:'';
        $data['hr_emp_salary_designations'] = $request->hr_emp_salary_designations?$request->hr_emp_salary_designations:'';
        $data['hr_emp_salary_units'] = $request->hr_emp_salary_units?$request->hr_emp_salary_units:'';
        $data['hr_salary_month_config'] = $conf_id?$conf_id:'';

        $config_info = DB::table('hr_salary_month_configs')
            ->join('hr_emp_categorys','hr_emp_categorys.hr_emp_categorys_id','=','hr_salary_month_configs.hr_emp_categorys_id')
            ->where('hr_salary_month_configs.hr_salary_month_configs_id','=',$conf_id)
            ->get()
            ->first();
        $data['salary_config_info'] = $config_info;
        return view('Hr_payroll.salary_wages.monthly_attendance_report',$data);
    }

    function salarySheetDeleteEmployee(Request $request){
        $sys_users_ids = $request->emp_id;
        $salary_month = $request->salary_month;
        $delete = DB::table('hr_monthly_salary_wages')
            ->where('hr_salary_month_name',$salary_month)
            ->whereIn('sys_users_id',$sys_users_ids)
            ->update(['status'=>'Inactive']);
        if($delete){
            return response()->json([
                'success'=>true,
            ]);
        }else{
            return response()->json([
                'success'=>false,
            ]);
        }
    }

    function pfpSalarySheetGenerate($salary_sheet){
        foreach ($salary_sheet as $sheet) {
            $month = $sheet->salary_month;
            $month_year_ary = explode('-', $month);
            $emp_not_get_kpi_qry = DB::table('hr_emp_attendance')
                ->join('sys_users','sys_users.id','hr_emp_attendance.sys_users_id')
                ->whereIn('sys_users.designations_id',[543,545])
                ->selectRaw("sys_users_id,daily_status, count(daily_status) as count_status");
            $emp_not_get_kpi_qry->whereRaw("YEAR ( day_is ) = '$month_year_ary[0]' AND MONTH ( day_is ) = '$month_year_ary[1]' AND daily_status IN('Lv','A')
                                GROUP BY sys_users_id,daily_status
                                HAVING count_status>=2 OR daily_status='A'");
            $emp_not_get_kpi = $emp_not_get_kpi_qry->get();
            $emp_not_get_kpi_id_ary = [];

            if(count($emp_not_get_kpi) > 0){
                foreach($emp_not_get_kpi as $emp_kpi_id){
                    $emp_not_get_kpi_id_ary[] = $emp_kpi_id->sys_users_id;
                }
            }

            $selected_designations = $sheet->selected_designations;
            $q = DB::table('sys_users')
                ->selectRaw("
                    `sys_users`.*,
                    IFNULL(COALESCE((select sum(variable_salary_amount) from hr_emp_monthly_variable_salary where sys_users_id=sys_users.id and vsalary_month='$month'
                    AND hr_emp_monthly_variable_salary.status='Active'),max_variable_salary),0) as target_variable_salary,
		            ifnull(func_get_variable_salary(sys_users.user_code,'$month'),0) as target_achive_ratio");

            $q->whereRaw("sys_users.bat_dpid=$sheet->bat_dpid
                    AND sys_users.id NOT IN(SELECT sys_users_id FROM hr_emp_pfp_salary WHERE salary_month='$month')
                    AND sys_users.is_employee=1
                    AND sys_users.status IN('Active','Probation')");
            if ($selected_designations != 'All') {
                $q->whereRaw("FIND_IN_SET(sys_users.designations_id,'$selected_designations')");
            }
            $session_con = (sessionFilter('url','hr-salary-wages-emp-list'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            $q->whereRaw($session_con);
            $q->groupBy('sys_users.id');
            $emp_list = $q->get();
//            dd($emp_list);
            $emp_info = [];
            if (!empty($emp_list)) {
                foreach ($emp_list as $emp) {
                    if(!in_array($emp->id, $emp_not_get_kpi_id_ary)){
                        $emp_info[] = array(
                            'sys_users_id'=>$emp->id,
                            'hr_emp_salary_sheet_id'=>$emp->id,
                            'hr_emp_salary_sheet_id'=>$sheet->hr_emp_salary_sheet_id,
                            'salary_month'=>$sheet->salary_month,
                            'branchs_id'=>$emp->branchs_id,
                            'bat_company_id'=>$emp->bat_company_id,
                            'bat_dpid'=>$emp->bat_dpid,
                            'hr_emp_categorys_id'=>$emp->hr_emp_categorys_id,
                            'hr_emp_sections_id'=>$emp->hr_emp_sections_id,
                            'hr_emp_units_id'=>$emp->hr_emp_units_id,
                            'hr_emp_grades_id'=>$emp->hr_emp_grades_id,
                            'hr_emp_grades_id'=>$emp->hr_emp_grades_id,
                            'designations_id'=>$emp->designations_id,
                            'pfp_target_amount'=>$emp->target_variable_salary,
                            'pfp_achieve_ratio'=>$emp->target_achive_ratio,
                            'pfp_earn_amount'=>($emp->target_variable_salary*$emp->target_achive_ratio)/100,
                            'created_by'=>Auth::id(),
                            'created_at'=>dTime()
                        );
                    }
                }

                DB::table('hr_emp_pfp_salary')->insert($emp_info);
            }
        }
        return true;
    }

    function salarySheetBankAdvicePDF($code){
        $adviceInfo = DB::table('hr_bank_advices')
            ->select(
                'hr_bank_advices.bank_advice_ref',
                'hr_bank_advices.advice_note',
                'hr_bank_advices.authorize_note',
                'hr_bank_advices.bank_advice_type',
                'hr_bank_advices.bank_advice_ref',
                'hr_bank_advices.bank_ac_no',
                'banks.banks_name',
                'bank_branchs.bank_branch_name',
                'bank_branchs.bank_branch_address',
                'hr_emp_salary_sheet.salary_sheet_type',
                'hr_bank_advices.bank_advice_date',
                DB::raw('group_concat(hr_emp_salary_sheet.hr_emp_salary_sheet_id) as salary_sheet_id')
            )
            ->join('banks', 'banks.banks_id', '=', 'hr_bank_advices.banks_id')
            ->join('bank_branchs', 'hr_bank_advices.branchs_id', '=', 'bank_branchs.bank_branchs_id')
            ->join('hr_emp_salary_sheet', 'hr_emp_salary_sheet.hr_emp_salary_sheet_code', '=', 'hr_bank_advices.bank_advice_ref')
            ->where('bank_advice_ref', '=', $code)
            //->groupBy('bank_advice_ref')->get();
            ->first();

        $salary_sheet_id =  explode(",",$adviceInfo->salary_sheet_id);

        if (!empty($salary_sheet_id) && $adviceInfo->salary_sheet_type=='PFP'){
            $data['salaries'] = DB::table('hr_emp_pfp_salary')
                ->select('sys_users.name','hr_emp_pfp_salary.pfp_earn_amount  as salary_amount','hr_emp_pfp_salary.salary_account_no','hr_emp_pfp_salary.salary_month as salary_month')
                ->join('sys_users', 'hr_emp_pfp_salary.sys_users_id', '=', 'sys_users.id')
                ->whereIn('hr_emp_pfp_salary.hr_emp_salary_sheet_id', $salary_sheet_id)
                ->get();

            $data['report_title'] = ' PfP Salary Sheet - '. date("F, Y", strtotime($adviceInfo->bank_advice_date));

        }elseif(!empty($salary_sheet_id) && $adviceInfo->salary_sheet_type=='Fixed'){
            $data['salaries'] = DB::table('hr_monthly_salary_wages')
                ->select('sys_users.name','hr_monthly_salary_wages.net_payable as salary_amount','hr_monthly_salary_wages.salary_account_no','hr_monthly_salary_wages.hr_salary_month_name  as salary_month')
                ->join('sys_users', 'hr_monthly_salary_wages.sys_users_id', '=', 'sys_users.id')
                ->whereIn('hr_monthly_salary_wages.hr_emp_salary_sheet_id', $salary_sheet_id)
                ->get();
            $data['report_title'] = 'Fixed Salary Sheet - '. date("F, Y", strtotime($adviceInfo->bank_advice_date));
        }


        $data['filename'] = 'salary_sheet';
        $data['orientation'] = "P";
        $data['signatures']=['Prepared by','Checked by','Approved by'];
        $view='Hr_payroll.report.salary_sheet_bank_advice_pdf';
        $data['advice_info'] = $adviceInfo;
        PdfHelper::exportPdf($view,$data);
    }




    //Go for delegation process
    public function salarySheetDelegationProcess(Request $request) {
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
    public function salarySheetApproveList() {
        $slug = 'slry_code';
        $data['columns'] = array(
            'hr_emp_salary_sheet_code',
            'preparation_date',
            'salary_sheet_type',
            'salary_sheet_status',
            'salary_month',
            'hr_emp_salary_sheet.created_by',
            'hr_emp_salary_sheet.created_at'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'], array());
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_emp_salary_sheet_code;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;
        return view('Hr_payroll.salary_wages.approval_list', $data);
    }



//Approved form deligation
    public function salarySheetBulkApproved(Request $request) {
        $codes = $request->codes;
        $comments = 'Salary Sheet Bulk Approved';
        $request->merge([
            'slug' => 'slry_code',
            'comments' => $comments,
            'additional_data' => ''
        ]);
        $post = $request->all();
//        dd($post);
        $result = goToDelegationProcess($post);
        if ($result) {
            $resultArray = json_decode($result, true);
            $sucs_msg = [];
            $fail_msg = [];
            foreach ($resultArray as $item) {
                foreach ($item['data'] as $code => $prc_item) {
                    if ($prc_item['mode'] == 'Success') {
                        $sucs_msg[$code] = $prc_item['msg'];
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


}
