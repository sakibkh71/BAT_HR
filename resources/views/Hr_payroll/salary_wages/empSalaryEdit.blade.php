<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Salary Details - ({{$employeeInfo->name}})</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Employee Name:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{$employeeInfo->name}}-({{$employeeInfo->user_code}})</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Employee Code:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{$employeeInfo->user_code}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Distributor Point:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{$employeeInfo->point_name}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>{{__lang('Designation')}}:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{$employeeInfo->designations_name}}</dd>
                        </div>
                    </dl>

                </div>
                <div class="col-lg-6" id="cluster_info">
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Salary Grade:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{$employeeInfo->hr_emp_grade_name}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Gross Salary:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{number_format($employeeInfo->gross,2)}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Earned Salary:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{number_format($employeeInfo->earned_salary,2)}}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Net Salary:</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1 font-bold">{{number_format($employeeInfo->net_payable,2)}}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-12">
            <form method="post" id="empSalaryEditForm">
                <hr>

                <div class="row">
                    {{--<div class="form-group col-md-4">--}}
                        {{--<label class="form-label">Variable Salary(Amount)</label>--}}
                        {{--<input type="number" step="any" name="earn_variable_salary" value="{{$employeeInfo->earn_variable_salary}}" placeholder=""--}}
                               {{--id="earn_variable_salary"--}}
                               {{--class="form-control">--}}
                        {{--<div class="help-block with-errors has-feedback"></div>--}}
                    {{--</div>--}}
                    <div class="form-group col-md-4">
                        <label class="form-label">Arrear(Amount)</label>
                        <input type="number" name="arrear" value="{{$employeeInfo->arrear}}" placeholder=""
                               id="arrear"
                               class="form-control">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Advance Deduction(Amount)</label>
                        <input type="number" name="advance_deduction" value="{{$employeeInfo->advance_deduction}}" placeholder=""
                               id="advance_deduction"
                               class="form-control">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Other Deduction(Amount)</label>
                        <input type="number" name="other_deduction" value="{{$employeeInfo->other_deduction}}" placeholder=""
                               id="other_deduction"
                               class="form-control">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
                <div class="row">


                    <div class="form-group col-md-4">
                        <label class="form-label">Deduction Cause</label>
                        <textarea name="other_deduction_cause" placeholder=""
                                  id="other_deduction_cause"
                                  class="form-control">{{$employeeInfo->other_deduction_cause}}</textarea>
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" id="empSalarySubmit" class="btn btn-primary">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>
<style>
    input[type='number'] {
        -moz-appearance:textfield;
        text-align: left;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        text-align: left;
    }
</style>