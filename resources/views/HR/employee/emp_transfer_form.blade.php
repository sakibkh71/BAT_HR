<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Transfer Entry (<u>{{$emp_log->name}}</u>)</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">

                <div class="col-lg-6" id="cluster_info">

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Status:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"><?php echo $emp_log->status == 'Active' ? '<span class="label label-primary">Active</span>' : '<span class="label label-warning">Inactive</span>'?></dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Applicable Date:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{toDated($emp_log->applicable_date)}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Branch:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1" id="branchs_id"
                                data-amount="{{$emp_log->branchs_id}}">{{$emp_log->branchs_name}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Department:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1" id="departments_id"
                                data-amount="{{$emp_log->departments_id}}">{{$emp_log->departments_name}}</dd>
                        </div>
                    </dl>
                </div>

                <div class="col-lg-6" id="salary_info">
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Designation:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1" id="designations_id"
                                data-amount="{{$emp_log->designations_id}}">{{$emp_log->designations_name}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Section:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1" id="hr_emp_sections_id"
                                data-amount="{{$emp_log->hr_emp_sections_id}}">{{$emp_log->hr_emp_section_name}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Unit:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1" id="hr_emp_units_id"
                                data-amount="{{$emp_log->hr_emp_units_id}}">{{$emp_log->hr_emp_unit_name}}</dd>
                        </div>
                    </dl>

                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <br>
            <form method="post" action="" autocomplete="off" id="transfer_form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Applicable Date<span class="required">*</span></label>
                            <div class="col-sm-12">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input
                                            type="text"
                                            id="applicable_date"
                                            name="applicable_date"
                                            value="{{ date('Y-m-d') }}"
                                            class="form-control date"
                                            data-error="Please enter Increment Applicable Date"
                                            required="required">
                                </div>
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Branch<span
                                        class="required">*</span></label>
                            <div class="col-sm-12">
                                {{ __combo('branchs', array('selected_value' => $emp_log->branchs_id,'attributes'=>array('class'=>'form-control multi new_branchs','name'=>'branchs_id','required'=>'required')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Section<span
                                        class="required">*</span></label>
                            <div class="col-sm-12">
                                {{ __combo('hr_emp_sections', array('selected_value' => $emp_log->hr_emp_sections_id,'attributes'=>array('class'=>'form-control multi new_hr_emp_sections','name'=>'hr_emp_sections_id','required'=>'required')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Department<span
                                        class="required">*</span></label>
                            <div class="col-sm-12">
                                {{ __combo('departments', array('selected_value' => $emp_log->departments_id,'attributes'=>array('class'=>'form-control multi new_department','name'=>'departments_id','required'=>'required')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Unit<span
                                        class="required">*</span></label>
                            <div class="col-sm-12">
                                {{ __combo('hr_emp_units', array('selected_value' => $emp_log->hr_emp_units_id,'attributes'=>array('class'=>'form-control multi new_hr_emp_units','name'=>'hr_emp_units_id','required'=>'required')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Designation<span
                                        class="required">*</span></label>
                            <div class="col-sm-12">
                                {{ __combo('designations', array('selected_value' => $emp_log->designations_id,'attributes'=>array('class'=>'form-control multi new_designation','name'=>'designations_id','required'=>'required')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <div class="modal-footer">
        <button type="submit" id="transfer_submit" class="btn btn-primary">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>
<script>
    $(function () {
        datepic();
    });
</script>