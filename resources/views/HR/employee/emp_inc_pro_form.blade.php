@php($new_gross = ($emp_log->min_gross+($emp_log->basic_salary*($emp_log->yearly_increment/100))))
@php($inc_amount = $new_gross-$emp_log->min_gross)

<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Increment & Promotion Entry (<u>{{$emp_log->name}}</u>)</h4>
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
                            <dd class="mb-1"><span class="label label-primary">{{$emp_log->status}}</span></dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Salary Grade:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{$emp_log->hr_emp_grade_name}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Gross Salary:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{number_format($emp_log->min_gross,2)}}</dd>
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
                </div>

                <div class="col-lg-6" id="salary_info">
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Basic:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1" id="inc_basic_salary" data-amount="{{$emp_log->basic_salary}}">{{number_format($emp_log->basic_salary,2)}}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label"><strong>Increment/Promotion? : </strong></label>
                        <div class="input-group">
                            <div class="form-check form-check-inline">
                                <input required id="inc_pro_type1" name="inc_pro_type" class="inc_pro_type"
                                       type="radio" value="salary_restructure" checked>
                                <label class="form-check-label" for="inc_pro_type1">Increment</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input required id="inc_pro_type2" name="inc_pro_type" class="inc_pro_type"
                                       type="radio" value="promotion">
                                <label class="form-check-label" for="inc_pro_type2">Promotion</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" id="IncrementBased">
                    <div class="form-group">
                        <label class="form-label"><strong>Increment based on : </strong></label>
                        <div class="input-group">
                            <label for="gross_salary"><input type="radio" name="based_on" value="gross" id="gross_salary" class="float-left mt-1 mr-2">  Gross Salary </label>
                            <label for="basic_salary"><input type="radio" checked="" name="based_on" value="basic" id="basic_salary" class="float-left  mt-1 mr-2 ml-3"> Basic Salary </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <form method="post" action="" autocomplete="off" id="inc_pro_form">
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
                        <div id="increment_item">
                            <div class="form-group row">
                                <label class="col-sm-12 form-label">Increment Ratio<span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <input type="number" step=".01" min="0" name="increment_ratio" id="inc_increment_ratio"
                                           value="{{$emp_log->yearly_increment?$emp_log->yearly_increment:0}}"
                                           class="form-control">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="promotion_item" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Promotion Grade<span class="required">*</span></label>
                            <div class="col-sm-12">
                                {{__combo('salary_grade', array('selected_value' => $emp_log->hr_emp_grades_id,'attributes'=>array('name'=>'hr_emp_grades_id', 'id'=>'hr_emp_grades_id', 'class'=>'form-control multi new_salary_grade')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Promotion Designation<span
                                        class="required">*</span></label>
                            <div class="col-sm-12">
                                {{ __combo('designations', array('selected_value' => $emp_log->designations_id,'attributes'=>array('class'=>'form-control multi new_designation','name'=>'designations_id','required'=>'required')))}}
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Increment Amount<span class="required">*</span></label>
                            <div class="col-sm-12">
                                <input type="number" step=".01" min="0" name="increment_amount"
                                       id="inc_increment_amount" value="{{sprintf('%0.2f',$inc_amount)}}" class="form-control">
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-12 form-label">Gross Salary<span class="required">*</span></label>
                            <div class="col-sm-12">
                                <input type="number" step=".01" min="0" data-amount="{{$emp_log->min_gross??0}}"
                                       value="{{sprintf('%0.2f',$new_gross??0)}}" name="gross_salary" id="new_gross_salary"
                                       class="form-control">
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <div class="modal-footer">
        <button type="submit" id="inc_pro_submit" class="btn btn-primary">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>

<script>
    $(function () {
        datepic();
    });
</script>
