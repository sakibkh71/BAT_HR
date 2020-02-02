<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Session;
use Auth;
use DB;
use Config;

class ModuleController extends Controller {
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public static function getModuleList(){
        $modules = session('USER_MODULES');
        $moduleList = DB::table('sys_modules')->whereIn('id', $modules)->get();
        return $moduleList;
    }

    public function moduleChanger(Request $request, $id){
        $moduleid = $id;
        $module_lang = self::getModuleLang($moduleid);
        $request->session()->forget('SELECTED_MODULE');
        $request->session()->forget('MODULE_LANG');
        $request->session()->put('SELECTED_MODULE', $moduleid);
        $request->session()->put('MODULE_LANG', $module_lang);
        return redirect('/');
    }

    public function getModule(){
        $id = Session::get('SELECTED_MODULE') > 0 ? Session::get('SELECTED_MODULE') : Session::get('DEFAULT_MODULE_ID');
        $defaultModule = DB::table('sys_modules')->where('id','=', $id)->value('name');
        if($defaultModule)
            return $defaultModule;
        else
            return 'Not Assign';
    }

    public function moduleList(){
        $userId = Auth::id();
        $moduleList = Module::where('status', 'Active')
            ->whereIn('id', function ($query) use ($userId) {
                $query->select('modules_id')
                    ->from('sys_privilege_modules')
                    ->where('users_id', $userId)
                    ->orWhereIn('user_levels_id', function ($query) use ($userId) {
                        $query->select('user_levels_id')
                            ->from('sys_privilege_levels')
                            ->where('users_id', $userId);
                    });
            })->get();
        return view('module_landing_page', compact('moduleList'));
    }

    public function getModuleLang($module_id){
        $module_lang = DB::table('sys_modules')->where('id',$module_id)->get()->first()->name;
        return $module_lang;
    }
}
