<?php
namespace App\Http\Controllers;

use URL;
use Session;
use App\Events\NotifyEvent;
use DB;

class TestController extends Controller {
    public $data = [];
    /*============================================================================================*/
    /*============================================================================================*/
    /*============================================================================================*/
    public function sendNotice(){
        $notification_id = self::getNotification();
        if($notification_id){
            event(new NotifyEvent($notification_id));
        }
    }
    public function getNotice(){
        return view('pusher_test', $this->data);
    }
    public function getNotification(){
        $noti_arr = [
            'generated_from'=> 'Person',
            'generated_source'=> 3,
            'notify_to'=> 3, // jar kache notification jabe
            'event_for'=> 'prc_req', // event slug / id_logic_slug / approval event slug
            'event_id'=> 45,
            'content'=> 'Somebody Approved a requisition and now is for you.',
            'url_ref'=> 'sales-order-approval-list', // Approval module redirect url
            'created_at'=> currentDate(),
            'priority'=> 3
        ];



        $id = DB::table('sys_notifys')->insertGetId($noti_arr);
        if($id){
            return $id;
        }else{
            return false;
        }

    }
}
