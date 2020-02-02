@extends('layouts.app')
@section('content')
    <script src="{{asset('public/js/plugins/bootstrap_toggle/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('public/js/bootstrap-checkbox.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/plugins/bootstrap_toggle/bootstrap-toggle.min.css')}}">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                <div class="col-lg-12 no-padding">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h2>{{$title}}</h2>
                            <div class="ibox-tools">
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ibox">
                                        <div class="ibox-title  bg-white">
                                            <h5> {{__lang('Attendance Manual Entry')}}</h5>
                                           {{-- <div class="ibox-tools">
                                                <button onclick="window.location='{{URL::to('approved-attendance-list')}}'" type="button" class="btn btn-primary btn-xs" id="confirm_list"><i class="fa fa-eye"></i> Attendance History</button>
                                            </div>--}}
                                        </div>
                                        <div class="ibox-content  bg-white">
                                            <form action="{{route('attendance-manual-entry', isset($attendance->hr_emp_attendance_id)?$attendance->hr_emp_attendance_id:'')}}" method="post" id="attendanceForm">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="font-normal"><strong>{{__lang('Date')}} </strong><span class="required">*</span></label>
                                                        <div class="form-group">
                                                            <div class='input-group'>
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input  type="text" name="day_is" id="day_is" class="form-control date" data-error="Please select start time" value="{{!empty($attendance->day_is)?$attendance->day_is:old('day_is')}}" placeholder="Date"  required="" autocomplete="off">
                                                            </div>
                                                            <div class="help-block with-errors has-feedback"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="font-normal"><strong>Employee</strong> <span class="required">*</span></label>
                                                        <div class="form-group  {{ $errors->has('sys_users_id') ? ' has-error' : '' }}">
                                                            {{__combo('hr_attendance_employee_list', array('attributes'=>array('name'=>'sys_users_id','id'=>'sys_users_id','class'=>'from-control multi'), 'selected_value'=> (isset($attendance->sys_users_id) ? $attendance->sys_users_id : old('sys_users_id'))))}}
                                                            <div class="help-block with-errors has-feedback">{{ $errors->first('sys_users_id') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row no-display">
                                                    <div class="col-md-6">
                                                        <label class="font-normal"><strong>Shift</strong> <span class="required">*</span></label>
                                                        <div class="form-group  {{ $errors->has('shift') ? ' has-error' : '' }}">
                                                            <select name="shift" class="form-control" id="shift">
                                                                <option value="">-- Select Shift --</option>
                                                            </select>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback">{{ $errors->first('shift') }}</div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label class="font-normal"><strong>{{__lang('Status')}}</strong><span class="required">*</span></label>
                                                        <div class="form-group">
                                                            <select name="daily_status" class="form-control" id="daily_status" required>
                                                                <option value="P" @if(empty($attendance->daily_status) || (!empty($attendance->daily_status) && $attendance->daily_status =='P')) selected @endif>{{__lang('Present')}} (P)</option>
                                                                <option value="A" @if(!empty($attendance->daily_status) && $attendance->daily_status =='A') selected @endif>{{__lang('Absent')}} (A)</option>
                                                                <option value="WP" @if(!empty($attendance->daily_status) && $attendance->daily_status =='WP') selected @endif>{{__lang('Weekend present')}} (WP)</option>
                                                                <option value="HP" @if(!empty($attendance->daily_status) && $attendance->daily_status =='HP') selected @endif>{{__lang('Holiday Present')}} (HP)</option>
                                                                {{--<option value="L" @if(!empty($attendance->daily_status) && $attendance->daily_status =='L') selected @endif>{{__lang('Late')}} (L)</option>--}}
                                                                {{--<option value="EO" @if(!empty($attendance->daily_status) && $attendance->daily_status =='EO') selected @endif>{{__lang('Early out')}} (EO)</option>--}}
{{--                                                                <option value="A" @if(!empty($attendance->daily_status) && $attendance->daily_status =='A') selected @endif>{{__lang('Absent')}} (A)</option>--}}
                                                            </select>
                                                            <div class="help-block with-errors has-feedback"></div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <button class="btn btn-primary" type="submit">{{__lang('Submit')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                             </form>
                                        </div>
                                    </div>
                                </div>
                                @if(!isset($attendance->hr_emp_attendance_id))
                                        <div class="col-md-4 offset-md-1 no-display">
                                    <div class="ibox bg-white">
                                        <div class="ibox-title  bg-white">
                                            <h5> {{__lang('Bulk Upload')}}</h5>
                                            <div class="ibox-tools">
                                                <a href="{{URL::to('public/documents/attendance/bulk-attendence-example.csv')}}" class="btn btn-primary btn-xs" download="bulk-attendence-example.csv"><i class="fa fa-download"></i> Sample File</a>
                                            </div>
                                        </div>
                                        <div class="ibox-content bg-white">
                                            <form data-toggle="validator" role="form" action="{{ route('preview-attendance-history') }}" method="post" id="employee_attendance_upload_form" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <div class="form-group">
                                                <label class="form-label">{{__lang('Bulk Upload Attendance Record')}} <span class="required">*</span></label>
                                                <div class="input-group">
                                                    <input id="file" type="file" required name="select_file" id="select_file" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-md" id="preview-attendance-history">{{__lang('Temporary Upload')}}</button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-5  no-display">
                                        <div class="ibox bg-white">
                                            <div class="ibox-title  bg-white">
                                                <h5>{{__lang('Device Attendance Data')}}</h5>
                                                <div class="ibox-tools">
                                                    <form method="post" style="float: right; max-width: 220px;">
                                                        @csrf
                                                    <div class='input-group'>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input  type="text" name="bulk_day_is" id="bulk_day_is" class="form-control date" data-error="Please select date" value="{{@$bulk_day_is?$bulk_day_is:date('Y-m-d')}}" placeholder="Date"  required="" autocomplete="off">
                                                        <button type="submit" class="btn btn-primary btn-md" id="preview-attendance-history">Process</button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="ibox-content bg-white">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <th>{{__lang('name')}}</th>
                                                    <th>{{__lang('user_code')}}</th>
                                                    <th>{{__lang('log_time')}}</th>
                                                    <th>{{__lang('sync')}}</th>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($deviceData))
                                                        @foreach($deviceData as $data)
                                                    <tr>
                                                        <td>{{$data->name}}</td>
                                                        <td>{{$data->user_code}}</td>
                                                        <td>{{$data->log_time}}</td>
                                                        <td>{{$data->sync?'Yes':'No'}}</td>
                                                    </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>

                                                </table>
                                                <div class="col-md-12 mt-2">{{ $deviceData->links() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>



<script>
    {{--$(document).on('click','#preview-attendance-history',function () {--}}
        {{--Ladda.bind(this);--}}
        {{--var load = $(this).ladda();--}}
        {{--var frm_data = $('#employee_attendance_upload_form').serialize();--}}
        {{--var url = '{{URL::to('preview-attendance-history')}}';--}}
        {{--makeAjaxPostText(frm_data,url,load).then((response) =>{--}}

        {{--});--}}
    {{--});--}}

    $(function () {
        $('.datetime').datetimepicker({
            format: 'YYYY-MM-DD H:mm',
            //format : 'YYYY-MM-DD g:i a'
        });
        $('.hourpicker').datetimepicker({
            format: 'H:mm'
        });
    });

    //Datepicker
    function datepic(){
        $(".date").datetimepicker({
            format: 'YYYY-MM-DD'
        });
    }
    datepic();

    $(document).ready(function(){
        $(':checkbox').checkboxpicker();
    });

    $('#attendanceForm').validator();

    $(document).on('change', '#daily_status', function () {
        var $val = $(this).val();
        if($val=='A'){
            $('#time_imput').hide();
            $('#in_time').val('');
            $('#out_time').val('');
            $('#in_time').removeAttr("required");
            $('#out_time').removeAttr("required");
        }else{
            $('#time_imput').show();
            $('#in_time').attr("required", "true");
            $('#out_time').attr("required", "true");
        }
        $('#attendanceForm').validator('update')
    });


    /*$('#daily_status').change(function(e) {
        var st = $(this).val();
        if(st =='WP' || st =='P' || st =='L' || st =='HP') {
            $('#in_time').attr("required", "true");
            $('#out_time').attr("required", "true");
        }else{
            $('#in_time').removeAttr("required");
            $('#out_time').removeAttr("required");
        }
        $('#attendanceForm').validator('update')
    });*/


    $('#day_is').click(function () {
        $('#sys_users_id').val("");
        $('#sys_users_id').multiselect('refresh');
        //$('#is_salary_enabled').prop('checked', true);
        $('#in_time').val('');
        $('#out_time').val('');
        //$('#break_time').val('');
        //$('#ot_hours').val('');
        $("#daily_status").val('P');
    });

    
    $(document).on('change', '#sys_users_id', function (e) {
        getAttendanceInfo();
    });

    function getAttendanceInfo(){
        var date = $('#day_is').val();
        var shift = $('#shift').val();
        var user = $('#sys_users_id').val();

        if( date== ''){
            swalError("Please Select Date first")
        }else if(user == ''){
            swalError("Please Select Employee")
        }
        /*else if(shift == ''){
            swalError("Please Select Shift")
        }*/else{
            var data = {date:date, user:user, shift:shift};
            var url = '{{route('check-employee-attendance')}}';
            makeAjaxPost(data,url,null).then(function (response) {
                console.log(response.data);
                if (response.data) {
                    $('#in_time').val(response.data.in_time);
                    $('#out_time').val(response.data.out_time);
                    //$('#break_time').val(response.data.break_time);
                   // $('#ot_hours').val(response.data.ot_hours);
                    $("#daily_status").val(response.data.daily_status || 'P');
                    $('#attendanceForm').attr('action', '{{URL::to('attendance-manual-entry')}}/'+response.data.hr_emp_attendance_id);

                    if(response.data.is_salary_enabled ==1){
                        $('#is_salary_enabled').prop('checked', true);
                    }else{
                        $('#is_salary_enabled').prop('checked', false);
                    }
                }else{
                    $('#attendanceForm').attr('action', '{{URL::to('attendance-manual-entry')}}');
                    $('#is_salary_enabled').prop('checked', true);
                    $('#in_time').val('');
                    $('#out_time').val('');
                    //$('#break_time').val('');
                    //$('#ot_hours').val('');
                    $("#daily_status").val('');
                    swalError('Sorry! we haven\'t find any data. Please configure Company Calender for the date of this employee');
                }
            });
        }
    }

    /*$(document).on('change', '#sys_users_id', function (e) {
        var date = $('#day_is').val();
        var user = $(this).val();

        if( date== ''){
            swalError("Please Select Date first")
        }else{
            var data = {date:date, user:user};
            var url = '{{route('get-employee-attendance-shift')}}';
            makeAjaxPost(data,url,null).then(function (response) {
                if (response.data) {
                    var opt = '';

                    $(response.data).each(function(index,element){
                        opt += '<option value="'+ element.hr_working_shifts_id +'">'+element.shift_name+'</option>';
                    });

                    $('#shift').html(opt);
                    //on change shift
                    setTimeout(function(){ changeShift() }, 50);

                }else{
                    swalError('Sorry! we haven\'t find any data for the date of this employee');
                }
            });
        }
    });*/

   /* $(document).on('change', '#is_salary_enabled', function (e) {
        if(!$(this).is(':checked')) {
            swalWarning('Salary will deducted for this day');
        }
    });*/

    $('.clockpicker').clockpicker();



</script>
@endsection