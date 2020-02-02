@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
   <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2>Leave Report</h2>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="input-group emp_code_entry">
                                       <label class="form-label col-md-4" style="padding-top: 10px; text-align: right">Employee</label>
                                        <div class="col-md-8">{{__combo('hr_leave_report_employee')}}</div>

                                        {{--<input type="text"--}}
                                               {{--placeholder="Enter Employee Code to Proceed"--}}
                                               {{--class="form-control"--}}
                                               {{--id="user_code"--}}
                                               {{--name=""--}}
                                               {{--value=""/>--}}
                                        {{--<span class="input-group-append">--}}
                                                {{--<button type="button" class="btn btn-info" id="user_code_search"--}}
                                                        {{--data-style="zoom-out"><i class="fa fa-arrow-right"></i></button>--}}
                                            {{--</span>--}}
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" class="employee_id" value=""/>
                            </div>
                        </div>
                    </div>
                        <div class="ibox-content">
                            <div class="col-sm-12">
                                <div class="ibox" style="margin-bottom: 0;">
                                    <div class="ibox-title">
                                        <h4><i class="fa fa-user"></i> Employee Information</h4>
                                        <div class="ibox-tools">
                                            <h5 class="text-danger">Select Employee from Employee Dropdown Box</h5>
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content no-padding row" id="report_list" style="display: none;">

                                        <div class="col-sm-6">
                                            <br/>
                                            <span class="">Employee Name : &nbsp;<span class="font-bold employee_name">--</span></span><br/>
                                            <span class="">Employee Mobile : &nbsp;<span class="font-bold employee_mobile">--</span></span><br/>
                                            <span class="">Designation : &nbsp;<span class="font-bold designation_name">--</span></span><br/>
                                        </div>
                                        <div class="col-sm-6">
                                            <br/>
                                            {{--<span class="">Department : &nbsp;<span class="font-bold department_name">--</span></span><br/>--}}
                                            <span class="">Distributor House : &nbsp;<span class="font-bold distributor_house">--</span></span><br/>
                                            <span class="">Distributor Point : &nbsp;<span class="font-bold distributor_point">--</span> </span>

                                        </div>
                                       <br/>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="report_list2" style="display: none;">

                                <div class="col-4">
                                    <div class="widget bg-success text-center">
                                        <div class="m-b-md">
                                            <h3 class="m-xs text-white m-3"><i class="fa fa-calendar"></i> Monthly Leave Transaction</h3>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <h3 class="font-bold no-margins">
                                                        <div class="input-group leave_month">
                                                            <input type="text"
                                                                   placeholder=""
                                                                   class="form-control"
                                                                   value="{{ date('Y-m')}}"
                                                                   id="leave_month" name="leave_month"/>
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </h3>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button class="btn btn-primary" id="monthlyLeave">  View Report</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="widget bg-success text-center">
                                        <div class="m-b-md">
                                            <h3 class="m-xs text-white m-3"><i class="fa fa-calendar"></i> Yearly Leave Summary</h3>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <h3 class="font-bold no-margins">
                                                        <div class="input-group date_year">
                                                            <input type="text"
                                                                   placeholder=""
                                                                   class="form-control"
                                                                   value="{{ date('Y')}}"
                                                                   id="leave_year" name="leave_year"/>
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </h3>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button class="btn btn-primary" id="yearlyLeave">  View Report</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-4">
                                        <div class="widget bg-success text-center">
                                            <div class="m-b-md">
                                                <h3 class="m-xs text-white m-3"><i class="fa fa-calendar"></i> Earn Leave Record</h3>
                                                <div class="row">
                                                    <div class="col-sm-12 text-center">
                                                        <button class="btn btn-primary" id="yearlyEarnLeave">  View Report</button>
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
        </div>
    <style>
        .emp_code_entry button{
            width: 100% !important;
        }
    </style>
    <script>
        $(document).on('click','#yearlyLeave',function () {
            var url = '<?php echo URL::to('get-emp-yearly-leave');?>';
            var emp_id = $('.employee_id').val();
            if(emp_id){
                var data = {
                    'sys_users_id':emp_id,
                    'year':$('#leave_year').val()
                };
                Ladda.bind(this);
                var load = $(this).ladda();
                makeAjaxPostText(data,url,load).done(function (response) {
                    if(response){
                        $('#large_modal .modal-content').html(response);
                        $('#large_modal').modal('show');
                    }
                });
            }else{
                swalWarning('please enter a valid Employee Code');
            }
        });
        $(document).on('click','#yearlyEarnLeave',function () {
            var url2 = '<?php echo URL::to('get-emp-yearly-earn-leave');?>';
            var emp_id = $('.employee_id').val();
            if(emp_id){
                var data = {
                    'sys_users_id':emp_id,
                };
                Ladda.bind(this);
                var load = $(this).ladda();
                makeAjaxPostText(data,url2,load).done(function (response) {
                    if(response){
                        $('#large_modal .modal-content').html(response);
                        $('#large_modal').modal('show');
                    }
                });
            }else{
                swalWarning('please enter a valid Employee Code');
            }
        });

        $(document).on('click','#monthlyLeave',function () {
            var url2 = '<?php echo URL::to('get-emp-monthly-leave-report');?>';
            var emp_id = $('.employee_id').val();
            if(emp_id){
                var data = {
                    'sys_users_id':emp_id,
                    'month':$('#leave_month').val()
                };
                Ladda.bind(this);
                var load = $(this).ladda();
                makeAjaxPostText(data,url2,load).done(function (response) {
                    if(response){
                        $('#medium_modal .modal-content').html(response);
                        $('#medium_modal').modal('show');
                    }
                });
            }else{
                swalWarning('please enter a valid Employee Code');
            }
        });

        $('#leave_year').datepicker({
            viewMode: "years",
            minViewMode: "years",
            format: "yyyy",
            autoclose:true
        });
        $('#leave_month').datepicker({
            viewMode: "months",
            minViewMode: "months",
            format: "yyyy-mm",
            autoclose:true
        });

        $('#hr_leave_report_employee').multiselect({
            enableFiltering:true,
            maxHeight: 350,
            onDropdownShown: function(even) {
                this.$filter.find('.multiselect-search').focus();
            },
            onChange: function (option, checked, select) {
                var user_code = $('#hr_leave_report_employee').val();
                if (user_code.length === 0) {
                    swalError("Please Input a valid Employee Code");
                } else {
                    var url1 = '<?php echo URL::to('get-emp-info');?>/' + user_code;
                    var basic_info_ajax = makeAjax(url1, null).then(function (data) {
                        if (data.success == 0) {
                            swalError("No Employee Found for this code.");
                            $('.emp_code_entry').addClass('has-error');
                            $('.leave_submit').fadeOut();
                        }
                        else {
                            $('.emp_code_entry').removeClass('has-error');
                            if (data.user_info['name'] != null) {
                                $('.employee_name').text(data.user_info['name']);
                            }
                            if (data.user_info['mobile'] != null) {
                                $('.employee_mobile').text(data.user_info['mobile']);
                            }
                            if (data.user_info['designations_name'] != null) {
                                $('.designation_name').text(data.user_info['designations_name']);
                            }
                            if (data.user_info['departments_name'] != null) {
                                $('.department_name').text(data.user_info['departments_name']);
                            }
                            if (data.user_info['distributor_point'] != null) {
                                $('.distributor_point').text(data.user_info['distributor_point']);
                            }
                            // if (data.user_info['hr_emp_unit_name'] != null) {
                            //     $('.unit_name').text(data.user_info['hr_emp_unit_name']);
                            // }
                            if (data.user_info['distributor_house'] != null) {
                                $('.distributor_house').text(data.user_info['distributor_house']);
                            }
                            $('.employee_id').val(data.user_info['id']);

                            $('#report_list').show();
                            $('#report_list2').show();
                        }
                    });
                }

            }
        });
        

    </script>
@endsection
