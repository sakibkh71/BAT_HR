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


class EvaluateEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function evaluateEmployee(Request $request){

        $result_ary = [];
        $access_designation_ary = [];
        $access_year_ary = [];
        $data['designation_id'] = '';
        $data['user_id'] = '';
        $data['year'] = '2020';
        $logged_in_user_designation = $request->session()->get('DESIGNATION_ID');
//        $logged_in_user_designation = 1555;

        $access_designation = DB::table('pe_configs')->select('year', 'designations')
//                            ->join('designations', 'designations.designations_id', 'pe_configs')
                            ->whereRaw("find_in_set($logged_in_user_designation,evaluate_by)")
                            ->get();



        if(count($access_designation) > 0){
            foreach($access_designation as $info){

                if(!in_array($info->year, $access_year_ary)){
                    $access_year_ary[] = $info->year;
                }
//                $ary = explode(',', $info->designations);
//                foreach($ary as $val){
//                    $access_designation_ary[] = $val;
//                }
            }

            foreach($access_designation as $info){

//                if(!in_array($info->year, $access_year_ary)){
//                    $access_year_ary[] = $info->year;
//                }
                if($info->year == $access_year_ary[0]){
                    $ary = explode(',', $info->designations);
                    foreach($ary as $val){
                        $access_designation_ary[] = $val;
                    }
                }
            }
        }

//        dd($access_designation, $access_year_ary, $access_designation_ary);

        $designation_ary = DB::table('designations')->whereIn('designations_id', $access_designation_ary)->pluck('designations_name', 'designations_id');

        if (!empty($_POST) && $request->user_id > 0){
//            dd($request->all());
            //find designation
            $find_designation = DB::table('sys_users')->select('designations_id')->where('id', $request->user_id)->first();
            $data['user_id'] = $request->user_id;
            $data['designation_id'] = $request->designation_id;

            $config = DB::table('pe_configs')->whereRaw("find_in_set($find_designation->designations_id,designations)")
                ->where('year', $request->search_year)->first();
            $data['year'] = $config->year;
            $data['config_id'] = $config->pe_configs_id;
//            dd($config);

            if(empty($config)){
               dd('This User Have No Configuration');
            }

            $config_details = DB::table('pe_config_details')
                ->join('pe_head_titles','pe_head_titles.pe_head_titles_id','pe_config_details.pe_head_titles_id')
                ->join('pe_kpi_questions', function($join){
                    $join->whereRaw("find_in_set(pe_kpi_questions.pe_kpi_questions_id, pe_config_details.pe_kpi_questions_id)");
                })
                ->leftjoin('pe_auto_apis','pe_auto_apis.pe_auto_apis_id','pe_kpi_questions.pe_auto_apis_id')
                ->where('pe_configs_id', $config->pe_configs_id)->get();

            foreach($config_details as $info){
//                dd($info->pe_kpi_questions_id);
                $result_ary[$info->pe_head_titles_id]['head_name'] = $info->pe_head_titles_name;
                $result_ary[$info->pe_head_titles_id]['weight'] = $info->weight;
                $result_ary[$info->pe_head_titles_id][$info->pe_kpi_questions_id] = $info;
            }
//            dd($result_ary, $find_designation->designations_id, $config->pe_configs_id, $config_details);
        }

        $data['result_ary'] = $result_ary;

        $qry = DB::table('sys_users')->where('status', 'Active')->where('is_employee', 1);
        $session_con = (sessionFilter('url','pe-user-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qry->whereRaw($session_con);
        }
        $data['users'] = $qry->get();
        $data['designation_ary'] = $designation_ary;
        $data['access_year_ary'] = $access_year_ary;

        return view('PerformanceEvaluation.evaluate_employee', $data);
    }

