@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{$title}}</h2>
                        <div class="ibox-tools">
                            <button class="btn btn-primary btn-xs no-display" id="view_attendance_details"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                            <button class="btn btn-info btn-xs no-display" id="draft_attendance_process"><i class="fa fa-check" aria-hidden="true"></i> Process</button>
                            <button class="btn btn-success btn-xs" id="all_draft_attendance_process"><i class="fa fa-check" aria-hidden="true"></i> All Process</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('attendance-final-process')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 form-label">{{__lang('Date Range')}}</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" placeholder="Attendance date" autocomplete="off"
                                                   class="form-control" id="date_range" name="date_range"
                                                   value="{{$date_range}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong>{{__lang('User')}}</strong></label>
                                        <div class="col-sm-12">
                                            {{__combo('sys_users_by_code', array('selected_value'=> $hr_users, 'attributes'=> array( 'name'=>'sys_users[]', 'id'=>'sys_users','multiple'=>true,'class'=>'form-control multi')))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong>{{__lang('Employee Category')}}</strong></label>
                                        <div class="col-sm-12">
                                            {{__combo('hr_emp_categorys', array('selected_value'=> $hr_emp_categorys, 'attributes'=> array( 'name'=>'hr_emp_categorys[]','multiple'=>true, 'id'=>'hr_emp_categorys', 'class'=>'form-control multi')))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>{{__lang('Filter')}}</button>
                                    <a class="btn btn-warning btn" href="{{url('attendance-final-process')}}"><i class="fa fa-resolving"></i> {{__lang('Reset')}} </a>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="temporary_employee_attendance_list" class="checkbox-clickable table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">{{__lang('SL')}}#</th>
                                        <th rowspan="2">{{__lang('User Code')}}</th>
                                        <th rowspan="2">{{__lang('Attendance From')}}</th>
                                        <th rowspan="2">{{__lang('Attendance To')}}</th>
                                        <th rowspan="2">{{__lang('Number Of Days')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($draft_attendance_history))
                                        @foreach($draft_attendance_history as $i=>$value)
                                            <tr class="attendance" code="{{$value->user_code}}">
                                                <td align="center">
                                                    {{($i+1)}}
                                                </td>
                                                <td>{{!empty($value->user_code)?$value->user_code:'N/A'}}</td>
                                                <td>{{!empty($value->min_date)?$value->min_date:'N/A'}}</td>
                                                <td>{{!empty($value->max_date)?$value->max_date:'N/A'}}</td>
                                                <td>{{!empty($value->number_of_days)?$value->number_of_days:'N/A'}}</td>
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
    </div>
    <!-- Modal For Open Existing Attendance -->
    <div id="open_existing_attendance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" id="existing_attendance" style="width: 980px;">
            <div class="modal-content" style="overflow: hidden; padding-bottom: 15px;">
                <div class="modal-header">
                    <span style="overflow: hidden !important;">
                        <h3 class="modal-title pull-left" style="color: red;">{{__lang('Existing Confirmed Attendance List')}}</h3>
                        <div class="pull-right">
                            <button style="margin-left: 20px;" class="btn btn-primary btn-xs" id="close_modal"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> {{__lang('Back')}}</button>
                        </div>
                    </span>
                </div>
                <div class="modal-body show_existing_attendance" style="overflow: hidden;">
                </div>
            </div>
        </div>
    </div>


    <style>
        .selected{
            background-color: green;
            color: #FFF;
        }
        .selected:hover{
            background-color: green !important;
            color: #FFF;
        }
    </style>
<script>
    $(function ($) {
        //Date Range Picker
        $('#date_range').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            autoApply: true,
        });
    });

    var selected_temp_attendance = [];
    $(document).on('click','.attendance',function (e) {
        $obj = $(this);
        if(!$(this).attr('code')){
            return true;
        }
        $obj.toggleClass('selected');
        var id = $obj.attr('code');
        if ($obj.hasClass( "selected" )){
            $obj.find('input[type=checkbox]').prop( "checked", true );
            selected_temp_attendance.push(id);
        }else{
            $obj.find('input[type=checkbox]').prop( "checked", false );
            var index = selected_temp_attendance.indexOf(id);
            selected_temp_attendance.splice(index,1);
        }
        actionManager(selected_temp_attendance);
    });

    $('#view_attendance_details').on('click', function () {
        var view_url = "<?php echo URL::to('show-hr-employee-attendance-details-info')?>/"+selected_temp_attendance[0];
        window.location.replace(view_url);
    });

    $('#close_modal').on('click', function () {
        swalConfirm("Please Prepare Your Excel/CSV with valid Data").then((e) => {
            if(e.value){
                $('#open_existing_attendance').modal("hide");
            }
        });
    });

    $('#draft_attendance_process, #all_draft_attendance_process').on('click', function () {
        Ladda.bind(this);
        var load = $(this).ladda();

        if(selected_temp_attendance.length == 1){
            var user_type = "single";
            var code = selected_temp_attendance[0];
        }else{
            var user_type = "Multiple";
            var code = '';
        }
        var data = {code:code,user_type:user_type,"_token":"{{ csrf_token() }}"};
        var url = "<?php echo URL::to('attendance-process')?>";
        var check_url = "<?php echo URL::to('check-previous-attendance')?>";
        swalConfirm("Are you sure you want to confirm employee attendance?").then((e) => {
            if(e.value){
                makeAjaxPostText(data,check_url,load).done((response) =>{
                    if(response == "not-found"){
                        makeAjaxPostText(data,url,load).then((response) =>{
                            if(response){
                                swalRedirect('','Employee Attendance Information Successfully Stored!.');
                            }else{
                                swalError();
                            }
                        });
                    }else if(response){
                        $('#open_existing_attendance').modal("show");
                        $('.show_existing_attendance').html(response);
                    }else{
                        swalError();
                    }
                });
            }
        });
    });

    function actionManager(selected_temp_attendance){
        if(selected_temp_attendance.length < 1){
            $('#view_attendance_details').fadeOut();
            $('#draft_attendance_process').fadeOut();
            $('#all_draft_attendance_process').fadeIn();
        }else if(selected_temp_attendance.length == 1){
            $('#view_attendance_details').fadeIn();
            $('#draft_attendance_process').fadeIn();
            $('#all_draft_attendance_process').fadeOut();
        }else if(selected_temp_attendance.length == 0){
            $('#all_draft_attendance_process').fadeIn();
        }else{
            $('#view_attendance_details').fadeOut();
            $('#draft_attendance_process').fadeOut();
            $('#all_draft_attendance_process').fadeIn();
        }
    }
</script>
@endsection