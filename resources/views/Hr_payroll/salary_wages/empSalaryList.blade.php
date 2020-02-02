@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Salary Sheet month : <strong>{{isset($salary_month)?$salary_month:''}}</strong><span
                                    style="margin-left: 20px">Distributor Points:<strong> {{@$distributor_points}}</strong></span>
                        </h3>
                        <div class="ibox-tools">

                            @if($salary_sheet_status == 95)
                                <button id="final_settlement" class="btn btn-success btn-xs item_edit"
                                        style="display: none"><i class="fa fa-bar-chart" aria-hidden="true"></i> Pay Slip
                                </button>
                                {{--<button id="salary_edit" class="btn btn-warning btn-xs item_edit" style="display: none"><i class="fa fa-edit" aria-hidden="true"></i> Edit Salary</button>--}}
                                {{--<button id="salary_delete" class="btn btn-danger btn-xs item_delete" style="display: none"><i class="fa fa-trash" aria-hid                                    den="true"></i> Delete</button>--}}
                            @endif
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="salary_sheet_form"
                                          action="{{route('hr-salary-wages-emp-list'                                                        ).'/'.$sheet_code}}"
                                          method="post">
                                        @csrf
                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label"><strong>Salary Grade
                                                        </strong></label>
                                                    <div class="col-sm-12">
                                                        {{__combo('hr_emp_grades_list', array('selected_value'=> $hr_emp_grades_list))}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label"><strong>{{__lang('Distributor Point')}}</strong></label>
                                                    <div class="col-sm-12">
                                                        {{__combo('bat_distributor_point_multi', array('selected_value'=> @$bat_dpid,'attributes'=>array('class'=>'form-control multi','multiple'=>'1','name'=>'bat_dpid[]')))}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label"><strong>{{__lang('Designation')}}
                                                        </strong></label>
                                                    <div class="col-sm-12">
                                                        {{__combo('hr_emp_salary_designations', array('selected_value'=> $hr_emp_salary_designations))}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-primary"><i
                                                            class="fa fa-search"></i>
                                                    Filter
                                                </button>
                                                {{--@if($salary_disbusement!=0)--}}
                                                <button type="button" id="makepdf" class="btn btn-success"><i
                                                            class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                                                </button>
                                                {{--@endif--}}
                                                <a class="btn btn-warning btn" href=""><i
                                                            class="fa fa-resolving"></i> Reset</a>
                                                <button type="button" id="exportExcel" class="btn btn-info "><i class="fa fa-file-excel-o"></i> Excel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                        <br>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="employee_list"
                                       class="checkbox-clickable table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">SL No.</th>

                                        <th rowspan="2">Distributor Point</th>
                                        <th rowspan="2">Employee Name</th>
                                        <th rowspan="2">Employee Code</th>
                                        <th rowspan="2">Present Days</th>
                                        <th rowspan="2">Leave Days</th>
                                        <th rowspan="2">Absent Days</th>
                                        <th colspan="{{count($salary_component)+2}}" class=" text-center">Fixed Salary
                                        </th>
                                        {{--<th rowspan="2">{{__lang('PfP Amount')}}</th>--}}
                                        <th colspan="3" class="bg_deduction text-center">Deduction</th>
                                        <th rowspan="2">Net Salary</th>
                                    </tr>
                                    <tr>
                                        <th>{{__lang('Basic')}}</th>
                                        @if(!empty($salary_component))
                                            @foreach($salary_component as $component)
                                                <th>{{__lang($component->component_slug)}}</th>
                                            @endforeach
                                        @endif
                                        <th>{{__lang('Total')}}</th>
                                        <th class="bg_deduction">{{__lang('PF Amount')}}</th>
                                        <th class="bg_deduction">Advance Loan</th>
                                        <th class="bg_deduction">Salary Deduction</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @if(!empty($employeeList))
                                        @foreach($employeeList as $i=>$emp)
                                            <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                                                <td align="center">
                                                    {{($i+1)}}
                                                </td>

                                                <td>{{$emp->point_name}}</td>
                                                <td>{{$emp->name}}</td>
                                                <td>{{$emp->user_code}}</td>
                                                <td class="text-right">{{number_format($emp->present_days,1)}}</td>
                                                <td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>
                                                <td class="text-right">{{number_format($emp->absent_days,1)}}</td>
                                                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>

                                                @if(!empty($salary_component))
                                                    @foreach($salary_component as $component)
                                                        @php($slug_name = $component->component_slug)
                                                        <td class="text-right">{{number_format($emp->$slug_name,2)}}</td>
                                                    @endforeach
                                                @endif
                                                <td class="text-right">{{number_format($emp->gross,2)}}</td>
                                                {{--<td class="text-right">{{number_format($emp->max_variable_salary,2)}}</td>--}}
                                                {{--<td class="text-right">{{number_format($emp->other_conveyance,2)}}</                                                        td>--}}
                                                {{--<td class="text-right">{{number_format($emp->arrear,2)}}</                                                        td>--}}
                                                {{--<td class="text-right">{{number_format($emp->earned_salary,2)}}</td>--}}

                                                <td class="text-right bg_deduction2">{{number_format($emp->pf_amount_employee,2)}}</td>
                                                {{--<td class="text-right bg_deduction2">{{number_format($emp->absent_deduction,2)}}</td>--}}
                                                <td class="text-right bg_deduction2">{{number_format($emp->advance_deduction,2)}}</td>
                                                <td class="text-right bg_deduction2">{{number_format($emp->other_deduction,2)}}</td>

                                                {{--<td class="text-right bg_deduction2">{{number_format(($emp->absent_deduction+$emp->advance_deduction+$emp->other_deduction+$emp->card_lost_deduction+$emp->stamp_amount+$emp->pf_amount_employee+$emp->insurance_amount),2)}}</td>--}}
                                                <td class="text-right">{{number_format($emp->net_payable,2)}}</td>
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
    <div id="edit_id" style="display: none"></div>
    <style>
        .bg_deduction {
            background-color: #fdba13 !important;
            color: #ffffff;
        }

        .bg_deduction2 {
            background-color: #d2bb82 !important;
        }

        .selected {
            background-color: green;
            color: #FFF;
        }

        .selected:hover {
            background-color: green !important;
            color: #FFF;
        }
    </style>
    <script>
        $('#employee_list').dataTable();
        var date = new Date();
        date.setDate(date.getDate());
        // $('#employee_list').dataTable();
        $("#salary_month").datetimepicker({
            format: "YYYY-MM",
            maxDate: new Date()
        });
        var selected_emp = [];
        var send_leave = [];
        $(document).on('click', '.checkbox-clickable tbody tr', function (e) {
             selected_emp = [];
             send_leave = [];
            $('.checkbox-clickable tbody tr').not($(this)).removeClass('selected');
            $obj = $(this);
            if (!$(this).attr('id')) {
                return true;
            }
            $obj.toggleClass('selected');
            var id = $obj.attr('id');
            // console.log(id);
            if ($obj.hasClass("selected")) {
                selected_emp.push(id);
                send_leave.push($obj.data('status'));
            } else {
                var index = selected_emp.indexOf(id);
                selected_emp.splice(index, 1);
                send_leave.splice($.inArray($obj.data('status'), send_leave), 1);

            }

            if (selected_emp.length == 1) {
                $('.item_edit, .item_delete').show();
            } else if (selected_emp.length == 0) {
                $('.item_edit, .send_for_approval_leave, .item_delete').hide();
            } else {
                $('.send_for_approval_leave, .item_delete').show();
                $('.item_edit').hide();
            }

        });
        $('#makepdf').click(function () {
            var form = $('#salary_sheet_form');
            var action = form.attr('action');
            form.attr('action', action + '/pdf').attr("target", "_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

        $(document).on('click', '#salary_edit', function () {
            var url = '<?php echo URL::to('get-hr-emp-salary-info'); ?>';
            var employeeId = selected_emp;
            var salary_month = '{{$salary_month}}';
            var data = {'emp_id': employeeId, 'salary_month': salary_month};
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data, url, load).done(function (response) {
                if (response) {
                    $('#medium_modal .modal-content').html(response);
                    $('#medium_modal').modal('show');
                }
            });
        });

        $('#exportExcel').click(function () {
            var $form = $('#salary_sheet_form');
            var data={};
            data = $form.serialize() + '&' + $.param(data);
            var url='{{route('hr-salary-wages-emp-list')}}'+'/{{$sheet_code}}/excel';

            $.ajax({
                type:'get',
                url:url,
                data:data,
                success:function (data) {
                    console.log(data);
                    window.location.href = '.././public/export/' + data.file;
                    swalSuccess('Export Successfully');
                }
            });
        });

        $(document).on('click', '#salary_delete', function () {
            var url = '<?php echo URL::to('hr-salary-sheet-emp-delete'); ?>';
            var employeeId = selected_emp;
            var salary_month = '{{$salary_month}}';
            var data = {'emp_id': employeeId, 'salary_month': salary_month};
            Ladda.bind(this);
            var load = $(this).ladda();
            swalConfirm().then(function (e) {
                if (e.value) {

                    makeAjaxPostText(data, url, load).done(function (response) {
                        if (response.success == true) {
                            swalRedirect(window.location, 'Successfully Removed from Salary Sheet.', 'success');
                        }
                    });
                }
            });

        });
        $(document).on('click', '#final_settlement', function () {
            var url = '<?php echo URL::to('get-hr-salary-pay-slip'); ?>';
            var employeeId = selected_emp;
            var salary_month = '{{$salary_month}}';

            $('#setelmentForm').remove();

            var form = $('<form action="' + url + '"  target="_blank" method="post" style="display: none" id="setelmentForm"> @csrf' +
                '<input type="hidden" name="emp_id" value="' + employeeId + '" />' +
                '<input type="hidden" name="salary_month" value="' + salary_month + '" />' +
                '</form>');
            $('body').append(form);
            form.submit();

            $('#setelmentForm').remove();


            /*var data = {'emp_id':employeeId,'hr_salary_month_configs_id':salary_month_configs_id};
             Ladda.bind(this);
             var load = $(this).ladda();
             makeAjaxPostText(data,url,load).done(function(response){
             if(response){
             ..
             }
             });*/
        });


        $(document).on('click', '#empSalarySubmit', function (e) {
            var url = '<?php echo URL::to('hr-emp-salary-update'); ?>';
            var employeeId = selected_emp;
            var salary_month = '{{$salary_month}}';
            var data = $('#empSalaryEditForm').serialize() + '&emp_id=' + employeeId + '&salary_month=' + salary_month;
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data, url, load).done(function (response) {
                if (response.success) {
                    $('#medium_modal').modal('hide');
                    swalSuccess('Salary Update Successfully');
                    setTimeout(
                        function () {
                            window.location.reload();
                        }, 1000
                    );

                } else {
                    swalError('Salary Update Failed.');
                }
            });
        });
    </script>
@endsection
