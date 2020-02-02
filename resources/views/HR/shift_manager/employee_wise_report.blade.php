@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2>Employee Wise Duty Report</h2>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('employee-wise-duty')}}" method="post" id="empshiftForm" class="mb-4">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Calendar Month<span class="required">*</span></label>
                                    <div class="input-group calendar_month">
                                        <input type="text" required
                                               placeholder=""
                                               class="form-control"
                                               value="{{ isset($calendar_month)?$calendar_month:date('Y-m')}}"
                                               id="calendar_month" name="calendar_month"/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Employee <span class="required">*</span></label>
                                        {{__combo('calendar_employee_list',array('selected_value'=>  isset($user_id)?$user_id:'', 'attributes'=> array('class'=>'form-control', 'required'=>'tryue', 'id'=>'user_id','name'=>'user_id')))}}
                                    </div>
                                </div>

                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Filter</button>
                                    <button type="button" id="makepdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                </div>
                            </div>
                        </form>
                        @if(!empty($attendance_rows))
                        <div class="border mb-4">
                            <div class="ibox">
                                <div class="ibox-title">
                                   {!! isset($emp_info) && !empty($emp_info)?$emp_info:'' !!}
                                </div>
                                <div class="ibox-content">
                                    <table id="record_table" class="table table-striped text-lefts table-bordered">
                                        <thead>
                                        <tr>
                                            <td rowspan="2" style="vertical-align: middle">Date</td>
                                            <td rowspan="2" style="vertical-align: middle">Shift Name</td>
                                            <td rowspan="2" style="vertical-align: middle">Daily Status</td>
                                            <td colspan="2">Shift Info</td>
                                            <td colspan="2">Attendance Info</td>
                                        </tr>
                                        <tr>
                                            <td>Start Time</td>
                                            <td>End Time</td>
                                            <td>Start Time</td>
                                            <td>End Time</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($attendance_rows as $row)
                                            <tr>
                                                <td>{{ toDated($row->day_is)}}</td>
                                                <td>{{$row->shift_name}}</td>
                                                <td>{{ !empty($row->daily_status)?$row->daily_status:'N/A'}}</td>
                                                <td>{{!empty($row->shift_start_time)?date('h:i:s A',strtotime($row->shift_start_time)):'N/A'}}</td>
                                                <td>{{!empty($row->shift_end_time)?date('h:i:s A',strtotime($row->shift_end_time)):'N/A'}}</td>
                                                <td>{{!empty($row->in_time)?(date('Y-m-d',strtotime($row->in_time)) == $row->day_is ? date('h:i:s A',strtotime($row->in_time)): toDateTimed($row->in_time)):'N/A'}}</td>
                                                <td>{{!empty($row->out_time)?(date('Y-m-d',strtotime($row->out_time)) == $row->day_is ? date('h:i:s A',strtotime($row->out_time)):  toDateTimed($row->out_time)):'N/A'}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .emp_code_entry button{
            width: 100% !important;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#calendar_month").datetimepicker({
                format: 'YYYY-MM'
            });

            $('#makepdf').click(function (e) {
                e.preventDefault();
                var form =  $('#empshiftForm');
                var action = form.attr('action');

                form.attr('target', '_blank');
                form.attr("action", action + '/pdf');

                form.submit();

                form.attr("action", action);
                form.removeAttr("target");

            });


            /*$('#leave_year').datepicker({
                viewMode: "years",
                minViewMode: "years",
                format: "yyyy",
                autoclose:true
            });
            $('#leave_month').datepicker({
                viewMode: "months",
                minViewMode: "months",
                format: "yyyy-mm",
                autoclose:true
            });*/
        });


    </script>
@endsection
