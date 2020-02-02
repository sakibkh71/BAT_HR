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
                                <h2>Shift Wise Report</h2>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('shift-wise-duty')}}" method="post" id="shiftFilterForm" class="mb-4">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Calendar Date <span class="required">*</span></label>
                                    <div class="input-group calendar_month">
                                        <input type="text" required
                                               placeholder=""
                                               class="form-control"
                                               value="{{ isset($calendar_date)?@$calendar_date:date('Y-m-d')}}"
                                               id="calendar_date" name="calendar_date"/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Working Shift <span class="required">*</span></label>
                                        {{__combo('hr_emp_working_shift',array('selected_value'=>  isset($shifts_id)?$shifts_id:'', 'attributes'=> array('class'=>'form-control', 'required'=>'tryue', 'id'=>'user_id','name'=>'shifts_id')))}}
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Filter</button>
                                    <button type="button" id="makepdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-md-12 mt-4">
                                @if(!empty($attendance_rows) && count($attendance_rows))
                                    @foreach($attendance_rows as $shift => $shift_rows)
                                        <div class="border mb-4">
                                            <div class="ibox">
                                                <div class="ibox-title">
                                                    <h3>{{ $shift}}</h3>
                                                </div>
                                                <div class="ibox-content">
                                                    <table id="record_table" class="table table-striped text-lefts table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <td rowspan="2" style="vertical-align: middle">SL#</td>
                                                            <td rowspan="2" style="vertical-align: middle">Employee Name</td>
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
                                                            @if(!empty($shift_rows))
                                                                @foreach($shift_rows as $key => $rows)
                                                                    <tr>
                                                                        <td>{{$key+1}}</td>
                                                                        <td>{{$rows['name']}} @if(!empty($rows['name'])) ({{$rows['name']}}) @endif</td>
                                                                        <td>{{$rows['shift_name'] ?? 'N/A'}}</td>
                                                                        <td>{{$rows['daily_status'] ?? 'N/A'}}</td>
                                                                        <td>{{$rows['shift_start_time'] ?? 'N/A'}}</td>
                                                                        <td>{{$rows['shift_end_time'] ?? 'N/A'}}</td>
                                                                        <td>{{$rows['in_time'] ?? 'N/A'}}</td>
                                                                        <td>{{$rows['out_time'] ?? 'N/A'}}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
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
            $("#calendar_date").datetimepicker({
                format: 'YYYY-MM-DD'
            });
            
            $('#makepdf').click(function (e) {
                e.preventDefault();
                var form =  $('#shiftFilterForm');
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
