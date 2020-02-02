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
                        <h3>Monthly PF Sheet</h3>
                        <div class="ibox-tools">
                            <button id="final_settlement" class="btn btn-success btn-xs item_edit" style="display: none"><i class="fa fa-bar-chart" aria-hidden="true"></i> Final Settlement</button>
                            {{--<button id="salary_edit" class="btn btn-warning btn-xs item_edit ladda-button" style="display: none"><i class="fa fa-edit" aria-hidden="true"></i> Edit Salary</button>--}}
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="salary_sheet_form" action="{{route('hr-pf-report')}}" method="post">
                                        @csrf
                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label"><strong><span class="required">*</span> Month of Salary
                                                        </strong></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="salary_month" value="{{@$salary_month}}" id="salary_month" required class="form-control"/>
                                                    </div>
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
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-primary"><i
                                                            class="fa fa-search"></i>
                                                    Filter
                                                </button>
                                                <button type="button" id="makepdf" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
                                                <button type="button" id="exportExcel" class="btn btn-info"><i class="fa fa-file-excel-o"></i> Excel</button>
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
                                <table id="employee_list"
                                       class="checkbox-clickable table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">SL No.</th>

                                        <th rowspan="2">Distributor Point</th>
                                        <th rowspan="2">Employee Name</th>
                                        <th rowspan="2">Employee Code</th>
                                        <th rowspan="2">Basic Salary</th>
                                        <th colspan="2" class="text-center">PF Amount</th>
                                        <th rowspan="2">Total PF Amount</th>
                                    </tr>
                                    <tr>
                                        <th>Employee Contribution</th>
                                        <th>Company Contribution</th>
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
                                                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pf_amount_employee,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pf_amount_company,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pf_amount_employee+$emp->pf_amount_company,2)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 mt-2">{{ $employeeList->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
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
            var url='{{route("hr-pf-report",['type'=>'excel'])}}';
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

    </script>
@endsection