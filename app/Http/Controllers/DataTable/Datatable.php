<?php
namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\URL;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Helper\ExportHelper;

class Datatable extends Controller{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function dataTableSubmit(Request $request){
        //debug($request->all(),1);
        $grid_function = $request->input('grid_function');
        $limit = $request->input('length');
        $start = $request->input('start');
        $order_column = $request->input('order.0.column');
        $dir = strtoupper($request->input('order.0.dir'));
        $search = $request->input('search.value');
        $custom_search = !empty($request->input('custom_search')) ? $request->input('custom_search') : '';
        $condition_data = array(
            'limit' => $limit,
            'start' => $start,
            'order_column' => $order_column,
            'dir' => $dir,
            'search' => $search,
            'custom_search' => $custom_search
        );
        $req_data = $this->$grid_function($condition_data);
        //debug($req_data,1);
        //extract($req_data);
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($req_data['totalData']),
            "recordsFiltered" => intval($req_data['totalFiltered']),
            "data" => $req_data['grid_data']
        );
        echo json_encode($json_data);
    }
}
