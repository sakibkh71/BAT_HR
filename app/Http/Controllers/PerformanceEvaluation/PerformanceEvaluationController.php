<?php

namespace App\Http\Controllers\PerformanceEvaluation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Mpdf\Tag\P;
use Redirect;
use Auth;
use Response;
use App\Helpers\PdfHelper;
use Carbon\Carbon;

//for excel library
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
//use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use exportHelper;


class PerformanceEvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function headTitle(){

        $data =  [];
        $data['api_list'] = DB::table('pe_auto_apis')->where('status', 'Active')->get();

        return view('PerformanceEvaluation.head_title', $data);
    }

    //Store and Edit
    public function storeHeadTitle(Request $request){

        $data = [
            'pe_head_titles_name'=> $request->head_name,
            'details'=> $request->head_details,
//            'has_child_kpi'=> $request->has_child_kpi,
//            'type'=> $request->head_type,
//            'pe_auto_apis_id'=> $request->head_type == 'Auto'?$request->auto_api:'',
            'status'=> $request->head_status
        ];

        DB::beginTransaction();

        try{
            if($request->title_id > 0){
                DB::table('pe_head_titles')->where('pe_head_titles_id', $request->title_id)->update($data);
            }else{
                DB::table('pe_head_titles')->insert($data);
            }

            DB::commit();
            $data['code'] = 200;
            $data['msg'] = 'Add Successfully !';
            $data['insert_or_update'] = $request->title_id;

        }catch (\Exception $e) {
            DB::rollback();
            $data['code'] = 500;
            $data['msg'] = 'No Added !';
        }

        return $data;

    }

    public function getHeadData($id){

        $data['info'] = DB::table('pe_head_titles')->where('pe_head_titles_id', $id)->first();
        return $data;
    }

    public function kpiQuestion(Request $request){

        $data = [];
        $data['api_list'] = DB::table('pe_auto_apis')->where('status', 'Active')->get();
        return view('PerformanceEvaluation.kpi_question', $data);
    }

    public function storeKpiQuestion(Request $request){

        $data = [
            'question'=> $request->question,
            'details'=> $request->question_details,
            'status'=> $request->question_status,
            'type'=> $request->head_type,
            'pe_auto_apis_id'=> $request->head_type == 'Auto'?$request->auto_api:''
        ];

//        dd($request->all(), $data);

        DB::beginTransaction();

        try{
            if($request->question_id > 0){
                DB::table('pe_kpi_questions')->where('pe_kpi_questions_id', $request->question_id)->update($data);
            }else{
                DB::table('pe_kpi_questions')->insert($data);
            }

            DB::commit();
            $data['code'] = 200;
            $data['msg'] = 'Add Successfully !';
            $data['insert_or_update'] = $request->question_id;

        }catch (\Exception $e) {
            DB::rollback();
            $data['code'] = 500;
            $data['msg'] = 'No Added !';
        }

        return $data;
    }

    public function getQuestionData($id){

        $data['info'] = DB::table('pe_kpi_questions')->where('pe_kpi_questions_id', $id)->first();
        return $data;
    }

    public function configurationList(){

        $designation_ary = [];
        $kpi_question_ary = [];
        $list_ary = [];
        $data_list = DB::table('pe_configs')->select('pe_configs.pe_configs_id','pe_configs.evaluate_by','pe_configs_name', 'designations', 'year',
                        'pe_config_details.pe_head_titles_id', 'pe_config_details.weight','pe_config_details.pe_kpi_questions_id',
                        'pe_head_titles.pe_head_titles_name')
                        ->join('pe_config_details', 'pe_configs.pe_configs_id', '=', 'pe_config_details.pe_configs_id')
                        ->join('pe_head_titles', 'pe_config_details.pe_head_titles_id', '=', 'pe_head_titles.pe_head_titles_id')
                        ->where('pe_configs.status', 'Active')->get();

        foreach($data_list as $info){
            $list_ary[$info->pe_configs_id]['conf_name'] = $info->pe_configs_name;
            $list_ary[$info->pe_configs_id]['conf_id'] = $info->pe_configs_id;
            $list_ary[$info->pe_configs_id]['designations'] = explode(',', $info->designations);
            $list_ary[$info->pe_configs_id]['evaluate_by'] = explode(',', $info->evaluate_by);
            $list_ary[$info->pe_configs_id]['year'] = $info->year;
            $list_ary[$info->pe_configs_id][$info->pe_head_titles_id] = $info;
        }
        
        $designation = DB::table('designations')->where('status', 'Active')->get();
        $questions = DB::table('pe_kpi_questions')->where('status', 'Active')->get();
        foreach($designation as $info){
            $designation_ary[$info->designations_id] = $info->designations_name;
        }

        foreach($questions as $info){
            $kpi_question_ary[$info->pe_kpi_questions_id] = $info->question;
        }
        $data['designation_ary'] = $designation_ary;
        $data['kpi_question_ary'] = $kpi_question_ary;
        $data['list'] = $list_ary;

//        dd($data, $list_ary);

        return view('PerformanceEvaluation.configuration_list', $data);
    }

    public function storeConfig(Request $request){

        $result_ary = [];
        $mendatory_field = 0;
        $total_weight = 0;

        //check ..
        foreach($request->designation as $info){
            $q = DB::table('pe_configs')->where('year', $request->year)->whereRaw("FIND_IN_SET($info,designations)")->get();

            if(count($q) > 0){
                $data['code'] = 500;
                $data['msg'] = 'Configuration Available For Same Year, Same Designation';
                return $data;
            }
        }

        foreach($request->head_ary as $head_key => $head_val){
//            echo "Head:".$head_val;
            if(!empty($head_val) && !empty($request->head_weight[$head_key])){
                $result_ary[$head_val]['weight'] =   $request->head_weight[$head_key];
                $result_ary[$head_val]['question'] = !empty($request->question_ary[$head_key])?implode(",",$request->question_ary[$head_key]):'';
                $total_weight += $request->head_weight[$head_key];

                if(!empty($request->question_ary[$head_key])){
                    foreach($request->question_ary[$head_key] as $q_key => $q_val){
                        if(empty($q_val)){
                            $mendatory_field = 1;
                        }
                    }
                }
            }
            else{
                $mendatory_field = 1;
            }
        }

        if($mendatory_field == 1){
            $data['code'] = 500;
            $data['msg'] = 'Please Fill Up Mandatory Field !';
            return $data;
        }

        if($total_weight != 100){
//            dump('Weight Total Must Be 100 !');
            $data['code'] = 500;
            $data['msg'] = 'Weight Total Must Be 100 !';
            return $data;
        }



        DB::beginTransaction();

        try{

            $config_tbl_data = [
                'pe_configs_name'=>$request->config_name,
                'designations'=>implode(",",$request->designation),
                'evaluate_by'=>implode(",",$request->designation_by),
                'year'=>$request->year,
                'status'=>'Active'
            ];

            DB::table('pe_configs')->insert($config_tbl_data);

            $last_insert_id = DB::getPdo()->lastInsertId();

            foreach($result_ary as $key=>$val){
                DB::table('pe_config_details')->insert([
                    'pe_configs_id'=>$last_insert_id,
                    'pe_head_titles_id'=>$key,
                    'weight'=>$val['weight'],
                    'pe_kpi_questions_id'=>$val['question'],
                    'status'=>'Active'
                ]);
            }

            DB::commit();
            $data['code'] = 200;
            $data['msg'] = 'Add Successfully !';
            $data['insert_or_update'] = $request->question_id;

        }catch (\Exception $e) {
            DB::rollback();
            $data['code'] = 500;
            $data['msg'] = 'No Added !';
        }

        return $data;
    }

    public function createConfig(Request $request){
        $data['heads'] = DB::table('pe_head_titles')->where('status', 'Active')->get();
        $data['questions'] = DB::table('pe_kpi_questions')->where('status', 'Active')->get();
        $data['designations'] = DB::table('designations')->where('status', 'Active')->get();

        return view('PerformanceEvaluation.create_configuration', $data);
    }

    public function getConfigView($id){

        $result_ary = [];

        $designation = DB::table('designations')->where('status', 'Active')->get();
        $questions = DB::table('pe_kpi_questions')->where('status', 'Active')->get();
        foreach($designation as $info){
            $designation_ary[$info->designations_id] = $info->designations_name;
        }

        foreach($questions as $info){
            $kpi_question_ary[$info->pe_kpi_questions_id] = $info->question;
        }

        $data = DB::table('pe_configs')->select('pe_configs.pe_configs_id','pe_configs_name', 'designations', 'year',
            'pe_config_details.pe_head_titles_id', 'pe_config_details.weight','pe_config_details.pe_kpi_questions_id',
            'pe_head_titles.pe_head_titles_name')
            ->join('pe_config_details', 'pe_configs.pe_configs_id', '=', 'pe_config_details.pe_configs_id')
            ->join('pe_head_titles', 'pe_config_details.pe_head_titles_id', '=', 'pe_head_titles.pe_head_titles_id')
            ->where('pe_configs.status', 'Active')->where('pe_configs.pe_configs_id',$id)->get();

//        dd($data);

        foreach($data as $info){
            $result_ary[$info->pe_configs_id]['conf_name'] = $info->pe_configs_name;
            $result_ary[$info->pe_configs_id]['year'] = $info->year;
            $result_ary[$info->pe_configs_id]['designations'] = explode(',', $info->designations);
            $result_ary[$info->pe_configs_id][$info->pe_head_titles_id] = $info;
        }

        $string = "<div>";
        foreach($result_ary as $info){

            $string .="<p><strong>Configuration Name:</strong>".$info['conf_name']."</p>";
            $string .="<p><strong>Configuration Year:</strong>".$info['year']."</p>";
            $string .="<p><strong>Designation :</strong> ";
            foreach($info['designations'] as $des){
//                echo "---".$designation_ary[$des].",";
                $string .= $designation_ary[$des].",";
            }
            $string .="</p>";

            $string .="<p>";
            foreach($info as $key=>$val){
                if(!in_array($key, ['conf_name', 'year', 'designations'])){
//                    echo "---Title Name :".$val->pe_head_titles_name;
                    $string .="<strong>".$val->pe_head_titles_name."</strong><br/>";
                    $questions = explode(',', $val->pe_kpi_questions_id);
                    foreach($questions as $q){
                        $string .="<strong>* </strong>".$kpi_question_ary[$q]."<br/>";
                    }
                }
            }
            $string .="</p>";
        }

        $string .="</div>";

        return $string;
    }

    public function configEditForm($id){
        $result_ary = [];

        $result = DB::table('pe_configs')->select('pe_configs.pe_configs_id','pe_configs.evaluate_by','pe_configs_name', 'designations', 'year',
            'pe_config_details.pe_head_titles_id', 'pe_config_details.weight','pe_config_details.pe_kpi_questions_id',
            'pe_head_titles.pe_head_titles_name')
            ->join('pe_config_details', 'pe_configs.pe_configs_id', '=', 'pe_config_details.pe_configs_id')
            ->join('pe_head_titles', 'pe_config_details.pe_head_titles_id', '=', 'pe_head_titles.pe_head_titles_id')
            ->where('pe_configs.status', 'Active')->where('pe_configs.pe_configs_id',$id)->get();

        foreach($result as $info){
            $result_ary[$info->pe_configs_id]['conf_name'] = $info->pe_configs_name;
            $result_ary[$info->pe_configs_id]['year'] = $info->year;
            $result_ary[$info->pe_configs_id]['designations'] = explode(',', $info->designations);
            $result_ary[$info->pe_configs_id][$info->pe_head_titles_id] = $info;
            $result_ary[$info->pe_configs_id]['designations_by'] = explode(',', $info->evaluate_by);
        }

        $data['result_ary'] = $result_ary;
        $data['heads'] = DB::table('pe_head_titles')->where('status', 'Active')->get();
        $data['questions'] = DB::table('pe_kpi_questions')->where('status', 'Active')->get();
        $data['designations'] = DB::table('designations')->where('status', 'Active')->get();
        $data['config_id'] = $id;

        return view('PerformanceEvaluation.editForm', $data);
//        dd($data, $result_ary);
    }

    public function updateConfig(Request $request){

        $result_ary = [];
        $mendatory_field = 0;
        $total_weight = 0;

        //check ..
        foreach($request->designation as $info){
            $q = DB::table('pe_configs')->where('pe_configs_id', '!=', $request->config_id)->where('year', $request->year)->whereRaw("FIND_IN_SET($info,designations)")->get();

            if(count($q) > 0){
                $data['code'] = 500;
                $data['msg'] = 'Configuration Available For Same Year, Same Designation';
                return $data;
            }
        }

        foreach($request->head_ary as $head_key => $head_val){
//            echo "Head:".$head_val;
            if(!empty($head_val) && !empty($request->head_weight[$head_key])){
                $result_ary[$head_val]['weight'] =   $request->head_weight[$head_key];
                $result_ary[$head_val]['question'] = !empty($request->question_ary[$head_key])?implode(",",$request->question_ary[$head_key]):'';
                $total_weight += $request->head_weight[$head_key];

                if(!empty($request->question_ary[$head_key])){
                    foreach($request->question_ary[$head_key] as $q_key => $q_val){
                        if(empty($q_val)){
                            $mendatory_field = 1;
                        }
                    }
                }
            }
            else{
                $mendatory_field = 1;
            }
        }

        if($mendatory_field == 1){
            $data['code'] = 500;
            $data['msg'] = 'Please Fill Up Mandatory Field !';
            return $data;
        }

        if($total_weight != 100){
//            dump('Weight Total Must Be 100 !');
            $data['code'] = 500;
            $data['msg'] = 'Weight Total Must Be 100 !';
            return $data;
        }



        DB::beginTransaction();

        try{

            $config_tbl_data = [
                'pe_configs_name'=>$request->config_name,
                'designations'=>implode(",",$request->designation),
                'evaluate_by'=>implode(",",$request->designation_by),
                'year'=>$request->year,
                'status'=>'Active'
            ];

            DB::table('pe_configs')->where('pe_configs_id', $request->config_id)->update($config_tbl_data);

            $last_insert_id = $request->config_id;
            DB::table('pe_config_details')->where('pe_configs_id', $last_insert_id)->delete();

            foreach($result_ary as $key=>$val){
                DB::table('pe_config_details')->insert([
                    'pe_configs_id'=>$last_insert_id,
                    'pe_head_titles_id'=>$key,
                    'weight'=>$val['weight'],
                    'pe_kpi_questions_id'=>$val['question'],
                    'status'=>'Active'
                ]);
            }

            DB::commit();
            $data['code'] = 200;
            $data['msg'] = 'Update Successfully !';
            $data['insert_or_update'] = $request->question_id;

        }catch (\Exception $e) {
            DB::rollback();
            $data['code'] = 500;
            $data['msg'] = 'No Added !';
        }

        return $data;
    }

    public function downloadConfigExcel(Request $request){
        dd('downlaod');
    }
}