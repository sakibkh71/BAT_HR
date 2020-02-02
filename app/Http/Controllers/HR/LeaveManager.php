<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\AuditTrailEvent;
use DB;
use Input;
use Redirect;
use Auth;
use Response;
use Session;

use App\Helpers\PdfHelper;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;


class LeaveManager extends Controller {
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    function getEmployeeLeaveType($company_id=''){
        $leave_types = DB::table('hr_yearly_leave_policys')
            ->where('hr_yearly_leave_policys_year',date('Y'))
            ->where('bat_company_id',$company_id)
            ->where('status','Active')->get();
        $html = '';
        if(!empty($leave_types)){
            foreach ($leave_types as $type){
                $html .= "<option value='$type->hr_yearly_leave_policys_name'>$type->hr_yearly_leave_policys_name</option>";
            }
        }
        return $html;
    }
    public function leaveForm($data = ''){
        $this->data['emp_code'] = '';
        if(!empty($data)){
            $data_explode = explode('-', $data);
            if($data_explode[0] == 'l'){
                $leave_id = $data_explode[1];
                if(!empty($leave_id)){
                    $this->data['emp_leave_records'] = self::getUserLeaveRecords('', $leave_id);
                    $this->data['emp_code'] = $this->data['emp_leave_records']->user_code;
                }
            }
            if($data_explode[0] == 'u'){
                $emp_code = $data_explode[1];
                if(!empty($emp_code)){
                    $this->data['emp_code'] = $emp_code;
                }
            }
        }
//        dd($this->data);
        return view('HR.leave_manager.leave_form', $this->data);
    }

    public function getEmployeeLeaveTotal(Request $request){
       $uCode = $request->uid;
       $leave_type = $request->leave_type;

       $uid = DB::table('sys_users')
                ->select('sys_users.id')
                ->where('sys_users.user_code', $uCode)
                ->value('id');

       $total_leave = DB::table('hr_yearly_leave_balances')
                ->select('hr_yearly_leave_balances.balance_leaves')
                ->where('hr_yearly_leave_balances.hr_yearly_leave_balances_year', date('Y'))
                ->where('hr_yearly_leave_balances.sys_users_id', $uid)
                ->where('hr_yearly_leave_balances.hr_yearly_leave_policys_name', $leave_type)
                ->value('balance_leaves');
       echo $total_leave;

    }


    public function getLeavePolicy($year='', $user_id=''){

        $year = $year?$year:date('Y');
        if($user_id){
            return $leave_policys  = DB::table('hr_yearly_leave_balances')
                ->select('hr_yearly_leave_policys_name', 'bat_company_id', 'is_earn_leave', 'policy_days', 'enjoyed_leaves', 'balance_leaves')
            ->where('sys_users_id',$user_id)->where('hr_yearly_leave_balances_year',$year)->get();
        }

        return false;
    }

