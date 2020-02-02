<?php
namespace App\Http\Controllers\HR;

use App\Events\AuditTrailEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;

class EmployeeGrade extends Controller
{
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function basicFormGrade($hr_emp_grades_id = null){
        $data = [];
        if ($hr_emp_grades_id){
            $data['emp_grade_info'] =  DB::table('hr_emp_grades')->where('hr_emp_grades_id',$hr_emp_grades_id)->first();
        }
        return view('HR.employee.grade.emp_grade_entry_form', $data);
    }

    /*
     * Store Employee Personal Information
     */
    public function storeGradeInfo(Request $request, $id=null){
        $data['hr_emp_grade_name'] = $request->hr_emp_grade_name;
        $data['basic_salary'] = $request->basic_salary;
        $data['yearly_increment'] = $request->yearly_increment;
        $data['insurance_applicable'] = $request->insurance_applicable;
       // $data['insurance_amount'] = $request->insurance_amount;
        $data['pf_applicable'] = $request->pf_applicable;
      //  $data['pf_amount'] = $request->pf_amount;
        $data['gf_applicable'] = $request->gf_applicable;
        //$data['gf_amount'] = $request->gf_amount;
        $data['late_deduction_applied'] = $request->late_deduction_applied;
        $data['description'] = $request->description;

        if($id !=null){
            $update = DB::table('hr_emp_grades')->where('hr_emp_grades_id',$id)->update($data);
//            AuditTrailEvent::updateForAudit($update, $data);
             return 'success';
        }
        else{
            DB::table('hr_emp_grades')->insert($data);
            return 'success';
        }
    }



    public function empGradeList(Request $request){
        return view('HR.employee.grade.emp_grade_list');
    }

     function deleteEmpGrade(Request $request){
        $hr_emp_grades_id = $request->hr_emp_grades_id;
         $update = DB::table('hr_emp_grades')->whereIn('hr_emp_grades_id', $hr_emp_grades_id)->update(['status'=>'Inactive']);
//         AuditTrailEvent::updateForAudit($update, ['status'=>'Inactive']);
        return response()->json([
            'success'=>true,
        ]);
    }

    public function empGradeComponentList($hr_emp_grades_id){
        $data=[];
        $data['emp_grade_info'] =  DB::table('hr_emp_grades')->where('hr_emp_grades.hr_emp_grades_id',$hr_emp_grades_id)->first();
            if($hr_emp_grades_id){
                $data['emp_grade_component_info'] =  DB::table('hr_grade_components')
                    ->join('hr_emp_grades','hr_grade_components.hr_emp_grades_id','=','hr_emp_grades.hr_emp_grades_id')
                    ->where('hr_grade_components.status', 'Active')
                    ->where('hr_emp_grades.hr_emp_grades_id',$hr_emp_grades_id)->get();
            }
        return view('HR.employee.grade.emp_grade_component_list',$data);
    }


    //Component From
    function componentForm(Request $request){
        $data = [];
        $data['compoment_info'] = [];
        $data['hr_emp_grades_id'] = $request->hr_emp_grades_id;
        $data['hr_emp_grade_name'] = $request->hr_emp_grade_name;
        $data['basic_salary'] = $request->basic_salary;
        if(isset($request->hr_grade_components_id)){
            $data['compoment_info'] = DB::table('hr_grade_components')->where('hr_grade_components_id',$request->hr_grade_components_id)->first();
        }
        return view('HR.employee.grade.grade_component_form', $data);
    }

    public function storeGradeComponentInfo(Request $request){
        //dd($request->all());
        $data = array();
        $data['hr_emp_grades_id'] = $request->hr_emp_grades_id;
        $data['component_name'] = $request->component_name;
        $data['component_type'] = $request->component_type;

        if($data['component_type'] == 'Deduction'){
            $data['deduction_amount'] = $request->amount;
            $data['addition_amount'] = 0;
        }
        else{
            $data['addition_amount'] = $request->amount;
            $data['deduction_amount'] = 0;
        }

        $data['component_slug'] = str_slug($request->component_name, '_');
        $data['ratio_of_basic'] = $request->ratio_of_basic;
        $data['component_note'] = $request->component_note;

        if($request->has('auto_applicable')){
           $data['auto_applicable'] = 'YES';
        }
        else {
            $data['auto_applicable'] = 'NO';
        }

        if(isset($request->hr_grade_components_id) && $request->hr_grade_components_id!=''){
            $update = DB::table('hr_grade_components')->where('hr_grade_components_id',$request->hr_grade_components_id)->update($data);
//            AuditTrailEvent::updateForAudit($update, $data);
            self::updateGross($request->hr_emp_grades_id);
            return response()->json(array('success' => true));
        }
        else{
            DB::table('hr_grade_components')->insert($data);
            self::updateGross($request->hr_emp_grades_id);
            return response()->json(array('success' => true));
        }
    }

    static function updateGross($grade){
       $query =  DB::table('hr_grade_components')->where('hr_emp_grades_id',$grade)->where('auto_applicable','YES')->where('status','Active');
       $addition_amount = $query->sum('addition_amount');
       $deduction_amount =  $query->sum('deduction_amount');
       $gross = ($addition_amount-$deduction_amount);
        return DB::table('hr_emp_grades')->where('hr_emp_grades_id',$grade)->update(['gross_salary'=>DB::raw("basic_salary+$gross"),'grade_addition_amount'=>$addition_amount, 'grade_deduction_amount'=>$deduction_amount]);
//        return  AuditTrailEvent::updateForAudit($update, ['gross_salary'=>DB::raw("basic_salary+$gross"),'grade_addition_amount'=>$addition_amount, 'grade_deduction_amount'=>$deduction_amount]);
    }


    public function basicFormGradeComponent($hr_emp_grades_id = null){
        $data=[];
        if ($hr_emp_grades_id !=null){
            $data['emp_grade_info'] =  DB::table('hr_emp_grades')->where('hr_emp_grades.hr_emp_grades_id',$hr_emp_grades_id)->first();
        }
        return view('HR.employee.grade.emp_grade_component_entry_form', $data);
    }

    /*
     * Store Employee Personal Information
     */
    function deleteEmpGradeComponent(Request $request){
        $update = DB::table('hr_grade_components')->whereIn('hr_grade_components_id', $request->hr_grade_components_id)->update(['status'=>'Inactive']);
//        AuditTrailEvent::updateForAudit($update,['status'=>'Inactive']);
        return response()->json(array('success' => true));
    }
   
}
