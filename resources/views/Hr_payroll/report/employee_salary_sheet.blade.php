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
                        <h3>Salary Sheet </h3>
                        <div class="ibox-tools">
                            <button id="final_settlement" class="btn btn-success btn-xs item_edit" style="display: none"><i class="fa fa-bar-chart" aria-hidden="true"></i> Pay Slip</button>

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="salary_sheet_form" action="{{route('employee-salary-sheet')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label"><strong>Salary Month
                                                        </strong></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="salary_month" value="{{@$salary_month}}" id="salary_month" class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Salary Sheet Type <span class="required">*</span></label>
                                                <div class="form-group">
                                                    <select class="form-control" name="salary_sheet_type" required id="salary_sheet_type">
                                                        <option {{@$salary_sheet_type=='Fixed'?'selected':''}} value="Fixed">Fixed</option>
                                                        <option {{@$salary_sheet_type=='PFP'?'selected':''}} value="PFP">PFP</option>
                                                    </select>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
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
                                            <div class="col-sm-3">
                                                <label class="col-sm-12 form-label"></label>
                                                <button type="submit" class="btn btn-primary btn-sm"><i
                                                            class="fa fa-search"></i>
                                                    Filter
                                                </button>
                                                <button type="button" id="makepdf" class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
                                                <button type="button" id="exportExcel" class="btn btn-info btn-sm"><i class="fa fa-file-excel-o"></i> Excel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                        <br>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                @if(@$salary_sheet_type == 'PFP')
                                    {{--@php(dd($employeeList))--}}
                                    <table id="employee_list"
                                           class="checkbox-clickable table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>SL No.</th>

                                            <th>Distributor Point</th>
                                            <th>Employee Name</th>
                                            <th>Employee Code</th>
                                            <th>{{__lang('PFP Target Amount')}}</th>
                                            <th>{{__lang('PFP Achieve Ratio')}}</th>
                                            <th>{{__lang('PFP Earn Amount')}}</th>
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
                                                    <td class="text-right">{{number_format($emp->pfp_target_amount,2)}}</td>
                                                    <td class="text-right">{{number_format($emp->pfp_achieve_ratio,2)}}</td>
                                                    <td class="text-right">{{number_format($emp->pfp_earn_amount,2)}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                    @else
                                    <table id="employee_list"
                                           class="checkbox-clickable table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th rowspan="2">SL No.</th>

                                            <th rowspan="2">Distributor Point</th>
                                            <th rowspan="2">Employee Name</th>
                                            <th rowspan="2">Employee Code</th>
                                            <th rowspan="2">Present Days</th>
                                            <th rowspan="2">Leave Days</th>
                                            <th rowspan="2">Absent Days</th>
                                            <th colspan="{{count($salary_component)+2}}" class=" text-center">Fixed Salary</th>
                                            <th rowspan="2" class="bg_deduction">{{__lang('PF Amount')}}</th>
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
                                                    <td class="text-right bg_deduction2">{{number_format($emp->pf_amount_employee,2)}}</td>
                                                    <td class="text-right">{{number_format($emp->net_payable,2)}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                    @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="edit_id" style="display: none"></div>

    <script>
        $('#employee_list').dataTable();

        $("#salary_month").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        var selected_emp = [];
        var send_leave = [];
        $(document).on('click','.checkbox-clickable tbody tr',function (e) {
            $obj = $(this);
            if(!$(this).attr('id')){
                return true;
            }
            $obj.toggleClass('selected');
            var id = $obj.attr('id');
            // console.log(id);
            if ($obj.hasClass( "selected" )){
                selected_emp.push(id);
                send_leave.push($obj.data('status'));
            }else{
                var index = selected_emp.indexOf(id);
                selected_emp.splice(index,1);
                send_leave.splice($.inArray($obj.data('status'), send_leave), 1);

            }

            if(selected_emp.length==1){
                $('.item_edit, .item_delete').show();
            }else if(selected_emp.length==0){
                $('.item_edit, .send_for_approval_leave, .item_delete').hide();
            }else{
                $('.send_for_approval_leave, .item_delete').show();
                $('.item_edit').hide();
            }

        });
        $('#makepdf').click(function () {
            var form = $('#salary_sheet_form');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

        $('#exportExcel').click(function () {
            var $form = $('#salary_sheet_form');
            var data={};
            data = $form.serialize() + '&' + $.param(data);
            var url='{{route("employee-salary-sheet",['type'=>'excel'])}}';
            $.ajax({
                type:'get',
                url:url,
                data:data,
                success:function (data) {
                    console.log(data);
                    window.location.href = './public/export/' + data.file;
                    swalSuccess('Export Successfully');
                }
            });
        });

        $(document).on('click','#final_settlement',function () {
           @if(@$salary_sheet_type == 'PFP')
            var url = '<?php echo URL::to('get-hr-pfp-pay-slip');?>';
           @else
            var url = '<?php echo URL::to('get-hr-salary-pay-slip');?>';
           @endif
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

        });
    </script>
@endsection