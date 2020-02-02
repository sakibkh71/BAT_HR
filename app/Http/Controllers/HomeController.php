<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;

use URL;

class HomeController extends Controller {
    private $data = [];
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }
    public function noPermission(){
        return view('errors/no_permission');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
//        debug(Session::all());
        $data['modules'] = session('USER_MODULES');
        $data['selected_module'] = session('SELECTED_MODULE');
//        return view('Dashboard.dyn_home');
        return view('Dashboard.home', $data);
    }

    public function getUserNotifications(Request $request){
        $user_id=$request->user_id;
        $user_notifications=DB::table('sys_notifys')  ->select('sys_notifys.*','sys_approval_modules.sys_approval_modules_name')->where('notify_to',$user_id)->where('sys_notifys.seen_status', 'Unseen')
            ->leftjoin('sys_approval_modules', 'sys_notifys.event_for', '=', 'sys_approval_modules.unique_id_logic_slug')
            ->orderBy('created_at','DESC')
            ->get();
        return $user_notifications;
    }

    public function redirectToNotificationRoute(Request $request){


        $url=$request->url;
        $event_for=$request->event_for;
        $user_id = session()->get('USER_ID');
        //        DB::connection()->enableQueryLog();
        DB::table('sys_notifys')
            ->where('notify_to', $user_id)
            ->where('event_for', $event_for)
            ->where('url_ref',$url)
            ->where('seen_status','Unseen')
            ->update(['seen_status'=>'Seen', 'seen_at'=>currentDateTime()]);

       return $url;
    }

    public function seeAllNotifications(){
        $user_id = session()->get('USER_ID');
//        DB::table('sys_notifys')
//            ->where('notify_to', $user_id)
//            ->where('seen_status','Unseen')
//            ->update(['seen_status'=>'Seen', 'seen_at'=>currentDateTime()]);
        return view('HR.employee.employee_notifications');
    }

    public function redirectToSingleNotification(Request $request){
        $notification_id=$request->notification_id;

        DB::table('sys_notifys')
            ->where('sys_notifys_id', $notification_id)
            ->update(['seen_status'=>'Seen', 'seen_at'=>currentDateTime()]);

        $notification_url=DB::table('sys_notifys')->where('sys_notifys_id', $notification_id)->first()->url_ref;
        return $notification_url;

    }
}
