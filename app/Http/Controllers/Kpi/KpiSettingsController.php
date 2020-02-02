<?php

namespace App\Http\Controllers\Kpi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class KpiSettingsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return 'kpi lisging page';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $code=null){
        $points = null;
        if ($code){
            //for config
            $data['kpi'] = DB::table('bat_kpi_configs')->select(
                'bat_kpi_configs_name',
                'kpi_config_code',
                'config_month',
                'selected_ff_type',
                DB::raw('group_concat(bat_dpid) as bat_dpid')
            )
            ->where('kpi_config_code', $code)
            ->groupBy('kpi_config_code')
            ->groupBy('bat_kpi_configs_name')
            ->groupBy('config_month')
            ->groupBy('selected_ff_type')
            ->first();
            $points = $data['kpi']->bat_dpid;

            //for Config Details
            $config_details = DB::table('bat_kpi_config_details')->where('kpi_config_code', $code)->get();
            $kpiDetailsArray = [];
            foreach ($config_details as $kpiconfig) {
                $kpiDetailsArray[$kpiconfig->bat_kpi_id]['bat_kpi_id'] = $kpiconfig->bat_kpi_id;
                $kpiDetailsArray[$kpiconfig->bat_kpi_id]['weight'] = $kpiconfig->weight;
                $kpiDetailsArray[$kpiconfig->bat_kpi_id]['target_brands'] = !empty($kpiconfig->target_brands)?explode(",",$kpiconfig->target_brands):[];
                $kpiDetailsArray[$kpiconfig->bat_kpi_id]['target_familys'] = !empty($kpiconfig->target_familys)?explode(",",$kpiconfig->target_familys):[];
                $kpiDetailsArray[$kpiconfig->bat_kpi_id]['target_segments'] = !empty($kpiconfig->target_segments)?explode(",",$kpiconfig->target_segments):[];
            }
            $data['kpi_details'] = $kpiDetailsArray;
        }

        $data['components'] = DB::table('bat_kpis')->where('status', 'Active')->get();
        $data['brands_list'] = DB::table('bat_products')->where('stts', 1)->pluck('name','products_id');
        $data['family_list'] = DB::table('bat_cats')->where('parent', 168)->where('stts', 1)->pluck('slug','id');
        $data['segments_list'] = DB::table('bat_cats')->where('parent', 1)->where('stts', 1)->pluck('slug','id');
        $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationTree')->searchForm($points);

