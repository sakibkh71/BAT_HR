<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Dsms\DsmsSkus;
use App\Models\Dsms\DsmsProducts;
use App\Models\Dsms\DsmsCategoryAttributes;
use App\Models\Dsms\DsmsSkuAttributes;
use App\Models\Dsms\DsmsCategorys;
// use App\Services\LocationDropDownService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;
use Auth;
use URL;
use DB;
use Redirect;

// use App\Helpers\locatonDropDownHelper;


class LocationConfigurationController extends Controller
{
    public function view(Request $request){
        
        $data['title'] = "MULTI SELECT";

        return view('multiselect', $data);
    }

    public function find_nested_data($ary_index, $parent_id, $tbl_list){
        
        $target_ary = [];
        $results = [];
        $parent_id_ary = [];

        $session_concat_val = "session_".$tbl_list[$ary_index]."_ary";
        $target_ary = Session::get($session_concat_val);

        $p = 0;
        if(count($target_ary) > 0){
            foreach($target_ary as $info){
                if(in_array($info['parent'], $parent_id)){
                    $results[$p]['name'] = $info['name'];
                    $results[$p]['id'] = $info['id'];
                    $parent_id_ary[] = $info['id'];
                    $p++;
                }
            }
        }

        return [$results, $parent_id_ary];

    }

    public function find_prev_data($indxCurrent, $prev_id, $tbl_list){

        $session_val = "session_".$tbl_list[$indxCurrent]."_ary";
        $dropdown_ary = Session::get($session_val);

        $results = [];
        $prev_id_ary = [];
        $p = 0;
        foreach($dropdown_ary as $innfo){
            if(in_array($innfo['id'], $prev_id)){

                $results[$p]['name'] = $innfo['name'];
                $results[$p]['id'] = $innfo['id'];
                if($innfo['parent'] > 0){
                    $prev_id_ary[] = $innfo['parent'];
                    //parent for current 
                    //id for prev
                }

                $p++;
            }
        }

        return [$results, $prev_id_ary];
    }

    public function test(Request $request){

        $req_from = "geo_location_5";
        $parent_id = [];
        $parent_id = ["27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40"];
        //id for req_from 
        //parent id for next table
        $reasult = [];
        $final_result_prev = [];
        $final_result_next = [];
        $result = [];

        $tbl_list[0] = 'geo_location_1';
        $tbl_list[1] = 'geo_location_2';
        $tbl_list[2] = 'geo_location_3';
        $tbl_list[3] = 'geo_location_4';
        $tbl_list[4] = 'geo_location_5';
        $tbl_list[5] = 'geo_location_6';
        $tbl_list[6] = 'geo_location_7';
        $tbl_list[7] = 'geo_location_8';

        //===============
        $prev_id = [];
        //find parent of req_from 
        $ary_index_back = array_search($req_from, $tbl_list);

        $req_from_concat = "session_".$req_from."_ary";
        $current_dropdown_ary = Session::get($req_from_concat);

        foreach($current_dropdown_ary as $innfo){
            if(in_array($innfo['id'], $parent_id)){
                if($innfo['parent'] > 0){
                    $prev_id[] = $innfo['parent'];
                    //parent for current 
                    //id for prev
                }
            }
        }

        for($indxCurrent = $ary_index_back-1; $indxCurrent >= 0; $indxCurrent--){
            if($indxCurrent >= 0){
                
                $reasult = $this->find_prev_data($indxCurrent, $prev_id, $tbl_list);
                $prev_id = [];
                $prev_id = $reasult[1];

                if(count($reasult[0]) > 0){
                    $final_result_prev[$tbl_list[$indxCurrent]] = $reasult[0];
                }
            }
        }

        //=====

        $check = 0;
        $temp_ary_index = 0;
        foreach($tbl_list as $info){
            if($info == $req_from || $check > 0){
                if($check == 0){
                    $ary_index = array_search($req_from, $tbl_list)+1;
                }
                $req_from = '';
                $check++;
                
                if($ary_index < count($tbl_list)){
                    $temp = $ary_index;
                    $reasult = $this->find_nested_data($ary_index++, $parent_id, $tbl_list);
                    $parent_id = [];
                    $parent_id = $reasult[1];
                    $final_result_next[$tbl_list[$temp]] = $reasult[0];
                }


            } 


        }


        $result['next'] = $final_result_next;
        $result['prev'] = $final_result_prev;

        dd($result);
        
    }

    public function return_location(Request $request){

        $req_from = $request->req_from;
        $parent_id = [];
        $parent_id = $request->parent_id;

        $reasult = [];
        $final_result_prev = [];
        $final_result_next = [];
        $result = [];

        $tbl_list[0] = 'geo_location_1';
        $tbl_list[1] = 'geo_location_2';
        $tbl_list[2] = 'geo_location_3';
        $tbl_list[3] = 'geo_location_4';
        $tbl_list[4] = 'geo_location_5';
        $tbl_list[5] = 'geo_location_6';
        $tbl_list[6] = 'geo_location_7';
        $tbl_list[7] = 'geo_location_8';

        //===============
        $prev_id = [];
        //find parent of req_from 
        $ary_index_back = array_search($req_from, $tbl_list);

        $req_from_concat = "session_".$req_from."_ary";
        $current_dropdown_ary = Session::get($req_from_concat);

        foreach($current_dropdown_ary as $innfo){
            if(in_array($innfo['id'], $parent_id)){
                if($innfo['parent'] > 0){
                    $prev_id[] = $innfo['parent'];
                    //parent for current 
                    //id for prev
                }
            }
        }

        for($indxCurrent = $ary_index_back-1; $indxCurrent >= 0; $indxCurrent--){
            if($indxCurrent >= 0){

                $reasult = $this->find_prev_data($indxCurrent, $prev_id, $tbl_list);
                $prev_id = [];
                $prev_id = $reasult[1];
            
                if(count($reasult[0]) > 0){
                    $final_result_prev[$tbl_list[$indxCurrent]] = $reasult[0];
                }
            }
        }
        //=====

        $check = 0;
        $temp_ary_index = 0;
        foreach($tbl_list as $info){
            if($info == $req_from || $check > 0){
                if($check == 0){
                    $ary_index = array_search($req_from, $tbl_list)+1;
                }
                $req_from = '';
                $check++;
                
                if($ary_index < count($tbl_list)){
                    $temp = $ary_index;
                    $reasult = $this->find_nested_data($ary_index++, $parent_id, $tbl_list);
                    $parent_id = [];
                    $parent_id = $reasult[1];
                    $final_result_next[$tbl_list[$temp]] = $reasult[0];
                }
            } 
        }

        $result['next'] = $final_result_next;
        $result['prev'] = $final_result_prev;

        return $result;
    }
}