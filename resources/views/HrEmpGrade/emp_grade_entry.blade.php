@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    {{csrf_field()}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Employee grade Entry</h2>
                    </div>
                    <div class="ibox-content">
                        <div class="col-md-12">
                            @if(isset($empgrade->hr_emp_grades_id) && !empty($empgrade->hr_emp_grades_id))
                                <form action="{{route('hr-emp-grade-store', $empgrade->hr_emp_grades_id)}}" method="post" id="hr_emp_grade" class="form master-form validator"  data-toggle="validator">
                            @else
                                <form action="{{route('hr-emp-grade-store')}}" method="post" id="hr_emp_grade" class="form master-form validator"  data-toggle="validator">
                            @endif

                                @csrf
                                <div class="col-md-12">
                                    <form action="{{url('hr-salary-config-save')}}" method="post" id="hr_emp_grade" class="form master-form validator"  data-toggle="validator">
                                        <div class=" col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Salary Grade</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" name="hr_emp_grade_name" id="hr_emp_grade_name" value="{{!empty($empgrade->hr_emp_grade_name)?$empgrade->hr_emp_grade_name:''}}" class="hr_emp_grade_name form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Gross</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="min_gross" id="min_gross" value="{{!empty($empgrade->min_gross)?$empgrade->min_gross:'0'}}" class="form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Basic Salary</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="basic_salary" readonly="" id="basic_salary" value="{{!empty($empgrade->basic_salary)?$empgrade->basic_salary:'0'}}" class="basic_salary form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>House Rent</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> % </span>
                                                            <input type="text" name="house_rent" id="house_rent" value="{{!empty($empgrade->house_rent)?$empgrade->house_rent:'50.00'}}" class="house_rent sub_salary form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>House Rent Amount</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="house_rent_amount"   readonly=""  id="house_rent_amount" value="{{!empty($empgrade->house_rent_amount)?$empgrade->house_rent_amount:'500.00'}}" required class="house_rent_amount sub_salary form-control">
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Medical</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="min_medical" id="min_medical" value="{{!empty($empgrade->min_medical)?$empgrade->min_medical:'500.00'}}" class="sub_salary form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>TA/DA</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="min_tada" id="min_tada" value="{{!empty($empgrade->min_tada)?$empgrade->min_tada:'1000.00'}}" class="sub_salary form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Food</strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="min_food" id="min_food" value="{{!empty($empgrade->min_food)?$empgrade->min_food:'1000.00'}}" class="sub_salary form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong> Yearly Increment </strong> <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">% </span>
                                                            <input type="text" name="yearly_increment" id="yearly_increment" value="{{!empty($empgrade->yearly_increment)?$empgrade->yearly_increment:'0'}}" class="form-control" required>
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" id="ot_applicable" name="ot_applicable" value="1" @if(!empty($empgrade->ot_applicable)) checked="" @endif class="custom-check">
                                                        <label class="form-label" for="ot_applicable">Ot Applicable</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" id="pf_applicable" name="pf_applicable" value="1" @if(!empty($empgrade->pf_applicable)) checked="" @endif  class="custom-check">
                                                        <label class="form-label" for="pf_applicable">Pf Applicable</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" id="insurance_applicable" name="insurance_applicable" value="1" @if(!empty($empgrade->insurance_applicable)) checked="" @endif  class="custom-check">
                                                        <label class="form-label" for="insurance_applicable">Insurance Applicable</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Attendance Bonus</strong></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                            <input type="text" name="attendance_bonus" id="attendance_bonus" value="{{!empty($empgrade->attendance_bonus)?$empgrade->attendance_bonus:''}}" class="sub_salary form-control">
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong> Description </strong></label>
                                                        <textarea name="description" rows="1" class="form-control form-control">{{!empty($empgrade->description)?$empgrade->description:''}}</textarea>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong> Status </strong> <span class="required">*</span></label>
                                                        <select class="form-control form-control" name="status">
                                                            <option value="Active" @if(!empty($empgrade->status) && $empgrade->status =='Active') selected="" @endif >Active</option>
                                                            <option value="Inactive" @if(!empty($empgrade->status) && $empgrade->status =='Inactive') selected="" @endif >Inactive</option>
                                                        </select>
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

                                    </form>
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
            $("form").on("input", ".basic_salary, .house_rent, .house_rent_amount", function () {
                var basic_salary = parseFloat($("#basic_salary").val())||0;
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



        (function ($) {
            $("form").on("input", "#min_gross", function () {
                var salary_grade = $("#salary_grade").val();

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

            });
        })(jQuery);


    </script>
@endsection
