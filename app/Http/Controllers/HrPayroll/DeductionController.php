<?php

namespace App\Http\Controllers\HrPayroll;

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

class DeductionController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    function deductionList(){
        return view('Hr_payroll.deduction.list');
    }

    function newDeductionEntry($deduction_id=null){
        $data = [];
        if($deduction_id){
            $data['row'] = DB::table('hr_emp_salary_deduction')->where('hr_emp_salary_deduction_id',$deduction_id)->first();
        }
        return view('Hr_payroll.deduction.deduction_entry',$data);
    }

    function employeeDeductionInfo($sys_users_id){
        $sql = DB::table('sys_users');
        $sql->selectRaw('min_gross,max_variable_salary,ifnull(sum(due_amount),0) as previous_loan_due');
        $sql->leftJoin('hr_emp_loan',function ($join){
            $join->on('sys_users.id','=','hr_emp_loan.sys_users_id');
            $join->where('hr_emp_loan.status','=','Active');
        });
        $sql->where('sys_users.id',$sys_users_id);
        $emp_info = $sql->first();

        $deduction_data = DB::table('hr_emp_salary_deduction')->select('deduction_date','deduction_amount')->where('sys_users_id',$sys_users_id)->where('deduction_status','!=',113)->first();
        if(!empty($deduction_data)){
            $deduction_check = $deduction_data;
        }else{
            $deduction_check = 0;
        }

        $arr = [
            'emp_info' => $emp_info,
            'deduction_check' => $deduction_check
        ];
        //echo "<pre>"; print_r($deduction_check); exit;
        return response()->json($arr);
    }

    function deductionEntrySave(Request $request){
        //echo "<pre>"; print_r($request); exit;
        $form_data = $request->except(['hr_emp_deduction_id','_token']);
        $emp_info = DB::table('sys_users')->where('id',$request->sys_users_id)->first();

        if($request->hr_emp_deduction_id){
            $form_data['bat_dpid'] = $emp_info->bat_dpid;
            $form_data['deduction_amount'] = $request->deduction_amount;
            $form_data['deduction_date'] = $request->deduction_date;
            $form_data['deduction_status'] = 110;
            $form_data['updated_at'] = dTime();
            $form_data['updated_by'] = Auth::id();
            DB::table('hr_emp_salary_deduction')->where('hr_emp_salary_deduction_id',$request->hr_emp_deduction_id)->update($form_data);
        }else {
            $form_data['bat_dpid'] = $emp_info->bat_dpid;
            $form_data['sys_users_id'] = $request->sys_users_id;
            $form_data['deduction_amount'] = $request->deduction_amount;
            $form_data['deduction_date'] = $request->deduction_date;
            $form_data['deduction_status'] = 110;
            $form_data['created_at'] = dTime();
            $form_data['created_by'] = Auth::id();
            DB::table('hr_emp_salary_deduction')->insert($form_data);
        }
        return response()->json([
            'success'=>true,
        ]);
    }

    public function deductionDelete(Request $request){
        $form_data['status'] = 'Inactive';
        $form_data['updated_at'] = dTime();
        $form_data['updated_by'] = Auth::id();
        DB::table('hr_emp_salary_deduction')->where('hr_emp_salary_deduction_id',$request->hr_emp_deduction_id)->update($form_data);
        return response()->json([
            'success'=>true,
        ]);
    }

    //Delegation Process
    public function DeductionDelegationProcess(Request $request){
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
    public function hrDeductionApproveList() {
        $slug = 'hr_deducti';
        $data['columns'] = array(
            'hr_emp_salary_deduction_id',
            'deduction_date',
            'deduction_amount',
            'sys_users.name',
            'sys_users.basic_salary',
            'sys_users.date_of_join',
            'hr_emp_salary_deduction.created_by',
            'hr_emp_salary_deduction.created_at',
            'deduction_status'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'], array('sys_users'));
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_emp_salary_deduction_id;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;
        return view('Hr_payroll.deduction.deduction_approval_list', $data);
    }

    //Approved form deligation
    public function hrDeductionBulkApproved(Request $request) {
        $codes = $request->codes;
        $comments = 'Loan Bulk Approved';
        $request->merge([
            'slug' => 'hr_deducti',
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