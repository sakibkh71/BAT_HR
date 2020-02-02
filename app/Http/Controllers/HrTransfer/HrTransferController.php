<?php

namespace App\Http\Controllers\HrTransfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;

use App\Helpers\PdfHelper;
class HrTransferController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function transfer(Request $request){
        $post = $request->all();
        $employeeInfo=DB::table('hr_employee_record_logs');
        $employeeInfo ->select(
            'sys_users.id',
            'sys_users.name',
            'sys_users.user_code',
            'branchs.branchs_name',
            'hr_emp_sections.hr_emp_section_name',
            'hr_emp_units.hr_emp_unit_name',
            'departments.departments_name',
            'designations.designations_name',
            'tbranchs.branchs_name as tbranchs_name',
            'thr_emp_sections.hr_emp_section_name as thr_emp_section_name',
            'thr_emp_units.hr_emp_unit_name as thr_emp_unit_name',
            'tdepartments.departments_name as tdepartments_name',
            'tdesignations.designations_name as tdesignations_name',
            'c.name as creator_name','sys_status_flows.status_flows_name',
            'sys_delegation_conf.step_name','b.name as delegation_person_name',
            'hr_employee_record_logs.*'
        );
        $employeeInfo->join('sys_users','sys_users.id','=','hr_employee_record_logs.sys_users_id');
        // current information
        $employeeInfo->join('branchs','branchs.branchs_id','=','sys_users.branchs_id');
        $employeeInfo->join('hr_emp_sections','hr_emp_sections.hr_emp_sections_id','=','sys_users.hr_emp_sections_id');
        $employeeInfo->join('hr_emp_units','hr_emp_units.hr_emp_units_id','=','sys_users.hr_emp_units_id');
        $employeeInfo->join('departments','departments.departments_id','=','sys_users.departments_id');
        $employeeInfo->join('designations','designations.designations_id','=','sys_users.designations_id');

        // transfer information
        $employeeInfo->join('branchs as tbranchs','tbranchs.branchs_id','=','hr_employee_record_logs.branchs_id');
        $employeeInfo->join('hr_emp_sections as thr_emp_sections','thr_emp_sections.hr_emp_sections_id','=','hr_employee_record_logs.hr_emp_sections_id');
        $employeeInfo->join('hr_emp_units as thr_emp_units','thr_emp_units.hr_emp_units_id','=','hr_employee_record_logs.hr_emp_units_id');
        $employeeInfo->join('departments as tdepartments','tdepartments.departments_id','=','hr_employee_record_logs.departments_id');
        $employeeInfo->join('designations as tdesignations','tdesignations.designations_id','=','hr_employee_record_logs.designations_id');

