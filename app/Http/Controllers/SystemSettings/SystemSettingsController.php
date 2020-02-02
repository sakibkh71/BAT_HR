<?php

namespace App\Http\Controllers\SystemSettings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Redirect;
use Auth;
use Session;

//for pdf
// use App\Helpers\PdfHelper;


class SystemSettingsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function view(){

    	$data['lists'] =  DB::table('sys_system_settings')->orderBy('id', 'DESC')->get();

    	return view('System_settings.view', $data);
    }

    public function update(Request $request){

    	if(!empty($request->id)){

    		$data = array(
    			'option_group' => $request->group_val,
    			'option_key' => $request->key_val,
    			'option_value' => $request->option_value,
    			'status' => $request->option_status,
    		);

    		DB::table('sys_system_settings')->where('id', '=', $request->id)->update($data);
    	}
    	else{

    		$data = array(
    			'option_group' => $request->groupVal,
    			'option_key' => $request->keyVal,
    			'option_value' => $request->valueVal,
    			'status' => $request->statusVal,
    		);

    		DB::table('sys_system_settings')->insert($data);
    	}
    }

}