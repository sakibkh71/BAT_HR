<?php

namespace App\Http\Controllers\HrPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Psr\Log\NullLogger;
use URL;
use DB;
use Input;
use Redirect;
use Auth;
use Session;
use Validator;
use File;
use DateTime;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class HrPayrollController extends Controller {

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function salaryMonthConfig($id = NULL){
        $data['title'] = 'Working Days Configuration';
        if($id){
            $info = self::getHrSalaryMonthConfigsData($id);
            $data['info'] = $info;
        }
        return view('Hr_payroll.salary_month_config_form', $data);
    }

    /*
     * Return Days based on month and category
     */
    public function getDays(Request $request){
        $result = [];
        $month = date('m',strtotime( $request->month));
        $year = date('Y',strtotime( $request->month));

       $data = DB::table('hr_company_calendars')
           ->select('hr_company_calendars.*', DB::raw('COUNT(hr_company_calendars.day_status) as day_num'))
           ->whereMonth('date_is', $month)
           ->whereYear('date_is', $year)
           ->where('hr_emp_categorys_id', $request->cat)
           ->groupBy('hr_company_calendars.day_status')
           ->get();

        $result['active_emp'] = self::getNumberOfActiveEmpByHrEmpCategory($request->cat);


       if (!empty($data)){
           foreach ($data as $item) {
               $result[$item->day_status] = $item->day_num;
           }
           return response()->json([
               'data' => $result,
               'status' => 'success'
           ]);
       }
       else{
           return response()->json([
               'status' => 'error'
           ]);
       }
    }

    public function hrEmployeeSalaryMonthInfoSave(Request $request){
        $post = $request->all();
        $year_month = explode('-',$post['hr_salary_month_name']);
        $year = $year_month[0];

        $month = date("F", mktime(0, 0, 0, $year_month[1], 10));

        $last_date = date("t", strtotime($post['hr_salary_month_name']));

        $number_of_active_emp = self::getNumberOfActiveEmpByHrEmpCategory($post['hr_emp_category_id']);

        $hr_salary_month_configs_arr = array(
            'hr_salary_month_name' => date('Y-m',strtotime($post['hr_salary_month_name'])),
            'year' => $year,
            'month' => $month,
            'hr_emp_categorys_id' => $post['hr_emp_category_id'],
            'number_of_days' => $last_date,
            'number_of_working_days' => $post['number_of_working_days'],
            'number_of_holidays' => $post['number_of_holidays'],
            'number_of_weekend' => $post['number_of_weekend'],
            'number_of_active_emp' => $number_of_active_emp,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s')
        );
        $exquery = DB::table('hr_salary_month_configs')->where('hr_emp_categorys_id', $post['hr_emp_category_id'])
            ->where('year', $year)->where('month', $month);
            if(isset($post['existing_id'])){
                $exquery->where('hr_salary_month_configs_id', '!=', $post['existing_id']);
            }
        $exist = $exquery->get();

        if(count($exist) > 0) {
            return response()->json([
                'status' => 'exist',
            ]);
        }


        if(isset($post['existing_id'])){
            DB::table('hr_salary_month_configs')->where('hr_salary_month_configs.hr_salary_month_configs_id', '=', $post['existing_id'])
                ->update($hr_salary_month_configs_arr);

            return response()->json([
                'status' => 'success',
                'result' => 'updated'
            ]);

        }else{
            DB::table('hr_salary_month_configs')->insert($hr_salary_month_configs_arr);
            $hr_salary_month_configs_id = DB::getPdo()->lastInsertId();
            //echo $hr_salary_month_configs_id;

            return response()->json([
                'status' => 'success',
                'result' => 'created',
                'id' =>$hr_salary_month_configs_id
            ]);
        }
    }


    private function getNumberOfActiveEmpByHrEmpCategory($categoy_id){
        $sql = DB::table('sys_users');
        $sql->select(DB::raw('count("sys_users.*") as total'));
        $sql->where('sys_users.status', 'Active');
        $sql->where('sys_users.hr_emp_categorys_id', $categoy_id);
        $sql->where('sys_users.is_employee', 1);
        $result = $sql->get()->first();
        if($result->total){
            $output = $result->total;
        }else{
            $output = 0;
        }
        return $output;
    }
    public function hrEmployeeSalaryMonthConfigurationList(Request $request,$id=NULL){
        $data['title'] = 'Working days configuration list';
        $data['all_data'] = self::getHrSalaryMonthConfigsData($id);
        return view('Hr_payroll.month_wise_salary_configuration_list', $data);
    }

    private function getHrSalaryMonthConfigsData($id=NULL){
        $sql = DB::table('hr_salary_month_configs');
        $sql->select('hr_salary_month_configs.*');
        $sql->addSelect('hr_emp_categorys.hr_emp_category_name');
        $sql->addSelect('sys_users.name');
        $sql->leftJoin('hr_emp_categorys', 'hr_emp_categorys.hr_emp_categorys_id', '=', 'hr_salary_month_configs.hr_emp_categorys_id');
        $sql->leftJoin('sys_users', 'sys_users.id', '=', 'hr_salary_month_configs.created_by');
        $sql->where('hr_salary_month_configs.status', 'Active');
        if($id){
            $sql->where('hr_salary_month_configs.hr_salary_month_configs_id', $id);
            $result = $sql->get()->first();
        }else{
            $result = $sql->get()->toArray();
        }
        return $result;
    }

    public function deleteHrEmployeeSalaryMonthDetailsInfo($id){
        $where['hr_salary_month_configs_id'] =  $id;
        DB::table('hr_salary_month_configs')->where($where)->delete();
        echo 'done';
    }
    public function ShowHrEmployeeSalaryMonthDetailsInfo($id){
        $data['title'] = 'Hr Employee Month Wise Salary Configuration';
        $data['info'] = self::getHrSalaryMonthConfigsData($id);
        return view('Hr_payroll.show_hr_employee_salary_month_details_info', $data);
    }

    public function hrEmployeeMontlyHolidayCheck(Request $request){
        $post = $request->except('_token');
        $last_date = date("t", strtotime($post['hr_salary_month_name']));
        $total_allocated_days = $post['number_of_working_days'] + $post['number_of_holidays'] + $post['number_of_weekend'];
        if($last_date == $total_allocated_days){
            echo "matched";
        }else{
            echo "not matched";
        }
    }


    private function getUserInfo($user_code){
        $sql  = DB::table('sys_users')
            ->select('sys_users.*')
            ->addSelect('hr_emp_sections.hr_emp_section_name')
            ->join('hr_emp_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id')
            ->where('sys_users.user_code','=',$user_code)
            ->get()->first();
        return $sql;
    }

    private function getTotalWorkingTime($start_hour_min_second,$end_hour_min_second,$break_time,$overtime){
        $start = date("Y-m-d H:i",strtotime($start_hour_min_second));
        $end = date("Y-m-d H:i",strtotime($end_hour_min_second));

        $datetime1 = new DateTime($start);
        $datetime2 = new DateTime($end);
        $interval = $datetime1->diff($datetime2);
        $time_difference = $interval->format('%h').".".$interval->format('%i');
        $actual_working_time = $time_difference -($break_time)+$overtime;
//        echo $start;
//        echo '<br>';
//        echo $end;
//        echo '<br>';
//        echo "difference ".$time_difference;
//        echo '<br>';
//        echo 'breaktime '.$break_time;
//        echo '<br>';
//        echo 'overtime'.$overtime;
//        echo '<br>';
//        echo $actual_working_time;
//        exit();
        return $actual_working_time;
    }



    private function getHrEmployeeAttendanceRawData($post = NULL){
//        DB::enableQueryLog();
        $sql  = DB::table('hr_emp_attendance');
        $sql->select('hr_emp_attendance.*');
        $sql->addSelect(DB::raw('hr_emp_categorys.hr_emp_category_name'));
        $sql->addSelect(DB::raw('hr_working_shifts.shift_name'));
        $sql->Join('hr_emp_categorys', 'hr_emp_categorys.hr_emp_categorys_id', '=', 'hr_emp_attendance.hr_emp_categorys_id');
        $sql->Join('hr_working_shifts', 'hr_working_shifts.hr_working_shifts_id', '=', 'hr_emp_attendance.hr_working_shifts_id');
        if (!empty($post['date_range'])) {
            $range = explode(" - ", $post['date_range']);
            $from = date('Y-m-d',strtotime($range[0]));
            $to = date('Y-m-d',strtotime($range[1]));
            $sql->whereBetween('hr_emp_attendance.day_is', [$from, $to]);
        }
//        if (!empty($post['sys_users'])  && $post['sys_users'] != '') {
//            $sql->whereIn('hr_emp_attendance.user_code', $post['sys_users']);
//        }
        if (!empty($post['hr_emp_categorys'])) {
            $sql->whereIn('hr_emp_attendance.hr_emp_categorys_id', $post['hr_emp_categorys']);
        }
        $result = $sql->get()->toArray();
//        debug(DB::getQueryLog());
        return $result;
    }

    private function getPreparedEmployeeAttendanceRawData($code = NULL,$process_type = NULL){
        $sql  = DB::table('hr_temporary_emp_attendance');
        $sql->select('hr_temporary_emp_attendance.user_code','hr_temporary_emp_attendance.day_is','hr_temporary_emp_attendance.created_at','hr_temporary_emp_attendance.created_by','hr_temporary_emp_attendance.status','hr_temporary_emp_attendance.start_date_time','hr_temporary_emp_attendance.end_date_time','hr_temporary_emp_attendance.break_time','hr_temporary_emp_attendance.total_work_time','hr_temporary_emp_attendance.ot_hours');
        $sql->addSelect(DB::raw('sys_users.id as sys_users_id'));
        $sql->addSelect('sys_users.hr_emp_categorys_id','sys_users.hr_working_shifts_id');
        $sql->addSelect(DB::raw('hr_emp_sections.hr_emp_section_name as section_name'));
        $sql->Join('sys_users', 'sys_users.user_code', '=', 'hr_temporary_emp_attendance.user_code');
        $sql->Join('hr_emp_sections', 'hr_emp_sections.hr_emp_sections_id', '=', 'sys_users.hr_emp_sections_id');
        if($code){
            $sql->where('hr_emp_attendance.user_code',$code);
        }
        $result = $sql->get()->toArray();
        $result = array_map(function($item){
            return (array) $item;
        },$result);
        return $result;
    }

    private function getHrTemporaryEmployeeAttendanceRawData($code = NULL){
        $sql  = DB::table('hr_temporary_emp_attendance');
        $sql->select('hr_temporary_emp_attendance.*');
        if($code){
            $sql->where('hr_temporary_emp_attendance.user_code',$code);
        }
        $result = $sql->get()->toArray();
        $result=array_map(function($item){
            return (array) $item;
        },$result);
        return $result;
    }

    private function getMatchedEmployeeAttendanceDetails($code=NULL){
//      DB::enableQueryLog();
        $sql  = DB::table('hr_emp_attendance');
        $sql->select('hr_emp_attendance.*');
        $sql->join('hr_temporary_emp_attendance', 'hr_temporary_emp_attendance.day_is', '=', 'hr_emp_attendance.day_is');
        if($code){
            $sql->where('hr_temporary_emp_attendance.user_code',$code);
        }
        $sql->where(DB::raw('hr_emp_attendance.start_date_time'), '=', DB::raw('hr_temporary_emp_attendance.start_date_time'));
        $sql->where(DB::raw('hr_emp_attendance.end_date_time'), '=', DB::raw('hr_temporary_emp_attendance.end_date_time'));
        $result = $sql->get();
//      debug(DB::getQueryLog());
        return $result->toArray();
    }

    private function getHrEmployeeAttendance($id = NULL){
        $sql  = DB::table('hr_emp_attendance');
        $sql->select('hr_emp_attendance.*');
        if($id){
            $sql->where('hr_emp_attendance.hr_emp_attendance_id',$id);
        }
        $result = $sql->get()->toArray();
        return $result;
    }


    public function autoVoucherCall(){
        $start =1;
        for($start;$start <= 5000;$start++){
            $is_succeed = autoVoucherProcess('Sales','Sales Order','sales_order','1',NULL,'','Sales Order',NULL,NULL);
        }
        if($is_succeed){
            echo 'Action successfully done!';
        }else{
            echo "Autovoucher error found!!";
        }
    }

    //Active Employee
    public  function  activeEmployee($data){
        $sql  = DB::table('hr_emp_attendance')->where('in_time', '!=', null)->where('in_time', '!=', '');
        if(!empty($data->hr_salary_month_name)){
            $year = date('Y', strtotime($data->hr_salary_month_name));
            $month = date('m', strtotime($data->hr_salary_month_name));
            $sql->whereYear('day_is', $year);
            $sql->whereMonth('day_is', $month);
        }
        if (isset($data->hr_emp_categorys_id) && $data->hr_emp_categorys_id !=''){
            $sql->where('hr_emp_categorys_id', $data->hr_emp_categorys_id);
        }
        return $sql->get();
    }

}
