@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2 class="">
                            Test Custom Search
                        </h2>
                        <button class="btn btn-sm" id="show-custom-search"><i class="fa fa-search"></i> show search</button>
                    </div>
                    <div class="ibox-content">
                       {!! __getMasterGrid('hr_emp_list', 1) !!}
                    </div>
                </div>
                <div class="ibox-content">
                    Test content for ibox content custom search
                </div>
            </div>
        </div>
    </div>
@endsection

