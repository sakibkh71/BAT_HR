<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Events\AuditTrailEvent as Audit;
use Auth;
use Session;
use DB;
class AuditLogController extends Controller {
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
    public function index($table,$type=0,$from_date=null,$to_date=null){

        $log_data = Audit::table($table)->get($from_date,$to_date)->logdata($type);
        return $log_data;
//        return view('audit_log',$data);
    }

}
