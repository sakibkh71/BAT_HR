<?php

namespace App\Http\Controllers\SystemSettings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Redirect;
use Auth;
use Session;

//for pdf
// use App\Helpers\PdfHelper;


class SystemSettingsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }

    public function view(){

    	dd('System Settings');
    }

}