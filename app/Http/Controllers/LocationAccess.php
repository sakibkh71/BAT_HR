<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

class LocationAccess extends Controller
{
    public function searchForm($dpids=null){
        if ($dpids){
            $data['single'] = true;
        }else{
            $data['single'] = false;
        }
        return view('location_access.search_criteria_multiple', $this->getAllPoints($dpids), $data);
    }

    public function searchFormTest($dpids=null){
        if ($dpids){
            $data['single'] = true;
        }else{
            $data['single'] = false;
        }
        return view('location_access.search_criteria_multiple_test', $this->getAllPoints($dpids), $data);
    }


    public function searchFormSingle(){
        return view('location_access.search_criteria_single', $this->getAllPoints());
    }

    public function searchFormServey(){
        return view('location_access.search_criteria_survey', $this->getAllPoints());
    }

    public function getAllPlaces(){
        return response()->json($this->getAllPoints());
    }

    public function getAllPoints($dpids2=null) {

        if($dpids2){
            $dpids = $dpids2;
        }else{
            $dpids = session('PRIVILEGE_POINT');
        }
//        if($dpids==''){
//            $points = DB::table('bat_distributorspoint')->where('stts','!=','0')->get()->toArray();
//            $dpids = implode(',',array_column($points,'id'));
//
//        }

        // $dsids = session('user')['dsid'];
        $dsids = collect(DB::select("SELECT GROUP_CONCAT(DISTINCT dsid) as dsid FROM `bat_distributorspoint` WHERE id IN ($dpids)"))->first()->dsid;
        // Regions Data
        $regions = DB::select(DB::raw("SELECT DISTINCT bat_locations.id, bat_locations.slug FROM bat_locations INNER JOIN bat_distributorspoint ON bat_distributorspoint.region = bat_locations.id WHERE bat_locations.ltype = 7 AND bat_locations.stts = 1 AND bat_distributorspoint.id IN($dpids)"));
        //
        // Area Data
        $area = DB::select(DB::raw("SELECT DISTINCT bat_locations.id, bat_locations.slug, bat_locations.parent AS region FROM bat_locations INNER JOIN bat_distributorspoint ON bat_distributorspoint.area = bat_locations.id WHERE bat_locations.ltype = 8 AND bat_locations.stts = 1 AND bat_distributorspoint.id IN($dpids)"));

        // Companies Data
        $companies = DB::select(DB::raw("SELECT bat_company.region, bat_company.area, bat_company.bat_company_id, bat_company.company_name AS name FROM bat_company WHERE bat_company.bat_company_id IN($dsids)"));

//         Territory Data
         $territory = DB::select(DB::raw("SELECT bat_company_territory.dsid as company, bat_company_territory.territory as id, bat_company_territory.name FROM bat_company_territory INNER JOIN bat_distributorspoint ON bat_company_territory.territory = bat_distributorspoint.territory WHERE bat_company_territory.dsid IN($dsids) AND bat_distributorspoint.id IN($dpids) GROUP BY bat_company_territory.dsid,bat_company_territory.territory"));


        // Points Data
         $points = DB::select(DB::raw("SELECT bat_distributorspoint.dsid, bat_distributorspoint.region, bat_distributorspoint.area, bat_distributorspoint.territory, bat_distributorspoint.id, bat_distributorspoint.`name` FROM bat_distributorspoint WHERE bat_distributorspoint.id IN($dpids)"));
        // Routes Data
        // $routes = DB::select(DB::raw("SELECT routes.id AS id, CONCAT( routes.number, section_days.slug) AS slug, distributorspoint.region AS region, distributorspoint.area AS area, distributorspoint.territory AS territory, routes.dsid AS dsid, routes.dpid AS dpid FROM routes INNER JOIN section_days ON routes.section = section_days.section INNER JOIN distributorspoint ON routes.dpid = distributorspoint.id WHERE routes.dsid IN($dsids) AND routes.dpid IN ($dpids) AND routes.stts = 1"));
        
        // $route_list = array ();
        // foreach ($routes as $route) :
        //     $route_list[] = $route->id;
        // endforeach;
        // $custom_routes = implode(',',$route_list);
            
        // Retailers Data
        // $retailers = DB::select(DB::raw("SELECT retailers.id AS id, retailers.`name` AS `name`, routes.id AS rtid, distributorspoint.dsid AS dsid, distributorspoint.region AS region, distributorspoint.area AS area, distributorspoint.territory AS territory, routes.dpid AS dpid FROM retailers INNER JOIN routes ON retailers.rtid = routes.id INNER JOIN distributorspoint ON routes.dpid = distributorspoint.id WHERE retailers.rtid IN($custom_routes) AND retailers.stts = 1"));

        $data = array (
            'regions'   => $regions,
            'areas'     => $area,
             'territory' => $territory,
            'companies' => $companies,
             'points'    => $points,
            // 'routes'    => $routes,
            //'retailers' => $retailers
        );
         
        return $data;
    }

    // public function getAllRouteWiseOutlet() {
        
    //     $dsids = session('user')['dsid'];
    //     $dpids = session('user')['dpid'];

    //     // Routes Data
    //     $routes = DB::select(DB::raw("SELECT routes.id AS `routes`, CONCAT(routes.number,section_days.slug) AS `slug` FROM routes INNER JOIN section_days ON routes.section = section_days.section WHERE routes.dsid IN($dsids) and routes.dpid IN($dpids) and routes.stts = 1"));
        
    //     $route_list = array ();
    //     foreach ($routes as $route) :
    //         $route_list[] = $route->routes;
    //     endforeach;
    //     $routes = implode(',',$route_list);
    
    //     // Retailers Data
    //     $retailers = DB::select(DB::raw("SELECT retailers.id AS `id`, retailers.`name` AS `name` FROM `retailers` WHERE retailers.rtid IN($routes) AND retailers.stts = 1 "));
        
    //     $data = array (
    //         'routes'    => $routes,
    //         'retailers' => $retailers
    //     );
    //     return response()->json($data);
    // }
}