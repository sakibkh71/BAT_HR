<?php

namespace App\Http\Controllers\HrAttendance;

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

use App\Helpers\PdfHelper;

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class HrAttendanceReportController extends Controller {

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
     * Attendance Entry
     */
    public function dailyAttendanceSheet(Request $request, $type=null){
        $shift_disable = getOptionValue('is_shift_disable');

        $posts = $request->all();
        $data['posted'] = $posts;
        $isdate = !empty($request->date_is)?$request->date_is:date('Y-m-d');
        $data['posted']['date_is'] =  $isdate;

        $sql = DB::table('hr_emp_attendance')
            ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
            ->join('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->join('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->join('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id')
            ->join('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id')
            ->where('hr_emp_attendance.day_is',  $isdate)
            ->whereNotNull('hr_emp_attendance.daily_status')
            ->where('hr_emp_attendance.attn_status','Active')
            ->select(
                'sys_users.user_code as employee_code',
                'sys_users.name as name',
                'designations.designations_name as FF_type',
                'bat_distributorspoint.name as distributors_point',
                'hr_emp_attendance.route_number',
                'sys_users.date_of_join',
                'hr_emp_attendance.daily_status'
            );
        if (!$shift_disable){
            $sql->addSelect(
                'hr_working_shifts.shift_name',
                'hr_emp_attendance.shift_start_time as shift_start',
                'hr_emp_attendance.shift_end_time as shift_end'
            );
        }
        $sql->addSelect(
//            'hr_emp_attendance.in_time',
//            'hr_emp_attendance.out_time',
            DB::raw('CONCAT("") as Signature')
        );

        /*if ($type == null) {
            $sql->addSelect('sys_users.user_code', 'hr_emp_attendance.day_is', 'hr_emp_attendance.approved_status');
        }*/

        $session_con = (sessionFilter('url','daily-attendance-sheet'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $sql->whereRaw($session_con);
        }

        if (isset($posts['status']) && $posts['status'][0]!= null){
            $sql->whereIn('hr_emp_attendance.daily_status', $posts['status']);
        }

        if (!$shift_disable) {
            if (isset($posts['hr_working_shifts_id']) && $posts['hr_working_shifts_id'][0] != null) {
                $sql->whereIn('sys_users.hr_working_shifts_id', $posts['hr_working_shifts_id']);
            }
        }

        if (isset($posts['bat_dpid']) && $posts['bat_dpid'][0] != null) {
            $sql->whereIn('sys_users.bat_dpid', $posts['bat_dpid']);
        }

        if (isset($posts['bat_company_id']) && $posts['bat_company_id'][0] != null) {
            $sql->whereIn('sys_users.bat_company_id', $posts['bat_company_id']);
        }

        if (isset($posts['hr_emp_grades_id']) && $posts['hr_emp_grades_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_grades_id', $posts['hr_emp_grades_id']);
        }

        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_sections_id', $posts['hr_emp_sections_id']);
        }

        if (isset($posts['sys_users_id']) && $posts['sys_users_id'][0]!= null){
            $sql->whereIn('hr_emp_attendance.sys_users_id', $posts['sys_users_id']);
        }

        if ($type == null) {
            $report_data = $sql->paginate(15);
        }else{
            $report_data = $sql->get();
        }

        if(!empty($report_data)){
            foreach ($report_data as $row){
                $row->date_of_join = toDated($row->date_of_join);
                if (!$shift_disable) {
                    $row->shift_start = toTimed($row->shift_start);
                    $row->shift_end = toTimed($row->shift_end);
                }
//                $row->in_time = $row->in_time>0?(toDated($isdate)!=toDated($row->in_time)?toDateTimed($row->in_time):date('h:i A',strtotime($row->in_time))):'';
//                $row->out_time = $row->out_time>0?(toDated($isdate)!=toDated($row->out_time)?toDateTimed($row->out_time):date('h:i A',strtotime($row->out_time))):'';
            }
        }

        $data['report_data'] = $report_data;

        //dd( DB::getQueryLog() );
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'ID No.');
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'Name');
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'Designation');
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'Distributors Point');
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'DoJ');
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'Daily Status');

        if (!$shift_disable) {
            $data['complex_header'][] = array('row' => 2, 'text' => 'Shift Name');
            $data['complex_header'][] = array('row' => 0, 'col' => 2, 'text' => 'Shift Time');
        }