    public function evaluateEmployeeStore(Request $request){

        $weight_ary = [];
        $question_answer = [];
        $request_all = $request->all();
//        dd($request_all);

        foreach($request->all() as $key=>$val){
            $ary = [];

            if($key != '_token'){
                $ary = explode('_', $key);
//                dump($ary);
                if($ary[0] == 'weight'){
                    $r_key = "weight_".$ary[1];
                    $weight_ary[$ary[1]] = $request_all[$r_key];
                }
                elseif($ary[0] == 'question'){
                    $r_key = "question_".$ary[1]."_".$ary[2];

                    $question_answer[$ary[1]][$ary[2]] = $request_all[$r_key];
                }
                elseif ($ary[0] == 'questionAuto'){
                    $r_key = "questionAuto_".$ary[1]."_".$ary[2];
                    $question_answer[$ary[1]][$ary[2]] = $request_all[$r_key];
                }

            }
        }

        $ary_value['bad'] = 33;
        $ary_value['good'] = 66;
        $ary_value['vgood'] = 100;
        $total_achievement = 0;

        foreach($weight_ary as $key=>$val){
            $total_cal = 0;
            $head_final_val = 0;
            $count_total_question = count($question_answer[$key]);
            foreach($question_answer[$key] as $key_q=>$val_q){
//                dump($val_q);
                if(in_array($val_q, ['bad', 'good', 'vgood']))
                {
                    $total_cal += $ary_value[$val_q];
                }
                else{

                    $total_cal += $val_q;
                }

            }

            $total_cal = $total_cal/$count_total_question;
            $head_final_val = ($total_cal*$val)/100;
            $total_achievement += $head_final_val;
//            dd($total_cal, $weight_ary, $head_final_val, $question_answer);
        }

        $logged_in_user = $request->session()->get('USER_ID');

        DB::beginTransaction();

        try{
            DB::table('pe_evaluate_employees')->insert([
                'sys_users_id'=>$request->user_id,
                'evaluate_by'=>$logged_in_user,
                'pe_configs_id'=>$request->config_id,
                'year' => $request->year,
                'achievement'=>sprintf('%0.2f', $total_achievement),
                'created_at'=>date("Y-m-d H:i:s")
            ]);

            $last_id = DB::getPdo()->lastInsertId();

            foreach($question_answer as $key=>$info){
                foreach($info as $question=>$ans){
                    DB::table('pe_evaluate_emp_details')->insert([
                        'pe_evaluate_employees_id'=>$last_id,
                        'pe_head_titles_id'=>$key,
                        'pe_kpi_questions_id'=>$question,
                        'answer' => $ans
                    ]);
                }
            }

            DB::commit();
            $return_data['msg'] = "Data Stored Successfully!<br/>Achievement : <h3>".sprintf('%0.2f', $total_achievement)." %</h3>";
            $return_data['code'] = 200;

//            dd($last_id);

        }catch (\Exception $e) {

            DB::rollback();
            $return_data['msg'] = "Data Not Stored!";
            $return_data['code'] = 500;

        }

//        dd($total_achievement, $weight_ary, $question_answer, count($request->all()), $request->all(), 'exit');
        return $return_data;
    }

    public function evaluationList(Request $request){

        if (!empty($_POST)){
            dd($request->all());
        }

        $year = date('Y');
        $data['list'] = DB::table('pe_evaluate_employees')
                        ->select('pe_evaluate_employees.pe_evaluate_employees_id','pe_evaluate_employees.year', 'pe_evaluate_employees.achievement',
                            'pe_evaluate_employees.created_at',
                            'sys_users.name as user_name', 'sy_user.name as evaluate_by', 'designations.designations_name')
                        ->join('sys_users', 'sys_users.id', 'pe_evaluate_employees.sys_users_id')
                        ->join('designations', 'designations.designations_id', 'sys_users.designations_id')
                        ->join('sys_users as sy_user', 'sy_user.id', 'pe_evaluate_employees.evaluate_by')
//                        ->where('year', $year)
                        ->get();

//        dd($data['list']);
        //pe_evaluation_list
        return view('PerformanceEvaluation.pe_evaluation_list', $data);
    }

    public function evaluationListDetails($id){

        $result_ary = [];

        $qry = DB::table('pe_evaluate_employees')
            ->select('pe_evaluate_employees.pe_evaluate_employees_id','pe_evaluate_employees.year', 'pe_evaluate_employees.achievement','pe_evaluate_employees.created_at',
                'sys_users.name as user_name', 'designations.designations_name',
                'pe_evaluate_emp_details.pe_head_titles_id','pe_evaluate_emp_details.pe_kpi_questions_id', 'pe_evaluate_emp_details.answer',
                'pe_head_titles.pe_head_titles_name', 'pe_kpi_questions.question')
            ->join('pe_evaluate_emp_details', 'pe_evaluate_emp_details.pe_evaluate_employees_id', 'pe_evaluate_employees.pe_evaluate_employees_id')
            ->join('sys_users', 'sys_users.id', 'pe_evaluate_employees.sys_users_id')
            ->join('designations', 'designations.designations_id', 'sys_users.designations_id')
            ->join('pe_head_titles', 'pe_head_titles.pe_head_titles_id', 'pe_evaluate_emp_details.pe_head_titles_id')
            ->join('pe_kpi_questions', 'pe_kpi_questions.pe_kpi_questions_id', 'pe_evaluate_emp_details.pe_kpi_questions_id')
            ->where('pe_evaluate_employees.pe_evaluate_employees_id', $id)->get();

        foreach($qry as $key=>$val){
            $result_ary['emp_name'] = $val->user_name;
            $result_ary['year'] = $val->year;
            $result_ary['designation'] = $val->designations_name;
            $result_ary['achievement'] = $val->achievement;
            $result_ary[$val->pe_head_titles_name][] = $val;
        }

//        dd($result_ary);

        $final_string = "";
        $final_string .= "<b>Employee Name:</b>".$result_ary['emp_name']."<br/>";
        $final_string .= "<b>Designation:</b>".$result_ary['designation']."<br/>";
        $final_string .= "<b>Achievement:</b>".$result_ary['achievement']."<br/>";
        $final_string .= "<b>Year:</b>".$result_ary['year']."<br/><br/>";

        foreach($result_ary as $key=>$val){
            if(!in_array($key, ['emp_name', 'year', 'designation', 'achievement'])){
                $final_string .= "<strong>*<u>".$key."</u></strong><br/>";

                $sl = 1;
                foreach($val as $q_key=>$q_val){
//                    if($q_key != 'head_name'){
                        $final_string .= $sl++.". ".$q_val->question." : <b>".$q_val->answer."</b><br/>";
//                    }
                }
            }
        }

        return $final_string;
    }

