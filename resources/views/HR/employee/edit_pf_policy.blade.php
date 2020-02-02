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
                        <h2> PF Policy Edit</h2>
                    </div>
                    <div class="ibox-content ">
                        <form action="" method="post" id="basicForm" >
                            @csrf
                            <div class="row">
                               <div class="col-md-3 ">
                                <div class="form-group row">
                                    <label class="col-sm-12 font-normal"><strong>Company</strong> </label>
                                    <div class="col-sm-12">
                                        {{__combo('bat_company', array( 'selected_value'=> !empty($company_pf_policy_details->bat_company_id)?$company_pf_policy_details->bat_company_id:old('designation_name'),'attributes'=> array( 'name'=>'previous_company_name', 'placeholder'=>'Select Company', 'id'=>'company_name', 'class'=>'form-control multi')))}}
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                               </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Ratio Of Basic</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="basic_ratio" id="basic_ratio" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_pf_policy_details->ratio_of_basic)?$company_pf_policy_details->ratio_of_basic:old('basic_ratio')}}" max="10" required>
                                            <span class="input-group-addon"> % </span>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Employee Contribution</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="employee_ratio" id="employee_ratio" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_pf_policy_details->employee_ratio)?$company_pf_policy_details->employee_ratio:old('employee_ratio')}}" required>
                                            <span class="input-group-addon"> % </span>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Company Contribution & others</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="company_ratio" id="company_ratio" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_pf_policy_details->company_ratio)?$company_pf_policy_details->company_ratio:old('basic_ratio')}}" required>
                                            <span class="input-group-addon"> % </span>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Joining Policy</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="joining_policy" id="joining_policy" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_pf_policy_details->joining_policy)?$company_pf_policy_details->joining_policy:old('joining_policy')}}" required>
                                            <span class="input-group-addon"> Month</span>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>Termination Policy</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="termination_policy" id="termination_policy" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_pf_policy_details->termination_policy)?$company_pf_policy_details->termination_policy:old('termination_policy')}}" required>
                                            <span class="input-group-addon"> Month </span>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">

                                        <div class="col-sm-12 input-group">
                                            <input type="hidden" name="company_pf_policy_id" id="company_pf_policy_id" value="{{(isset($company_pf_policy_details->hr_company_pf_policys_id)?$company_pf_policy_details->hr_company_pf_policys_id:'')}}">
                                            <button class="btn btn-primary btn-lg" type="button" id="submitCompanyPFPolicy"><i class="fa fa-check"></i>&nbsp;Submit</button>

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
        $(document).on('click','#submitCompanyPFPolicy',function () {

            if (!$('#basicForm').validator('validate').has('.has-error').length) {
                var company_id = $("#company_name").val();
                var ratio_of_basic = parseFloat($('#basic_ratio').val());
                var employee_ratio = parseFloat($('#employee_ratio').val());
                var company_ratio = parseFloat($("#company_ratio").val());
                var company_pf_policy_id = $("#company_pf_policy_id").val();
                var joining_policy=$("#joining_policy").val();
                var termination_policy=$('#termination_policy').val();
                var pf_ratio = company_ratio + employee_ratio;
                console.log(pf_ratio);
                if (pf_ratio != 100) {
                    swalError("Addition of Employee & Company Ratio must be 100");
                } else {
                    var data = {
                        'company_id': company_id,
                        'ratio_of_basic': ratio_of_basic,
                        'employee_ratio': employee_ratio,
                        'company_ratio': company_ratio,
                        'company_pf_policy_id': company_pf_policy_id,
                        'joining_policy':joining_policy,
                        'termination_policy':termination_policy
                    }
                    var url = '{{url('update-company-pf-policy')}}';

                    $.ajax({
                        type: 'get',
                        data: data,
                        url: url,
                        success: function (data) {
                            if(data.success){
                                swalSuccess('Company PF Policy Updated Successfully');
                                window.location.href = '{{ route('pf-policy-list')}}';
                            }

                        },
                        error: function () {
                            swalError("PF Policy Could not be Updated");
                        }
                    });
                }
            }
        });

    </script>
@endsection