<?php

namespace App\Http\Controllers\Kpi;

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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;


class KpiListController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function kpiList($list_group = 'point'){
        $data=array();

        $kpi_sql = DB::table('bat_kpi_configs')->select(
                'bat_kpi_configs.bat_kpi_configs_id',
                'bat_kpi_configs.bat_kpi_configs_name',
                'bat_kpi_configs.kpi_config_code',
                'bat_kpi_configs.config_month',
                'bat_kpi_configs.bat_company_id',
                'bat_company.company_name',
                'bat_kpi_configs.bat_dpid',
                'bat_distributorspoint.name as point_name',
                DB::raw("
                    (SELECT GROUP_CONCAT( bat_kpis.bat_kpi_name ) FROM bat_kpis, bat_kpi_config_details  WHERE bat_kpis.bat_kpi_id = bat_kpi_config_details.bat_kpi_id AND bat_kpi_config_details.bat_kpi_configs_id = bat_kpi_configs.bat_kpi_configs_id ) AS kpi_name,
                    ( SELECT GROUP_CONCAT( designations_name ) FROM designations WHERE FIND_IN_SET( designations.designations_id, bat_kpi_configs.selected_ff_type ) ) AS designation
                ")
            )
            ->join('bat_distributorspoint','bat_distributorspoint.id','=','bat_kpi_configs.bat_dpid')
            ->join('bat_company','bat_company.bat_company_id','=','bat_kpi_configs.bat_company_id');

            if ($list_group =='house'){
                $kpi_sql->addSelect(DB::raw('group_concat(bat_distributorspoint.name separator ", ") as point_group'));
                $kpi_sql->groupBy('bat_company_id');
            }
        $kpi_sql->orderBy('bat_kpi_configs.bat_kpi_configs_id','DESC');

        $session_con = (sessionFilter('url','get-kpi-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $kpi_sql->whereRaw($session_con);
        }


            $data['kpi_list'] = $kpi_sql->get();

            $data['list_group'] = $list_group;



        /*$kpi_list=DB::table('bat_kpi_configs')
            ->selectRaw(
            'bat_kpi_configs.bat_kpi_configs_id,
                        bat_kpi_configs.bat_kpi_configs_name,
                        bat_kpi_configs.kpi_config_code,
                        bat_kpi_configs.config_month,
                        bat_kpi_configs.bat_company_id,
                        bat_company.company_name,
                        bat_kpi_configs.bat_dpid,
                        bat_distributorspoint.`name`,
                        (
                    SELECT
                        GROUP_CONCAT( bat_kpis.bat_kpi_name ) 
                    FROM
                        bat_kpis,
                        bat_kpi_config_details 
                    WHERE
                        bat_kpis.bat_kpi_id = bat_kpi_config_details.bat_kpi_id 
                        AND bat_kpi_config_details.kpi_config_code = bat_kpi_configs.kpi_config_code 
                        ) AS kpi_name,
                        ( SELECT GROUP_CONCAT( designations_name ) FROM designations WHERE FIND_IN_SET( designations.designations_id, bat_kpi_configs.selected_ff_type ) ) AS designation ')
            ->join('bat_distributorspoint','bat_distributorspoint.id','=','bat_kpi_configs.bat_dpid' )
            ->join('bat_company','bat_company.bat_company_id','=','bat_kpi_configs.bat_company_id')
            ->get();*/
         return view('HR.kpi.kpi_list',$data);
    }

    public function locationWiseKpi($id='',$type='',$location_id=''){
        $data=array();

        $kpi_config_sql=DB::table('bat_kpi_configs')->select(
            'bat_kpi_configs.bat_kpi_configs_id',
            'bat_kpi_configs.bat_kpi_configs_name',
            'bat_kpi_configs.kpi_config_code',
            'bat_kpi_configs.config_month',
            'bat_kpi_configs.bat_company_id',
            'bat_company.company_name',

            DB::raw("
                    (SELECT GROUP_CONCAT( bat_kpis.bat_kpi_name ) FROM bat_kpis, bat_kpi_config_details  WHERE bat_kpis.bat_kpi_id = bat_kpi_config_details.bat_kpi_id AND bat_kpi_config_details.kpi_config_code = bat_kpi_configs.kpi_config_code ) AS kpi_name,
                    ( SELECT GROUP_CONCAT( designations_name ) FROM designations WHERE FIND_IN_SET( designations.designations_id, bat_kpi_configs.selected_ff_type ) ) AS designation
                ")
        )
            ->join('bat_distributorspoint','bat_distributorspoint.id','=','bat_kpi_configs.bat_dpid' )
            ->join('bat_company','bat_company.bat_company_id','=','bat_kpi_configs.bat_company_id');


        if($type=='point'){
                $kpi_config_sql->addSelect('bat_distributorspoint.name as point_name');
                $kpi_config_sql->addSelect('bat_distributorspoint.id as dpid');
            $kpi_config = $kpi_config_sql->where('bat_kpi_configs.bat_kpi_configs_id',$id)->first();

        }elseif ($type=='house'){
            $kpi_config_sql->addSelect(DB::raw('group_concat(bat_distributorspoint.name separator ", ") as point_name'));
            $kpi_config_sql->addSelect(DB::raw('group_concat(bat_distributorspoint.id separator ",") as point_ids'));
            $kpi_config_sql->where('bat_kpi_configs.bat_company_id',$location_id);
            $kpi_config = $kpi_config_sql->where('bat_kpi_configs.kpi_config_code',$id)->first();
        }

        $kpi_config_details=DB::table('bat_kpi_config_details')
            ->selectRaw('
                bat_kpi_config_details.bat_kpi_config_details_id,  
                bat_kpi_config_details.bat_kpi_id,  
                bat_kpis.bat_kpi_name,
                bat_kpi_config_details.weight,
                bat_kpi_config_details.uploaded_file,
                ( SELECT GROUP_CONCAT( bat_products.`name` ) FROM bat_products WHERE FIND_IN_SET( bat_products.products_id, bat_kpi_config_details.target_brands ) ) AS target_product,
                ( SELECT GROUP_CONCAT( bat_cats.slug ) FROM bat_cats WHERE FIND_IN_SET( bat_cats.id, bat_kpi_config_details.target_familys ) AND parent = 168 ) AS target_family,
                ( SELECT GROUP_CONCAT( bat_cats.slug ) FROM bat_cats WHERE FIND_IN_SET( bat_cats.id, bat_kpi_config_details.target_segments ) AND parent = 1 ) AS target_segments 
           ')
            ->join('bat_kpis','bat_kpi_config_details.bat_kpi_id','=','bat_kpis.bat_kpi_id')
            ->where('bat_kpi_configs_id',$id)
            ->get();
        $data['kpi_config']=$kpi_config;
        $data['kpi_config_details']=$kpi_config_details;
        $data['list_type'] = $type;
       // dd($data);
        return view('HR.kpi.kpi_config_details',$data);
    }

    public function downloadKpiDetail(Request $request){
        $kpi_config_detail_id=$request->kpi_config_detail_id;
        $location_id=$request->location_id;
        $type=$request->type;
        $spreadsheet = new Spreadsheet();
        if($type=='house'){
            $location_id=explode(',',$location_id);
        }

        //====query===
        $kpi_config_details=DB::table('bat_kpi_config_details')
            ->selectRaw('
                            bat_kpi_config_details.bat_kpi_config_details_id,
                            bat_kpi_config_details.kpi_config_code,    
                            bat_kpis.bat_kpi_name,
                            bat_kpi_config_details.bat_kpi_id,
                            bat_kpi_config_details.weight,
                            ( SELECT GROUP_CONCAT( bat_products.`name` ) FROM bat_products WHERE FIND_IN_SET( bat_products.products_id, bat_kpi_config_details.target_brands ) ) AS target_product,
                            ( SELECT GROUP_CONCAT( bat_cats.slug ) FROM bat_cats WHERE FIND_IN_SET( bat_cats.id, bat_kpi_config_details.target_familys ) AND parent = 168 ) AS target_family,
                            ( SELECT GROUP_CONCAT( bat_cats.slug ) FROM bat_cats WHERE FIND_IN_SET( bat_cats.id, bat_kpi_config_details.target_segments ) AND parent = 1 ) AS target_segments 
                       ')
            ->join('bat_kpis','bat_kpi_config_details.bat_kpi_id','=','bat_kpis.bat_kpi_id')
            ->where('bat_kpi_config_details_id',$kpi_config_detail_id)
            ->first();

        $kpi_config_sql=DB::table('bat_kpi_configs')
            ->where('kpi_config_code',$kpi_config_details->kpi_config_code);
         if($type=='point'){
             $kpi_config=$kpi_config_sql->where('bat_dpid',$location_id)->first();
         }elseif ($type=='house'){
             $kpi_config=$kpi_config_sql->where('bat_dpid',$location_id[0])->first();
         }


        $designations=DB::table('designations')
            ->selectRaw('designations_id,
                                designations_name')
            ->whereRaw('FIND_IN_SET(designations_id,"'.$kpi_config->selected_ff_type.'")')->get();


        $locations_sql=DB::table('sys_users')
            ->selectRaw('region.id AS region_id,
                    region.slug AS region,
                    area.id AS area_id,
                    area.slug AS area,
                    bat_company.bat_company_id ,
                    bat_company.company_name,
                    territory.id as territory_id,
                    territory.slug as territory_name,
                    designations.designations_id as designation_id,
                    designations.designations_name as designation_name,
                    bat_distributorspoint.id as point_id,
                    bat_distributorspoint.`name` as point_name,
                    sys_users.`name` as user_name,
                    sys_users.user_code,
                    sys_users.route_number')
            ->join('bat_distributorspoint','sys_users.bat_dpid','=','bat_distributorspoint.id')
            ->join('designations','sys_users.designations_id','=','designations.designations_id')
            ->join('bat_locations as region','region.id','=','bat_distributorspoint.region')
            ->join('bat_locations as area','area.id','=','bat_distributorspoint.area')
            ->join('bat_locations as territory','territory.id','=','bat_distributorspoint.territory')
            ->join('bat_company','bat_distributorspoint.dsid','=','bat_company.bat_company_id');

        if($type=='point') {
           $locations=$locations_sql->
            where('bat_distributorspoint.id', $location_id)
                ->whereRaw('FIND_IN_SET(designations.designations_id,"' . $kpi_config->selected_ff_type . '")')
                ->get();
        }elseif ($type=='house'){
            $locations=$locations_sql->
            whereIn('bat_distributorspoint.id', $location_id)
                ->whereRaw('FIND_IN_SET(designations.designations_id,"' . $kpi_config->selected_ff_type . '")')
                ->get();
        }


        $designation_wise_array=array();

        foreach ($locations as $loc){
            if(!isset($designation_wise_array[$loc->designation_id])){

                $designation_wise_array[$loc->designation_id]=array();
            }

            $temp=array(
                'region'=>$loc->region,
                'area'=>$loc->area,
                'company_name'=>$loc->company_name,
                'territory_name'=>$loc->territory_name,
                'point_name'=>$loc->point_name,
                'user_name'=> $loc->user_name,
                'user_code'=>$loc->user_code,
                'route_number'=>$loc->route_number
            );

            array_push($designation_wise_array[$loc->designation_id],$temp);

        }
        //dd($designation_wise_array);
        $user_id=$request->session()->get('USER_ID');
        $user_info=DB::table('sys_users')->where('id',$user_id)->first();


        $brand_array= !empty($kpi_config_details->target_product)?explode(",",$kpi_config_details->target_product):null;
        $family_array=!empty($kpi_config_details->target_family)?explode(",",$kpi_config_details->target_family):null;
        $segment_array=!empty($kpi_config_details->target_segments)?explode(",",$kpi_config_details->target_segments):null;



        $style_bold = array(
            'font' => array('bold' => true
            )
        );
        $font_bold = array(
            'font' => array(
                'size' => 10,
                'bold' => true

            )

        );
        $font_size = array(
            'font' => array(
                'size' => 9
            )
        );

//        $style_fill_header_cell = array(
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                'color' => array('rgb' => 'ededed')
//            ),
//            'font' => array(
//                'size' => 9,
//                'bold' => true
//
//            )
//
//        );

        $s=0;
        foreach ($designations as $ff) {
            $designation_id = $ff->designations_id;

            if (isset($designation_wise_array[$designation_id]) ) {
                $sheet = $ff->designations_name;


                if ($s == 0) {

                    $$sheet = $spreadsheet->getActiveSheet();
                    $$sheet->setTitle($sheet);
                    $s++;
                } else {
                    $$sheet = $spreadsheet->createSheet();
                    $$sheet->setTitle($sheet);
                }

                $$sheet->setCellValueByColumnAndRow(3, 1, 'Kpi Config Name: ' . $kpi_config->bat_kpi_configs_name);
                $$sheet->mergeCellsByColumnAndRow(3, 1, 5, 1);
                $$sheet->getStyleByColumnAndRow(3, 1)->applyFromArray($font_bold);

                //$$sheet->setCellValueByColumnAndRow(5, 1, $kpi_config->bat_kpi_configs_id);


                $$sheet->setCellValueByColumnAndRow(3, 2, 'Kpi Config Code: ' . $kpi_config->kpi_config_code);
                $$sheet->mergeCellsByColumnAndRow(3, 2, 5, 2);
                $$sheet->getStyleByColumnAndRow(3, 2)->applyFromArray($font_bold);

                //$$sheet->setCellValueByColumnAndRow(5, 2, $kpi_config->kpi_config_code);

                $$sheet->setCellValueByColumnAndRow(3, 3, 'Config Month: ' . $kpi_config->config_month);
                $$sheet->mergeCellsByColumnAndRow(3, 3, 5, 3);
                $$sheet->getStyleByColumnAndRow(3, 3)->applyFromArray($font_bold);
               // $$sheet->setCellValueByColumnAndRow(5, 3, $kpi_config->config_month);

                $$sheet->setCellValueByColumnAndRow(3, 4, 'Kpi Name: ' . $kpi_config_details->bat_kpi_name);
                $$sheet->mergeCellsByColumnAndRow(3, 4, 5, 4);
                $$sheet->getStyleByColumnAndRow(3, 4)->applyFromArray($font_bold);
               // $$sheet->setCellValueByColumnAndRow(5, 4,  $kpi_config_details->bat_kpi_id);

                $$sheet->setCellValueByColumnAndRow(3, 5, 'Please Only Change those Fields that You are Allowed');
                $$sheet->mergeCellsByColumnAndRow(3, 5, 5, 6);
                $$sheet->getStyleByColumnAndRow(3, 5)->applyFromArray($font_bold);
                $$sheet->setCellValueByColumnAndRow(1,7,$kpi_config->bat_kpi_configs_id.','.$kpi_config->kpi_config_code.','.$kpi_config->config_month.','.$kpi_config_details->bat_kpi_id);
                $col = 3;
                $row = 7;
                $header_array = ['Region', 'Area', 'Distribution House', 'Territory', 'Point', 'FF Name', 'User Code', 'Route No.'];
                $j = 0;

                foreach ($header_array as $ha) {
                    $$sheet->setCellValueByColumnAndRow($col, $row, $ha);
                    $$sheet->mergeCellsByColumnAndRow($col, $row, $col, $row + 1);
                    $$sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font_bold)
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ededed');
                    $$sheet->getStyleByColumnAndRow($col, $row)
                        ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

                    $$sheet->getStyleByColumnAndRow($col, $row + 1)
                        ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                    if ($j == 0) {
                        $$sheet->getStyleByColumnAndRow($col, $row)
                            ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $$sheet->getStyleByColumnAndRow($col, $row + 1)
                            ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $j++;
                    }

                    $col++;
                }

                if ($brand_array != null) {
                    $$sheet->setCellValueByColumnAndRow($col, $row, 'Brand');
                    $$sheet->mergeCellsByColumnAndRow($col, $row, $col + count($brand_array) - 1, $row);
                    $$sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font_bold)
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cacaca');
                    $add_col = 0;
                    $j = 0;
                    foreach ($brand_array as $ba) {
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row + 1, $ba);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)->applyFromArray($font_bold)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('86c5da');
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)
                            ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        if ($j == 0) {
                            $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                            $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)
                                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                            $j++;
                        }
                        $add_col++;
                    }


                    $col += count($brand_array);

                }

                if ($family_array != null) {
                    $$sheet->setCellValueByColumnAndRow($col, $row, 'Family');
                    $$sheet->mergeCellsByColumnAndRow($col, $row, $col + count($family_array) - 1, $row);
                    $$sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font_bold)
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cacaca');
                    $add_col = 0;
                    $j = 0;
                    foreach ($family_array as $fa) {
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row + 1, $fa);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)->applyFromArray($font_bold)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('86c5da');
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)
                            ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        if ($j == 0) {
                            $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                            $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)
                                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                            $j++;
                        }
                        $add_col++;
                    }

                    $col += count($family_array);

                }


                if ($segment_array != null) {
                    $$sheet->setCellValueByColumnAndRow($col, $row, 'Segment');
                    $$sheet->mergeCellsByColumnAndRow($col, $row, $col + count($segment_array) - 1, $row);

                    $$sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font_bold)
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cacaca');
                    $add_col = 0;
                    $j = 0;
                    foreach ($segment_array as $sa) {
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row + 1, $sa);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)->applyFromArray($font_bold)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('86c5da');
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)
                            ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        if ($j == 0) {
                            $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                            $$sheet->getStyleByColumnAndRow($col + $add_col, $row + 1)
                                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                            $j++;
                        }
                        $add_col++;
                    }

                    $col += count($segment_array);
                }

                $row+=2;
                $col = 3;
                if (isset($designation_wise_array[$designation_id]) && !empty($designation_wise_array[$designation_id])) {


                    foreach ($designation_wise_array[$designation_id] as $loc) {

                        $add_col = 0;
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['region']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;

                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['area']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['company_name']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['territory_name']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['point_name']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['user_name']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;
                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['user_code']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $add_col++;

                        $$sheet->setCellValueByColumnAndRow($col + $add_col, $row, $loc['route_number']);
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff55');
                        $$sheet->getStyleByColumnAndRow($col + $add_col, $row)
                            ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $add_col++;
                        $row++;
                    }

                }

                /* custom column dimension*/
                $$sheet->getColumnDimension('E')->setWidth(34);

                $$sheet->getColumnDimension('H')->setWidth(24);

                $sheet_col_string_high = $$sheet->getHighestColumn();
                $sheet_row_high = $$sheet->getHighestRow();
                $$sheet->getStyle("A1:{$sheet_col_string_high}{$sheet_row_high}")
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center')
                    ->setWrapText(true);

                $$sheet->getStyle("A1:{$sheet_col_string_high}{$sheet_row_high}")
                    ->applyFromArray($font_size);
                $$sheet->protectCells("B1:E6", "NoOneShallPass");
                $$sheet->getColumnDimension('A')->setVisible(false);
                $$sheet->protectCells("B7:{$sheet_col_string_high}8", 'NoOneShallPass');
                $$sheet->protectCells("B9:I{$sheet_row_high}", "NoOneShallPass");
                $$sheet->getProtection()->setSheet(true);

                $$sheet->getStyle("J9:{$sheet_col_string_high}{$sheet_row_high}")->getProtection()
                    ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            }
        }
        $current_date_time = Carbon::now()->timestamp;;
        $fileName='PfP Target'.$kpi_config_details->bat_kpi_name.$kpi_config->config_month.$user_info->user_code.$current_date_time.'.xlsx';
        //$fileName='PfP Target.xlsx';
        exportHelper::excelHeader($fileName, $spreadsheet);
        return response()->json(['status' => 'success', 'file' => $fileName]);
    }

    public function uploadKpiTarget(Request $request)
    {
        $type = $request->kpi_type;
        if ($type == 'point') {
            $kpi_configs_id = $request->kpi_configs_id;
            $kpi_config_code = $request->kpi_config_code;
        } elseif ($type == 'house') {
            $kpi_config_code = $request->kpi_config_code;
            $point_ids = $request->point_ids;
            $point_ids = explode(',', $point_ids);

            $kpi_config_id_by_kpi_config_code_and_point = DB::table('bat_kpi_configs')->where('kpi_config_code', $kpi_config_code)->whereIn('bat_dpid', $point_ids)->get();
            $kpi_config_id_by_kpi_config_code_and_point_array = array();
            foreach ($kpi_config_id_by_kpi_config_code_and_point as $kpi) {
                $kpi_config_id_by_kpi_config_code_and_point_array[$kpi->bat_dpid] = $kpi->bat_kpi_configs_id;
            }

        }


        $target_month = $request->target_month;
        $bat_kpi_id = $request->bat_kpi_id;
        $bat_kpi_config_details_id = $request->bat_kpi_config_details_id;

        ///  echo $bat_kpi_config_details_id; exit;

        $document = $request->file('file');
        $original_name = $document->getClientOriginalName();
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $month_year = date('M_Y');
        if (!is_dir(public_path("documents/kpi_target/" . $month_year))) {
            mkdir(public_path("documents/kpi_target/" . $month_year), 0777, true);
        }
        $document->move(public_path("documents/kpi_target/" . $month_year), $original_name);
        $now = Carbon::now('utc')->toDateTimeString();

        if ($file_extension == 'xlsx' || $file_extension == 'XLSX') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $path = public_path("documents/kpi_target/" . $month_year . "/") . $original_name;
            $spreadsheet = $reader->load($path);
            $sheetCount = $spreadsheet->getSheetCount();

            for($a=0;$a<$sheetCount;$a++){


            $spreadsheet->setActiveSheetIndex($a);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $validation_string=$sheetData[6][0];
            $validation_array=explode(',',$validation_string);

            $sheet_config_id=$validation_array[0];
            $sheet_config_code=$validation_array[1];
            $sheet_config_month=$validation_array[2];
            $sheet_kpi_id=$validation_array[3];
//            echo $kpi_configs_id.' '.$sheet_config_id;
//            echo $kpi_config_code.' '.$sheet_config_code;
//
//                echo $target_month.' '.$sheet_config_month;
//                echo $bat_kpi_id.' '.$sheet_kpi_id;exit;
            if($type=='point'){
                if($kpi_configs_id==$sheet_config_id && $kpi_config_code==$sheet_config_code && $target_month==$sheet_config_month && $bat_kpi_id==$sheet_kpi_id){

                }else{
                    return redirect()->back()->with('error', 'Wrong File Uploaded!');
                }
            }
       //echo '<pre>'; print_r($validation_string);exit;
            for ($i = 0; $i < 6; $i++) {
                unset($sheetData[$i]);
            }
            $search_array = ['Brand', 'Family', 'Segment'];

            end($sheetData[6]);
            $last_index = key($sheetData[6]);


            $product_table_array = [
                'Brand' => [
                    'table' => 'bat_products'
                ],
                'Family' => [
                    'table' => 'bat_cats',
                    'parent' => 168
                ],
                'Segment' => [
                    'table' => 'bat_cats',
                    'parent' => 1
                ]
            ];
            $product_categorys = array();
            foreach ($search_array as $key => $sa) {
                $pos = array_search($sa, $sheetData[6]);
                if ($pos != null) {
                    $product_categorys[$sa] = $pos;
                } else {
                    unset($search_array[$key]);
                }
            }
            $product_category_elements = array();
            $product_category_element_id_array = array();
            foreach ($search_array as $key => $sa) {
                $current_pos = $product_categorys[$sa];
                $position_to = isset($search_array[$key + 1]) ? $product_categorys[$search_array[$key + 1]] - 1 : $last_index;
                while ($current_pos <= $position_to) {
                    $product_category_elements[$sa][$current_pos] = $sheetData[7][$current_pos];
                    $current_pos++;
                }

                if ($sa == 'Brand') {
                    $table_name = $product_table_array[$sa]['table'];
                    $product_category_element_id = DB::table($table_name)->whereIn('name', $product_category_elements[$sa])->get();
                    foreach ($product_category_element_id as $pce) {
                        $category_position = array_search($pce->name, $product_category_elements[$sa]);
                        $product_category_element_id_array[$sa][$category_position] = $pce->products_id;
                    }

                } else {
                    $table_name = $product_table_array[$sa]['table'];
                    $parent = $product_table_array[$sa]['parent'];
                    $product_category_element_id = DB::table($table_name)->whereIn('slug', $product_category_elements[$sa])->where('parent', $parent)->get();
                    foreach ($product_category_element_id as $pce) {
                        $category_position = array_search($pce->slug, $product_category_elements[$sa]);
                        $product_category_element_id_array[$sa][$category_position] = $pce->id;
                    }
                }

            }

            $user_code_index = 8;
            unset($sheetData[6]);
            unset($sheetData[7]);

            $data_to_be_inserted_to_bat_kpi_target = array();
            $data_to_be_inserted_to_bat_kpi_target_detail = array();
            foreach ($sheetData as $data) {
                $user_code = $data[$user_code_index];
                $user_info = DB::table('sys_users')->where('user_code', $user_code)->first();
                if ($type == 'point') {
                    $temp_to_be_inserted_to_bat_kpi_target = array(
                        'bat_kpi_configs_id' => $kpi_configs_id,
                        'target_month' => $target_month,
                        'user_code' => $user_code,
                        'bat_company_id' => $user_info->bat_company_id,
                        'bat_dpid' => $user_info->bat_dpid,
                        'bat_kpi_id' => $bat_kpi_id,
                        'created_by' => $request->session()->get('USER_ID'),
                        'created_at' => $now
                    );
                    $data_to_be_inserted_to_bat_kpi_target[] = $temp_to_be_inserted_to_bat_kpi_target;
                } elseif ($type == 'house') {
                    $temp_to_be_inserted_to_bat_kpi_target = array(
                        'bat_kpi_configs_id' => $kpi_config_id_by_kpi_config_code_and_point_array[$user_info->bat_dpid],
                        'target_month' => $target_month,
                        'user_code' => $user_code,
                        'bat_company_id' => $user_info->bat_company_id,
                        'bat_dpid' => $user_info->bat_dpid,
                        'bat_kpi_id' => $bat_kpi_id,
                        'created_by' => $request->session()->get('USER_ID'),
                        'created_at' => $now
                    );
                    $data_to_be_inserted_to_bat_kpi_target[] = $temp_to_be_inserted_to_bat_kpi_target;
                }


                foreach ($search_array as $key => $sa) {
                    foreach ($product_category_element_id_array[$sa] as $k => $v) {
                        $target = $data[$k];
                        $temp_to_be_inserted_to_bat_kpi_target_detail = array(
                            'user_code' => $user_code,
                            'target_month' => $target_month,
                            'bat_kpi_id' => $bat_kpi_id,
                            'target_type' => $sa,
                            'target_ref_id' => $v,
                            'target_set' => $target,
                            'created_by' => $request->session()->get('USER_ID'),
                            'created_at' => $now
                        );
                        $data_to_be_inserted_to_bat_kpi_target_detail[] = $temp_to_be_inserted_to_bat_kpi_target_detail;
                    }

                }


            }

            $kpi_config_detail_data = array(
                'uploaded_file' => $original_name,
                'uploaded_by' => $request->session()->get('USER_ID'),
                'uploaded_at' => $now
            );

       DB::beginTransaction();
        try{
            DB::table('bat_kpi_target')->insert($data_to_be_inserted_to_bat_kpi_target);
            DB::table('bat_kpi_target_detail')->insert($data_to_be_inserted_to_bat_kpi_target_detail);
            DB::table('bat_kpi_config_details')->where('bat_kpi_config_details_id', $bat_kpi_config_details_id)->update($kpi_config_detail_data);
            DB::commit();
        }catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Targets Could Not BE Uploaded');
        }



        }

    }

        return redirect()->back()->with('success', 'Targets have Uploaded Successfully');
    }

    public function deleteKpiTarget(Request $request){

        $type = $request->kpi_type;

        if ($type == 'point') {
            $kpi_configs_id = $request->kpi_configs_id;
            $bat_dpid=$request->point_id;
        } elseif ($type == 'house') {
            $kpi_config_code = $request->kpi_config_code;
            $point_ids = $request->point_ids;
            $point_ids = explode(',', $point_ids);

            $kpi_config_id_by_kpi_config_code_and_point = DB::table('bat_kpi_configs')->selectRaw('DISTINCT bat_kpi_configs_id')->where('kpi_config_code', $kpi_config_code)->whereIn('bat_dpid', $point_ids)->get();

            $kpi_config_id_by_kpi_config_code_and_point_array = array();
            foreach ($kpi_config_id_by_kpi_config_code_and_point as $kpi) {
                $kpi_config_id_by_kpi_config_code_and_point_array[] = $kpi->bat_kpi_configs_id;
            }
           // dd($kpi_config_id_by_kpi_config_code_and_point_array);
        }

        $user_code_to_delete=array();
        $kpi_target_to_delete=array();
        $target_month = $request->target_month;
        $bat_kpi_id = $request->bat_kpi_id;
        $bat_kpi_config_details_id = $request->bat_kpi_config_details_id;

        DB::beginTransaction();
        if($type=='point'){

            try{

                $user_code_to_delete_sql=DB::table('bat_kpi_target')->where('bat_kpi_configs_id',$kpi_configs_id)->where('bat_dpid',$bat_dpid)
                    ->where('bat_kpi_id',$bat_kpi_id)->where('target_month',$target_month)->get();
             //   dd($user_code_to_delete_sql);
                foreach ($user_code_to_delete_sql as $uc_sql){
                  array_push($user_code_to_delete,$uc_sql->user_code);
                  array_push($kpi_target_to_delete,$uc_sql->bat_kpi_target_id);
                }

                DB::table('bat_kpi_target_detail')->where('bat_kpi_id',$bat_kpi_id)->where('target_month',$target_month)
                    ->whereIn('user_code',$user_code_to_delete)->delete();
                DB::table('bat_kpi_target')->whereIn('bat_kpi_target_id',$kpi_target_to_delete)->delete();
                $data_to_be_updated=array(
                    'uploaded_file'=>null,
                    'uploaded_by'=>null,
                    'uploaded_at'=>null
                );
                DB::table('bat_kpi_config_details')->where('bat_kpi_config_details_id',$bat_kpi_config_details_id)->update($data_to_be_updated);

                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
//                return redirect()->back()->with('error', 'Targets Could Not BE Deleted');
                $data['msg'] = 'Targets Could Not BE Deleted';
                $data['code'] = 500;
                return $data;
            }
        }elseif($type=='house'){

            try{
                $user_code_to_delete_sql=DB::table('bat_kpi_target')->whereIn('bat_kpi_configs_id',$kpi_config_id_by_kpi_config_code_and_point_array)->whereIn('bat_dpid',$point_ids)
                    ->where('bat_kpi_id',$bat_kpi_id)->where('target_month',$target_month)->get();
               // dd($user_code_to_delete_sql);
                foreach ($user_code_to_delete_sql as $uc_sql){
                    array_push($user_code_to_delete,$uc_sql->user_code);
                    array_push($kpi_target_to_delete,$uc_sql->bat_kpi_target_id);
                }

                DB::table('bat_kpi_target_detail')->where('bat_kpi_id',$bat_kpi_id)->where('target_month',$target_month)
                    ->whereIn('user_code',$user_code_to_delete)->delete();
                DB::table('bat_kpi_target')->whereIn('bat_kpi_target_id',$kpi_target_to_delete)->delete();
                $data_to_be_updated=array(
                    'uploaded_file'=>null,
                    'uploaded_by'=>null,
                    'uploaded_at'=>null
                );
                DB::table('bat_kpi_config_details')->where('bat_kpi_config_details_id',$bat_kpi_config_details_id)->update($data_to_be_updated);


                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();

                $data['msg'] = 'Targets Could Not BE Deleted';
                $data['code'] = 500;
                return $data;
            }
        }

//        return redirect()->back()->with('success', 'Targets have Deleted Successfully');
        $data['msg'] = 'Targets have Deleted Successfully';
        $data['code'] = 200;
        return $data;
    }
}
