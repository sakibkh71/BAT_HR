<?php

namespace App\Http\Controllers\Delegation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Events\NotifyEvent;
use DB;
use Session;
class DelegationProcess extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function delegationLinkViewTest($code=null){
        //test view
        $query = DB::table('purchase_requisitions');
        $query->select('purchase_requisitions.*','sys_status_flows.status_flows_name');
        $query->leftJoin('sys_status_flows','sys_status_flows.status_flows_id','=','purchase_requisitions.requisition_status');

        //for requisition list
        if($code){
            $query->where('purchase_requisitions.purchase_requisitions_code',$code);
            $data['grid_data'] = $query->first();
        }else{
            $data['grid_data'] = $query->get()->toArray();
        }
        $data['code'] = $code;
        return view('test',$data);
    }


    public function waitingForApproval($slug, $columns = [], $join_tables = []){
        $id_logic = self::idManagerInfo($slug);
        $without_same_sort = DB::table($id_logic->ref_db_table_name);
        if($join_tables){
            foreach($join_tables as $jtname){
                $table = $jtname;
                $table_id = $jtname.'_id';
                $ref_table_id = $table_id;
                if($jtname == 'sys_users'){
                    $table = 'sys_users';
                    $table_id = 'id';
                    $ref_table_id = 'sys_users_id';
                }
                $without_same_sort->leftJoin($table,$table.'.'.$table_id,'=',$id_logic->ref_db_table_name.'.'.$ref_table_id);
            }
        }
        if($columns){
            $without_same_sort->select($columns);
        }else{
            $without_same_sort->select($id_logic->ref_db_table_name.'.'.$id_logic->ref_id_field,$id_logic->ref_db_table_name.'.'.$id_logic->ref_status_field);
        }

        $without_same_sort->where($id_logic->ref_db_table_name.'.'.$id_logic->ref_status_field,$id_logic->initiate_approve_status);
        $without_same_sort->where($id_logic->ref_db_table_name.'.delegation_reliever_id',Auth::user()->id);
//        $without_same_sort->orderBy($id_logic->ref_db_table_name.'.'.$id_logic->ref_id_field,'desc');
        $without_same_sort->groupBy($id_logic->ref_id_field);

        $same_sort = DB::table($id_logic->ref_db_table_name);
        if($columns){
            $same_sort->select($columns);
        }else{
            $same_sort->select($id_logic->ref_db_table_name.'.'.$id_logic->ref_id_field,$id_logic->ref_db_table_name.'.'.$id_logic->ref_status_field);
        }
        if($join_tables){
            foreach($join_tables as $jtname){
                $table = $jtname;
                $table_id = $jtname.'_id';
                $ref_table_id = $table_id;

                if($jtname == 'sys_users'){
                    $table = 'sys_users';
                    $table_id = 'id';
                    $ref_table_id = 'sys_users_id';
                }
                $same_sort->leftJoin($table,$table.'.'.$table_id,'=',$id_logic->ref_db_table_name.'.'.$ref_table_id);
//                $same_sort->leftJoin($jtname,$jtname.'.'.$jtname.'_id','=',$id_logic->ref_db_table_name.'.'.$jtname.'_id');
            }
        }
        $same_sort->leftJoin('sys_delegation_conf', function ($join) use($id_logic) {
            $join->on($id_logic->ref_db_table_name.'.delegation_for', '=', 'sys_delegation_conf.delegation_for')
                ->where($id_logic->ref_db_table_name.'.delegation_ref_event_id', 'sys_delegation_conf.ref_event_id')
                ->where($id_logic->ref_db_table_name.'.delegation_version', 'sys_delegation_conf.delegation_version')
                ->where($id_logic->ref_db_table_name.'.delegation_step', 'sys_delegation_conf.step_number')
                ->where('sys_delegation_conf.same_sort',1)
                ->where('sys_delegation_conf.user_id',Auth::user()->id);
        });


        $same_sort->whereNotIn('sys_delegation_conf.user_id',function($squery)use($id_logic){
            $squery->select('delegation_reliever_id');
            $squery->from('sys_delegation_historys');
            $squery->where('ref_event',$id_logic->slug);
            $squery->where('ref_id',$id_logic->ref_db_table_name.'.'.$id_logic->ref_id_field);
            $squery->where('step_no',$id_logic->ref_db_table_name.'.delegation_step');
            $squery->where('delegation_reliever_id',Auth::user()->id);
            $squery->get();
        });


        $same_sort->where($id_logic->ref_db_table_name.'.'.$id_logic->ref_status_field,$id_logic->initiate_approve_status);
        $same_sort->whereNotNull($id_logic->ref_db_table_name.'.delegation_step');
        $same_sort->whereNull($id_logic->ref_db_table_name.'.delegation_reliever_id');
//        $same_sort->orderBy($id_logic->ref_db_table_name.'.'.$id_logic->ref_id_field,'desc');
        $data['results'] = $same_sort->unionAll($without_same_sort)->get()->toArray();

        //return $data['results'];
        $data['id_logic'] = $id_logic;
        return $data;
    }



    public function delegationDeclineProcess($post){
        $code = $post['code'][0];
        unset($post['code']);
        $post['code'] = $code;

        $id_logic = self::idManagerInfo($post['slug']);
        $job_info = self::actionTableInfo($id_logic,$post);

        $result['mode'] = 'Success';
        self::insertDelegationHistory($job_info,'Declined',$post);

        if($job_info->is_manual){
            DB::table($id_logic->ref_db_table_name)
                ->where($id_logic->ref_id_field,$post['code'])
                ->update(array(
                    'delegation_for'=>null,
                    'delegation_ref_event_id'=>null,
                    'delegation_version'=>null,
                    'delegation_step'=>null,
                    'delegation_person'=>null,
                    'delegation_reliever_id'=>null,
                    $id_logic->ref_status_field=>$id_logic->after_decline_status,
                    'delegation_decline_count'=>DB::raw('delegation_decline_count+1'),
                    'delegation_manual_user'=>null,
                    'is_manual'=>0
                ));
            $result['data'][$post['code']]['mode']='Success';
            $result['data'][$post['code']]['msg']='Successfully Declined.';
            $result['status_id']=$id_logic->after_decline_status;
        }else{
            $conf = self::configurationInfoSingleRow($job_info);
            if($conf->same_sort){
                if($conf->approve_priority == 'All'){
                    $result = self::prevStepInitiateAfterDecline($job_info,$id_logic,$post);
                }else if($conf->approve_priority == 'Majority'){
                    $total_decline_person = self::totalDeclinePerson($job_info,$post);
                    $total_step_person = self::totalPersonOfSameSort($job_info);
                    if($total_decline_person >= ($total_step_person/2)){
                        $result = self::prevStepInitiateAfterDecline($job_info,$id_logic,$post);
                    }
                }else if($conf->approve_priority == 'Minority'){
                    $total_decline_person = self::totalDeclinePerson($job_info,$post);
                    $total_step_person = self::totalPersonOfSameSort($job_info);
                    if($total_decline_person == $total_step_person){
                        $result = self::prevStepInitiateAfterDecline($job_info,$id_logic,$post);
                    }
                }
            }else{
                $user_config = self::configurationInfoSingleRow($job_info,'for_user_info');
                //debug($user_config,1);
                if($user_config->decline_logic == 'Initiator'){

                    DB::table($id_logic->ref_db_table_name)
                        ->where($id_logic->ref_id_field,$post['code'])
                        ->update(array(
                            'delegation_for'=>null,
                            'delegation_ref_event_id'=>null,
                            'delegation_version'=>null,
                            'delegation_step'=>null,
                            'delegation_person'=>null,
                            'delegation_reliever_id'=>null,
                            $id_logic->ref_status_field=>$id_logic->after_decline_status,
                            'delegation_decline_count'=>DB::raw('delegation_decline_count+1')
                        ));
                    $result['data'][$post['code']]['mode']='Success';
                    $result['data'][$post['code']]['msg']='Successfully Declined.';
                    $result['status_id']=$id_logic->after_decline_status;

                }else{
                    $last_approval_person = self::lastApprovalPersonInfo($job_info,$post);
                    if($last_approval_person){
                        //self::insertDelegationHistory($job_info,'Declined',$post);
                        $releiver_of_id = self::getReleiverOfInfo($last_approval_person->delegation_person);
                        DB::table($id_logic->ref_db_table_name)
                            ->where($id_logic->ref_id_field,$post['code'])
                            ->update(array(
                                'delegation_person'=>$last_approval_person->delegation_person,
                                'delegation_reliever_id'=>($releiver_of_id)?$releiver_of_id:$last_approval_person->delegation_person,
                                'delegation_decline_count'=>DB::raw('delegation_decline_count+1')
                            ));
                        $result['data'][$post['code']]['mode']='Success';
                        $result['data'][$post['code']]['msg']='Successfully Declined.';
                        $result['status_id']=$id_logic->initiate_approve_status;

                    }else{
                        $result = self::prevStepInitiateAfterDecline($job_info,$id_logic,$post);
                    }
                }
            }
        }

        $return_result[] = $result;
        return json_encode($return_result);
    }


    public function prevStepInitiateAfterDecline($job_info,$id_logic,$post){
        $prev_conf_query = DB::table('sys_delegation_conf');
        $prev_conf_query->where('delegation_for',$job_info->delegation_for);
        $prev_conf_query->where('ref_event_id',$job_info->delegation_ref_event_id);
        $prev_conf_query->where('delegation_version',$job_info->delegation_version);
        $prev_conf_query->where('step_number','<',$job_info->delegation_step);
        $prev_conf_query->orderBy('step_number','DESC');
        $prev_conf = $prev_conf_query->first();
        if($prev_conf){
            if($prev_conf->same_sort){
                //self::insertDelegationHistory($job_info,'Declined',$post);
                DB::table($id_logic->ref_db_table_name)
                    ->where($id_logic->ref_id_field,$post['code'])
                    ->update(array(
                        'delegation_for'=>null,
                        'delegation_ref_event_id'=>null,
                        'delegation_version'=>null,
                        'delegation_step'=>null,
                        'delegation_person'=>null,
                        'delegation_reliever_id'=>null,
                        $id_logic->ref_status_field=>$id_logic->after_decline_status,
                        'delegation_decline_count'=>DB::raw('delegation_decline_count+1')
                    ));
                $result['data'][$post['code']]['mode']='Success';
                $result['data'][$post['code']]['msg']='Successfully Declined.';
                $result['status_id']=$id_logic->initiate_approve_status;

            }else{
                $query = DB::table('sys_delegation_historys');
                $query->select('delegation_person');
                $query->where('ref_event',$job_info->delegation_for);
                $query->where('ref_id',$post['code']);
                $query->where('step_no',$prev_conf->step_number);
                $query->where('act_status','Approved');
                $query->orderBy('created_at','DESC');
                $result_array = $query->first();
                $releiver_of_id = self::getReleiverOfInfo($result_array->delegation_person);
                DB::table($id_logic->ref_db_table_name)
                    ->where($id_logic->ref_id_field,$post['code'])
                    ->update(array(
                        'delegation_step'=>$prev_conf->step_number,
                        'delegation_person'=>$result_array->delegation_person,
                        'delegation_reliever_id'=>($releiver_of_id)?$releiver_of_id:$result_array->delegation_person,
                        'delegation_decline_count'=>DB::raw('delegation_decline_count+1')
                    ));
                $result['data'][$post['code']]['mode']='Success';
                $result['data'][$post['code']]['msg']='Successfully Declined.';
                $result['status_id']=$id_logic->initiate_approve_status;

            }
        }else{
            //self::insertDelegationHistory($job_info,'Declined',$post);
            DB::table($id_logic->ref_db_table_name)
                ->where($id_logic->ref_id_field,$post['code'])
                ->update(array(
                    'delegation_for'=>null,
                    'delegation_ref_event_id'=>null,
                    'delegation_version'=>null,
                    'delegation_step'=>null,
                    'delegation_person'=>null,
                    'delegation_reliever_id'=>null,
                    $id_logic->ref_status_field=>$id_logic->after_decline_status,
                    'delegation_decline_count'=>DB::raw('delegation_decline_count+1')
                ));
            $result['data'][$post['code']]['mode']='Success';
            $result['data'][$post['code']]['msg']='Successfully Declined.';
            $result['status_id']=$id_logic->after_decline_status;

        }
        return $result;
    }

    public function lastApprovalPersonInfo($job_info,$post){
        $query = DB::table('sys_delegation_historys');
        $query->select('delegation_person');
        $query->where('ref_event',$job_info->delegation_for);
        $query->where('ref_id',$post['code']);
        $query->where('step_no',$job_info->delegation_step);
        $query->where('act_status','Approved');
        $query->where('delegation_decline_count',$job_info->delegation_decline_count);
        $query->orderBy('created_at','DESC');
        $result = $query->first();
        return $result;
    }

    public function totalDeclinePerson($job_info,$post){
        $query = DB::table('sys_delegation_historys');
        $query->select(DB::raw('count(*) as total'));
        $query->where('ref_event',$job_info->delegation_for);
        $query->where('ref_id',$post['code']);
        $query->where('step_no',$job_info->delegation_step);
        $query->where('act_status','Declined');
        $query->where('delegation_decline_count',$job_info->delegation_decline_count);
        $query->groupBy('ref_event');
        $result = $query->first();
        return ($result)?$result->total:0;
    }



    public function sendForApproval($post){
        foreach ($post['code'] as $val){
            $request['slug'] = $post['slug'];
            $request['code']=array($val);
            $result[] = self::delegationInitialize($request);
        }
        return $result;
    }


    public function delegationApprove($post){
        $result = array();
        $request = array();
        foreach ($post['code'] as $val){
            $request['slug'] = $post['slug'];
            $request['code'] = $val;
            $request['comments'] = $post['comments'];
            $request['additional_data'] = $post['additional_data'];
            $result[] = self::delegationApprovalProcess($request);
        }
        return json_encode($result);
    }


    public function delegationApprovalProcess($post){
        //$post = $request->all();
        //debug($request->all(),1);
        $id_logic = self::idManagerInfo($post['slug']);
        $job_info = self::actionTableInfo($id_logic,$post);


        if($job_info->is_manual){
            $current_step = $job_info->delegation_step;
            $next_step = $current_step+1;
            $step_user_info = json_decode($job_info->delegation_manual_user,true);
            //debug($step_user_info,1);
//            $next_conf = new stdClass;
//            $next_conf->step_number = isset($step_user_info[$next_step])?$step_user_info[$next_step]:'';
            $next_conf_array = array(
                //'step_number'=>isset($step_user_info[$next_step])?$step_user_info[$next_step]:''
                'step_number'=>array_key_exists($next_step,$step_user_info)?$next_step:''
            );
            $next_conf = json_decode(json_encode($next_conf_array));
            //debug($next_conf,1);
            $result = self::nextStepOrFinalApproveAction($id_logic,$next_conf,$post,$job_info);
            //debug($result,1);
            //insert delegation history
            self::insertDelegationHistory($job_info,'Approved',$post);
        }else{
            $conf = self::configurationInfoSingleRow($job_info);
            $result['mode'] = 'Success';
            switch ($conf->manage_by){
                case 'Hierarchy':
                    $session_variable = str_replace('@delegation_person_id',$job_info->delegation_person,$conf->session_variable);
                    $termination_value = DB::select($session_variable);
                    $termination_value_array = array();
                    if($termination_value){
                        $termination_value_json = json_decode(json_encode($termination_value), true);
                        $termination_value_array = array_column($termination_value_json,'termination_values');
                    }
                    if(in_array($conf->termination_trigger,$termination_value_array)){
                        $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                        //insert delegation history
                        self::insertDelegationHistory($job_info,'Approved',$post);

                    }else{
                        $orginal_user_query = DB::table('sys_users');
                        $orginal_user_query->where('id',$job_info->delegation_person);
                        $orginal_user_info = $orginal_user_query->first();

                        if($orginal_user_info->line_manager_id){
                            $releiver_of_id = self::getReleiverOfInfo($orginal_user_info->line_manager_id);
                            $update_job_table = array(
                                'delegation_person'=>$orginal_user_info->line_manager_id,
                                'delegation_reliever_id'=>($releiver_of_id)?$releiver_of_id:$orginal_user_info->line_manager_id
                            );
                            //update job table
                            DB::table($id_logic->ref_db_table_name)
                                ->where($id_logic->ref_id_field,$post['code'])
                                ->update($update_job_table);

                            //insert delegation history
                            self::insertDelegationHistory($job_info,'Approved',$post);
                            $result['data'][$post['code']]['mode']='Success';
                            $result['data'][$post['code']]['msg']='Successfully Updated.';
                            $result['status_id']=$id_logic->initiate_approve_status;

                        }else{
                            $result['data'][$post['code']]['mode']='Failed';
                            $result['data'][$post['code']]['msg']='Line Manager Not Found.';
                            $result['status_id']=$id_logic->initiate_approve_status;
                        }
                    }

                    break;

                case 'Designation':
                    $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                    //insert delegation history
                    self::insertDelegationHistory($job_info,'Approved',$post);

                    break;

                case 'Sorting':
                    if($conf->same_sort){
//                    $must_approve_query = DB::table('sys_delegation_conf');
//                    $must_approve_query->select('user_id');
//                    $must_approve_query->where('delegation_for',$job_info->delegation_for);
//                    $must_approve_query->where('ref_event_id',$job_info->delegation_ref_event_id);
//                    $must_approve_query->where('delegation_version',$job_info->delegation_version);
//                    $must_approve_query->where('step_number',$job_info->delegation_step);
//                    $must_approve_query->where('same_sort',1);
//                    $must_approve_query->where('must_approve',1);
//                    $must_approve_object = $must_approve_query->get()->toArray();
                        $must_approve_object = self::configurationInfoMultiRow($job_info);
                        $must_approve_array = array_column($must_approve_object,'user_id');

                        //insert delegation history
                        self::insertDelegationHistory($job_info,'Approved',$post);

                        //find next step or final approve
                        $total_approve = self::totalDelegationHistory($job_info,$post,'Approved');
                        $must_approve_person = self::totalDelegationHistory($job_info,$post,'Approved',$must_approve_array);
                        $total_person_of_same_sort = self::totalPersonOfSameSort($job_info);

                        if($conf->approve_priority == 'All'){
                            if((count($must_approve_array) == $must_approve_person) && ($total_person_of_same_sort == $total_approve)){
                                //$result = self::nextStepOrFinalApprove($request,$post,$id_logic,$job_info);
                                $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                            }
                        }else if($conf->approve_priority == 'Majority'){
                            if((count($must_approve_array) == $must_approve_person) && ($total_approve >= ($total_person_of_same_sort/2))){
                                //$result = self::nextStepOrFinalApprove($request,$post,$id_logic,$job_info);
                                $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                            }
                        }else if($conf->approve_priority == 'Minority'){
                            if(count($must_approve_array) == $must_approve_person){
                                //$result = self::nextStepOrFinalApprove($request,$post,$id_logic,$job_info);
                                $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                            }
                        }

                    }else{
//                    $step_sort_query = DB::table('sys_delegation_conf');
//                    $step_sort_query->where('delegation_for',$job_info->delegation_for);
//                    $step_sort_query->where('ref_event_id',$job_info->delegation_ref_event_id);
//                    $step_sort_query->where('delegation_version',$job_info->delegation_version);
//                    $step_sort_query->where('step_number',$job_info->delegation_step);
//                    $step_sort_query->where('user_id',$job_info->delegation_person);
//                    $step_sort = $step_sort_query->first();
                        $step_sort = self::configurationInfoSingleRow($job_info,'for_user_info');

                        $next_person_query = DB::table('sys_delegation_conf');
                        $next_person_query->where('delegation_for',$job_info->delegation_for);
                        $next_person_query->where('ref_event_id',$job_info->delegation_ref_event_id);
                        $next_person_query->where('delegation_version',$job_info->delegation_version);
                        $next_person_query->where('step_number',$job_info->delegation_step);
                        $next_person_query->where('sort_number','>',$step_sort->sort_number);
                        $next_person_query->orderBy('sort_number','ASC');
                        $next_person = $next_person_query->first();


                        if($next_person){
                            $releiver_of_id = self::getReleiverOfInfo($next_person->user_id);
                            $update_job_table = array(
                                'delegation_person'=>$next_person->user_id,
                                'delegation_reliever_id'=>($releiver_of_id)?$releiver_of_id:$next_person->user_id
                            );
                            //update job table
                            DB::table($id_logic->ref_db_table_name)
                                ->where($id_logic->ref_id_field,$post['code'])
                                ->update($update_job_table);
                            $result['data'][$post['code']]['mode']='Success';
                            $result['data'][$post['code']]['msg']='Successfully Updated.';
                            $result['status_id']=$id_logic->initiate_approve_status;
                        }else{
                            //$result = self::nextStepOrFinalApprove($request,$post,$id_logic,$job_info);
                            $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                        }
                        //insert delegation history
                        self::insertDelegationHistory($job_info,'Approved',$post);
                    }
                    break;

                case 'Limit':
                    $job_amount_query_string = str_replace('@generated_id','"'.$post['code'].'"',$id_logic->sql_calc_amount);
                    $job_amount_query = DB::select($job_amount_query_string);

//                $delegation_conf_user_info_query = DB::table('sys_delegation_conf');
//                $delegation_conf_user_info_query->where('delegation_for',$job_info->delegation_for);
//                $delegation_conf_user_info_query->where('ref_event_id',$job_info->delegation_ref_event_id);
//                $delegation_conf_user_info_query->where('delegation_version',$job_info->delegation_version);
//                $delegation_conf_user_info_query->where('step_number',$job_info->delegation_step);
//                $delegation_conf_user_info_query->where('user_id',$job_info->delegation_person);
//                $delegation_conf_user_info = $delegation_conf_user_info_query->first();
                    $delegation_conf_user_info = self::configurationInfoSingleRow($job_info,'for_user_info');
                    //debug($id_logic->after_approve_status,1);
                    if($delegation_conf_user_info->max_limit >= $job_amount_query[0]->amount){
                        //$result = self::nextStepOrFinalApprove($request,$post,$id_logic,$job_info);
                        $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                    }else{
                        $next_limit_person_query = DB::table('sys_delegation_conf');
                        $next_limit_person_query->where('delegation_for',$job_info->delegation_for);
                        $next_limit_person_query->where('ref_event_id',$job_info->delegation_ref_event_id);
                        $next_limit_person_query->where('delegation_version',$job_info->delegation_version);
                        $next_limit_person_query->where('step_number',$job_info->delegation_step);
                        $next_limit_person_query->where('max_limit','>',$delegation_conf_user_info->max_limit);
                        $next_limit_person = $next_limit_person_query->first();

                        if($next_limit_person){
                            $releiver_of_id = self::getReleiverOfInfo($next_limit_person->user_id);
                            $update_job_table = array(
                                'delegation_person'=>$next_limit_person->user_id,
                                'delegation_reliever_id'=>($releiver_of_id)?$releiver_of_id:$next_limit_person->user_id
                            );
                            //update job table
                            DB::table($id_logic->ref_db_table_name)
                                ->where($id_logic->ref_id_field,$post['code'])
                                ->update($update_job_table);
                            $result['data'][$post['code']]['mode']='Success';
                            $result['data'][$post['code']]['msg']='Successfully Updated.';
                            $result['status_id']=$id_logic->initiate_approve_status;

                        }else{
                            //$result = self::nextStepOrFinalApprove($request,$post,$id_logic,$job_info);
                            $result = self::nextStepOrFinalApprove($post,$id_logic,$job_info);
                        }
                    }
                    self::insertDelegationHistory($job_info,'Approved',$post);
                    break;

                default:
                    $result['data'][$post['code']]['mode']='Failed';
                    $result['data'][$post['code']]['msg']='Something wrong.';
                    $result['status_id']=$id_logic->initiate_approve_status;

                    break;
            }
        }
        return $result;
    }

    public function nextStepOrFinalApprove($post, $id_logic, $job_info){
        $next_conf_query = DB::table('sys_delegation_conf');
        $next_conf_query->where('delegation_for',$job_info->delegation_for);
        $next_conf_query->where('ref_event_id',$job_info->delegation_ref_event_id);
        $next_conf_query->where('delegation_version',$job_info->delegation_version);
        $next_conf_query->where('step_number','>',$job_info->delegation_step);
        $next_conf_query->orderBy('step_number','ASC');
        $next_conf = $next_conf_query->first();
        $result = self::nextStepOrFinalApproveAction($id_logic,$next_conf,$post,$job_info);
        return $result;
    }

    public function nextStepOrFinalApproveAction($id_logic,$next_conf,$post,$job_info){
        $request = array();
        //debug($next_conf,1);
        if(isset($next_conf->step_number) && $next_conf->step_number){ // changed by Jakir vai
            //initiate next step
            $request['slug'] = $post['slug'];
            $request['step_no']=$next_conf->step_number;
            $request['code']=array($post['code']);

            $result = self::delegationInitialize($request);
            // debug($result,1);

        }else{
            //final approve
            DB::table($id_logic->ref_db_table_name)
                ->where($id_logic->ref_id_field,$post['code'])
                ->update(array(
                    'delegation_for'=>null,
                    'delegation_ref_event_id'=>null,
                    'delegation_version'=>null,
                    'delegation_step'=>null,
                    'delegation_person'=>null,
                    'delegation_reliever_id'=>null,
                    $id_logic->ref_status_field=>$id_logic->after_approve_status,
                    'delegation_final_approved'=>currentDateTime()
                ));
            $result['data'][$post['code']]['mode']='Success';
            $result['data'][$post['code']]['msg']='Successfully Approved.';
            $result['status_id']=$id_logic->after_approve_status;

        }


        // notification
        $approval_url = DB::table('sys_approval_modules')->select('approval_url')->where('unique_id_logic_slug',$post['slug'])->first()->approval_url;
        $noti_arr = [
            'generated_from'=> 'Person',
            'generated_source'=> session()->get('USER_ID'),
            'notify_to'=> $job_info->created_by, // jar kache notification jabe
            'event_for'=> $post['slug'], // event slug / id_logic_slug / approval event slug
            'event_id'=> json_encode($post['code']),
            'content'=> 'On of your job ('.$post['code'].') has been approved.',
            'url_ref'=> $approval_url, // Approval module redirect url
            'created_at'=> currentDate(),
            'priority'=> 3
        ];
        $id = DB::table('sys_notifys')->insertGetId($noti_arr);
        event(new NotifyEvent($id));
        return $result;
    }

    public function insertDelegationHistory($job_info,$action_type,$post){
        $delegation_history = array(
            'ref_event'=>$post['slug'],
            'ref_id'=>$post['code'],
            'step_no'=>$job_info->delegation_step,
            'act_status'=>$action_type,
            'delegation_person'=>$job_info->delegation_person,
            'delegation_reliever_id'=>$job_info->delegation_reliever_id,
            'act_comments'=>$post['comments'],
            'additional_data'=>$post['additional_data'],   // additional_data
            'delegation_decline_count'=>$job_info->delegation_decline_count
        );
        DB::table('sys_delegation_historys')->insert($delegation_history);
    }


    public function totalDelegationHistory($job_info,$post,$action_type,$must_approve = array()){
        $total_approve_query = DB::table('sys_delegation_historys');
        $total_approve_query->select(DB::raw('count(*) as total'));
        $total_approve_query->where('ref_event',$job_info->delegation_for);
        $total_approve_query->where('ref_id',$post['code']);
        $total_approve_query->where('step_no',$job_info->delegation_step);
        $total_approve_query->where('act_status',$action_type);
        $total_approve_query->where('delegation_decline_count',$job_info->delegation_decline_count);
        if(!empty($must_approve)){
            $total_approve_query->whereIn('delegation_person',$must_approve);
        }
        $total_approve_query->groupBy('ref_event');
        $total_approve = $total_approve_query->first();
        return ($total_approve)?$total_approve->total:0;
    }

    public function totalPersonOfSameSort($job_info){
        $query = DB::table('sys_delegation_conf');
        $query->select(DB::raw('count(*) as total'));
        $query->where('delegation_for',$job_info->delegation_for);
        $query->where('ref_event_id',$job_info->delegation_ref_event_id);
        $query->where('delegation_version',$job_info->delegation_version);
        $query->where('step_number',$job_info->delegation_step);
        $query->where('same_sort',1);
        $result = $query->first();
        return $result->total;
    }

    /*
     *check for approve or dacline
     */
    public static function checkDeligationAccessibility($grid_data){
        if($grid_data->delegation_reliever_id == Auth::user()->id){
            return true;
        }else{
            $query = DB::table('sys_delegation_conf');
            $query->select('user_id');
            $query->where('delegation_for',$grid_data->delegation_for);
            $query->where('ref_event_id',$grid_data->delegation_ref_event_id);
            $query->where('delegation_version',$grid_data->delegation_version);
            $query->where('step_number',$grid_data->delegation_step);
            $query->where('same_sort',1);

            $deligated_user_result = $query->get()->toArray();
            $deligated_user = array_column($deligated_user_result,'user_id');

            $now = date('Y-m-d H:i:s');
            $rquery = DB::table('sys_users');
            $rquery->select('id','reliever_to');
            $rquery->whereIn('id',$deligated_user);
            $rquery->where('is_reliever',1);
            $rquery->where('reliever_start_datetime','<=',$now);
            $rquery->where('reliever_end_datetime','>=',$now);
            $reliever_result = $rquery->get()->toArray();


            $user = array_column($reliever_result,'id');
            $reliever_user = array_column($reliever_result,'reliever_to');


            $query = DB::table('sys_delegation_historys');
            $query->select('delegation_reliever_id');
            $query->where('ref_event',$grid_data->delegation_for);
            $query->where('step_no',$grid_data->delegation_step);
            $query->where('delegation_reliever_id',Auth::user()->id);
            $query->where('delegation_decline_count',$grid_data->delegation_decline_count);
            $log_result = $query->get()->toArray();
            $log_user = array_column($log_result,'delegation_reliever_id');

            $different_user = array_diff($deligated_user,$user);
            $total_user_list = array_merge($different_user,$reliever_user);

            $total_user_list_after_history = array_diff($total_user_list,$log_user);

            if(in_array(Auth::user()->id,$total_user_list_after_history)){
                return true;
            }else{
                return false;
            }

        }

    }


    /*
     *For the first time an Job will drop in the Deligation process.
     */
    public function delegationInitialize($post){

        $step_no = null;
        if(isset($post['step_no'])){
            $step_no = $post['step_no'];
        }

        $result = array('mode'=>'Success');
        $id_manager_info = self::idManagerInfo($post['slug']);
        $job_info = self::actionTableInfo($id_manager_info,$post);
        if($job_info->is_manual){
            $step_user_info = json_decode($job_info->delegation_manual_user,true);
            //debug($step_no,1);
            $step_no = ($step_no)?$step_no:1;
            //debug($step_user_info[$step_no],1);
            if(isset($step_user_info[$step_no])){
                $releiver_of_id = self::getReleiverOfInfo($step_user_info[$step_no]);
                $json = array(
                    "delegation_for"=>$id_manager_info->slug,
                    "ref_event_id"=> "",
                    "delegation_version"=>"",
                    "step_number"=>$step_no,
                    "manage_by"=>"",
                    "same_sort"=>"");
                $delegation_conf = json_decode(json_encode($json));
//                debug($delegation_conf,1);
//                $delegation_conf = new stdClass();
//                $delegation_conf->delegation_for = $id_manager_info->slug;
//                $delegation_conf->ref_event_id = '';
//                $delegation_conf->delegation_version = '';
//                $delegation_conf->step_number = $step_no;
//                $delegation_conf->manage_by = '';
//                $delegation_conf->same_sort = '';
                self::jobUpdateForInitialize($post,$delegation_conf,$id_manager_info,$releiver_of_id,$step_user_info[$step_no]);
                $result['data'][$post['code'][0]]['mode']='Success';
                $result['data'][$post['code'][0]]['msg']='Successfully Initiatate.';
                $result['status_id']=$id_manager_info->initiate_approve_status;
            }else{
                $result['data'][$post['code'][0]]['mode']='Failed';
                $result['data'][$post['code'][0]]['msg']='Delegation Configuration Not Found.';
                $result['status_id']=$id_manager_info->initiate_approve_status;
            }
        }else{

            $delegation_conf = self::delegationConf($id_manager_info,$step_no,$post);

            if($delegation_conf){
                switch ($delegation_conf->manage_by){
                    case 'Hierarchy':
                        $line_manager_info = self::getLineManagerInfo(Auth::user()->id);
                        if($line_manager_info->next_assign_person){
                            $releiver_of_id = self::getReleiverOfInfo($line_manager_info->next_assign_person);
                            self::jobUpdateForInitialize($post,$delegation_conf,$id_manager_info,$releiver_of_id,$line_manager_info->next_assign_person);
                            $result['data'][$post['code'][0]]['mode']='Success';
                            $result['data'][$post['code'][0]]['msg']='Successfully Initiatate.';
                            $result['status_id']=$id_manager_info->initiate_approve_status;

                        }else{
                            $result['data'][$post['code'][0]]['mode']='Failed';
                            $result['data'][$post['code'][0]]['msg']='Line Manager Not Found.';
                            $result['status_id']=$id_manager_info->initiate_approve_status;
                        }
                        break;

                    case 'Designation':
                        //debug($delegation_conf,1);
                        $duser = getUserInfoFromDesignationId($delegation_conf->designation_id);
                        $releiver_of_id = self::getReleiverOfInfo($duser->id);
                        self::jobUpdateForInitialize($post,$delegation_conf,$id_manager_info,$releiver_of_id,$duser->id);
                        $result['data'][$post['code'][0]]['mode']='Success';
                        $result['data'][$post['code'][0]]['msg']='Successfully Initiatate.';
                        $result['status_id']=$id_manager_info->initiate_approve_status;
                        break;

                    case 'Sorting':
                        $step_info = self::getStepInfo($delegation_conf,'sort_number');
                        if($delegation_conf->same_sort && (count($step_info) > 1)){
                            self::jobUpdateForInitialize($post,$delegation_conf,$id_manager_info);
                            $result['data'][$post['code'][0]]['mode']='Success';
                            $result['data'][$post['code'][0]]['msg']='Successfully Initiatate.';
                            $result['status_id']=$id_manager_info->initiate_approve_status;
                        }else{
                            $releiver_of_id = self::getReleiverOfInfo($step_info[0]->user_id);
                            self::jobUpdateForInitialize($post,$delegation_conf,$id_manager_info,$releiver_of_id,$step_info[0]->user_id);
                            $result['data'][$post['code'][0]]['mode']='Success';
                            $result['data'][$post['code'][0]]['msg']='Successfully Initiatate.';
                            $result['status_id']=$id_manager_info->initiate_approve_status;
                        }
                        break;

                    case 'Limit':
                        $step_info = self::getStepInfo($delegation_conf,'max_limit');
                        if($step_info[0]->max_limit){
                            $releiver_of_id = self::getReleiverOfInfo($step_info[0]->user_id);
                            self::jobUpdateForInitialize($post,$delegation_conf,$id_manager_info,$releiver_of_id,$step_info[0]->user_id);
                            $result['data'][$post['code'][0]]['mode']='Success';
                            $result['data'][$post['code'][0]]['msg']='Successfully Initiatate.';
                            $result['status_id']=$id_manager_info->initiate_approve_status;
                        }else{
                            $result['data'][$post['code'][0]]['mode']='Failed';
                            $result['data'][$post['code'][0]]['msg']='Limit Configuration Not Found.';
                            $result['status_id']=$id_manager_info->initiate_approve_status;
                        }
                        break;

                    default:
                        $result['data'][$post['code'][0]]['mode']='Failed';
                        $result['data'][$post['code'][0]]['msg']='Something Wrong!';
                        $result['status_id']=$id_manager_info->initiate_approve_status;
                        break;
                }
            }else{
                $result['data'][$post['code'][0]]['mode']='Failed';
                $result['data'][$post['code'][0]]['msg']='Delegation Configuration Not Found.';
                $result['status_id']=$id_manager_info->initiate_approve_status;
            }
        }
        return $result;
    }




    /*
     * $job_info = which jobs are sending for approval. its have actually auto generated id and comes from purchase or sales or requisition tabale etc
     * $delegation_configuration = delegation configuration store delegation for, reference event, delegation step no. , delegation person, manage by, limit etc
     * $reliever_of_id = reliever id its comes from user table. if the assign person is relievered.
     * $assign_person = the next assign person who will be assign.
     *
     */

    public function jobUpdateForInitialize($job_info,$delegation_configuration,$id_manager_info,$reliever_of_id=null,$assign_person_id=null){
        $job_ids = array();
        foreach($job_info['code'] as $job){
            array_push($job_ids,$job);
            $delegation_reliever_id = ($reliever_of_id)?$reliever_of_id:$assign_person_id;
            $update_array = array(
                'delegation_for'=>$delegation_configuration->delegation_for, // slug
                'delegation_ref_event_id'=>$delegation_configuration->ref_event_id,
                'delegation_version'=>$delegation_configuration->delegation_version,
                'delegation_step'=>$delegation_configuration->step_number,
                'delegation_person'=>$assign_person_id,
                'delegation_reliever_id'=>$delegation_reliever_id,
                $id_manager_info->ref_status_field=>$id_manager_info->initiate_approve_status,
                'delegation_initialized'=>currentDateTime()
            );

            DB::table($id_manager_info->ref_db_table_name)
                ->where($id_manager_info->ref_id_field,$job)
                ->update($update_array);
        }

        //for notification
        $approval_url = DB::table('sys_approval_modules')->select('approval_url')->where('unique_id_logic_slug',$delegation_configuration->delegation_for)->first()->approval_url;
        if(($delegation_configuration->manage_by == 'Sorting') && $delegation_configuration->same_sort){
            $same_delegation_person = DB::table('sys_delegation_conf')
                ->where('delegation_for',$delegation_configuration->delegation_for)
                ->where('ref_event_id',$delegation_configuration->ref_event_id)
                ->where('delegation_version',$delegation_configuration->delegation_version)
                ->get()->toArray();
            foreach ($delegation_configuration as $noti_info){
                $noti_arr = [
                    'generated_from'=> 'Person',
                    'generated_source'=> session()->get('USER_ID'),
                    'notify_to'=> $noti_info->user_id, // jar kache notification jabe
                    'event_for'=> $delegation_configuration->delegation_for, // event slug / id_logic_slug / approval event slug
                    'event_id'=> json_encode($job_ids),
                    'content'=> 'You have a job to approve. Please Check your Approval Queue.',
                    'url_ref'=> $approval_url, // Approval module redirect url
                    'created_at'=> currentDate(),
                    'priority'=> 3
                ];
                $id = DB::table('sys_notifys')->insertGetId($noti_arr);
                event(new NotifyEvent($id));
            }
        }else{
            $noti_arr = [
                'generated_from'=> 'Person',
                'generated_source'=> session()->get('USER_ID'),
                'notify_to'=> ($delegation_reliever_id)?$delegation_reliever_id:'', // jar kache notification jabe
                'event_for'=> $delegation_configuration->delegation_for, // event slug / id_logic_slug / approval event slug
                'event_id'=> json_encode($job_ids),
                'content'=> 'You have a job to approve. Please Check your Approval Queue.',
                'url_ref'=> $approval_url, // Approval module redirect url
                'created_at'=> currentDate(),
                'priority'=> 3
            ];
            $id = DB::table('sys_notifys')->insertGetId($noti_arr);
            event(new NotifyEvent($id));
        }
    }



    public function getStepInfo($delegation_configuration,$order_by){
        $query = DB::table('sys_delegation_conf');
        $query->where('delegation_for',$delegation_configuration->delegation_for);
        $query->where('ref_event_id',$delegation_configuration->ref_event_id);
        $query->where('step_number',$delegation_configuration->step_number);
        $query->orderBy($order_by,'ASC');
        $result = $query->get()->toArray();
        return $result;
    }




    public function getReleiverOfInfo($user_id){
        $now = date('Y-m-d H:i:s');
        $query = DB::table('sys_users');
        $query->select('reliever_to');
        $query->where('id',$user_id);
        $query->where('is_reliever',1);
        $query->where('reliever_start_datetime','<=',$now);
        $query->where('reliever_end_datetime','>=',$now);
        $result = $query->first();
        if($result){
            return $result->reliever_to;
        }else{
            return false;
        }
    }




    public function getLineManagerInfo($user_id){
        $query = DB::table('sys_users');
        $query->select('line_manager_id as next_assign_person');
        $query->where('id',$user_id);
        $result = $query->first();
//        dd($user_id);
        return $result;
    }




    public function idManagerInfo($slug){
        $query = DB::table('sys_unique_id_logic');
        $query->where('slug',$slug);
        $result = $query->first();
        return $result;
    }



    /*
     *$step_no = if this function use for initiate delegation then its value null but when use for approval_process then its has a value
     */
    public function delegationConf($id_manager_info,$step_no,$post){
        $ref_event_id = self::refEventId($id_manager_info,$post);
        $query = DB::table('sys_delegation_conf');
        $query->where('delegation_for',$id_manager_info->slug);
        $query->where('ref_event_id',$ref_event_id);
        $query->where('delegation_version',$id_manager_info->delegation_version);
        if($step_no){
            $query->where('step_number',$step_no);
        }
        $query->orderBy('step_number','ASC');
        $result = $query->first();
        //debug($result,1);
        return $result;
    }




    public function refEventId($id_manager_info,$post){
        //debug($post,1);
        if($id_manager_info->delegation_trigger == 'SQL'){
            $sql = str_replace('@job_code',"'".$post['code'][0]."'",$id_manager_info->trigger_sql);
            //debug($sql,1);
            $sql_raw = DB::select($sql);
            $ref_event_id = $sql_raw[0]->job_value;
        }else{
            $ref_event_id = '';
            $sessions = explode(',',$id_manager_info->session_variable);
            foreach($sessions as $session){
                $ref_event_id .= session(strtoupper($session)).',';
            }
        }

        return rtrim($ref_event_id,',');
    }


    public function configurationInfoSingleRow($job_info, $query_type=''){

        $conf_query = DB::table('sys_delegation_conf');
        $conf_query->where('delegation_for',$job_info->delegation_for);
        $conf_query->where('ref_event_id',$job_info->delegation_ref_event_id);
        $conf_query->where('delegation_version',$job_info->delegation_version);
        $conf_query->where('step_number',$job_info->delegation_step);
        if($query_type == 'for_user_info'){
            $conf_query->where('user_id',$job_info->delegation_person);
        }
        $conf = $conf_query->first();
        return $conf;
    }


    public function configurationInfoMultiRow($job_info){
        $conf_query = DB::table('sys_delegation_conf');
        $conf_query->select('user_id');
        $conf_query->where('delegation_for',$job_info->delegation_for);
        $conf_query->where('ref_event_id',$job_info->delegation_ref_event_id);
        $conf_query->where('delegation_version',$job_info->delegation_version);
        $conf_query->where('step_number',$job_info->delegation_step);
        $conf_query->where('same_sort',1);
        $conf_query->where('must_approve',1);
        $conf = $conf_query->get()->toArray();
        return $conf;
    }

    public function actionTableInfo($id_logic,$post){
        $job_info_query = DB::table($id_logic->ref_db_table_name);
        $job_info_query->where($id_logic->ref_id_field,$post['code']);
        $job_info = $job_info_query->first();
        return $job_info;
    }

}
