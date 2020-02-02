@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Salary Increment Log</h2>
                        <div class="ibox-tools">
                            <a class="btn btn-primary btn-xs" id="add_link" href="{{url('hr-new-increment')}}"><i class="fa fa-plus-circle" aria-hidden="true"></i> New</a>
                            <button type="button" class="btn btn-primary btn-xs no-display" id="view_increment_letter"><i class="fa fa-eye"></i> Increment Letter</button>
                            <button class="btn btn-primary btn-xs no-display" id="send_for_approval_btn"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                            <button class="btn btn-warning btn-xs ladda-button no-display" id="item_edit"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                            <button class="btn btn-danger btn-xs ladda-button no-display" id="item_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('hr-promotion')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 form-label">Salary Applied Date :</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" placeholder="Salary Applied Date" autocomplete="off"
                                                   class="form-control" id="date_range" name="date_range"
                                                   value="{{$date_range}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong>Status :</strong></label>
                                        <div class="col-sm-12">
                                            {{__combo('salary_approval_status',array('selected_value'=>$salary_approval_status))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong>Salary Grade
                                                :</strong></label>
                                        <div class="col-sm-12">
                                            {{__combo('hr_emp_grades', array('selected_value'=> $hr_emp_grades, 'attributes'=> array( 'name'=>'hr_emp_grades[]', 'id'=>'hr_emp_grades','multiple'=>true,'class'=>'form-control multi')))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter </button>
                                    <a class="btn btn-warning btn" href="{{url('hr-increment')}}"><i class="fa fa-resolving"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="employee_increment_list" class="checkbox-clickable table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Employee Name</th>
                                            <th>Code</th>
                                            <th>Point</th>
                                            <th>Designation</th>
                                            <th>Applicable Date</th>
                                            <th>Current Gross Salary</th>
                                            <th>Increment Amount</th>
                                            <th>New Gross Salary</th>
                                            <th>Initiator</th>
                                            <th>Delegation Step</th>
                                            <th>Delegation Location</th>
                                            <th>Sent for Approval</th>
                                            <th>Approved At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($employeeList))
                                            @foreach($employeeList as $i=>$emp)
                                                <tr id="{{$emp->hr_employee_record_logs_id}}"  class="hr_employee_record_logs_id delegation_job_id" data-status="{{ $emp->hr_log_status }}">
                                                    <td align="center">
                                                        {{($i+1)}}
                                                    </td>
                                                    <td>{{$emp->name}}</td>
                                                    <td>{{$emp->user_code}}</td>
                                                    <td>{{$emp->point_name}}</td>
                                                    <td>{{$emp->designations_name}}</td>
                                                    <td>{{toDated($emp->applicable_date)}}</td>
                                                    <td class="text-right">{{number_format($emp->previous_gross,2)}}</td>
                                                    <td class="text-right">{{number_format($emp->gross_salary-$emp->previous_gross,2)}}</td>
                                                    <td class="text-right">{{number_format($emp->gross_salary,2)}}</td>
                                                    <td align="center">{{$emp->creator_name}}</td>
                                                    <td align="center">{{$emp->step_name}}</td>
                                                    <td align="center">{{$emp->delegation_person_name}}</td>
                                                    <td align="center">{{($emp->delegation_initialized)?toDateTimed($emp->delegation_initialized):''}}</td>
                                                    <td align="center">{{($emp->delegation_final_approved)?toDateTimed($emp->delegation_final_approved):''}}</td>
                                                    <td align="center">{{$emp->status_flows_name}}</td>
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
            $('#employee_increment_list').dataTable();

            //Date Range Picker
            $('#date_range').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply: true,
            });

            var selected_emp = [];
            var send = [];

            $(document).on('click','.checkbox-clickable tbody tr',function (e) {
                $obj = $(this);

                if(!$(this).attr('id')){  return true; }

                $obj.toggleClass('selected');

                var id = $obj.attr('id');

                if ($obj.hasClass( "selected" )){
                    selected_emp.push(id);
                    send.push($obj.data('status'));
                }else{
                    var index = selected_emp.indexOf(id);
                    selected_emp.splice(index,1);
                    send.splice($.inArray($obj.data('status'), send), 1);
                }


                if(selected_emp.length==1 && send[0] == 50) {
                    $('#view_increment_letter').show();
                }else{
                    $('#view_increment_letter').hide();
                }

                if(send.includes(49)||send.includes(50)){
                    $('#item_delete').hide();
                    $('#item_edit').hide();
                    $('#send_for_approval_btn').hide();
                }else{
                    if (selected_emp.length > 0) {
                        $('#item_delete').show();
                        $('#item_edit').show();
                        $('#send_for_approval_btn').show();
                    }else{
                        $('#item_delete').hide();
                        $('#item_edit').hide();
                        $('#send_for_approval_btn').hide();
                    }
                }
            });

            //Print Letter
            $(document).on('click','#view_increment_letter', function (e) {
                var data = {'log_id':selected_emp[0]};
                if (selected_emp.length == 1) {
                    var url= '<?php echo URL::to('get-hr-increment-letter');?>/'+selected_emp[0];
                    swalConfirm('To view Increment Letter.').then(function (e) {
                        if(e.value){
                            window.open(url,'_blank');
                        }
                    });
                } else {
                    swalWarning("Please select single item");
                    return false;
                }
            });

            //Send for approval
            $(document).on('click', '#send_for_approval_btn', function (e) {
                e.preventDefault();
                var id_slug = 'hr_inc';
                var url = '<?php echo URL::to('go-to-hr-delegation-process');?>';
                var job_value = selected_emp;

                if(job_value.length){
                    swalConfirm().then(function (e) {
                        if(e.value){
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {slug:id_slug,code:job_value,'delegation_type':'send_for_approval'},
                                success: function (data) {
                                    var url = window.location;
                                    swalRedirect(url,data,'success');
                                },
                                failure: function() {
                                    swalError('Failed');
                                }
                            });
                        }
                    });
                }else{
                    swalWarning("Please select at least one job!");
                }
            });

            //Item Delete
            $(document).on('click', '#item_delete', function (e) {
                e.preventDefault();
                Ladda.bind(this);
                var load = $(this).ladda();
                var log_id = selected_emp;
                var data = {log_id:log_id};
                var url = '<?php echo URL::to('hr-record-delete');?>';
                if(log_id.length) {
                    swalConfirm("Delete Selected Items").then(function (e) {
                        if (e.value) {
                            makeAjaxPost(data,url,load).done(function (response) {
                                var url2 = window.location;
                                if(response.success){
                                    swalRedirect(url2,"Successfully Delete",'success');
                                }else{
                                    swalWarning('Operation Failed!');
                                }
                            });
                        }else{
                            load.ladda('stop');
                        }
                    });
                }else{
                    swalWarning("Please select at least one job!");
                }
            });

            //Item Edit
            $(document).on('click', '#item_edit', function (e) {
                e.preventDefault();
                Ladda.bind(this);
                var load = $(this).ladda();
                var log_id = selected_emp;

                var data = {inc_ratio:'',log_id:log_id, '_token':$('input[name="_token"]').val()};
                var url = '<?php echo URL::to('hr-new-increment');?>';
                if(log_id.length) {
                    swalConfirm("To edit selected items").then(function (e) {
                        if (e.value) {
                            $.redirectPost(url,data);
                        }else{
                            load.ladda('stop');
                        }
                    });
                }else{
                    swalWarning("Please Select at least one items");
                }
            });
            $.extend({
                redirectPost: function(location, args) {
                    var form = $('<form></form>');
                    form.attr("method", "post");
                    form.attr("action", location);
                    $.each( args, function( key, value ) {
                        var field = $('<input></input>');
                        field.attr("type", "hidden");
                        field.attr("name", key);
                        field.attr("value", value);
                        form.append(field);
                    });
                    $(form).appendTo('body').submit();
                }
            });
        });
    </script>
@endsection