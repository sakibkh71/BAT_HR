<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
// use App\Models\User;
use App\Models\Kpi\KpiConfig;
use App\Models\Kpi\KpiConfigRange;
use App\Models\Kpi\KpiProperties;
use App\Models\Kpi\KpiConfigDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use URL;
use DB;

class KpiConfigController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function index(){
        
        // $data['bonusSheet'] = "";
        $data[] = "";

        return view('HR.kpi.kpi_config', $data);
    }

    public function view($kpi_config_id){

        $data = DB::select("select bat_kpi_configs.bat_kpi_configs_id, 
                bat_kpi_configs.bat_kpi_configs_name,
                bat_kpi_configs.start_month,
                bat_kpi_configs.end_month,
                bat_kpi_configs.have_range,
                bat_kpi_config_ranges.bat_kpi_config_ranges_id,
                bat_kpi_config_ranges.bat_kpi_configs_id,
                bat_kpi_config_ranges.range_from,
                bat_kpi_config_ranges.range_to,
                bat_kpi_config_ranges.range_value,
                bat_kpi_config_details.bat_kpi_config_details_id,
                bat_kpi_config_details.bat_kpi_configs_id,
                bat_kpi_config_details.bat_kpi_properties_id,
                bat_kpi_config_details.weight,
                bat_company.bat_company_id,
                bat_company.company_name,
                bat_kpi_properties.bat_kpi_properties_id,
                bat_kpi_properties.bat_kpi_properties_name
                from bat_kpi_configs
                left join bat_kpi_config_ranges on bat_kpi_configs.bat_kpi_configs_id = bat_kpi_config_ranges.bat_kpi_configs_id
                join bat_kpi_config_details on bat_kpi_configs.bat_kpi_configs_id = bat_kpi_config_details.bat_kpi_configs_id
                join bat_company on find_in_set(bat_company_id, bat_kpi_configs.scope_market) 
                join bat_kpi_properties on bat_kpi_config_details.bat_kpi_properties_id = bat_kpi_properties.bat_kpi_properties_id 
                where bat_kpi_configs.bat_kpi_configs_id = '$kpi_config_id' and bat_kpi_configs.status = 'Active'");

        $config_range_ary = [];
        $config_details_ary = [];
        $house_ary = [];
        foreach($data as $key=>$info){
            $config_range_ary[$info->bat_kpi_config_ranges_id] = $info;
            $config_details_ary[$info->bat_kpi_config_details_id] = $info;
            $house_ary[$info->bat_company_id] = $info;
        }

        $time_range_string = "<b>&nbsp;".date_format(date_create($data[0]->start_month),"M-Y")."&nbsp;</b>&nbsp;to&nbsp;<b>&nbsp;".date_format(date_create($data[0]->end_month),"M-Y")."</b>";

        $house_name_string = '<br/><ul>';
        foreach($house_ary as $val){
            $house_name_string .= "<li>".$val->company_name."</li>";
        }
        $house_name_string .='</ul>';

        $config_details_string = '';
        if(!empty($config_details_ary)){
            $config_details_string .="<br/><table class='table col-md-6'>";
            $config_details_string .="<tr>";
                $config_details_string .="<th>Name</th>";
                $config_details_string .="<th>Value</th>";
            $config_details_string .="</tr>";
            foreach($config_details_ary as $range){
                $config_details_string .="<tr>";
                    $config_details_string .="<td>".$range->bat_kpi_properties_name."</td>";
                    $config_details_string .="<td>".$range->weight."%</td>";
                $config_details_string .="</tr>";
            }
            $config_details_string .="</table>";
        }

        // dd($config_range_ary);
        $range_string = '';
        if(!empty($data[0]->have_range)){
            $range_string .="<br/><table class='table col-md-6'>";
            $range_string .="<tr>";
                $range_string .="<th>Range from</th>";
                $range_string .="<th>Range to</th>";
                $range_string .="<th>Range value</th>";
            $range_string .="</tr>";
            foreach($config_range_ary as $range){
                $range_string .="<tr>";
                    $range_string .="<td>".$range->range_from."</td>";
                    $range_string .="<td>".$range->range_to."</td>";
                    $range_string .="<td>".$range->range_value."</td>";
                $range_string .="</tr>";
            }
            $range_string .="</table>";
        }
        else{
            $range_string .='No Range available!';
        }
        // dd($config_range_ary, $config_details_ary,$house_ary, $data);


        $mod_result['bat_kpi_configs_id'] = $data[0]->bat_kpi_configs_id;
        $mod_result['bat_kpi_configs_name'] = "<b>".$data[0]->bat_kpi_configs_name."</b>";
        // $mod_result['start_month'] = $result[0]->start_month;
        // $mod_result['end_month'] = $result[0]->end_month;
        $mod_result['kpi_range'] = $range_string;
        $mod_result['config_details'] = $config_details_string;
        $mod_result['market_scope'] = $house_name_string;
        $mod_result['time_range'] = $time_range_string;


        return $mod_result;
    }

    public function editValidation($kpi_config_id){
        $val = KpiConfig::find($kpi_config_id);

        $monthyear = date("Y-m"); 
        $finish = date("Y-m",strtotime($val->end_month));
        $start = date("Y-m",strtotime($val->start_month));
        $current = date("Y-m",strtotime($monthyear));
        
        if($current >= $start && $current <= $finish){
            return "notEdit";
        }
        elseif($current >= $start){
            return "notEdit";
        }
        else{
            return "canEdit";
        }
    }

    public function create_form($id = null){
        
        $data['kpi_properties'] = KpiProperties::where('status', 'Active')->get();
        $data['kpi_config_id'] = ($id > 0) ? $id : 0;

        $ary_house = [];
        $ary_region = [];
        $ary_area = [];
        $properties_id_ary = [];
        $data['config_val'] = '';
        $weight_ary = [];
        $range_from_ary = [];
        $range_to_ary = [];
        $range_value_ary = [];

        if($id > 0){
            $data['config_val'] = KpiConfig::where('bat_kpi_configs_id', $id)->first();
            $house_ary = $data['config_val']->scope_market;

            // dd($house_ary);

            $geo_location_house = DB::select("SELECT * FROM bat_company where bat_company_id In ($house_ary)");

            $properties_info = DB::select("SELECT bat_kpi_properties_id, weight FROM bat_kpi_config_details where bat_kpi_configs_id = $id");
            $config_range = DB::select("SELECT * FROM bat_kpi_config_ranges where bat_kpi_configs_id = $id");

            foreach ($properties_info as $value) {
                array_push($properties_id_ary, $value->bat_kpi_properties_id);
                $weight_ary[$value->bat_kpi_properties_id] = $value->weight;
            }

            // dd($properties_id_ary, $weight_ary);

            foreach($geo_location_house as $loc){
                // echo $loc->geo_location_6_id;
                array_push($ary_house, $loc->bat_company_id);
                array_push($ary_region, $loc->region);
                array_push($ary_area, $loc->area);
            }

            if(count($config_range) > 0){
                foreach($config_range as $rng){
                   array_push($range_from_ary, $rng->range_from); 
                   array_push($range_to_ary, $rng->range_to); 
                   array_push($range_value_ary, $rng->range_value); 
                }
            }

            // dd(count($config_range));

            // dd($ary_house, $ary_region, $ary_area);
            // die();
            
        }
        $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationAccess')->searchForm();
        
        $data['ary_house'] = $ary_house;
        $data['ary_region'] = $ary_region;
        $data['ary_area'] = $ary_area;
        $data['properties_id_ary'] = $properties_id_ary;
        $data['weight_ary'] = $weight_ary;
        $data['range_from_ary'] = $range_from_ary;
        $data['range_to_ary'] = $range_to_ary;
        $data['range_value_ary'] = $range_value_ary;

        return view('HR.kpi.kpi_config_create', $data);
    }

    public function store(Request $request){

        DB::beginTransaction();

        try{
            //check same house same date range available or not
            
            // $scope_market_ary = KpiConfig::select('scope_market')->whereBetween('start_month', [$request->month_from, $request->month_to])->orWhereBetween('end_month', [$request->month_from, $request->month_to])->get();
            $scope_market_ary = KpiConfig::select('scope_market')->where('start_month', $request->month_from)->get();

            foreach($scope_market_ary as $sm){
                $sm_ary = explode(",", $sm->scope_market);
                $result = array_intersect($sm_ary, $request->house);

                if(count($result) > 0){
                    $data['msg'] = "In same time range Same House already used!";
                    $data['code'] = 500;

                    return $data;
                }
            }

            $config_name = $request->config_name;
            // $have_range = $request->have_range;
            $have_range = 'true';
            $range_from = [70, 81, 91];
            $range_to = [80, 90, 110];
            $range_val = [50, 80, ''];
            $start_month = $request->month_from;
            // $end_month = $request->month_to;
            $end_month = $request->month_from;
            $houses = $request->point;
            
            if(in_array('multiselect-all', $houses)){
                $houses = array_diff($houses, ["multiselect-all"]);
            }


            $save_config = new KpiConfig;
            $save_config->bat_kpi_configs_name = $config_name;
            $save_config->have_range = $have_range;
            $save_config->start_month = $start_month;
            $save_config->end_month = $end_month;
            $save_config->status = "Active";
            $save_config->created_by = Auth::user()->id;
            $save_config->scope_market = implode(',', $houses);
            $save_config->save();

            if($have_range == 'true'){
                for($i=0; $i < count($range_from); $i++){

                    //Config Range Validation
                    if(empty($range_from[$i]) || empty($range_to[$i]) ){
                        $data['msg'] = "Config range value can't be null!";
                        $data['code'] = 500;
                        return $data;
                    }

                    $save_config_range = new KpiConfigRange;
                    $save_config_range->range_from = $range_from[$i];        
                    $save_config_range->range_to = $range_to[$i];        
                    $save_config_range->range_value = $range_val[$i];        
                    $save_config_range->bat_kpi_configs_id = $save_config->bat_kpi_configs_id;        
                    $save_config_range->status = 'Active';        
                    $save_config_range->save();        
                }
            }

            for($i=0; $i<count($request->properties); $i++){
                $save_config_details = new KpiConfigDetails;
                $save_config_details->bat_kpi_configs_id = $save_config->bat_kpi_configs_id;
                $save_config_details->bat_kpi_properties_id = $request->properties[$i];
                $save_config_details->weight = $request->weights[$request->properties[$i]];
                $save_config_details->status = 'Active';
                $save_config_details->save();
            }

            DB::commit();
            $data['msg'] = "Data saved Successfully";
            $data['code'] = 200;

        }catch (\Exception $e) {

            DB::rollback();
            $data['msg'] = "Data Not saved";
            $data['code'] = 500;
        }

        return $data;        
    }

    public function update(Request $request){

        // dd($request->all());
        DB::beginTransaction();

        try{
            $config_name = $request->config_name;
            $have_range = $request->have_range;
            $start_month = $request->month_from;
            $end_month = $request->month_to;
            $houses = $request->house;
            if(in_array('multiselect-all', $houses)){
                $houses = array_diff($houses, ["multiselect-all"]);
            }

            $save_config = KpiConfig::find($request->hdn_conf_id);
            $save_config->bat_kpi_configs_name = $config_name;
            $save_config->have_range = $have_range;
            $save_config->start_month = $start_month;
            $save_config->end_month = $end_month;
            $save_config->status = "Active";
            $save_config->created_by = Auth::user()->id;
            $save_config->scope_market = implode(',', $houses);
            $save_config->save();

            if($have_range == 'true'){

                KpiConfigRange::where('bat_kpi_configs_id', $request->hdn_conf_id)->delete();

                for($i=0; $i < count($request->range_from); $i++){
                    if(!empty($request->range_from[$i]) && !empty($request->range_to[$i])){
                        $save_config_range = new KpiConfigRange;
                        $save_config_range->range_from = $request->range_from[$i];        
                        $save_config_range->range_to = $request->range_to[$i];        
                        $save_config_range->range_value = $request->range_val[$i];        
                        $save_config_range->bat_kpi_configs_id = $request->hdn_conf_id;        
                        $save_config_range->status = 'Active';        
                        $save_config_range->save();
                    }         
                }
            }
            else{
                 KpiConfigRange::where('bat_kpi_configs_id', $request->hdn_conf_id)->delete();
            }

            KpiConfigDetails::where('bat_kpi_configs_id', $request->hdn_conf_id)->delete();

            for($i=0; $i<count($request->properties); $i++){
                $save_config_details = new KpiConfigDetails;
                $save_config_details->bat_kpi_configs_id = $request->hdn_conf_id;
                $save_config_details->bat_kpi_properties_id = $request->properties[$i];
                $save_config_details->weight = $request->weights[$request->properties[$i]];
                $save_config_details->status = 'Active';
                $save_config_details->save();
            }
                 
        
            DB::commit();

            // return redirect('kpi-config');
            $data['msg'] = "Data Update Successfully";
            $data['code'] = 200;

        }catch (\Exception $e) {
           
            DB::rollback(); 
            // return redirect('kpi-config-create-form')->with('error', 'Data not saved! ');
            $data['msg'] = "Data Not updated";
            $data['code'] = 500;
        }

        return $data;        
    }

    public function delete(Request $request){
        
        foreach($request->kpi_config_id as $info){
            $val = KpiConfig::where('bat_kpi_configs_id', $info)->first();
            $val->status = 'Inactive';
            $val->save();
        }

        $data['msg'] = "Data Delete Successfully";

        return $data;
    }
}



