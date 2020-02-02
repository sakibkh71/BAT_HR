<?php
namespace App\Http\Controllers\EmployeeReport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Nexmo\Response;
use Redirect;
use Auth;
use App\Helpers\PdfHelper;


//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use exportHelper;

class EmployeeReport extends Controller
{
    public $data = [];

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
     * Active Inactive Employee List
     */
    public function employeeList(Request $request, $type=null){
        $posts = $request->all();
        $data['posted'] = $posts;
        $isdate = !empty($request->date_is)?$request->date_is:date('Y-m-d');
        $data['posted']['date_is'] =  $isdate;

        $status = !empty($request->status)?$request->status:array('Active');
        $data['posted']['status'] = $status;

        $pic_status = !empty($request->pic)?$request->pic:'no';
        $data['posted']['pic'] = $pic_status;


        $sql =  DB::table('sys_users')->select('sys_users.user_code');

            if ($pic_status =='yes'){
                $sql->addSelect('sys_users.user_image as picture');
            }

            $sql->addSelect(
                'sys_users.name',
                'designations.designations_name',
                'hr_emp_grades.hr_emp_grade_name',
                'sys_users.date_of_join',
                'sys_users.blood_group',
                'sys_users.gender',
//                'hr_emp_categorys.hr_emp_category_name',
                'bat_distributorspoint.name as point_name',
//                'hr_emp_units.hr_emp_unit_name',
//                'departments.departments_name',
//                'hr_emp_sections.hr_emp_section_name',
//                'sys_users.attendance_bonus',
                'sys_users.basic_salary'
            )
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->where('is_employee', 1);

        $session_con = (sessionFilter('url','employee-list'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $sql->whereRaw($session_con);
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

        if (isset($posts['branchs_id']) && $posts['branchs_id'][0]!= null){
            $sql->whereIn('sys_users.branchs_id', $posts['branchs_id']);
        }

        if (isset($posts['hr_emp_categorys_id']) && $posts['hr_emp_categorys_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys_id']);
        }

        if (isset($posts['status']) && $posts['status'][0]!= null){
            $sql->whereIn('sys_users.status', $posts['status']);
        }

        if ($type =='pdf') {
            $report_data = $sql->get();
        }else{
            $report_data = $sql->paginate(30);
        }

        if(!empty($report_data)){
            foreach ($report_data as $row){
                $row->date_of_join = toDated($row->date_of_join);

                if ($pic_status =='yes'){
                    $imgurl = !empty($row->picture)&&file_exists('public/img/'.$row->picture)? url('public/img/'.$row->picture) : asset('public/img/default-user.jpg');
                    $row->picture = '<img src="'.$imgurl.'"  style="max-height:60px">';
                }

                $row->remarks = '';
            }
        }
        $data['report_data'] = $report_data;
        /**/
        if ($pic_status =='yes'){
            $sql->addSelect('sys_users.user_image as picture');
        }

        //dd( DB::getQueryLog() );
        $data['complex_header'][] = array('text'=>'ID No.');

        if ($pic_status =='yes'){
            $data['complex_header'][] = array('text'=>'Picture');
        }

        $data['complex_header'][] = array('text'=>'Name');
        $data['complex_header'][] = array('text'=>'Designation');
        $data['complex_header'][] = array('text'=>'Grade');
        $data['complex_header'][] = array('text'=>'DoJ');
        $data['complex_header'][] = array('text'=>'Blood Group');
        $data['complex_header'][] = array('text'=>'Gender');
//        $data['complex_header'][] = array('text'=>'Staff Category');
        $data['complex_header'][] = array('text'=>'Distributor Point');
//        $data['complex_header'][] = array('text'=>'Unit');
//        $data['complex_header'][] = array('text'=>'Department');
//        $data['complex_header'][] = array('text'=>'Section');
//        $data['complex_header'][] = array('text'=>'Attendance Bonus');
        $data['complex_header'][] = array('text'=>'Salary');
        $data['complex_header'][] = array('text'=>'Remarks');

        $data['table_header'] = array();

        if ($type =='pdf'){
            $data['report_title'] = implode(" ,",$status) .' Employee List - '. toDated($isdate);
            $data['filename'] = 'active_employee_list_pdf';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.pdf_report_template';
            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('Employee_report.employee_list_report', $data);
        }
    }


    /*
     * New Joining status ( Unit & Catagori wise)
     */
    public function newJoiningStatus(Request $request, $type=null){
        $posts = $request->all();
        //dd( $posts);
        $data['posted'] = $posts;
        $join_date = isset($posts['hr_join_month'])?$posts['hr_join_month']:date('Y-m');
        $data['posted']['hr_join_month'] =  $join_date;

        $sql =  DB::table('sys_users')
            ->select(
                'sys_users.id',
                'sys_users.name',
                'designations.designations_name',
                'hr_emp_grades.hr_emp_grade_name',
                'sys_users.date_of_join',
                'sys_users.blood_group',
                'bat_distributorspoint.name as point_name',
//                'hr_emp_units.hr_emp_unit_name',
//                'departments.departments_name',
//                'hr_emp_sections.hr_emp_section_name',
                //'hr_emp_categorys.hr_emp_category_name',
//                'sys_users.attendance_bonus as right_attendance_bonus',
                'sys_users.basic_salary as right_basic_salary'
            )
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->where('is_employee', 1);
        $session_con = (sessionFilter('url','new-joining-status'));
        $session_con = trim(trim(strtolower($session_con)),'and');
        if($session_con){
            $sql->whereRaw($session_con);
        }
        $sql->whereMonth('sys_users.date_of_join',date('m',strtotime($join_date)));
        $sql->whereYear('sys_users.date_of_join',date('Y',strtotime($join_date)));

        if (isset($posts['hr_emp_units_id']) && $posts['hr_emp_units_id'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_units_id', $posts['hr_emp_units_id']);
        }

        if (isset($posts['hr_emp_categorys']) && $posts['hr_emp_categorys'][0]!= null){
            $sql->whereIn('sys_users.hr_emp_categorys_id', $posts['hr_emp_categorys']);
        }

        $report_data = $sql->get();

        if(!empty($report_data)){
            foreach ($report_data as $row){
                $row->date_of_join = toDated($row->date_of_join);
                $row->remarks = '';
            }
        }
        $data['report_data'] = $report_data;

        //dd( DB::getQueryLog() );
        $data['complex_header'] = array(
            array(
                'text'=>'ID No.'
            ),array(
                'text'=>'Name'
            ),array(
                'text'=>'Designation'
            ),array(
                'text'=>'Grade'
            ),array(
                'text'=>'DoJ'
            ),array(
                'text'=>'Blood Group'
            ),
            array(
                'text'=>'Distributor Point'
            ),
//            array(
//                'text'=>'Unit'
//            ),array(
//                'text'=>'Department'
//            ),
            /*array(
                'text'=>'Staff Category'
            ),*/
//            array(
//                'text'=>'Section'
//            ),array(
//                'text'=>'Attendance Bonus'
//            ),
            array(
                'text'=>'Salary'
            ),array(
                'text'=>'Remarks'
            )
        );
        $data['table_header'] = array();

        if ($type =='pdf'){
            $data['report_title'] =' New Joining Status  of '.  date("M, Y", strtotime($join_date));
            $data['filename'] = 'new_joining_status_report_pdf';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.pdf_report_template';
            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('Employee_report.new_joining_report', $data);
        }
    }


    /*
    * Employee List with all components
    */
     public function employeeListAllComponents(Request $request, $reqtype=null){
        $posts = $request->all();
        $data['posted'] = $posts;

        $status = !empty($request->status)?$request->status:array('Active');
        $data['posted']['status'] = $status;

        $sql =  DB::table('sys_users')
            ->select(
                'sys_users.user_code as id_no',
                'sys_users.name',
                'designations.designations_name as designations',
                'hr_emp_grades.hr_emp_grade_name as grade',
                'sys_users.date_of_join as doj',
                'sys_users.blood_group',
                'bat_distributorspoint.name as point_name',
//                'hr_emp_units.hr_emp_unit_name as unit',
//                'departments.departments_name as department',
//                'hr_emp_sections.hr_emp_section_name as section',
//                'hr_emp_categorys.hr_emp_category_name as staff_category',
                'sys_users.name_bangla as employee_name_bangla',
                'sys_users.date_of_birth',
                'sys_users.gender',
                'sys_users.marital_status',
                'hr_emp_nominees.nominee_name',
                'sys_users.father_name',
                'sys_users.mother_name',
                 DB::raw("CONCAT(sys_users.address, '<br>', sys_users.present_village, '<br>', sys_users.present_po, '(', sys_users.present_post_code, ')', sys_users.present_thana, '<br>', sys_users.present_district) AS present_address"),
                 DB::raw("CONCAT(sys_users.permanent_village, '<br>', sys_users.permanent_po, '(', sys_users.permanent_post_code, ')', sys_users.permanent_thana, '<br>', sys_users.permanent_district) AS permanent_address"),
                'sys_users.religion',
                'sys_users.nationality',
                'sys_users.nid as nid',
                'sys_users.mobile as contact_no.',
                'sys_users.basic_salary',
                'hr_emp_bank_accounts.bank_name',
                'hr_emp_bank_accounts.account_number'
            )
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('hr_emp_nominees', 'sys_users.id', '=', 'hr_emp_nominees.sys_users_id')
            ->leftJoin('bat_distributorspoint', 'sys_users.bat_dpid', '=', 'bat_distributorspoint.id')
            ->leftJoin('hr_emp_bank_accounts', function($join){
                $join->on('sys_users.id', '=', 'hr_emp_bank_accounts.sys_users_id');
                $join->on('hr_emp_bank_accounts.bank_account_types_name', '=', DB::raw("'Salary Account'"));
            })
            ->where('is_employee', 1)
            ->whereIn('sys_users.status', $status);

         $session_con = (sessionFilter('url','employee-list-with-all-components'));
         $session_con = trim(trim(strtolower($session_con)),'and');
         if($session_con){
             $sql->whereRaw($session_con);
         }

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

        if ($reqtype =='pdf' || $reqtype =='xlsx' ){
            $report_data = $sql->groupBy('sys_users.user_code')->get();
        }else{
            $report_data = $sql->groupBy('sys_users.user_code')->paginate(10);
        }

        if(!empty($report_data)){
            foreach ($report_data as $row){
                $row->date_of_birth = toDated($row->date_of_birth);
                $row->doj = toDated($row->doj);
                $row->remarks = '';
            }
        }

        $data['report_data'] = $report_data;
        $data['complex_header'] = array(
            array(
                'text'=>'ID No.'
            ),array(
                'text'=>'Name'
            ),array(
                'text'=>'Designation'
            ),array(
                'text'=>'Grade'
            ),array(
                'text'=>'DoJ'
            ),array(
                'text'=>'Blood Group'
            ),array(
                'text'=>'Distributor Point'
            ),array(
                'text'=>'Employee name Bangla'
            ),array(
                'text'=>'Date of Birth'
            ),array(
                'text'=>'Gender'
            ),array(
                'text'=>'Marital Status'
            ),array(
                'text'=>'Nominee Name'
            ),array(
                'text'=>'Father Name'
            ),array(
                'text'=>'Mother Name'
            ),array(
                 'text'=>'Present address'
             ),array(
                'text'=>'Permanent address'
            ),array(
                'text'=>'Religion'
            ),array(
                'text'=>'Nationality'
            ),array(
                'text'=>'NID/Birth Certificate'
            ),array(
                'text'=>'Contact No'
            ),array(
                'text'=>'Basic Salary'
            ),array(
                'text'=>'Bank Name'
            ),array(
                 'text'=>'Account no'
            ),array(
                'text'=>'Remarks'
            )
        );
        $data['table_header'] = array();

        if($reqtype =='xlsx'){
            $data['filename'] = 'employee_list_all_components'.Auth::user()->id.'.xlsx';
            $data['title'] = 'Employee List';
            $filename = self::excelReport($data);
            return response()->json(['status'=>'success','file'=>$filename]);
        }elseif ($reqtype =='pdf'){
            $data['report_title'] = 'Employee List with all components - '. toDated(date('Y-m-d'));
            $data['filename'] = 'Employee_List_with_all_components_pdf';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.pdf_report_template';
            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('Employee_report.employee_list_all-components_report', $data);
        }

     }


    /*
     * Staff Off day summary(Monthly)
     * with amount / without amount
     */
    public function employeeOffDaySummary(Request $request, $type=null){
        $posts = $request->all();
        $data['posted'] = $posts;

        $status = !empty($request->status)?$request->status:array('Active');
        $data['posted']['status'] = $status;

        $month = !empty($request->off_day_month)?$request->off_day_month:date('Y-m');
        $data['posted']['off_day_month'] = $month;

        $amount_status = !empty($request->amount_status)?$request->amount_status:'without_amount';
        $data['posted']['amount_status'] = $amount_status;

       // DB::connection()->enableQueryLog();

        $sql =  DB::table('hr_emp_attendance')
            ->select(
                'sys_users.user_code as id_no',
                'sys_users.name',
                'designations.designations_name as designations',
                'hr_emp_grades.hr_emp_grade_name as grade',
                'sys_users.date_of_join as doj',
                'hr_emp_categorys.hr_emp_category_name as staff_category',
                'hr_emp_units.hr_emp_unit_name as unit',
                'departments.departments_name as department',
                'hr_emp_sections.hr_emp_section_name as section',
                'hr_monthly_salary_wages.gross as salary'
            )
            ->where('is_employee', 1)
            ->whereIn('sys_users.status', $status);

        if ( $amount_status == 'with_amount'){
            $sql->addSelect(
                'hr_monthly_salary_wages.offday_ot_rate as amount_per_hour'
            );
        }

        $sql->addSelect(
            'hr_monthly_salary_wages.offday_ot_hours as days'
        );

        if ( $amount_status == 'with_amount'){
            $sql->addSelect(
                'hr_monthly_salary_wages.offday_ot_payable as total_amount'
            );
        }

        $sql->addSelect(
            DB::raw("CONCAT('') AS remarks")
        );

        $sql ->join('sys_users', 'sys_users.id', '=', 'hr_emp_attendance.sys_users_id')
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('hr_monthly_salary_wages', 'sys_users.id', '=', 'hr_monthly_salary_wages.sys_users_id')
            ->where('sys_users.is_employee', 1)
            ->whereIn('hr_emp_attendance.daily_status', ['WP','HP'])
            ->where('hr_monthly_salary_wages.hr_salary_month_name', $month);

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

        $sql->groupBy('sys_users.user_code');


        if ($type =='pdf' || $type =='xlsx' ){
            $report_data = $sql->get();
        }else{
            $report_data =  $sql->paginate(15);
        }
        //dd(DB::getQueryLog());

        if(!empty($report_data)){
            foreach ($report_data as $row){
                $row->doj = toDated($row->doj);
            }
        }

        $data['report_data'] = $report_data;
        $data['complex_header'][] = array('text'=>'ID No.');
        $data['complex_header'][] = array('text'=>'Name');
        $data['complex_header'][] = array('text'=>'Designation');
        $data['complex_header'][] = array('text'=>'Grade');
        $data['complex_header'][] = array('text'=>'DoJ');
        $data['complex_header'][] = array('text'=>'Staff Category');
        $data['complex_header'][] = array('text'=>'Unit');
        $data['complex_header'][] = array('text'=>'Department');
        $data['complex_header'][] = array('text'=>'Section');
        $data['complex_header'][] = array('text'=>'Salary');

        if ( $amount_status == 'with_amount') {
            $data['complex_header'][] = array('text'=>'Amount per day');
            $data['complex_header'][] = array('text'=>'Hours');
            $data['complex_header'][] = array('text' => 'Total Amount');
            $data['complex_header'][] = array('text' => 'Signature');
        }else{
            $data['complex_header'][] = array('text'=>'Hours');
            $data['complex_header'][] = array('text' => 'Remarks');
        }

        $data['table_header'] = array();

        if($type =='xlsx'){
            $data['filename'] = 'staff_off_day_summary'.Auth::user()->id.'.xlsx';
            $data['title'] = 'Off day summary ('. date("M, Y", strtotime($month)).')';
            $filename = self::excelReport($data);
            return response()->json(['status'=>'success','file'=>$filename]);
        }elseif ($type =='pdf'){
            $data['report_title'] = 'Off day summary ('. date("M, Y", strtotime($month)).')';
            $data['filename'] = 'staff_off_day_summary';
            $data['orientation'] = "L";
            $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
            $data['signatures']=['Prepared by','Checked by','Approved by'];
            $view='HR.pdf_report_template';
            PdfHelper::exportPdf($view,$data);
        }else{
            $data['report_data_html'] = view('HR.report_template',$data);
            return view('Employee_report.employee-off-day-summary', $data);
        }

    }


    /*
     * Appointment Letter
     */
    public function AppointmentLetter(Request $request, $id){
        $employee =   self::employeeinfo($id);
        $data['filename'] = 'appointment_letter';
        $data['orientation'] = "p";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $data['employee'] = $employee;
        if($employee->hr_emp_category_name == 'Worker'){
            $data['report_title'] = 'শ্রমিকদের নিয়োগপত্র';
            $view='Employee_report.appointment_letter_bn_pdf';
        }else{
            $data['report_title'] = 'Appointment Letter ' .$employee->hr_emp_category_name;
            $view='Employee_report.appointment_letter_pdf';
        }
        PdfHelper::exportPdf($view,$data);
    }

    /*
    * Confirmation Letter
    */
    public function ConfirmationLetter(Request $request, $id){
        $employee =  self::employeeinfo($id);
        $data['filename'] = 'confirmation_letter';
        $data['orientation'] = "p";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $data['employee'] = $employee;
        $data['salary_components'] = DB::table('hr_emp_salary_components')->where('sys_users_id', $id)->where('record_type', 'default')->get();
        $data['report_title'] = 'নিয়োগপত্র';
//        $view='Employee_report.confirmation_letter_pdf';
        $view='Employee_report.confirmation_letter_pdf_bangla';
        PdfHelper::exportPdf($view,$data);
    }

    /*
    * Confirmation Letter
    */
    public function JobApplication(Request $request, $id){
        $employee =  self::employeeinfo($id);
        $data['professions'] = DB::table('hr_emp_professions')->where('sys_users_id', $employee->id)->get();
        $data['reference_user'] = self::employeeinfo($employee->ref_userid);

        if($employee->hr_emp_category_name == 'Worker'){
            $data['report_title'] = 'চাকুরীর জন্য আবেদন পত্র';
            $view='Employee_report.job_application_bn_pdf';
        }else{
            $data['report_title'] = 'Application for The Job';
            $view='Employee_report.job_application_pdf';
        }
        $data['filename'] = 'Application for The Job';
        $data['orientation'] = "p";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $data['employee'] = $employee;
        PdfHelper::exportPdf($view,$data);
    }

    /*
    * Age and Affidavit Certificate
    */
    public function AgeAffidavitCertificate(Request $request, $id){
        $employee =  self::employeeinfo($id);
        $data['report_title'] = 'বয়স ও সক্ষমতার প্রত্যয়নপত্র';
        $view='Employee_report.age_affidavit_certificate_pdf';
        $data['filename'] = 'age_affidavit_certificate';
        $data['orientation'] = "p";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $data['employee'] = $employee;
        PdfHelper::exportPdf($view,$data);
    }


    /*
    * Night Shift Work Consent Letter
    */
    public function nswcLetter(){
        $data['report_title'] = 'Night Shift Work Consent Letter';
        $data['filename'] = 'nswcl';
        $data['orientation'] = "p";
        $data['branch_address'] = getUserInfoFromId(Auth::user()->id)->address;
        $view='Employee_report.nswcletter_pdf';
        PdfHelper::exportPdf($view,$data);
    }


    /*
     * Excel Export
     */
    private function excelReport($data){
        $filename = $data['filename']; //'employee-list-'.Auth::user()->id.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($data['title']);

        $header = array_map(function ($ar) {
            return !empty($ar['text']) ? $ar['text'] : '';
        }, $data['complex_header']);

        $number = 0;
        $row = 1;
        exportHelper::getCustomCell($sheet, 1, 0, getOptionValue('company_name'), count($header) - 1, null, 18, 'center');
        exportHelper::getCustomCell($sheet, 2, 0, $data['title'], count($header) - 1, null, 16, 'center');
        //exportHelper::getCustomCell($sheet,3,0,'Filter By',2,null,null,null,true);

        $row = 3;
        exportHelper::get_column_title($number, $row, $header, $sheet);

        $row = 4;
        if (isset($data['report_data'])){
            foreach ($data['report_data'] as $item) {
                $number = 0;
                foreach ($item as $col => $val){
                    $sheet->setCellValue(exportHelper::get_letter($number++).$row, str_replace("<br>",", ",$val) );
                }
                $row++;
            }
        }

        exportHelper::excelHeader($filename,$spreadsheet);
        return $filename;
    }

    /*
     * Employee Information by ID
     */
    private function employeeinfo($id){
        return  DB::table('sys_users')
            ->leftJoin('hr_emp_categorys', 'sys_users.hr_emp_categorys_id', '=', 'hr_emp_categorys.hr_emp_categorys_id')
            ->leftJoin('hr_emp_units', 'sys_users.hr_emp_units_id', '=', 'hr_emp_units.hr_emp_units_id')
            ->leftJoin('designations', 'sys_users.designations_id', '=', 'designations.designations_id')
            ->leftJoin('departments', 'sys_users.departments_id', '=', 'departments.departments_id')
            ->leftJoin('hr_emp_grades', 'sys_users.hr_emp_grades_id', '=', 'hr_emp_grades.hr_emp_grades_id')
            ->leftJoin('hr_emp_sections', 'sys_users.hr_emp_sections_id', '=', 'hr_emp_sections.hr_emp_sections_id')
            ->leftJoin('hr_emp_educations', 'sys_users.id', '=', 'hr_emp_educations.sys_users_id')
            ->leftJoin('sys_users as ref', 'sys_users.reference_user_id', '=', 'ref.id')
            ->leftJoin('bat_company', 'sys_users.bat_company_id', '=', 'bat_company.bat_company_id')
            ->select(
                'sys_users.*',
                'hr_emp_categorys.hr_emp_category_name',
                'hr_emp_units.hr_emp_unit_name',
                'designations.designations_name',
                'departments.departments_name',
                'hr_emp_grades.hr_emp_grade_name',
                'hr_emp_sections.hr_emp_section_name',
                'hr_emp_educations.educational_qualifications_name',
                'hr_emp_educations.educational_degrees_name',
                'hr_emp_educations.educational_institute_name',
                'ref.name as reference',
                'ref.id as ref_userid',
                'bat_company.company_name'
            )
            ->where('sys_users.id', $id)
            ->orderBy('hr_emp_educations.hr_emp_educations_id', 'desc')
            ->first();
    }


}
