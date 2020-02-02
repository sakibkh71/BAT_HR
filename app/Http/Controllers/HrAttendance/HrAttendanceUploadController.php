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

//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class HrAttendanceUploadController extends Controller {

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    /*
    * Attendance Entry
    */
    public function bulkEntry(Request $request, $id = null){
        $data['title'] = "Attendance Bulk Upload";
        return view('HrAttendance.attendance_bulk_upload', $data);
    }

    public function storeBulkAttendanceData(Request $request){
        $document = $request->file('select_file');
        $original_name = $document->getClientOriginalName();
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $current_date_time = strtotime(date('Y-m-d H:m:s'));
        $new_name = $filename.'_'.$current_date_time. '.' . $document->getClientOriginalExtension();

        if (!is_dir(public_path('documents/attendance'))) {
            mkdir(public_path('documents/attendance'), 0777, true);
        }

        $document->move(public_path('documents/attendance'), $new_name);

        if('csv' == $file_extension || ('xlsx' == $file_extension || 'XLSX' ==$file_extension)) {
            if('csv' == $file_extension){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }else{
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            try {
                $path = public_path('documents/attendance/').$new_name;
                $spreadsheet = $reader->load($path);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();



                if(!empty($sheetData) && (
                        count($sheetData[0]) !=4 ||
                        $sheetData[0][0] !='SL' ||
                        $sheetData[0][1] !='User Code' ||
                        $sheetData[0][2] !='Log Time' ||
                        $sheetData[0][3] !='Device ID')
                ){
                    return redirect()->route('attendance-bulk-upload')
                        ->with('warning','Please provide correct formatted file');
                }



                if(!empty($sheetData)){

                    $prepare_arr = [];

                    array_shift($sheetData);

                    foreach ($sheetData as $k=>$value){
                        $val_4 = !empty($value[2])? date("Y-m-d H:i:s",strtotime($value[2])) : null;
                        $prepare_arr[$k]['user_code']  = trim($value[1]);
                        $prepare_arr[$k]['log_time'] = $val_4;
                        $prepare_arr[$k]['device_id'] = $value[3];
                        $prepare_arr[$k]['created_at'] = date('Y-m-d H:i:s');
                        $prepare_arr[$k]['created_by'] = Auth::id();
                    }

                    foreach (array_chunk($prepare_arr,1000) as $t){
                        DB::table('hr_temporary_emp_attendance')->insert($t);
                    }

                    return redirect()->route('attendance-bulk-upload')
                        ->with('info','Successfully Uploaded!');
                }else{
                    return redirect()->route('attendance-bulk-upload')
                        ->with('warning','data is not found');
                }
            }catch (Exception $e) {
                return redirect()->route('attendance-bulk-upload')
                    ->with('error','Error occured!');
            }
        }
    }


    /*
    * Manual Attendance Entry
    */
    public function manualEntry(Request $request, $id = null){
        $data['title'] = "Attendance Manual Upload";
        return view('HrAttendance.attendance_manual_upload', $data);
    }


    /*
     * Manual Attendance Data Store
     */
    public function storeManualAttendanceData(Request $request){
        $document = $request->file('select_file');
        $original_name = $document->getClientOriginalName();
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $current_date_time = strtotime(date('Y-m-d H:m:s'));
        $new_name = $filename.'_'.$current_date_time. '.' . $document->getClientOriginalExtension();

        if (!is_dir(public_path('documents/attendance'))) {
            mkdir(public_path('documents/attendance'), 0777, true);
        }

        $document->move(public_path('documents/attendance'), $new_name);

        if('csv' == $file_extension || ('xlsx' == $file_extension || 'XLSX' ==$file_extension)) {
            if('csv' == $file_extension){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }else{
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            try {
                $path = public_path('documents/attendance/').$new_name;
                $spreadsheet = $reader->load($path);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                if(!empty($sheetData) && (
                        count($sheetData[0]) !=5 ||
                        $sheetData[0][0] !='SL' ||
                        $sheetData[0][1] !='Employee Code' ||
                        $sheetData[0][2] !='Employee Name' ||
                        $sheetData[0][3] !='Date' ||
                        $sheetData[0][4] !='Daily Status')
                ){
                    return redirect()->route('attendance-manual-upload')
                        ->with('warning','Please provide correct formatted file');
                }

                if(!empty($sheetData)){
                    $prepare_arr = [];
                    array_shift($sheetData);
                    foreach ($sheetData as $k=>$value){

                        $day = !empty($value[3])? date("Y-m-d",strtotime($value[3])) : null;
                        $daily_status = !empty($value[4])?$value[4]:'P';
//                        $in_time = $daily_status !='A'?(!empty($value[3])? $day .' ' .date("H:i:s",strtotime($value[3])):null):null;
//                        $out_time = $daily_status !='A'?(!empty($value[4])? $day .' ' .date("H:i:s",strtotime($value[4])):null):null;

                        $prepare_arr[$k]['user_code'] = trim($value[1]);
                        $prepare_arr[$k]['day_is'] = $day;
                        if(in_array($daily_status,['P','HP','L','WP','EO'])){
                            $prepare_arr[$k]['in_time'] = $day.' 09:00';
                            $prepare_arr[$k]['out_time'] = $day.' 18:00';
                        }else{
                            $prepare_arr[$k]['in_time'] = null;
                            $prepare_arr[$k]['out_time'] = null;
                        }

                        $prepare_arr[$k]['daily_status'] = $daily_status;
                        $prepare_arr[$k]['file_name'] = $new_name;
                        $prepare_arr[$k]['created_at'] = date('Y-m-d H:i:s');
                        $prepare_arr[$k]['created_by'] = Auth::id();
                    }

                    //Remove Data by User id on  temp table
                    DB::table('hr_temporary_manual_attendance')->where('created_by', Auth::id())->delete();

                    //Insert attendance data
                    foreach (array_chunk($prepare_arr,1000) as $t){
                        DB::table('hr_temporary_manual_attendance')->insert($t);
                    }

                    return redirect()->route('attendance-manual-upload')
                        ->with('info','Successfully Uploaded!');

                }else{
                    return redirect()->route('attendance-manual-upload')
                        ->with('warning','data is not found');
                }
            }catch (Exception $e) {
                return redirect()->route('attendance-manual-upload')
                    ->with('error','Error occured!');
            }
        }
    }


    /*
     * Sync Manual Attendance
     */
    public function syncManualAttendance(Request $request){

        $res = DB::table('hr_temporary_manual_attendance')
            ->select('hr_temporary_manual_attendance.*','sys_users.name')
            ->join('sys_users', 'hr_temporary_manual_attendance.user_code', '=', 'sys_users.user_code')
            ->where('hr_temporary_manual_attendance.created_by', Auth::id())
            ->where('sync', 0);

        if(isset($request->employee_id)){
            $res->whereIn('sys_users.id', $request->employee_id);
        }

        if(isset($request->day_is)) {
            $day_is = $request->day_is;
            $date_range = explode(' - ', $day_is);
            $res->where(function ($query) use ($date_range) {
                $query->where(function ($q) use ($date_range) {
                    $q->whereDate('in_time', '>=', $date_range[0])->whereDate('in_time', '<=', $date_range[1]);
                });

                $query->orWhere(function ($q) use ($date_range) {
                    $q->whereDate('out_time', '>=', $date_range[0])->whereDate('out_time', '<=', $date_range[1]);
                });
            });
        }
        if ($res->update(['sync' => 1])){
            return response()->json([
                'status' => 'success'
            ]);
        }else{
            return response()->json([
                'status' => 'failed'
            ]);
        }
    }

}
