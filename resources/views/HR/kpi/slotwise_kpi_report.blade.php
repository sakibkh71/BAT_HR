@extends('layouts.app')
@section('content')
    <style>
        .row-select-toggle{
            cursor: default;
        }
        .dropdown-item {
            margin: 0;
            padding: 5px;
        }

    </style>
    <script src="{{asset('public/js/plugins/bootstrap_toggle/bootstrap-toggle.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/plugins/bootstrap_toggle/bootstrap-toggle.min.css')}}">
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">

                <div class="ibox-title">
                    <h2>KPI Achievement Summary</h2>
                </div>
                <div class="ibox-content">
                    <form action="{{route('slotwise-kpi-report')}}" method="post" id="attendanceForm">
                        @csrf
                        <div class="row">
                            {!! __getCustomSearch('kpi-monthly-summary', $posted) !!}
                            <div class="col-md-3">
                                <label class="font-normal"><strong>{{__lang('Slect Month')}} </strong><span class="required">*</span></label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="prev_month" class="form-control" id="prev_month" data-error="Please select Date" value="{{!empty($prev_month)?$prev_month:''}}" placeholder="YYYY-MM"  required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:28px;">
                                    <button class="btn btn-primary btn" name="submit" type="submit">{{__lang('Search')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered" id="slot-table">
                        <thead>
                            <tr>
                                <th>Designation</th>
                                <th>Less Then 70</th>
                                <th>70 to 80</th>
                                <th>81 to 90</th>
                                <th>More Then 90</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($result))
                                @foreach($result as $key=>$info)
                                    <tr>
                                        <td>{{$info['designation_name']}}</td>
                                        @foreach($key_ary as $val)
                                            <td>
                                                @if(array_key_exists($val, $info))
                                                    <form action="{{route('kpi-monthly-summery')}}" method="post" id="">
                                                        @csrf
                                                        <input type="hidden" value="{{$key}}" name="designations_id[]">
                                                        <input type="hidden" value="{{$val}}" name="range_val">
                                                        <input type="hidden" value="{{$prev_month}}" name="month_from">
                                                        <input type="hidden" value="{{$prev_month}}" name="month_to">
                                                        <button name="submit" type="submit" data-toggle="tooltip" title="View Report" class="btn btn-xs btn-success btn-css">{{count($info[$val])}}</button>
                                                    </form>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <style>

            /*.btn-css{*/
                /*outline: none;*/
                /*padding: 5px;*/
                /*border: 0px;*/
                /*background-color: transparent;*/
            /*}*/
        </style>
    </div>

    <script>
        $('#slot-table').dataTable();
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();

            $("#prev_month").datepicker( {
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true,
            });
        });

    </script>
@endsection