//        $data['complex_header'][] =  array('row' => 0, 'col' => 2, 'text'=>'Attendance Time');
        // $data['complex_header'][] =  array('row'=>2, 'text'=>'Overtime');
//        $data['complex_header'][] =  array('row'=>2, 'text'=>'Signature');


        if (!$shift_disable) {
            $data['table_header'][] = 'In Time';
            $data['table_header'][] = 'Out Time';
        }
//        $data['table_header'][] = 'In Time';
//        $data['table_header'][] = 'Out Time';

        if($type =='xlsx'){
            $data['date_is']= $isdate;
            $filename = self::attendanceExcel($data);
            return response()->json(['status'=>'success','file'=>$filename]);
        }elseif ($type =='pdf'){
            $data['report_title'] = 'Daily Attendance Report - '. toDated($isdate);
            $data['filename'] = 'daily_attendance_pdf';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.pdf_report_template';
            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('HrAttendance.report.daily_attendance_sheet', $data);
        }
    }


    /*
     * Job Card
     */
    public function jobCard(){
        return view('HrAttendance.report.jobcard');
    }

    /*
     * Job Card Data by ajax request
     */
    public function jobCardData(Request $request, $type=null){
        $data['psotdata'] = $request->except('_token');
        $code = $request->code;
        $shift_disable = getOptionValue('is_shift_disable');
        $userInfo = DB::table('sys_users')
            ->join('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id')
            ->select(
                'sys_users.id',
                'sys_users.user_code',
                'sys_users.name',
                'sys_users.date_of_join',
                'designations.designations_name',
                'bat_distributorspoint.name as distributors_point',
                'bat_company.company_name'
            )
            ->where('sys_users.user_code', $code)->first();

        if (!empty($userInfo)) {
            $sql = DB::table('hr_emp_attendance')
                ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
                ->join('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
                ->join('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id');

            if(!getOptionValue('is_shift_disable')){
                $sql->join('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id');
            }

            $sql->select(
                DB::raw("MONTH(day_is) as month"),
                DB::raw("YEAR(day_is) as year"),
                'hr_emp_attendance.*',
                'sys_users.username',
                'sys_users.date_of_join',
                'designations.designations_name',
                'bat_distributorspoint.name as distributors_point'
            );
            if (!$shift_disable) {
                if (!getOptionValue('is_shift_disable')) {
                    $sql->addSelect(
                        'hr_working_shifts.shift_name',
                        'hr_working_shifts.start_time as shift_start',
                        'hr_working_shifts.end_time as shift_end'
                    );
                }
            }
            $sql->whereNotNull('hr_emp_attendance.daily_status')
                ->where('sys_users.id', $userInfo->id);

            if(isset($request->date_range)){
                $range = explode(" - ", $request->date_range);
                $sql->whereBetween('hr_emp_attendance.day_is',$range);
            }

            if(isset($request->shift) && !empty($request->shift[0])){
                $sql->whereIn('hr_working_shifts.hr_working_shifts_id',$request->shift);
            }

            if(isset($request->daily_status) && !empty($request->daily_status[0])){
                $sql->whereIn('hr_emp_attendance.daily_status',explode(',',$request->daily_status[0]));
            }

            $session_con = (sessionFilter('url','daily-attendance-sheet'));
            $session_con = trim(trim(strtolower($session_con)),'and');
            if($session_con){
                $sql->whereRaw($session_con);
            }


            $rows = $sql->get();

            //dd(DB::getQueryLog());

            $attendance_rows = [];

            if (!empty($rows)){
                foreach ($rows as $item) {
                    $data_array['id']= $item->sys_users_id;
                    $data_array['day_is']= $item->day_is;
                    $data_array['daily_status']= $item->daily_status;
                    if(!getOptionValue('is_shift_disable')) {
                        $data_array['shift_name'] = $item->shift_name;
                        $data_array['shift_start_time'] = $item->shift_start_time;
                        $data_array['shift_end_time'] = $item->shift_end_time;
                    }
                    $data_array['in_time']= $item->in_time;
                    $data_array['out_time']=  $item->out_time;
                    //$data_array['ot_hours']=  $item->ot_hours;
                    $attendance_rows[$item->year][$item->month][]= $data_array;
                }
            }

            $data['attendance_rows']=$attendance_rows;
           // dd($data['attendance_rows']);

            if ($type==null){
                $attendance = view('HrAttendance.report.ajax-jobcard-data', $data)->render();
                return response()->json([
                    'status' => 'success',
                    'userInfo' => $userInfo,
                    'attendance' =>  $attendance //$attendance
                ]);
            }elseif($type=='pdf'){

                $data['report_title'] = 'Individual Attendance Report';
                $data['filename'] = 'daily_attendance_pdf';
                $data['orientation'] = "L";
                $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
                $data['signatures']=['Prepared by','Checked by','Approved by'];
                $data['userInfo'] = $userInfo;

                $view='HrAttendance.report.job_card_pdf';
                PdfHelper::exportPdf($view,$data);
            }
        }
        else{
            return response()->json([
                'status' => 'error',
                'userInfo' => null,
                'attendance' =>null
            ]);
        }
    }

    public function monthlyAttendanceSheet(Request $request,$type=null){
        $posts = $request->all();

        $report_month = isset($posts['month'])?$posts['month']:date('Y-m');
        $data['report_month'] = $report_month;
        $posts['month'] = $report_month;

        $report_type = isset($posts['report_type'])?$posts['report_type']:'without_ot_hours';
        $posts['report_type'] = $report_type;

        $status = isset($posts['status'])?$posts['status']: array('Active');
        $posts['status'] =  $status;

        $data['posted'] = $posts;

        if($type =='xlsx'){
            $attendance = self::attendanceSheetData($posts);
            $data['attendance_sheet'] = $attendance['attendance_data'];

            $filename = self::excelReport($data);
            return response()->json(['status'=>'success','file'=>$filename]);

        }elseif($type =='pdf'){
            $attendance = self::attendanceSheetData($posts);
            $data['attendance_sheet'] = $attendance['attendance_data'];
            $data['report_title'] = 'Monthly Attendance Sheet - '. date('M,Y',strtotime($report_month));
            $data['filename'] = 'monthly_attendance_sheet';
            $data['orientation'] = "L";
            $data['paper_size'] = "Legal";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HrAttendance.report.monthly_attendance_sheet_pdf';
            PdfHelper::exportPdf($view,$data);
        }else{
            $attendance = self::attendanceSheetData($posts,10);
            $data['attendance_sheet'] = $attendance['attendance_data'];
            $data['paginate_data']= $attendance['paginate_data'];
            return view('HrAttendance.report.monthly_attendance_sheet',$data);
        }
    }


    //Attendance Sheet
    function attendanceSheetData($posts, $limit=null)
    {
        $shift_disable = getOptionValue('is_shift_disable');
        $posts['month'] = isset($posts['month']) ? $posts['month'] : date('Y-m');
        $sql = DB::table('hr_emp_attendance')
            ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
            ->join('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->join('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->join('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id');

        if (!$shift_disable) {
            $sql->join('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id');
        }

        $sql->select(
            'sys_users.user_code',
            'sys_users.name as name',
            'designations.designations_name as designation',
            'sys_users.date_of_join',
            'hr_emp_attendance.day_is'
        );
        if (!$shift_disable){
            $sql->addSelect(
                'hr_emp_attendance.shift_start_time as shift_start',
                'hr_emp_attendance.shift_end_time as shift_end'
            );
        }
        $sql->addSelect(
            'hr_emp_attendance.in_time',
            'hr_emp_attendance.out_time',
            'hr_emp_attendance.daily_status'
        );

        $sql->where('sys_users.status','!=', 'Inactive');
        $sql->where('hr_emp_attendance.attn_status','Active');
        $session_con = (sessionFilter('url','daily-attendance-sheet'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $sql->whereRaw($session_con);
        }

        if (isset($posts['bat_dpid']) && $posts['bat_dpid'][0] != null) {
            $sql->whereIn('hr_emp_attendance.bat_dpid', $posts['bat_dpid']);
        }


        if (!$shift_disable) {
            if (isset($posts['hr_working_shifts_id']) && $posts['hr_working_shifts_id'][0] != null) {
                $sql->whereIn('hr_emp_attendance.hr_working_shifts_id', $posts['hr_working_shifts_id']);
            }
        }

        if (isset($posts['month']) && $posts['month']!= null){
            $sql->whereYear('hr_emp_attendance.day_is',date('Y',strtotime($posts['month'])));
            $sql->whereMonth('hr_emp_attendance.day_is',date('m',strtotime($posts['month'])));
        }

        if (isset($posts['designations_id']) && $posts['designations_id'][0]!= null){
            $sql->whereIn('sys_users.designations_id', $posts['designations_id']);
        }

        if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
        }

        if($limit !=null){
            $dt =  explode("-",$posts['month']);
            $d = cal_days_in_month(CAL_GREGORIAN, $dt[1],$dt[0]);
            $getdata = $sql->paginate($limit * $d);
        }else{
            $getdata = $report_data = $sql->get();
        }
        $report_data = $getdata;
        $employee_list = [];

        foreach ($report_data as $emp){
            $employee_list[$emp->user_code]['user_code'] = $emp->user_code;
            $employee_list[$emp->user_code]['name'] = $emp->name;
            $employee_list[$emp->user_code]['designation'] = $emp->designation;
            $employee_list[$emp->user_code]['date_of_join'] = $emp->date_of_join;
            $employee_list[$emp->user_code]['daily_status'] = $emp->daily_status;
        }

        $attendance_data = [];

        if($limit !=null){
            $report_data = $report_data->toArray();
            $report_data = $report_data['data'];
        }else{
            $report_data = $report_data->toArray();
        }

        foreach ($employee_list as $key=>$employee){
            $emp_data=[];
            foreach($report_data as $arr){
                if($arr->user_code == $key){
                    $emp_data[intval(date('d',strtotime($arr->day_is)))] = array(
                        'daily_status'=>$arr->daily_status,
                        'day_is'=>$arr->day_is,
                        'in_time'=>$arr->in_time,
                        'out_time'=>$arr->out_time,
                    );
                }
            }
            $employee['daily'] = $emp_data;
            $attendance_data[] = $employee;
        }

        return ['paginate_data'=> $getdata, 'attendance_data'=>$attendance_data];
    }

    /*
     * Excel Export
     */
    private function excelReport($data){

        $filename = 'attendance_sheet'.Auth::user()->id.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Attendance sheet ('. $data['report_month'] .')');
        $header = array();
        $header[] = 'Id no';
        $header[] = 'Name';
        $header[] = 'Designation';
        $header[] = 'DoJ';
        $header[] = 'Days of Month';
        $header[] = 'Present';
        $header[] = 'Leave';
        $header[] = 'Holidays';
        $header[] = 'Absent';
        $header[] = 'Payable Days';
        if($data['posted']['report_type'] !='without_ot_hours'){
            $header[] = 'Over Time';
        }
        for($d=1;$d<=date('t',strtotime($data['report_month']));$d++){
            $header[] = $d;
        }
        $header[] = 'Signature';

        $number = 0;

        $row = 1;
        exportHelper::getCustomCell($sheet, 1, 0, getOptionValue('company_name'), count($header) - 1, null, 18, 'center');
        exportHelper::getCustomCell($sheet, 2, 0, 'Monthly attendance sheet ('. $data['report_month'] .')', count($header) - 1, null, 16, 'center');

        $row = 3;
        exportHelper::get_column_title($number, $row, $header, $sheet);

        $row = 4;
        if (isset($data['attendance_sheet'])){
            $marge_row = $data['posted']['report_type'] =='all_components' ? 2 : null;

            foreach ($data['attendance_sheet'] as $employee) {
                //exportHelper::getCustomCell($sheet,$row,$col,$val,$colspan=null,$rowspan=null,$font_size=11,$align='left',$bold=false);
                $att_arr = array();
                for($d=1;$d<=date('t',strtotime($data['report_month'] ));$d++) {
                    $att_arr[] = isset($employee["daily"][$d]) ? $employee["daily"][$d]["daily_status"] : 'NA';
                    $attArray = array_count_values(array_filter($att_arr));
                    $number_of_day = date('t', strtotime($data['report_month'] ));
                    $absent = array_key_exists('A', $attArray) ? $attArray['A'] : 0;
                    $present = array_key_exists('P', $attArray) ? $attArray['P'] : 0;
                    $leave = array_key_exists('LV', $attArray) ? $attArray['LV'] : 0;
                    $holiday = array_key_exists('H', $attArray) ? $attArray['H'] : 0;
                }
                exportHelper::getCustomCell($sheet,$row,0,$employee['user_code'],0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,1,$employee['name'],0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,2,$employee['designation'],0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,3,toDated($employee['date_of_join']),0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,4,$number_of_day,0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,5,$present,0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,6,$leave,0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,7,$holiday,0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,8,$absent,0,$marge_row);
                exportHelper::getCustomCell($sheet,$row,9,intval($number_of_day-$absent),0,$marge_row);

                if($data['posted']['report_type'] !='without_ot_hours'){
                    exportHelper::getCustomCell($sheet,$row,10,'',0,$marge_row);
                    $cel = 11;
                }else{
                    $cel = 10;
                }
                if($data['posted']['report_type'] =='all_components'){
                    for($d=1;$d<=date('t',strtotime($data['report_month']));$d++) {
                        if (isset($employee['daily'][$d])) {
                            exportHelper::getCustomCell($sheet, $row, $cel, $employee['daily'][$d]['daily_status']);
                            exportHelper::getCustomCell($sheet, $row+1, $cel, isset($employee['daily'][$d]['in_time'])&&!empty($employee['daily'][$d]['in_time'])?toTimed($employee['daily'][$d]['in_time']):'');
                            exportHelper::getCustomCell($sheet, $row+2, $cel, isset($employee['daily'][$d]['out_time'])&&!empty($employee['daily'][$d]['out_time'])?toTimed($employee['daily'][$d]['out_time']):'');
                        }
                        $cel++;
                    }
                    $row += 2;
                }else{
                    for($d=1;$d<=date('t',strtotime($data['report_month']));$d++){
                        if(isset($employee['daily'][$d])){
                            exportHelper::getCustomCell($sheet, $row, $cel, $employee['daily'][$d]['daily_status']);
                        }
                        $cel++;
                    }
                }
                $row++;
            }
        }

        exportHelper::excelHeader($filename,$spreadsheet);
        return $filename;
    }


    /*
     * Punch Missing Report
     */
    public  function punchMissingReport(Request $request, $type = null){
        $shift_disable = getOptionValue('is_shift_disable');
        $posts = $request->all();

        $posts['month'] = isset($posts['month'])?$posts['month']:null;
        $posts['day'] = isset($posts['day'])?$posts['day']:( $posts['month'] == null ? date('Y-m-d'):'');

        $punch_type = isset($posts['punch_type'])?$posts['punch_type']:'inpunch_missing';
        $posts['punch_type'] = $punch_type;

        $report_type = isset($posts['report_type'])?$posts['report_type']:'daily';
        $posts['report_type'] = $report_type;

        $data['posted'] = $posts;


        $sql = DB::table('hr_emp_attendance')
            ->join('sys_users', 'hr_emp_attendance.sys_users_id', '=', 'sys_users.id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id');

        if (!$shift_disable) {
            $sql->join('hr_working_shifts', 'sys_users.hr_working_shifts_id', '=', 'hr_working_shifts.hr_working_shifts_id');
        }

            //->leftJoin('hr_monthly_salary_wages', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id')
        $sql->select(
                'sys_users.user_code',
                'sys_users.name as name',
                'designations.designations_name as designation',
                'sys_users.date_of_join',
                'hr_emp_attendance.daily_status',
                'hr_emp_attendance.day_is'
        );

        if (!$shift_disable){
            $sql->addSelect(
                'hr_working_shifts.shift_name',
                'hr_emp_attendance.shift_start_time as shift_start',
                'hr_emp_attendance.shift_end_time as shift_end'
            );
        }
        $sql->addSelect(
            'hr_emp_attendance.in_time',
            'hr_emp_attendance.out_time',
            DB::raw("CONCAT('') as signature")
        )
        ->where('sys_users.status', 'Active');

        $sql->whereNotNull('hr_emp_attendance.daily_status');

        if (isset($posts['branchs_id']) && $posts['branchs_id'][0]!= null){
            $sql->whereIn('sys_users.branchs_id', $posts['branchs_id']);
        }

        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_sections_id']) && $posts['hr_emp_sections_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_sections_id', $posts['hr_emp_sections_id']);
        }

        if (isset($posts['departments_id']) && $posts['departments_id'][0]!= null){
            $sql->whereIn('sys_users.departments_id', $posts['departments_id']);
        }
        if (isset($posts['designations_id']) && $posts['designations_id'][0]!= null){
            $sql->whereIn('sys_users.designations_id', $posts['designations_id']);
        }

        if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
        }

        if($report_type == 'monthly'){
            $sql->whereYear('hr_emp_attendance.day_is',date('Y',strtotime($posts['month'])));
            $sql->whereMonth('hr_emp_attendance.day_is',date('m',strtotime($posts['month'])));
        }else{
            $sql->where('hr_emp_attendance.day_is', $posts['day']);
        }

        if ($punch_type == 'inpunch_missing'){
            $sql->where('hr_emp_attendance.in_time', '=', null);
        }elseif($punch_type == 'outpunch_missing'){
            $sql->where('hr_emp_attendance.out_time', '=', null);
        }

        $sql->groupBy('hr_emp_attendance.day_is');
        $sql->groupBy('sys_users.user_code');

        if ($type =='pdf' || $type =='xlsx' ){
            $report_data = $sql->get();
        }else{
            $report_data = $sql->paginate(30);
        }

        if(!empty($report_data)){
            foreach ($report_data as $row){
                $row->date_of_join = toDated($row->date_of_join);
                $row->day_is = toDated($row->day_is);
                if (!$shift_disable) {
                    $row->shift_start = date('h:i:s A', strtotime($row->shift_start));
                    $row->shift_end = date('h:i:s A', strtotime($row->shift_end));
                }

                $row->in_time = $row->in_time !=null ? (date('Y-m-d',strtotime($row->in_time)) == $row->day_is ? date('h:i:s A',strtotime($row->in_time)): toDateTimed($row->in_time)):'';
                $row->out_time = $row->out_time !=null ? (date('Y-m-d',strtotime($row->out_time)) == $row->day_is ? date('h:i:s A',strtotime($row->out_time)): toDateTimed($row->out_time)):'';
            }
        }

        $data['report_data'] = $report_data;
        $data['complex_header'][] = array('row'=>2, 'text'=>'ID No.');
        $data['complex_header'][] = array('row'=>2, 'text'=>'Name');
        $data['complex_header'][] = array('row'=>2, 'text'=>'Designation');
        $data['complex_header'][] = array('row'=>2, 'text'=>'DoJ');
        $data['complex_header'][] = array('row' => 2, 'text' => 'Daily  Status');
        $data['complex_header'][] = array('row' => 2, 'text' => 'Date');

        if (!$shift_disable) {
            $data['complex_header'][] = array('row' => 2, 'text' => 'Shift Info');
            $data['complex_header'][] = array('row' => 2, 'col' => 2, 'text' => 'Shift Time');
        }
        $data['complex_header'][] = array('row'=>0, 'col' => 2, 'text'=>'Attendance Time');

        $data['complex_header'][] = array('row'=>2, 'text'=>'Signature');

        if (!$shift_disable) {
            $data['table_header'][] = 'In Time';
            $data['table_header'][] = 'Out Time';
        }
        $data['table_header'][] = 'In Time';
        $data['table_header'][] = 'Out Time';

        $data['title'] = ucwords($posts['report_type']) .' '. ucwords(str_replace("_"," ", $posts['punch_type'])) .' Report';

        if($type =='xlsx'){
            $data['filename'] = 'punch_missing_report'.Auth::user()->id.'.xlsx';
            $filename = self::excelPunchMissingReport($data);
            return response()->json(['status'=>'success','file'=>$filename]);
        }elseif ($type =='pdf'){
            $data['report_title'] = $data['title'] . '-'. toDated(date('Y-m-d'));
            $data['filename'] = 'punch_missing_report';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.pdf_report_template';
            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('HrAttendance.report.punch-missing-report', $data);
        }

    }


    /*
      * Excel Export
      */
    private function excelPunchMissingReport($data)
    {
        $shift_disable = getOptionValue('is_shift_disable');

        $filename = 'punch_missing_report'.Auth::user()->id.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet 01');

        $number = 0;
        $row = 1;

        //getCustomCell($sheet,$row,$col,$val,$colspan=false,$rowspan=false,$font_size=11,$align='left',$bold=false)
        exportHelper::getCustomCell($sheet, 1, 0, getOptionValue('company_name'),12, null, 18, 'center');
        exportHelper::getCustomCell($sheet, 2, 0, $data['title'], 12, null, 16, 'center');

        $row = 3;
        exportHelper::getCustomCell($sheet, 3, 0, 'Id No.', 0, 1, 12, 'center', true);
        exportHelper::getCustomCell($sheet, 3, 1, 'Name', 0, 1, 12, 'center', true);
        exportHelper::getCustomCell($sheet, 3, 2, 'Designation', 0, 1, 12, 'center', true);
        exportHelper::getCustomCell($sheet, 3, 3, 'DoJ', 0, 1, 12, 'center', true);
        exportHelper::getCustomCell($sheet, 3, 4, 'Daily  Status', 0, 1, 12, 'center', true);
        exportHelper::getCustomCell($sheet, 3, 5, 'Date', 0, 1, 12, 'center', true);

        if(!$shift_disable){
            exportHelper::getCustomCell($sheet, 3, 6, 'Shift Info', 0, 1, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 3, 7, 'Shift Time', 1, 0, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 3, 9, 'Attendance Time', 1, 0, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 3, 11, 'Signature', 0, 1, 12, 'center', true);
        }else{
            exportHelper::getCustomCell($sheet, 3, 6, 'Attendance Time', 1, 0, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 3, 8, 'Signature', 0, 1, 12, 'center', true);
        }
        if(!$shift_disable){
            exportHelper::getCustomCell($sheet, 4, 7, 'In Time', null, null, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 4, 8, 'Out Time', null, null, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 4, 9, 'In Time', null, null, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 4, 10, 'Out Time', null, null, 12, 'center', true);
        }else{
            exportHelper::getCustomCell($sheet, 4, 6, 'In Time', null, null, 12, 'center', true);
            exportHelper::getCustomCell($sheet, 4, 7, 'Out Time', null, null, 12, 'center', true);
        }

        $row = 5;
        if (isset($data['report_data'])){
            foreach ($data['report_data'] as $item) {
                $sheet->setCellValue(exportHelper::get_letter(0).$row, $item->user_code);
                $sheet->setCellValue(exportHelper::get_letter(1).$row, $item->name);
                $sheet->setCellValue(exportHelper::get_letter(2).$row, $item->designation);
                $sheet->setCellValue(exportHelper::get_letter(3).$row, $item->date_of_join);
                $sheet->setCellValue(exportHelper::get_letter(4).$row, $item->daily_status);
                $sheet->setCellValue(exportHelper::get_letter(5).$row, $item->day_is);

                if(!$shift_disable) {
                    $sheet->setCellValue(exportHelper::get_letter(6) . $row, $item->shift_name);
                    $sheet->setCellValue(exportHelper::get_letter(7) . $row, $item->shift_start);
                    $sheet->setCellValue(exportHelper::get_letter(8) . $row, $item->shift_end);
                    $sheet->setCellValue(exportHelper::get_letter(9) . $row, $item->in_time);
                    $sheet->setCellValue(exportHelper::get_letter(10) . $row, $item->out_time);
                    $sheet->setCellValue(exportHelper::get_letter(11).$row, '');
                }else{
                    $sheet->setCellValue(exportHelper::get_letter(6) . $row, $item->in_time);
                    $sheet->setCellValue(exportHelper::get_letter(7) . $row, $item->out_time);
                    $sheet->setCellValue(exportHelper::get_letter(9).$row, '');
                }


                $row++;
            }
        }

        exportHelper::excelHeader($filename,$spreadsheet);
        return $filename;
    }

    /*
     * Punch In Out Report
     */
    public function punchInOutReport(Request $request, $type=null){ }


    /*
     * Excel Daily Attendance Report
     */
    private function attendanceExcel($data){
        $filename = 'daily_attendance_sheet'.Auth::user()->id.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daily Attendance sheet');
        $marge = !getOptionValue('is_shift_disable')?8:6;
        //set sheet header
        exportHelper::getCustomCell($sheet, 1, 0, getOptionValue('company_name'),$marge, null, 18, 'center');
        exportHelper::getCustomCell($sheet, 2, 0, 'Daily attendance sheet - ('.apsisDate($data['date_is']).')', $marge, null, 16, 'center');

        //set table header
        exportHelper::getCustomCell($sheet,3,0,'ID No.',0,1);
        exportHelper::getCustomCell($sheet,3,1,'Name',0,1);
        exportHelper::getCustomCell($sheet,3,2,'Designation',0,1);
        exportHelper::getCustomCell($sheet,3,3,'Distributors Point',0,1);
        exportHelper::getCustomCell($sheet,3,4,'DoJ',0,1);
        exportHelper::getCustomCell($sheet,3,5,'Daily Status',0,1);

        if (!getOptionValue('is_shift_disable')) {
            exportHelper::getCustomCell($sheet,3,6,'Shift Name',0,1);
            exportHelper::getCustomCell($sheet,3,7,'Shift Time',1,0);
            exportHelper::getCustomCell($sheet,3,9,'Attendance Time',1,0);
        }else{
//            exportHelper::getCustomCell($sheet,3,6,'Attendance Time',1,0);
        }

        if (!getOptionValue('is_shift_disable')) {
            exportHelper::getCustomCell($sheet,4,7,'In Time');
            exportHelper::getCustomCell($sheet,4,8,'Out Time');
            exportHelper::getCustomCell($sheet,4,9,'In Time');
            exportHelper::getCustomCell($sheet,4,10,'Out Time');
        }else{
//            exportHelper::getCustomCell($sheet,4,6,'In Time');
//            exportHelper::getCustomCell($sheet,4,7,'Out Time');
        }

        $row = 5;
        if (isset($data['report_data'])){
            foreach ($data['report_data'] as $item) {
                //exportHelper::getCustomCell($sheet,$row,$col,$val,$colspan=null,$rowspan=null,$font_size=11,$align='left',$bold=false);
                exportHelper::getCustomCell($sheet,$row,0,$item->employee_code);
                exportHelper::getCustomCell($sheet,$row,1,$item->name);
                exportHelper::getCustomCell($sheet,$row,2,$item->FF_type);
                exportHelper::getCustomCell($sheet,$row,3,$item->distributors_point);
                exportHelper::getCustomCell($sheet,$row,4,toDated($item->date_of_join));
                exportHelper::getCustomCell($sheet,$row,5,$item->daily_status);

                if (!getOptionValue('is_shift_disable')) {
                    exportHelper::getCustomCell($sheet,$row,6,$item->shift_name);
                    exportHelper::getCustomCell($sheet,$row,7,$item->shift_start);
                    exportHelper::getCustomCell($sheet,$row,8,$item->shift_end);
                    exportHelper::getCustomCell($sheet,$row,9,$item->in_time);
                    exportHelper::getCustomCell($sheet,$row,10,$item->out_time);
                }else{
//                     exportHelper::getCustomCell($sheet,$row,6,$item->in_time);
//                    exportHelper::getCustomCell($sheet,$row,7,$item->out_time);
                }
                $row++;
            }
        }
        exportHelper::excelHeader($filename,$spreadsheet);
        return $filename;
    }

}
