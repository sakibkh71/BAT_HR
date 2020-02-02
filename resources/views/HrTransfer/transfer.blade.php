<?php
/**
 * Created by PhpStorm.
 * User: rashed.islam
 * Date: 1/14/2019
 * Time: 11:12 AM
 */
?>
@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <script src="{{asset('public/js/plugins/bootstrap-validator/validator.min.js')}}"></script>
    @include('dropdown_grid.dropdown_grid')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Employee Transfer Form</h2>
                        <div class="ibox-tools">

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="col-md-12">
                            <form method="post" action="" id="transfer_filter_form" data-toggle="validator">
                                @csrf
                                <input type="hidden" value="{{$log_id}}" name="log_id" id="log_id">
                                <div class="row">

                                    <div class="form-group col-md-3">
                                        <label class="form-label">Employee </label>
                                            {{__combo('hr_increment_emp_list',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Employee Category</label>
                                            {{__combo('hr_increment_emp_categorys',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Salary Grade</label>
                                            {{__combo('hr_emp_grades_list',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Branch</label>
                                            {{__combo('hr_branchs',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Department</label>
                                            {{__combo('hr_emp_departments',array('selected_value'=>''))}}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="form-label"></label>
                                        <div class="input-group">
                                            <button id="btn_add_employee_list" type="button" class="btn btn-success btn-lg">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form method="post" id="transfer_form" style="display: none">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="font-bold">Applicable Date<span class="required">*</span></label>
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input
                                                    type="text"
                                                    id="applicable_date"
                                                    name="applicable_date"
                                                    class="form-control date"
                                                    data-error="Please enter Transfer Applicable Date"
                                                    required="required">

                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                    <div class="row col-md-6 mt-4">
                                        <div class="form-group">
                                            <input type="hidden" name="remove_emp" id="remove_emp" value=""/>
                                            <button id="transfer_submit" type="submit" class="btn btn-success btn-lg hide">Confirm
                                            </button>
                                            <button id="promotion_list_pdf" type="button" class="btn btn-primary btn-lg">
                                                <i class="fa fa-download"></i> Make PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive" id="emptransfer">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .change_area{
            background-color: #8aaf30 !important;
            color: #FFF;
        }
        .change_area2{
            background-color: #c5de89 !important;
        }

    </style>
    <script>

        $('.input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        $(document).on('click', '#btn_add_employee_list', function (e) {
            Ladda.bind(this);
            var load = $(this).ladda();
            var data = $('#transfer_filter_form').serialize();
            var url= '<?php echo URL::to('hr-get-selected-transfer-emp');?>';
            makeAjaxPost(data,url,load).done(function (response) {
                $('#emptransfer').empty();
                $('#emptransfer').html(response.emp_list);
                $('#transfer_form').show();
            });
        });


        $('#transfer_form').validator();
        $(document).on('submit', '#transfer_form', function (e) {
            if (!$('#transfer_form').validator('validate').has('.has-error').length) {
                e.preventDefault();
                var applicable_date = $('#applicable_date').val();
                var log_id = $('#log_id').val();
                var emp_id = [];
                var new_branchs = [];
                var new_departments = [];
                var new_hr_emp_sections = [];
                var new_hr_emp_units = [];
                var new_designation = [];
                $('.emp_id').each(function (i,v) {
                    emp_id.push($(this).val());
                });

                $('.new_branchs').each(function (i,v) {
                    new_branchs.push($(this).val());
                });
                $('.new_departments').each(function (i,v) {
                    new_departments.push($(this).val());
                });
                $('.new_hr_emp_sections').each(function (i,v) {
                     new_hr_emp_sections.push($(this).val());
                });
                 $('.new_hr_emp_units').each(function (i,v) {
                     new_hr_emp_units.push($(this).val());
                });

                $('.new_designation').each(function (i,v) {
                    new_designation.push($(this).val());
                });

                swalConfirm().then(function (e) {
                    if(e.value){
                        var data = {
                            applicable_date: applicable_date,
                            emp_id: emp_id,
                            log_id: log_id.split(','),
                            new_branchs: new_branchs,
                            new_departments: new_departments,
                            new_hr_emp_sections: new_hr_emp_sections,
                            new_hr_emp_units: new_hr_emp_units,
                            new_designation: new_designation
                        };
                        if(log_id!=''){
                            var url = '<?php echo URL::to('hr-update-selected-emp-transfer');?>';
                        }else{
                            var url = '<?php echo URL::to('hr-store-selected-emp-transfer');?>';
                        }
                        makeAjaxPost(data,url).done(function (response) {
                            if(response.success == true){
                                var url = '<?php echo url("hr-transfer")?>';
                                swalRedirect(url, "Save Successfully.");
                            }else{
                                swalError();
                            }
                        });
                    }
                });
            }
        });

        $(function ($) {
            var log_id = $('#log_id').val();
            if(log_id){
                $('#btn_add_employee_list').hide();
                var data = {
                    'log_id': log_id,
                    'applicable_date': ''
                };
                var url= '<?php echo URL::to('hr-get-selected-transfer-emp-edit');?>';
                makeAjaxPost(data,url,null).done(function (response) {
                    $('#employee_transfer_list tbody').html(response.emp_list);
                    $('#transfer_form').show();
                    $('#transfer_filter_form').hide();
                });
            }
        });

        $(document).on('click','.remove_row',function () {
            $row = $(this).closest('tr');
            var emp_id = $row.attr('emp_id');
            var existing_emp_id = $('#remove_emp').val();
            var existing_emp = [];
            existing_emp = existing_emp_id.split(',');
            existing_emp.push(emp_id);
            if(emp_id){
                swalConfirm('to Remove this').then(function (e) {
                    if(e.value){
                        $row.remove();
                        $('#remove_emp').val(existing_emp.toString());
                    }
                });
            }
        });

        //Make PDF View
        $(document).on('click', '#promotion_list_pdf', function () {
            if ($('#employee_transfer_list>tbody >tr').length > 0) {

                var emp_ids = $("#employee_transfer_list>tbody >tr").map(function () {
                    return $(this).data("emp_id");
                }).get().join(", ");

                $('#transfer_filter_form').append('<input type="hidden" id="emp_ids" name="emp_ids" value="'+emp_ids+'" /> ');
                $('#transfer_filter_form').attr('action','hr-transfer-list-pdf').attr('target', '_blank');
                $('#transfer_filter_form').submit();
                $('#transfer_filter_form').removeAttr('action').removeAttr("target");
                $('#emp_ids').remove();

            } else {
                swalWarning('Empty Employee Transfer List');
            }
        });

        //Display Confirm button on change date
        $(document).on('change', '#applicable_date', function(){
            if ($(this).val() !='') {
                $('#transfer_submit').show();
            }else{
                $('#transfer_submit').hide();
                swalError('please select applicable date');
            }

        });


    </script>
@endsection
