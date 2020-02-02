@extends('layouts.app')
@section('content')
    @inject('moduleController', 'App\Http\Controllers\ModuleController')
    <div class="wrapper wrapper-content animated fadeIn">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="middle-box text-center mt-3" style="max-width: 100%">
                        <h1>{!! session::get('MODULE_LANG') !!}</h1>
                    </div>
                    <div class="text-center">
                        <br/>
                        @php($dashboardlink = @$my_dashboard == 1 ? URL::to('dashboard') : URL::to('dashboard'))
                        <a class="btn btn-primary btn-lg" href="{{$dashboardlink}}"><i class="fa fa-dashboard"></i> Go to Dashboard</a>
                        <br/>
                        <br/>
                        <br/>
                        @foreach ($moduleController->getModuleList() as $val)
                            <a class="btn btn-sm btn-info" href="{{URL::to("/moduleChanger/".$val->id)}}">
                                <i class="{{ $val->modules_icon }}"></i> {{ $val->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .middle-box h1 {
            font-size: 100px;
        }
    </style>
@endsection
