<?php

namespace App\Http\Controllers\HrEmpGrade;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Response;
use Session;
use View;
use Redirect;
use App\Helpers\PdfHelper;

class HrEmpGradeController extends Controller {
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
     * Display Grade List
     */
    public function gradeList(){
        $data['emp_grades']=DB::table('hr_emp_grades')
            ->selectRaw("
                hr_emp_grades.hr_emp_grades_id,
                hr_emp_grades.hr_emp_grade_name,
                hr_emp_grades.basic_salary,
                hr_emp_grades.house_rent_amount,
                hr_emp_grades.house_rent,
                hr_emp_grades.min_medical,
                hr_emp_grades.min_tada,
                hr_emp_grades.min_food,
                hr_emp_grades.min_gross,               
                hr_emp_grades.yearly_increment,
                hr_emp_grades.attendance_bonus,
                CASE WHEN hr_emp_grades.ot_applicable = 0 OR hr_emp_grades.ot_applicable IS NULL THEN 'NO' ELSE 'YES' END AS ot_applicable,
                CASE WHEN hr_emp_grades.pf_applicable = 0 OR hr_emp_grades.pf_applicable IS NULL THEN 'NO' ELSE 'YES' END AS pf_applicable,
                CASE WHEN hr_emp_grades.insurance_applicable = 0 OR hr_emp_grades.insurance_applicable IS NULL THEN 'NO' ELSE 'YES' END AS insurance_applicable,
                hr_emp_grades.description,
                hr_emp_grades.status"
            )
            ->where('status', 'Active')
            ->orderBy('hr_emp_grades.hr_emp_grades_id', 'DESC')->get();
        return view('HrEmpGrade.emp_rade_list',$data);
    }


    /*
     * Entry/Edit Grade List
     */
    public function gradeEntry(Request $request, $id=null){
        $data=[];
        if ($id !=null){
            $data['empgrade'] = DB::table('hr_emp_grades')->where('hr_emp_grades_id',$id)->first();
        }
        return view('HrEmpGrade.emp_grade_entry',$data);
    }

    /*
     * Store Employee Grade Data
     */
    public function gradeStpre(Request $request, $id=null){
        $insert_data=[
            'hr_emp_grade_name'        => $request->hr_emp_grade_name,
            'basic_salary'             => $request->basic_salary,
            'house_rent_amount'        => $request->house_rent_amount,
            'house_rent'               => $request->house_rent,
            'min_medical'              => $request->min_medical,
            'min_tada'                 => $request->min_tada,
            'min_food'                 => $request->min_food,
            'min_gross'                => $request->min_gross,
            'yearly_increment'         => $request->yearly_increment,
            'attendance_bonus'         => $request->attendance_bonus,
            'ot_applicable'            => $request->ot_applicable,
            'pf_applicable'            => $request->pf_applicable,
            'insurance_applicable'     => $request->insurance_applicable,
            'description'              => $request->description,
            'status'                   => $request->status,
        ];
        if ($id !=null){
            DB::table('hr_emp_grades')->where('hr_emp_grades_id', $id)->update($insert_data);
            Session::flash('succ_msg_po_create', 'Employee Grade Edit Successfully');
        }else{
            DB::table('hr_emp_grades')->insert($insert_data);
            Session::flash('succ_msg_po_create', 'Employee Grade Added Successfully');
        }
        return redirect()->route('hr-emp-grade');
    }

    /*
     * Destroy Employee Grade DAta
     */
    public  function destroy(Request $request){
        if (!empty($request->ids)){
            //$ids = implode(',', $request->ids);
            DB::table('hr_emp_grades')->whereIn('hr_emp_grades_id', $request->ids)->update(['status'=>'Inactive']);
            return response()->json([
                'status' => 'success'
            ]);
        }else{
            return response()->json([
                'status' => 'error',
            ]);
        }
    }
}