    public function getUserLeaveRecords($user_id = '', $record_id = '',$posted=''){
        DB::connection()->enableQueryLog();

        $sql = DB::table('hr_leave_records');
        $sql->select('hr_leave_records.hr_leave_records_id',
            'hr_leave_records.sys_users_id',
            'hr_leave_records.application_type',
            'hr_leave_records.leave_types',
            'hr_leave_records.start_date',
            'hr_leave_records.to_date',
            'hr_leave_records.leave_days',
            'hr_leave_records.applied_date',
            'hr_leave_records.approval_date',
            'hr_leave_records.remarks',
            'hr_leave_records.leave_status',
            'sys_status_flows.status_flows_name',
            'sys_users.user_code',
            'sys_users.name',
            'sys_users.user_code',
            'sys_delegation_conf.step_name',
            'c.name as creator_name',
            'b.name as delegation_person_name'
        );
        $sql->leftJoin('sys_users', function ($join) {
            $join->on('sys_users.id', '=', 'hr_leave_records.sys_users_id');
        });
        $sql->leftJoin('sys_status_flows', function ($join) {
            $join->on('sys_status_flows.status_flows_id', '=', 'hr_leave_records.leave_status');
        });
        $sql->leftJoin('sys_users as b', 'b.id', '=', 'hr_leave_records.delegation_person');
        $sql->leftJoin('sys_users as c', 'c.id', '=', 'hr_leave_records.created_by');

        $sql->leftJoin('sys_delegation_conf', function ($join) {
            $join->on('hr_leave_records.delegation_for', '=', 'sys_delegation_conf.delegation_for')
                ->on('hr_leave_records.delegation_ref_event_id', '=', 'sys_delegation_conf.ref_event_id')
                ->on('hr_leave_records.delegation_version', '=', 'sys_delegation_conf.delegation_version')
                ->on('hr_leave_records.delegation_step', '=', 'sys_delegation_conf.step_number');
        });

        $sql->where('hr_leave_records.status','!=', 'Inactive');

        $session_con = (sessionFilter('url','get-emp-leave-history'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $sql->whereRaw($session_con);
        }

        $sql->orderBy('hr_leave_records.hr_leave_records_id', 'desc');
        $sql->groupBy('hr_leave_records.hr_leave_records_id');
        if(!empty($record_id)){
            $sql->where('hr_leave_records.hr_leave_records_id','=', $record_id);
            $result = $sql->first();
            return $result;
        }
        if(!empty($user_id)){
            $sql->where('hr_leave_records.sys_users_id','=', $user_id);
            if(isset($posted['leave_year'])){
                $sql->whereYear('hr_leave_records.start_date','=', $posted['leave_year']);
            }elseif(isset($posted['leave_month'])){
                $sql->where('hr_leave_records.start_date','LIKE', $posted['leave_month'].'%');
            }else{
                $sql->whereYear('hr_leave_records.start_date','=', date('Y'));
            }
            $result = $sql->get()->toArray();
            return $result;
        }
        if(!empty($posted)){
            if(isset($posted['leave_type'][0])){
                $sql->where('hr_leave_records.leave_types', $posted['leave_type']);
            }
            if(isset($posted['emp_list'][0])){
                $sql->whereIn('hr_leave_records.sys_users_id', $posted['emp_list']);
            }
            if(isset($posted['name'])){
                $sql->where('sys_users.name','LIKE', "%$posted[name]%");
            }
            if(isset($posted['approved_date']) && $posted['approved_date']){
                $approved_date = explode(' - ',$posted['approved_date']);
                $sql->where('hr_leave_records.approval_date','>=', $approved_date[0]);
                $sql->where('hr_leave_records.approval_date','<=', $approved_date[1]);
            }
            if(isset($posted['applied_date']) && $posted['applied_date']){
                $applied_date = explode(' - ',$posted['applied_date']);
                $sql->where('hr_leave_records.applied_date','>=', $applied_date[0]);
                $sql->where('hr_leave_records.applied_date','<=', $applied_date[1]);
            }

        }

        //dd(DB::getQueryLog());
        return $sql->get()->toArray();

    }
    public function getEmployeeInfo($user_code = '', $user_id = ''){
        $this->data['success'] = 0;
        if(!empty($user_code) || !empty($user_id)){
            $sql = DB::table('sys_users');
            $sql->select(
                'sys_users.id',
                'sys_users.user_code',
                'sys_users.bat_company_id',
                'sys_users.name',
                'sys_users.email',
                'sys_users.mobile',
                'sys_users.user_image',
                'departments.departments_name',
                'designations.designations_name',
                'branchs.branchs_name',
                'bat_company.company_name as distributor_house',
                'bat_distributorspoint.name as distributor_point',
                'hr_emp_units.hr_emp_unit_name',
                'hr_emp_sections.hr_emp_section_name'
            );
            $sql->leftJoin('departments', function ($join) {
                $join->on('departments.departments_id', '=', 'sys_users.departments_id');
            });
            $sql->leftJoin('designations', function ($join) {
                $join->on('designations.designations_id', '=', 'sys_users.designations_id');
            });
            $sql->leftJoin('bat_company', function ($join) {
                $join->on('bat_company.bat_company_id', '=', 'sys_users.bat_company_id');
            });
            $sql->leftJoin('bat_distributorspoint', function ($join) {
                $join->on('bat_distributorspoint.id', '=', 'sys_users.bat_dpid');
            });
            $sql->leftJoin('hr_emp_units', function ($join) {
                $join->on('hr_emp_units.hr_emp_units_id', '=', 'sys_users.hr_emp_units_id');
            });
            $sql->leftJoin('hr_emp_sections', function ($join) {
                $join->on('hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id');
            });
            $sql->leftJoin('branchs', function ($join) {
                $join->on('branchs.branchs_id', '=', 'sys_users.branchs_id');
            });
            if(empty($user_id)) {
                $sql->where('sys_users.user_code', '=', $user_code);
                $this->data['user_info'] = $sql->first();
                if(!empty($this->data['user_info'])){
                    $this->data['success'] = 1;
                }
                echo json_encode($this->data);
            }else{
                $sql->where('sys_users.id','=', $user_id);
                $this->data['user_info'] = $sql->first();
                return $this->data['user_info'];
            }
        }
    }

    public function getEmployeeLeaveInfo($user_id,$year=''){
        $data['user_id'] = $user_id;
        $data['year'] = $year?$year:date("Y");
        $data['leave_policys'] = self::getLeavePolicy($year,$user_id);

        return view('HR.leave_manager.leave_summary', $data);
    }
    public function saveLeaveInfo(Request $request){
        $post = $request->all();
        $leave_dates = explode(' - ', $post['leave_date']);
        $insert_arr = array(
            'sys_users_id' => $post['user_id'],
            'application_type' => $post['application_type'],
            'leave_types' => $post['leave_type'],
            'start_date' => $leave_dates[0],
            'to_date' => $leave_dates[1],
            'leave_days' => $post['leave_days'],
            'applied_date' => $post['application_date'],
            'approval_date' => isset($post['approved_date'])?$post['approved_date']:null,
            'remarks' => $post['remarks'],
            'leave_status' => 62
        );
        if(isset($post['hr_leave_records_id']) && !empty($post['hr_leave_records_id'])){
            $insert_arr['updated_by'] = Auth::id();
            $insert_arr['updated_at'] = date('Y-m-d h:i:s');
            $update = DB::table('hr_leave_records')->where('hr_leave_records_id','=',$post['hr_leave_records_id'])->update($insert_arr);
            //call audit log
//            AuditTrailEvent::updateForAudit($update, $insert_arr);
            $this->data['insert_id'] = $post['hr_leave_records_id'];
        }else{
            $insert_arr['created_by'] = Auth::id();
            $insert_arr['created_at'] = date('Y-m-d h:i:s');
            $this->data['insert_id'] = DB::table('hr_leave_records')->insertGetId($insert_arr);
        }
        echo json_encode($this->data);
    }
    public function employeeLeaveList(){
//        DB::enableQueryLog();
        $sql = DB::table('sys_users');
        $sql->select(
            'sys_users.id',
            'sys_users.name',
            'sys_users.email',
            'sys_users.mobile',
            'departments.departments_name',
            'designations.designations_name',
            'branchs.branchs_name',
            'hr_emp_units.hr_emp_unit_name',
            'hr_emp_sections.hr_emp_section_name',
            DB::raw('SUM(hr_leave_records.leave_days) AS total_leaves')
        );
        $sql->leftJoin('departments', function ($join) {
            $join->on('departments.departments_id', '=', 'sys_users.departments_id');
        });
        $sql->leftJoin('designations', function ($join) {
            $join->on('designations.designations_id', '=', 'sys_users.designations_id');
        });
        $sql->leftJoin('hr_emp_units', function ($join) {
            $join->on('hr_emp_units.hr_emp_units_id', '=', 'sys_users.hr_emp_units_id');
        });
        $sql->leftJoin('hr_emp_sections', function ($join) {
            $join->on('hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id');
        });
        $sql->leftJoin('branchs', function ($join) {
            $join->on('branchs.branchs_id', '=', 'sys_users.branchs_id');
        });
        $sql->leftJoin('hr_leave_records', function ($join) {
            $join->on('hr_leave_records.sys_users_id', '=', 'sys_users.id');
        });
        $sql->where('sys_users.status','=', 'Active');
        $sql->groupBy('sys_users.id');
        $this->data['user_info'] = $sql->get()->toArray();
//        dd(DB::getQueryLog());
        return view('HR.leave_manager.emp_leave_list', $this->data);
    }

    public function getEmployeeLeaveHistory(Request $request,$type=''){
        $posted = $request->all();
        $this->data['leave_records'] = self::getUserLeaveRecords('','',$posted);

        $this->data['posted'] = [];
        if(!empty($posted)){
            $this->data['posted'] = $posted;
        }

        if($type == 'excel'){

            $file_name = 'Leave History.xlsx';
          $header_array = [
              ['text'=>'#'],
              ['text'=>'Name'],
              ['text'=>'Code'],
              ['text'=>'Applied'],
              ['text'=>'Leave Type'],
              ['text'=>'Leave Date'],
              ['text'=>'Days'],
              ['text'=>'Applied Date'],
              ['text'=>'Approved Date'],
              ['text'=>'Remarks'],
              ['text'=>'Created By'],
              ['text'=>'Delegation Step'],
              ['text'=>'Delegation Person'],
              ['text'=>'Status']
          ];
          $excel_array=[];
          if(!empty($this->data['leave_records'])){
              $sl =1;
              foreach ($this->data['leave_records'] as $i=>$leave_record){
                  $temp=array();
                  $temp['sl']=$sl;
                  $temp['name']=$leave_record->name;
                  $temp['code']=$leave_record->user_code;
                  $temp['applied']=$leave_record->application_type;
                  $temp['leave_type']=$leave_record->leave_types;
                  $temp['leave_date']=toDated($leave_record->start_date)." - ".toDated($leave_record->to_date);
                  $temp['days']=$leave_record->leave_days;
                  $temp['applied_date']=toDated($leave_record->applied_date);
                  $temp['approved_date']=$leave_record->approval_date?toDated($leave_record->approval_date):'N/A';
                  $temp['remarks']=$leave_record->remarks;
                  $temp['created_by']=$leave_record->creator_name;
                  $temp['delegation_step']=$leave_record->step_name;
                  $temp['delegation_person']=$leave_record->delegation_person_name;
                  $temp['status']=$leave_record->status_flows_name;
                $excel_array[]=$temp;
                $sl++;
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

        return view('HR.leave_manager.emp_leave_history', $this->data);
    }

    function deleteLeaveRecord(Request $request){
        $arr = array(
            'status'=>'Inactive',
            'updated_at'=>date("Y-m-d h:i:s"),
            'updated_by'=>Auth::id()
        );
        $update = DB::table('hr_leave_records')->whereIn('hr_leave_records_id',explode(',',$request->record_id))->update($arr);
//        AuditTrailEvent::updateForAudit($update,$arr);
        if($update){
            return response()->json(array('success'=>true));
        }else{
            return response()->json(array('success'=>false));
        }
    }

    //Cancel Leave
    function cancelLeaveRecord(Request $request){
        $arr = array(
            'status'=>'Cancel',
            'leave_status' =>87,
            'updated_at'=>date("Y-m-d h:i:s"),
            'updated_by'=>Auth::id()
        );
        $update = DB::table('hr_leave_records')->whereIn('hr_leave_records_id',explode(',',$request->record_id))->update($arr);
//        AuditTrailEvent::updateForAudit($update,$arr);
        if($update){
            return response()->json(array('success'=>true));
        }else{
            return response()->json(array('success'=>false));
        }
    }

    public function goToLeaveDelegationProcess(Request $request){
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

    public function leaveApprovalList(){
        $slug = 'hr_leave';
        $data['columns'] = array(
            'hr_leave_records.hr_leave_records_id',
            'hr_leave_records.application_type',
            'hr_leave_records.leave_types',
            'hr_leave_records.start_date',
            'hr_leave_records.to_date',
            'hr_leave_records.leave_days',
            'hr_leave_records.applied_date',
            'hr_leave_records.approval_date',
            'hr_leave_records.created_by',
            'hr_leave_records.created_at',
            'hr_leave_records.remarks',
            'sys_users.name'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'],array('sys_users'));
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->hr_leave_records_id;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }
        $data['records'] = $unique_array;
        return view('HR.leave_manager.leave_approval_list',$data);
    }

    function HRLeaveBulkApproved(Request $request){
        $codes = $request->codes;
        $comments = 'Leave Bulk Approved';
        $request->merge([
            'slug' => 'hr_leave',
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
                       $leave_record = DB::table('hr_leave_records')->where('hr_leave_records_id','=',$code)->get()->first();
                       $start_date = $leave_record->start_date;
                       $end_date = $leave_record->to_date;
                        $update = DB::table('hr_emp_attendance')->whereBetween('day_is',[$start_date,$end_date])->update(['daily_status'=>'Lv','in_time'=>null,'out_time'=>null]);
//                        AuditTrailEvent::updateForAudit($update,['daily_status'=>'Lv','in_time'=>null,'out_time'=>null]);
                        $update_arr = array(
                            'approval_date'=>date('Y-m-d'),
                            'approved_by'=>Auth::id()
                        );
                        $update2 = DB::table('hr_leave_records')->where('hr_leave_records_id','=',$code)->update($update_arr);
//                        AuditTrailEvent::updateForAudit($update2,$update_arr);

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

    function HRLeaveBulkDecline(Request $request){
        $codes = $request->codes;
        $comments = 'Leave Decline';
        $request->merge([
            'slug' => 'hr_leave',
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
                        $update_arr = array(
                            'approved_by'=>Auth::id()
                        );
                        $update = DB::table('hr_leave_records')->where('hr_leave_records_id','=',$code)->update($update_arr);
//                        AuditTrailEvent::updateForAudit($update,$update_arr);
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
     * Create Leave Encashment
     */
    public  function leaveEncashmentCreate(Request $request, $id=null){
        if ($id !=null){
            $enhancement = DB::table('hr_leave_encashments')->where('hr_leave_encashments_id', $id)->first();
            if (empty($enhancement)){
                return redirect('404');
            }
            $data['encashment_records'] = $enhancement;
            $emp_info = employeeInfo($enhancement->sys_users_id);
            $data['basic_salary'] = $emp_info->emp_log->basic_salary;
            $data['emp_info'] = $emp_info;
            $request->request->add(['users' => $id]);
        }elseif(isset($request->users)){
            $leaveReport = new LeaveReport();
            $encashment_balance = $leaveReport->leaveEncashmaneBalance($request->users);
            $request->request->add(['net_balance' => $encashment_balance]);
            $emp_info = employeeInfo($request->users);
            $data['basic_salary'] = $emp_info->emp_log->basic_salary;
            $data['emp_info'] = $emp_info;
            if(!isset($request->net_balance)){

            }
        }

        $data['post_data'] = $request->all();
        return view('HR.leave_manager.leave_encashment', $data);
    }

    /*
     * Store Encashment Data
     */
    public function leaveEncashmentStore(Request $request, $id=null){
        $amount =  floatval(($request->basic_salary/30) * $request->encashment_days);

        $insertdata['sys_users_id']               =  $request->user_id;
        $insertdata['encashment_date']            =  $request->encashment_date;
        $insertdata['encashment_days']            =  $request->encashment_days;
        $insertdata['encashment_amount']          =  $amount;
        $insertdata['encashment_note']            =  $request->encashment_note;
        $insertdata['encashment_ballance_days']   =  $request->net_balance;

        if ($id !=null){
            $insertdata['updated_by']                 =  Auth::id();
            $insertdata['updated_at']                 =  date('Y-m-d');
            $update = DB::table('hr_leave_encashments')->where('hr_leave_encashments_id', $id)->update($insertdata);
//            AuditTrailEvent::updateForAudit($update,$insertdata);
            //Session::flash('succ_msg_po_create', 'Employee Grade Edit Successfully');
            return redirect()->route('leave-encashment-create', $id)->with('success', 'Employee Encashment Edit Successfully.');
        }else{
            $insertdata['created_by']                 =  Auth::id();
            $insertdata['created_at']                 =  date('Y-m-d');
            DB::table('hr_leave_encashments')->insert($insertdata);
            $id = DB::getPdo()->lastInsertId();;
            //Session::flash('succ_msg_po_create', 'Employee Grade Edit Successfully');
            return redirect()->route('leave-encashment-create', $id)->with('success', 'Employee Encashment add Successfully.');
        }

    }

    /*
     * Encashment List
     */
    public function leaveEncashmentHistory(Request $request){
        $sql = DB::table('hr_leave_encashments')
            ->join('sys_users', 'hr_leave_encashments.sys_users_id', '=', 'sys_users.id')
            ->select('hr_leave_encashments.*', 'sys_users.user_code', 'sys_users.name');

        if (isset($request->users_id) && $request->users_id !=''){
            $sql->where('hr_leave_encashments_id', $request->users_id);
        }

        if (isset($request->date_range) && $request->date_range !=''){
            $range = explode(" - ", $request->date_range);
            $sql->whereBetween('encashment_date', $request->users_id);
        }
        $data['encashment_records'] = $sql->get();

        return view('HR.leave_manager.leave_encashment_list', $data);

    }

    /*
     * Leave Policy Apply for New Year
     */
    public function leavePolicyApplyNewYear(Request $request){
        $psot = $request->all();
        $data['posted'] = $psot;
        if(isset($request->leave_year) && $request->bat_company_id){
            $sql = DB::table('hr_yearly_leave_policys')
                ->join('bat_company', 'hr_yearly_leave_policys.bat_company_id', '=', 'bat_company.bat_company_id')
                ->select('bat_company.company_name', 'hr_yearly_leave_policys.hr_yearly_leave_policys_year', 'hr_yearly_leave_policys.hr_yearly_leave_policys_name', 'hr_yearly_leave_policys.hr_yearly_leave_policys_id', 'hr_yearly_leave_policys.policy_leave_days', 'hr_yearly_leave_policys.is_carry');

            if (!empty($request->bat_company_id)){
                $sql->where('hr_yearly_leave_policys.bat_company_id', '=', $request->bat_company_id);
            }

            if (!empty($request->leave_year)){
                $sql->where('hr_yearly_leave_policys.hr_yearly_leave_policys_year',$request->leave_year);
            }
            $data['result'] =  $sql->get();
        }

        return view('HR.leave_manager.leave_policy_apply_for_new_year', $data);
    }

    /*
     * Apply Leave Policy
     */

    public function applyLeavePolicy(Request $request){
        $apply_cat = isset($request->apply_cat)?array_filter($request->apply_cat):[];
        if(isset($request->leave_year) && isset($request->apply_cat) && isset($request->aply_for)){
            $leave_data = DB::table('hr_yearly_leave_policys')->where('bat_company_id', '=', $request->bat_company)->where('hr_yearly_leave_policys_year',$request->leave_year)->get();
            foreach ($leave_data as $leave) {

                foreach ($apply_cat as $cat){
                    $check_data = DB::table('hr_yearly_leave_policys')
                        ->where('hr_yearly_leave_policys_year', '=', $request->aply_for)
                        ->where('bat_company_id', '=', $cat)
                        ->where('hr_yearly_leave_policys_name', '=', $leave->hr_yearly_leave_policys_name)->first();

                    $insertData = [];
                    $insertData['hr_yearly_leave_policys_year'] = $request->aply_for;
                    $insertData['hr_yearly_leave_policys_name'] = $leave->hr_yearly_leave_policys_name;
                    $insertData['bat_company_id'] = $cat;
                    $insertData['policy_leave_days'] = $leave->policy_leave_days;
                    $insertData['is_carry'] = $leave->is_carry;
                    $insertData['is_earn_leave'] = $leave->is_earn_leave;
                    $insertData['is_carry'] = $leave->is_carry;
                    $insertData['status'] = $leave->status;

                    if(!empty($check_data)){
                        $insertData['updated_by'] =  Auth::user()->id;
                        $insertData['updated_at'] = date('Y-m-d');

                        $update = DB::table('hr_yearly_leave_policys')
                            ->where('hr_yearly_leave_policys_id', '=', $check_data->hr_yearly_leave_policys_id)
                            ->where('hr_yearly_leave_policys_year', '=', $request->aply_for)
                            ->where('hr_yearly_leave_policys_year', '=', $leave->hr_yearly_leave_policys_year)
                            ->where('hr_yearly_leave_policys_name', '=', $leave->hr_yearly_leave_policys_name)->update($insertData);
//                        AuditTrailEvent::updateForAudit($update,$insertData);

                    }else{
                        $insertData['created_by'] = Auth::user()->id;
                        $insertData['created_at'] = date('Y-m-d');
                        DB::table('hr_yearly_leave_policys')->insert($insertData);
                    }
                }
            }

            DB::connection()->enableQueryLog();
            $return_data = DB::table('hr_yearly_leave_policys')
                ->join('bat_company', 'hr_yearly_leave_policys.bat_company_id', '=', 'bat_company.bat_company_id')
                ->select('bat_company.company_name', 'hr_yearly_leave_policys.hr_yearly_leave_policys_name', 'hr_yearly_leave_policys.hr_yearly_leave_policys_year', 'hr_yearly_leave_policys.hr_yearly_leave_policys_id','hr_yearly_leave_policys.policy_leave_days', 'hr_yearly_leave_policys.is_carry')
                ->where('hr_yearly_leave_policys.hr_yearly_leave_policys_year',$request->aply_for)
                ->whereIn('hr_yearly_leave_policys.bat_company_id', $apply_cat)
                ->get();

            //dd( DB::getQueryLog() );

            return response()->json([
                'status' => 'success',
                'data' => $return_data,
            ]);

        }else{
            return response()->json([
                'status' => 'error',
            ]);
        }
    }


    /*
     * HR Employee Leave Report
     ----------------------------------------------------*/
    public function hrEmpLeaveReport(Request $request, $type=null){
        $data['post_data'] = $request->all();
        //$userInfo = $this->getEmployeeInfo( null,3883);
        if ($type == 'pdf' && $request->users !=''){
            //$userInfo = $this->getEmployeeInfo( null, $request->users);

            $year = $request->year?$request->year:date("Y");

            $data['user_id'] = $request->users;
            $data['year'] = $year;
            $data['leave_policys'] = self::getLeavePolicy($year,$request->users);

            $data['report_title'] = 'Leave Report - '. $year;
            $data['filename'] = 'leave_report_pdf';
            $data['orientation'] = "P";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.leave_report.leave_report_pdf';
            PdfHelper::exportPdf($view,$data);
        }else if(!empty($data['post_data'])){
            $data['emp_info'] = employeeInfo($request->users);
            $data['leave_summery'] = $this->getEmployeeLeaveInfo($request->users,'');
        }

        return view('HR.leave_report.leave_report', $data);

    }


    //Check If Pending Leave exist
    function checkPendingLeaveExist(Request $request){
        $data = DB::table('hr_leave_records')
            ->join('sys_users', 'sys_users.id', '=', 'hr_leave_records.sys_users_id')
            ->where('sys_users.user_code', $request->user_code)
            ->where('hr_leave_records_id', '!=', $request->leave_id)
            ->whereIn('leave_status', [62,63])
            ->where('hr_leave_records.status', '=', 'Active')
            ->count();

        if (!empty($data)){
            return response()->json([
                'pending' => 'yes',
            ]);
        }else{
            return response()->json([
                'pending' => 'no',
            ]);
        }
    }

}
