@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Attendance Report for the month of <strong>{{isset($salary_config_info->month)?$salary_config_info->month:''}}-{{isset($salary_config_info->year)?$salary_config_info->year:''}}</strong></h2>
                        <div class="ibox-tools">
                            <button id="details" class="btn btn-success btn-xs" style="display: none"><i class="fa fa-external-link" aria-hidden="true"></i> Details</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-9">
                                        <form action="{{route('hr-monthly-attendance-report')}}" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 form-label"><strong>Salary Month
                                                                :</strong></label>
                                                        <div class="col-sm-12">
                                                            {{__combo('hr_salary_month_config',array('selected_value'=>$hr_salary_month_config))}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 form-label"><strong>Salary Grade
                                                                :</strong></label>
                                                        <div class="col-sm-12">
                                                            {{__combo('hr_emp_grades_list', array('selected_value'=> $hr_emp_grades_list))}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 form-label"><strong>Unit
                                                                :</strong></label>
                                                        <div class="col-sm-12">
                                                            {{__combo('hr_emp_salary_units', array('selected_value'=> $hr_emp_salary_units))}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 form-label"><strong>Department
                                                                :</strong></label>
                                                        <div class="col-sm-12">
                                                            {{__combo('hr_emp_departments', array('selected_value'=> $hr_emp_departments))}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 form-label"><strong>{{__lang('Designation')}}
                                                                :</strong></label>
                                                        <div class="col-sm-12">
                                                            {{__combo('hr_emp_salary_designations', array('selected_value'=> $hr_emp_salary_designations))}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <button type="submit" class="btn btn-primary"><i
                                                                class="fa fa-search"></i>
                                                        Filter
                                                    </button> &nbsp;
                                                    <a class="btn btn-warning btn" href=""><i
                                                                class="fa fa-resolving"></i> Reset</a>
                                                </div>
                                            </div>
                                        </form>
                                </div>
                                <div class="col-sm-3">
                                    <style>
                                        #salary_config th{
                                            padding: 0px !important;
                                        }
                                    </style>

                                    @if(!empty($salary_config_info))
                                        <table id="salary_config" class="table table-bordered">
                                           <tr>
                                                <th>Month</th>
                                                <td>{{$salary_config_info->month}}-{{$salary_config_info->year}}</td>
                                            </tr>

                                            <tr>
                                                <th>Total Days</th>
                                                <td>{{$salary_config_info->number_of_days}}</td>
                                            </tr>
                                            <tr>
                                                <th>Working Days</th>
                                                <td>{{$salary_config_info->number_of_working_days}}</td>
                                            </tr>
                                            <tr>
                                                <th>HolyDays</th>
                                                <td>{{$salary_config_info->number_of_holidays}}</td>
                                            </tr>
                                            <tr>
                                                <th>Weekend</th>
                                                <td>{{$salary_config_info->number_of_weekend}}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Employee</th>
                                                <td>{{$salary_config_info->number_of_active_emp}}</td>
                                            </tr>
                                        </table>
                                        @endif
                                </div>
                            </div>
                        </div>

                        <br>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="attendance_list"
                                       class="checkbox-clickable table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">SL No.</th>
                                        <th rowspan="2">Department</th>
                                        <th rowspan="2">Employee Name</th>
                                        <th class="text-center" colspan="3">Attendance</th>
                                        <th class="text-center" colspan="7">Salary</th>
                                    </tr>
                                    <tr>
                                        <th>Present</th>
                                        <th>Leave</th>
                                        <th>Absent</th>
                                        <th>OT(H)</th>
                                        <th>OT Ext. (H)</th>
                                        <th>Off Day (H)</th>
                                    </tr>

                                    </thead>
                                    <tbody>

                                    @if(!empty($employeeList))
                                        @foreach($employeeList as $i=>$emp)
                                            <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                                                <td align="center">
                                                    {{($i+1)}}
                                                </td>
                                                <td>{{$emp->departments_name}}</td>
                                                <td>{{$emp->name}}</td>
                                                <td class="text-right">{{number_format($emp->present_days,1)}}</td>
                                                <td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>
                                                <td class="text-right">{{number_format($emp->absent_days,1)}}</td>
                                                <td class="text-right">{{number_format(($emp->ot_hours),1)}}</td>
                                                <td class="text-right">{{number_format(($emp->ot_extra_hours),1)}}</td>
                                                <td class="text-right">{{number_format($emp->offday_ot_hours,1)}}</td>
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
    .bg_deduction{
        background-color: grey !important;
        color: #ffffff;
        opacity: 0.6;
    }
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
        // $('#attendance_list').dataTable();
        var selected_emp = [];
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
            }else{
                var index = selected_emp.indexOf(id);
                selected_emp.splice(index,1);
            }
            if(selected_emp.length==1){
                $('#details').show();
            }else{
                $('#details').hide();
            }

        });

        $(document).on('click','#empSalarySubmit',function (e) {
            var url = '<?php echo URL::to('hr-emp-salary-update');?>';
            var employeeId = selected_emp;
            var salary_month_configs_id = '{{$hr_salary_month_config}}';
            var data = $('#empSalaryEditForm').serialize()+'&emp_id='+employeeId+'&hr_salary_month_configs_id='+salary_month_configs_id;
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data,url,load).done(function(response){
                if(response.success){
                    $('#medium_modal').modal('hide');
                    swalSuccess('Salary Update Successfully');
                    setTimeout(
                        function() {
                            window.location.reload();
                        },1000
                    );

                }else{
                    swalError('Salary Update Failed.');
                }
            });
        });



        $('#details').click(function (e) {
            e.preventDefault();
            var month_config = $('#hr_salary_month_config').val();
            var user = $('#attendance_list .row-select-toggle.selected').attr('id');
            alert(user);
        })
    </script>
@endsection