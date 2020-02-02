@extends('layouts.clean_template')
@section('content')
    @inject('moduleController', 'App\Http\Controllers\ModuleController')
        <div class="text-center customized_div">
            <br/>
            @php($dashboardlink = $my_dashboard == 1 ? URL::to('my-dashboard') : URL::to('dashboard'))
            <br/>
            <br/>
            <br/>
            @foreach ($moduleController->getModuleList() as $val)
                <a class="btn btn-sm btn-info" href="{{URL::to("/moduleChanger/".$val->id."/No")}}">
                    <i class="{{ $val->modules_icon }}"></i> {{ $val->name }}
                </a>
            @endforeach
        </div>
    <style>
        .middle-box h1 {
            font-size: 100px;
        }
        .customized_div{
            margin-top: 250px;
        }
        
        body{
            background: white !important;
        }
    </style>
@endsection
