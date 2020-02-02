<?php

namespace App\Http\Controllers\SystemSettings;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Redirect;
use Auth;
use Session;


class MenuManagerController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
     * Menu Mange List
     */
    public function list(Request $request, $id=null){
        $data=[];
        if (!empty($id)){
            $data['nav_item']=DB::table('sys_menus')->where('id', $id)->first();
        }
        return view('System_settings.MenuManager.menu_list', $data);
    }

    /*
     * Store Menu Item
     */
    public function storeItem(Request $request){
        $insertArr['name'] =$request->label;
        $insertArr['menu_url'] =$request->link;
        $insertArr['icon_class'] =$request->icon;
        $insertArr['modules_id'] =$request->modules_id;
        $insertArr['menus_description'] =$request->menus_description;
        $insertArr['menus_description'] =$request->menus_description;

        if (isset($request->menu_id) && $request->menu_id !=''){
            $insertArr['updated_by'] =  Auth::id();
            $insertArr['updated_at'] =  date('Y-m-d h:i:s');
            if (DB::table('sys_menus')->where('id', $request->menu_id)->update($insertArr)){
                return redirect()->back()->with('succ_msg', 'Menu Item Update successfully');
            }
        }else{
            $insertArr['created_by'] =  Auth::id();
            $insertArr['created_at'] =  date('Y-m-d h:i:s');
            if (DB::table('sys_menus')->insert($insertArr)){
                return redirect()->back()->with('succ_msg', 'Create menu item successfully');
            }
        }
        return redirect()->back()->with('error', 'Something wrong please try again');
    }


    /*
     * return Json all of Menu Items
     */
    public function items(Request $request){
        if ($request->module !=''){
        $data =[];
        $nav = DB::table('sys_menus')
            ->select('id','modules_id','name','parent_menus_id','sort_number','menu_url','icon_class','menus_description')
            ->where('status', 'Active')
            ->where('modules_id', $request->module)
            ->orderBY('sort_number', 'ASC')
            ->get();

        $data = self::buildTree($nav,0);
        return response()->json([
                'data' => $data,
                'status' => 'success'
            ]);
        }
        return response()->json([ 'status' => 'error']);
    }

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
     * Get Single Items
     */
    public function menuItem(Request $request){
        $data=DB::table('sys_menus')->where('id', $request->id)->first();
        return response()->json([
            'data' => $data,
            'status' => 'success'
        ]);
    }

    /*
     * Delete Menu Item
     */
    public function destroyItem(Request $request){
        $delItem = DB::table('sys_menus')->where('id', $request->id)->first();
        DB::table('sys_menus')->where('id', $request->id)->update(['status'=>'Inactive']);
        DB::table('sys_menus')->where('parent_menus_id', $request->id)->update(['parent_menus_id'=>$delItem->parent_menus_id]);
        return response()->json([
            'status' => 'success'
        ]);
    }

    /*
     * Save Menu Orders
     */
    public function saveMenuOrder(Request $request){
        if (isset($request->menus) && !empty($request->menus)){
            self::updateRecursive($request->menus);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }
    //recursive function for update
    static function updateRecursive($data,$parent=0){
        foreach($data as $key =>$item) {
            // echo 'order ='.$key.'* id ='.$item['id'].'* parent='.$parent .'<br>';
            // Prepare data
            $updteArr = [];
            $updteArr['menus_type'] = $parent==0?'Main':'Sub';
            $updteArr['parent_menus_id'] = $parent;
            $updteArr['sort_number'] = $key;

            //Update order
            DB::table('sys_menus')->where('id', $item['id'])->update($updteArr);

            //Call Recursive method
            if (isset($item['children']) && !empty($item['children'])){
                self::updateRecursive($item['children'], $item['id']);
            }
        }
    }

}