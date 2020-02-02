@extends('layouts.app')


@section('content')
    <style>
        #record_table tr td {
            padding: 3px;
        }
        #record_table tr td{
            text-align: right;
        }

        #record_table thead tr td{
            text-align: left;
        }
        #record_table thead tr td, #record_table tfoot tr td {
            font-weight: bold;
        }

    </style>

    <link href="{{asset('public/css/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
   <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-3">
                                <h2>Leave Report</h2>
                            </div>
                            <div class="col-sm-9 form-group row">

                                    <label class="form-label" style="padding-top: 10px; margin-right:10px ">Employee</label>
                                <div style="width: 200px">{{__combo('hr_leave_report_employee',array('selected_value'=>@$bat_users,'attributes'=>array('class'=>'form-control multi','id'=>'hr_leave_report_employee','name'=>'hr_leave_report_employee')))}}</div>
                                <div class="ibox-tools " style="position: relative; margin-left: 50px; margin-top: -10px;" >
                                    <button class="btn btn-success btn-md " id="leave_report"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp; Leave Report </button>
                                    <button class="btn btn-warning btn-md " id="leave_report_pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;PDF</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="ibox-content" id="append_data">

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
        $(document).on('click','#leave_report',function () {
            var user_code=$('#hr_leave_report_employee').val();
            if (user_code.length === 0) {
                swalError("Please Input a valid Employee Code");
            }else {
                var url='<?php echo URL::to('get-emp-leave-report');?>/' + user_code;
                var basic_info_ajax = makeAjax(url,null).then(function (data) {
                    if (data.success == 0) {
                        swalError("No Employee Found for this code.");
                    } else {
                        var html='';
                        // html+='<div class="row">' +
                        //     '<div class="col-md-3">' +
                        //     '<b>Employee Name</b>: ' +data.emp_info["name"]+
                        //     '</div>' +
                        //     '<div class="col-md-2"></div>' +
                        //     '<b>Designation </b>: ' +data.emp_info["designations_name"]+
                        //     '' +
                        //     '</div>';

                        html+='<div class="row">\n' +
                            '    <div class="col-lg-6" id="cluster_info">\n' +
                            '        <dl class="row mb-0">\n' +
                            '            <div class="col-sm-4 text-sm-right">\n' +
                            '                <dt>Employee Name:</dt>\n' +
                            '            </div>\n' +
                            '            <div class="col-sm-8 text-sm-left">\n' +
                            '                <dd class="mb-1">'+data.emp_info["name"]+'</dd>\n' +
                            '            </div>\n' +
                            '        </dl>\n' +
                            '        <dl class="row mb-0">\n' +
                            '            <div class="col-sm-4 text-sm-right">\n' +
                            '                <dt>Employee ID:</dt>\n' +
                            '            </div>\n' +
                            '            <div class="col-sm-8 text-sm-left">\n' +
                            '                <dd class="mb-1">'+data.emp_info["user_code"]+'</dd>\n' +
                            '            </div>\n' +
                            '        </dl>\n' +
                            '        <dl class="row mb-0">\n' +
                            '            <div class="col-sm-4 text-sm-right">\n' +
                            '                <dt>Date of Join:</dt>\n' +
                            '            </div>\n' +
                            '            <div class="col-sm-8 text-sm-left">\n' +
                            '                <dd class="mb-1">'+data.emp_info["date_of_join"]+'</dd>\n' +
                            '            </div>\n' +
                            '        </dl>\n' +
                            '    </div>\n' +
                            '    <div class="col-lg-6" id="cluster_info">\n' +
                            '        <dl class="row mb-0">\n' +
                            '            <div class="col-sm-4 text-sm-right">\n' +
                            '                <dt>Designation:</dt>\n' +
                            '            </div>\n' +
                            '            <div class="col-sm-8 text-sm-left">\n' +
                            '                <dd class="mb-1">'+data.emp_info["designations_name"]+' </dd>\n' +
                            '            </div>\n' +
                            '        </dl>\n' +
                            '        <dl class="row mb-0">\n' +
                            '            <div class="col-sm-4 text-sm-right">\n' +
                            '                <dt>Distributor House:</dt>\n' +
                            '            </div>\n' +
                            '            <div class="col-sm-8 text-sm-left">\n' +
                            '                <dd class="mb-1">'+data.emp_info["distributor_house"]+'</dd>\n' +
                            '            </div>\n' +
                            '        </dl>\n' +
                            '        <dl class="row mb-0">\n' +
                            '            <div class="col-sm-4 text-sm-right">\n' +
                            '                <dt>Distributor Point:</dt>\n' +
                            '            </div>\n' +
                            '            <div class="col-sm-8 text-sm-left">\n' +
                            '                <dd class="mb-1">'+data.emp_info["distributor_point"]+'</dd>\n' +
                            '            </div>\n' +
                            '        </dl>\n' +
                            '        {{--<dl class="row mb-0">--}}\n' +
                            '            {{--<div class="col-sm-4 text-sm-right">--}}\n' +
                            '                {{--<dt>Category:</dt>--}}\n' +
                            '            {{--</div>--}}\n' +
                            '            {{--<div class="col-sm-8 text-sm-left">--}}\n' +
                            '                {{--<dd class="mb-1">{{$emp_log->hr_emp_category_name}}</dd>--}}\n' +
                            '            {{--</div>--}}\n' +
                            '        {{--</dl>--}}\n' +
                            '    </div>\n' +
                            '</div>';

                            html+='<table id="record_table" class="table table-striped text-lefts table-bordered">\n' +
                                '    <thead>\n' +
                                '    <tr>\n' +
                                '        <td>Leave Types</td>\n' +
                                '        <td>Entitle Days</td>\n' +
                                '        <td>Leave Taken</td>\n' +
                                '        <td>Leave Balance</td>\n' +
                                '    </tr>\n' +
                                '    </thead>\n' +
                                '    <tbody>';
                       var total_policy_leave = 0;
                       var total_elapsed = 0;

                       $.each(data.leave_policys,function(i,v){
                           total_policy_leave += v.policy_days;
                           total_elapsed += v.enjoyed_leaves;


                           html+='<tr>';
                           html+=  '<td>'+v["hr_yearly_leave_policys_name"]+'</td>';
                           html+= '                <td class="text-right">'+v.policy_days+'</td>' ;
                           html+= '                <td class="text-right">'+(v.enjoyed_leaves?v.enjoyed_leaves:0)+'</td>' ;
                           html+=  '                <td class="text-right">'+(v.policy_days-v.enjoyed_leaves)+'</td>' ;
                           html+='</tr>';
                           console.log(html);
                       });

                       html+=' </tbody>\n' +
                           '    <tfoot>\n' +
                           '    <tr>\n' +
                           '        <td>Total</td>\n' +
                           '        <td class="text-right">'+parseFloat(total_policy_leave)+'</td>\n' +
                           '        <td class="text-right">'+parseFloat(total_elapsed)+'</td>\n' +
                           '        <td class="text-right">'+(parseFloat(total_policy_leave)-parseFloat(total_elapsed))+'</td>\n' +
                           '    </tr>\n' +
                           '    </tfoot>\n' +
                           '</table>';
                        $("#append_data").html(html);

                        $("#leave_report_pdf").show();
                    }
                });

            }
        });


       $("#leave_report_pdf").hide();
        $(document).on('click','#leave_report_pdf',function(){
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
                    } else {
                        $('.emp_code_entry').removeClass('has-error');
                        $('.employee_id').val(data.user_info['id']);

                        var redirectWindow = window.open('{{url('/')}}'+'/emp_leave_report_print/'+data.user_info['id'], '_blank');
                        redirectWindow.location;


                    }
                });

            }

        });



        

    </script>
@endsection