        $employeeInfo->leftJoin('sys_users as b', 'b.id', '=', 'hr_employee_record_logs.delegation_person');
        $employeeInfo->leftJoin('sys_users as c', 'c.id', '=', 'hr_employee_record_logs.created_by');
        $employeeInfo->join('sys_status_flows', 'sys_status_flows.status_flows_id', '=', 'hr_employee_record_logs.hr_transfer_status');
        $employeeInfo->leftJoin('sys_delegation_conf', function ($join) {
            $join->on('hr_employee_record_logs.delegation_for', '=', 'sys_delegation_conf.delegation_for')
                ->on('hr_employee_record_logs.delegation_ref_event_id', '=', 'sys_delegation_conf.ref_event_id')
                ->on('hr_employee_record_logs.delegation_version', '=', 'sys_delegation_conf.delegation_version')
                ->on('hr_employee_record_logs.delegation_step', '=', 'sys_delegation_conf.step_number');
        });
        $employeeInfo->where('hr_employee_record_logs.record_type','=','transfer');
        $employeeInfo->where('hr_employee_record_logs.status', '=', 'Active');
        $employeeInfo->where('hr_employee_record_logs.created_by', '=', Auth::id());
        if (!empty($post['date_range'])) {
            $range = explode(" - ", $post['date_range']);
            $employeeInfo->whereBetween('hr_employee_record_logs.applicable_date', $range);
        }
        if (!empty($post['transfer_approval_status'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_transfer_status', $post['transfer_approval_status']);
        }
        if (!empty($post['hr_emp_categorys'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_emp_units_id', $post['hr_emp_categorys']);
        }
        if (!empty($post['hr_emp_units'])) {
            $employeeInfo->whereIn('hr_employee_record_logs.hr_emp_units_id', $post['hr_emp_units']);
        }

        $employeeInfo->orderBy('hr_employee_record_logs.hr_employee_record_logs_id','DESC');

        $data['employeeList'] = $employeeInfo->get();
        $data['hr_emp_units'] = $request->hr_emp_units?$request->hr_emp_units:'';
        $data['hr_emp_categorys'] = $request->hr_emp_categorys?$request->hr_emp_categorys:'';
        $data['transfer_approval_status'] = $request->transfer_approval_status?$request->transfer_approval_status:'';
        $data['date_range'] = $request->date_range?$request->date_range:'';

        return view('HrTransfer.transferList',$data);
    }
    public function transferForm(Request $request){
        $data['log_id'] = $request->log_id;
        return view('HrTransfer.transfer',$data);
    }
    public function transferEmployees(Request $request){

        $sql=DB::table('sys_users');
        $sql ->select('sys_users.id','sys_users.name','sys_users.applicable_date',
            'sys_users.designations_id','branchs.branchs_id','departments.departments_id','hr_emp_sections.hr_emp_sections_id','hr_emp_units.hr_emp_units_id',
            'sys_users.user_code','branchs.branchs_name','hr_emp_sections.hr_emp_section_name','hr_emp_units.hr_emp_unit_name',
            'departments.departments_name','designations.designations_name', 'hr_emp_categorys.hr_emp_category_name');
        $sql->join('branchs','branchs.branchs_id','=','sys_users.branchs_id');
        $sql->join('hr_emp_sections','hr_emp_sections.hr_emp_sections_id','=','sys_users.hr_emp_sections_id');
        $sql->join('hr_emp_units','hr_emp_units.hr_emp_units_id','=','sys_users.hr_emp_units_id');
        $sql->join('departments','departments.departments_id','=','sys_users.departments_id');
        $sql->leftJoin('designations','designations.designations_id','=','sys_users.designations_id');
        $sql->leftJoin('hr_emp_categorys','sys_users.hr_emp_categorys_id','=','hr_emp_categorys.hr_emp_categorys_id');

        if($request->emp_list){
            $sql->whereIn('sys_users.id',$request->emp_list);
        }
        if($request->hr_emp_category_id){
            $sql->whereIn('sys_users.hr_emp_categorys_id',$request->hr_emp_category_id);
        }
        if($request->hr_emp_grades_list){
            $sql->whereIn('sys_users.hr_emp_grades_id',$request->hr_emp_grades_list);
        }
        if($request->branchs_id){
            $sql->whereIn('sys_users.branchs_id',$request->branchs_id);
        }
        if($request->hr_emp_departments){
            $sql->whereIn('sys_users.departments_id',$request->hr_emp_departments);
        }
        $data['employeeInfo'] = $sql->get();
        $emp_item =  view('HrTransfer.hr_transfer_list_view',$data)->render();
        return Response::json(['emp_list' => $emp_item]);
    }
    function storeTransfer(Request $request){
        $emp_id = $request->emp_id;
        $new_branchs = $request->new_branchs;
        $new_departments = $request->new_departments;
        $new_hr_emp_sections = $request->new_hr_emp_sections;
        $new_hr_emp_units = $request->new_hr_emp_units;
        $new_designation = $request->new_designation;
        $data_arr_log = [];

        foreach ($request->emp_id as $i=>$item) {
            $user_info = DB::table('sys_users')->where('id', $emp_id[$i])->first();

            $user_info_arr = array();

            $user_info_arr['branchs_id'] = !empty($new_branchs[$i])?$new_branchs[$i]:$user_info->branchs_id;
            $user_info_arr['designations_id'] = !empty($new_designation[$i])?$new_designation[$i]:$user_info->designations_id;
            $user_info_arr['hr_emp_sections_id'] = !empty($new_hr_emp_sections[$i])?$new_hr_emp_sections[$i]:$user_info->hr_emp_sections_id;
            $user_info_arr['departments_id'] = !empty($new_departments[$i])?$new_departments[$i]:$user_info->departments_id;
            $user_info_arr['hr_emp_units_id'] = !empty($new_hr_emp_units[$i])?$new_hr_emp_units[$i]:$user_info->hr_emp_units_id;
            $user_info_arr['applicable_date'] = !empty($request->applicable_date)?$request->applicable_date:$user_info->applicable_date;
            $user_info_arr['hr_emp_grades_id'] = !empty($user_info->hr_emp_grades_id)?$user_info->hr_emp_grades_id:$user_info->hr_emp_grades_id;
            $user_info_arr['sys_users_id'] = $user_info->id;
            $user_info_arr['record_type'] = 'transfer';
            $user_info_arr['basic_salary'] = $user_info->basic_salary;
            $user_info_arr['house_rent'] =  $user_info->house_rent;
            $user_info_arr['house_rent_amount'] = $user_info->house_rent_amount;
            $user_info_arr['min_medical'] = $user_info->min_medical;
            $user_info_arr['min_food'] = $user_info->min_food;
            $user_info_arr['min_tada'] = $user_info->min_tada;
            $user_info_arr['gross_salary'] = $user_info->min_gross;
            $user_info_arr['previous_gross'] = $user_info->min_gross;
            $user_info_arr['hr_transfer_status'] = 58;
            $user_info_arr['created_by'] = Auth::id();
            $user_info_arr['created_at'] = date('Y-m-d h:i:s');

            $data_arr_log[] = $user_info_arr;
        }

        $insert = DB::table('hr_employee_record_logs')->insert($data_arr_log);

        if($insert){
            return response()->json(array('success'=>true));
        }else{
            return response()->json(array('success'=>false));
        }
    }

    function transferLetter($log_id){
        $sql=DB::table('hr_employee_record_logs');
        $sql ->select('sys_users.id','sys_users.name','sys_users.applicable_date',
            'sys_users.user_code','branchs.branchs_name','hr_emp_sections.hr_emp_section_name','hr_emp_units.hr_emp_unit_name',
            'departments.departments_name','designations.designations_name','hr_employee_record_logs.*');
        $sql->join('sys_users','sys_users.id','=','hr_employee_record_logs.sys_users_id');
        $sql->join('branchs','branchs.branchs_id','=','hr_employee_record_logs.branchs_id');
        $sql->join('hr_emp_sections','hr_emp_sections.hr_emp_sections_id','=','hr_employee_record_logs.hr_emp_sections_id');
        $sql->join('hr_emp_units','hr_emp_units.hr_emp_units_id','=','hr_employee_record_logs.hr_emp_units_id');
        $sql->join('departments','departments.departments_id','=','hr_employee_record_logs.departments_id');
        $sql->join('designations','designations.designations_id','=','hr_employee_record_logs.designations_id');
        $sql->where('hr_employee_record_logs.hr_employee_record_logs_id',$log_id);
        $data['emp_log'] = $sql->first();

        $data['report_title'] = 'Letter of Transfer';
        $data['filename'] = 'transfer_letter';
        $data['orientation'] = "P";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view='HrTransfer.transferLetterView';
        PdfHelper::exportPdf($view,$data);
    }


    function editTransferRecord(Request $request){

        $sql=DB::table('hr_employee_record_logs');
        $sql ->select('sys_users.id','sys_users.name','sys_users.applicable_date',
            'sys_users.user_code','sys_users.designations_id','branchs.branchs_id','departments.departments_id','hr_emp_sections.hr_emp_sections_id','hr_emp_units.hr_emp_units_id',
            'sys_users.user_code','branchs.branchs_name','hr_emp_sections.hr_emp_section_name','hr_emp_units.hr_emp_unit_name',
            'departments.departments_name','designations.designations_name','hr_employee_record_logs.*');
        $sql->join('sys_users','sys_users.id','=','hr_employee_record_logs.sys_users_id');
        $sql->join('branchs','branchs.branchs_id','=','sys_users.branchs_id');
        $sql->join('hr_emp_sections','hr_emp_sections.hr_emp_sections_id','=','sys_users.hr_emp_sections_id');
        $sql->join('hr_emp_units','hr_emp_units.hr_emp_units_id','=','sys_users.hr_emp_units_id');
        $sql->join('departments','departments.departments_id','=','sys_users.departments_id');
        $sql->leftJoin('designations','designations.designations_id','=','sys_users.designations_id');
        $sql->whereIn('hr_employee_record_logs.hr_employee_record_logs_id',explode(',',$request->log_id));
        $sql->orderBy('hr_employee_record_logs_id','DESC');
        $employeeInfo = $sql->get();
        $emp_item = '';
        $existing_selected_emp = [];
        if(!empty($employeeInfo)) {
            foreach ($employeeInfo as $key => $item) {
                // print_r($item->id);
                array_push($existing_selected_emp, $item->id);
                $emp_item .= '<tr id="row'.$item->id.'">';
                $emp_item .= '<td class="text-left"><input type="hidden" name="emp_id" class="emp_id" value="'.$item->id.'">' . $item->name .'</td>';
                $emp_item .= '<td class="text-left">' . $item->user_code .'</td>';
                $emp_item .= '<td class="text-left">' . toDated($item->applicable_date) .'</td>';
                $emp_item .= '<td class="text-left">' . $item->branchs_name .'</td>';
                $emp_item .= '<td class="text-left">' . $item->departments_name .'</td>';
                $emp_item .= '<td class="text-left">' . $item->hr_emp_section_name .'</td>';
                $emp_item .= '<td class="text-left">' . $item->hr_emp_unit_name .'</td>';
                $emp_item .= '<td class="text-left">' . $item->designations_name .'</td>';
                $emp_item .= '<td class="text-left change_area2"><div class="form-group"> ' . __combo('branchs', array('selected_value' => $item->branchs_id,'attributes'=>array('class'=>'form-control multi new_branchs','required'=>'required'))) . '</div></td>';
                $emp_item .= '<td class="text-left change_area2"><div class="form-group"> ' . __combo('departments', array('selected_value' => $item->departments_id,'attributes'=>array('class'=>'form-control multi new_departments','required'=>'required'))) . '</div></td>';
                $emp_item .= '<td class="text-left change_area2"><div class="form-group"> ' . __combo('hr_emp_sections', array('selected_value' => $item->hr_emp_sections_id,'attributes'=>array('class'=>'form-control multi new_hr_emp_sections','required'=>'required'))) . '</div></td>';
                $emp_item .= '<td class="text-left change_area2"><div class="form-group"> ' . __combo('hr_emp_units', array('selected_value' => $item->hr_emp_units_id,'attributes'=>array('class'=>'form-control multi new_hr_emp_units','required'=>'required'))) . '</div></td>';
                $emp_item .= '<td class="text-left change_area2"><div class="form-group"> ' . __combo('designations', array('selected_value' => $item->designations_id,'attributes'=>array('class'=>'form-control multi new_designation','required'=>'required'))) . '</div></td>';
                $emp_item .= '<td></td>';

            }
            $existing_selected_emp = implode(',', $existing_selected_emp);
        }

        return Response::json(['emp_list' => $emp_item, 'existing_selected_emp' => $existing_selected_emp]);
    }

    function updateTransfer(Request $request){
        $new_branchs = $request->new_branchs;
        $new_departments = $request->new_departments;
        $new_hr_emp_sections = $request->new_hr_emp_sections;
        $new_hr_emp_units = $request->new_hr_emp_units;
        $new_designation = $request->new_designation;

        if(sizeof($request->log_id)>0) {
            foreach ($request->log_id as $i => $item) {
                $user_info_arr = array(
                    'branchs_id' => $new_branchs[$i],
                    'designations_id' => $new_designation[$i],
                    'hr_emp_sections_id' => $new_hr_emp_sections[$i],
                    'departments_id' => $new_departments[$i],
                    'hr_emp_units_id' => $new_hr_emp_units[$i],
                    'applicable_date' => $request->applicable_date,
                    'record_type' => 'transfer',
                    'hr_transfer_status' => 58,
                    'updated_by' => Auth::id(),
                    'updated_at' => date('Y-m-d h:i:s')
                );
                $update = DB::table('hr_employee_record_logs')->where('hr_employee_record_logs_id', $item)->update($user_info_arr);
            }
            if ($update) {
                return response()->json(array('success' => true));
            } else {
                return response()->json(array('success' => false));
            }
        }
        return response()->json(array('success'=>false));
    }


    // deligation process

    public function goToHRDelegationProcess(Request $request){
        $post = $request->all();
        $result = goToDelegationProcess($post);
        $success_count = 0;
        $failed_count = 0;
        $failed_cause = '';

        if(isset($result)){
            foreach($result as $data){
                foreach($data['data'] as $result_key=>$result_code){
                    if($result_code['mode'] == 'Success'){
                        $success_count++;
                    }else{
                        $failed_count++;
                        $failed_cause .= $result_key.' - '.$result_code['msg'].'<br/>';
                    }
                }

            }
        }
        $return_result = "Total Success ".$success_count."<br/>Total Failed ".$failed_count."<br/>".$failed_cause;

        return $return_result;
    }


    public function transferApprovalList(){
        $slug = 'hr_tfr';
        $data['columns'] = array(
            'hr_employee_record_logs_id',
            'branchs.branchs_name',
            'hr_emp_sections.hr_emp_section_name',
            'hr_emp_units.hr_emp_unit_name',
            'departments.departments_name',
            'designations.designations_name',
            'hr_employee_record_logs.applicable_date',
            'sys_users.name',
            'hr_employee_record_logs.created_by',
            'hr_employee_record_logs.created_at'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'],array('sys_users','branchs','hr_emp_sections','hr_emp_units','departments','designations'));
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_employee_record_logs_id;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;
        return view('HrTransfer.transfer_approval_list',$data);
    }

    function HRTransferBulkApproved(Request $request){
        $codes = $request->codes;
        $comments = 'Transfer Bulk Approved';
        $request->merge([
            'slug' => 'hr_tfr',
            'comments'=> $comments,
            'additional_data'=> ''
        ]);
        $post = $request->all();
        $result = goToDelegationProcess($post);
        if ($result) {
            $resultArray = json_decode($result, true);
            $sucs_msg = [];
            $fail_msg = [];
            foreach ($resultArray as $item){
                foreach ($item['data'] as $code => $prc_item){
                    if($prc_item['mode'] == 'Success'){
                        $increment_info = DB::table('hr_employee_record_logs')->where('hr_employee_record_logs_id','=',$code)->first();
                        $update_arr = array(
                            'applicable_date'=>$increment_info->applicable_date,
                            'designations_id'=>$increment_info->designations_id,
                            'departments_id'=>$increment_info->departments_id,
                            'branchs_id'=>$increment_info->branchs_id,
                            'hr_emp_units_id'=>$increment_info->hr_emp_units_id,
                            'hr_emp_sections_id'=>$increment_info->hr_emp_sections_id
                        );
                        DB::table('sys_users')->where('id','=',$increment_info->sys_users_id)->update($update_arr);
                        $sucs_msg[$code] = $prc_item['msg'];
                    }else{
                        $fail_msg[$code] = $prc_item['msg'].' for '.$code;
                    }
                }
            }
            return Response::json(['sucs_msg'=>$sucs_msg, 'fail_msg'=>$fail_msg]);
        } else {
            return 'There is no return from delegation';
        }
    }

    /*
     * Employee Transfer PDF
     */
    public function HRTransferListPdf(Request $request){
       // dd($request->all());
        $sql=DB::table('sys_users');
        $sql ->select('sys_users.id','sys_users.name','sys_users.applicable_date',
            'sys_users.designations_id','branchs.branchs_id','departments.departments_id','hr_emp_sections.hr_emp_sections_id','hr_emp_units.hr_emp_units_id',
            'sys_users.user_code','branchs.branchs_name','hr_emp_sections.hr_emp_section_name','hr_emp_units.hr_emp_unit_name','hr_emp_grades.hr_emp_grade_name',
            'departments.departments_name','designations.designations_name','hr_emp_categorys.hr_emp_category_name', 'branchs.branchs_name');
        $sql->join('branchs','branchs.branchs_id','=','sys_users.branchs_id');
        $sql->join('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','sys_users.hr_emp_grades_id');
        $sql->join('hr_emp_sections','hr_emp_sections.hr_emp_sections_id','=','sys_users.hr_emp_sections_id');
        $sql->join('hr_emp_units','hr_emp_units.hr_emp_units_id','=','sys_users.hr_emp_units_id');
        $sql->join('departments','departments.departments_id','=','sys_users.departments_id');
        $sql->leftJoin('designations','designations.designations_id','=','sys_users.designations_id');
        $sql->leftJoin('hr_emp_categorys','sys_users.hr_emp_categorys_id','=','hr_emp_categorys.hr_emp_categorys_id');

        if($request->emp_ids){
            $emp_list = explode(", ",$request->emp_ids);
            $sql->whereIn('sys_users.id',$emp_list);
        }
        if($request->hr_emp_category_id){
            $sql->whereIn('sys_users.hr_emp_categorys_id',$request->hr_emp_category_id);
        }
        if($request->hr_emp_grades_list){
            $sql->whereIn('sys_users.hr_emp_grades_id',$request->hr_emp_grades_list);
        }
        if($request->branchs_id){
            $sql->whereIn('sys_users.branchs_id',$request->branchs_id);
        }
        if($request->hr_emp_departments){
            $sql->whereIn('sys_users.departments_id',$request->hr_emp_departments);
        }

        $data['employeeList'] = $sql->get();
        $empArray = $data['employeeList']->toArray();
        //dd($empArray);

        $data['branchs'] = array_unique(array_column($empArray, 'branchs_name'));
        $data['categories'] = array_unique(array_column($empArray, 'hr_emp_category_name'));
        $data['departments'] = array_unique(array_column($empArray, 'departments_name'));
        $data['grades'] = array_unique(array_column($empArray, 'hr_emp_grade_name'));

        $data['report_title'] = 'Employee Transfer';
        $data['filename'] = 'transfer_list';
        $data['orientation'] = "L";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view='HrTransfer.transfer_list_pdf';
        PdfHelper::exportPdf($view,$data);
    }
}