<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Salary Sheet Generate</h4>
</div>
<div class="modal-body">
    <form action="" id="salary_sheet_form" method="post" autocomplete="off">
        <input type="hidden" name="hr_emp_salary_sheet_code" id="hr_emp_salary_sheet_code" value="{{@$sheet_info->hr_emp_salary_sheet_code}}"/>
        <div class="col-md-12">
            <label class="form-label">{{__lang('Distributor Point')}}<span class="required">*</span></label>
            <div class="form-group">
                {!! __combo('bat_distributor_point_multi',array('selected_value'=>explode(',',@$sheet_info->bat_dpid),'attributes'=>array('multiple'=>true,'class'=>'from-control multi','id'=>'bat_point','required'=>'required'))) !!}
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
        <div class="col-md-12">
            <label class="form-label">{{__lang('Salary Month')}}<span class="required">*</span></label>
            <div class="form-group">
                <div class='input-group'>
                    <input type="text" name="salary_month" id="salary_month" class="form-control"
                           data-error="Please Enter Sheet Name" value="{{@$sheet_info->salary_month}}" placeholder="Salary Month" required="">
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
        <div class="col-md-12">
            <label class="form-label">{{__lang('Salary Preparation Date')}}<span class="required">*</span></label>
            <div class="form-group">
                <div class='input-group'>
                    <input type="text" name="preparation_date" id="preparation_date" class="form-control"
                           data-error="Please Enter Salary Preparation Date" value="{{@$sheet_info->preparation_date?$sheet_info->preparation_date:date('Y-m-d')}}" placeholder="Salary Preparation Date" required="">
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label"></label>
            <div class="form-group">
                <div class='input-group'>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>

    </form>
</div>
<script>
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
    $('#bat_point').multiselect('rebuild');
    $('#bat_point').parent().find('.btn-group').css('width','100%');
</script>