<?php
namespace App\Http\Controllers\HR;

use App\Events\AuditTrailEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;
use App\Helpers\PdfHelper;
use Symfony\Component\Routing\Tests\Fixtures\AnnotationFixtures\RequirementsWithoutPlaceholderNameController;
use Illuminate\Support\Facades\Route;

class EmployeeInfo extends Controller
{
    public $data = [];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function emargencyContractList(){
//
//        $routes = array_map(function (\Illuminate\Routing\Route $route) { return $route->uri; }, (array) Route::getRoutes()->getIterator());
//dd($routes);
        $data = [];
        return view('HR.employee.emargency_contract_list', $data);
    }

    public function empEmargencyContractList($emp_id){
//        dd($emp_id);
        $contract_string = "";
        $list = DB::table('hr_emp_emargency_contract_info')->where('sys_users_id', $emp_id)->get();
        if(count($list) > 0){
            $contract_string .="<br/><table class='table col-md-6'>";
            $contract_string .="<tr>";
            $contract_string .="<th>Name</th>";
            $contract_string .="<th>Mobile</th>";
            $contract_string .="<th>Relation</th>";
            $contract_string .="<th>Address</th>";
            $contract_string .="<th>Primary Contact</th>";
            $contract_string .="</tr>";
            foreach($list as $info){
                $primary_data = $info->is_primary==1?"Primary":"-";
                $contract_string .="<tr>";
                $contract_string .="<td>".$info->name."</td>";
                $contract_string .="<td>".$info->mobile."</td>";
                $contract_string .="<td>".$info->relation."</td>";
                $contract_string .="<td>".$info->address."</td>";
                $contract_string .="<td>".$primary_data."</td>";
                $contract_string .="</tr>";
            }
            $contract_string .="</table>";
        }
        else{
            $contract_string .="No data found!";
        }

        return $contract_string;
    }

    public function probationList(){
        $data = [];
        return view('HR.employee.employee_probation_list', $data);
    }

    public function extendProbationDate($emp_id){

        $emp_info =  DB::table('sys_users')->where('id', $emp_id)->first();

        $data['emp_id'] = $emp_info->id;
        $data['confirmation_date'] = $emp_info->date_of_confirmation;

        return $data;
    }

    public function extendProbationUpdate(Request $request){

        $id=$request->employee_id;
        $user_info = DB::Table('sys_users')->select('bat_company_id','bat_dpid')->where('id',$id)->first();

        $probation_data=array(
            'sys_users_id'=>$request->employee_id,
            'bat_company_id'=>$user_info->bat_company_id,
            'bat_dpid'=>$user_info->bat_dpid,
            'date_of_confirmation'=>$request->extend_date,
            'remarks'=>$request->probation_remarks,
            'created_by'=>Auth::id(),
            'created_at'=>dTime()
        );
        $probation_insert = DB::table('hr_emp_probation_log')->insert($probation_data);

        if(!empty($request->extend_date)){
            $update = DB::table('sys_users')->where('id', $request->employee_id)->update(['date_of_confirmation'=> $request->extend_date]);
//            AuditTrailEvent::updateForAudit($update,['date_of_confirmation'=> $request->extend_date]);
            return response()->json([
                'success'=>true
            ]);
        }

        return response()->json([
            'success'=>false
        ]);
    }

    public function probationToActive(Request $request){
//        dd($request->employee_id[0]);
        if(!empty($request->employee_id[0])){
            $update = DB::table('sys_users')->where('id', $request->employee_id[0])->update(['date_of_confirmation'=> date("Y-m-d"), 'status'=>'Active']);
//            AuditTrailEvent::updateForAudit($update,['date_of_confirmation'=> date("Y-m-d"), 'status'=>'Active']);
            return response()->json([
                'success'=>true
            ]);
        }

        return response()->json([
            'success'=>false
        ]);
    }
}
