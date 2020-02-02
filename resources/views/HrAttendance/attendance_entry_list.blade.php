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
        .checkAttendance{
            visibility: hidden;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Attendance Entry List</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ibox">
                                {{--<div class="ibox-title  bg-white">--}}
                                    {{--<h5> {{__lang('Attendance Manual Entry')}}</h5>--}}
                                    {{----}}
                                {{--</div>--}}

                                <div class="ibox-content  bg-white">
                                    <form action="{{route('attendance-entry')}}" method="post" id="attendanceForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="font-normal"><strong>{{__lang('Date')}} </strong><span class="required">*</span></label>
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" name="attendance_date" class="form-control" id="attendance_date" data-error="Please select Date" value="{{!empty($search_date)?$search_date:''}}" placeholder="YYYY-MM-DD"  required="" autocomplete="off">
                                                    </div>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal"><strong>{{__lang('Designation')}} </strong><span class="required">*</span></label>
                                                <div class="form-group">
                                                    @if(!empty($designation_id))
                                                        {{__combo('designations', ['multiple' => 1, 'selected_value' =>$designation_id])}}
                                                    @else
                                                        {{__combo('designations', ['multiple' => 1])}}
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal"><strong>{{__lang('Distributor Points')}} </strong><span class="required">*</span></label>
                                                <div class="form-group">
                                                    @if(!empty($dpid))
                                                        {{__combo('bat_distributor_point_multi', ['multiple' => 0, 'selected_value' =>$dpid])}}
                                                    @else
                                                        {{__combo('bat_distributor_point_multi',['multiple' => 0])}}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group" style="margin-top:28px;">
                                                    <button class="btn btn-primary btn" name="submit" type="submit">{{__lang('Search')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">
                                        <table class="table table-striped table-bordered" id="example">
                                            <thead>
                                                <tr>
                                                    <th>Employee Code</th>
                                                    <th>Employee Name</th>
                                                    <th>Distributor Point</th>
                                                    <th>Designation</th>
                                                    <th>Route Number</th>
                                                    <th>Status</th>
                                                    <th>Assign EFF</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($users))
                                                   @foreach($users as $info)
                                                       @php
                                                       $cDate=date('Y-m-d');
                                                       $cls_name_status_btn = 'cls_status_'.$info->user_code;
                                                       $cls_name_status_td = 'cls_status_td_'.$info->user_code;
                                                       @endphp

                                                        <tr class="text-center">
                                                            <td>{{$info->user_code}}</td>
                                                            <td>{{$info->name}}</td>
                                                            <td>{{$info->distibutor_point}}</td>
                                                            <td>{{$info->designations_name}}</td>
                                                            <td>{{$info->route_number}}</td>
                                                            @if($info->day_is==$cDate)

                                                            <td class="{{$cls_name_status_td}}">
                                                              @if($info->daily_status == 'Lv')
                                                                   Leave
                                                                   
                                                              @else
                                                                <input type="hidden" value="{{$search_date}}" id="attend_date">
                                                                <input type="checkbox" data-usercode="{{$info->user_code}}" class="checkAttendance {{$cls_name_status_btn}}" value="{{$info->id}}"
                                                                       @if($info->daily_status == 'P') checked @endif 
                                                                       data-toggle="toggle" data-on="Present" data-off="Absent" data-offstyle="danger" >
                                                              @endif
                                                                  <span class="no-display">{{$info->daily_status}}</span>
                                                            </td>                                                            
                                                            
                                                            @else
                                                            <td>
                                                                @php
                                                                    $class = $status = '';
                                                                        $stat=$info->daily_status;
                                                                        if($stat=='A')
                                                                        {
                                                                            $status='Absent';
                                                                            $class='btn btn-warning';
                                                                        }
                                                                        elseif($stat=='P'){
                                                                         $status='Present';
                                                                         $class='btn btn-info';
                                                                        }

                                                                         elseif($stat=='Lv')
                                                                         $status='Leave';
                                                                @endphp
                                                                <div id="status_edit_{{$info->id}}" class="text-center" style="display:block">
                                                                    <button  class="{{$class}} a-btn-slide-text">
                                                                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                                                                        <span><strong>{{$status}}</strong></span>            
                                                                    </button>
                                                                </div>
                                                                <div id='stat_edit_{{$info->id}}' style='display:none' class="text-center" width='100%'>
                                                                <input type="hidden" value="{{$search_date}}" id="attend_date">
                                                                <input type="checkbox" class="checkAttendance" id="abc_{{$info->id}}" value="{{$info->id}}" 
                                                                    @if($info->daily_status == 'P') checked @endif 
                                                                       data-toggle="toggle" data-on="Present" data-off="Absent" data-offstyle="danger" >
                                                                </div>
                                                                <span class="no-display">{{$status}}</span>
                                                            </td>
                                                            @endif

                                                            <?php
                                                            $cls_name = 'cls_'.$info->user_code;
                                                            $cls_name_btn = 'cls_btn_'.$info->user_code;
                                                            $cls_name_emp = 'emp_cls_'.$info->user_code;
                                                            ?>
                                                            <td style="width: 280px;">
                                                                <form action="{{route('route-list-with-ff')}}" method="post" class="{{$cls_name}}">
                                                                    <input type="hidden" value="{{$search_date}}" name="present_date">
                                                                    <input type="hidden" value="{{$dpid}}" name="esr_dpid">
                                                                    <input type="hidden" value="{{$info->route_number}}" name="esr_route_number">
                                                                    <input type="hidden" value="{{$info->user_code}}" name="current_user_code">


                                                                    <div class="input-group">
                                                                        {{--<select class="custom-select" id="inputGroupSelect04">--}}
                                                                            {{--<option selected>Choose...</option>--}}
                                                                            {{--<option value="1">One</option>--}}
                                                                            {{--<option value="2">Two</option>--}}
                                                                            {{--<option value="3">Three</option>--}}
                                                                        {{--</select>--}}
                                                                        <select name="assign_emp_code" id="inputGroupSelect04" class="custom-select {{$cls_name_emp}} common-sr-select-box">

                                                                        </select>
                                                                        <div class="input-group-append">
                                                                            {{--<button class="btn btn-outline-secondary btn-primary" type="button">Button</button>--}}
                                                                            <button data-usercode="{{$cls_name}}" type="submit" name="assign" class="btn btn-outline-secondary btn-primary formSubmit {{$cls_name_btn}}">
                                                                            Assign</button>
                                                            </div>
                                                        </div>


                                                        {{--<div class="col-md-12 row">--}}
                                                                        {{--<div class="col-md-8">--}}
                                                                            {{--<select name="assign_emp_code" id="" class="form-control {{$cls_name_emp}} common-sr-select-box">--}}

                                                                            {{--</select>--}}
                                                                        {{--</div>--}}
                                                                        {{--<div class="col-md-4">--}}
                                                                            {{--<button data-usercode="{{$cls_name}}" type="submit" name="assign" class="btn btn-xs btn-primary formSubmit {{$cls_name_btn}}" style="margin-top: 5px;">--}}
                                                                                {{--<i class="fa fa-road" aria-hidden="true"></i> Assign</button>--}}
                                                                        {{--</div>--}}
                                                                    {{--</div>--}}
                                                                </form>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <button class="btn btn-primary" id="atten_submit" type="button">{{__lang('Submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .toggle.btn{
            min-width: 120px;
        }
    </style>
<script>

    //date picker
    function datepic() {
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", autoclose: true, endDate: '+0d'});
    }
    datepic();

    function getExtraSr(){
        var current_date = $('#attendance_date').val();
        var dpid = $('#bat_dpid').val();
        var designations_ary = $('#designations').val();
        // console.log(designations_ary+'--');
        var data = {"_token": "{{ csrf_token() }}",current_date: current_date, dpid: dpid, designations:designations_ary};
        var url = "{{url('get-extra-sr-list')}}";

        // alert(current_date);
        makeAjaxPost(data,url,null).done(function (response) {
            if(response.code == 500){
                swalError(response.msg);
            }
            else{
                console.log("------>"+response.ex_sr_list);
                var option_string = "<option value=''>---Extra SR---</option>";
                $.each( response.ex_sr_list, function( key, value ) {
                    option_string += "<option value='"+value.user_code+"'>"+value.name+"</option>";
                });

                $('.common-sr-select-box').html(option_string);
                // console.log(option_string);
                var total_user_ary = [];

                if(response.all_ex_sr_list.length > 0){
                    $.each( response.all_ex_sr_list, function( key, value ) {
                        total_user_ary[value.user_code] = value.name;
                    });

                    console.log(total_user_ary);

                    $.each( response.users, function( key, value ) {
                        // alert( key + ": " + value );
                        // console.log(value.user_code+"--"+value.alter_user_code+'---'+value.route_number);
                        var cls_name_emp = 'emp_cls_'+value.user_code;
                        var cls_name_btn = 'cls_btn_'+value.user_code;
                        var cls_status_btn = 'cls_status_'+value.user_code;
                        var cls_status_td = 'cls_status_td_'+value.user_code;


                        if((value.daily_status == 'A' || value.daily_status == 'Lv') && value.designations_id == 152){
                            $('.'+cls_name_emp).show();
                        }

                        $(document).on('change','.'+cls_name_emp,function () {
                            // alert(cls_name_emp+'--'+cls_name_btn+'--'+$('.'+cls_name_emp).val().length);
                            if($('.'+cls_name_emp).val().length > 0){
                                $('.'+cls_name_btn).show();
                            }
                            else{
                                $('.'+cls_name_btn).hide();
                            }
                        });

                        // console.log(value.alter_user_code);

                        if(value.alter_user_code != null && value.alter_user_code != 0){
                            $('.'+cls_name_emp).append("<option selected='selected' value='"+value.alter_user_code+"'>"+total_user_ary[value.alter_user_code]+"</option>");
                            $('.'+cls_status_btn).prop('disabled', true);

                            $('.'+cls_status_td).html("<button class='btn form-control'>Absent</button>");
                        }
                    });
                }

            }
        });
    }

    $(document).ready(function () {
        $('.formSubmit').hide();
        $('.common-sr-select-box').hide();

        $(document).on('click', '#atten_submit', function () {
            swalConfirm('to Save Attendance Data').then(function (data) {
                window.location.reload();
            });
        });

        getExtraSr();

        var users = @json($users);
        $.each(users, function (i, v) {
            $(document).on('click', '#status_edit_' + v['id'], function () {
                swalConfirm('to Confirm Edit Status?').then(function (e) {
                    if (e.value) {
                        $("#status_edit_" + v['id']).nextAll("#stat_edit_" + v['id']).first().show();
                        $("#status_edit_" + v['id']).hide();
                    }
                });
            });
        });

    });

    $('#example').DataTable({
        "paging": false
    });
    $(document).on('change', '.checkAttendance', function () {
        // alert($(this).val());
        var status = "";

        // var user_code = $(this).attr('data-usercode');
        // var cls_name_emp = 'emp_cls_'+user_code;

        if ($(this).prop("checked") == true) {
            status = "Present";
            //show extra sr dropdown
            // $('.'+cls_name_emp).hide();
        } else if ($(this).prop("checked") == false) {
            status = "Absent";
            //show extra sr dropdown
            // $('.'+cls_name_emp).show();
        }

        var user_id = $(this).val();
        var date = $('#attend_date').val();
        var url = "{{url('emp-change-attendance')}}/" + user_id + "/" + date + "/" + status;

        $.get(url, function (data, status) {

            var cls_name_emp = 'emp_cls_'+data.user_code;
            if (data.attendance == "P") {
                //show extra sr dropdown
                $('.'+cls_name_emp).hide();
            } else if (data.attendance == "A" && data.designations_id == 152) {
                //show extra sr dropdown
                $('.'+cls_name_emp).show();
                // alert(data.attendance+'--'+data.user_code+'--'+cls_name_emp);
            }

            getExtraSr();
        });
    });


    $(document).on('click','.formSubmit',function (e) {
        e.preventDefault();

        var cls_name = $(this).attr("data-usercode");
        var assign_emp_name = $('.emp_'+cls_name).val();

        // alert($('.'+cls_name).serialize()+'---'+assign_emp_name);
        if(assign_emp_name.length > 0 ){
            swalConfirm('Are You Sure ?').then(function(e) {
                if(e.value){
                    var data = $('.'+cls_name).serialize();
                    var url = "{{url('assign-route-for-esr')}}";

                    makeAjaxPost(data,url,null).done(function (response) {
                        if(response.code == 500){
                            swalError(response.msg);
                        }
                        else{
                            {{--swalRedirect("{{url('attendance-entry')}}", 'success');--}}
                            // var cls_status_btn = 'cls_status_'+response.current_user_code;
                            // $('.'+cls_status_btn).prop('disabled', true);
                            // alert(cls_status_btn);
                            getExtraSr();
                            swalSuccess('Extra SR Assigned Successfully!');
                        }
                    });
                }
            });
        }
        else{
            swalError("Please, Select A Employee First!");
        }
    });



</script>
@endsection

