<?php
namespace App\Http\Controllers\HR;

use App\Events\AuditTrailEvent as Audit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;
use App\Helpers\PdfHelper;

class EmployeeManager extends Controller
{
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function basicForm(Request $request, $id=null,$tab='basic', $mode=null){
        $data=[];
        $data['emp_ss_id'] = [];
        $data['emp_sr_id'] = '';
        $designations = DB::table('designations')->where('status','Active')->get();
        $designationWiseGradeArray = array();
        foreach ($designations as $des){
            $designationWiseGradeArray[$des->designations_id]= $des->hr_emp_grade_id;
        }

        $data['designationWiseGradeArray']= $designationWiseGradeArray;
        if (is_numeric($id)){
            if ($mode=='view'){
                $data['mode'] = 'view';
            }
            //Employee Information
            $employee =  DB::table('sys_users')
                ->select(
                    'sys_users.*',
                    'hr_emp_categorys.hr_emp_category_name',
                    'hr_emp_categorys.provision_period',
                    'designations.designations_name',
                    'designations.hr_emp_grade_id',
                    'departments.departments_name',
                    'hr_emp_grades.hr_emp_grade_name'
                )
                ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
                ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
                ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
                ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
                ->where('sys_users.id',$id)->first();
            $data['employee'] = $employee;

//            dd($data['employee']);

            switch ($tab){
                case 'basic':
                    //nominee Information
                    $nominee = DB::table('hr_emp_nominees')->where('sys_users_id',$id)->where('status', 'Active')->first();
                    $data['nominees'] = $nominee;

                    if(!empty($nominee)){
                        $data['nominees_id_type'] =  $nominee->id_type;
                    }
                    else{
                        $data['nominees_id_type'] =  null;
                    }

//                    dd($nominee,  $data['nominees_id_type']);

                    //Education Information
                    $education = DB::table('hr_emp_educations')->where('sys_users_id',$id)->get();
                    $data['education'] = $education;

                    //others conveyance
                    $others_conveyance = DB::table('hr_other_conveyances')->where('sys_users_id',$id)->get();
                    $data['others_conveyance'] = $others_conveyance;

                    //Salary Components
                    $salary_component = DB::table('hr_emp_salary_components')->where('sys_users_id',$id)->where('hr_emp_grades_id',$employee->hr_emp_grades_id)->where('record_type','default')->get();
                    $component_arr=[];
                    if(!empty($salary_component)){
                        foreach ($salary_component as $item) {
                            $component_arr[$item->component_type][]= array(
                                'component_type'=>$item->component_type,
                                'component_slug'=>$item->component_slug,
                                'component_name'=>$item->component_name,
                                'addition_amount'=>$item->addition_amount,
                                'deduction_amount'=>$item->deduction_amount,
                                'auto_applicable'=>$item->auto_applicable,
                            );
                        }
                    }
                    $data['salary_components'] = $component_arr;

                    //Bank Information
                    $bankaccounts = DB::table('hr_emp_bank_accounts')->where('sys_users_id',$id)->get();
                    $data['bankaccounts'] = $bankaccounts;

                    //MFS Information
                    $mfsInfo=DB::table('sys_users')->select('mfs_account_name','salary_account_no')->where('id',$id)->where('salary_disburse_type','MFS')->first();
                    $data['mfs_info']=$mfsInfo;

                    //Insurance Info
                      $insurance_info=DB::table('hr_insurance_claims')->where('sys_users_id',$id)->get();
                      $data['insurance_info'] = $insurance_info;

                      //Emergency Contract Info
                    $emergency_contract = DB::table('hr_emp_emargency_contract_info')->where('sys_users_id',$id)->get();
                    $data['emargency_contract'] = $emergency_contract;

                    //employment Information
                    $emp_professions = DB::table('hr_emp_professions')->join('bat_company','hr_emp_professions.bat_company_id','=','bat_company.bat_company_id') ->where('sys_users_id',$id)->get();
                    $data['emp_professions'] = $emp_professions;
                    // dd( $data['emp_professions']);
                    //Load Sections
                    if(!empty($employee->departments_id)){
                        $emp_sections = DB::table('hr_emp_sections')
                            ->leftJoin('hr_department_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'hr_department_sections.hr_emp_sections_id')
                            ->leftJoin('departments', 'departments.departments_id', '=', 'hr_department_sections.departments_id')
                            ->select('hr_emp_sections.hr_emp_sections_id', 'hr_emp_sections.hr_emp_section_name')
                            ->orderBy('hr_emp_section_name', 'asc')
                            ->where('hr_department_sections.departments_id', '=', $employee->departments_id)
                            ->where('hr_emp_sections.status', '=', 'Active')
                            ->get();
                        $data['emp_sections'] = $emp_sections;
                        // dd($emp_sections);
                    };
                    if(!empty($employee->bat_company_id)){
                        $previlige_points = explode(',',session('PRIVILEGE_POINT'));
                        $data['emp_dPoints'] = [];
                        if($previlige_points){
                            $data['emp_dPoints'] = DB::table('bat_distributorspoint')
                                ->where('dsid',$employee->bat_company_id)
                                ->whereIn('id',$previlige_points)
                                ->get();
                        }

                    }

                    $routes_sql = DB::table('bat_routes')->select('number')->where('stts', 1)
                                            ->where('dpid', $employee->bat_dpid)->where('number', '!=', 999);
                        if($employee->designations_id == 151){
                            $routes_sql->whereIn('ssid', [0, $employee->id]);
                        }elseif($employee->designations_id == 152){
                            $routes_sql->whereIn('srid', [0, $employee->id]);
                        }
                    $data['emp_routes'] = $routes_sql->groupBy('number')->get();

                    if($employee->designations_id == 151){
                        $data['emp_ss_id'] = DB::table('bat_routes')->where('dpid', $employee->bat_dpid)->where('ssid', $employee->id)->where('stts', 1)->groupBy('number')->pluck('number');
                    }elseif($employee->designations_id == 152){
                        $data['emp_sr_id'] = DB::table('bat_routes')->select('number')->where('dpid', $employee->bat_dpid)->where('srid', $employee->id)->groupBy('number')->where('stts', 1)->first();
                    }

//                    dd($data);

                    break;
                case 'leave':
                    $leaveManager = new LeaveManager();

                    if(!empty($request->leave_year)){
                        $data['leave_records'] = $leaveManager->getUserLeaveRecords($id, '', ['leave_year'=>$request->leave_year]);
                    }else{
                        $data['leave_records'] = $leaveManager->getUserLeaveRecords($id);
                    }
                    $data['leave_policys'] = $leaveManager->getLeavePolicy($request->leave_year,$id);
                    $data['year'] = $request->leave_year;
                    break;
                case 'attendance':

                    break;
                case 'salary':
                    $data['employeeList'] = $this->employeeSalaryInfo($id);
                    break;
                case 'variable_salary':
                    $data['variable_salarys'] = $this->employeeVariableSalaryInfo($id);
                    break;
                default:

                    break;
            }

            $data['tab'] = $tab;
            $data['post_year']= $request->leave_year;
        }else{
            redirect('employee-entry');
        }

        return view('HR.employee.emp_entry_form', $data);
    }

    /*
     * Find route list for sr and ss depand on distributor poin
     * */
    public function findRouteForSsSr($dp_id, $designation_id=null){
        $routes = '';
        if($dp_id > 0){
            $routes_sql = DB::table('bat_routes')->select('number')->where('stts', 1)
                        ->where('dpid', $dp_id)->groupBy('number')->where('number', '!=', 999);
            if($designation_id == 151){
                $routes_sql->where('ssid', 0);
            }elseif($designation_id == 152){
                $routes_sql->where('srid', 0);
            }else{
                $routes_sql->where('ssid', 41414141414);
            }

            $routes = $routes_sql->get();
        }

        return $routes;
    }

    /*
     * Store Employee Personal Information
     */

    function imageResize($imageResourceId=null,$width=null,$height=null, $targetWidth=null, $targetHeight=null) {

//        $targetWidth =300;
//        $targetHeight =260;
//        dd($imageResourceId,$width,$height, $targetWidth, $targetHeight);

        $targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
        imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height );
//        imagecopyresized($targetLayer,$imageResourceId,0,0,0,0, $width,$height,$targetWidth,$targetHeight);

        return $targetLayer;
    }

