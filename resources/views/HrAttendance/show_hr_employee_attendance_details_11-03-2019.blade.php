@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                <div class="col-lg-12 no-padding">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h2>{{$title}}</h2>
                            <div class="ibox-tools">
                                {{--<button class="btn btn-danger btn-xs" id="back-to-attendance-process"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Back</button>--}}
                                {{--<button class="btn btn-bitbucket btn-xs no-display" id="edit_attendance"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>--}}
                                {{--<button class="btn btn-danger btn-xs no-display" id="delete_attendance"><i class="fa fa-remove" aria-hidden="true"></i> Delete</button>--}}
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="col-md-12 no-padding row">
                                <div class="col-md-5">
{{----                                     <span class="">user Code : &nbsp;<span class="font-bold employee_mobile">{{isset($info[0]->user_code)?$info[0]->user_code:''}}</span></span><br/>--}}
                                    {{--<span class="">Section : &nbsp;<span class="font-bold designation_name">{{isset($info[0]->section_name)?$info[0]->section_name:''}}</span></span><br/>--}}
                                    {{--<span class="">Category : &nbsp;<span class="font-bold designation_name">{{isset($info[0]->hr_emp_category_name)?$info[0]->hr_emp_category_name:''}}</span></span><br/>--}}
                                    {{--<span class="">Shift : &nbsp;<span class="font-bold designation_name">{{isset($info[0]->shift_name)?$info[0]->shift_name:''}}</span></span><br/>--}}
                                    {{--<span class="">Upload By : &nbsp;<span class="font-bold designation_name">{{isset($info[0]->name)?$info[0]->name:''}}</span></span><br/>--}}
                                </div>
                                <div class="col-md-7">
                                </div>
                            </div>
                            <table class="checkbox-clickable table table-striped table-bordered table-hover emp-attendance-list">
                                <thead>
                                <tr>
                                    <th rowspan="2">SL#</th>
                                    <th rowspan="2">User Code</th>
                                    <th rowspan="2">status</th>
                                    <th rowspan="2">Day</th>
                                    <th rowspan="2">Start Time</th>
                                    <th rowspan="2">End Time</th>
                                    <th rowspan="2">Break Time</th>
                                    <th rowspan="2">Over Time</th>
                                    <th rowspan="2">Total Working Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($info))
                                    @foreach($info as $i=>$value)
                                        <tr class="item" code="{{$value->user_code}}" day_is="{{$value->day_is}}" start_date_time="{{$value->start_date_time}}">
                                            <td align="center">
                                                {{($i+1)}}
                                            </td>
                                            <td>{{$value->user_code}}</td>
                                            <td>{{$value->status}}</td>
                                            <td>{{$value->day_is}}</td>
                                            <td>{{$value->start_date_time}}</td>
                                            <td>{{$value->end_date_time}}</td>
                                            <td>{{$value->break_time}}</td>
                                            <td>{{$value->ot_hours}}</td>
                                            <td>{{$value->total_work_time}}</td>
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



<script>
    $(document).ready(function(){
        // $('.emp-attendance-list').dataTable();
    });

    $('#back-to-attendance-process').on('click', function () {
        var view_url = "<?php echo URL::to('attendance-final-process')?>";
        window.location.replace(view_url);
    });

    $('#delete_attendance').on('click', function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var code = $obj.attr('code');
        var table = "hr_temporary_emp_attendance";
        var day_is = $obj.attr('day_is');
        var start_date_time = $obj.attr('start_date_time');
        var data = {table:table,code:code,day_is:day_is,start_date_time:start_date_time,"_token":"{{ csrf_token() }}"};
        var url = "<?php echo URL::to('delete-hr-emp-attendance')?>";
        swalConfirm("Are you sure you want to delete this?").then((e) => {
            if(e.value){
                makeAjaxPostText(data,url,load).then((response) =>{
                    if(response){
                        swalRedirect('','successfully Deleted.');
                    }else{
                        swalError();
                    }
                });
            }
        });
    });

    $('#edit_attendance').on('click', function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var code = $obj.attr('code');
        var table = "hr_temporary_emp_attendance";
        var start_date_time = $obj.attr('start_date_time');
        var data = {table:table,code:code,day_is:day_is,start_date_time:start_date_time,"_token":"{{ csrf_token() }}"};
        var url = "<?php echo URL::to('get-temp-hr-emp-attendance')?>";
        makeAjaxPostText(data,url,load).then((response) =>{
            if(response){
                swalRedirect('','successfully Deleted.');
            }else{
                swalError();
            }
        });
    });


    var selected_temp_attendance = [];
    $(document).on('click','.item',function (e) {
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

    function actionManager(selected_temp_attendance){
        if(selected_temp_attendance.length < 1){
            $('#edit_attendance').fadeOut();
            $('#delete_attendance').fadeOut();
        }else if(selected_temp_attendance.length == 1){
            $('#edit_attendance').fadeIn();
            $('#delete_attendance').fadeIn();
        }else{
            $('#edit_attendance').fadeOut();
            $('#delete_attendance').fadeOut();
        }
    }
</script>
@endsection