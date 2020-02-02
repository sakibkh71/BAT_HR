<?php

namespace App\Http\Controllers\Attachment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Redirect;
use Auth;
use Session;
use Validator;
use File;



class AttachmentController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function supportDocumentUpload(Request $request){
//        $post = $request->all();
//        debug($post,1);
        $uploadData['document_name'] = $request->document_name;
        $uploadData['reference'] = $request->reference;
        $uploadData['reference_id'] = $request->reference_id;
        $uploadData['destination_folder_name'] = $request->file_folder;
        return documentUpload($request, $uploadData);
    }


    public function getSupportingFiles(Request $request){
        $post = $request->all();
        $datas = DB::table('attachments')
            ->where('reference', '=', $post['reference'])
            ->where('reference_id', '=', $post['reference_id'])
            ->get();
        $support_doc = '';
        $i = 1;
        foreach ($datas as $data) {
            $support_doc .= '<tr>';
            $support_doc .= '<td style="padding-left: 18px; !important;"><i class="fa fa-file-picture-o"></i></td>';
            $support_doc .= '<td>';
            $support_doc .= '<a download style="text-decoration: none;" title="Download" href="'.asset('public/'.$data->document_path).'">'.$data->document_name.'</a>';
            $support_doc .= '</td>';

            $support_doc .= '<td align="center">';
            $support_doc .= '<a download style="text-decoration: none;" title="Download" href="'.asset('public/'.$data->document_path).'"><button class="btn btn-primary btn-xs"><i class="fa fa-download"></i> </button></a>&nbsp;&nbsp;';
            $support_doc .= '<button type="button" data-attachments_id="'.$data->attachments_id.'" class="btn btn-danger btn-xs remove-attachment">';
            $support_doc .= '<i class="glyphicon glyphicon-remove-sign"></i>';
            $support_doc .= '</button>';
            $support_doc .= '</td>';
            $support_doc .= '</tr>';

//            $support_doc .= '<tr>';
//            $support_doc .= '<td><i class="fa fa-file-picture-o"></i><a download style="text-decoration: underline;" title="Download" href="'.asset('public/'.$data->document_path).'"><i class="fa fa-download"></i>' . $data->document_name . '</a></td>';
//            $support_doc .= '<td align="center"><button type="button" data-attachments_id="' . $data->attachments_id . '" class="btn btn-danger btn-xs remove-attachment"><i class="glyphicon glyphicon-remove-sign"></i></button></td>';
//            $support_doc .= '</tr>';
            $i++;
        }
        return response()->json([
            'status' => 'success',
            'support_doc_html' => $support_doc
        ]);
    }


    public function deleteAttachmentsItem(Request $request){
        $destinationPath = DB::table('attachments')->where('attachments_id', '=', $request->attachments_id)->value('document_path');
        $affected = DB::table('attachments')->where('attachments_id', '=', $request->attachments_id)->delete();
        if ($affected > 0) {
            File::delete(public_path($destinationPath));
            echo 'success';
        } else {
            echo 'failed';
        }
    }
    
  public function deleteAttachmentsAjax(Request $request)
  {
    $data['result'] = true;
    $path = DB::table('attachments')
            ->where('attachments_id', '=', $request->attachments_id)
            ->value('document_path');
    $affected = DB::table('attachments')
            ->where('attachments_id', '=', $request->attachments_id)
            ->delete();
    if ($affected > 0) {
      fileDelete($path);
    } else {
      $data['result'] = false;
    }
    return response()->json($data);
  }
    
}

