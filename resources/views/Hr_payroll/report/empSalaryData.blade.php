@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Salary Statement</h3>
                        <div class="ibox-tools">
                            @if(isset($salary_sheet_exist))
                            <button id="final_settlement" class="btn btn-success btn-xs item_edit" style="display: none"><i class="fa fa-bar-chart" aria-hidden="true"></i> Final Settlement</button>
                            @endif
                            {{--<button id="salary_edit" class="btn btn-warning btn-xs item_edit ladda-button" style="display: none"><i class="fa fa-edit" aria-hidden="true"></i> Edit Salary</button>--}}
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="salary_sheet_form" action="{{route('employee-salary-requisition')}}" method="post">
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
                                            {{--<div class="col-md-3">--}}
                                                {{--<div class="form-group row">--}}
                                                    {{--<label class="col-sm-12 form-label"><strong>Salary Grade--}}
                                                        {{--</strong></label>--}}
                                                    {{--<div class="col-sm-12">--}}
                                                        {{--{{__combo('hr_emp_grades_list', array('selected_value'=> $hr_emp_grades_list))}}--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
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
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-control form-control multi" name="status">
                                                        <option value="Active" @if( isset($status) && $status == "Active") selected @endif>Active</option>
                                                        <option value="Separated" @if( isset($status) &&  $status=="Separated") selected @endif>Separated</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-9 mt-3 pt-1">
                                                <button type="submit" class="btn btn-primary"><i
                                                            class="fa fa-search"></i>
                                                    Filter
                                                </button>

                                                {{--<button type="button" id="makepdf" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>--}}
                                                <button type="button" id="exportExcel" class="btn btn-info "><i class="fa fa-file-excel-o"></i> Excel</button>
                                                <a class="btn btn-warning btn" href=""><i
                                                            class="fa fa-resolving"></i> Reset</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                        <br>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                @if(@$salary_month == date('Y-m') && !isset($salary_sheet_exist))
                                    @php($data = array(

                                    'salary_component'=>$salary_component,
                                    'employeeList'=>$employeeList,
                                    ))
                                    @include('Hr_payroll/report/employee_salary_statement',$data)
                                @else
                                <table id="employee_list"
                                       class="checkbox-clickable table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">SL No.</th>

                                        <th rowspan="2">Distributor Point</th>
                                        <th rowspan="2">Employee Name</th>
                                        <th rowspan="2">Employee Code</th>
                                        <th class="text-center" colspan="3">Attendance</th>
                                        <th class="text-center" colspan="{{count($salary_component)+4}}">Fixed Salary</th>
                                        <th class="text-center" colspan="3">PFP Salary</th>
                                        <th rowspan="2">Net Salary</th>
                                    </tr>
                                    <tr>
                                        <th>Present Days</th>
                                        <th>Leave Days</th>
                                        <th>Absent Days</th>
                                        <th>Basic</th>
                                        @if(!empty($salary_component))
                                            @foreach($salary_component as $component)
                                                <th>{{__lang($component->component_slug)}}</th>
                                            @endforeach
                                        @endif
                                        <th>Total</th>
                                        <th>(-)PF</th>
                                        <th>Earn Salary</th>
                                        <th>PFP Target</th>
                                        <th>PFP Achieve</th>
                                        <th>PFP Earn</th>
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
                                                {{--<td>{{$emp->hr_emp_grade_name}}</td>--}}
                                                <td class="text-right">{{number_format($emp->present_days,0)}}</td>
                                                <td class="text-right">{{number_format($emp->number_of_leave,0)}}</td>
                                                <td class="text-right">{{number_format($emp->absent_days,0)}}</td>
                                                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                                                @if(!empty($salary_component))
                                                    @foreach($salary_component as $component)
                                                        @php($slug_name = $component->component_slug)
                                                        <td class="text-right">{{number_format($emp->$slug_name,2)}}</td>
                                                    @endforeach
                                                @endif

                                                <td class="text-right">{{number_format($emp->gross,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pf_amount_employee,2)}}</td>
                                                <td class="text-right">{{number_format($emp->net_payable,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pfp_target_amount,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pfp_achieve_ratio,2)}}%</td>
                                                <td class="text-right">{{number_format($emp->pfp_earn_amount,2)}}</td>
                                                <td class="text-right">{{number_format($emp->net_payable+$emp->pfp_earn_amount,2)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                    @endif
                            </div>
                            {{--<div class="col-md-12 mt-2">{{ $employeeList->appends(Input::except('page'))->links() }}</div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>




$('#employee_list').dataTable();
    var selected_emp = [];
    var send_leave = [];
    $(document).on('click','.checkbox-clickable tbody tr',function (e) {

        $obj = $(this);
        if(!$(this).attr('id')){ return true; }

        /*add this for new customize*/
        selected_emp = [];
        send_leave = [];
        $('.checkbox-clickable tbody tr').not($(this)).removeClass('selected');
        /* end this */

        $obj.toggleClass('selected');

        var id = $obj.attr('id');

        if ($obj.hasClass( "selected" )){
            selected_emp.push(id);
            send_leave.push($obj.data('status'));
        }else{
            var index = selected_emp.indexOf(id);
            selected_emp.splice(index,1);
            send_leave.splice($.inArray($obj.data('status'), send_leave), 1);

        }

        if(selected_emp.length==1){
            $('.item_edit').show();
        }else{
            $('.send_for_approval_leave, .item_delete_leave').show();
            $('.item_edit').hide();
        }

    });
    $('#salary_month').datetimepicker({
        'format':'YYYY-MM'
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
    var url='{{route("employee-salary-requisition",['type'=>'excel'])}}';
    $.ajax({
        type:'get',
        url:url,
        data:data,
        success:function (data) {
            //console.log(data);
            window.location.href = './public/export/' + data.file;
            swalSuccess('Export Successfully');
        }
    });
});

    $(document).on('click','#final_settlement',function () {
        var url = '<?php echo URL::to('get-hr-final-settlement');?>';
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