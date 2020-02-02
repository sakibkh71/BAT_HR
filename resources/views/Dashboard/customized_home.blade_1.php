@extends('layouts.app')
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}"/>
        <title>SRCIL - {{session::get('MODULE_LANG')}}</title>
        <link rel="shortcut icon" type="image/png" href="{{asset('public/img/srcil_icon.png')}}"/>
        @include('includes.assets')
        @php(date_default_timezone_set('Asia/Dhaka'))
    </head>
    <body>
        @inject('moduleController', 'App\Http\Controllers\ModuleController')
        <div class="col-lg-12 no-padding  customized_div">
            <div class="middle-box text-center mt-3" style="max-width: 100%">
                <h1>{!! session::get('MODULE_LANG') !!}</h1>
            </div>
            <div class="text-center">
                @foreach ($moduleController->getModuleList() as $val)
                    <a class="btn btn-sm btn-info" href="{{URL::to("/moduleChanger/".$val->id."/"."No")}}">
                        <i class="{{ $val->modules_icon }}"></i> {{ $val->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </body>
    <script src="{{asset('public/js/apsisScript.js')}}"></script>
</html>


    <style>
        .middle-box h1 {
            font-size: 100px;
        }
        .customized_div{
            margin-top: 200px;
        }
        
        body{
            background: white !important;
        }
    </style>

