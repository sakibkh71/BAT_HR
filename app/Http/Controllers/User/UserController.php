<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use URL;
use DB;
use Session;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function index(Request $request){
        $module = 'User';
        $grid_sql = DB::select(DB::raw("select * from sys_master_entry where sys_master_entry_name = '".strtolower($module)."' "));
        foreach ($grid_sql as $sqlKey => $sql) {
            $gridSql = $sql->grid_sql;
            $primaryKeyHide = $sql->primary_key_hide;
            $grid_title = $sql->grid_title;
        };
        $records = DB::select(DB::raw($gridSql));
        if(count($records) < 1){
            $th = array();
        }else{
            foreach ($records[0] as $key => $record) {
                $th[] = $key;
            };
        }
        $pageData = [
            'modal' => 'includes.apsysmodal',
            'title' => 'User Form',
            'moduleName' => strtolower($module),
            'getRawUrl' => URL::to('getUserRaw'),
            'content' => [
                'new' => 'User.new',
                'view' => 'User.view',
                'search' => 'User.search',
                'sampleImport' => 'User.sampleimport',
            ],
            'action' => [],

            'propsData' => [
                'grid_title' => $grid_title,
                'th' => $th,
                'tdata' =>$records,
                'primaryKeyHide' =>$primaryKeyHide,
                'primaryKey' => 'id',
            ]
        ];
        return view('User.list', compact('pageData'));
    }




    public function notifyDismiss(Request $request){
        session()->put('PASSWORD_NOTIFY', 0);
    }

    public function getUserProfile()
    {
        $pageData = [
            'title' => 'User Profile',
            'record' => \App\User::find(Auth::id()),
        ];

        return view('user.profile', compact('pageData'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $record = \App\User::findOrFail($request->id);
            $record->save($request->all());
            return response()->json(['status' => 'success', 'message' => 'Profile Successfully updated']);
        } catch (Exception $exception) {
            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request!']);
        }
    }

//    Functions added for Managing User Profile 02-12-18
    public function getUserProfiles(){
        $pageData = [
            'title' => 'User Profile',
            'record' => \App\User::find(Auth::id()),
        ];

        $password_conf = app(LoginController::class)->userLevelInfoQuery(Auth::user()->id);

        $privilege_houses = Session::get('PRIVILEGE_HOUSE');
        if(empty($privilege_houses) ){
            $privilege_house_info=DB::select('SELECT * FROM bat_company WHERE bat_company_id ');
        }
        else {
            $privilege_house_info = DB::select('SELECT * FROM bat_company WHERE bat_company_id IN (' . $privilege_houses . ')');
        }

        return view('user.profiles', compact('pageData','password_conf','privilege_house_info'));
    }

    //Password Reset
    public function resetUserPassword() {
        $data['password_conf'] = app(LoginController::class)->userLevelInfoQuery(Auth::user()->id);
        return view('user.reset_password',$data);
    }

    public function resetPasswordSubmit(Request $request){
        $post = $request->all();
        //debug($post,1);
        $user = DB::table('sys_users')->where('id', Auth::user()->id)->first();
        if($user && \Hash::check($post['current-password'], $user->password)){
            $updateArray = array(
                'password'=>\Hash::make($post['new-password']),
                'password_changed_date'=>date('Y-m-d h:i:s')
            );
            DB::table('sys_users')->where('id', Auth::user()->id)->update($updateArray);
            session()->put('PASSWORD_NOTIFY', 0);
            session()->put('PASSWORD_EXPIRY', 0);
            echo true;
        }else{
            echo false;
        }
    }

    //Updating Company Logo

    public function updateCompanyLogo(Request $request){
        $company_id=$request->company_id;

        $company_info=DB::table('bat_company')->where('bat_company_id',$company_id)->first();

        $file_to_delete=$company_info->logo;
        if(strlen($file_to_delete) >0 ){
            if(file_exists(public_path().'/img/company_logo/'.$file_to_delete)){
                unlink(public_path().'/img/company_logo/'.$file_to_delete);
            }

        }

     $fileName = $_FILES['file']['name'];

        $fileType = $_FILES['file']['type'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        $modifiedFileName=$company_info->company_name.'.'.$ext;
        $fileError = $_FILES['file']['error'];
        $fileContent =$_FILES['file']['tmp_name'];
        move_uploaded_file($fileContent,public_path().'/img/company_logo/'.$modifiedFileName);

        $data=array(
           'logo'=>   $modifiedFileName
        );
        DB::table('bat_company')->where('bat_company_id',$company_id)->update($data);


    }
    //Updating User Profile
    public function updateUserProfile(Request $request) {

      if($request->hasFile('inpFile')){
          $file = $request->file('inpFile');
          $image_name = $file->getClientOriginalName();
          $ext = pathinfo($image_name, PATHINFO_EXTENSION);
          $name = $request->pkid.".".$ext;
          $file->move(public_path().'/img/users/',$name);
          $img_location = '/img/users/'.$name;

      } else {
          $img_location = '/img/users/Avatar.png';
      }
      $user = $request->all();

       $id = $user['pkid'];
       $name = $user['name'];
       $email = $user['email'];
       $mobile = $user['mobile'];
       $date_of_birth = $user['date_of_birth'];
       $gender = $user['gender'];
       $religion = $user['religion'];
       $address = $user['address'];

       $updateUser = array(
         'name'=>$name,
         'email'=>$email,
         'mobile'=>$mobile,
         'date_of_birth'=>$date_of_birth,
         'gender'=>$gender,
         'religion'=>$religion,
         'address'=>$address,
         'updated_at'=>date('Y-m-d'),
         'updated_by'=>Auth::id()
       );

       if($img_location !='/img/users/Avatar.png') {
           $updateUser['user_image']=$img_location;
       }
//dd($updateUser);
      $succ_chk=  DB::table('sys_users')
           ->where('id',$id)
           ->update($updateUser);

//        dd($updateUser, $id, $succ_chk);
       if($succ_chk) {
           return redirect()->back()->with('message', 'Profile Updated Successfully');
       } else {
           return redirect()->back()->with('message', 'Update Failed!');
       }
    }


/*
 * User List
 * */

    public function List(Request $request){
        $query =  DB::table('sys_users')
            ->select(
                'sys_users.id',
                'sys_users.name',
                'sys_users.user_code',
                'sys_users.username',
                'sys_users.email',
                'sys_users.mobile',
                'sys_users.status'
            )
            ->where('username','!=', null);
        $session_con = (sessionFilter('url','user-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $query->whereRaw($session_con);
        }

        $userlist = $query->whereIn('sys_users.status',['Active','Probation'])->get();
        $data['userlist'] = $userlist;
//        dd($data['userlist'] );
        return view('user.list', $data);
    }

    public function entryForm($sys_users_id=null){
        $data = [];
        $dpids = '';
        if($sys_users_id){
            $sql = DB::table('sys_users');
            $sql->select('sys_users.*',
                DB::raw('GROUP_CONCAT(sys_privilege_levels.user_levels_id) as user_levels'),
                DB::raw('GROUP_CONCAT(sys_privilege_modules.modules_id) as user_modules')
            );
            $sql->leftJoin('sys_privilege_levels','sys_privilege_levels.users_id','sys_users.id');
            $sql->leftJoin('sys_privilege_modules','sys_privilege_modules.users_id','sys_users.id');
            $sql->where('id',$sys_users_id);
            $sql->groupBy('sys_users.id');


            $user_points_query = DB::table('sys_privilege_points');
            $user_points_query->where('sys_users_id', $sys_users_id);
            $user_points_result = $user_points_query->get()->toArray();
            $dpids = implode(',',array_column($user_points_result,'bat_dpid'));

            $data['user'] = $sql->first();
        }

        $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationTree')->searchForm($dpids);
        return view('user.user_entry', $data);
    }

    function storeUser(Request $request){
        $company_privilege = '';
        if(!empty($request->point)) {
            $points = implode(',',$request->point);
            $dsids = collect(DB::select("SELECT GROUP_CONCAT(DISTINCT dsid) as dsid FROM `bat_distributorspoint` WHERE id IN ($points)"))->first()->dsid;
        }

        if(!empty(explode(',',$dsids))){
            $house_id = explode(',',$dsids)[0];

        }else{
            $house_id = '';
        }
        $user_arr = array(
            'name'=>$request->name,
            'email'=>$request->email,
            'username'=>$request->username,
            'user_code'=>$request->user_code,
            'default_url'=>$request->default_url,
            'default_module_id'=>$request->default_module_id,
            'privilege_houses'=>$dsids,
            'bat_company_id' => $house_id
//            'privilege_points'=>implode(',',$request->point),
//            'password'=>Hash::make(123456),
        );
        if($request->sys_users_id){
            $user_arr['updated_by'] = Auth::id();
            $user_arr['updated_at'] = date('Y-m-d h:i:s');
            DB::table('sys_users')->where('id',$request->sys_users_id)->update($user_arr);
            $sys_users_id = $request->sys_users_id;
            DB::table('sys_privilege_levels')->where('users_id',$sys_users_id)->delete();
            DB::table('sys_privilege_modules')->where('users_id',$sys_users_id)->delete();
            DB::table('sys_privilege_points')->where('sys_users_id',$sys_users_id)->delete();
        }else{
            $user_arr['created_by'] = Auth::id();
            $user_arr['created_at'] = date('Y-m-d h:i:s');
            $user_arr['password'] = Hash::make(123456);
            DB::table('sys_users')->insert($user_arr);
            $sys_users_id = DB::getPdo()->lastInsertId();
        }
        if(!empty($request->point)){
            $user_levels = [];

            foreach ($request->point as $point){
                $user_points[] = array(
                    'sys_users_id'=>$sys_users_id,
                    'bat_dpid'=>$point
                );
            }
            DB::table('sys_privilege_points')->insert($user_points);
        }
        if(!empty($request->user_levels)){
            $user_levels = [];
            foreach ($request->user_levels as $level){
                $user_levels[] = array(
                    'user_levels_id'=>$level,
                    'users_id'=>$sys_users_id
                );
            }
            DB::table('sys_privilege_levels')->insert($user_levels);
        }
        if(!empty($request->user_modules)){
            $user_modules = [];
            foreach ($request->user_modules as $module){
                $user_modules[] = array(
                    'modules_id'=>$module,
                    'users_id'=>$sys_users_id
                );
            }
            DB::table('sys_privilege_modules')->insert($user_modules);
        }
        if($sys_users_id){
            return response()->json([
                'success' => true,
                'message' => 'Save Successfully'
            ]);

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Failed to Submit!'
            ]);
        }
    }

    //User Info
    public function getUserInfo(Request $request){
        if($request->sys_users_id){
            $sql = DB::table('sys_users');
            $sql->select('sys_users.*',
                'dmodule.name as default_module',
                DB::raw('GROUP_CONCAT(DISTINCT sys_user_levels.`name` SEPARATOR ", ") AS levels'),
                DB::raw('GROUP_CONCAT(DISTINCT sys_modules.`name` SEPARATOR " <br>") AS modules'),
                DB::raw('GROUP_CONCAT(DISTINCT bat_company.`company_name` SEPARATOR " <br>") AS house'),
                DB::raw('GROUP_CONCAT(DISTINCT bat_distributorspoint.`name` SEPARATOR " <br>") AS point')
            );
            $sql->leftJoin('sys_modules as dmodule','dmodule.id','sys_users.default_module_id');
            $sql->leftJoin('sys_privilege_levels','sys_privilege_levels.users_id','sys_users.id');
            $sql->leftJoin('sys_user_levels','sys_privilege_levels.user_levels_id','sys_user_levels.id');
            $sql->leftJoin('sys_privilege_modules','sys_users.id','sys_privilege_modules.users_id');
            $sql->leftJoin('sys_modules','sys_privilege_modules.modules_id','sys_modules.id');

            $sql->leftJoin('bat_company', function($query) {
                $query->whereRaw('FIND_IN_SET(sys_users.privilege_houses,bat_company.bat_company_id) = 0');
            });

            $sql->leftJoin('bat_distributorspoint', function($query) {
                $query->whereRaw('FIND_IN_SET( sys_users.privilege_points,bat_distributorspoint.id) = 0');
            });

            $sql->where('sys_users.id',$request->sys_users_id);
            $sql->groupBy('sys_users.id');
            $sql->get();

            $data['user'] = $sql->first();
        }

        return view('user.user_info', $data);
    }


    //Employee List
    public function userEmployeeList(Request $request){
        $query =  DB::table('sys_users')
            ->select(
                'sys_users.id',
                'sys_users.name',
                'sys_users.user_code',
                'sys_users.username',
                'sys_users.email',
                'sys_users.mobile',
                'sys_users.status'
            )
            ->where('is_employee','=', 1)
            ->where('username', '=', '');
        $userlist = $query->where('sys_users.status','Active')->get();
        $data['userlist'] = $userlist;
        return view('user.employee_user_list', $data);
    }

    //Create User to Employee
    public function createUserEmployee($sys_users_id=null){
        $data = [];
        $dpids = '';
        if($sys_users_id){
            $sql = DB::table('sys_users');
            $sql->select('sys_users.*',
                DB::raw('GROUP_CONCAT(sys_privilege_levels.user_levels_id) as user_levels'),
                DB::raw('GROUP_CONCAT(sys_privilege_modules.modules_id) as user_modules')
            );
            $sql->leftJoin('sys_privilege_levels','sys_privilege_levels.users_id','sys_users.id');
            $sql->leftJoin('sys_privilege_modules','sys_privilege_modules.users_id','sys_users.id');
            $sql->where('id',$sys_users_id);
            $sql->groupBy('sys_users.id');
            //$sql->get();
            $data['user'] = $sql->first();

            $user_points_query = DB::table('sys_privilege_points');
            $user_points_query->where('sys_users_id', $sys_users_id);
            $user_points_result = $user_points_query->get()->toArray();
            $dpids = implode(',',array_column($user_points_result,'bat_dpid'));
        }



        $data['multiple_search_criteria'] = app('App\Http\Controllers\LocationTree')->searchForm($dpids);
        return view('user.create-user-employee', $data);
    }

    //Store Employee Data
    function storeUserEmployee(Request $request){
        if (isset($request->password) && !empty($request->password)) {
            $user_arr['password'] = Hash::make($request->password);
        }

        $user_arr['email'] = $request->email;
        $user_arr['username'] = $request->username;
        $user_arr['default_url'] = $request->default_url;
        $user_arr['default_module_id'] = 3;

        if($request->sys_users_id){
            $user_arr['updated_by'] = Auth::id();
            $user_arr['updated_at'] = date('Y-m-d h:i:s');
            DB::table('sys_users')->where('id',$request->sys_users_id)->update($user_arr);
            $sys_users_id = $request->sys_users_id;
            DB::table('sys_privilege_points')->where('sys_users_id',$sys_users_id)->delete();
            DB::table('sys_privilege_levels')->where('users_id',$sys_users_id)->delete();
            DB::table('sys_privilege_modules')->where('users_id',$sys_users_id)->delete();
        }else{
            $user_arr['created_by'] = Auth::id();
            $user_arr['created_at'] = date('Y-m-d h:i:s');
            DB::table('sys_users')->insert($user_arr);
            $sys_users_id = DB::getPdo()->lastInsertId();
        }

        if(!empty($request->point)){
            $user_levels = [];

            foreach ($request->point as $point){
                $user_points[] = array(
                    'sys_users_id'=>$sys_users_id,
                    'bat_dpid'=>$point
                );
            }
            DB::table('sys_privilege_points')->insert($user_points);
        }
        //user_levels
        if(!empty($request->user_levels)){
            $user_levels = [];
            foreach ($request->user_levels as $level){
                $user_levels[] = array(
                    'user_levels_id'=>$level,
                    'users_id'=>$sys_users_id
                );
            }
            DB::table('sys_privilege_levels')->insert($user_levels);
        }

        DB::table('sys_privilege_modules')->insert([ 'modules_id'=>3,'users_id'=>$sys_users_id]);

        /*if(!empty($request->user_modules)){
            $user_modules = [];
            foreach ($request->user_modules as $module){
                $user_modules[] = array(
                    'modules_id'=>$module,
                    'users_id'=>$sys_users_id
                );
            }
        }*/

        if($sys_users_id){
            return response()->json([
                'success' => true,
                'message' => 'Save Successfully'
            ]);

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Failed to Submit!'
            ]);
        }
    }

}
