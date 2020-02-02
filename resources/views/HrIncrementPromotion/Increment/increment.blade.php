@extends('layouts.app')
@section('content')
    @include('dropdown_grid.dropdown_grid')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Salary Increment Form</h2>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    @if(!isset($log_id))
                    <div class="ibox-content">
                        <div class="col-md-12">
                            <form method="post" action="" id="increment_form">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Employee </label>
                                        {{__combo('hr_increment_emp_list',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Designation</label>
                                        {{__combo('designation_list',array('selected_value'=>''))}}
                                    </div>
                                    {{--<div class="form-group col-md-3">--}}
                                        {{--<label class="form-label">Salary Grade</label>--}}
                                        {{--{{__combo('hr_emp_grades_list',array('selected_value'=>''))}}--}}
                                    {{--</div>--}}
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Distribution Point</label>
                                        {{__combo('bat_distributor_point_multi',array('selected_value'=>''))}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Increment Eligible Month</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="eligible_month" id="eligible_month" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label"></label>
                                        <div class="input-group">
                                            <button id="btn_add_employee_list" type="button"
                                                    class="btn btn-success btn-lg"><i class="fa fa-search"></i> Filter
                                            </button>
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
                            <form method="post" action="" id="increment_form_submit">
                                @csrf
                                <div class="employee_area no-display">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Increment Ratio(%)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                                <input
                                                        type="number"
                                                        id="cincrement_ratio"
                                                        name="increment_ratio"
                                                        class="form-control text-left" autocomplete="off"
                                                        min="0" step="any"
                                                        data-error="Please enter Increment Ratio (%)">
                                            </div>
                                            <div class="help-block with-errors text-dark has-feedback">If remain blank,
                                                default increment % will appear
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="form-label">Increment based on</label>
                                            <div class="input-group pt-2">
                                                <label for="gross_salary"><input type="radio" checked name="based_on" value="gross" id="gross_salary" class="float-left mt-1 mr-2"> <strong class="float-left"> Gross Salary</strong></label>
                                                <label for="basic_salary"><input type="radio" name="based_on" value="basic" id="basic_salary" class="float-left  mt-1 mr-2 ml-3">  <strong class="float-left">Basic Salary</strong></label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="form-label">Increment Type</label>
                                            <select name="increment_type" id="increment_type" class="form-control multi">
                                                <option value="Yearly">Yearly</option>
                                                <option value="Special">Special</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="form-label">Applicable Date<span
                                                        class="required">*</span></label>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input
                                                        type="text"
                                                        id="applicable_date"
                                                        name="applicable_date" autocomplete="off"
                                                        class="form-control date"
                                                        data-error="Please enter Increment Applicable Date"
                                                        required="required">

                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                        <div class="form-group col-md-3 pt-1 mb-4">
                                            <input type="hidden" name="remove_emp" id="remove_emp" value=""/>
                                            <button id="increment_submit" type="submit" class="btn btn-success btn-lg">
                                                Confirm
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="empIncList">
                                        <tr class="increment_item odd" emp_id="3891" data-log_id="" role="row">
                                            <td class="text-left sorting_1"><input type="hidden" name="emp_id" class="emp_id" value="3891">Md. Kobir Mia</td>
                                            <td class="text-left">20</td>
                                            <td class="text-left">SR</td>
                                            <td class="text-left">19 Oct, 2019</td>
                                            <td class="text-right min_basic" data-amount="33600.00">33,600.00</td>

                                            <td class="text-right min_gross" data-amount="9600.00">9,600.00</td>
                                            <td class="text-right" width="130px"><input type="text" name="increment_ratio" class="form-control increment_ratio text-right" value="" min="1" required=""></td>
                                            <td class="text-right" width="130px"><input type="text" name="increment_amount" class="form-control increment_amount text-right" value="" min="1" required=""> </td>
                                            <td class="text-right" width="130px"><input type="text" name="new_gross_salary" class="form-control new_gross_salary text-right" value="" min="1" required=""></td>
                                            <td class="text-right" width="30px"><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash"></i> </button> </td>
                                        </tr>
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
        var mem = $('.input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        //validator initialize
        $('#increment_form_submit').validator();


        $(document).on('click', '#btn_add_employee_list', function (e) {
            Ladda.bind(this);
            var load = $(this).ladda();
            $('#remove_emp').val('');
            var data = $('#increment_form').serialize() + '&remove_emp=';
            var url = '<?php echo URL::to('hr-get-selected-increment-emp');?>';
            makeAjaxPost(data, url, load).done(function (response) {
                $('#empIncList').html(response.emp_list);
                $('.employee_area').show();
                $("#increment_form_submit").validator('update');
            });
        });


        $(document).on('submit', '#increment_form_submit', function (e) {
            e.preventDefault();
            if (!$('#increment_form_submit').validator('validate').has('.has-error').length) {
                var applicable_date = $('#applicable_date').val();
                var log_id = [];
                var emp_id = [];
                var old_salary = [];
                var increment_ratio = [];
                var increment_amount = [];
                var min_gross = [];
                var new_gross_salary = [];

                var based_on = $("input[name='based_on']:checked").val();
                var increment_type = $("#increment_type").val();


                $('.emp_id').each(function (i, v) {
                    emp_id.push($(this).val());
                });

                $('.increment_ratio').each(function (i, v) {
                    increment_ratio.push($(this).val());
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

                $('.increment_item').each(function (i, v) {
                    if ($(this).data('log_id') !=''){
                        log_id.push($(this).data('log_id'));
                    }
                });

                var emp_count = $('#employee_increment_list >tbody >tr').length;

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
                                increment_ratio: increment_ratio,
                                increment_amount: increment_amount,
                                new_gross_salary: new_gross_salary,
                                min_gross: min_gross,
                                based_on : based_on,
                                increment_type : increment_type,
                            };

                            if (log_id.length > 0) {
                                var url = '<?php echo URL::to('hr-update-selected-emp-increment');?>';
                            } else {
                                var url = '<?php echo URL::to('hr-store-selected-emp-increment');?>';
                            }
                            makeAjaxPost(data, url).done(function (response) {
                                var url = '<?php echo url("hr-increment")?>';
                                swalRedirect(url, "Save Successfully.", 'success');
                            });
                        }
                    });
                }
            }else{
                swalError("Please fill up form properly.");
            }
        });


        $(function ($) {
            $(document).on("input", ".new_gross_salary", function () {
                var ratio_based = $("input[name='based_on']:checked").val();
                var row = $(this).closest("tr");
                var min_gross = parseFloat(row.find(".min_gross").data('amount'));
                var min_basic = parseFloat(row.find(".min_basic").data('amount'));
                var new_gross_salary = parseFloat(row.find(".new_gross_salary").val());
                var increment_amount = new_gross_salary - min_gross;
                if (ratio_based == 'basic') {
                    var increment_ratio = parseFloat((increment_amount * 100) / min_basic);
                }else{
                    var increment_ratio = parseFloat((increment_amount * 100) / min_gross);
                }
                row.find(".increment_ratio").val((increment_ratio).toFixed(2) == 'NaN' ? '0' : (increment_ratio).toFixed(2));
                row.find(".increment_amount").val((increment_amount).toFixed(2) == 'NaN' ? '0' : (increment_amount).toFixed(2));
            });
        });

        $(function ($) {
            $(document).on("input", ".increment_amount", function () {
                var ratio_based = $("input[name='based_on']:checked").val();
                var row = $(this).closest("tr");
                var min_basic = parseFloat(row.find(".min_basic").data('amount'));
                var min_gross = parseFloat(row.find(".min_gross").data('amount'));
                var increment_amount = parseFloat(row.find(".increment_amount").val());
                if (ratio_based == 'basic') {
                    var increment_ratio = parseFloat((increment_amount * 100) / min_basic);
                }else{
                    var increment_ratio = parseFloat((increment_amount * 100) / min_gross);
                }
                var new_gross_salary = min_gross + increment_amount;
                row.find(".increment_ratio").val(increment_ratio.toFixed(2) == 'NaN' ? '0' : increment_ratio.toFixed(2));
                row.find(".new_gross_salary").val(new_gross_salary.toFixed(2) == 'NaN' ? '0' : new_gross_salary.toFixed(2));
            });
        });

        $(function ($) {
            $(document).on("input", ".increment_ratio", function (){
                var ratio_based = $("input[name='based_on']:checked").val();
                var row = $(this).closest("tr");
                var min_basic = parseFloat(row.find(".min_basic").data('amount'));
                var min_gross = parseFloat(row.find(".min_gross").data('amount'));
                var increment_ratio = parseFloat(row.find(".increment_ratio").val());

                if (ratio_based == 'basic') {
                    var increment_amount = parseFloat((min_basic * increment_ratio) / 100);
                }else{
                    var increment_amount = parseFloat((min_gross * increment_ratio) / 100);
                }

                var new_gross_salary = min_gross + increment_amount;
                row.find(".increment_amount").val(increment_amount.toFixed(2) == 'NaN' ? '' : increment_amount.toFixed(2));
                row.find(".new_gross_salary").val(new_gross_salary.toFixed(2) == 'NaN' ? '' : new_gross_salary.toFixed(2));
            });
        });

        //onchange Increment ratio or increment based
        $(function ($) {
            $("body").on("keyup", "#cincrement_ratio", function () {
                ratioChange();
            });

            $(document).on("change", "input[name='based_on']", function () {
                ratioChange();
            });

            function ratioChange() {
                var ratio_based = $("input[name='based_on']:checked").val();

                $('.increment_ratio').each(function () {

                    var row = $(this).closest("tr");

                    var inc_ratio = $('#cincrement_ratio').val() || null;

                    if (inc_ratio == null){ inc_ratio =  $(this).val() || 0; }
                    var min_basic = parseFloat(row.find(".min_basic").data('amount'));
                    var min_gross = parseFloat(row.find(".min_gross").data('amount'));

                    if(ratio_based == 'gross'){
                        var increment_amount = parseFloat((min_gross * inc_ratio) / 100);
                    }else{
                        var increment_amount = parseFloat((min_basic * inc_ratio) / 100);
                    }

                    var new_gross_salary = min_gross + increment_amount;

                    $(this).val(inc_ratio);

                    row.find(".increment_amount").val(increment_amount.toFixed(2) == 'NaN' ? '0' : increment_amount.toFixed(2));

                    row.find(".new_gross_salary").val(new_gross_salary.toFixed(2) == 'NaN' ? '0' : new_gross_salary.toFixed(2));

                });
            }
        });

        //If Edit mode load data
        @if(isset($log_id))
            $(function ($) {
                var log_id = "{{$log_id}}";
                if (log_id) {
                    $('#btn_add_employee_list').hide();
                    var data = {
                        'log_id': log_id,
                        'inc_ratio': '',
                        'applicable_date': ''
                    };
                    var url = '<?php echo URL::to('hr-get-selected-increment-emp-edit');?>';
                    makeAjaxPost(data, url, null).done(function (response) {
                        $('#empIncList').html(response.emp_list);
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

        //Make PDF View
        $(document).on('click', '#increment_list_pdf', function () {
             if ($("tr[emp_id]").length > 0) {
                 var ratio = $('#cincrement_ratio').val();
                    $('#increment_form').append('<input type="hidden" id="ratio_inputid" name="increment_ratio" value="'+ratio+'" /> ');
                    $('#increment_form').attr('action','hr-increment-list-pdf').attr('target', '_blank');
                    $('#increment_form').submit();
                    $('#increment_form').removeAttr('action').removeAttr("target");
                    $('#ratio_inputid').remove();
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