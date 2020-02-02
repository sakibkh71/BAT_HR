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
                        <h2>Bonus Policy Form</h2>
                    </div>
                    <div class="ibox-content ">
                        <form action="{{route('store-bonus-policy')}}" method="post" id="basicForm" >
                            @csrf
                            <div class="row">
                                <div class="col-md-3 ">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Select Company</strong><span class="required">*</span> </label>
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                            {{__combo('bat_company', array('selected_value'=> !empty($bonus_policy->bat_company_id)?$bonus_policy->bat_company_id:old('company_id'),'attributes'=> array( 'name'=>'company_id', 'placeholder'=>'Select Company','required'=>'', 'data-error'=>'Please Select a Value', 'id'=>'company_id', 'class'=>'form-control multi')))}}

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Bonus Eligible Based On</strong><span class="required">*</span> </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <select class="form-control" name="bonus_eligible_based_on" id="bonus_eligible_based_on" data-error="Please Select a Value" @if(isset($bonus_policy->bonus_eligible_based_on)) disabled @else required @endif>
                                                <option value="" >Select an Option</option>
                                                <option value="date_of_confirmation" @if(isset($bonus_policy) && $bonus_policy->bonus_eligible_based_on=='date_of_confirmation') selected @endif>date of confirmation</option>
                                                <option value="date_of_join" @if(isset($bonus_policy) && $bonus_policy->bonus_eligible_based_on=='date_of_join') selected  @endif>date of join</option>
                                            </select>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Number Of Month</strong><span class="required">*</span> </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <input type="number"  name="number_of_month" id="number_of_month" placeholder="0" class="form-control text-left input_money"  value="{{isset($bonus_policy->number_of_month)?$bonus_policy->number_of_month:0}}" required>
                                            <span class="input-group-addon"> Month </span>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Bonus Based On</strong> <span class="required">*</span></label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <select class="form-control" name="bonus_based_on" id="bonus_based_on" required>
                                                <option value="" >Select an Option</option>
                                                <option value="basic" @if(isset($bonus_policy) && $bonus_policy->bonus_based_on=='basic') selected @endif>Basic</option>
                                                <option value="gross" @if(isset($bonus_policy) && $bonus_policy->bonus_based_on=='gross') selected @endif>Gross</option>
                                            </select>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Bonus Ratio</strong><span class="required">*</span> </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <input type="number"  name="bonus_ratio" id="bonus_ratio" placeholder="0" class="form-control text-left input_money" value="{{isset($bonus_policy->bonus_ratio)?$bonus_policy->bonus_ratio:0}}" required>
                                            <span class="input-group-addon"> % </span>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Status</strong><span class="required">*</span> </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="" >Select an Option</option>
                                                <option value="Active" @if(isset($bonus_policy) && $bonus_policy->status=='Active') selected @endif>Active</option>
                                                <option value="Inactive" @if(isset($bonus_policy) && $bonus_policy->status=='Inactive') selected @endif>Inactive</option>
                                            </select>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">

                                        <div class="col-sm-12 input-group">
                                            <input type="hidden" name="emp_bonus_id" id="emp_bonus_id" value="{{isset($bonus_policy->hr_emp_bonus_policys_id)?$bonus_policy->hr_emp_bonus_policys_id:''}}" >
                                            <button class="btn btn-primary btn-lg" type="button" id="submitBonusPolicy"><i class="fa fa-check"></i>&nbsp;Submit</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <script>
    $("#submitBonusPolicy").click(function(){
        if (!$('#basicForm').validator('validate').has('.has-error').length) {

            var selected_company = $("#company_id").val();
            var bonus_eligible_based_on = $("#bonus_eligible_based_on").val();
            var emp_bonus_id = $("#emp_bonus_id").val();

            if (emp_bonus_id == '') {

                $.ajax({
                    type: 'get',
                    data: {
                        'company_id': selected_company,
                        'bonus_eligible_based_on': bonus_eligible_based_on
                    },
                    url: '{{url('check-company-for-bonus-policy-eligibility')}}',
                    async: false,
                    success: function (data) {
                        if (data == 0) {
                            swalError('Sorry You Already Have Bonus Policy');
                        } else {
                            $("#basicForm").submit();
                        }
                    }
                });


            } else {

                $("#basicForm").submit();
            }

        }
    });

    </script>
@endsection