<?php

function gen_ary($collection, $tbl_name, $tbl_id, $tbl_parent){

    $result = [];
    $i = 0;
    if(count($collection) > 0){
        foreach($collection as $val){
            $result[$i]['id'] = $val->$tbl_id;
            $result[$i]['name'] = $val->$tbl_name;
            $result[$i]['parent'] =  empty($tbl_parent)?0:$val->$tbl_parent;
            $i++;
        }
    }

    return $result;
}

function getLocationDropDownData($val=null, $fl=null){
  
  // $enable_location_id = DB::table('dsms_user_location_permission')->select('geo_location_8_id')->where('sys_users_id', session('USER_ID'))->pluck('geo_location_8_id');

    $data['title'] = '';
    $locations = DB::table('sys_system_settings')->select('option_value', 'option_key')
            ->where('option_group', 'geo_location')->where('status', 'Active')->get();

    $innn = 1;
    $exclude = [];
    foreach($locations as $inn){
      
      if($fl=='first' || $fl=='First'){
        if($innn > $val){
          $exclude[] = $innn;
        }
      }
      elseif($fl=='last' || $fl=='Last'){
        if($innn <= count($locations)-$val){
          $exclude[] = $innn;
        }
      }
      else{
          $exclude[] = [];
      }

      $innn++;
    }

    $option_value = [];
    $option_key   = [];

    $chhkk  = 1;

    foreach($locations as $info){
        if(!in_array($chhkk, $exclude)){
          if(!empty($info->option_key) && !empty($info->option_value)){
              $option_key[]   = $info->option_key;

              if($info->option_key ==  'geo_location_1'){
                 $option_value[1] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_2'){
                 $option_value[2] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_3'){
                 $option_value[3] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_4'){
                 $option_value[4] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_5'){
                 $option_value[5] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_6'){
                 $option_value[6] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_7'){
                 $option_value[7] = $info->option_value; 
              }

              if($info->option_key ==  'geo_location_8'){
                 $option_value[8] = $info->option_value; 
              }
          }
        }

        $chhkk ++;
    }

    $geo_location_ary = [];

    $data['option_value'] = $option_value; //name
    $data['option_key']   = $option_key; //loc table
    $data['geo_last_tbl'] = DB::table('geo_location_8')->where('status', 'Active')->get();

    //->whereIn('geo_location_8_id', $enable_location_id->toArray())

    $ary_8_id = ['geo_location_1_id', 'geo_location_2_id', 'geo_location_3_id', 'geo_location_4_id', 'geo_location_5_id', 'geo_location_6_id', 'geo_location_7_id', 'geo_location_8_id' ];

    $ary_8_name = ['geo_location_1_name', 'geo_location_2_name', 'geo_location_3_name', 'geo_location_4_name', 'geo_location_5_name', 'geo_location_6_name', 'geo_location_7_name', 'geo_location_8_name' ];

    $ary_8_session = ['session_geo_location_1_ary', 'session_geo_location_2_ary', 'session_geo_location_3_ary', 'session_geo_location_4_ary', 'session_geo_location_5_ary', 'session_geo_location_6_ary', 'session_geo_location_7_ary', 'session_geo_location_8_ary'];

    $ary_8_geo = ['geo_location_1_ary', 'geo_location_2_ary', 'geo_location_3_ary', 'geo_location_4_ary', 'geo_location_5_ary', 'geo_location_6_ary', 'geo_location_7_ary', 'geo_location_8_ary'];

    for($ir = 0; $ir <= 7; $ir++){

        $loc_qry = DB::table('geo_location_8')->groupBy($ary_8_id[$ir])->where('status', 'Active')->get();

        if($ir == 0){
          
          $geo_location_ary[$ir] = gen_ary($loc_qry, $ary_8_name[$ir], $ary_8_id[$ir], '');
        }
        else{
          
          $geo_location_ary[$ir] = gen_ary($loc_qry, $ary_8_name[$ir], $ary_8_id[$ir], $ary_8_id[$ir-1]);
        }

        // if (!Session::has($ary_8_session[$ir])){
            Session::put($ary_8_session[$ir], $geo_location_ary[$ir]);
        // }

        $data[$ary_8_geo[$ir]] = $geo_location_ary[$ir];
    }

    Session::save();

    $data['exclude'] = json_encode($exclude);
    $data['option_key_js'] = json_encode($option_key);

    return view('includes.location_drop_down', $data);
}