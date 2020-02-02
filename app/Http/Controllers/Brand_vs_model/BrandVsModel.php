<?php

namespace App\Http\Controllers\Brand_vs_model;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use URL;
use DB;
use Response;

class BrandVsModel extends Controller
{
    public function index(){
        $formData = [
            'title' => 'Brand VS Model Form',
            'product_brands' => 'brands',
            'product_models' => 'models',
        ];
        return view('Brand_vs_model.list', compact('formData'));
    }

    public function getBrand(Request $request){
        $brands = DB::table('product_brands')->where('status','=','Active')->orderBy('product_brands_id')->get();
        $existBrands = array_map('current', (array)(DB::select(DB::raw("SELECT product_brands_id FROM brand_vs_models WHERE product_models_id=".$request->product_models_id))));
        echo view('Brand_vs_model.product_brand_list', compact('brands','existBrands'));
    }

    public function saveBrand(Request $request){
        $data = $request->except('_token', 'updated_at');
        DB::table('brand_vs_models')->insert($data);
        return Response::json(['status' => 'success', 'message' => 'Successfully added']);
    }

    public function removeBrand(Request $request){
        DB::table('brand_vs_models')->where('product_models_id','=',$request->product_models_id)->delete();
        return Response::json(['status' => 'success', 'message' => 'Successfully deleted']);
    }

    public function getModel(Request $request){
        $models = DB::table('product_models')->where('status','=','Active')->orderBy('product_models_id')->get();
        $existModels = array_map('current', (array)(DB::select(DB::raw("SELECT product_models_id FROM brand_vs_models WHERE product_brands_id=".$request->product_brands_id))));
        echo view('Brand_vs_model.product_model_list', compact('models','existModels'));
    }

    public function saveModel(Request $request){
        $data = $request->except('_token', 'updated_at');
        DB::table('brand_vs_models')->insert($data);
        return Response::json(['status' => 'success', 'message' => 'Successfully added']);
    }

    public function removeModel(Request $request){
        DB::table('brand_vs_models')->where('product_brands_id','=',$request->product_brands_id)->delete();
        return Response::json(['status' => 'success', 'message' => 'Successfully deleted']);
    }
}
