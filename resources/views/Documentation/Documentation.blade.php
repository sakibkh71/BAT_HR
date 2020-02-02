@extends('layouts.app')
@section('content')
    <link href="{{asset('public/js/plugins/prism_code_viewer/prism.css')}}" rel="stylesheet" >
    <script src="{{asset('public/js/plugins/prism_code_viewer/prism.js')}}"></script>
    {{--/***********************/--}}
    {{--/***********************/--}}

    {{--/***********************/--}}
    {{--/***********************/--}}
    <div class="row">
        <div class="col-md-12 px-0 py-2">
            <div class="tabs-container">
                <div class="tabs-left">
                    <ul class="nav nav-tabs">
                        <li><a class="nav-link" data-toggle="tab" href="#sample-doc">Sample Doc Format</a></li>
                        <li><a class="nav-link active show" data-toggle="tab" href="#master-entry">Master Entry</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#custom-search">Dynamic Custom Search</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#dropdown-regular">Regular Dropdown</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#dropdown-grid">Dropdown grid Integration</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#label-changer">Dynamic Label Changer</a></li>
                    </ul>
                    <div class="panel-body p-0">
                        <div class="tab-content col-md-12">
                            <div id="sample-doc" class="tab-pane">@include('Documentation.sample-doc')</div>
                            <div id="master-entry" class="tab-pane active show">@include('Documentation.master-entry')</div>
                            <div id="custom-search" class="tab-pane">@include('Documentation.custom-search')</div>
                            <div id="dropdown-regular" class="tab-pane">@include('Documentation.dropdown-regular')</div>
                            <div id="dropdown-grid" class="tab-pane">@include('Documentation.dropdown-grid')</div>
                            <div id="label-changer" class="tab-pane">@include('Documentation.label-changer')</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"></div>
    </div>
    <div class="row my-3"></div>
    <style>
        .tabs-container .panel-body {
            background: none;
            border: none;
        }
        .nav-link.active {
            color: #c17700 !important;
            background-color: transparent !important;
            border-radius: 0 !important;
            border-color: transparent !important;
            border-left: 5px solid #c17700 !important;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .tabs-left > .nav-tabs > li > a {
            margin-right: -1px;
            border-radius: 0 !important;
        }
        .tabs-container .tabs-left > .nav-tabs > li > a {
            margin-bottom: -5px !important;
        }
        ul,ol{
            padding-left: 20px;
        }
        li{
            line-height: 1.8;
        }
    </style>

@endsection
