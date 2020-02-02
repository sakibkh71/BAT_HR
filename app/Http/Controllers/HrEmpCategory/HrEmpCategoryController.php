<?php

namespace App\Http\Controllers\HrEmpCategory;

use App\Http\Controllers\controller;
use App\Models\Hr_emp_category;
use Illuminate\Http\Request;
use Validator;
use Auth;
use URL;
use DB;
use Session;

class HrEmpCategoryController extends Controller {
    public $data = [];
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }
    //Add Product Category
    public function create($id = ''){
        if (!empty($id)){
            $this->data['category'] = DB::table('hr_emp_categorys')
                ->select("hr_emp_categorys.hr_emp_categorys_id",
                    "hr_emp_categorys.hr_emp_category_name",
                    "hr_emp_categorys.sub_type_of",
                    "hr_emp_categorys.parents",
                    "hr_emp_categorys.description",
                    DB::raw("parent_category.hr_emp_category_name AS sub_type_name"))
                ->leftJoin('hr_emp_categorys AS parent_category', function ($join) {
                    $join->on('parent_category.hr_emp_categorys_id', '=', 'hr_emp_categorys.sub_type_of');
                })
                ->where('hr_emp_categorys.hr_emp_categorys_id', $id)->first();

            $this->data['subtype_of_list'] = $this->buildList(0,
                $this->data['category']->sub_type_of,
                $this->data['category']->hr_emp_categorys_id);
        }else{
            $this->data['subtype_of_list'] = $this->buildList(0);
        }
        return view('Hr_emp_category.entry', $this->data);
    }

    public function buildList($sub_type_of = 0, $selected = null, $accept = '', $parent = '' ){
        $string ='';
        $checked = '';
        $lists = DB::table('hr_emp_categorys')
            ->where('sub_type_of', $sub_type_of)
            ->whereNotIn('hr_emp_categorys_id', [$accept])
            ->where('status', 'Active')
            ->get();

        if (count($lists)){
            $string .= $sub_type_of == 0 ? '<ul class="sub-type-list">':'<ul>';
            foreach ($lists as $i => $list) {
                $parent_name = DB::table('hr_emp_categorys')->where('hr_emp_categorys_id', $list->sub_type_of)->first();
                $catname = !empty($parent_name->hr_emp_category_name) ? $parent_name->hr_emp_category_name:'';
                if ($parent =='' && $i ==0){
                    $parent = $catname;
                }elseif($i ==0){
                    $parent = $parent . ' > '.  $catname;
                }

                $string .= '<li>';
                $checked = $selected == $list->hr_emp_categorys_id ? 'checked' : '';

                $pr = !empty($parent) ? $parent . ' > ' : '';
                $pr .= $list->hr_emp_category_name;

                $string .= '<label for="cat' . $list->hr_emp_categorys_id
                    . '" data-id="' . $list->hr_emp_categorys_id
                    . '" data-name="'. $list->hr_emp_category_name
                    . '" data-parent="'. $pr . '">
                    <input type="radio" name="sub_type_of" value="'
                    . $list->hr_emp_categorys_id
                    . '" id="cat'. $list->hr_emp_categorys_id .'" '
                    . $checked . '> '
                    . $list->hr_emp_category_name . ' </label>';

                if (!empty($list->hr_emp_categorys_id)) {
                    $string .= $this->buildList($list->hr_emp_categorys_id, $selected, $accept, $parent);
                }
                $string .= '</li>';
            }
            $string .= '</ul>';
        }
        return $string;
    }
    // Store Product Category
    public function store(Request $request){
        $insertData = array(
            'hr_emp_category_name'    => $request->hr_emp_category_name,
            'sub_type_of'               => !empty($request->sub_type_of) ? $request->sub_type_of : 0,
            'parents'                   => $request->parents !='' ? $request->parents : null,
            'description'               => $request->description,
            'status'                    => $request->status,
            'created_at'                => date('Y-m-d'),
            'created_by'                => Auth::id(),
        );
        if(isset($request->hr_emp_categorys_id) && !empty(isset($request->hr_emp_categorys_id))){
            DB::table('hr_emp_categorys')->where('hr_emp_categorys_id', $request->hr_emp_categorys_id)->update($insertData);
            $insertid = $request->hr_emp_categorys_id;
        }else{
            $insertid = DB::table('hr_emp_categorys')->insertGetId($insertData);
        }
        Session::flash('succ_msg', 'Employee Category Added Successfully');
        return redirect()->route('emp-category-entry', $insertid);
    }
}
