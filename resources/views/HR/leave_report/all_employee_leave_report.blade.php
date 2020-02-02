@extends('layouts.app')


@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Leave Balance </h3>
                    </div>
                    <div class="ibox-content">
                        <form id="leave_form" action="{{route('get-all-employee-leave-report')}}"  method="post" >
                            @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 form-label"><strong>{{__lang('Distributor Point')}}</strong></label>
                                            <div class="col-sm-12">
                                                {{__combo('bat_distributor_point_multi', array('selected_value'=> @$bat_dpid,'attributes'=>array('class'=>'form-control multi', 'id'=>'bat_dpid', 'multiple'=>'1','name'=>'bat_dpid[]')))}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 form-label"><strong>{{__lang('Designation')}}
                                                </strong></label>
                                            <div class="col-sm-12">
                                                {{__combo('hr_emp_salary_designations', array('selected_value'=> @$bat_designation,'attributes'=>array('class'=>'form-control multi', 'id'=>'bat_designation', 'multiple'=>'1','name'=>'bat_designation[]')))}}
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-3">--}}
                                        {{--<div class="form-group row">--}}
                                            {{--<label class="col-sm-12 form-label"><strong>{{__lang('Employee')}}--}}
                                                {{--</strong></label>--}}
                                            {{--<div class="col-sm-12">--}}
                                                {{--{{__combo('hr_leave_report_employee',array('selected_value'=>@$bat_users,'attributes'=>array('class'=>'form-control multi','id'=>'user_id','name'=>'bat_users')))}}--}}

                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    <div class="col-sm-3">
                                        <label class="col-sm-12 form-label"></label>
                                        <button type="submit" class="btn btn-primary btn-sm" id="filter"><i
                                                    class="fa fa-search"></i>
                                            Filter
                                        </button>
                                        <button type="button" id="makepdf" class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
                                        {{--<button type="button" id="exportExcel" class="btn btn-info btn-sm"><i class="fa fa-file-excel-o"></i> Excel</button>--}}
                                    </div>

                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-lg-12">
                                    <table id="employee_list" class="apsis_table table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center">SL No.</th>

                                            <th rowspan="2" class="text-center">Distributor Point</th>
                                            <th rowspan="2" class="text-center">Employee Name</th>
                                            <th rowspan="2" class="text-center">Employee Code</th>
                                            <th rowspan="2" class="text-center">Designation</th>
                                            <th colspan="3" class="text-center">Leave</th>

                                        </tr>
                                        <tr>
                                         <th>Entitle Days</th>
                                         <th>Enjoyed Days</th>
                                          <th>Balance Days</th>
                                        </tr>

                                        </thead>

                                        <tbody>

                                        @php($i=0)
                                        @foreach($user_info as $user)
                                           <tr>
                                            <td class="text-center">{{$i+1}}</td>
                                            <td class="text-left">{{$user->point}}</td>
                                            <td class="text-left">{{$user->user_name}}</td>
                                            <td class="text-left"> {{$user->user_code}}</td>
                                            <td class="text-left">{{$user->designations_name}}</td>
                                               <td class="text-center">{{isset($user->entitled_leave)?$user->entitled_leave:0}}</td>
                                               <td class="text-center">{{isset($user->leave_taken)?$user->leave_taken:0}}</td>
                                               <td class="text-center">{{isset($user->balance_leaves)?$user->balance_leaves:0}}</td>
                                           </tr>
                                            @php($i++)
                                        @endforeach
                                        </tbody>

                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
       // $("#employee_list tbody").hide();
       $('#makepdf').click(function () {
           var form = $('#leave_form');
           var action = form.attr('action');
           form.attr('action', action+'/pdf').attr("target","_blank");
           form.submit();
           form.attr('action', action);
           form.removeAttr('target');
       });

    </script>
@endsection
