@extends('layouts.app')
@section('content')
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">--}}
    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>--}}
    {{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>--}}
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
                    <h2>Route List</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ibox">

                                <div class="ibox-content  bg-white">
                                    <form action="{{route('route-list-with-ff')}}" method="post" id="">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="font-normal">Distributor Point</label>
                                                {{--{{__combo('bat_distributor_point_all', ['multiple' => 0, 'selected_value' =>$point])}}--}}
                                                {{__combo('bat_distributor_point_all', ['multiple' => 0, 'selected_value' =>$point, 'name'=> 'change_point',
                                                'attributes' =>['class'=>'change_point form-control','id'=>'change_point']])}}
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-normal">FF Type</label>
                                                <div class="form-group">
                                                    <select name="change_designation_id" id="change_designation_id" class="form-control">
                                                        <option value="152" @if($designation_id == 152) selected="selected" @endif>SR</option>
                                                        <option value="151" @if($designation_id == 151) selected="selected" @endif>SS</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Select Date</label>
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" value="{{$change_date}}" name="change_date" class="form-control" id="change_date" data-error="Please select Date" value="" placeholder="YYYY-MM-DD"  required="" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
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
                                                <th>Route Number</th>
                                                <th>Distributor Point</th>
                                                <th>FF Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($route_list) > 0)
                                                    @foreach($route_list as $info)
                                                        {{--@if($designation_id==151)--}}
                                                            {{--@php($user_id = $info->ssid)--}}
                                                        {{--@else--}}
                                                            {{--@php($user_id = $info->srid)--}}
                                                        {{--@endif--}}
                                                        <tr>
                                                            <td>{{$info->number}}</td>
                                                            <td>{{$info->point_name}}</td>
                                                            <td>
                                                                @if(empty($info->sys_users_id))
                                                                    <?php
                                                                    $cls_name = 'cls_'.$info->number;
                                                                    $cls_name_btn = 'cls_btn_'.$info->number;
                                                                    $cls_name_emp = 'emp_cls_'.$info->number;
                                                                    ?>
                                                                    {{--<form action="{{route('route-list-with-ff')}}" method="post" class="{{$cls_name}}">--}}
                                                                        {{--<input type="hidden" value="{{$point}}" name="bat_dpid">--}}
                                                                        {{--<input type="hidden" value="{{$designation_id}}" name="designation_id">--}}
                                                                        {{--<input type="hidden" value="{{$info->number}}" name="route_number">--}}
                                                                        {{--<input type="hidden" value="{{$user_id}}" name="user_id">--}}

                                                                        <div class="row col-md-12">
                                                                            <div class="col-md-8">
                                                                                {{--<select name="assign_emp_id" id="" class="form-control multi {{$cls_name_emp}}">--}}
                                                                                    {{--<option value="">-- Assign @if($designation_id==151) SS @else SR @endif --</option>--}}
                                                                                    {{--@if(count($emp_not_assign_list) > 0)--}}
                                                                                        {{--@foreach($emp_not_assign_list as $list)--}}
                                                                                            {{--<option value="{{$list->id}}">{{$list->name}}</option>--}}
                                                                                        {{--@endforeach--}}
                                                                                    {{--@endif--}}
                                                                                {{--</select>--}}
                                                                                {{--<select name="change_emp_id" id="change_emp_id" class="form-control change_emp_id">--}}

                                                                                {{--</select>--}}

                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                {{--<button data-number="{{$cls_name}}" type="submit" name="assign" class="btn btn-xs btn-primary formSubmit {{$cls_name_btn}}" style="margin-top: 5px;"><i class="fa fa-road" aria-hidden="true"></i> Assign Route</button>--}}
                                                                                <button type="button" class="btn btn-info btn-xs assign-btn" data-number="{{$info->number}}" data-toggle="modal" data-target="#myModal">Change/Assign Route</button>
                                                                            </div>
                                                                        </div>
                                                                    {{--</form>--}}
                                                                @else
                                                                    <div class="row col-md-12">
                                                                        <div class="col-md-8">
                                                                            <select name="" id="" class="form-control" readonly="">
                                                                                <option value="">{{$info->emp_name}}</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            @if(!empty($info->emp_name))
                                                                                <button class="btn btn-xs empty_route btn-danger" data-userid="{{$info->sys_users_id}}" data-companyid="{{$info->bat_company_id}}" data-number="{{$info->number}}" data-dpid="{{$info->bat_dpid}}"><i class="fa fa-trash" aria-hidden="true"></i> Change Route</button>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
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
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="#" method="post" id="change_assign_route">
                    <div class="modal-header">
                        {{--<button type="button" class="close" data-dismiss="modal">&times;</button>--}}
                        <h4 class="modal-title">Change/Assign Route</h4>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" value="{{$change_date}}" name="hdn_date" class="hdn_date">
                        <input type="hidden" value="" name="hdn_dpid" class="hdn_dpid">
                        <input type="hidden" value="" name="hdn_designation_id" class="hdn_designation_id">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Route Number</strong> </label>
                                    <input type="text" value="" name="hdn_route_number" class="hdn_route_number form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="font-normal"><strong>FF Name</strong> <span class="required">*</span></label>
                                    <select name="change_emp_id" id="change_emp_id" class="form-control change_emp_id">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn" type="button" id="change_assign_route_submit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <style>
        .toggle.btn{
            min-width: 120px;
        }
    </style>
    <script>

        // $('#example').dataTable();

        //date picker
        function datepic(){
            $('.input-group.date').datepicker({
                format: "yyyy-mm-dd",
                autoclose:true,
                // startDate: "now()"
            });
        }

        function srSSList(point=null, designation=null){

            var ary_sr_ss = @json($emp_sr_ss_list);
            {{--var ary_route_list = @json($route_list_dd);--}}
            var user_string = "";
            var route_string = "";

            user_string += "<option value=''>Select FF First</option>";
            $.each( ary_sr_ss, function( key, value ) {
                // console.log(value.designations_id+"---");
                if(point.length > 0){
                    if(point == value.bat_dpid && designation == value.designations_id){
                        user_string += "<option value='"+value.id+"'>"+value.name+"</option>";
                    }
                }
                else{
                    user_string += "<option value=''>NO Data Found</option>";
                }
            });

            $('.change_emp_id').html(user_string);
            // $("#change_emp_id").multiselect("rebuild");

            // $.each( ary_route_list, function( key, value ) {
            //     console.log(value.designations_id+"---");
            //     if(point.length > 0){
            //         // alert(route_string+"--In");
            //         if(point == value.dpid){
            //
            //             route_string += "<option value='"+value.number+"'>"+value.number+"-"+value.distibutor_point+"</option>";
            //         }
            //     }
            //     else{
            //         route_string += "<option value=''>NO Data Found</option>";
            //     }
            // });
            //
            // $('#change_route_number').html(route_string);
            // $("#change_route_number").multiselect("rebuild");


        }

        $(document).ready(function () {
            datepic();

            var change_designation_id = $('#change_designation_id').val();
            var change_point = $('#change_point').val();

            srSSList(change_point, change_designation_id);

            // alert(change_designation_id+'--'+change_point);
        });


        // $(document).on('change','#change_point, #change_designation_id',function () {
        //     var change_designation_id = $('#change_designation_id').val();
        //     var change_point = $('#change_point').val();
        //
        //     srSSList(change_point, change_designation_id);
        // });

        $(document).on('change','#change_date',function () {

            // alert($('#change_date').val());
            $('.hdn_date').val($('#change_date').val());
        });

        $(document).on('click','.assign-btn',function () {

            var route_number = $(this).attr("data-number");

            $('.hdn_route_number').val(route_number);
            $('.hdn_dpid').val($('#change_point').val());
            $('.hdn_designation_id').val($('#change_designation_id').val());
        });

        $(document).on('click','#change_assign_route_submit',function (e) {
            e.preventDefault();

            if($('#change_emp_id').val().length > 0){

                var route_list_ary = @json($route_list);
                var mssgg = '';
                $.each( route_list_ary, function( key, value ) {
                    // console.log(value.sys_users_id+"-----");
                    // alert('y');
                    if(value.sys_users_id == $('#change_emp_id').val()){
                        mssgg = "This User Already Assigned In Route :"+value.number;
                    }
                });

                swalConfirm(mssgg.length > 0?mssgg:"Want To Assign This User!").then(function(e) {
                    if(e.value){
                        var data = $('#change_assign_route').serialize();
                        var url = "{{url('assign-route')}}";

                        makeAjaxPost(data,url,null).done(function (response) {
                            if(response.code == 500){
                                swalError(response.msg);
                            }
                            else{
                                {{--swalRedirect("{{url('route-list-with-ff')}}/"+response.dpid+'/'+response.designation_id+'/'+response.change_date, response.msg, 'success');--}}
                                swalSuccess(response.msg);
                                setTimeout(function() {
                                    location.href = "{{url('route-list-with-ff')}}/"+response.dpid+'/'+response.designation_id+'/'+response.change_date;
                                }, 2000);
                            }
                        });
                    }
                });
            }
            else{
                swalError("Please Select Field Force!")
            }

        });

        $(document).on("click",".empty_route",function() {

            var route_number = $(this).attr("data-number");
            var dp_id = $(this).attr("data-dpid");
            var company_id = $(this).attr("data-companyid");
            var user_id = $(this).attr("data-userid");
            var designation_id = $('#change_designation_id').val();
            var change_date = $('#change_date').val();

            if (route_number.length === 0) {
                swalError("Please Select A Route.");
                return false;
            } else {
                swalConfirm('to Confirm Empty Route?').then(function (e) {
                    if(e.value){
                        var url = "{{URL::to('empty-route')}}";
                        var data = {change_date:change_date, route_number:route_number, dp_id: dp_id, designation_id:designation_id, company_id:company_id, user_id:user_id};
                        makeAjaxPost(data,url,null).then(function(response) {

                            swalSuccess(response.msg);
                            setTimeout(function() {
                                location.href = "{{url('route-list-with-ff')}}/"+response.dpid+'/'+response.designation_id+'/'+response.change_date;
                            }, 2000);
                        });
                    }
                });

            }
        });

        {{--$(document).on('click','.formSubmit',function (e) {--}}
            {{--e.preventDefault();--}}

            {{--swalConfirm('Are You Sure ?').then(function(e) {--}}
                {{--if(e.value){--}}
                    {{--var data = $('.'+cls_name).serialize();--}}
                    {{--var url = "{{url('assign-route')}}";--}}

                    {{--makeAjaxPost(data,url,null).done(function (response) {--}}
                        {{--if(response.code == 500){--}}
                            {{--swalError(response.msg);--}}
                        {{--}--}}
                        {{--else{--}}
                            {{--alert(response.change_date);--}}
                            {{--var redirect_url = "{{URL::to('route-list-with-ff')}}/"+response.dpid+'/'+response.designation_id+'/'+response.change_date;--}}
                            {{--swalRedirect(redirect_url, response.msg, 'success');--}}
                        {{--}--}}
                    {{--});--}}
                {{--}--}}
            {{--});--}}

        {{--});--}}











        $(document).ready(function () {

            var ary = @json($route_list);
            if(ary.length > 0){
                $.each( ary, function( key, value ) {
                    // alert( key + ": " + value );
                    var cls_name_emp = 'emp_cls_'+value.number;
                    var cls_name_btn = 'cls_btn_'+value.number;

                    $(document).on('change','.'+cls_name_emp,function () {
                        // alert(cls_name_emp);
                        if($('.'+cls_name_emp).val() > 0){
                            $('.'+cls_name_btn).removeClass("invisible");
                            $('.'+cls_name_btn).show();
                        }
                        else{
                            $('.'+cls_name_btn).hide();
                        }
                    });
                });
            }
        });

        // delete employee
        // $(".empty_route").on('click', function (e) {




    </script>
@endsection

