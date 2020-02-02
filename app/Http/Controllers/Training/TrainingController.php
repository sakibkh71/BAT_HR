<?php

namespace App\Http\Controllers\Training;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Mpdf\Tag\P;
use Redirect;
use Auth;
use Response;
use App\Helpers\PdfHelper;
use Carbon\Carbon;

//for excel library
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
//use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use exportHelper;


class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function trainingList(Request $request){

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
//            dd('post', $request->all());
            if($request->training_id > 0){
                $updateAry = [
                    'name' => $request->training_name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'assign_to_new_emp' => $request->auto_assign,
                    'details' => $request->details,
                    'hours' => $request->number_hours,
                    'location' => $request->location,
                    'web_link' => $request->web_link,
                    'fees' => $request->training_fee,
                    'status' => $request->status
                ];

                DB::table('training_lists')->where('training_list_id', $request->training_id)->update($updateAry);

                $data['code'] = 200;
                $data['insert_or_update'] = 1;
            }
            else{
                $insertAry = [
                    'name' => $request->training_name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'assign_to_new_emp' => $request->auto_assign,
                    'details' => $request->details,
                    'hours' => $request->number_hours,
                    'location' => $request->location,
                    'web_link' => $request->web_link,
                    'fees' => $request->training_fee,
                    'status' => $request->status
                ];

                DB::table('training_lists')->insert($insertAry);

                $data['code'] = 200;
                $data['insert_or_update'] = 0;
            }


            return $data;
        }
        else{

            $data['list'] = DB::table('training_lists')->where('status', 'Active')->get();
            return view('Training.trainingList', $data);
        }
    }

    public function trainingListEdit($id){
//        dd($id);
        $data['training'] = DB::table('training_lists')->where('training_list_id', $id)->first();

        return $data;
    }
}