@extends('layouts.app')
@section('content')
<style>
    .multiselect, .btn-group{
        width: 100% !important;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox">
                <div class="ibox-title">
                    <h2>Employee Leave Entry</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-5 border-right">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Employee Code<span class="required">*</span></label>
                                        {{__combo('hr_leave_report_employee',array('selected_value'=> @$emp_leave_records->user_code, 'attributes'=> array('class'=>'form-control multi uid','id'=>'user_code','name'=>'user_code')))}}
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <form class="" method="post" id="leave-form">
                                <div class="row user_found">
                                    @if(isset($emp_leave_records))
                                    <input type="hidden" name="hr_leave_records_id" id="hr_leave_records_id" class="hidden" value="{{$emp_leave_records->hr_leave_records_id??''}}"/>
                                    @endif
                                    <input type="hidden" name="user_id" class="employee_id" value=""/>
                                    {{csrf_field()}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Application Type<span class="required">*</span></label>
                                            <select class="form-control" name="application_type" id="application_type" required>
                                                <option value="Pre-Applied">Pre-Applied</option>
                                                <option value="Post-Applied">Post-Applied</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Leave Type<span class="required">*</span></label>
                                            <div class="input-group">
                                                {{__combo('hr_yearly_leave_policy',array('selected_value'=>@$emp_leave_records->leave_types,  'attributes'=> array('class'=>'form-control multi', 'id'=>'leave_type', 'required'=>'true')))}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label">Leave Date</label>
                                            <div class="input-group ">
                                                <input type="text"
                                                       placeholder=""
                                                       class="form-control leave_date_picker"
                                                       id="leave_date"
                                                       name="leave_date"
                                                       value="{{isset($emp_leave_records) ? $emp_leave_records->start_date .' - '.$emp_leave_records->to_date : ''}}"
                                                       required/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Balance Leave</label>
                                            <div class="input-group ">
                                                <input type="text"
                                                       placeholder=""
                                                       class="form-control" readonly
                                                       id="balance_leave"
                                                       name="balance_leave"
                                                       value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label">Application Date</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       placeholder=""
                                                       class="form-control datepicker"
                                                       value="{{isset($emp_leave_records) ? $emp_leave_records->applied_date : date('Y-m-d')}}"
                                                       id=""
                                                       name="application_date" required readonly/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Leave Days</label>
                                            <div class="input-group ">
                                                <input type="text"
                                                       placeholder=""
                                                       class="form-control" readonly
                                                       id="leave_days"
                                                       name="leave_days"
                                                       value="{{isset($emp_leave_records) ? $emp_leave_records->leave_days : '1'}}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Remarks</label>
                                            <div class="input-group">
                                                <textarea class="form-control" name="remarks" rows="2">{{isset($emp_leave_records) ? $emp_leave_records->remarks : ''}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 leave_submit no-display">
                                        <div class="form-group">
                                            <button class="btn btn-success" id="leave-submit" type="submit"
                                                    data-style="zoom-out">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="ibox" style="margin-bottom: 0;">
                                <div class="ibox-title">
                                    <h4><i class="fa fa-user"></i> Employee Information</h4>
                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content no-padding">
                                    <br/>
                                    <span class="">Employee Name : &nbsp;<span class="font-bold employee_name">--</span></span><br/>
                                    <span class="">Employee Mobile : &nbsp;<span class="font-bold employee_mobile">--</span></span><br/>
                                    <span class="">Designation : &nbsp;<span class="font-bold designation_name">--</span></span><br/>
                                    <span class="">Distribution House : &nbsp;<span class="font-bold distributor_house">--</span></span><br/>
                                    <span class="">Distributor Point : &nbsp;<span class="font-bold distributor_point">--</span></span><br/>
                                    {{--<span class="">Unit / Section : &nbsp;<span class="font-bold unit_name">--</span> / <span class="font-bold section_name">--</span></span>--}}
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                            <div id="leave_summary">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .emp_code_entry .btn-group{
        width: 100%;
    }
</style>
<script>

    $(document).ready(function () {
        daterangepicker_config();
        if ($('#user_code').val().length != 0) {
            employeeLeaveInfo($('#user_code').val());
            $('#user_code').multiselect('disable');
            $('.multiselect-selected-text').css('color', 'black');
        }
        var total_leave = 0;
        $('.total_leave').each(function () {
            total_leave += parseInt($(this).text());
        });
        $('#total_leave').text(total_leave);
        //shibly:,.uid

        $('#leave_type').change(function () {
            var uid = $('.uid').val();
            var leave_type = $('#leave_type').val();
            ajax_call(uid, leave_type);
        });
    });

    function daterangepicker_config(){
           var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

           var current_date = yyyy+'-'+mm+'-'+dd;
        $('.leave_date_picker').daterangepicker({
                minDate:current_date,
                autoApply:true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
        }
        );
    }
    $(document).on('change','#application_type',function(){
       var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        current_date = yyyy+'-'+mm+'-'+dd;

        var application_type = $('#application_type').val();
        if(application_type == 'Pre-Applied'){
            $('.leave_date_picker').daterangepicker({
                    minDate:current_date,
                    autoApply:true,
                    locale: {
                        format: 'YYYY-MM-DD'
                    },

                }
            );
        }else{
            $('.leave_date_picker').daterangepicker({
                    maxDate:current_date,
                    autoApply:true,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                }
            );
        }
    });
    function ajax_call(uid = null, leave_type = null) {
        //console.log(uid,leave_type);
        $.ajax({
            type: 'post',
            url: '<?php echo URL::to('get-emp-leave-total'); ?>',
            async: false,
            data: {uid: uid, leave_type: leave_type},
            success: function (response) {
                $('#balance_leave').val(response);
            }
        });
    }

    $('#leave-form').validator().on('submit', function (e) {
        e.preventDefault();

        var user_code = $('#user_code').val();
        var leave_id = $('#hr_leave_records_id').val();

        // alert(user_code+'--'+leave_id);
        var check_data = {user_code:user_code, leave_id:leave_id};
        var check_url = '{{route('check-pending-leave-exist')}}';
        makeAjaxPost(check_data, check_url, null).then(function (resp) {
            if (resp.pending =="no") {
                var leave_balance = $("#balance_leave").val();
                var leave_days = $("#leave_days").val();
                if (Number(leave_days) <= Number(leave_balance)) {
                    var formdata = $('#leave-form').serialize();
                    var url = '<?php echo URL::to('save-leave-info'); ?>';
                    Ladda.bind($('#leave-submit'));
                    var load = $('#leave-submit').ladda();
                    makeAjaxPost(formdata, url, load).done(function (data) {
                        swalSuccess();
                        var url = '<?php echo URL::to('get-emp-leave-history'); ?>';
                        window.location.replace(url);
                    });
                } else {
                    swalError("You can not select days more than balance leaves.");
                }
            }else{
                swalError('Leave request already exist for this employee, <br> please do the action first for this leave.');
            }
        });
    });


    $('#user_code').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 350,
        onChange: function (option, checked, select) {
            var user_code = $('#user_code').val();
            if (user_code.length === 0) {
                swalError("Please Input a valid Employee Code");
                $('.leave_submit').fadeOut();
                $('#show_leave_history').fadeOut();
            } else {
                employeeLeaveInfo(user_code);
            }
        }
    });


    function employeeLeaveInfo(user_code) {
        var url1 = '<?php echo URL::to('get-emp-info'); ?>/' + user_code;
        var basic_info_ajax = makeAjax(url1, null).then(function (data) {
            if (data.success == 0) {
                swalError("No Employee Found for this code.");
                $('.emp_code_entry').addClass('has-error');
                $('.leave_submit').fadeOut();
            } else {
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
                if (data.user_info['distributor_house'] != null) {
                    $('.distributor_house').text(data.user_info['distributor_house']);
                }
                if (data.user_info['distributor_point'] != null) {
                    $('.distributor_point').text(data.user_info['distributor_point']);
                }
                if (data.user_info['hr_emp_unit_name'] != null) {
                    $('.unit_name').text(data.user_info['hr_emp_unit_name']);
                }
                if (data.user_info['hr_emp_section_name'] != null) {
                    $('.section_name').text(data.user_info['hr_emp_section_name']);
                }
                $('.employee_id').val(data.user_info['id']);

                var url3 = '<?php echo URL::to('get-emp-leave-type'); ?>/' + data.user_info['bat_company_id'];

                $.ajax({
                    url: url3,
                    async: false,
                    success: function (response) {
                        $('#leave_type').html(response);
                        $('#leave_type').multiselect('rebuild');

                        var leave_type = $('#leave_type').val();
                        var uid = $('#user_code').val();
                        ajax_call(uid, leave_type);

                    }
                });

                var url2 = '<?php echo URL::to('get-emp-leave-info'); ?>/' + data.user_info['id'];
                $.ajax({
                    url: url2,
                    async: false,
                    success: function (response) {
                        $('#leave_summary').html(response);
                    }
                });
            }
        });
        $('.leave_submit').fadeIn();
    }




    $('#leave_date').on('apply.daterangepicker', function (ev, picker) {
        var start = picker.startDate.format('YYYY-MM-DD');
        var end = picker.endDate.format('YYYY-MM-DD');
        var diff = Math.floor((Date.parse(end) - Date.parse(start)) / 86400000) + 1;
        $('#leave_days').val(diff);
        $('#leave_date').parent().parent().removeClass('has-error');
    });

</script>
@endsection
