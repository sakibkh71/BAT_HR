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
    <script src="{{asset('public/js/plugins/bootstrap-validator/validator.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Salary Grade Config</h2>
                        <div class="ibox-tools">

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="col-md-12">
                            <form action="{{url('hr-salary-config-save')}}" method="post" id="hr_emp_grade" class="form master-form validator"  data-toggle="validator">

                                <div class="col-md-12 row">
                                    <div class=" col-md-12 row">
                                        <div class="clone_div col-md-12 row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Salary Grade</label>
                                                    {{__combo($slug='salary_grade',$data=array('selected_value'=>'','attribute'=>array('id'=>'salary_grade','required'=>true)))}}

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Basic Salary</label>
                                                    <input type="text" name="basic_salary" id="basic_salary" disabled="" value="" class="basic_salary form-control" required>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">House Rent(%)</label>
                                                    <input type="text" name="house_rent" id="house_rent" value="" class="house_rent sub_salary form-control" required>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">House Rent Amount</label>
                                                    <input type="text" disabled name="house_rent_amount" id="house_rent_amount" value="" required class="house_rent_amount sub_salary form-control">
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"> Medical</label>
                                                    <input type="text" name="min_medical" id="min_medical" value="" class="sub_salary form-control" required>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"> Tada</label>
                                                    <input type="text" name="min_tada" id="min_tada" value="" class="sub_salary form-control" required>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"> Food</label>
                                                    <input type="text" name="min_food" id="min_food" value="" class="sub_salary form-control" required>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"> Gross</label>
                                                    <input type="text" name="min_gross" id="min_gross" value="" class="form-control" required>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button class="submit_button btn btn-primary" data-style="expand-right"  type="submit">Submit Form</button>
                                        </div>
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
    $(document).on('change','#salary_grade',function () {
        var grade_id = $(this).val();
        var url = '<?php echo url('get-hr-grade-wise-salary')?>/'+grade_id;
        
        makeAjax(url).done(function (response) {
            $('#house_rent').val(response.house_rent);
            $('#basic_salary').val(response.basic_salary);
            $('#min_medical').val(response.min_medical);
            $('#min_tada').val(response.min_tada);
            $('#min_food').val(response.min_food);
            $('#min_gross').val(response.min_gross);
            $('#min_gross').data('old_gross',response.min_gross);
            $('#house_rent_amount').val(response.house_rent_amount);
        });
    });

    $(function ($) {
        $("form").on("input", ".sub_salary", function () {
            var min_medical = parseFloat($("#min_medical").val());
            var min_food = parseFloat($("#min_food").val());
            var min_tada = parseFloat($("#min_tada").val());
            var house_rent = parseFloat($("#house_rent").val());
            var old_gross = parseFloat($("#min_gross").data('old_gross'));
            var convince_bill = parseFloat(min_food+min_medical+min_tada);
            var min_gross = $('#min_gross').val();
            // var min_gross = parseFloat(basic_salary+house_rent_amount+min_food+min_medical+min_tada);
            var basic_salary = parseFloat((min_gross - convince_bill)/(1.5));
            var house_rent_amount = parseFloat(basic_salary*parseFloat(house_rent/100));
            $('#basic_salary').val(basic_salary.toFixed(2));
            $('#house_rent_amount').val(house_rent_amount.toFixed(2));
        });
    });

    $(function ($) {
        $("form").on("input", ".basic_salary, .house_rent, .house_rent_amount", function () {
            var basic_salary = parseFloat($("#basic_salary").val());
            if($(this).hasClass('house_rent_amount')){
                var house_rent_amount = parseFloat($("#house_rent_amount").val());
                var house_rent = ((house_rent_amount/basic_salary)*100);
                $("#house_rent").val(house_rent.toFixed(2) == 'NaN' ? '0' : house_rent.toFixed(2));
            }else{
                var house_rent = parseFloat($("#house_rent").val());
                var house_rent_amount = ((house_rent * basic_salary)/100);
                $("#house_rent_amount").val(house_rent_amount.toFixed(2) == 'NaN' ? '0' : house_rent_amount.toFixed(2));
            }
            var basic_salary = parseFloat($("#basic_salary").val());
            var house_rent_amount = parseFloat($("#house_rent_amount").val());
            var min_medical = parseFloat($("#min_medical").val());
            var min_food = parseFloat($("#min_food").val());
            var min_tada = parseFloat($("#min_tada").val());

            var min_gross = parseFloat(basic_salary+house_rent_amount+min_food+min_medical+min_tada);
            $("#min_gross").val(house_rent.toFixed(2) == 'NaN' ? '0' : min_gross.toFixed(2));
        });
    });

    (function ($) {
        $("form").on("input", "#min_gross", function () {
            var salary_grade = $("#salary_grade").val();
            if(salary_grade) {
                var min_medical = parseFloat($("#min_medical").val());
                var min_food = parseFloat($("#min_food").val());
                var min_tada = parseFloat($("#min_tada").val());
                var min_gross = parseFloat($("#min_gross").val());
                var convince = parseFloat(min_medical+min_food+min_tada);
                var basic = parseFloat((min_gross-convince)/(1.5));
                var house_rent = parseFloat($("#house_rent").val());
                var house_rent_amount = parseFloat((house_rent*basic)/100);
                $("#basic_salary").val((basic).toFixed(2) == 'NaN' ? '0' : (basic).toFixed(2));
                $("#house_rent_amount").val((house_rent_amount).toFixed(2) == 'NaN' ? '0' : (house_rent_amount).toFixed(2));
            }else{
                swalWarning('Please select Salary Grade.');
            }
        });
    })(jQuery);


    $(document).on('submit','#hr_emp_grade',function (e) {
        $('form').validator();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        e.preventDefault();
        // Ladda.bind(this);
        var load = '';
        var salary_grade = $("#salary_grade").val();
        if(salary_grade) {
            var gross = parseFloat($('#min_gross').val());
            var grade_text = $('#salary_grade option:selected').text();
            var old_gross = parseFloat($('#min_gross').data('old_gross'));
            var basic_salary = parseFloat($("#basic_salary").val());
            var house_rent = parseFloat($("#house_rent").val());
            var house_rent_amount = parseFloat($("#house_rent_amount").val());
            var min_medical = parseFloat($("#min_medical").val());
            var min_food = parseFloat($("#min_food").val());
            var min_tada = parseFloat($("#min_tada").val());

            if(gross >0) {

                var data = {
                    'salary_grade': salary_grade,
                    'basic_salary': basic_salary,
                    'house_rent': house_rent,
                    'house_rent_amount': house_rent_amount,
                    'min_medical': min_medical,
                    'min_food': min_food,
                    'min_tada': min_tada,
                    'min_gross': gross,
                    'prev_gross_total': old_gross
                };
                var url = '<?php echo url('hr-salary-config-save')?>';

                swalConfirm('Salary Structure will applied for all ' + grade_text + ' Employees').then(function (e) {
                    if (e.value) {
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: data,
                            success: function (response) {
                                if (response.success) {
                                    swalSuccess(response.message);
                                    $('form').trigger('reset');
                                } else {
                                    swalError('Failed to Changed');
                                }
                            }
                        });
                    }
                });
            }else{
                swalWarning('Please Enter Gross total salary.');
            }
        }else{
            swalWarning('Please select Salary Grade');
        }
    });
</script>
@endsection
