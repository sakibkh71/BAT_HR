<?php
namespace App\Http\Controllers\Delegation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use URL;

class MyApprovalList extends Controller{
    private $data;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function index(){
        $approval_modules = DB::table('sys_approval_modules');
        $approval_modules->where('status','Active');
        $approval_modules_names = $approval_modules->get();
        return view('Delegation.delegation_approval_list',compact('approval_modules_names'));
    }

    public function seenApprovalNotification(Request $request){
        $all_req = $request->all();
        $user_id = session()->get('USER_ID');
        //        DB::connection()->enableQueryLog();
        DB::table('sys_notifys')
            ->where('notify_to', $user_id)
            ->where('event_for', $all_req['slug'])
            ->where('url_ref',$all_req['route'])
            ->where('seen_status','Unseen')
            ->update(['seen_status'=>'Seen', 'seen_at'=>currentDateTime()]);
        //        $queries = DB::getQueryLog();
        $this->data['message'] = 'Success';
        $this->data['url'] = URL::to($all_req['route']);
        echo json_encode($this->data);
    }

    public function getDelegationList(Request $request) {
        $approved_cond = $request->all();
//        dd($approved_cond);
        $delegation_list = DB::table('sys_delegation_historys');
        $delegation_list->select('ref_id','act_status','act_comments', 'created_at');
        $delegation_list->where('delegation_reliever_id',auth()->user()->id);
        if(isset($approved_cond['date_range'])) {
            $range = explode(" - ", $approved_cond['date_range']);
            $delegation_list->whereBetween('sys_delegation_historys.created_at', $range);
        }

        if(isset($approved_cond['ref_id'])) {
            $delegation_list->whereIn('sys_delegation_historys.ref_id', $approved_cond['ref_id']);
        }
        $data['dele_list'] = $delegation_list->get();
        $data['date_range'] = $request->date_range;
        $data['ref_code'] = $request->ref_id;
//        dd($data);
        return view('Delegation.my_approved_list', compact('data'));
    }
}