    function imageUpload($file, $fileNewName, $ext, $targetWidth, $targetHeight){
        $sourceProperties = getimagesize($file);
        $folderPath = "public/img/";
        $imageType = $sourceProperties[2];
//        dd($targetWidth, $targetHeight);

        switch ($imageType) {

            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file);
                $targetLayer = $this->imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1],$targetWidth,$targetHeight);
                imagepng($targetLayer, $folderPath . $fileNewName . "." . $ext);
                break;


            case IMAGETYPE_GIF:
                $imageResourceId = imagecreatefromgif($file);
                $targetLayer = $this->imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1],$targetWidth,$targetHeight);
                imagegif($targetLayer, $folderPath . $fileNewName . "." . $ext);
                break;


            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file);
                $targetLayer = $this->imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1],$targetWidth,$targetHeight);
                imagejpeg($targetLayer, $folderPath . $fileNewName .".". $ext);
                break;

            default:
                echo "Invalid Image type.";
                exit;
                break;
        }
    }

    public function storePersonalInfo(Request $request){

//        dump($request->bat_dpid, $request->all());

        $data = array();
        $data['name'] = ucwords($request->name);
        $data['name_bangla'] = $request->name_bangla;
        $data['father_name'] = ucwords($request->father_name);
        $data['mother_name'] = ucwords($request->mother_name);
        $data['spouse_name'] = ucwords($request->spouse_name);
        $data['mobile'] = strpos($request->mobile,'+')!==false?$request->mobile:'+88'.$request->mobile;
        $data['date_of_birth'] = $request->date_of_birth;
        $data['blood_group'] = $request->blood_group;
        $data['gender'] = $request->gender;
        $data['religion'] = $request->religion;
        $data['marital_status'] = $request->marital_status;
        $data['nid'] = $request->nid;
        $data['birth_certificate_no'] = $request->birth_certificate;
        $data['passport'] = $request->passport;
        $data['driving_license'] = $request->driving_license;

        $data['permanent_district'] = $request->permanent_district;
        $data['permanent_thana'] = $request->permanent_thana;
        $data['permanent_po'] = $request->permanent_po;
        $data['permanent_post_code'] = $request->permanent_post_code;
        $data['permanent_village'] = $request->permanent_village;
        $data['permanent_address_line'] = $request->permanent_address_line;

        $data['present_district'] = $request->present_district;
        $data['present_thana'] = $request->present_thana;
        $data['present_po'] = $request->present_po;
        $data['present_post_code'] = $request->present_post_code;
        $data['present_village'] = $request->present_village;
        $data['present_address_line'] = $request->present_address_line;

        $data['bat_company_id'] = $request->bat_company_id;
        $data['bat_dpid'] = $request->bat_dpid;
        $data['designations_id'] = $request->designations_id;
        $data['date_of_join'] = $request->date_of_join;
        $data['date_of_confirmation'] = $request->date_of_confirmation;
        $data['leave_policy_apply'] = $request->leave_apply;
//        $data['start_time'] = $request->start_time;
//        $data['end_time'] = $request->end_time;
//        $data['is_roaster'] =  $request->is_roaster;
        $data['is_employee'] = 1;
        $data['route_number'] = !empty($request->route_id)?implode (",", $request->route_id): null;

//        dd($data['route_number']);

        //========= generate id using point =============

        $final_emp_id = $this->generateIdCompanyWise($request->designations_id,$request->bat_company_id,$request->bat_dpid);

        //===============================================

        if($request->date_of_confirmation <= $request->date_of_join){
            $data['status'] = 'Active';
        }
        else{
            $data['status'] = 'Probation';
        }


//        if($request->hasfile('user_image')){
//            $imageName = time().'.'.$request->user_image->getClientOriginalExtension();
//            $request->user_image->move(public_path('img'), $imageName);
//            $data['user_image'] = $imageName;
//        }
        //file upload with resize img

        if($request->hasfile('user_image')) {
            $file = $request->user_image;
            $fileNewName = time();
            $ext = $request->user_image->getClientOriginalExtension();

            $this->imageUpload($file, $fileNewName, $ext, 300, 260);

            $data['user_image'] = $fileNewName.".".$ext;
//            dd($data['user_image']);
        }

        if($request->hasfile('user_sign')){

            $file_sign = $request->user_sign;
            $fileSignNewName = time().'_sign';
            $ext = $request->user_sign->getClientOriginalExtension();

            $this->imageUpload($file_sign, $fileSignNewName, $ext, 150, 80);


//            $signName = time().'_sign'.'.'.$request->user_sign->getClientOriginalExtension();
//            $request->user_sign->move(public_path('img'), $signName);
            $data['user_sign'] = $fileSignNewName.".".$ext;
        }

        DB::beginTransaction();
        $sys_employee_id = null;
        try{
            if (isset($request->employee_id) && $request->employee_id!=''){

                $sys_employee_id = $request->employee_id;
                $find_prev_stat = DB::table('sys_users')->select('designations_id')->find($request->employee_id);

                if($find_prev_stat->designations_id != $request->designations_id && in_array($find_prev_stat->designations_id, [151, 152])){

                    if($find_prev_stat->designations_id == 151){
                        DB::table('bat_routes')->where('ssid',$request->employee_id)->update(['ssid'=>0]);
                    }
                    else{
                        DB::table('bat_routes')->where('srid',$request->employee_id)->update(['ssid'=>0]);
                    }
                }

                if($request->designations_id == 151){
                    DB::table('bat_routes')->where('ssid',$request->employee_id)->update(['ssid'=>0]);
                    if($request->route_id != null){
                        foreach($request->route_id as $route_info){
                            DB::table('bat_routes')->where('dpid',$request->bat_dpid)->where('number', $route_info)->update(['ssid'=>$request->employee_id]);
                        }
                    }
                }
                elseif($request->designations_id == 152){
                    DB::table('bat_routes')->where('srid',$request->employee_id)->update(['srid'=>0]);
                    DB::table('bat_routes')->where('dpid',$request->bat_dpid)->where('number', $request->route_id[0])->update(['srid'=>$request->employee_id]);
                }

                if (!empty($request->user_code)){
                    $data['user_code'] = $request->user_code;
                }

//                DB::table('sys_users')->where('id', $request->employee_id)->update($data);
                $update_property = DB::table('sys_users')->where('id', $request->employee_id);
                Audit::build($update_property)->update($data);


                if (isset($request->leave_apply) && $request->leave_apply == 1 && !empty($request->date_of_join)){
                    if(date('Y',strtotime($request->date_of_join))==date('Y')){
                        self::leaveBalanceEntry($request->employee_id,$request->date_of_join);
                    }else{
                        $first_date = date("Y").'-01-01';
                        self::leaveBalanceEntry($request->employee_id,$first_date);
                    }

                }

//                return redirect()->back()->with('success', 'Personal Information Update Successfully!');
            }
            else{
                if (!empty($request->user_code)) {
                    $data['user_code'] = $request->user_code;
                } else {
//                    $data['user_code'] = generateId('emp_code');
                    $data['user_code'] = $final_emp_id;
                }

               $sys_employee_id =  DB::table('sys_users')->insertGetId($data);

                $id = DB::getPdo()->lastInsertId();
                if($request->designations_id == 151){
                    if($request->route_id != null){
                        foreach($request->route_id as $route_info){
                            $update = DB::table('bat_routes')->where('dpid',$request->bat_dpid)->where('number', $route_info)->update(['ssid'=>$id]);
//                            AuditTrailEvent::updateForAudit($update, ['ssid'=>$id]);
                        }
                    }
                }
                elseif($request->designations_id == 152){
                    $update = DB::table('bat_routes')->where('dpid',$request->bat_dpid)->where('number', $request->route_id[0])->update(['ssid'=>$id]);
//                    AuditTrailEvent::updateForAudit($update, ['ssid'=>$id]);
                }

                if (isset($request->leave_apply) && $request->leave_apply == 1 && !empty($request->date_of_join)&& !empty($id)) {
                    if (date('Y', strtotime($request->date_of_join)) == date('Y')) {
                        self::leaveBalanceEntry($id, $request->date_of_join);
                    } else {
                        $first_date = date("Y") . '-01-01';
                        self::leaveBalanceEntry($id, $first_date);
                    }
                }
    //                if (isset($request->attendance_apply) && $request->attendance_apply == 1 && !empty($request->date_of_join) && !empty($id)) {
                if (!empty($request->date_of_join) && !empty($id)) {
                    $emp_info = DB::table('sys_users')->where('id', $id)->get()->first();
                    $joinDAte = $request->date_of_join;
                    if(date('Y-m',strtotime($request->date_of_join))==date('Y-m')){
                        $lastDateOfMonth = date("Y-m-t", strtotime($joinDAte));

                        //$shiftinfo = DB::table('hr_working_shifts')->where('hr_working_shifts_id', $request->hr_working_shifts_id)->first();

                        $calendarData = DB::table('hr_company_calendars')
                            ->where('bat_company_id', $emp_info->bat_company_id)
                            ->whereBetween('date_is', [$joinDAte, $lastDateOfMonth])
                            ->select('date_is', 'day_status', 'bat_company_id')
                            ->get();

                        if (!empty($calendarData)) {
                            $cdata = array();
                            foreach ($calendarData as $key => $citem) {
                                $cdata[$key]['sys_users_id'] = $id;
                                $cdata[$key]['user_code'] = $emp_info->user_code;
                                $cdata[$key]['day_is'] = $citem->date_is;
                                $cdata[$key]['bat_company_id'] = $emp_info->bat_company_id;
                                $cdata[$key]['bat_dpid'] = $emp_info->bat_dpid;
                                $cdata[$key]['route_number'] = $emp_info->route_number;
                                $cdata[$key]['shift_day_status'] = $citem->day_status;
                                $cdata[$key]['shift_start_time'] = $emp_info->start_time;
                                $cdata[$key]['shift_end_time'] = $emp_info->end_time;
                                if($citem->day_status == 'R'){
                                    $cdata[$key]['daily_status'] = 'P';
                                }else{
                                    $cdata[$key]['daily_status'] = $citem->day_status;
                                }
                            }
                            //dd($cdata);
                            DB::table('hr_emp_attendance')->insert($cdata);
                        }
                    }

                }

            }

            $this->storeSalaryInfo($request,$sys_employee_id);

            DB::commit();

            if (isset($request->employee_id) && $request->employee_id!=''){
                return redirect()->back()->with('success', ' Personal Information Update Successfully!');
            }
            else{
                return redirect()->route('employee-entry', $id)->with('success', ' Personal Information Added Successfully!');
            }
        }catch (\Exception $e) {

            DB::rollback();
            return redirect('404','refresh');
        }
    }

    /*
     * Check Basic Employee Uniqueness
     * */
    public function checkBasicEmployeeUniqueness(Request $request){
        $employee_id=$request->sys_users_id;
        $type=$request->type;
        $value=$request->value;

        if($value==null)
            return 1;
        if($employee_id != 'New'){
            if($type=='mobile_check'){
                $mobile_check= DB::table('sys_users')->select('mobile')->where('mobile',$value)->where('id','!=',$employee_id)->get();
                if(count($mobile_check)>0){
                    return 0;
                    //matched
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'nid_check'){
                $nid_check= DB::table('sys_users')->select('nid')->where('nid',$value)->where('id','!=',$employee_id)->get();

                if(count($nid_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'birth_certificate_check'){
                $birth_certificate_check= DB::table('sys_users')->select('birth_certificate_no')->where('birth_certificate_no',$value)->where('id','!=',$employee_id)->get();
                if(count($birth_certificate_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'passport_check'){
                $passport_check= DB::table('sys_users')->select('passport')->where('passport',$value)->where('id','!=',$employee_id)->get();
                if(count($passport_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'driving_licence_check'){
                $driving_license_check= DB::table('sys_users')->select('passport')->where('passport',$value)->where('id','!=',$employee_id)->get();
                if(count($driving_license_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
        }else{
            if($type=='mobile_check'){

                $mobile_check= DB::table('sys_users')->select('mobile')->where('mobile',$value)->get();

                if(count($mobile_check) == 0){
                    $sub_mob = substr($value, 0, 3);
                    if($sub_mob == '+88'){
                        $value = substr($value, 3);
                    }else{
                        $value = '+88'.$value;
                    }
                }

                $mobile_check= DB::table('sys_users')->select('mobile')->where('mobile',$value)->get();

                if(count($mobile_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'nid_check'){
                $nid_check= DB::table('sys_users')->select('nid')->where('nid',$value)->get();

                if(count($nid_check)>0 ){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'birth_certificate_check'){
                $birth_certificate_check= DB::table('sys_users')->select('birth_certificate_no')->where('birth_certificate_no',$value)->get();
                if(count($birth_certificate_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'passport_check'){
                $passport_check= DB::table('sys_users')->select('passport')->where('passport',$value)->get();
                if(count($passport_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            elseif ($type == 'driving_licence_check'){
                $driving_license_check= DB::table('sys_users')->select('passport')->where('driving_license',$value)->get();
                if(count($driving_license_check)>0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
        }

    }



    //Check User code for validator
    public function checkUserCode(Request $request){
        $sql = DB::table('sys_users')->where('user_code', $request->user_code);
        if (!empty($request->id)){
            $sql->where('id', '!=', $request->id);
        }
        $user_data = $sql->first();
        if (!empty($user_data)){
            return response()->json([
                'result'=>'exist',
            ]);
        }else{
            return response()->json([
                'result'=>'valid',
            ]);
        }
    }

    //Check User code for validatoi
    public function checkEmailExist(Request $request){
        if($request->email==''){
            return response()->json([
                'result'=>'valid',
            ]);
        }
        $sql = DB::table('sys_users')->where('email', $request->email);
        if (!empty($request->id)){
            $sql->where('id', '!=', $request->id);
        }
        $user_data = $sql->first();
        if (!empty($user_data)){
            return response()->json([
                'result'=>'exist',
            ]);
        }else{
            return response()->json([
                'result'=>'valid',
            ]);
        }
    }

    //get District Name Bangla
    public function getDistrictNameBangla($id){
        $distBangla = DB::table('districts')->select('districts_name', 'districts_name_bangla')->where('districts_id',$id)->first();
        return response()->json([
            'success'=>true,
            'data'=>$distBangla,
        ]);
    }

    //get Educational Qualifications List
    public function getDegreeList(Request $request){
        $name = $request->name;
        $data = DB::table('educational_degrees')->select('educational_degrees_id','educational_degrees_name')->where('educational_qualifications_name',$name)->get();
        return response()->json([
            'success'=>true,
            'data'=>$data,
        ]);
    }

    //get Branch List
    public function getBranchList($id){
        $data = DB::table('bank_branchs')->select('bank_branchs_id','bank_branch_name')->where('bank_id',$id)->get();
        return response()->json([
            'success'=>true,
            'data'=>$data,
        ]);
    }


    /*
     * Load Insurance Form
     * */
    public function getInsuranceForm(Request $request){
        $data=array();

        if (isset($request->id) && $request->id!='') {
            $data['insurance']=DB::table('hr_insurance_claims')->where('hr_insurane_claim_id',$request->id)->first();
        }
        //return $data['insurance'];
        return view('HR.employee.insurance_form',$data);
    }

    /*
     * Load Emargency Contract Form
     * */
    public function getEmargencyContractForm(Request $request){
        $data=array();

        if (isset($request->id) && $request->id!='') {
            $data['info']=DB::table('hr_emp_emargency_contract_info')->where('id',$request->id)->first();
        }
        //return $data['insurance'];
        return view('HR.employee.emargency_contract_form',$data);
    }



    /*
     * Store or Edit Insurance Form
     * */

    public function storeInsuranceInfo(Request $request){
        $data=array();
        $data['sys_users_id'] = $request->sys_users_id;
        $data['claim_type']=$request->claim_type;
        $data['claim_date']=$request->claim_date;
        $data['claim_amount']=$request->claim_amount;
        $data['claim_details']=$request->claim_details;
        $data['claim_status']=$request->claim_status;

        if (isset($request->insurance_id) && $request->insurance_id!=''){
            $update = DB::table('hr_insurance_claims')->where('hr_insurane_claim_id', $request->insurance_id)->update($data);
//            AuditTrailEvent::updateForAudit($update, $data);
            $dataReturn =  DB::table('hr_insurance_claims')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Insurance information update successfully',
            ]);
        }else{
            DB::table('hr_insurance_claims')->insert($data);

            $dataReturn=DB::table('hr_insurance_claims')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Insurance information added successfully',
            ]);
        }


    }


    /*
     * Store or Edit storeEmargencyContractInfo Form
     * */

    public function storeEmargencyContractInfo(Request $request){

        $data=array();
        $data['sys_users_id'] = $request->sys_users_id;
        $data['name']=$request->emg_con_name;
        $data['mobile']=$request->emg_con_mobile;
        $data['relation']=$request->emg_con_relation;
        $data['address']=$request->emg_con_address;
        $data['is_primary']=$request->is_primary_contract == 1?1:0;

        $all_prev_data =  DB::table('hr_emp_emargency_contract_info')->where('sys_users_id', $request->sys_users_id)
            ->where('is_primary',1)->get();

        if (isset($request->emg_con_id) && $request->emg_con_id!=''){
            if($request->is_primary_contract == 1){
                if(count($all_prev_data) > 0){
                    foreach($all_prev_data as $prev){
                        DB::table('hr_emp_emargency_contract_info')->where('id', $prev->id)->update(['is_primary'=>0]);
                    }
                }
            }

            $update = DB::table('hr_emp_emargency_contract_info')->where('id', $request->emg_con_id)->update($data);
//            AuditTrailEvent::updateForAudit($update, $data);
            $dataReturn =  DB::table('hr_emp_emargency_contract_info')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Emargency contract information update successfully',
            ]);
        }else{

            if($request->is_primary_contract == 1){
                if(count($all_prev_data) > 0){
                    foreach($all_prev_data as $prev){
                        DB::table('hr_emp_emargency_contract_info')->where('id', $prev->id)->update(['is_primary'=>0]);
                    }
                }
            }else{
                if(count($all_prev_data) <= 0){
                    $data['is_primary'] = 1;
                }
            }

            DB::table('hr_emp_emargency_contract_info')->insert($data);

            $dataReturn=DB::table('hr_emp_emargency_contract_info')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Emergency Contact Information Added Successfully',
            ]);
        }
    }
    /*
     * Delete Insurance Info
     * */

    public function deleteInsuranceInfo(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                DB::table('hr_insurance_claims')->where('hr_insurane_claim_id', $id)->delete();
            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }


    /*
     * Delete Emargency contract Info
     * */

    public function deleteEmergencyContractInfo(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                DB::table('hr_emp_emargency_contract_info')->where('id', $id)->delete();
            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }
    /*
     * Load Education Form
     */
    public function getEduForm(Request $request){
        $data=[];
        if (isset($request->id) && $request->id!=''){
            $data['edu'] = DB::table('hr_emp_educations')->where('hr_emp_educations_id',$request->id)->first();
        }
        //dd($data);
        return view('HR.employee.education_form', $data);
    }

    /*
     * Store / Edit Education Information
     */
    public function storeEducationInfo(Request $request, $id=null){
        $data=[];
        $data['educational_qualifications_name'] = $request->educational_qualifications_name;
        $data['educational_degrees_name'] = $request->educational_degrees_name;
        $data['educational_institute_name'] = $request->educational_institute_name;
        $data['education_board'] = $request->education_board;
        $data['education_category'] = $request->education_category;
        $data['passing_year'] = $request->passing_year;
        $data['education_study_filed'] = $request->education_study_filed;
        $data['result_type'] = $request->result_type;
        $data['sys_users_id'] = $request->sys_users_id;

        if ($request->result_type =='Division'){
            $data['results'] = $request->results_division;
            $data['outof'] = '';
        }else{
            $data['results'] = $request->results;
            $data['outof'] = $request->outof;
        }

        if (isset($request->hr_emp_educations_id) && $request->hr_emp_educations_id!=''){
            $update = DB::table('hr_emp_educations')->where('hr_emp_educations_id', $request->hr_emp_educations_id)->update($data);
//            AuditTrailEvent::updateForAudit($update, $data);
            $dataReturn =  DB::table('hr_emp_educations')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Educational information update successfully',
            ]);
        }else{
            DB::table('hr_emp_educations')->insert($data);
            $dataReturn =  DB::table('hr_emp_educations')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Educational information added successfully',
            ]);
        }

    }

    /*
     * Destroy Education Information
     */
    public function destroyEducationInfo(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                DB::table('hr_emp_educations')->where('hr_emp_educations_id', $id)->delete();
            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }




    /*
     * Load Account Form
     */
    public function getAccForm(Request $request){
        $data=[];
        if (isset($request->id) && $request->id!=''){
            $data['acc'] = DB::table('hr_emp_bank_accounts')
                ->select('hr_emp_bank_accounts.*', 'bank_account_types.bank_account_types_id','banks.banks_id')
                ->leftJoin('banks', 'hr_emp_bank_accounts.bank_name', '=', 'banks.banks_name')
                ->leftJoin('bank_account_types', 'hr_emp_bank_accounts.bank_account_types_name', '=', 'bank_account_types.bank_account_types_name')
                ->where('hr_emp_bank_accounts.hr_emp_bank_accounts_id',$request->id)->first();
        }
        return view('HR.employee.accounts_form', $data);
    }

    /*
     * Store / Edit Education Information
     */
    public function storeAccountInfo(Request $request, $id=null){

        $bank = DB::table('banks')->select('banks_name')->where('banks_id',$request->banks_id)->first();
        $acctype = DB::table('bank_account_types')->select('bank_account_types_name')->where('bank_account_types_id',$request->bank_account_types_id)->first();

        $data = array(
            'sys_users_id' => $request->sys_users_id,
            'account_number' => $request->account_number,
            'bank_account_types_name' =>  $acctype->bank_account_types_name,
            'bank_name' =>  $bank->banks_name,
            'branch_name' => $request->branch_name,
            'is_active_account' => $request->is_active_account
        );

        if ($request->is_active_account !=''){
            $update2 = DB::table('hr_emp_bank_accounts')->where('sys_users_id', $request->sys_users_id)->update(['is_active_account'=>0]);
//            AuditTrailEvent::updateForAudit($update2,['is_active_account'=>0]);

            $update = DB::table('sys_users')->where('id', $request->sys_users_id)->update(['salary_account_no'=>$request->account_number, 'salary_disburse_type'=>'Bank']);
//            AuditTrailEvent::updateForAudit($update, ['salary_account_no'=>$request->account_number, 'salary_disburse_type'=>'Bank']);
        }

        if (isset($request->hr_emp_bank_accounts_id) && $request->hr_emp_bank_accounts_id!=''){
            $update = DB::table('hr_emp_bank_accounts')->where('hr_emp_bank_accounts_id', $request->hr_emp_bank_accounts_id)->update($data);
//            AuditTrailEvent::updateForAudit($update,$data);
            $dataReturn =  DB::table('hr_emp_bank_accounts')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Bank Account information update successfully',
            ]);
        }else{
            DB::table('hr_emp_bank_accounts')->insert($data);
            $dataReturn =  DB::table('hr_emp_bank_accounts')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Bank Account information Added successfully',
            ]);
        }
    }

    /*
     * Destroy Account Information
     */
    public function destroyAccInfo(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                DB::table('hr_emp_bank_accounts')->where('hr_emp_bank_accounts_id', $id)->delete();
            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }

    /*
     * Store Mfs Account information
     * */
    public function addMfsAccount(Request $request){
        $sys_user_id= $request->sys_users_id;
        $data=array(
            'salary_disburse_type' => 'MFS',
            'mfs_account_name' => $request->mfs_account_holder_name,
            'salary_account_no' => $request->mfs_account_number
        );

        $update = DB::table('sys_users')->where('id', $sys_user_id)->update($data);
//        AuditTrailEvent::updateForAudit($update,$data);
        $dataReturn =  DB::table('sys_users')->where('id', $request->sys_users_id)->get();

        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }

    /*
     * Get Info is Salary Account Exist
     */
    public function checkAccountTypes(Request $request){
        $acctype = DB::table('bank_account_types')->select('bank_account_types_name')->where('bank_account_types_id',$request->bank_account_types_id)->first();
        if($acctype){
            $emp_bank_info = DB::table('hr_emp_bank_accounts')->where('sys_users_id', $request->sys_users_id)->where('bank_account_types_name',  $acctype->bank_account_types_name)->first();
            if (!empty($emp_bank_info)){
                return response()->json([
                    'data' =>  'exist',
                ]);
            }else{
                return response()->json([
                    'data' =>  'empty',
                ]);
            }
        }
    }


    /*
     * Load Employment Profession Form
     */
    public function getEmpForm(Request $request){
        $data=[];
        if (isset($request->id) && $request->id!=''){
            $data['emp'] = DB::table('hr_emp_professions')->where('hr_emp_professions_id',$request->id)->first();
        }
        return view('HR.employee.employment_form', $data);
    }

    /*
     * Store / Edit Account Information
     */
    public function storeEmploymentInfo(Request $request, $id=null){
        //$dataReturn = $request->all();
        $data = array();
        $data['sys_users_id'] = $request->sys_users_id;
        $data['organization_name'] = $request->organization_name;
        $data['department_name'] = $request->department_name;
        $data['bat_company_id']=$request->previous_company_name;
        $data['designation_name'] = $request->designation_name;
        $data['responsibilities'] = $request->responsibilities;
        $data['from_date'] = $request->from_date;

        if (!empty($request->to_date)){
            $data['to_date'] = $request->to_date;
            $data['is_continue'] = 0;
        }else{
            $data['to_date'] = '';
            $data['is_continue'] = 1;
        }

        if (isset($request->hr_emp_professions_id) && $request->hr_emp_professions_id!=''){
            $update = DB::table('hr_emp_professions')->where('hr_emp_professions_id', $request->hr_emp_professions_id)->updaet($data);
//            AuditTrailEvent::updateForAudit($update,$data);
            $dataReturn =  DB::table('hr_emp_professions')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Employment History update successfully',
            ]);
        }else{
            DB::table('hr_emp_professions')->insert($data);
            $dataReturn =  DB::table('hr_emp_professions')->where('sys_users_id', $request->sys_users_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$dataReturn,
                'message'=>'Employment History Added Successfully',
            ]);
        }
    }

    /*
     * Destroy Employee Information
     */
    public function destroyEmpInfo(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                DB::table('hr_emp_professions')->where('hr_emp_professions_id', $id)->delete();
            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }


    /*
     * Get Provission Period
     */
    public function getProvisionPeriod( Request $request){
        $data = DB::table('hr_emp_categorys')->where('hr_emp_categorys_id', $request->empcatid)->first();
        return response()->json([
            'success'=>true,
            'data'=>$data,
        ]);
    }

    /*
     * Get Salary Information based on Grade
     */
    public function getGradeInfo( Request $request){
        $data = DB::table('hr_emp_grades')->where('hr_emp_grades_id', $request->grade)->first();
        $addition = DB::table('hr_grade_components')->where('hr_emp_grades_id', $request->grade)->where('component_type', 'Addition')->where('status', 'Active')->get();
        $deduct = DB::table('hr_grade_components')->where('hr_emp_grades_id', $request->grade)->where('component_type', 'Deduction')->where('status', 'Active')->get();
        $variable = DB::table('hr_grade_components')->where('hr_emp_grades_id', $request->grade)->where('component_type', 'Variable')->where('status', 'Active')->get();
        return response()->json([
            'success'=>true,
            'data'=>$data,
            'addition'=>$addition,
            'deduct'=>$deduct,
            'variable'=>$variable,
        ]);
    }

    /*
     * Employee List
     */
    public function List(Request $request){

        return view('HR.employee.list');
    }

    public function show($id){
        $employee =  DB::table('sys_users')
            ->select(
                'sys_users.*',
                'hr_emp_categorys.hr_emp_category_name',
                'hr_emp_categorys.provision_period',
                'designations.designations_name',
                'departments.departments_name',
                'hr_emp_grades.hr_emp_grade_name',
                'hr_emp_units.hr_emp_unit_name',
                'hr_working_shifts.shift_name',
                'hr_emp_sections.hr_emp_section_name',
                'supervisors.name as supervisor_name',
                'reversnces.name as referance_name'
            )
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
            ->leftJoin('sys_users AS supervisors', 'supervisors.id', '=', 'sys_users.line_manager_id')
            ->leftJoin('sys_users AS reversnces', 'reversnces.id', '=', 'sys_users.reference_user_id')
            ->where('sys_users.id',$id)->first();
        $data['employee'] = $employee;

        $data['nominees'] = DB::table('hr_emp_nominees')->where('hr_emp_nominees.sys_users_id',$id)->where('status', 'Active')->get();

        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;

        $education = DB::table('hr_emp_educations')->where('sys_users_id',$id)->get();
        $data['education'] = $education;

        $emp_professions = DB::table('hr_emp_professions')->where('sys_users_id',$id)->get();
        $data['emp_professions'] = $emp_professions;

        return view('HR.employee.employee_view', $data)->render();


    }

    //Employee PDF Generate
    public function employeePDF($id){
        $employee =  DB::table('sys_users')
            ->select(
                'sys_users.*',
                'hr_emp_categorys.hr_emp_category_name',
                'hr_emp_categorys.provision_period',
                'designations.designations_name',
                'departments.departments_name',
                'hr_emp_grades.hr_emp_grade_name',
                'hr_emp_units.hr_emp_unit_name',
                'hr_working_shifts.shift_name',
                'hr_emp_sections.hr_emp_section_name'
            )
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
            ->where('sys_users.id',$id)->first();
        $data['employee'] = $employee;

        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;

        $nominee_info = DB::table('hr_emp_nominees')->where('sys_users_id',$id)->where('status', 'Active')->get();
        $data['nominees'] = $nominee_info;

        $education = DB::table('hr_emp_educations')->where('sys_users_id',$id)->get();
        $data['education'] = $education;

        $emp_professions = DB::table('hr_emp_professions')->where('sys_users_id',$id)->get();
        $data['emp_professions'] = $emp_professions;

        $data['report_title'] = 'RESUME';
        $data['filename'] = 'resume';
        $data['orientation'] = "P";
        $view = 'HR.employee.emp_view';
        PdfHelper::exportPdf($view,$data);
    }

    function workingShiftTime(Request $request){
        $shift = $request->shift;
        $shift_info = DB::table('hr_working_shifts')->where('hr_working_shifts_id','=',$shift)->first();
        return response()->json($shift_info);
    }

    function storeOfficialInfo(Request $request){
        $data = array(
            'line_manager_id' => $request->line_manager_id,
            'designations_id' => $request->designations_id,
            'departments_id' => $request->departments_id,
            'hr_emp_units_id' => $request->hr_emp_units_id,
            'hr_emp_sections_id' => $request->hr_emp_sections_id,
            'branchs_id' => $request->branchs_id

        );

        $update = DB::table('sys_users')->where('id', $request->sys_users_id)->update($data);
//        AuditTrailEvent::updateForAudit($update,$data);
        $dataReturn =  DB::table('sys_users')->where('id', $request->sys_users_id)->get();
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);

    }




    function storeSalaryInfo(Request $request,$sys_uses_id){

       // $sys_uses_id=$request->sys_users_id;
        $pf_amount_company=null;
        $pf_amount_employee=null;
        if($request->pf_applicable != null) {

            $bat_company_array = DB::table('sys_users')->select('bat_company_id')->where('id', $sys_uses_id)->first();
            $bat_company_id = $bat_company_array->bat_company_id;

            $company_pf_policy_array = DB::table('hr_company_pf_policys')->where('bat_company_id', $bat_company_id)->first();

            if (!empty($company_pf_policy_array)) {
                $ratio_of_basic = $company_pf_policy_array->ratio_of_basic;
                $employee_ratio = $company_pf_policy_array->employee_ratio;
                $company_ratio = $company_pf_policy_array->company_ratio;

            } else {

                $ratio_of_basic = 5;
                $employee_ratio = 50;
                $company_ratio = 50;
            }
            $basic_salary = $request->basic_salary;
            $pf_amount = ($basic_salary * $ratio_of_basic) / 100;
            $pf_amount_company = ($pf_amount * $company_ratio) / 100;
            $pf_amount_employee = ($pf_amount * $employee_ratio) / 100;
        }

        $data = array(
            'hr_emp_grades_id' => $request->hr_emp_grades_id,
            'basic_salary' => $request->basic_salary,
            'max_variable_salary' => $request->max_variable_salary,
            'min_gross' => $request->min_gross,

            'insurance_applicable' => $request->insurance_applicable,
            'insurance_amount' => $request->insurance_amount,

            'pf_applicable' => $request->pf_applicable,
            'pf_amount_employee'=>$pf_amount_employee,
            'pf_amount_company'=>$pf_amount_company,

            'gf_applicable' => $request->gf_applicable,
            'gf_amount'=> $request->gf_amount,

            'late_deduction_applied' => $request->late_deduction_applied,

            'other_conveyance' => $request->other_conveyance,
            'yearly_increment' => $request->yearly_increment
        );
//$users = DB::table('sys_users')->where('is_employee','1')->where('designations_id','150')->get();
//foreach ($users as $each_user) {
//    $request->sys_users_id = $each_user->id;

    $usercode = DB::table('sys_users')->select('user_code')->where('id', $sys_uses_id)->pluck('user_code')->first();
    DB::table('hr_emp_salary_components')->where('sys_users_id', '=', $sys_uses_id)->where('record_type', 'default')->delete();
    if (!empty($request->component_slug)) {
        $componentArr = array();
        foreach ($request->component_slug as $key => $item) {
            $componentArr[$key]['sys_users_id'] =$sys_uses_id;
            $componentArr[$key]['record_ref'] = $usercode;
            $componentArr[$key]['hr_emp_grades_id'] = $request->hr_emp_grades_id;
            $componentArr[$key]['record_type'] = 'default';
            $componentArr[$key]['component_type'] = $request->component_type[$item];
            $componentArr[$key]['component_name'] = $request->component_name[$item];
            $componentArr[$key]['component_slug'] = $item;

            $componentArr[$key]['addition_amount'] = $request->salary_component[$item];

            if ($request->component_type[$item] == 'Addition' || $request->component_type[$item] == 'Variable') {
                $componentArr[$key]['addition_amount'] = $request->salary_component[$item];
            } else {
                $componentArr[$key]['addition_amount'] = 0;
            }

            if ($request->component_type[$item] == 'Deduction') {
                $componentArr[$key]['deduction_amount'] = $request->salary_component[$item];
            } else {
                $componentArr[$key]['deduction_amount'] = 0;
            }

            $componentArr[$key]['auto_applicable'] = isset($request->component_autoapply[$item]) ? $request->component_autoapply[$item] : 'NO';
        }

        DB::table('hr_emp_salary_components')->where('sys_users_id', '=',$sys_uses_id)->insert($componentArr);
    }


    DB::table('hr_other_conveyances')->where('sys_users_id', '=',$sys_uses_id)->delete();
    if ($request->other_conveyance == 1) {
        $conveyance_title = $request->conveyance_title;
        $conveyance_amount = $request->conveyance_amount;
        if (!empty($conveyance_title)) {
            $all_other_conv = [];
            foreach ($conveyance_title as $i => $cnv) {
                $other_conveyance['conveyance_title'] = $cnv;
                $other_conveyance['conveyance_amount'] = $conveyance_amount[$i];
                $other_conveyance['sys_users_id'] = $sys_uses_id;
                $other_conveyance['created_by'] = Auth::id();
                $other_conveyance['created_at'] = date('Y-m-d h:i:s');
                $all_other_conv[] = $other_conveyance;
            }
            DB::table('hr_other_conveyances')->insert($all_other_conv);
        }
    }

        $update = DB::table('sys_users')->where('id', $sys_uses_id)->update($data);
//        AuditTrailEvent::updateForAudit($update,$data);
//}
        return 1;
    }



    function incrementPromotionForm(Request $request){
        $emp_id = $request->emp_id;
        $data = [];
        $employeeInfo=DB::table('sys_users');
        $employeeInfo ->select('sys_users.*','sys_users.name','sys_users.user_code','hr_emp_grades.hr_emp_grade_name','designations.designations_name');
        $employeeInfo->join('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','sys_users.hr_emp_grades_id');
        //$employeeInfo->join('departments','departments.departments_id','=','sys_users.departments_id');
        $employeeInfo->leftJoin('designations','designations.designations_id','=','sys_users.designations_id');
        $employeeInfo->where('sys_users.id','=',$emp_id);
        $data['emp_log'] = $employeeInfo->first();

        return view('HR.employee.emp_inc_pro_form', $data);
    }

    //Store Increment Promotion
    function incrementPromotionStore(Request $request){
        $emp_id = $request->sys_users_id;

        if (!empty($emp_id)) {
            $emp_id = $request->sys_users_id;
            $user_info = DB::table('sys_users')->where('id', $emp_id)->first();
            $gross_salary = $request->gross_salary;
            $record_type = $request->inc_pro_type;

            if($record_type == 'promotion'){
                $designation_id = $request->designations_id;
                $hr_emp_grades_id = $request->hr_emp_grades_id;
                $based_on = '';
            }else{
                $designation_id = $user_info->designations_id;
                $hr_emp_grades_id = $user_info->hr_emp_grades_id;
                $based_on = $request->based_on;
            }

            if($gross_salary == $user_info->min_gross ){
                $increment_amount = 0;
                $basic_salary = $user_info->basic_salary;
                $salary_cal = 0;
            }else{
                $increment_amount = floatval($gross_salary) - floatval($user_info->min_gross);
                $salary_cal = floatval($increment_amount / $user_info->min_gross);
                $basic_salary = $user_info->basic_salary+($user_info->basic_salary*$salary_cal);
            }

            $user_info_arr = array(
                'sys_users_id' => $user_info->id,
                'record_type' => $record_type,
                'designations_id' => $designation_id,
                'previous_designations_id' =>$user_info->designations_id,
                'hr_emp_grades_id' => $hr_emp_grades_id,
                'previous_grades_id' => $user_info->hr_emp_grades_id,
                'bat_company_id' => $user_info->bat_company_id,
                'bat_dpid' => $user_info->bat_dpid,
                'applicable_date' => $request->applicable_date,
                'basic_salary' => $basic_salary,
                'increment_amount' => $increment_amount,
                'gross_salary' => $gross_salary,
                'previous_gross' => $user_info->min_gross,
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d h:i:s'),
                'hr_log_status' => 48,
            );

            $insert = DB::table('hr_employee_record_logs')->insert($user_info_arr);

            $record_log_last_id = DB::getPdo()->lastInsertId();

            if($gross_salary != $user_info->min_gross ) {
                $salary_info_array = salary_calculation_arr($user_info->id, $hr_emp_grades_id, $record_log_last_id, $salary_cal);
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



    //Increment Promotion history
    function incrementPromotionHistory(Request $request){
        $emp_id = $request->sys_users_id;
        // increment & promotion
        $sql = DB::table('hr_employee_record_logs');
        $sql->select("hr_employee_record_logs.*",
            'hr_emp_grades.hr_emp_grade_name',
            'designations.designations_name',
            'prev_grade.hr_emp_grade_name as prev_grade_name',
            'prev_designations.designations_name as prev_designation_name',
            'sys_status_flows.status_flows_name',
            'b.name as delegation_person_name'
        );
        $sql->join('sys_users','sys_users.id','=','hr_employee_record_logs.sys_users_id');
        $sql->leftJoin('hr_emp_grades as prev_grade', 'prev_grade.hr_emp_grades_id', '=', 'hr_employee_record_logs.previous_grades_id');
        $sql->leftJoin('hr_emp_grades','hr_emp_grades.hr_emp_grades_id', '=', 'hr_employee_record_logs.hr_emp_grades_id');

        $sql->leftJoin('designations as prev_designations', 'prev_designations.designations_id', '=', 'hr_employee_record_logs.previous_designations_id');
        $sql->leftJoin('designations','designations.designations_id','=','hr_employee_record_logs.designations_id');

        $sql->join('sys_status_flows', 'sys_status_flows.status_flows_id', '=', 'hr_employee_record_logs.hr_log_status');
        $sql->leftJoin('sys_users as b', 'b.id', '=', 'hr_employee_record_logs.delegation_person');

        $sql->where('hr_employee_record_logs.sys_users_id','=',$emp_id);
        $sql->where('hr_employee_record_logs.status', '=', 'Active');
        $sql->whereIn('hr_employee_record_logs.record_type', ['salary_restructure','promotion']);
        $sql->orderBy('hr_employee_record_logs.hr_employee_record_logs_id','DESC');
        $incrementpromotionLog = $sql->get();


        $html = '';
        foreach($incrementpromotionLog as $i=>$emp) {
            $html .= "<tr  id=".$emp->hr_employee_record_logs_id." data-record_type=".$emp->record_type." class=".'hr_employee_record_logs_id delegation_job_id'." data-status=". $emp->hr_log_status .">
                        <td class='text-center'>".($i+1)."</td>
                        <td>".ucwords(str_replace('_',' ',$emp->record_type))."</td>
                        <td>".toDated($emp->applicable_date)."</td>
                        <td>".$emp->designations_name."</td>
                        <td>".$emp->prev_designation_name."</td>
                        <td>".$emp->hr_emp_grade_name."</td>
                        <td>".$emp->prev_grade_name."</td>
                        <td class='text-right'>".number_format($emp->previous_gross,2)."</td>
                        <td class='text-right'>".number_format($emp->basic_salary,2)."</td>

                        <td class='text-right'>".number_format($emp->gross_salary-$emp->previous_gross,2)."</td>
                        <td class='text-right'>".number_format($emp->gross_salary,2)."</td>
                        <td>".$emp->delegation_person_name."</td>
                        <td>".$emp->status_flows_name."</td>
                    </tr>";
        }

        return response()->json(array('data'=>$html));
    }

    function transferHistory(Request $request){
        $emp_id = $request->sys_users_id;
        // increment & promotion
        $employeeInfo=DB::table('hr_employee_record_logs');
        $employeeInfo ->select('sys_users.id','sys_users.name','sys_users.user_code',
            'branchs.branchs_name','hr_emp_sections.hr_emp_section_name','hr_emp_units.hr_emp_unit_name','departments.departments_name','designations.designations_name',
            'tbranchs.branchs_name as tbranchs_name','thr_emp_sections.hr_emp_section_name as thr_emp_section_name','thr_emp_units.hr_emp_unit_name as thr_emp_unit_name','tdepartments.departments_name as tdepartments_name','tdesignations.designations_name as tdesignations_name',
            'sys_status_flows.status_flows_name',
            'hr_employee_record_logs.*');
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
        $employeeInfo->join('sys_status_flows', 'sys_status_flows.status_flows_id', '=', 'hr_employee_record_logs.hr_transfer_status');
        $employeeInfo->where('hr_employee_record_logs.record_type', '=', 'transfer');
        $employeeInfo->where('hr_employee_record_logs.status', '=', 'Active');
        $employeeInfo->where('hr_employee_record_logs.sys_users_id', '=', $emp_id);
        $employeeInfo->orderBy('hr_employee_record_logs_id','DESC');
        $transferLog = $employeeInfo->get();
        $html = '';
        foreach($transferLog as $i=>$emp) {
            $html .= "<tr  id=".$emp->hr_employee_record_logs_id." data-record_type=".$emp->record_type." class=".'hr_employee_record_logs_id delegation_job_id'." data-status=". $emp->hr_transfer_status .">
                        <td class='text-center'>".($i+1)."</td>
                         <td>".$emp->branchs_name."</td>
                         <td>".$emp->departments_name."</td>
                         <td>".$emp->hr_emp_section_name."</td>
                         <td>".$emp->hr_emp_unit_name."</td>
                         <td>".$emp->designations_name."</td>
                        <td>".toDated($emp->applicable_date)."</td>
                        <td>".$emp->tbranchs_name."</td>
                         <td>".$emp->tdepartments_name."</td>
                         <td>".$emp->thr_emp_section_name."</td>
                         <td>".$emp->thr_emp_unit_name."</td>
                         <td>".$emp->tdesignations_name."</td>
                        <td>".$emp->status_flows_name."</td>
                    </tr>";
        }

        return response()->json(array('data'=>$html));
    }

    function transferForm(Request $request){
        $emp_id = $request->emp_id;
        $data = [];
        $employeeInfo=DB::table('sys_users');
        $employeeInfo ->select('sys_users.*',
            'branchs.branchs_name','hr_emp_sections.hr_emp_section_name','hr_emp_units.hr_emp_unit_name','departments.departments_name','designations.designations_name');
        // current information
        $employeeInfo->join('branchs','branchs.branchs_id','=','sys_users.branchs_id');
        $employeeInfo->join('hr_emp_sections','hr_emp_sections.hr_emp_sections_id','=','sys_users.hr_emp_sections_id');
        $employeeInfo->join('hr_emp_units','hr_emp_units.hr_emp_units_id','=','sys_users.hr_emp_units_id');
        $employeeInfo->join('departments','departments.departments_id','=','sys_users.departments_id');
        $employeeInfo->join('designations','designations.designations_id','=','sys_users.designations_id');

        $employeeInfo->where('sys_users.id','=',$emp_id);
        $data['emp_log'] = $employeeInfo->first();
        return view('HR.employee.emp_transfer_form', $data);
    }

    function transferStore(Request $request){
        $emp_id = $request->sys_users_id;
        $user_info = DB::table('sys_users')->where('id', $emp_id)->first();
        $user_info_arr = array(
            'branchs_id' => $request->branchs_id,
            'designations_id' => $request->designations_id,
            'departments_id' => $request->departments_id,
            'hr_emp_sections_id' => $request->hr_emp_sections_id,
            'hr_emp_units_id' => $request->hr_emp_units_id,
            'applicable_date'=>$request->applicable_date,
            'hr_emp_grades_id' => $user_info->hr_emp_grades_id,
            'sys_users_id' => $user_info->id,
            'record_type' => 'transfer',
            'basic_salary' =>  $user_info->basic_salary,
            'house_rent' =>  $user_info->house_rent,
            'house_rent_amount' =>  $user_info->house_rent_amount,
            'min_medical' =>  $user_info->min_medical,
            'min_food' =>  $user_info->min_food,
            'min_tada' =>  $user_info->min_tada,
            'gross_salary' =>  $user_info->min_gross,
            'previous_gross' => $user_info->min_gross,
            'hr_transfer_status' => 58,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d h:i:s')
        );
        $insert = DB::table('hr_employee_record_logs')->insert($user_info_arr);
        if($insert){
            return response()->json(array('success'=>true));
        }else{
            return response()->json(array('success'=>false));
        }
    }

    function leaveForm(Request $request){
        $emp_id = $request->emp_id;
        $hr_leave_records_id = $request->record_id;
        $data = [];

        if($hr_leave_records_id){
            $leaveManager = new LeaveManager();
            $data['emp_leave_records'] = $leaveManager->getUserLeaveRecords($emp_id,$hr_leave_records_id);
        }
        $data['emp_log'] = DB::table('sys_users')->where('id', $emp_id)->first();
        $data['emp_code'] = $data['emp_log']->user_code;
        return view('HR.employee.emp_leave_form', $data);
    }

    function monthlyAttendanceReport(Request $request){
        $sql = DB::table('hr_emp_attendance')
            ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->join('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
            ->select(
                'hr_working_shifts.shift_name',
                'hr_emp_attendance.approved_status',
                'hr_emp_attendance.shift_start_time as shift_start',
                'hr_emp_attendance.shift_end_time as shift_end',
                'hr_emp_attendance.day_is',
                'hr_emp_attendance.in_time',
                'hr_emp_attendance.out_time',
                'hr_emp_attendance.daily_status'
            );

        $sql->where('hr_emp_attendance.sys_users_id',$request->sys_users_id);
        $sql->where('hr_emp_attendance.day_is','LIKE',$request->month.'%');

        $attendance_history =  $sql->orderBy('day_is', 'asc')->get();

        if(!empty($attendance_history)){
            return response()->json([
                'data' => view('HR.employee.emp_attendance', compact('attendance_history'))->render(),
                'status' => 'success',
            ]);
        }
        return response()->json(array('data'=>false));
    }

    function employeeSalaryInfo($id){
        $employeeInfo=DB::table('hr_monthly_salary_wages');
        $employeeInfo ->select('sys_users.id','sys_users.name','sys_users.user_code','hr_emp_grades.hr_emp_grade_name',
            'hr_emp_pfp_salary.pfp_target_amount',
            'hr_emp_pfp_salary.pfp_achieve_ratio',
            'hr_emp_pfp_salary.pfp_earn_amount',
            'designations.designations_name','bat_company.company_name','bat_distributorspoint.name as point_name','hr_monthly_salary_wages.*');
        $employeeInfo->join('sys_users','sys_users.id','=','hr_monthly_salary_wages.sys_users_id');
        $employeeInfo->leftJoin('hr_emp_pfp_salary', function ($join){
            $join->on('hr_emp_pfp_salary.salary_month','=','hr_monthly_salary_wages.hr_salary_month_name');
            $join->on('hr_emp_pfp_salary.sys_users_id','=','hr_monthly_salary_wages.sys_users_id');
        });
        $employeeInfo->leftJoin('hr_emp_grades','hr_emp_grades.hr_emp_grades_id','=','hr_monthly_salary_wages.hr_emp_grades_id');
        $employeeInfo->leftJoin('bat_company','bat_company.bat_company_id','=','hr_monthly_salary_wages.bat_company_id');
        $employeeInfo->leftJoin('bat_distributorspoint','bat_distributorspoint.id','=','hr_monthly_salary_wages.bat_dpid');
        $employeeInfo->leftJoin('designations','designations.designations_id','=','hr_monthly_salary_wages.designations_id');
        $employeeInfo->where('hr_monthly_salary_wages.sys_users_id',$id);
        $employeeInfo->orderby('hr_monthly_salary_wages.hr_salary_month_name','DESC');
        return $employeeInfo->get();
    }

    /*
     * Load Education Form
     */
    public function getNomineeForm(Request $request){
        $data=[];
        if (isset($request->id) && $request->id!=''){
            $data['nominee'] = DB::table('hr_emp_nominees')->where('hr_emp_nominees_id',$request->id)->first();
        }
        //dd($data);
        return view('HR.employee.nominee_form', $data);
    }

    public function storeNomineeInfo(Request $request){

        $data = array(
            'nominee_name' => $request->nominee_name,
            'nominee_relationship' => $request->nominee_relationship,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'nominee_ratio' => 100,
            'sys_users_id' => $request->sys_users_id,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d h:i:s')
        );
        if (isset($request->hr_emp_nominees_id) && $request->hr_emp_nominees_id!=''){
            $update = DB::table('hr_emp_nominees')->where('hr_emp_nominees_id', $request->hr_emp_nominees_id)->update($data);
//            AuditTrailEvent::updateForAudit($update,$data);
            $dataReturn =  DB::table('hr_emp_nominees')->where('sys_users_id', $request->sys_users_id)->where('status', 'Active')->get();
        }else{
            DB::table('hr_emp_nominees')->insert($data);
            $dataReturn =  DB::table('hr_emp_nominees')->where('sys_users_id', $request->sys_users_id)->where('status', 'Active')->get();
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }

    public function destroyNomineeInfo(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                $update = DB::table('hr_emp_nominees')->where('hr_emp_nominees_id', $id)->update(['status'=>'Inactive']);
//                AuditTrailEvent::updateForAudit($update,['status'=>'Inactive']);
            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }

    function deleteEmp(Request $request){
        $id = $request->employee_id;
        $status = $request->employee_status;

        if($status == 'Inactive'){
            $update = DB::table('sys_users')->where('id', $id)->update(['status'=>'Active']);
//            AuditTrailEvent::updateForAudit($update,['status'=>'Active']);
        }
        else{
            $update = DB::table('sys_users')->where('id', $id)->update(['status'=>'Inactive']);
//            AuditTrailEvent::updateForAudit($update,['status'=>'Inactive']);
        }

        return response()->json([
            'success'=>true,
        ]);
    }

    function calendaConfigure(){
        return view('Hr_company_calendar.calendar_config');
    }

    function calendaConfigureSetEvent(Request $request){
        dd($request->all());
    }


    //get Section by Department
    function sectionByDepartment(Request $request){
        DB::enableQueryLog();
        $sections = DB::table('hr_emp_sections')
            ->leftJoin('hr_department_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'hr_department_sections.hr_emp_sections_id')
            ->leftJoin('departments', 'departments.departments_id', '=', 'hr_department_sections.departments_id')
            ->select('hr_emp_sections.hr_emp_sections_id', 'hr_emp_sections.hr_emp_section_name')
            ->orderBy('hr_emp_section_name', 'asc')
            ->where('departments.departments_id', '=', $request->department_id)
            ->where('hr_emp_sections.status', '=', 'Active')
            ->get();
        //dd(DB::getQueryLog());
        return response()->json([
            'data'=>$sections,
        ]);
    }

    function getDPbySH($dh_id){
        $list = [];
        $previlige_points = explode(',',session('PRIVILEGE_POINT'));
        if($previlige_points){
            $list = DB::table('bat_distributorspoint')
                ->where('dsid',$dh_id)
                ->whereIn('id',$previlige_points)
                ->get();
        }

       return response()->json([
           'data'=>$list
       ]);
    }

    //Shift Calender
    function empShiftCalender(Request $request){
        $calendar_month = !empty($request->month)?$request->month:date('Y-m');
        $data['calendar_month'] = $calendar_month;
        $data['calendar_day'] = $calendar_month.'-01';
        $data['sys_users_id'] = $request->user_id;
        $all_shift = DB::table('hr_working_shifts')
            ->where('is_rotable','=',1)
            ->where('status','=','Active')
            ->get();

        $data['shiftList'] = $all_shift;

        if(!empty($data)){
            return response()->json([
                'data' => view('HR.shift_manager.shift_calendar_data', $data)->render(),
                'status' => 'success',
            ]);
        }
    }

    //Get Shift Info by Roster Type
    function getWorkingShifts(Request $request){
        $is_rotable = $request->is_rotable;
        $shift = DB::table('hr_working_shifts')->where('is_rotable', $is_rotable)->select('hr_working_shifts_id', 'shift_name')->get();
        return response()->json([
            'data'=> $shift,
        ]);
    }

    function employeeVariableSalaryInfo($id){
        $sql = DB::table('hr_emp_monthly_variable_salary')
        ->where('sys_users_id',$id)
        ->where('status','Active');
        return $sql->get();
    }
    function variableSalaryForm(Request $request){
        $emp_id = $request->emp_id;
        $vsalary_id = $request->vsalary_id;
        $data = [];
        if($vsalary_id){
            $data['emp_vsalary'] = DB::table('hr_emp_monthly_variable_salary')->where('vsalary_id',$vsalary_id)->get()->first();
        }
        $data['emp_log'] = DB::table('sys_users')->where('id', $emp_id)->first();
        $data['emp_code'] = $data['emp_log']->user_code;
        return view('HR.employee.variable_salary_form', $data);
    }

    function variableSalaryStore(Request $request){
        $emp_id = $request->emp_id;
        $vsalary_id = $request->vsalary_id;
        $data = [];
        $data['sys_users_id'] = $request->sys_users_id;
        $data['vsalary_month'] = $request->vsalary_month;
        $data['variable_salary_amount'] = $request->variable_salary_amount;
        if($vsalary_id){
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = date('Y-m-d h:i:s');
            $update = DB::table('hr_emp_monthly_variable_salary')
                ->where('sys_users_id',$request->sys_users_id)
                ->where('vsalary_month',$request->vsalary_month)
            ->update($data);

//            AuditTrailEvent::updateForAudit($update,$data);
        }else{
            $data['created_by'] = Auth::id();
            $data['created_at'] = date('Y-m-d h:i:s');
            DB::table('hr_emp_monthly_variable_salary')->insert($data);
        }
        return response()->json([
            'success'=>true,
        ]);
    }

    function deleteVsalaryRecord(Request $request){
        $data['updated_by'] = Auth::id();
        $data['updated_at'] = date('Y-m-d h:i:s');
        $data['status'] = 'Inactive';
        $delete = DB::table('hr_emp_monthly_variable_salary')->whereIn('vsalary_id',$request->vsalary_ids)->update($data);
//        AuditTrailEvent::updateForAudit($delete,$data);
        if($delete){
            return response()->json(array('success'=>true));
        }else{
            return response()->json(array('success'=>false));
        }
    }


    function separationCausesStore(Request $request){
        $data['hr_separation_causes_id']= isset($request->hr_separation_causes_id)?$request->hr_separation_causes_id:null;
        $data['separation_date']= isset($request->separation_date)?$request->separation_date:null;

        if (isset($request->hr_separation_causes_id) && isset($request->separation_date)){
            $data['status'] = 'Separated';
        }else{
            $data['status']= 'Active';
        }

        $update = DB::table('sys_users')->where('id',$request->employee_id)->upadte($data);
//        AuditTrailEvent::updateForAudit($update,$data);
       // dd($data);
        return response()->json(array('success'=>true));
    }

    function leaveBalanceEntry($sys_user_id,$joining_date){
        $user_info = DB::table('sys_users')->where('id',$sys_user_id)->get()->first();
        $join_date = strtotime($joining_date);
        $last_date = strtotime(date('Y').'-12-31');

        $diff = ceil(($last_date - $join_date)/(60*60*24));
        $ratio = $diff/365;
        $q = DB::table('hr_yearly_leave_policys')
            ->where('hr_yearly_leave_policys_year',date('Y'))
            ->where('bat_company_id',$user_info->bat_company_id);

        $leave_policy = $q->get();
        if(!empty($leave_policy)){
            DB::table('hr_yearly_leave_balances')->where('sys_users_id',$sys_user_id)
                ->where('hr_yearly_leave_balances_year',date('Y'))
                ->delete();
            $policy_info = [];
            foreach ($leave_policy as $policy){


                $policy_info[] = array(
                    'hr_yearly_leave_balances_year'=>$policy->hr_yearly_leave_policys_year,
                    'bat_company_id'=>$policy->bat_company_id,
                    'is_earn_leave'=>$policy->is_earn_leave,
                    'hr_yearly_leave_policys_name'=>$policy->hr_yearly_leave_policys_name,
                    'policy_days'=>round($policy->policy_leave_days*$ratio),
                    'balance_leaves'=>round($policy->policy_leave_days*$ratio),
                    'sys_users_id'=>$sys_user_id
                );
            }
            DB::table('hr_yearly_leave_balances')->insert($policy_info);
        }
        return true;
    }

    function employeeUploadForm(){
        $data['title'] = "Employee Upload";
        $data['companies'] = DB::table('bat_company')->pluck('company_name', 'bat_company_id');

        return view('HR.employee.upload_form', $data);
    }

    function generateIdCompanyWise($designations_id, $bat_company_id, $bat_dpid){
        //========= generate id using point =============
        $sys_unique_id_logic = DB::table('sys_unique_id_logic')->where('slug', 'bat_emp')->pluck('id_length');

        $company_short_form_ary = DB::table('bat_company')->pluck('company_short_name', 'bat_company_id')->toArray();

//        $company_short_form_ary = [
//            1 => 'ATG',
//            2 => 'ACL',
//            3 => 'AZT',
//            4 => 'ATS',
//            5 => 'MAM',
//            6 => 'SPS',
//        ];

        $designatoion_name = DB::table('designations')->where('designations_id', $designations_id)->pluck('designations_name');

        $area_string = $company_short_form_ary[$bat_company_id];

        if(strlen((string)$bat_dpid) == 1){
            $point_string = "00".$bat_dpid;
        }
        elseif(strlen((string)$bat_dpid) == 2){
            $point_string = "0".(string)$bat_dpid;
        }else{
            $point_string = (string)$bat_dpid;

        }

        $pre_fix_str =  $area_string."-".$point_string;
        $find_id_last = DB::table('sys_generated_id')
            ->where('actual_id', 'LIKE', $pre_fix_str.'%')
            ->where('slug', 'bat_emp')
            ->max('sequential_id');

//        $find_id_last = 11;
        $generate_id_string = !empty($find_id_last) ? $find_id_last+1 : 1;

        for($i = strlen((string)$generate_id_string); $i<$sys_unique_id_logic[0]; $i++){

            $generate_id_string = "0".$generate_id_string;
        }

        $final_emp_id = $pre_fix_str."-".$generate_id_string;

        $insert_id_data = [
            'slug' => 'bat_emp',
            'prefix' => 'NUL',
            'sequential_id' => $generate_id_string,
            'actual_id' => $final_emp_id,
            'created' => ''
        ];

        DB::table('sys_generated_id')->insert($insert_id_data);

        return $final_emp_id;
    }

    function employeeUploadStore(Request $request){

        $d_points_ary = DB::table('bat_distributorspoint')
                    ->where('dsid', $request->company_id)->pluck('id', 'name')->toArray();

        $document = $request->file('select_file');
        $original_name = $document->getClientOriginalName();
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $current_date_time = strtotime(date('Y-m-d H:m:s'));
        $new_name = $filename.'_'.$current_date_time. '.' . $document->getClientOriginalExtension();

        if (!is_dir(public_path('documents/employee'))) {
            mkdir(public_path('documents/employee'), 0777, true);
        }

        $document->move(public_path('documents/employee'), $new_name);

        if('csv' == $file_extension || ('xlsx' == $file_extension || 'XLSX' ==$file_extension)) {
            if('csv' == $file_extension){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }else{
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            try {
                $path = public_path('documents/employee/').$new_name;
                $spreadsheet = $reader->load($path);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                if(!empty($sheetData)){

                    $prepare_arr = [];
                    $prepare_arr_for_variable = [];
                    array_shift($sheetData);
                    $user_header = array(
                        "user_code","name", "father_name","mother_name","spouse_name","mobile","date_of_birth","blood_group","gender",
                        "religion","nid","date_of_join","reference_user_id","present_district","present_thana","present_po",
                        "present_village","permanent_district","permanent_thana","permanent_po","permanent_village","basic_salary",
                        "min_gross","designations_id","bat_company_id","bat_dpid","created_at","created_by","is_employee");

                    $designation_ary = DB::table('designations')->pluck('designations_id', 'designations_name')->toArray();
                    $grade_id_ary = DB::table('designations')->pluck('hr_emp_grade_id', 'designations_id')->toArray();

//                    dd($grade_id_ary);
                    dd($sheetData);
                    $error_distributor_point = [];
                    $error_designation = [];
                    $has_error = 0;
                    $designation_id_from_ary = 0;
                    $empCode = 0;

                    foreach ($sheetData as $k=>$value){

                        if(trim($value[1])!='') {
                            //designation exist
                            if (!array_key_exists(trim($value[3]),$designation_ary)){
//                                dd('NOT exist Designation', trim($value[3]));
                                $error_designation[] = trim($value[3]);
                                $has_error = 1;
                            }
                            else{
                                $designation_id_from_ary = $designation_ary[trim($value[3])];
                            }

                            //Distributor point exist
                            if (!array_key_exists(trim($value[2]),$d_points_ary)){
//                                dd('NOT exist Distributor Point : ', trim($value[2]), $d_points_ary);
                                $error_distributor_point[] = trim($value[2]);
                                $has_error = 1;
                            }
                            else{
                                $prepare_arr[$k]['bat_dpid'] = $d_points_ary[trim($value[2])];
                            }

                            if($has_error == 0){
                                $empCode = $this->generateIdCompanyWise($designation_ary[trim($value[3])],$request->company_id,$d_points_ary[trim($value[2])]);
                                $prepare_arr[$k]['user_code'] = $empCode;
                                $prepare_arr[$k]['nid'] = $request->company_id."0".rand(1000000,9999999)."0".$d_points_ary[trim($value[2])];
                            }

                            $prepare_arr[$k]['name'] = trim($value[1]);
                            $prepare_arr[$k]['father_name'] = 'Not Available';
                            $prepare_arr[$k]['mother_name'] = 'Not Available';
                            $prepare_arr[$k]['spouse_name'] = 'Not Available';
                            $prepare_arr[$k]['mobile'] = '08824524524';
                            $prepare_arr[$k]['date_of_birth'] = "1971-12-16";
                            $prepare_arr[$k]['blood_group'] = 'A+';
                            $prepare_arr[$k]['gender'] = 'Male';

                            $prepare_arr[$k]['religion'] = 'Islam';
                            $prepare_arr[$k]['date_of_join'] = "1971-12-16";
                            $prepare_arr[$k]['date_of_confirmation'] = "1971-12-16";
                            $prepare_arr[$k]['reference_user_id'] = '';
                            $prepare_arr[$k]['present_district'] = 'Dhaka';
                            $prepare_arr[$k]['present_thana'] = 'ADABOR';
                            $prepare_arr[$k]['present_po'] = '';
                            $prepare_arr[$k]['present_village'] = '';
                            $prepare_arr[$k]['permanent_district'] = 'Dhaka';;
                            $prepare_arr[$k]['permanent_thana'] = 'ADABOR';
                            $prepare_arr[$k]['permanent_po'] = '';
                            $prepare_arr[$k]['permanent_village'] = '';

                            $prepare_arr[$k]['basic_salary'] = trim($value[4]);
                            $prepare_arr[$k]['min_gross'] = trim($value[4]);

                            $prepare_arr[$k]['designations_id'] = $designation_id_from_ary;
                            $prepare_arr[$k]['bat_company_id'] = $request->company_id;

                            $prepare_arr[$k]['created_at'] = date('Y-m-d H:i:s');
                            $prepare_arr[$k]['created_by'] = Auth::id();
                            $prepare_arr[$k]['is_employee'] = 1;
                            $prepare_arr[$k]['hr_emp_grades_id'] = array_key_exists($designation_id_from_ary, $grade_id_ary)?$grade_id_ary[$designation_id_from_ary]:23;
                            $prepare_arr[$k]['max_variable_salary'] = !empty(trim($value[5]))?trim($value[5]):0;

                            $prepare_arr_for_variable[$k]['hr_emp_grades_id'] = array_key_exists($designation_id_from_ary, $grade_id_ary)?$grade_id_ary[$designation_id_from_ary]:2;
                            $prepare_arr_for_variable[$k]['record_type'] = 'default';
                            $prepare_arr_for_variable[$k]['record_ref'] = $empCode;
                            $prepare_arr_for_variable[$k]['component_type'] = 'Variable';
                            $prepare_arr_for_variable[$k]['component_name'] = 'PFP Amount';
                            $prepare_arr_for_variable[$k]['component_slug'] = 'pfp_amount';
                            $prepare_arr_for_variable[$k]['addition_amount'] = !empty(trim($value[5]))?trim($value[5]):0;
                            $prepare_arr_for_variable[$k]['auto_applicable'] = 'NO';
                        }
                    }

                    if($has_error > 0){
//                        dd('error: Excel data not matched with DB data: ', $error_designation, $error_distributor_point);
                        $error_string = '';

                        if(count($error_designation) > 0){
                            $error_string .= "DB data not matched with excel data : ";

                            foreach($error_designation as $info){
                                $error_string .= $info." ,";
                            }
                        }

                        if(count($error_distributor_point) > 0){
                            $error_string .= "DB data not matched with excel data : ";

                            foreach($error_distributor_point as $info){
                                $error_string .= $info." ,";
                            }
                        }

//                        dd($error_string);

                        return redirect()->route('hr-upload-employee')
                            ->with('error', $error_string);
                    }

//                    dd($prepare_arr, $prepare_arr_for_variable);

                    DB::beginTransaction();
                    try {
//                        foreach (array_chunk($prepare_arr, 1000) as $t) {
//                            DB::table('sys_users')->insert($t);
//                        }

                        foreach($prepare_arr as $key=>$info){
                            DB::table('sys_users')->insert($info);
                            $Last_id = DB::getPdo()->lastInsertId();

                            $prepare_arr_for_variable[$key]['sys_users_id'] = $Last_id;
                            DB::table('hr_emp_salary_components')->insert($prepare_arr_for_variable[$key]);
                        }

                        DB::commit();
                    } catch (\Exception $exception){
                    DB::rollback();
                    throw $exception;
                } catch (\Throwable $exception){
                    DB::rollback();
                    throw $exception;
                }
                    return redirect()->route('employee-list')
                        ->with('info','Successfully Uploaded!');
                }else{
                    return redirect()->route('hr-upload-employee')
                        ->with('warning','data is not found');
                }
            }catch (Exception $e) {
                return redirect()->route('hr-upload-employee')
                    ->with('error','Error Found!');
            }
        }
    }

    function manualAttendanceSheetCreate(Request $request){
        $sys_users_id = $request->sys_users_id;
        $month = $request->month;

            $lastDateOfMonth = date("Y-m-t", strtotime($month));
            $firstDate = $month.'-01';
            $emp_info = DB::table('sys_users')->where('id', $request->sys_users_id)->first();
            $joinDAte = strtotime($emp_info->date_of_join) > strtotime($firstDate) ? $emp_info->date_of_join: $firstDate;
            $calendarData = DB::table('hr_company_calendars')
                ->where('bat_company_id', $emp_info->bat_company_id)
                ->whereBetween('date_is', [$joinDAte, $lastDateOfMonth])
                ->select('date_is', 'day_status', 'bat_company_id')
                ->get();

            $check_emp = DB::table('hr_emp_attendance')->select('sys_users_id')
                        ->where('day_is','>=',$firstDate)
                        ->where('day_is', '<=', $lastDateOfMonth)
                        ->where('sys_users_id', $sys_users_id)->get();

            if(count($check_emp) > 0){
                return response()->json([
                    'success'=>false,
                    'message'=>'Attendance Sheet Already Created For This Employee.'
                ]);
            }

            if (!empty($calendarData)) {
                $cdata = array();
                foreach ($calendarData as $key => $citem) {
                    $cdata[$key]['sys_users_id'] = $sys_users_id;
                    $cdata[$key]['user_code'] = $emp_info->user_code;
                    $cdata[$key]['day_is'] = $citem->date_is;
                    $cdata[$key]['bat_company_id'] = $emp_info->bat_company_id;
                    $cdata[$key]['bat_dpid'] = $emp_info->bat_dpid;
                    $cdata[$key]['route_number'] = $emp_info->route_number;
                    $cdata[$key]['shift_day_status'] = $citem->day_status;
                    $cdata[$key]['shift_start_time'] = $emp_info->start_time;
                    $cdata[$key]['shift_end_time'] = $emp_info->end_time;
                    if($citem->day_status == 'R'){
                        $cdata[$key]['daily_status'] = 'P';
                    }else{
                        $cdata[$key]['daily_status'] = $citem->day_status;
                    }
                }

                DB::table('hr_emp_attendance')->insert($cdata);
            }
            return response()->json([
                'success'=>true,
            ]);
        }

      public function emargencyContactExcel()
      {
          $sql = DB::table('sys_users')
              ->select(
                  'sys_users.name as NAME',
                  'sys_users.user_code as USER CODE',
                  'sys_users.mobile as CONTACT NO',
                  'bat_distributorspoint.name as DISTRIBUTORS POINT',
                  'hr_emp_emargency_contract_info.name as EMG CONTRACT NAME',
                  'hr_emp_emargency_contract_info.mobile as EMG CONTACT MOBILE',
                  'hr_emp_emargency_contract_info.relation as EMG CONTACT RELATION',
                  'hr_emp_emargency_contract_info.address as EMG CONTACT ADDRESS'
              )
              ->leftJoin('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id')
              ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
              ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
              ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
              ->leftJoin('hr_emp_emargency_contract_info', function ($jon){
                  $jon->on('sys_users.id','=','hr_emp_emargency_contract_info.sys_users_id');
                  $jon->where('hr_emp_emargency_contract_info.is_primary','=',1);
              })

              ->where('is_employee', 1)
              ->where('sys_users.status', '!=', 'Inactive');
          $session_con = (sessionFilter('url', 'employee-list-with-all-components'));
          $session_con = trim(trim(strtolower($session_con)), 'and');
          if ($session_con) {
              $sql->whereRaw($session_con);
          }

          $sqlRes = $sql->get();

          if (!empty($sqlRes)) {

              $file_name = 'Emergency Contact List.xlsx';
              $columns = array_keys((array)$sqlRes[0]);
              $header_array = array();
              foreach ($columns as $ha) {
                  $header_array[] = [
                      'text' => $ha,
                  ];
              }

              $excel_array = array();
              foreach ($sqlRes as $res) {
                  $temp = array();
                  foreach ($columns as $col) {
                      $temp[$col] = $res->$col;
                  }
                  $excel_array[] = $temp;
              }

              $excel_array_to_send = [
                  'header_array' => $header_array,
                  'data_array' => $excel_array,
                  'file_name' => $file_name
              ];
              $fileName = exportExcel($excel_array_to_send);
              return response()->json(['status' => 'success', 'file' => $fileName]);


          }
      }
      public function getEmployeeInfoExcel(){
          $sql =  DB::table('sys_users')
              ->select(
                  'sys_users.user_code as user code',
                  'sys_users.name',
                  'designations.designations_name as designations',
                  'hr_emp_grades.hr_emp_grade_name as grade',
                  'sys_users.date_of_join as date of join',
                  'sys_users.blood_group as blood group',
                  'bat_distributorspoint.name as point name',
                  'sys_users.name_bangla as name bangla ',
                  'sys_users.date_of_birth as date of birth',
                  'sys_users.gender',
                  'sys_users.marital_status as marital status',
                  'hr_emp_nominees.nominee_name as nominee name',
                  'sys_users.father_name as father name',
                  'sys_users.mother_name as mother name',
                  DB::raw("CONCAT(sys_users.address, '<br>', sys_users.present_village, '<br>', sys_users.present_po, '(', sys_users.present_post_code, ')', sys_users.present_thana, '<br>', sys_users.present_district) AS `present address`"),
                  DB::raw("CONCAT(sys_users.permanent_village, '<br>', sys_users.permanent_po, '(', sys_users.permanent_post_code, ')', sys_users.permanent_thana, '<br>', sys_users.permanent_district) AS `permanent address`"),
                  'sys_users.religion',
                  'sys_users.nationality',
                  'sys_users.nid as nid',
                  'sys_users.mobile as contact no',
                  'sys_users.basic_salary as basic salary',
                  'hr_emp_bank_accounts.bank_name as bank name',
                  'hr_emp_bank_accounts.account_number as account number',
                  'sys_users.status'
              )
              ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
              ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
              ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
              ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
              ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
              ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
              ->leftJoin('hr_emp_nominees', 'sys_users.id', '=', 'hr_emp_nominees.sys_users_id')
              ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
              ->leftJoin('hr_emp_bank_accounts', function($join){
                  $join->on('sys_users.id', '=', 'hr_emp_bank_accounts.sys_users_id');
                  $join->on('hr_emp_bank_accounts.bank_account_types_name', '=', DB::raw("'Salary Account'"));
              })
              ->where('is_employee', 1)
              ->whereIn('sys_users.status', ['Active','Probation']);

          $session_con = (sessionFilter('url','employee-list-with-all-components'));
          $session_con = trim(trim(strtolower($session_con)),'and');
          if($session_con){
              $sql->whereRaw($session_con);
          }


          $sqlRes = $sql->get();

          if(!empty($sqlRes)){

                $file_name = 'Employee List.xlsx';
              $columns = array_keys((array)$sqlRes[0]);
             $header_array= array();
             foreach ($columns as $ha){
                 $header_array[]=[
                   'text'=>  $ha,
                 ];
             }

            $excel_array=array();
             foreach ($sqlRes as $res){
                 $temp = array();
                 foreach ($columns as $col){
                    $temp[$col]=$res->$col;
                 }
                 $excel_array[] =$temp;
             }

              $excel_array_to_send = [
                  'header_array' => $header_array,
                  'data_array' => $excel_array,
                  'file_name' => $file_name
              ];
              $fileName = exportExcel($excel_array_to_send);
              return response()->json(['status' => 'success', 'file' => $fileName]);

          }

      }
}
