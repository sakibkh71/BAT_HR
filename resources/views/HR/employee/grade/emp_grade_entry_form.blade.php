@extends('layouts.app')
@section('content')
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2> Grade</h2>
                    </div>
                    <div class="ibox-content pad10">
                        <form action="{{route('store-grade-info', isset($emp_grade_info->hr_emp_grades_id)?$emp_grade_info->hr_emp_grades_id:'')}}" method="post" id="basicForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="font-normal"><strong>Grade Name</strong><span class="required">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="hr_emp_grade_name" placeholder="Grade Name English" class="form-control" value="{{ !empty($emp_grade_info->hr_emp_grade_name)?$emp_grade_info->hr_emp_grade_name:old('hr_emp_grade_name')}}" data-error="Grade Name is required" required="">
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal"><strong>Basic Salary</strong> <span class="required">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="basic_salary" id="basic_salary" placeholder="Basic Salary" class="form-control input_money" value="{{ !empty($emp_grade_info->basic_salary)?$emp_grade_info->basic_salary:old('basic_salary')}}"  data-error="Basic Salary  is required" required="">
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal"><strong>Yearly Increment %</strong> <span class="required">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="yearly_increment" placeholder="Yearly Incremrnt " class="form-control input_money" value="{{ !empty($emp_grade_info->yearly_increment)?$emp_grade_info->yearly_increment:old('yearly_increment')}}"  data-error="Yearly Incremrnt  is required" required="">
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group tooltip-demo">
                                        <div class="text-left" style="margin-top: 10px">
                                            <input class="custom-check" name="insurance_applicable" id="insurance_applicable" type="checkbox" tabindex="3" value="1" {{ isset($emp_grade_info->insurance_applicable) && $emp_grade_info->insurance_applicable == 1?'checked':'' }} >
                                            <label for="insurance_applicable">Insurance Applicable <a data-toggle="tooltip" data-placement="top" title="Are you sure to Insurance Applicable?"><i class="fa fa-info-circle"></i></a></label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group tooltip-demo">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="text-left" style="margin-top: 10px">
                                                    <input class="custom-check" type="checkbox"  name="pf_applicable" id="pf_applicable" tabindex="3" value="1" {{ !empty($emp_grade_info->pf_applicable) && $emp_grade_info->pf_applicable == 1?'checked':'' }}>
                                                    <label for="pf_applicable">{{__lang('PF Applicable')}} <a data-toggle="tooltip" data-placement="top" title="Are you sure to Provident Fund Applicable ?"><i class="fa fa-info-circle"></i></a></label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group tooltip-demo">
                                        <div class="text-left" style="margin-top: 10px">
                                            <input class="custom-check" type="checkbox"  name="gf_applicable" id="gf_applicable" tabindex="3" value="1" {{ !empty($emp_grade_info->gf_applicable) && $emp_grade_info->gf_applicable == 1?'checked':'' }}>
                                            <label for="gf_applicable">Gratuity Fund Applicable <a data-toggle="tooltip" data-placement="top" title="Are you sure to Gratuity Fund Applicable?"><i class="fa fa-info-circle"></i></a></label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group tooltip-demo">
                                        <div class="text-left" style="margin-top: 10px">
                                            <input class="custom-check" type="checkbox"  name="late_deduction_applied" id="late_deduction_applied" tabindex="3" value="1" {{ !empty($emp_grade_info->late_deduction_applied) && $emp_grade_info->late_deduction_applied == 1?'checked':''}}>
                                            <label for="late_deduction_applied">Late Deduction Applied <a data-toggle="tooltip" data-placement="top" title="Are you sure to Gratuity Fund Applicable?"><i class="fa fa-info-circle"></i></a></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <div class="input-group">
                                            <textarea class="form-control" name="description" rows="2">{{ !empty($emp_grade_info->description)?$emp_grade_info->description:old('description')}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="hr_emp_grades_id" value="{{(isset($emp_grade_info->hr_emp_grades_id)?$emp_grade_info->hr_emp_grades_id:'')}}">
                                    <button class="btn btn-primary btn-lg" type="submit" id="submitEmpGradeInfo"><i class="fa fa-check"></i>&nbsp;Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            //pf_applicable
            $('#pf_applicable').change(function (e) {
                if($(this).prop('checked')){
                    $('#pf_input').removeClass('no-display');
                    $('#pfr_input').removeClass('no-display');
                } else {
                    $('#pf_input').addClass('no-display');
                    $('#pfr_input').addClass('no-display');
                    $('#pf_amount').val(0);
                    $('#pf_rate').val(0);
                }
            });

            $('#pf_rate').change(function (e) {
                var rate = parseFloat($(this).val());
                var basic = parseFloat($('#basic_salary').val());
                var aval = parseFloat(( basic * rate ) / 100).toFixed(2);
                $('#pf_amount').val(aval);
            });

            $('#pf_amount').change(function (e) {
                var aval = parseFloat($(this).val());
                var basic = parseFloat($('#basic_salary').val());
                var rate = parseFloat((aval*100)/basic ).toFixed(2);
                $('#pf_rate').val(rate);
            });

            $('#basic_salary').change(function (e) {
                var rate = parseFloat($('#pf_rate').val());
                var basic = parseFloat($(this).val());
                var aval = parseFloat(( basic * rate ) / 100).toFixed(2);
                $('#pf_amount').val(aval);
            });


            //insurance_applicable
            $('#insurance_applicable').change(function (e) {
                if($(this).prop('checked')){
                    $('#insurance_input').removeClass('no-display');
                } else {
                    $('#insurance_input').addClass('no-display');
                    $('#insurance_amount').val(0);
                }
            });

            //insurance_applicable
            $('#gf_applicable').change(function (e) {
                if($(this).prop('checked')){
                    $('#gf_input').removeClass('no-display');
                } else {
                    $('#gf_input').addClass('no-display');
                    $('#gf_amount').val(0);
                }
            });


            $("#basicForm").validator();

            // when the form is submitted
            $("#basicForm").on("submit", function(e) {
                if (!e.isDefaultPrevented()) {
                    swalConfirm('Are you sure?').then(function(s){
                        if(s.value){
                            var redirectUrl = '{{route('emp-grade-list')}}';
                            var data = $('#basicForm').serialize();
                            var postUrl = $('#basicForm').attr('action');
                            $.ajax({
                                type: 'POST',
                                url: postUrl,
                                data: data,
                                cache: false,
                                success: function(){
                                    swalRedirect(redirectUrl, 'Grade information added successfully', 'success');
                                },
                                error: function(){
                                    swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                                }
                            });
                        }
                    });
                    return false;
                }
            });
        });

        //Prevent Text on Money Field Salary
        $(document).on('keypress', '.input_money', function(eve) {
            if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
                eve.preventDefault();
            }
        });
    </script>

@endsection