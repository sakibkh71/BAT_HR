<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
class Documentation extends Controller {
    private $data = [];
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('menu_permission');
    }
    public function noPermission(){
        return view('errors/no_permission');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('Documentation.Documentation');
    }
}
