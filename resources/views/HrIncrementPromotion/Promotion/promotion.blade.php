@extends('layouts.app')
@section('content')
@include('dropdown_grid.dropdown_grid')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2 id="title">@if(isset($log_id)) Edit @else New @endif Employee Promotion</h2>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    @if(!isset($log_id))
                    <div class="ibox-content" id="filter_area">
                        <div class="col-md-12">
                            <form method="post" action="#" id="promotion_form" data-toggle="validator">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Employee </label>
                                        {{__combo('hr_increment_emp_list',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Distribution Point</label>
                                        {{__combo('bat_distributor_point_multi',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Designation</label>
                                        {{__combo('designation_list',array('selected_value'=>''))}}
                                    </div>
                                  {{--  <div class="form-group col-md-3">
                                        <label class="form-label">Salary Grade</label>
                                        {{__combo('hr_emp_grades_list',array('selected_value'=>''))}}
                                    </div>--}}

{{--                                    <div class="form-group col-md-3">--}}
{{--                                        <label class="form-label">Promotion Eligible Month</label>--}}
{{--                                        <div class="input-group">--}}
{{--                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
{{--                                            <input type="text" name="eligible_month" id="eligible_month" class="form-control"/>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="form-group col-md-3">
                                        <div class="input-group  mt-3">
                                            <button id="btn_add_employee_list" type="button" class="btn btn-success btn-lg"><i class="fa fa-search"></i> Filter </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        <div class="col-md-12">
                            <form method="post" action="" id="promotion_form_submit"  data-toggle="validator">
                                @csrf
                                <div class="employee_area" style="display: none;">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Applicable Date<span class="required">*</span></label>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input
                                                    type="text"
                                                    id="applicable_date"
                                                    name="applicable_date" autocomplete="off"
                                                    class="form-control datepicker"
                                                    data-error="Please enter Increment Applicable Date"
                                                    required="required">
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                        <div class="form-group col-md-3 pt-1 mt-3">
                                            <input type="hidden" name="remove_emp" id="remove_emp" value=""/>
                                            <button id="increment_submit" type="submit" class="btn btn-success btn-lg">  Confirm </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="empPromoList">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $('#eligible_month').datepicker({
            autoclose: true,
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        });

        // promotion form validation
        $('#promotion_form_submit').validator();

        //load list
        $(document).on('click', '#btn_add_employee_list', function (e) {
            Ladda.bind(this);
            var load = $(this).ladda();
            $('#remove_emp').val('');
            var data = $('#promotion_form').serialize() + '&remove_emp=';
            var url = '<?php echo URL::to('hr-get-selected-promotion-emp');?>';
            makeAjaxPost(data, url, load).done(function (response) {
                $('#empPromoList').html(response.emp_list);
                $('.employee_area').show();

                $("#promotion_form_submit").validator('update');
            });
        });

        //Get all list
        $(document).on('change', '.new_emp_grades', function () {
            var grade = $(this).val();
            var row = $(this).closest("tr");
            var min_gross = parseFloat(row.find(".min_gross").data('amount'));
            var data = { 'grade_id': grade };
            var url = '<?php echo URL::to('hr-get-gross-by-grade');?>';
            makeAjaxPost(data, url, null).done(function (response) {
                row.find(".new_gross_salary").val(response.gross);
                var increment_amount = parseFloat(response.gross - min_gross);
                row.find(".increment_amount").val((increment_amount).toFixed(2) == 'NaN' ? '0' : (increment_amount).toFixed(2));
            });
        });

        //form submit
        $(document).on('submit', '#promotion_form_submit', function (e) {
            e.preventDefault();
            if (!$('#increment_form_submit').validator('validate').has('.has-error').length) {

                var applicable_date = $('#applicable_date').val();
                var emp_id = [];
                var old_salary = [];
                var increment_amount = [];
                var min_gross = [];
                var new_gross_salary = [];
                var designation_id = [];
             //   var emp_grade_id = [];
                var emp_point_id = [];
                var log_id = [];

                $('.emp_id').each(function (i, v) {
                    emp_id.push($(this).val());
                });

                $('.increment_amount').each(function (i, v) {
                    increment_amount.push($(this).val());
                });

                $('.min_gross').each(function (i, v) {
                    min_gross.push($(this).text());
                });

                $('.new_gross_salary').each(function (i, v) {
                    new_gross_salary.push($(this).val());
                });

                $('.new_designations').each(function (i, v) {
                    designation_id.push($(this).val());
                });

                // $('.new_emp_grades').each(function (i, v) {
                //     emp_grade_id.push($(this).val());
                // });
                $('.new_emp_point').each(function (i,v) {
                    emp_point_id.push($(this).val());
                });


                $('.promotion_item').each(function (i, v) {
                    if ($(this).data('log_id') !=''){
                        log_id.push($(this).data('log_id'));
                    }
                });


            // console.log(min_gross);
                var emp_count = $('#employee_promotion_list >tbody >tr').length;

                if (applicable_date == '') {
                    swalWarning("Please input Increment Applicable Date");
                } else if (emp_count <= 0) {
                    swalWarning("Please Select Employee first");
                } else {
                    swalConfirm("Confirm to save this.").then(function (e) {
                        if (e.value) {
                            var data = {
                                applicable_date: applicable_date,
                                emp_id: emp_id,
                                log_id: log_id,
                                increment_amount: increment_amount,
                                new_gross_salary: new_gross_salary,
                                min_gross: min_gross,
                                designation_id: designation_id,
                                emp_point_id: emp_point_id,
                            };
                            if (log_id != '') {
                                var url = '<?php echo URL::to('hr-update-selected-emp-promotion');?>';
                                makeAjaxPost(data, url).done(function (response) {
                                    var url = '<?php echo url("hr-promotion")?>';
                                    swalRedirect(url, "Save Successfully.", 'success');
                                });
                            } else {
                                var url = '<?php echo URL::to('hr-store-selected-emp-promotion');?>';
                                makeAjaxPost(data, url).done(function (response) {
                                    var url = '<?php echo url("hr-promotion")?>';
                                    swalRedirect(url, "Save Successfully.", 'success');
                                });
                            }
                        }
                    });
                }
            }else{
                swalError("Please fill up form properly.");
            }
        });


        //Change Input new_gross_salary then change value of  increment
        $(function ($) {
            $(document).on("input", ".new_gross_salary", function () {
                var row = $(this).closest("tr");
                var min_gross = parseFloat(row.find(".min_gross").data('amount'));
                var min_basic = parseFloat(row.find(".min_basic").data('amount'));
                var new_gross_salary = parseFloat(row.find(".new_gross_salary").val());
                var increment_amount = new_gross_salary - min_gross;
                row.find(".increment_amount").val((increment_amount).toFixed(2) == 'NaN' ? '0' : (increment_amount).toFixed(2));
            });
        });


        //Change Input increment_amount then change value of gross
        $(function ($) {
            $(document).on("input", ".increment_amount", function () {
                var row = $(this).closest("tr");
                var min_basic = parseFloat(row.find(".min_basic").data('amount'));
                var min_gross = parseFloat(row.find(".min_gross").data('amount'));
                var increment_amount = parseFloat(row.find(".increment_amount").val());
                var new_gross_salary = min_gross + increment_amount;
                row.find(".new_gross_salary").val(new_gross_salary.toFixed(2) == 'NaN' ? '0' : new_gross_salary.toFixed(2));
            });
        });

        @if(isset($log_id))
            $(function ($) {
                var log_id = "{{$log_id}}";
                if (log_id) {
                    $('#btn_add_employee_list').hide();
                    var data = {
                        'log_id': log_id,
                        'applicable_date': ''
                    };
                    var url = '<?php echo URL::to('hr-get-selected-promotion-emp-edit');?>';
                    makeAjaxPost(data, url, null).done(function (response) {
                        $('#empPromoList').html(response.emp_list);
                        $('.employee_area').show();
                        $('#increment_form').hide();
                    });
                }
            });
        @endif

        $(document).on('click', '#increment_list_export', function () {
            var data = $('#increment_form').serialize();
            var url = "{{route('hr-increment-list-export')}}";
            if ($('#employee_increment_list>tbody >tr').length > 0) {
                makeAjaxPost(data, url, null).done(function (response) {
                    window.location.href = './public/export/' + response.file;
                    swalSuccess('Export Successfully');
                });
            } else {
                swalWarning('Empty Employee List');
            }
        });


        $(document).on('click', '.remove_row', function () {
            $row = $(this).closest('tr');
            if ($row) {
                swalConfirm('To Remove This').then(function (e) {
                    if (e.value) {  $row.remove(); }
                });
            }
        });

    </script>
@endsection
