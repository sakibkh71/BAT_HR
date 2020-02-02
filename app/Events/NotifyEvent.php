<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use DB;

class NotifyEvent implements ShouldBroadcast{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $notification;
    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct() {
        $get_arguments       = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct'.$number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
    }

    public function __construct1($notification_id){
       // dd($notification_id);
        $base_sql = DB::table('sys_notifys')
            ->select('sys_notifys.*','sys_approval_modules.sys_approval_modules_name')
            ->leftjoin('sys_approval_modules', 'sys_notifys.event_for', '=', 'sys_approval_modules.unique_id_logic_slug');
        $counter_sql = clone $base_sql;
        $data['information'] = $base_sql->where('sys_notifys.sys_notifys_id', $notification_id)->first();

        $data['total_unread'] = $counter_sql->where('sys_notifys.seen_status', 'Unseen')
            ->where('sys_notifys.notify_to', $data['information']->notify_to)
            ->count();
        $this->notification = $data;
    }

    public function __construct2($notification_id, $extra) {


        $base_sql = DB::table('sys_notifys')
            ->select('sys_notifys.*');

        $counter_sql = clone $base_sql;
        $data['information'] = $base_sql->where('sys_notifys.sys_notifys_id', $notification_id)->first();
        //dd($data['information'] );
        $data['total_unread'] = $counter_sql->where('sys_notifys.seen_status', 'Unseen')
            ->where('sys_notifys.notify_to', $data['information']->notify_to)
            ->count();
        $this->notification = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(){
        return new Channel('apsisengin-channel');
    }

    public function broadcastWith()
    {
        return $this->notification;
    }
}
