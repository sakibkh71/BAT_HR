<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Variable Salary Entry - {{$emp_log->name}}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <br>
            <form class="" method="post" id="variable_salary_form" autocomplete="off">
                <div class="row user_found">
                    @if(isset($emp_vsalary))
                        <input type="hidden" name="vsalary_id" id="vsalary_id" class="" value="{{$emp_vsalary->vsalary_id}}"/>
                    @endif
                    <input type="hidden" name="user_id" class="employee_id" value=""/>
                    {{csrf_field()}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Month <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text"
                                       placeholder=""
                                       class="form-control"
                                       value="{{isset($emp_vsalary) ? $emp_vsalary->vsalary_month : date('Y-m')}}"
                                       id="vsalary_month"
                                       name="vsalary_month" required/>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Variable Salary Amount <span class="required">*</span></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </div>
                                <input type="number"
                                       placeholder=""
                                       class="form-control"
                                       value="{{isset($emp_vsalary) ? $emp_vsalary->variable_salary_amount : ''}}"
                                       id="variable_salary_amount"
                                       name="variable_salary_amount" required/>
                            </div>
                        </div>
                    </div>


                </div>
            </form>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" id="variable_salary_submit" class="btn btn-primary">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>
<script>
    $('#vsalary_month').datetimepicker({
        format: 'YYYY-MM'

    });
</script>