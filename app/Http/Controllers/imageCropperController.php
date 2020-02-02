<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class imageCropperController extends Controller
{
    public function image_cropper(){
        return view('image_cropper_view');
    }
}
