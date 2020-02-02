<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Response;

class BatApi extends Controller {

    public function __construct(){

    }
    function getLeaveStatus(Request $request){

        if($request->isMethod('post')){
            $dpid = $request->dpid;
            $q = DB::table('hr_emp_attendance')
                ->select('sys_users.name',
                    'sys_users.user_code as emp_code',
                    'daily_status')
                ->join('sys_users','sys_users.id','hr_emp_attendance.sys_users_id')
                ->whereIn('hr_emp_attendance.daily_status',['Lv'])
                ->where('hr_emp_attendance.day_is','=',$request->date)
                ->where('hr_emp_attendance.bat_dpid',$dpid);
            $data = $q->get();
            return Response::json($data);
        }else{
            return Response::json(['status'=>302,'message'=>'Bad Request']);
        }

    }
}