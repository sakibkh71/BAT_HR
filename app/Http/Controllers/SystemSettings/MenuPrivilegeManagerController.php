<?php
namespace App\Http\Controllers\SystemSettings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Redirect;
use Auth;
use Session;


class MenuPrivilegeManagerController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }


    /*
     * Menu Mange List
     */
    public function privilege(Request $request){
        $data['post'] = $request->all();
        $level = $request->user_level_id;

        $nav = DB::table('sys_menus')
            ->select('sys_menus.id','sys_menus.modules_id','sys_menus.name','sys_menus.parent_menus_id', 'sys_privilege_menus.user_levels_id')
            ->leftJoin('sys_privilege_menus', function ($join) use ($level) {
                $join->on('sys_menus.id', '=', 'sys_privilege_menus.menus_id')
                    ->where('sys_privilege_menus.user_levels_id',$level);
            })
            ->where('sys_menus.status', 'Active')
            ->where('sys_menus.modules_id',  $request->modules_id)
            ->orderBY('sys_menus.sort_number', 'ASC')
            ->get();

        $menu_data = self::buildTree($nav,0);

        $data['menus'] = self::menuBuild($menu_data);

        return view('System_settings.MenuManager.privilege', $data);
    }

    /*
     * Data Prepare for Menu sub menu form objects
     */
    static function buildTree($elements, $parentId = 0){
        $navarr = array();
        foreach ($elements as $element) {
            if ($element->parent_menus_id == $parentId) {
                $children =self::buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $navarr[] = $element;
            }
        }
        return $navarr;
    }


    /*
     * Menu Prepare form object
     */
    static function menuBuild($obj, $parent=0){
        if(!empty($obj)){
            $response = '<ul>';

                foreach($obj as $items){
                    $checked = !empty($items->user_levels_id)?' checked ':'';
                    $response .= '<li><label for="nav'.$items->id.'"><input type="checkbox" name="nav_id[]" id="nav'.$items->id.'" value="'.$items->id.'"'. $checked .' > '.$items->name.'</label>';
                    if (!empty($items->children)){
                        $response .= self::menuBuild($items->children, $items->id);
                    }
                    $response .= '</li>';
                }

            $response .= '</ul>';

            return $response;

        }else{
            return "Sorry No data found";
        }
    }


    /*
     * Save Menu Orders
     */
    public function setPrivilege(Request $request){
        if ( !empty($request->modules_id) && !empty($request->user_level_id)){
            $insertArray=[];
            foreach ($request->nav_id as $key=>$navItem) {
                $insertArray[$key]['user_levels_id'] =$request->user_level_id;
                $insertArray[$key]['menus_id'] =$navItem;
            }

            $delData = DB::table('sys_privilege_menus')
                 ->join('sys_menus', 'sys_privilege_menus.menus_id', '=', 'sys_menus.id')
                 ->where('sys_menus.modules_id', '=', $request->modules_id)
                 ->where('sys_privilege_menus.user_levels_id', '=',$request->user_level_id)
                ->delete();

            DB::table('sys_privilege_menus')->insert($insertArray);

            //return redirect()->route('menu-privilege', ['user_level_id'=>$request->user_level_id, 'modules_id'=>$request->modules_id]);
            return redirect()->back()->with('succ_msg', 'Menu Privilege Item Update successfully');
        }
        return redirect()->back()->with('error', 'Something wrong please try later');
    }

}