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
                        <h2>Employee Promotion Form</h2>
                        <div class="ibox-tools">

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="col-md-12  no-padding">
                            <form method="post" action="" id="increment_form" data-toggle="validator">
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
                            <form method="post" action="" id="increment_form_submit"
                                  data-toggle="validator">
                                <div class="employee_area" style="display: none;">
                                    <hr/>
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
                                                        data-error="Please enter Promotion Applicable Date"
                                                        required="required">
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                        <div class="row col-md-3 mt-4">
                                            <div class="form-group">
                                                <input type="hidden" name="remove_emp" id="remove_emp" value=""/>
                                                <button id="increment_submit" type="submit" class="btn btn-success btn-lg">Confirm
                                                </button>
                                                <button id="promotion_list_pdf" type="button" class="btn btn-primary btn-lg">
                                                    <i class="fa fa-download"></i> Make PDF
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                <div class="table-responsive" id="promowrap">

                                </div>

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
            var data = $('#increment_form').serialize();
            var url= '<?php echo URL::to('hr-get-selected-promotion-emp');?>';
            makeAjaxPost(data,url,load).done(function (response) {
                $('#promowrap').html(response.emp_list);
                $('.employee_area').show();
            });
        });

        $(document).on('click', '#employee_list_selection', function () {
            Ladda.bind(this);
            var load = $(this).ladda();

            var selectedEmployee = getSelectedItems();
            var applicable_date = $('#applicable_date').val();
            $('#btn_add_employee_list').data('selected_value', selectedEmployee);
            if (selectedEmployee.length > 0) {
                var data = {
                    'selected_employee': selectedEmployee,
                    'applicable_date': applicable_date
                };
                var url= '<?php echo URL::to('hr-get-selected-promotion-emp');?>';
                makeAjaxPost(data,url,load).done(function (response) {
                    $('#promowrap').html(response.emp_list);
                });
            } else {
                swalWarning("Select Employee");
            }
        });
        $('#increment_form_submit').validator();
        $(document).on('submit', '#increment_form_submit', function (e) {
            if (!$('#increment_form_submit').validator('validate').has('.has-error').length) {

                e.preventDefault();

                var applicable_date = $('#applicable_date').val();
                var log_id = $('#log_id').val();
                var emp_id = [];
                var new_salary_grade = [];
                var new_designation = [];
                var new_gross_salary = [];
                var new_increment_amount = []

                var err_form = true;

                $('.emp_id').each(function (i,v) {
                    emp_id.push($(this).val());
                });

                $('.new_designation').each(function (i,v) {
                    if ($(this).val() !=''){
                        err_form = false;
                    }
                    new_designation.push($(this).val());
                });

                $('.new_salary_grade').each(function (i,v) {
                    if ($(this).val() !=''){
                        err_form = false;
                    }
                    new_salary_grade.push($(this).val());
                });

                $('.increment_amount').each(function (i,v) {
                    new_increment_amount.push($(this).val());
                });

                $('.new_gross_salary').each(function (i,v) {
                    new_gross_salary.push($(this).val());
                });

                if(err_form == false){
                    swalConfirm().then(function (e) {
                        if(e.value){
                            var data = {
                                applicable_date: applicable_date,
                                emp_id: emp_id,
                                log_id: log_id.split(','),
                                new_designation: new_designation,
                                new_salary_grade: new_salary_grade,
                                new_increment_amount : new_increment_amount,
                                new_gross_salary: new_gross_salary,
                            };
                            if(log_id!=''){
                                var url = '<?php echo URL::to('hr-update-selected-emp-promotion');?>';
                            }else{
                                var url = '<?php echo URL::to('hr-store-selected-emp-promotion');?>';
                            }
                            makeAjaxPost(data,url).done(function (response) {
                                if(response.success == true){
                                    var url = '<?php echo url("hr-promotion")?>';
                                    swalRedirect(url, "Save Successfully.");
                                }else{
                                    swalError();
                                }

                            });
                        }
                    });
                }else{
                    swalError('Sorry!, There is no change for promotion');
                }
            }


        });

        (function ($) {
            $(document).on("input", ".new_gross_salary", function () {

                var row = $(this).closest("tr");
                var old_salary = parseFloat(row.find('.min_basic').data('amount'));
                console.log(old_salary);
                var new_gross_salary = parseFloat(row.find(".new_gross_salary").val());
                if(old_salary > new_gross_salary){
                    row.find('.new_gross_salary').parent().addClass('has-error');
                    row.find('.min_gross').addClass('text-danger');
                }else{
                    row.find('.new_gross_salary').parent().removeClass('has-error');
                    row.find('.min_gross').removeClass('text-danger');
                }
            });
        })(jQuery);
        (function ($) {
            $(document).on("input", ".new_salary_grade", function () {
                var row = $(this).closest("tr");
                 var salary_grade_id = row.find('.new_salary_grade').val();
                var url = "<?php echo url('get-hr-grade-wise-salary')?>/"+salary_grade_id;
                $.ajax({
                    url:url,
                    type:'get',
                    success:function (grade_info) {
                        var new_gross = grade_info.min_gross;
                        var old_gross = row.find(".min_gross").data('amount');
                        row.find(".new_gross_salary").val(grade_info.min_gross);
                        row.find(".increment_amount").val(parseFloat(new_gross - old_gross));
                    }
                });
            });
        })(jQuery);

        $(function ($) {
            var log_id = $('#log_id').val();
            if(log_id){
                $('#btn_add_employee_list').hide();
                var data = {
                    'log_id': log_id,
                    'applicable_date': ''
                };
                var url= '<?php echo URL::to('hr-get-selected-promotion-emp-edit');?>';
                makeAjaxPost(data,url,null).done(function (response) {
                    var row = '<table id="employee_increment_list" class="table table-bordered table-hover"><thead><tr>';
                    row += '<th rowspan="2" class="align-middle">Employee Name</th>';
                    row += '<th rowspan="2" class="align-middle">Code</th>';
                    row += '<th rowspan="2" class="align-middle">Department</th>';
                    row += '<th rowspan="2" class="align-middle">Current Designation</th>';
                    row += '<th rowspan="2" class="align-middle">Current Grade</th>';
                    row += '<th rowspan="2" class="align-middle">Cagetory</th>';
                    row += '<th colspan="6" class="align-middle text-center">Current Salary</th>';
                    row += '<th colspan="4" class="align-middle text-center">Proposed  Promotion Salary & Designation</th></tr>';
                    row += '<tr><th class="align-middle">Basic</th>';
                    row += '<th class="align-middle">House Rent</th>';
                    row += '<th class="align-middle">Medical</th>';
                    row += '<th class="align-middle">Food</th>';
                    row += '<th class="align-middle">TA DA</th>';
                    row += '<th class="align-middle">Gross Total</th>';
                    row += '<th class="change_area2 align-middle text-nowrap">Proposed Designation</th>';
                    row += '<th class="change_area2 align-middle text-center text-nowrap" style="min-width: 200px;">Proposed Grade</th>';
                    row += '<th class="change_area2 align-middle text-nowrap">Proposed <br>Increment Amount</th>';
                    row += '<th class="change_area2 align-middle text-nowrap">Proposed Gross Total</th></tr> </thead><tbody>';
                    row += response.emp_list;
                    row += '</tbody></table>';

                    $('#promowrap').html(row);

                    //$('#employee_increment_list tbody').html(response.emp_list);
                    $('.employee_area').show();
                    $('#increment_form').hide();
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
                swalConfirm('To Remove This').then(function (e) {
                    if(e.value){
                        $row.remove();
                        $('#remove_emp').val(existing_emp.toString());
                    }
                });
            }
        });

        //Make PDF View
        $(document).on('click', '#promotion_list_pdf', function () {
            if ($('#employee_increment_list>tbody >tr').length > 0) {

                var emp_ids = $("#employee_increment_list>tbody >tr").map(function () {
                    return $(this).attr("emp_id");
                }).get().join(", ");

                $('#increment_form').append('<input type="hidden" id="emp_ids" name="emp_ids" value="'+emp_ids+'" /> ');
                $('#increment_form').attr('action','hr-promotion-list-pdf').attr('target', '_blank');
                $('#increment_form').submit();
                $('#increment_form').removeAttr('action').removeAttr("target");
                $('#emp_ids').remove();
            } else {
                swalWarning('Empty Employee Promotion List');
            }
        });
    </script>
@endsection
