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
                                            {{__combo('bat_company', array( 'selected_value'=> !empty($company_organogram_details->bat_company_id)?$company_organogram_details->bat_company_id:old('bat_company_id'),'attributes'=> array( 'name'=>'bat_company_id', 'placeholder'=>'Select Company', 'id'=>'bat_company_id', 'class'=>'form-control multi')))}}
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>SM</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="sm" id="sm" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_organogram_details->sm)?$company_organogram_details->sm:old('sm')}}" max="10" required>

                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>SS</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="ss" id="ss" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_organogram_details->ss)?$company_organogram_details->ss:old('ss')}}" required>

                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>SR</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="sr" id="sr" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_organogram_details->sr)?$company_organogram_details->sr:old('sr')}}" required>

                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>ESR</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="esr" id="esr" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_organogram_details->esr)?$company_organogram_details->esr:old('esr')}}" required>

                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>ASR</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="asr" id="asr" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_organogram_details->asr)?$company_organogram_details->asr:old('asr')}}" required>

                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 font-normal"><strong>EASR</strong> </label>
                                        <div class="col-sm-12 input-group">
                                            <input type="number"  name="easr" id="easr" placeholder="0" class="form-control text-left input_money" value="{{!empty($company_organogram_details->easr)?$company_organogram_details->easr:old('easr')}}" required>

                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">

                                        <div class="col-sm-12 input-group">

                                            <button class="btn btn-primary btn-lg" type="button" id="submitCompanyOrganogram"><i class="fa fa-check"></i>&nbsp;Submit</button>

                                        </div>
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
        $(document).on('click','#submitCompanyOrganogram',function () {

            if (!$('#basicForm').validator('validate').has('.has-error').length) {

                var company_id=$("#bat_company_id").val();
                var sm=$("#sm").val();
                var ss=$("#ss").val();
                var sr=$("#sr").val();
                var esr=$("#esr").val();
                var asr=$("#asr").val();
                var easr=$("#easr").val();


                    var data = {
                       'bat_company_id':company_id,
                        'sm':sm,
                        'ss':ss,
                        'sr':sr,
                        'esr':esr,
                        'asr':asr,
                        'easr':easr
                    };
                    var url = '{{url('update-company-organogram')}}';

                    $.ajax({
                        type: 'get',
                        data: data,
                        url: url,
                        success: function (data) {
                            swalSuccess('Company Organogram Updated Successfully');
                            window.location.href = '{{ route('company-organogram-list')}}';
                        },
                        error: function () {
                            swalError("Company Organogram Could not be Updated");
                        }
                    });
                }

        });

    </script>
@endsection