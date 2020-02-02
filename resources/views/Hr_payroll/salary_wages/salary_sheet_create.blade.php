@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Generate Salary Sheet</h3>
                    </div>
                    <div class="ibox-content">

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form action="" id="salary_sheet_form" method="post" autocomplete="off">
                                        <input type="hidden" name="hr_emp_salary_sheet_code" id="hr_emp_salary_sheet_code" value="{{@$sheet_info->hr_emp_salary_sheet_code}}"/>

                                        <div class="col-md-6">
                                            <label class="form-label">{{__lang('Salary Month')}}<span class="required">*</span></label>
                                            <div class="form-group">
                                                <div class='input-group'>
                                                    <input type="text" name="salary_month" id="salary_month" class="form-control"
                                                           data-error="Please Enter Sheet Name" value="{{@$sheet_info->salary_month}}" placeholder="Salary Month" required="">
                                                </div>
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{__lang('Salary Preparation Date')}}<span class="required">*</span></label>
                                            <div class="form-group">
                                                <div class='input-group'>
                                                    <input type="text" name="preparation_date" id="preparation_date" class="form-control"
                                                           data-error="Please Enter Salary Preparation Date" value="{{@$sheet_info->preparation_date?$sheet_info->preparation_date:date('Y-m-d')}}" placeholder="Salary Preparation Date" required="" readonly>
                                                </div>
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{__lang('Distributor Point')}}<span class="required">*</span></label>
                                            <div class="form-group">
                                                {!! __combo('bat_distributor_point_multi',array('selected_value'=>explode(',',@$sheet_info->bat_dpid),'attributes'=>array('multiple'=>true,'class'=>'from-control multi','id'=>'bat_point','required'=>'required'))) !!}
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{__lang('Designation')}}</label>
                                            <div class="form-group">
                                                {!! __combo('hr_emp_salary_designations',array('selected_value'=>explode(',',@$sheet_info->selected_designations),'attributes'=>array('multiple'=>true,'name'=>'selected_designations[]','class'=>'from-control multi','id'=>'selected_designations'))) !!}
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Salary Sheet Type <span class="required">*</span></label>
                                            <div class="form-group">
                                                <select class="form-control" name="salary_sheet_type" required id="salary_sheet_type">
                                                    <option {{@$sheet_info->salary_sheet_type=='Fixed'?'selected':''}} value="Fixed">Fixed</option>
                                                    <option {{@$sheet_info->salary_sheet_type=='PFP'?'selected':''}} value="PFP">PFP</option>
                                                </select>
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label bg-danger no-display" id="kpi_note">
                                                Before Salary Sheet Generate Please Confirm KPI Target Achievement sync.
                                            </label>
                                            <div class="form-group">
                                                <div class='input-group'>
                                                    @if(isset($sheet_info))
                                                        <button type="submit" class="btn btn-warning ladda-button">Re-Generate</button>
                                                        @else
                                                        <button type="submit" class="btn btn-success ladda-button">Generate</button>
                                                        @endif

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
        </div>
    </div>
<script>
    $('#salary_sheet_type').on('change',function () {
       if($(this).val() == 'PFP'){
           $('#kpi_note').show();
       }else{
           $('#kpi_note').hide();
       }
    });
    $("#preparation_date").datepicker( {
        format: "yyyy-mm-dd",
        autoclose: true,
    });
    $("#salary_month").datepicker( {
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
    });
    @if(empty(@$sheet_info->selected_designations)||@$sheet_info->selected_designations=='All')
        $("#selected_designations option").attr("selected", "selected");
    @endif
    $(document).on('submit','#salary_sheet_form',function(e){
        e.preventDefault();
        var url = '{{route('hr-salary-sheet-create-save')}}';
        var data = $('#salary_sheet_form').serialize();
        makeAjaxPost(data,url,null).done(function(response){
            if(response.success){
                var url2 = '{{route('hr-salary-sheet')}}';
                swalRedirect(url2,'Successfully Save');

            }
        });
    });
</script>
    @endsection
