<?php
namespace App\Http\Controllers\Dropdown_grid;

use App\Http\Controllers\Controller;
use App\Models\Master\delivery_van;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;

class Dropdown_grid extends Controller{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function dropdown_index(){
        return view('dropdown_grid.dropdown_index');
    }
    public function dropdown_grid_view(Request $request){
        $slug = $request->slug;
        $selected_value = $request->selected_value;
        $addbuttonid = $request->addbuttonid;
        $multiple = $request->multiple;
        $dependent_data = json_encode($request->dependent_data);
        $dropdown = DB::table('sys_dropdowns')->where('dropdown_slug','=',$slug)->first();
        if(empty($dropdown)){
            return  response()->json(array('status'=>'0','message'=>'No DataGrid Found'));
        }
        $option_field = explode('.',$dropdown->option_field);
        $option_field = sizeof($option_field)>1?$option_field[1]:$option_field[0];
        $value_field = explode('.',$dropdown->value_field);
        $value_field = sizeof($value_field)>1?$value_field[1]:$value_field[0];
        $sqltext = $dropdown->sqltext;
        $sqlsource = $dropdown->sqlsource;
        $sqlcondition = $dropdown->sqlcondition;

        $sql_data = (array)$dropdown;
        $sqlquery = self::sqlQueryConcat($sql_data);

        $query =  DB::select($sqlquery);
        $header_data['last'] = [];
        if(!empty($query)){
            $columns = array_keys((array)$query[0]);
            foreach ($columns as $key){
                if($key == $value_field){
                    $key = '#';
                }
                array_push($header_data['last'],array('column'=>$key));
            }
        }
        $selected_data = '';
        if(!empty($selected_value)){
            $con = '';
            $selected_value = explode(',',$selected_value);
            $selected_value = array_map(function($v){return "'".trim($v,"'")."'";},$selected_value);
            $selected_value = implode(',',$selected_value);
            $con .= " AND $dropdown->value_field IN ($selected_value)";
            if(trim($con)!=''){
                $con = trim(trim($con),'AND');
                $sqlcondition = $sqlcondition.' AND '.$con;
                $sqlquery = $sqltext.' '.$sqlsource.' '.$sqlcondition;
            }
            $selected_data = DB::select($sqlquery);
        }
        $data['page_data'] = array(
            'page_title' => $dropdown->description,
            'grid_title' => 'grid title',
            'custom_search' => $dropdown->sys_search_panel_slug,
            'custom_search_default_show' => 1,
            'header' => $header_data,
            'slug' => $slug,
            'value_field'=>$value_field,
            'option_field'=>$option_field,
            'selected_value' => $selected_value,
            'addbuttonid' => $addbuttonid,
            'multiple' => $multiple,
            'dependent_data' => $dependent_data,
            'selected_data' => $selected_data
        );
        return view('dropdown_grid.dropdown_modal_view', $data);
    }
    public function get_dropdown_grid_ajax_data(Request $request){
        $req_data = (array)$request->all();
//        dd($req_data);
        /************* Mendatory table field which are in table column *****************/
        $slug = $request->slug;
        $dropdown = DB::table('sys_dropdowns')->where('dropdown_slug','=',$slug)->first();
//        dd($dropdown);
        $sqltext = $dropdown->sqltext;
        $sqlsource = $dropdown->sqlsource;
        $sqlgroupby = $dropdown->sqlgroupby;
        $sqlorderby = $dropdown->sqlorderby;
        $sqlcondition = $dropdown->sqlcondition;

        $sqlcondition .= sessionFilter('sys_dropdowns', $slug, $sqlcondition);
        $sql_data = (array)$dropdown;
        $sqlquery = self::sqlQueryConcat($sql_data);
        $query =  DB::select($sqlquery);
        $table_header = array_keys((array)$query[0]);
        $start = $req_data['start']; // Mendatory value
        $limit = $req_data['length']; // Mendatory value
        $order = isset($req_data['order'][0]['column'])?$req_data['order'][0]['column']:0; // Mendatory value order by
        $sort = isset($req_data['order'][0]['dir'])?$req_data['order'][0]['dir']:'ASC'; // Mendatory value asc/desc

        /*****************************************/
        $value_field = explode('.',$dropdown->value_field);
        $value_field = sizeof($value_field)>1?$value_field[1]:$value_field[0];

        $req_data['sqltext'] = $sqltext;
        $req_data['sqlsource'] = $sqlsource;
        $req_data['sqlcondition'] = $sqlcondition;
        $req_data['sqlgroupby'] = $sqlgroupby;
        $req_data['sqlorderby'] = $sqlorderby;
        $req_data['value_field'] = $dropdown->value_field;
        $req_data['option_field'] = $dropdown->option_field;
        $req_data['search_columns'] = $dropdown->search_columns;
        $datatable_condition = self::dropdown_query_generate($req_data);
        $where_condition = $datatable_condition['where_con'];
        $having_condition = $datatable_condition['having_con'];
        $req_data['where_condition'] = $where_condition;
        $req_data['having_condition'] = $having_condition;
        $datatable_query = self::sqlQueryConcat($req_data);
//        debug($datatable_query);
        $table_data = self::dropdown_query_data($table_header, $req_data, $start, $limit, $order, $sort);
        $rows = [];
        $option_field = explode('.',$dropdown->option_field);
        $option_field = sizeof($option_field)>1?$option_field[1]:$option_field[0];
        foreach ($table_data as $item) {
            $row = [];
            foreach ($item as $name=>$each) {
                if ($name == $value_field) {
                    if(@$req_data['multiple'] && $req_data['multiple']=='YES'){
                        $val = "<input type='checkbox' id='".$each."' class='grid-item-selection ".$each."' value='" . $each . "' name='selected_values'>";
                    }elseif($req_data['multiple']=='NO'){
                        $val = "<input type='radio' class='grid-item-selection ".$each." ".$req_data['addbuttonid']."' value='" . $each . "' name='selected_values[]'>";
                    }else{
                        if ($dropdown->multiple) {
                            $val = "<input type='checkbox' id='".$each."' class='grid-item-selection ".$each."' value='" . $each . "' name='selected_values'>";
                        } else {
                            $val = "<input type='radio' class='grid-item-selection ".$each." ".$req_data['addbuttonid']."' value='" . $each . "' name='selected_values[]'>";
                        }
                    }
                } else if ($name == $option_field) {
                    $val = "<span class='option-field'>" . $each . "</span>";
                } else if(strpos($name,'_date')){
                    $val = toDated($each);
                }else if(strpos($name,'_price')||strpos($name,'_amount')||strpos($name,'_qty')){
                    $val = "<span class='number-format'>".datatable_moneyFormat($each)."</span>";
                }else{
                    $val = $each;
                }
                $row[] = $val;
            }
            $rows[] = $row;
        }
        $total_rows = self::dropdown_row_count($datatable_query);
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $total_rows,
            "recordsFiltered" => $total_rows,
            "data" => $rows,
        );
        return json_encode($output);
    }
    // generate query for datatable
    private function dropdown_query_generate($req_data){
        $sqlcondition = $req_data['sqlcondition'];
        $custom_search_con = [];
        $custom_search_con['WH'] = [];
        $table_search = $req_data['search']['value'];
        $search_columns = $req_data['search_columns']?explode(',',$req_data['search_columns']):explode(',',$req_data['option_field']);
        $search_columns = array_filter($search_columns);
        $custom_search = isset($req_data['search_data'])?$req_data['search_data']:'';
        $selected_items = $req_data['selected_items'];
        $custom_search_con = self::custom_search($custom_search);
        $dependent_search_con = self::dependent_search($req_data['dependent_data']);

        $custom_search_con_hv = $custom_search_con_wh = [];
        if(!empty($custom_search_con)){
            if(isset($custom_search_con['WH'])){
                $custom_search_con['WH'] = $custom_search_con['WH'] + $dependent_search_con;
            }

        }else{
            $custom_search_con['WH'] = $dependent_search_con;
        }

//         dd($custom_search_con_hv);
        // this exception only for category wise products list
        $exceptional_case = [];
        $wherecon = $havingcon = '';
        $between_case_wh = [];
        if(isset($custom_search_con['WH'])){
            $custom_search_con_wh = $custom_search_con['WH'];
            foreach( $custom_search_con_wh as $key_name => $value ) {

                $exceptional_keys = array('_start','_end','_condition');
                foreach($exceptional_keys as $key){
                    if(strpos($key_name,$key)){
                        $item_key = substr($key_name,0,strpos($key_name,$key));
                        $between_case_wh[$item_key][trim($key,'_')] = $value;
                        unset($custom_search_con_wh[$key_name]);
                    }
                }
                if(strpos($key_name,'notable.') === 0){
                    $item_key = trim($key_name,'notable.');
                    $exceptional_case[$item_key] = $value;
                    unset($custom_search_con_wh[$key_name]);
                }
            }
//            dd($exceptional_case);
            $wherecon .= self::between_condition($between_case_wh);
        }
        $between_case_hv = [];
        if(isset($custom_search_con['HV'])){
            $custom_search_con_hv = $custom_search_con['HV'];
            foreach( $custom_search_con_hv as $key_name => $value ) {
                $exceptional_keys = array('_start','_end','_condition','rangetype-');
                foreach($exceptional_keys as $key){
                    if(strpos($key_name,$key)){
                        $item_key = substr($key_name,0,strpos($key_name,$key));
                        $between_case_hv[$item_key][trim($key,'_')] = $value;
                        unset($custom_search_con_hv[$key_name]);
                    }
                }
            }
            $havingcon .= self::between_condition($between_case_hv);
        }

        if(!empty($custom_search_con)){
            $wherecon .= self::custom_search_formatter($custom_search_con_wh);
            $havingcon .= self::custom_search_formatter($custom_search_con_hv);
        }

        if(!empty($table_search) && !empty($search_columns)) {
            foreach ($search_columns as $i => $column) {
                if ($i == 0) {
                    $wherecon .= " AND ($column LIKE '%$table_search%'";
                } else {
                    $wherecon .= " OR $column LIKE '%$table_search%'";
                }
            }
            $wherecon .= ')';
        }

        if(!empty($exceptional_case)){
            $exceptional_con = self::exceptional_data_prepare($exceptional_case);
            $wherecon .= $exceptional_con;
        }

        if(!empty($selected_items)){
            $value_field = $req_data['value_field'];
            $wherecon .= " AND $value_field NOT IN ($selected_items)";
        }
        if(trim($wherecon)!=''){
            $wherecon = trim(trim($wherecon),'AND');
            $sqlcondition = $sqlcondition.' AND '.$wherecon;
        }
        if(trim($havingcon)!=''){
            $havingcon = trim(trim($havingcon),'AND');
            $havingcon = ' HAVING '.$havingcon;
        }
        $conditions = array(
            'where_con'=>$sqlcondition,
            'having_con'=>$havingcon,
        );
//        dd($conditions);
        return $conditions;
    }
    private function dropdown_row_count($sqlquery){
        return count(DB::select($sqlquery));
    }
    private function dropdown_query_data($table_header,$data,$start=0,$limit=10,$order=0,$sort='ASC'){
        $order_by = $table_header[$order];
        extract($data);
        $sqltext = $data['sqltext']?$data['sqltext']:'';
        $sqlsource = $data['sqlsource']?$data['sqlsource']:'';
        $sqlorderby = $data['sqlorderby']?$data['sqlorderby']:'';
        $sqlgroupby = $data['sqlgroupby']?$data['sqlgroupby']:'';
        $sqlcondition = $data['sqlcondition']?$data['sqlcondition']:'';
        $having_condition = isset($data['having_condition'])?$data['having_condition']:'';
        $where_condition = isset($data['where_condition'])?$data['where_condition']:$sqlcondition;
        $sqlorderby = $order_by?' ORDER BY '.$order_by:$sqlorderby;
        $sql_query = $sqltext.' '.$sqlsource.' '.$where_condition.' '.$sqlgroupby.' '.$having_condition;

        $q = DB::select($sql_query.' '.$sqlorderby.' '.$sort.' LIMIT '.$start.', '.$limit);

        return $q;
    }

    /*************************************************************************/
    public function sqlQueryConcat($data){
        extract($data);
        $sqltext = $data['sqltext'] ? $data['sqltext'] : '';
        $sqlsource = $data['sqlsource'] ? $data['sqlsource'] : '';
        $sqlcondition = $data['sqlcondition'] ? $data['sqlcondition'] : '';
        $sqlgroupby = $data['sqlgroupby'] ? $data['sqlgroupby'] : '';
        $having_condition = isset($data['having_condition']) ? $data['having_condition'] : '';
        $where_condition = isset($data['where_condition']) ? $data['where_condition'] : $sqlcondition;
        $sql_query = $sqltext.' '.$sqlsource.' '.$where_condition.' '.$sqlgroupby.' '.$having_condition;
        return $sql_query;
    }
    private function custom_search($custom_search){
        $searchArray = array();
        if(!empty($custom_search)){
            foreach ($custom_search as $cust_src) {
                if(!empty($cust_src['value'])){
                    $cust_src_type = trim(substr($cust_src['name'], 0, 3), '-');
                    $name = trim(substr($cust_src['name'], 3));

                    if(strpos($cust_src['name'], '[]') == true){
                        str_replace($name, '[]', '');
                    }
                    $searchArray[$cust_src_type][$name][] = $cust_src['value'];
                }
            }
        }
        return $searchArray;
    }
    private function dependent_search($search_data){
        $searchArray = array();
        if(!empty($search_data)){
            foreach ($search_data as $cust_src) {
                if(!empty($cust_src['value']) && is_array($cust_src['value'])){

                    $searchArray[$cust_src['dbcolumn']] = $cust_src['value'];
                }elseif(!empty($cust_src['value'])){
                    $searchArray[$cust_src['dbcolumn']][] = $cust_src['value'];
                }
            }
        }
        return $searchArray;
    }
    private function exceptional_data_prepare($exceptional_data){
        $search_con = ' ';
        if(!empty($exceptional_data)) {
            foreach ($exceptional_data as $key=>$data) {
                if(strpos($key,'product_category_id')>=0){
                    $search_con_sub = ' ';
                    foreach ($data as $id){
                        if($id == 1){
                            $search_con_sub .= " OR products.is_rawmaterial=1";
                        }
                        if($id == 2){
                            $search_con_sub .= " OR products.is_finish_goods=1";
                        }
                        if($id == 3){
                            $search_con_sub .= " OR products.is_storeitem=1";
                        }
                        if($id == 5){
                            $search_con_sub .= " OR products.is_service=1";
                        }
                        if($id == 6){
                            $search_con_sub .= " OR products.is_salable=1";
                        }
                    }
                    $search_con_sub = str_replace_first('OR','',trim($search_con_sub));
                    $search_con .= ' AND ('.$search_con_sub.')';
                }

            }
            return $search_con;
        }
    }

    function custom_search_formatter($custom_search_con){
        $con = '';
        foreach ($custom_search_con as $name=>$value) {
            $cust_src_con = trim(substr($name, 0, 3), '-');
            $col_name = substr($name, 3);
            $col_name = trim($col_name, '[]');
            if(sizeof($value) > 1 || $cust_src_con == 'IN'){
                $value = "'".implode("','" , $value)."'";
                $con .= " AND $col_name IN ($value)";
            }elseif($cust_src_con == 'DR'){
                $value = implode("','" , $value);
                $date_range = explode(' - ', $value);
                if(sizeof($date_range) > 1){
                    $date1 = date('Y-m-d',strtotime($date_range[0]));
                    $date2 = date('Y-m-d',strtotime($date_range[1]));
                    $con .= " AND $col_name BETWEEN '$date1' AND '$date2'";
                }
            }elseif($cust_src_con == 'LK'){
                $value = implode("','" , $value);
                $con .= " AND $col_name LIKE "."'%$value%'";
            }elseif($cust_src_con == 'EQ'){
                $value = "'".implode("','" , $value)."'";
                $con .= " AND $col_name = $value";
            }elseif($cust_src_con == 'RG'){
                $value = "'".implode("','" , $value)."'";
                $con .= " AND $col_name = $value";
            }else{
                $value = "'".implode("','" , $value)."'";
                $con .= " AND $name IN ($value)";
            }

        }
        return $con;
    }

    function between_condition($data){
        $between_condition = '';
        foreach($data as $key =>$item){
            $col_name = substr($key,3);
            $condition = isset($item['condition'][0])?$item['condition'][0]:'';
            $start = isset($item['start'][0])?$item['start'][0]:'';
            $end = isset($item['end'][0])?$item['end'][0]:'';
            if($condition == 'between' && $end != ''){
                $between_condition .= " AND $col_name BETWEEN $start AND $end ";
            }elseif($end != ''){
                $between_condition .= " AND $col_name $condition $end ";
            }

        }
        return $between_condition;
    }
}
