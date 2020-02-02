<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

class LocationTree extends Controller
{

    public function searchForm($dpids=null){
        $data=[];
        if ($dpids){
            $data['dpids'] = $dpids;
        }
        return view('location_access.search_location_tree', $this->getAllPoints($dpids), $data);
    }


    public function searchFormSingle(){
        return view('location_access.search_criteria_single', $this->getAllPoints());
    }

    public function searchFormServey(){
        return view('location_access.search_criteria_survey', $this->getAllPoints());
    }

    public function getTreePlaces(){
        return response()->json($this->getAllPoints());
    }

    public function getAllPoints($dpids2=null) {

        $dpids = session('PRIVILEGE_POINT');

        // $dsids = session('user')['dsid'];
        $dsids = collect(DB::select("SELECT GROUP_CONCAT(DISTINCT dsid) as dsid FROM `bat_distributorspoint` WHERE id IN ($dpids)"))->first()->dsid;

        // Regions Data
        $regions = DB::select(DB::raw("SELECT DISTINCT bat_locations.id, bat_locations.slug FROM bat_locations INNER JOIN bat_distributorspoint ON bat_distributorspoint.region = bat_locations.id WHERE bat_locations.ltype = 7 AND bat_locations.stts = 1  AND bat_distributorspoint.id IN($dpids)"));

        //
        // Area Data
        $area = DB::select(DB::raw("SELECT DISTINCT bat_locations.id, bat_locations.slug, bat_locations.parent AS region FROM bat_locations INNER JOIN bat_distributorspoint ON bat_distributorspoint.area = bat_locations.id WHERE bat_locations.ltype = 8 AND bat_locations.stts = 1 AND bat_distributorspoint.id IN($dpids)"));

        // Companies Data
        $companies = DB::select(DB::raw("SELECT bat_company.region, bat_company.area, bat_company.bat_company_id, bat_company.company_name AS name FROM bat_company where bat_company.bat_company_id IN($dsids) AND bat_company.sales_type ='BAT'"));

        //Territory Data
         $territory = DB::select(DB::raw("SELECT bat_company_territory.dsid as company, bat_company_territory.territory as id, bat_company_territory.name FROM bat_company_territory INNER JOIN bat_distributorspoint ON bat_company_territory.territory = bat_distributorspoint.territory INNER JOIN bat_company ON bat_company.bat_company_id = bat_company_territory.dsid where bat_company.sales_type ='BAT' AND bat_company_territory.dsid IN($dsids) AND bat_distributorspoint.id IN($dpids) GROUP BY bat_company_territory.dsid,bat_company_territory.territory"));

        // Points Data
         $points = DB::select(DB::raw("SELECT bat_distributorspoint.dsid, bat_distributorspoint.region, bat_distributorspoint.area, bat_distributorspoint.territory, bat_distributorspoint.id, bat_distributorspoint.`name` FROM bat_distributorspoint  WHERE  bat_distributorspoint.sales_type ='BAT' AND bat_distributorspoint.id IN($dpids)"));

        $data = array (
            'regions'   => $regions,
            'areas'     => $area,
             'territory' => $territory,
            'companies' => $companies,
             'points'    => $points,
        );
         
        return $data;
    }


}