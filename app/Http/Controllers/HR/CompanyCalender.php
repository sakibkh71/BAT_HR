<?php
namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\AuditTrailEvent;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;

class CompanyCalender extends Controller
{
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    function calendarConfigure(Request $request){
        if(!empty($request->all())){
            $weekends = self::countFridays($request->calendar_month);
//            dd($weekends);
            $calendar_config = DB::table('hr_company_calendars')
                ->where('bat_company_id','=',$request->bat_company_id)
                ->where('date_is','like',$request->calendar_month.'%')
                ->get();
            // debug($calendar_config->toArray(),1);
            if(empty($calendar_config->toArray())){
/*                 procedure will insert all active employees to attendance table
                   and delete existing data of selected month
             */
               // DB::select("CALL proc_monthly_salary_generate('$request->calendar_month')");
                $dates = [];
                for($i=1;$i<=date('t',strtotime($request->calendar_month));$i++){
                    $date['date_is'] = $request->calendar_month.'-'.$i;
                    $date['bat_company_id'] = $request->bat_company_id;

                    if(in_array($request->calendar_month.'-'.sprintf("%02d", $i),$weekends)){
                        $date['day_status'] = 'W';
                    }else{
                        $date['day_status'] = 'R';
                    }
                    $dates[] = $date;
                }
                DB::table('hr_company_calendars')->insert($dates);
            }
            $sql = DB::table('hr_company_calendars');
            $sql->selectRaw('count(*) as total_days');
            $sql->addSelect('day_status');
            $sql->where('bat_company_id','=',$request->bat_company_id);
            $sql->where('date_is','like',$request->calendar_month.'%');
            $sql->groupBy('day_status');
            $monthly_day_status = $sql->get();
            $data['activeEmployees'] = self::getNumberOfActiveEmp($request->bat_company_id);
            $monthly_day = [];
            if(!empty($monthly_day_status)){
                foreach ($monthly_day_status as $day_status){
                    $monthly_day[$day_status->day_status] = $day_status->total_days;
                }
            }
            $data['monthly_day_status'] = $monthly_day;
        }

        $data['bat_company'] = $request->bat_company_id;
        $data['show_month'] = $request->calendar_month;

        return view('Hr_company_calendar.calendar_config',$data);
    }

    function countFridays($year_month){
        $day = 'Fri';
        $year = date('Y',strtotime($year_month));
        $month = date('m',strtotime($year_month));
        $ts=strtotime('first '.strtolower($day).' of '.$year.'-'.$month.'-01');
        $ls=strtotime('last day of '.$year.'-'.$month.'-01');
        $selectedDays=array(date('Y-m-d', $ts));
        while(($ts=strtotime('+1 week', $ts))<=$ls){
            $selectedDays[]=date('Y-m-d', $ts);
        }
        return $selectedDays;
    }

    //Show Calendar
    function calendarShow(Request $request){
        if(!empty($request->all())){
            $sql = DB::table('hr_company_calendars');
            $sql->selectRaw('count(*) as total_days');
            $sql->addSelect('day_status');
            $sql->where('bat_company_id','=',$request->bat_company_id);
            $sql->where('date_is','like',$request->calendar_month.'%');
            $sql->groupBy('day_status');
            $monthly_day_status = $sql->get();

            $data['activeEmployees'] = self::getNumberOfActiveEmp($request->bat_company_id);
            $monthly_day = [];
            if(!empty($monthly_day_status)){
                foreach ($monthly_day_status as $day_status){
                    $monthly_day[$day_status->day_status] = $day_status->total_days;
                }
            }
            $data['monthly_day_status'] = $monthly_day;
        }

        $data['emp_company'] = $request->bat_company_id;
        $data['show_month'] = $request->calendar_month;

        return view('Hr_company_calendar.calendar_show',$data);
    }

    function calendarConfigureSetEvent(Request $request){
        if($request->event_title == 'Weekend'){
            $day_is = 'W';
        }elseif($request->event_title == 'Holiday'){
            $day_is = 'H';
        }else{
            $day_is = 'R';
        }

       $data = array(
           'day_status'=>$day_is
       );
        $update = DB::table('hr_company_calendars')
            ->where('date_is',$request->start_date)
            ->where('bat_company_id',$request->company_id)->update($data);
//        AuditTrailEvent::updateForAudit($update, $data);

        return response()->json([
            'success'=>true,
        ]);
    }
    function calendarConfigureGetEvent($emp_company,$month){
        $sql = DB::table('hr_company_calendars')
            ->where('date_is','LIKE',"$month%")
            ->where('bat_company_id',$emp_company);
        $eventData = $sql->get();
        $allData = [];
        $row = [];
        if(!empty($eventData)){
            foreach($eventData as $data){
                if($data->day_status == 'W'){
                    $row['title'] = 'Weekend';
                    $row['color'] = '#f8ac59';
                    $row['textColor'] = '#FFF';
                }elseif($data->day_status == 'H'){
                    $row['title'] = 'Holiday';
                    $row['color'] = '#ed5565';
                    $row['textColor'] = '#FFF';
                }else{
                    $row['title'] = 'Working Day';
                    $row['color'] = '#1c84c6';
                    $row['textColor'] = '#FFF';
                }
                $row['resourceId'] = $data->hr_company_calendars_id;
                $row['start'] = $data->date_is;
                $row['end'] = $data->date_is;

                $allData[] = $row;
            }
        }

        return $allData;
    }

    private function getNumberOfActiveEmp($company_id){
        $sql = DB::table('sys_users');
        $sql->join('hr_working_shifts','hr_working_shifts.hr_working_shifts_id','=','sys_users.hr_working_shifts_id');
        $sql->select(DB::raw('count("sys_users.*") as total'));
        $sql->where('sys_users.status', 'Active');
        $sql->where('sys_users.bat_company_id', $company_id);
        $sql->where('sys_users.is_employee', 1);
        $sql->where('hr_working_shifts.is_rotable', 0);
        $result = $sql->get()->first();
        if($result->total){
            $output = $result->total;
        }else{
            $output = 0;
        }
        return $output;
    }
}
