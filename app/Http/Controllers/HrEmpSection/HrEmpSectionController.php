<?php

namespace App\Http\Controllers\HrEmpSection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Response;
use Session;
use View;
use Redirect;
use App\Helpers\PdfHelper;

class HrEmpSectionController extends Controller {

    public function createSection(Request $request, $id=null){
        $data = [];
        $data ['pagetitle'] = 'Create Section';
        if ($id !=null){
            $data ['pagetitle'] = 'Edit Section';
            DB::connection()->enableQueryLog();
            $data['section'] = DB::table('hr_emp_sections')
            ->leftJoin('hr_department_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'hr_department_sections.hr_emp_sections_id')
                ->leftJoin('departments', 'departments.departments_id', '=', 'hr_department_sections.departments_id')
                ->select('hr_emp_sections.*', 'departments.departments_id')
                ->where('hr_emp_sections.hr_emp_sections_id',$id)->first();
            //dd(DB::getQueryLog());

            //dd($data['section']);
        }
        return view('HR.section.create_section', $data);
    }

    public function storeSection(Request $request, $id=null){

        $data['hr_emp_section_name'] = $request->hr_emp_section_name;
        $data['description'] = $request->description;
        $data['status'] = $request->status;

        $depdata['departments_id'] = $request->departments_id;

        if ($id !=null){
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = date('Y-m-d h:i:s');
            DB::table('hr_emp_sections')->where('hr_emp_sections_id', $id)->update($data);

            $exDepartment = DB::table('hr_department_sections')->where('hr_emp_sections_id', $id)->first();
            if ($exDepartment ){
                DB::table('hr_department_sections')->where('hr_emp_sections_id', $id)->update($depdata);
            }else{
                DB::table('hr_department_sections')->insert(array(
                    'departments_id'=>$request->departments_id,
                    'hr_emp_sections_id'=>$id,
                ));
            }

        }else{
            $data['created_by'] = Auth::id();
            $data['created_at'] = date('Y-m-d h:i:s');

            DB::table('hr_emp_sections')->insert($data);

            $insertId = DB::getPdo()->lastInsertId();
            if ($insertId){
                $depdata['hr_emp_sections_id'] =  $insertId;
                $depdata['created_by'] = Auth::id();
                $depdata['created_at'] = date('Y-m-d h:i:s');

                DB::table('hr_department_sections')->insert($depdata);
            }
        }
        return redirect()->route('hr-employee-section')->with('success', 'Section Entry Successfully!');
    }

    //section list
    public function sectionList(){
        $data['sections'] = DB::table('hr_emp_sections')
            ->leftJoin('hr_department_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'hr_department_sections.hr_emp_sections_id')
            ->leftJoin('departments', 'departments.departments_id', '=', 'hr_department_sections.departments_id')
            ->select('hr_emp_sections.*', 'departments.departments_name')
            ->orderBy('hr_emp_sections_id', 'desc')
            ->get();
        return view('HR.section.list', $data);
    }

    //Data Destroy
    public function destroySection(Request $request){
        if (!empty($request->ids) && count($request->ids)>0){
            foreach($request->ids as $id){
                $delItem = DB::table('hr_emp_sections')
                    ->leftJoin('hr_department_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'hr_department_sections.hr_emp_sections_id')
                    ->leftJoin('departments', 'departments.departments_id', '=', 'hr_department_sections.departments_id')
                    ->select('hr_emp_sections.*', 'departments.departments_id')
                    ->where('hr_emp_sections.hr_emp_sections_id',$id)->first();

                DB::table('hr_emp_sections')->where('hr_emp_sections_id', $delItem->hr_emp_sections_id)->delete();
                DB::table('hr_department_sections')->where('hr_emp_sections_id', $delItem->hr_emp_sections_id)->where('departments_id',  $delItem->departments_id)->delete();

            }
            $dataReturn = $request->ids;
        }
        return response()->json([
            'success'=>true,
            'data'=>$dataReturn,
        ]);
    }
}
