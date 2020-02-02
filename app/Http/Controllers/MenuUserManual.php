<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\SysModule;
use Validator;
use URL;
use Session;
use Auth;
use DB;
use Illuminate\Support\Facades\Redirect;

class MenuUserManual extends Controller {
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function getExistingManualInfo(Request $request){
        $post = $request->except('_token');
        $sys_manual_info = DB::table('sys_manual');
        if(isset($post['menu_id'])){
            $sys_manual_info->where('sys_menu_id', '=', $post['menu_id']);
        }
        $result = $sys_manual_info->get()->first();
        $html = '';
        if(isset($post['menu_id'])){
            if(!empty($result)){
                if($post['type'] =="developer"){
                    echo $result->developer_manual;
                }else{
                    echo $result->user_manual;
                }
            }else{
                $html .= "<p style='color: red;'>In sys_manual table Menu ID configuration is not set yet!</p>";
                echo $html;
            }
        }else{
            $html .= "<p style='color: red;'>In sys_manual table Menu ID configuration is not set yet!</p>";
            echo $html;
        }
    }

    public function updateManualInfo(Request $request){
        $post = $request->except('_token');
        if($post['selected_type'] == "developer"){
            $update_arr = array(
                'developer_manual' => $post['manual']
            );
        }else{
            $update_arr = array(
                'user_manual' => $post['manual']
            );
        }
        DB::table('sys_manual')->where('sys_manual.sys_menu_id', '=', $post['menu_id'])
            ->update($update_arr);
        echo 'updated';
    }

}
