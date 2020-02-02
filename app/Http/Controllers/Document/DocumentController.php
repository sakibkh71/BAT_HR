<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use URL;
use DB;

class DocumentController extends Controller {

    public function index(){
        $data['list'] = DB::table('attachments')
            ->where('status', 'Active')
            ->where('reference', '=', 'file')
            ->orWhere('reference', '=', 'url')
            ->get();

        return view('Document.list', $data);
    }

    public function create($id=null){
        return view('Document.create');
    }

    public function store(Request $request, $id=null)
    {
        $request->validate([
            'document_name' => 'required|max:255',
            'reference' => 'required|max:255',
            'upload_file' => 'required_without:document_path',
            'document_path' => 'required_without:upload_file',
        ]);

        $insert = [
            'document_name' =>$request->document_name,
            'reference' =>$request->reference,
        ];

        if (!empty($request->upload_file)) {
            $request->validate([
                'upload_file' => 'required|mimes:jpeg,png,jpg,gif,svg,doc,pdf,docx,zip|max:2048',
            ]);
            $file = $request->upload_file;
            $new_name = date('Ymdhis') . $file->getClientOriginalName();
            $desstination = public_path() . '/documents/file';
            $file->move($desstination, $new_name);
            $insert['document_path'] = 'public/documents/file/'.$new_name;
        }else{
            $insert['document_path'] = $request->document_path;
        }

        if($id !=null){
            DB::table('attachments')->where('attachments_id',$id)->update($insert);
            return redirect()->route('document-list')->with('success', 'Document Upload Successfully');
        }
        else{
            DB::table('attachments')->insert($insert);
            return redirect()->route('document-list')->with('success', 'Document Upload Successfully');
        }

        /*if ($request->hasFile('upload_file')) {
            $allowedfileExtension = ['pdf', 'jpg', 'png', 'docx'];
            $files = $request->file('upload_file');
            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if ($check) {
                }
            }
        }*/
    }


    public function destroy(Request $request){
        $data =  DB::table('attachments')->where('attachments_id', $request->id)->first();
        if(file_exists($data->document_path)){
            unlink($data->document_path);
        }

        DB::table('attachments')->where('attachments_id', $request->id)->update(['status'=>'Inactive']);
        return response()->json([
            'msg' => 'Your item deleted successfully',
        ]);
    }
}
