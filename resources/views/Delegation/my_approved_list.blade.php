@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <link href="{{asset('public/css/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('public/js/moment.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/daterangepicker/daterangepicker.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('public/css/plugins/daterangepicker/daterangepicker.css')}}"/>
    {{csrf_field()}}
    @php
    extract($data);
    @endphp

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row" id="customer-grid-container">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">

                        <h2>Approved List</h2>

                        <div class="ibox-tools">
                            @if(isSuperUser())
                            @endif
                        </div>
                    </div>

                    <div class="ibox-content">
                        <form action="{{url('get-delegation-list')}}" method="post" class="ptype-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label"><strong>Reference Code :</strong></label>
                                        <div class="col-sm-12">
                                            <div class="inout-group">
                                                @php
                                                    $sql_approved_list ="SELECT DISTINCT ref_id as id , ref_id as ref_code  FROM sys_delegation_historys WHERE delegation_reliever_id = " .auth()->user()->id;
                                                @endphp
                                                {{__combo('approved_ref_code',array('sql_query'=>$sql_approved_list, 'selected_value'=> isset($ref_code)?$ref_code:''))}}
                                                {{--                                                {{__combo('approved_ref_code',array('sql_query'=>$sql_approved_list, 'selected_value'=> isset($ref_code)?$ref_code:''))}}--}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label"><strong>Deligating Date :</strong></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" placeholder="" class="form-control" id="dateRange" name="date_range" value="{{ isset($date_range)?$date_range:'' }}"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    {{--<div class="form-group">--}}
                                    <label class="col-sm-12 col-form-label"><strong> &nbsp;</strong></label>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Filter</button>
                                    <a class="btn btn-warning btn" href="{{url('get-delegation-list')}}"><i class="fa fa-resolving"></i>Reset</a>
                                    {{--</div>--}}
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" id="approval_list">
                                <thead>
                                <tr>
                                    <th>Reference Code</th>
                                    <th>Approval Action</th>
                                    <th>Approval Comment</th>
                                    <th>Delegating Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data['dele_list'])&& !empty($data['dele_list']))
                                    @foreach($data['dele_list'] as $delegation_list)
                                        <tr>
                                            <td>{{$delegation_list->ref_id}}</td>
                                            <td>{{$delegation_list->act_status}}</td>
                                            <td>{{$delegation_list->act_comments}}</td>
                                            <td>{{$delegation_list->created_at}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#approval_list').dataTable({
            "order":[[3,"desc"]]
        });

        (function ($) {
            $('#dateRange').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply:true,
            });
        })(jQuery);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
    </script>
@endsection