    public function getUserByDesignation($desig_id, $user_id){

        $qry = DB::table('sys_users')->where('designations_id', $desig_id)->where('status', 'Active')->where('is_employee', 1);
        $session_con = (sessionFilter('url','pe-user-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qry->whereRaw($session_con);
        }
        $users = $qry->pluck('name', 'id');

        $string_val = "";

        if(count($users) > 0){
            foreach($users as $key=>$val){
                if($user_id == $key){
                    $selected = "selected";
                }
                else{
                    $selected = "";
                }
                $string_val .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
            }
        }
        else{
            $string_val .= '<option value="0">No value</option>';
        }

        return $string_val;
    }

    public function getDesignationByYear(Request $request,$year,$user_id = 0,$designation_id = 0){

        $logged_in_user_designation = $request->session()->get('DESIGNATION_ID');


        $access_designation = DB::table('pe_configs')->select('pe_configs.year', 'pe_configs.designations')
            ->whereRaw("find_in_set($logged_in_user_designation,evaluate_by)")
            ->where("year", $year)
            ->get();


        if(count($access_designation) > 0){
            foreach($access_designation as $info){

                $ary = explode(',', $info->designations);
                foreach($ary as $val){
                    $access_designation_ary[] = $val;
                }
            }
        }

        $designation_ary = DB::table('designations')->whereIn('designations_id', $access_designation_ary)->pluck('designations_name', 'designations_id');

        $string_val = "";
        $first_designation = 0;
        if(count($designation_ary) > 0){
            foreach($designation_ary as $key=>$val){

                if($first_designation <= 0){
                    $first_designation = $key;
                }

                if($designation_id == $key){
                    $selected = "selected";
                    $first_designation = $key;
                }
                else{
                    $selected = "";
                }
                $string_val .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
            }
        }
        else{
            $string_val .= '<option value="0">No value</option>';
        }

        $data['string_designation'] = $string_val;

        //get designaton string

        $qry = DB::table('sys_users')->where('designations_id', $first_designation)->where('status', 'Active')->where('is_employee', 1);
        $session_con = (sessionFilter('url','pe-user-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qry->whereRaw($session_con);
        }
        $users = $qry->pluck('name', 'id');

        $string_emp = "";

        if(count($users) > 0){
            foreach($users as $key=>$val){
                if($user_id == $key){
                    $selected = "selected";
                }
                else{
                    $selected = "";
                }
                $string_emp .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
            }
        }
        else{
            $string_emp .= '<option value="0">No value</option>';
        }

        $data['string_emp'] = $string_emp;
        return $data;
    }

    public function EmpAchievementDatewise(Request $request){
        $data['user_id'] = '';
        $data['list'] = '';
        $data['date_from'] = '';
        $data['date_to'] = '';

        if (!empty($_POST)){
            //pe_evaluate_employees
            $dateStart = $request->date_from;
            $dateEnd = $request->date_to;
            $yearStart = date("Y", strtotime($dateStart));
            $yearEnd = date("Y", strtotime($dateEnd));

            $val = DB::table('pe_evaluate_employees')
                    ->select('pe_evaluate_employees.pe_evaluate_employees_id','pe_evaluate_employees.year', 'pe_evaluate_employees.achievement','pe_evaluate_employees.created_at',
                        'sys_users.name as user_name', 'sy_user.name as evaluate_by', 'designations.designations_name')
                    ->join('sys_users', 'sys_users.id', 'pe_evaluate_employees.sys_users_id')
                    ->join('designations', 'designations.designations_id', 'sys_users.designations_id')
                    ->join('sys_users as sy_user', 'sy_user.id', 'pe_evaluate_employees.evaluate_by')
                    ->where('pe_evaluate_employees.sys_users_id', $request->user_id)
                    ->whereBetween('pe_evaluate_employees.created_at', [$dateStart." 00:00:00",$dateEnd." 23:59:59"])
                    ->whereBetween('pe_evaluate_employees.year', [$yearStart,$yearEnd])
                    ->get();
            $data['list'] = $val;
            $data['date_from'] = $dateStart;
            $data['date_to'] = $dateEnd;
            $data['user_id'] = $request->user_id;

//            dd($request->all(), $val, $yearStart, $yearEnd);
        }

        $qry = DB::table('sys_users')->where('status', 'Active')->where('is_employee', 1);
        $session_con = (sessionFilter('url','pe-user-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $qry->whereRaw($session_con);
        }

        $data['users'] = $qry->pluck('name', 'id');
//        dd($data['users']);

        return view('PerformanceEvaluation.pe_emp_achievement_datewise', $data);
    }
}