        return view('HR.kpi.kpi_create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $code=null)
    {
        //Generate Code by logic
        if (!empty($code)){ $kpi_config_code = $code; }else { $kpi_config_code = generateId('kpi_code'); }

        $selected_ff_type = !empty($request->selected_ff_type)?implode(",",$request->selected_ff_type):'';

        if (!empty($request->point)) {
            //Prepare Insert Data for bat_kpi_configs
            $insertArr = [];

            foreach ($request->point as $key => $point) {

                //Get old configure if exist based on month and point id
                $old_ff_type = DB::table('bat_kpi_configs')
                    ->select('bat_dpid', 'config_month', DB::raw('group_concat(selected_ff_type) as ff_type'))
                    ->where('bat_dpid', $point)->where('config_month', $request->config_month)
                    ->where('kpi_config_code', '!=', $kpi_config_code)
                    ->groupBy('bat_dpid')
                    ->groupBy('config_month')
                    ->first();

                if (isset($old_ff_type) && !empty($old_ff_type->ff_type)) {
                    $ff_type_array = explode(",", $old_ff_type->ff_type);
                    //check if already exist configure
                    $check_exist = array_intersect($request->selected_ff_type, $ff_type_array);
                    //Redirect when find exist value
                    if (!empty($check_exist)) {
                        //return redirect()->back()->with('error', "KPI Configuration Already Exist!");
                        return back()->withInput()->with('error', "KPI Configuration Already Exist!");
                    };
                }

                //prepare data
                $insertArr[$key]['bat_kpi_configs_name'] = $request->bat_kpi_configs_name;
                $insertArr[$key]['kpi_config_code'] = $kpi_config_code;
                $insertArr[$key]['bat_company_id'] = self::getCompanyId($point);
                $insertArr[$key]['bat_dpid'] = $point;
                $insertArr[$key]['config_month'] = $request->config_month;
                $insertArr[$key]['selected_ff_type'] = $selected_ff_type;
                $insertArr[$key]['status'] = 'Active';
                if ($code) {
                    $insertArr[$key]['updated_by'] = Auth::id();
                    $insertArr[$key]['updated_at'] = dTime();
                } else {
                    $insertArr[$key]['created_by'] = 'Active';
                    $insertArr[$key]['created_at'] = dTime();
                }
            }

            //Prepare  Data For bat_kpi_config_details
            $config_detail = [];



            //Prepare data for bat_kpi_config_ranges
            $kpi_config_ranges = array([
                'kpi_config_code' => $kpi_config_code,
                'range_from' => 70,
                'range_to' => 80,
                'range_value' => 50,
                'status' => 'Active',
                'created_by' => Auth::id(),
                'created_at' => dTime(),
            ],
                [
                    'kpi_config_code' => $kpi_config_code,
                    'range_from' => 81,
                    'range_to' => 90,
                    'range_value' => 80,
                    'status' => 'Active',
                    'created_by' => Auth::id(),
                    'created_at' => dTime(),
                ],
                [
                    'kpi_config_code' => $kpi_config_code,
                    'range_from' => 91,
                    'range_to' => 110,
                    'range_value' => null,
                    'status' => 'Active',
                    'created_by' => Auth::id(),
                    'created_at' => dTime(),
                ]);
            DB::beginTransaction();

            try {
                if (!empty($insertArr)) {
                    DB::table('bat_kpi_configs')->where('kpi_config_code', $kpi_config_code)->delete();
                    $insert_config = DB::table('bat_kpi_configs')->insert($insertArr);
                }

                if ($insert_config) {
                    DB::table('bat_kpi_config_details')->where('kpi_config_code', $kpi_config_code)->delete();
                    $config_kpis = DB::table('bat_kpi_configs')->where('kpi_config_code', $kpi_config_code)->get();
                    foreach ($config_kpis as $kpi){
                        foreach ($request->bat_kpi_id as $key => $item) {
                            $config_detail[] = array(
                                'kpi_config_code'=>$kpi_config_code,
                                'bat_kpi_configs_id'=>$kpi->bat_kpi_configs_id,
                                'bat_kpi_id'=>$item,
                                'weight'=>$request->weight[$item],
                                'target_brands'=>!empty($request->target_brands[$item]) ? implode(",", $request->target_brands[$item]) : '',
                                'target_familys'=>!empty($request->target_familys[$item]) ? implode(",", $request->target_familys[$item]) : '',
                                'target_segments'=>!empty($request->target_segments[$item]) ? implode(",", $request->target_segments[$item]) : '',
                                'status'=>'Active',
                                'created_by'=>Auth::id(),
                                'created_at'=>dTime(),
                            );

                        }
                    }

                    DB::table('bat_kpi_config_details')->insert($config_detail);
                }


                if ($insert_config) {
                    DB::table('bat_kpi_config_ranges')->where('kpi_config_code', $kpi_config_code)->delete();
                    DB::table('bat_kpi_config_ranges')->insert($kpi_config_ranges);
                }


                DB::commit();
                return redirect()->back()->with('success', 'KPI Insert Successfully!');

            } catch (\Exception $e) {
                DB::rollback();
                $data['msg'] = "Data Not saved";
                return redirect()->back()->with('error', "Data Not saved!");
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public  static function getCompanyId($point){
        if(!empty($point)){
           return DB::table('bat_distributorspoint')->where('id',$point)->first()->dsid;
        }
        return false;
    }
}
