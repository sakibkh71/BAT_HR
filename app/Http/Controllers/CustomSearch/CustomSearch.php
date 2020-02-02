<?php
namespace App\Http\Controllers\CustomSearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Auth;
use Response;
use View;
use Session;

class CustomSearch extends Controller{
    private $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function implement(){
        return view('custom_search.implement', $this->data);
    }
    public function implementPost(Request $request){
        $this->data['posted'] = $request->all();
//        debug($this->data);
        return view('custom_search.implement', $this->data);
    }
    public function sessionSearchFilter(Request $request){
        $prev_sess = Session::get('SESSION_FILTER');
        $post = $request->all();
        $slug = $post['slug'];
        $arr = [];
        if(isset($post['data']) && !empty($post['data'])){
            foreach ($post['data'] as $filtered){
                $arr[] = $filtered[0];
            }
        }
        $prev_sess[$slug] = $arr;
        Session::forget('SESSION_FILTER');
        Session::put('SESSION_FILTER', $prev_sess);
    }

    public function fetchSearchForm($slug = "", $searched_value = [], $prefix = false){
        $search_panel = self::getSearchPanelInfo($slug);
        $this->data['session_filter'] = $search_panel->default_search_by;
        $this->data['prefix'] = $prefix;
        $query = DB::table('sys_search_panel_details');
        $query->select("*");
        $query->where('search_panel_slug', $slug);
        $query->where('status', 'Active');
        $query->orderBy('sorting', 'ASC');
        $this->data['search_fields'] = $query->get()->toArray();
        $this->data['search_slug'] = $slug;

        foreach ($this->data['search_fields'] as $search_fields){
            $this->data['search_options'][$search_fields->sys_search_panel_details_id] = $search_fields->label_name;
        }

        $this->data['searched_value'] = $searched_value;
        $session_filter = Session::get('SESSION_FILTER');

        if(isset($session_filter[$slug]) && !empty($session_filter[$slug])){
            $this->data['session_filter'] = implode(',', $session_filter[$slug]);
        }
        return View::make('Custom_search.custom_search_panel', $this->data)->render();
    }

    public function getSearchPanelInfo($slug){
        $query = DB::table('sys_search_panel');
        $query->select("*");
        $query->where('search_panel_slug', $slug);
        return $query->first();
    }
    public function getAutocompleteQuery($mode = 'search', $master_entry_details_id = '', $id = ''){
        if(!empty($master_entry_details_id)){
            $query = DB::table('sys_search_panel_details')->select("autocomplete_query")->where('sys_search_panel_details_id', $master_entry_details_id)->first()->autocomplete_query;
            //Query format must be
            //SELECT products_id AS data_value, products_name AS data_option FROM products WHERE products_name LIKE %SEARCH_KEY%
            if($mode == 'search'){
                $query_unit = $_GET['query'];
                $dropdowndata['query'] = $query_unit;
                $dropdowndata['suggestions'] = [];
                $format_query = str_replace('SEARCH_KEY', $query_unit, $query);
                $results = DB::select(DB::raw($format_query));
                foreach ($results as $key => $result){
                    $dropdowndata['suggestions'][$key]['value'] = $result->data_option; // string
                    $dropdowndata['suggestions'][$key]['data'] = $result->data_value; // any
                }
                return response()->json($dropdowndata);
            }else{
                $results = [];
                if(!empty($id)){
                    $query_arr = explode('where', strtolower($query));
                    $condition = "having data_value = ".$id."";
                    $format_query = $query_arr[0].$condition;
                    //DB::enableQueryLog();
                    $results = DB::select(DB::raw($format_query))[0];
                    //debug(DB::getQueryLog());
                }
                return response()->json($results);
            }
        }else{
            //else option------
        }
    }
}
