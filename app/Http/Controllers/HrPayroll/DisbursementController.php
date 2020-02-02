<?php

namespace App\Http\Controllers\HrPayroll;
use App\Events\AuditTrailEvent as Audit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;

use App\Helpers\PdfHelper;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class DisbursementController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    function disbursementList(){
        return view('Hr_payroll.disbursement.list');
    }

    function newLoanEntry($loan_id=null){
        $data = [];
        if($loan_id){
            $data['row'] = DB::table('hr_emp_loan')->where('hr_emp_loan_id',$loan_id)->first();
        }
        return view('Hr_payroll.disbursement.loan_entry',$data);
    }

    function storePaidLoan(Request $request){

        if($request->loan_id > 0){

            $loan_info = DB::table('hr_emp_loan')->select('sys_users_id','loan_duration',
                'loan_amount', 'monthly_payment', 'due_amount', 'paid_amount')->where('hr_emp_loan_id', $request->loan_id)->first();

            if($loan_info->due_amount >= $request->amount) {
                $insert_data = [
                    'hr_emp_loan_id' => $request->loan_id,
                    'sys_users_id' => $loan_info->sys_users_id,
                    'paid_amount' => $request->amount,
                    'created_by' => session('USER_ID'),
                    'created_at' => date("Y-m-d H:i:s"),
                ];

                $find_duration = DB::table('loan_history')->where('hr_emp_loan_id', $request->loan_id)
                    ->where('salary_sheet_code', '!=','')->count();


                $paid_amount = empty($loan_info->paid_amount) ? 0 : $loan_info->paid_amount;
                $due_amount = $loan_info->due_amount - $request->amount;

                if(!empty($loan_info->paid_amount)){
                    $paidAmount = $loan_info->paid_amount+$request->amount;
                }
                else{
                    $paidAmount = $request->amount;
                }

                $update_loan = [
                    'paid_amount' => $paidAmount,
                    'due_amount' => $due_amount,
                    'monthly_payment' => $due_amount / ($loan_info->loan_duration - $find_duration),
                ];

                DB::beginTransaction();

                try {
//                    DB::table('hr_emp_loan')->where('hr_emp_loan_id', $request->loan_id)->update($update_loan);

                    $update_property = DB::table('hr_emp_loan')->where('hr_emp_loan_id', $request->loan_id);
                    Audit::build($update_property)->update($update_loan);

                    DB::table('loan_history')->insert($insert_data);

                    DB::commit();
                    $return_data['msg'] = "Data Stored Successfully!";
                    $return_data['code'] = 200;

                } catch (\Exception $e) {

                    DB::rollback();
                    $return_data['msg'] = "Data Not Stored!";
                    $return_data['code'] = 500;
                }
            }
            else{

//                dd('okkk');
                $return_data['msg'] = "Paid Amount Gater Then Due Amount!";
                $return_data['code'] = 500;
            }
        }

        return $return_data;
    }

    function paidLoanList(Request $request){

        $qry = DB::table('sys_users');
        $qry->join('hr_emp_loan', 'hr_emp_loan.sys_users_id', 'sys_users.id');
        $qry->where('sys_users.status', 'Active')->where('sys_users.is_employee', 1);
        $qry->where('hr_emp_loan.due_amount' ,'>', 0);
        $session_con = (sessionFilter('url','emp-list-loan-paid'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qry->whereRaw($session_con);
        }
        $data['users'] = $qry->get();

//        dd($data['users'][0]->id);

        $data['user_id'] = 0;
        $data['loan_amount'] = 0;
        $data['due_amount'] = 0;
        $data['list'] = [];

        if (!empty($_POST) && $request->user_id > 0)
        {
            $data['user_id'] = $request->user_id;

            $result_data = DB::table('hr_emp_loan')->select('hr_emp_loan_id', 'loan_amount', 'due_amount')
                ->where('sys_users_id', $request->user_id)->where('due_amount', '>', 0)->first();

            if(!empty($result_data)){
                $data['loan_amount'] = $result_data->loan_amount;
                $data['due_amount'] = $result_data->due_amount;

                $data_list = DB::table('loan_history')->select('paid_amount', 'created_at')
                    ->where('hr_emp_loan_id', $result_data->hr_emp_loan_id)->get();

                if(count($data_list) > 0){
                    $data['list'] = $data_list;
                }
            }
        }

        return view('Hr_payroll.disbursement.paid_loan_list', $data);
    }

    function loanEntrySave(Request $request){
        $form_data = $request->except(['hr_emp_loan_id','_token']);
        $emp_info = DB::table('sys_users')->where('id',$request->sys_users_id)->first();
        if($request->hr_emp_loan_id){
            $form_data['bat_dpid'] = $emp_info->bat_dpid;
            $form_data['loan_duration'] = $request->loan_duration;
            $form_data['due_amount'] = $request->loan_amount;
            $form_data['monthly_payment'] = ($request->loan_amount/$request->loan_duration);
            $form_data['updated_at'] = dTime();
            $form_data['updated_by'] = Auth::id();
            $form_data['loan_status'] = 98;
            DB::table('hr_emp_loan')->where('hr_emp_loan_id',$request->hr_emp_loan_id)->update($form_data);
        }else{
            $form_data['bat_dpid'] = $emp_info->bat_dpid;
            $form_data['due_amount'] = $request->loan_amount;
            $form_data['loan_duration'] = $request->loan_duration;
            $form_data['monthly_payment'] = ($request->loan_amount/$request->loan_duration);
            $form_data['created_at'] = dTime();
            $form_data['created_by'] = Auth::id();
            $form_data['loan_status'] = 98;
            DB::table('hr_emp_loan')->insert($form_data);
        }
        return response()->json([
            'success'=>true,
        ]);
    }

    public function salaryDisbursement(){
        return view('Hr_payroll.disbursement.salary_disbursement');
    }

    public function loanDelete(Request $request){
        $form_data['status'] = 'Inactive';
        $form_data['updated_at'] = dTime();
        $form_data['updated_by'] = Auth::id();
        DB::table('hr_emp_loan')->where('hr_emp_loan_id',$request->hr_emp_loan_id)->update($form_data);
        return response()->json([
            'success'=>true,
        ]);
    }


    public function loanLock(Request $request){
        $form_data['lock_status'] = '1';
        $form_data['updated_at'] = dTime();
        $form_data['updated_by'] = Auth::id();
        DB::table('hr_emp_loan')->where('hr_emp_loan_id',$request->hr_emp_loan_id)->update($form_data);
        return response()->json([
            'success'=>true,
        ]);
    }

    public function employeeLoanPrint(Request $request){

        $data['report_data'] = DB::table('hr_emp_loan')
            ->select('hr_emp_loan.*',
                'sys_users.user_code',
                'sys_users.name',
                'sys_users.date_of_join',
                'sys_users.basic_salary',
                'designations.designations_name',
                'bat_distributorspoint.name as point_name',
                'hr_emp_grades.hr_emp_grade_name',
                'departments.departments_name',
                'hr_emp_sections.hr_emp_section_name'
            )
            ->leftJoin('sys_users', 'sys_users.id', '=', 'hr_emp_loan.sys_users_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->where('hr_emp_loan_id',$request->id )->first();

        $data['salary_components'] = DB::table('hr_emp_salary_components')->where('record_type','default')->where('sys_users_id', $data['report_data']->sys_users_id)->get();

        $data['report_title'] = 'Loan & Advance Salary';
        $data['filename'] = 'loan_report_pdf';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $data['signatures']=['Prepared by','Checked by','Approved by'];
        $view='Hr_payroll.disbursement.loan_report_pdf';
        PdfHelper::exportPdf($view,$data);

    }

    function employeeLoanInfo($sys_users_id){
        $sql = DB::table('sys_users');
        $sql->selectRaw('min_gross,max_variable_salary,ifnull(sum(due_amount),0) as previous_loan_due');
        $sql->leftJoin('hr_emp_loan',function ($join){
          $join->on('sys_users.id','=','hr_emp_loan.sys_users_id');
          $join->where('hr_emp_loan.status','=','Active');
        });
        $sql->where('sys_users.id',$sys_users_id);
        $emp_info = $sql->first();
        return response()->json($emp_info);
    }

    public function makeSalaryDisburse(Request $request){
       $data=array(
         'salary_sheet_status'=>93,
          'is_salary_disbursement'=>1
       );
       $update=DB::table('hr_emp_salary_sheet')->where('hr_emp_salary_sheet_code',$request->id)->update($data);
       return 1;
    }

    public function makeBonusDisburse(Request $request){
        $data=array(
            'bonus_sheet_status'=>'DISBURSE',
            'is_bonus_disbursement'=>1
        );
        $update=DB::table('hr_emp_bonus_sheet')->where('bonus_sheet_code',$request->id)->update($data);
        return 1;
    }

    //Delegation Process
    public function loanDelegationProcess(Request $request){
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
    public function hrLoanApproveList() {
        $slug = 'hr_loan';
        $data['columns'] = array(
            'hr_emp_loan_id',
            'loan_date',
            'loan_type',
            'loan_amount',
            'paid_amount',
            'loan_duration',
            'monthly_payment',
            'due_amount',
            'sys_users.name',
            'sys_users.basic_salary',
            'sys_users.date_of_join',
            'hr_emp_loan.created_by',
            'hr_emp_loan.created_at',
            'loan_status'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'], array('sys_users'));
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_emp_loan_id;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;
        return view('Hr_payroll.disbursement.loan_approval_list', $data);
    }


    //Approved form deligation
    public function hrLoanBulkApproved(Request $request) {
        $codes = $request->codes;
        $comments = 'Loan Bulk Approved';
        $request->merge([
            'slug' => 'hr_loan',
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
                        if($item['status_id'] == 100){
                            DB::table('hr_emp_loan')->where('hr_emp_loan_id', '=', $code)->update(['lock_status'=>1]);
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
}