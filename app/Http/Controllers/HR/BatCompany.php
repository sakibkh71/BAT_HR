<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;
use App\Helpers\PdfHelper;
use Symfony\Component\Routing\Tests\Fixtures\AnnotationFixtures\RequirementsWithoutPlaceholderNameController;

class BatCompany extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function pfPolicyList(){
        return view('HR.employee.pf_policy_list');
    }
    public function editPfPolicy($hr_company_pf_policy_id=null){
        $company_pf_policy_details=null;
        $data=array();
        if($hr_company_pf_policy_id !=null) {
            $company_pf_policy_details = DB::table('hr_company_pf_policys')->where('hr_company_pf_policys_id',$hr_company_pf_policy_id)->first();
        }

        if($company_pf_policy_details !=null){
            $data['company_pf_policy_details']= $company_pf_policy_details;
        }
        return view('HR.employee.edit_pf_policy',$data);

    }

    public function updateCompanyPFPolicy(Request $request){
        $company_id=$request->company_id;
        $ratio_of_basic=$request->ratio_of_basic;
        $employee_ratio=$request->employee_ratio;
        $company_ratio=$request->company_ratio;
        $company_pf_policy_id=$request->company_pf_policy_id;
        $joining_policy=$request->joining_policy;
        $termination_policy=$request->termination_policy;

        $data=array(
            'ratio_of_basic'=>$ratio_of_basic,
            'employee_ratio'=>$employee_ratio,
            'company_ratio'=>$company_ratio,
            'joining_policy'=>$joining_policy,
            'termination_policy'=>$termination_policy
        );

            $update=DB::table('hr_company_pf_policys')->where('bat_company_id',$company_id)->where('hr_company_pf_policys_id',$company_pf_policy_id)->update($data);
            $update = DB::unprepared("UPDATE sys_users SET pf_amount_employee=((basic_salary*$ratio_of_basic*$request->employee_ratio)/10000),pf_amount_company=((basic_salary*$ratio_of_basic*$request->company_ratio)/10000) WHERE bat_company_id='$company_id'");
        return response()->json([
            'success'=>true
        ]);
    }


    public function companyOrganogramList(){
        return view('HR.employee.company_organogram_list');
    }

    public function companyOrganogramEdit($bat_company_id=null){
        $company_organogram_details=null;
        $data=array();
        if($bat_company_id !=null) {
            $company_organogram_details = DB::table('bat_company')->where('bat_company_id',$bat_company_id)->first();
        }

        if($company_organogram_details !=null){
            $data['company_organogram_details']= $company_organogram_details;
        }
        return view('HR.employee.edit_company_organogram',$data);

    }
    public function updateCompanyOrganogram(Request $request){
        $data=array(
            'bat_company_id'=>$request->bat_company_id,
            'sm'=>$request->sm,
            'ss'=>$request->ss,
            'sr'=>$request->sr,
            'esr'=>$request->esr,
            'asr'=>$request->asr,
            'easr'=>$request->easr
        );

        $update=DB::table('bat_company')->where('bat_company_id',$request->bat_company_id)->update($data);
    }

}
