@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">

                <div class="ibox-title">
                    <h2>Employee Leaver Process</h2>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{route('separated-salary-submit')}}" method="post" id="separationForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-sm-12 font-normal"><strong>Leaver Cause</strong><span
                                                class="required">*</span></label>
                                    <div class="col-sm-12">
                                        {{__combo('hr_separation_causes', array('selected_value'=>'', 'attributes'=> array( 'name'=>'hr_separation_causes_id',  'id'=>'hr_separation_causes_id', 'required'=>'true', 'class'=>'form-control')))}}
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Leaver Date</strong></label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" disabled="disabled"
                                              class="form-control"
                                               data-error="Please select Leaver Date"
                                               value="{{ @$separation_date?$separation_date:date('Y-m-d')}}"
                                               placeholder="YYYY-MM-DD" required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <?php
                                $total_salary = 0;
                                ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th colspan="2">Salary Structure</th>
                                    </tr>
                                    <tr>
                                        <th width="50%">Basic Salary</th>
                                        <td class="text-right">{{number_format($salary_info->basic_salary,2)}}</td>
                                    </tr>

                                    @if(!empty($salary_component))
                                        @foreach($salary_component as $component)
                                            @php($slug = $component->component_slug)
                                            @php($total_salary += $salary_info->$slug)
                                            <tr>
                                                <th>{{$component->component_name}}</th>
                                                <td class="text-right">{{number_format($salary_info->$slug,2)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <?php
                                        $total_salary += $salary_info->basic_salary;
                                        $pfp_achivement = $salary_info->target_variable_salary * ($salary_info->pfp_achievement / 100);
                                        $total_salary += $pfp_achivement;
                                    ?>
                                    <tr>
                                        <th width="50%">PfP Salary</th>
                                        <td class="text-right">{{number_format($pfp_achivement,2)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td class="text-right">{{number_format($total_salary,2)}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <?php
                                $total_deduction = 0;
                                ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th colspan="2">Deduction</th>
                                    </tr>
                                    <tr>
                                        <th width="50%">Advance/ Loan</th>
                                        <td class="text-right">{{number_format($salary_info->due_loan_amount,2)}}</td>
                                    </tr>
                                    @php($total_deduction += $salary_info->due_loan_amount)
                                    <tr>
                                        <th>Total</th>
                                        <td class="text-right">{{number_format($total_deduction,2)}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-10">
                                <?php
                                $emp_total = $company_total = 0;
                                ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th colspan="4">Provident Fund</th>
                                    </tr>
                                    <tr>
                                        <th>Month</th>
                                        <th>Employer Contribution</th>
                                        <th>Company Contribution</th>
                                    </tr>
                                    @if($pf_salarys)
                                        @foreach($pf_salarys as $pf_salary)
                                            <?php
                                            $emp_total += $pf_salary->pf_amount_employee;
                                            $company_total += $pf_salary->pf_amount_company;
                                            ?>
                                            <tr>
                                                <th>{{$pf_salary->hr_salary_month_name}}</th>
                                                <td class="text-right">{{$pf_salary->pf_amount_employee}}</td>
                                                <td class="text-right">{{$pf_salary->pf_amount_company}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <th>Sub Total</th>
                                        <td class="text-right">{{number_format($emp_total,2)}}</td>
                                        <td class="text-right">{{number_format($company_total,2)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td colspan="2" class="text-right">{{number_format($emp_total+$company_total,2)}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="hidden">
                            <input type="hidden" name="separation_date" id="separation_date" value="{{@$separation_date}}">
                            <input type="hidden" name="sys_users_id" id="sys_users_id" value="{{@$employee->id}}">
                            <input type="hidden" name="fixed_salary" value="{{@$total_salary-@$pfp_achivement}}">
                            <input type="hidden" name="pfp_salary" value="{{@$pfp_achivement}}">
                            <input type="hidden" name="pf_total_employee" value="{{@$emp_total}}">
                            <input type="hidden" name="pf_total_company" value="{{@$company_total}}">
                            <input type="hidden" name="advance_deduction" value="{{@$salary_info->due_loan_amount}}">
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-sm-12 font-normal"><strong>Other Addition</strong></label>
                                    <div class="col-sm-12">
                                        <input type="number" class="form-control" name="other_addition"/>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-sm-12 font-normal"><strong>Other Deduction</strong></label>
                                    <div class="col-sm-12">
                                        <input type="number" class="form-control" name="other_deduction"/>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-sm-12 font-normal"><strong>Remarks</strong></label>
                                    <div class="col-sm-12">
                                       <textarea class="form-control" name="remarks"></textarea>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                <button class="btn btn-primary btn-lg" type="submit" id="separationProcess"><i
                                            class="fa fa-recycle"></i> Confirm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>

    </script>
@endsection