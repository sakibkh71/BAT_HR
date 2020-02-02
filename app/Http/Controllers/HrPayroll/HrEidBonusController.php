<?php

namespace App\Http\Controllers\HrPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;
use App\Helpers\PdfHelper;
use App\Models\HR\Employee;
use App\Models\HR\Bonus;
use App\Models\HR\BonusPolicy;
use App\Models\HR\Company;

class HrEidBonusController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    function generateBonusSheet(Request $request){
        $users =Employee::emp();
//dd(Employee::all());

        $posts = $request->all();
        $data['posted'] = $posts;
        $eligible_date = isset($posts['eligible_date'])?$posts['eligible_date']:date('Y-m-d');
        $data['eligible_date'] = $eligible_date;
        $data['bonus_based_on'] = isset($posts['bonus_based_on'])?$posts['bonus_based_on']:'basic';
        $data['bonus_eligible_based_on'] = isset($posts['bonus_eligible_based_on'])?$posts['bonus_eligible_based_on']:'date_of_join';
        if($posts){
            $sql = Employee::with('employeeDepartment','employeeDesignation','employeeGrade','company');
            $sql->where('is_employee',1);
            $sql->where('status','Active');
            $sql->whereRaw("bat_dpid IN (select bat_dpid from hr_emp_bonus_sheet where bonus_sheet_code='$request->bonus_sheet_code')");

            if($request->hr_emp_grades_list){
                $sql->whereIn('hr_emp_grades_id',$request->hr_emp_grades_list);
            }
            if($request->designations_id){
                $sql->whereIn('designations_id',$request->designations_id);
            }
            $session_con = (sessionFilter('url','hr-emp-bonus-generate'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            if($session_con){
                $sql->whereRaw($session_con);
            }
            $employeeList =$sql->get();

            foreach($employeeList as $emp){
                if($request->bonus_policy_type == 'Manual'){
                    $manual_data = array(
                        'bonus_eligible_based_on' => $request->bonus_eligible_based_on??'date_of_join',
                        'bonus_based_on' => $request->bonus_based_on??0,
                    );
                    $bonus_info = bonus_policy_manual($emp,$eligible_date,$manual_data);
                }else{
                    $bonus_info = bonus_policy($emp,$eligible_date);
                }

                $emp->earn_bonus = $bonus_info['bonus_amount'];
                $emp->bonus_amount = $bonus_info['bonus_amount'];
                $emp->bonus_policy = $bonus_info['bonus_policy'];
                $emp->bonus_policys_id = $bonus_info['bonus_policys_id'];
                $emp->bonus_eligible_based_on = $bonus_info['bonus_eligible_based_on'];
                $emp->bonus_based_on = $bonus_info['bonus_based_on'];
            }

            $data['employeeList'] = $employeeList;

        }

        return view('Hr_payroll.bonus.generate_bonus_sheet',$data);
    }

    public function addBonusPolicy(Request $request,$id=''){
            $data=array();
            if($id!=''){
                $data['bonus_policy']=DB::table('hr_emp_bonus_policys')->where('hr_emp_bonus_policys_id',$id)->first();
            }

            return view('Hr_payroll.bonus.bonus_policy_form',$data);
    }

    public function storeBonusPolicy(Request $request){
           $hr_emp_bonus_policy_id=$request->emp_bonus_id;
           $data['bat_company_id']=$request->company_id;

           $data['number_of_month']=$request->number_of_month;
           $data['bonus_based_on']=$request->bonus_based_on;
           $data['bonus_ratio']=$request->bonus_ratio;
           $data['status']=$request->status;

          if( $hr_emp_bonus_policy_id!=''){
              $data['updated_by']=session()->get('USER_ID');
              $data['updated_at']=date('Y-m-d H:i:s');
              DB::table('hr_emp_bonus_policys')->where('hr_emp_bonus_policys_id',$hr_emp_bonus_policy_id)->update($data);
              return redirect('hr-bonus-policy');
          }
          else{
              $data['bonus_eligible_based_on']=$request->bonus_eligible_based_on;
              $data['created_by']=session()->get('USER_ID');
              $data['created_at']=date('Y-m-d H:i:s');

              DB::table('hr_emp_bonus_policys')->insert($data);

              return redirect('hr-bonus-policy');

          }

    }

    public function deleteBonusPolicy(Request $request){
        $selected_ids=$request->selected_ids;
        DB::table('hr_emp_bonus_policys')->whereIn('hr_emp_bonus_policys_id',$selected_ids)->delete();
        return 1;

    }

    public function checkBonusEligibility(Request $request){
        $company_id=$request->company_id;
        $bonus_eligible_based_on=$request->bonus_eligible_based_on;
        $bonus_eligibility=1;
        $bonus_policys=DB::table('hr_emp_bonus_policys')->where('bat_company_id',$company_id)->where('bonus_eligible_based_on',$bonus_eligible_based_on)->get();
       if(count($bonus_policys)>=1){
           $bonus_eligibility=0;
       }
        return $bonus_eligibility;

    }


    public function addEmployeeManuallyForBonus(Request $request){
        $selected_users=explode(',',$request->selected_user_ids);
        $bonus_sheet_code=$request->sheet_code;
        $bonus_sheet_id=DB::table('hr_emp_bonus_sheet')->where('bonus_sheet_code',$bonus_sheet_code)->get();
        $bonus_sheet_id_array=array();
        foreach ($bonus_sheet_id as $sheet){
            array_push($bonus_sheet_id_array,$sheet->hr_emp_bonus_sheet_id);
        }
        $hr_emp_bonus_values=DB::table('hr_emp_bonus')->whereIn('hr_emp_bonus_sheet_id',$bonus_sheet_id_array)->get();
      $bonus_exists_array=array();
      $dpid_to_sheet_id=array();
      foreach ($hr_emp_bonus_values as $bonus){
          $bonus_exists_array[$bonus->sys_users_id]=$bonus;
          $dpid_to_sheet_id[$bonus->bat_dpid]=$bonus->hr_emp_bonus_sheet_id;
      }

      $employeeList=DB::table('sys_users')->whereIn('id',$selected_users)->get();

      $array_to_insert=array();
      foreach ($employeeList as $key=>$emp){
          if(isset($bonus_exists_array[$emp->id]) && $bonus_exists_array[$emp->id]->status=='Inactive'){
              DB::table('hr_emp_bonus')->where('sys_users_id',$emp->id)->update(['status'=>'Active']);
          }else if(isset($bonus_exists_array[$emp->id]) && $bonus_exists_array[$emp->id]->status=='Active'){
              unset($employeeList[$key]);
          }else{
              $temp=array();
              $temp['hr_emp_bonus_sheet_id']=$dpid_to_sheet_id[$emp->bat_dpid];
              $temp['sys_users_id']=$emp->id;
              $temp['designations_id']=$emp->designations_id;
              $temp['departments_id']=$emp->departments_id;
              $temp['branchs_id']=$emp->branchs_id;
              $temp['hr_emp_grades_id']=$emp->hr_emp_grades_id;
              $temp['hr_emp_units_id']=$emp->hr_emp_units_id;
              $temp['bat_company_id']=$emp->bat_company_id;
              $temp['bat_dpid']=$emp->bat_dpid;
              $temp['hr_emp_categorys_id']=$emp->hr_emp_categorys_id;
              $temp['hr_emp_sections_id']=$emp->hr_emp_sections_id;

              $temp['bonus_policy_type']='Manual';
              $temp['gross_salary']=$emp->min_gross;
              $temp['basic_salary']=$emp->basic_salary;
              $temp['earn_bonus']=0;
              $temp['payable_bonus']=0;
              $temp['is_edit']=0;
              $temp['created_by']=Auth::id();
              $temp['created_at']=date('Y-m-d H:i:s');
              array_push($array_to_insert,$temp);


          }
      }

        DB::table('hr_emp_bonus')->insert($array_to_insert);
        return 'success';
    }


    function submitBonusSheet(Request $request){
        $emp_ids = $request->emp_id;
        $eligible_based = $request->eligible_based;
        $bonus_based_on = $request->bonus_based_on;
        $eligible_date = $request->eligible_date;
        $bonus_amount = $request->bonus_amount;
        $stamp_amount = $request->stamp_amount;
//      $stamp_arr = array_combine($emp_ids,$stamp_amount);
        $bonus_arr = array_combine($emp_ids,$bonus_amount);
        $eligible_based_arr = array_combine($emp_ids,$eligible_based);
        $bonus_based_on_arr = array_combine($emp_ids,$bonus_based_on);

        $bonus_sheets = DB::table('hr_emp_bonus_sheet')->where('bonus_sheet_code',$request->bonus_sheet_code)->get();
        foreach($bonus_sheets as $sheet) {
            DB::table('hr_emp_bonus')->where('hr_emp_bonus_sheet_id',$sheet->hr_emp_bonus_sheet_id)->whereIn('sys_users_id',$emp_ids)->delete();
            $employeeList = DB::table('sys_users')
                ->where('bat_dpid', $sheet->bat_dpid)
                ->whereIn('id', $emp_ids)->get();
            foreach ($employeeList as $emp) {
                /* bonus will prepare by bonus code wise after gating hr_emp_bonus_sheet_id*/
                $emp_arr[] = array(
                    'hr_emp_bonus_sheet_id' => $sheet->hr_emp_bonus_sheet_id,
                    'bonus_applicable_date' => $eligible_date,
                    'sys_users_id' => $emp->id,
                    'hr_emp_grades_id' => $emp->hr_emp_grades_id,
                    'departments_id' => $emp->departments_id,
                    'branchs_id' => $emp->branchs_id,
                    'hr_emp_grades_id' => $emp->hr_emp_grades_id,
                    'hr_emp_units_id' => $emp->hr_emp_units_id,
                    'bat_company_id' => $emp->bat_company_id,
                    'hr_emp_sections_id' => $emp->hr_emp_sections_id,
                    'bat_company_id' => $emp->bat_company_id,
                    'bat_dpid' => $emp->bat_dpid,
                    'designations_id' => $emp->designations_id,
                    'bonus_policy_type' => $request->bonus_policy_type,
                    'gross_salary' => $emp->min_gross,
                    'basic_salary' => $emp->basic_salary,
                    'bonus_eligible_based_on' => $eligible_based_arr[$emp->id],
                    'bonus_based_on' => $bonus_based_on_arr[$emp->id],
                    'earn_bonus' => $bonus_arr[$emp->id],
//                'stamp'=>$stamp_arr[$emp->id],
                    'payable_bonus' => ($bonus_arr[$emp->id]),
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s')
                );
            }
        }
//        debug($emp_arr,1);
        DB::table('hr_emp_bonus')->insert($emp_arr);
        return response()->json([
            'success'=>true,
        ]);
    }


    function BonusSheet(Request $request){
        return view('Hr_payroll.bonus.bonus_sheet');
    }


    function BonusSheetData($sheet_code){
        $sql = Bonus::with('employee','department','designation','grade','company');
        $sql->where('status','Active');
        $sql->whereRaw("hr_emp_bonus_sheet_id IN (select hr_emp_bonus_sheet_id from hr_emp_bonus_sheet where bonus_sheet_code='$sheet_code')");
        $session_con = (sessionFilter('url','hr-emp-bonus-sheet-data'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if (!empty($session_con)){
            $sql->whereRaw($session_con);
        }
        $data['employeeList'] = $sql->get();

        $data['sheet_code']=$sheet_code;
        return view('Hr_payroll.bonus.bonus_sheet_data',$data);
    }

    function deleteBonusData(Request $request){
        $bonus_record = $request->bonus_record;
        $update_arr = array(
            'status'=>'Inactive',
            'updated_by'=>Auth::id()
        );
        DB::table('hr_emp_bonus')->where('hr_emp_bonus_id',$bonus_record)->update($update_arr);
        return response()->json([
            'success'=>true,
        ]);
    }

    function updateBonusSheet(Request $request){
        $sheet_id = $request->sheet_id;
        $submit_type = $request->submit_type;
        $bonus_ids = $request->bonus_id;
        $bonus_amount = $request->bonus_amount;
        $bonus_arr = array_combine($bonus_ids,$bonus_amount);

        foreach ($bonus_ids as $bonus_id){

            $emp_arr = array(
                'earn_bonus'=>$bonus_arr[$bonus_id],
                'payable_bonus'=>$bonus_arr[$bonus_id],
                'updated_by' => Auth::id(),
                'updated_at' => date('Y-m-d H:i:s')
            );
            DB::table('hr_emp_bonus')->where('hr_emp_bonus_id',$bonus_id)->update($emp_arr);
        }
        if($submit_type == 'submit_close'){
            DB::table('hr_emp_bonus_sheet')->where('hr_emp_bonus_sheet_id',$sheet_id)->update(['bonus_sheet_status'=>'CLOSE']);
        }


        return response()->json([
            'success'=>true,
        ]);
    }

    function BonusSheetReport(Request $request,$sheet_code,$type=false){
        $posts = $request->all();
        $data['posted'] = $posts;
        $data['categorys'] = '';
//        if(!empty($request->hr_emp_categorys_id)){
//            $categorys = DB::table('hr_emp_categorys')
//                ->selectRaw('group_concat(hr_emp_category_name) as hr_emp_category_name')
//                ->whereIn('hr_emp_categorys_id',$request->hr_emp_categorys_id)->first();
//            $data['categorys'] = $categorys->hr_emp_category_name;
//        }
        $sql = Bonus::with('employee','department','designation','grade','category','bonusConfig');
        $sql->where('status','Active');
        $sql->whereRaw("hr_emp_bonus_sheet_id IN (select hr_emp_bonus_sheet_id from hr_emp_bonus_sheet where bonus_sheet_code='$sheet_code')");

        $session_con = (sessionFilter('url','hr-emp-bonus-report'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $sql->whereRaw($session_con);
        }
        if($request->designations_id){
            $sql->whereIn('designations_id',$request->designations_id);
        }
        $sql->orderBy('hr_emp_bonus_id','DESC');
        $employeeData = $sql->get();
        $bonusData = [];
        if(!empty($employeeData)){
            foreach ($employeeData as $emp){
                $bonusData[] =(object) array(
                    'name'=>@$emp->employee->name,
                    'id_no'=>@$emp->employee->user_code,
                    'designation'=>@$emp->designation->designations_name,
                    'grade'=>@$emp->grade->hr_emp_grade_name,
                    'doj'=>todated(@$emp->employee->date_of_join),
                    'right_fixed_salary'=>number_format(@$emp->employee->min_gross,2),
                    'right_basic_salary'=>number_format(@$emp->employee->basic_salary,2),
                    'right_entitled_bonus'=>number_format(@$emp->earn_bonus,2),
//                    'right_stamp'=>number_format(@$emp->stamp,2),
                    'right_payable_bonus'=>number_format(@$emp->payable_bonus,2),
                    'signature'=>''
                );
            }
        }
        $data['report_data'] = $bonusData;
        $data['bonus_sheet'] = @$employeeData[0]->bonusConfig->bonus_sheet_name;
        $data['bonus_sheet_status'] = @$employeeData[0]->bonusConfig->bonus_status;
        if ($type =='pdf'){
//            $data['report_title'] =' Eid Bonus Report : '.($data['categorys']?$data['categorys']:"All Employee");
            $data['report_title'] =' Bonus Sheet : '.($data['bonus_sheet']);
            $data['filename'] = 'bonus_sheet';
            $data['orientation'] = "L";
            $data['signatures']=['Prepared by','Checked by','Approved by','Accounts & Finance'];
            $view='HR.pdf_report_template';

            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('Hr_payroll.bonus.bonus_sheet_report',$data);
        }

    }

    function hrBonuspolicy(){
        $sql = BonusPolicy::with('company');
//        dd($sql->get());
        $bonusPolicy = $sql->get();

        $bonusCategory = [];
        $bonusPolicys = [];
        foreach($bonusPolicy as $key=>$policy){

            $bonusPolicys[$policy->company->company_name][] = array(
                'hr_emp_bonus_policys_id'=>$policy->hr_emp_bonus_policys_id,
                'bonus_eligible_based_on'=>$policy->bonus_eligible_based_on,
                'number_of_month'=>$policy->number_of_month,
                'bonus_ratio'=>$policy->bonus_ratio,
                'bonus_based_on'=>$policy->bonus_based_on,
            );
        }
//        dd($bonusPolicys);
        $data['bonusPolicy'] = $bonusPolicys;
        return view('Hr_payroll.bonus.bonus_policy',$data);
    }

    //Create Bonus Sheet
    function hrBonusSheetCreate($sheet_code=''){
        $data = [];
        if($sheet_code){
            $data['sheet_info'] = DB::table('hr_emp_bonus_sheet')
                ->select('hr_emp_bonus_sheet.*')
                ->selectRaw('group_concat(hr_emp_bonus_sheet.bat_dpid) as bat_dpid')
                ->where('bonus_sheet_code',$sheet_code)->get()->first();
        } 

        return view('Hr_payroll.bonus.bonus_sheet_create',$data);
    }


    //Store Bonus sheet
    function hrBonusSheetCreateSave(Request $request){ 

        $selected_designations = $request->selected_designations?implode(',',$request->selected_designations):'All';

        $all_designations = DB::table('designations')->selectRaw('group_concat(designations_id) as designations_id')->get()->first();
        
        if($selected_designations == $all_designations->designations_id){
            $selected_designations = 'All';
        }
        
        if(!empty($request->bat_dpid)){

            if($request->bonus_sheet_code!=''){

                DB::table('hr_emp_bonus_sheet')
                    ->where('bonus_sheet_code',$request->bonus_sheet_code)
                    ->whereNotIn('bat_dpid',$request->bat_dpid)->delete();

                $sheet_code = $request->bonus_sheet_code;

            }else{

                $sheet_code = generateId('hr_bonus');

            }

            foreach ($request->bat_dpid as $point){

                $data = array(
                    'bonus_sheet_name'=>$request->bonus_sheet_name,
                    'bonus_type'=>$request->bonus_type,
                    'bat_dpid'=>$point,
                    'selected_designations'=>$selected_designations,
                    'bonus_sheet_status'=>'OPEN',
                    'bonus_preparation_date'=>$request->preparation_date,
                    'bonus_calculation_date'=>$request->calculation_date
                );

                if ($request->bonus_type !='Festival Bonus') {
                    $data['bonus_eligible_based_on'] = $request->bonus_eligible_based_on;
                    $data['number_of_month'] = $request->number_of_month;
                    $data['bonus_based_on'] = $request->bonus_based_on;
                    $data['bonus_ratio'] = $request->bonus_ratio;
                }


                $dpid_exists = DB::table('hr_emp_bonus_sheet')
                    ->where('bonus_sheet_code',$request->bonus_sheet_code)
                    ->where('bat_dpid',$point)->first();

                if($request->bonus_sheet_code !='' && !empty($dpid_exists)){

                    $data['updated_by'] = Auth::id();
                    $data['updated_at'] = date('Y-m-d H:i:s');

                    DB::table('hr_emp_bonus_sheet')
                        ->where('bonus_sheet_code', $request->bonus_sheet_code)
                        ->where('bat_dpid',$point)
                        ->update($data);

                    $bonus_sheet_info_to_delete=DB::table('hr_emp_bonus_sheet')
                        ->where('bonus_sheet_code',$sheet_code)
                        ->get();

                    foreach ($bonus_sheet_info_to_delete as $sheet_info){
                        DB::table('hr_emp_bonus')->where('hr_emp_bonus_sheet_id',$sheet_info->hr_emp_bonus_sheet_id)->delete();
                    }

                }else{
                    $data['bonus_sheet_code'] = $sheet_code;
                    $data['created_by'] = Auth::id();
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['bonus_status'] = 106;
                    DB::table('hr_emp_bonus_sheet')->insert($data);
                }
            }
        }


        $eligible_date = isset($request->calculation_date)?$request->calculation_date:date('Y-m-d');

        $sql = Employee::with('employeeDepartment','employeeDesignation','company');
        $sql->where('is_employee',1);
        $sql->where('status','Active');

        if ($request->bonus_type !='Festival Bonus') {
            $conDate = date('Y-m-d', strtotime("-$request->number_of_month months", strtotime($request->calculation_date)));
           
            if($request->bonus_eligible_based_on =='date_of_confirmation'){
               $sql->where('date_of_confirmation', '<', $conDate);  
            }else{
                $sql->where('date_of_join', '<', $conDate);  
            }         
        }

        
        $sql->whereRaw("bat_dpid IN (select bat_dpid from hr_emp_bonus_sheet where bonus_sheet_code='$sheet_code')");
        $sql->whereIn('designations_id',$request->selected_designations);

        $session_con = (sessionFilter('url','hr-emp-bonus-generate'));

        $session_con = trim(trim(strtolower($session_con)),'and');

        if($session_con){
            $sql->whereRaw($session_con);
        }

        $employeeList = $sql->get();

        $data_to_be_inserted = array();

        foreach($employeeList as $key=>$obj){

            if ($request->bonus_type =='Festival Bonus') {

                $bonus_info = bonus_policy($obj,$eligible_date);

                if(empty($bonus_info)){ 

                    unset($employeeList[$key]);

                } else{ 

                    $temp = self::setDataArray($obj, $sheet_code, $eligible_date, $request, $bonus_info);

                    array_push($data_to_be_inserted,$temp);
                }
            }else{

                $temp = self::setDataArray($obj, $sheet_code, $eligible_date, $request);

                array_push($data_to_be_inserted,$temp);
            }
        }


        DB::table('hr_emp_bonus')->insert($data_to_be_inserted);

        return redirect()->route('hr-emp-bonus-sheet-data', $sheet_code)->with('success','Sheet Create Successfully');

        // return $employeeList->hr_emp_bonus_sheet_id;

        //$data['employeeList'] = $employeeList;

        /*return response()->json([
            'bonus_sheet_code'=>$sheet_code,
            'success'=>true
        ]);*/
    }


    //set array data
    static function setDataArray($emp, $sheet_code, $eldate, $req, $info = null)
    {
        $temp = array();

        if ($info !=null) {
            $emp->earn_bonus =$info['bonus_amount'];
            $emp->bonus_amount = $info['bonus_amount'];
            $emp->bonus_policy = $info['bonus_policy'];
            $emp->bonus_policys_id = $info['bonus_policys_id'];
            $emp->bonus_eligible_based_on = $info['bonus_eligible_based_on'];
            $emp->bonus_based_on = $info['bonus_based_on'];
        }else{ 
            if ($req->bonus_based_on =='basic') { 
                $emp->earn_bonus = $emp->basic_salary * $req->bonus_ratio / 100;
                $emp->bonus_amount =  $emp->basic_salary * $req->bonus_ratio / 100;
            }elseif($req->bonus_based_on =='gross'){
                $emp->earn_bonus = $emp->min_gross * $req->bonus_ratio / 100;
                $emp->bonus_amount =  $emp->min_gross * $req->bonus_ratio / 100;
            }
 
            $emp->bonus_policy = 'Manual';
            $emp->bonus_policys_id = '';
            $emp->bonus_eligible_based_on = $req->bonus_eligible_based_on;
            $emp->bonus_based_on = $req->bonus_based_on;
        }

        $temp['sys_users_id']=$emp->id;
        $temp['designations_id']=$emp->designations_id;
        $temp['departments_id']=$emp->departments_id;
        $temp['branchs_id']=$emp->branchs_id;
        $temp['hr_emp_grades_id']=$emp->hr_emp_grades_id;
        $temp['hr_emp_units_id']=$emp->hr_emp_units_id;
        $temp['bat_company_id']=$emp->bat_company_id;
        $temp['bat_dpid']=$emp->bat_dpid;
        $temp['hr_emp_categorys_id']=$emp->hr_emp_categorys_id;
        $temp['hr_emp_sections_id']=$emp->hr_emp_sections_id;
        $temp['bonus_applicable_date']= $eldate;
        $temp['bonus_policy_type']='Company Policy';
        $temp['gross_salary']=$emp->min_gross;
        $temp['basic_salary']=$emp->basic_salary;
        $temp['earn_bonus']=$emp->earn_bonus;
        $temp['payable_bonus']=$emp->bonus_amount;
        $temp['is_edit']=0;

        $bonus_sheet_info = DB::table('hr_emp_bonus_sheet')->where('bonus_sheet_code',$sheet_code)->where('bat_dpid',$emp->bat_dpid)->first();
        $temp['hr_emp_bonus_sheet_id']= $bonus_sheet_info->hr_emp_bonus_sheet_id;
        $temp['bonus_eligible_based_on']=$emp->bonus_eligible_based_on;
        $temp['bonus_based_on']=$emp->bonus_based_on;
        $temp['bonus_payable_policy']=$emp->bonus_policy;
        $temp['created_by']=Auth::id();
        $temp['created_at']=date('Y-m-d H:i:s');

        return $temp;
    }



    //Go for delegation process
    public function bonusDelegationProcess(Request $request) {
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
    public function bonusApproveList() {
        $slug = 'hr_bonus';
        $data['columns'] = array(
            'hr_emp_bonus_sheet_id',
            'bonus_sheet_name',
            'bonus_sheet_code',
            'bonus_type',
            'bonus_status',
            'bonus_preparation_date',
            'bonus_calculation_date',
            'hr_emp_bonus_sheet.created_by',
            'hr_emp_bonus_sheet.created_at'
        );
        $data['approval_list'] = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug, $data['columns'], array());
        if ($data['approval_list']['results']) {
            foreach ($data['approval_list']['results'] as $element) {
                $hash = $element->bonus_sheet_code;
                $unique_array[$hash] = $element;
            }
        } else {
            $unique_array = [];
        }

        $data['records'] = $unique_array;
        return view('Hr_payroll.bonus.approval_list', $data);
    }


    //Approved form deligation
    public function bonusBulkApproved(Request $request) {
        $codes = $request->codes;
        $comments = 'Bonus Bulk Approved';
        $request->merge([
            'slug' => 'hr_bonus',
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
                        if($item['status_id'] == 108){
                            DB::table('hr_emp_bonus_sheet')->where('bonus_sheet_code', '=', $code)->update(['bonus_sheet_status'=>'CLOSE']);
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