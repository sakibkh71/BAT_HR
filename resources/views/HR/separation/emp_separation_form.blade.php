@extends('layouts.app')
@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">

            <div class="ibox-title">
                <h2>Employee Release Process</h2>
                <div class="ibox-tools">
                </div>
            </div>
            <div class="ibox-content">
                <form action="{{route('separated-salary-submit')}}" method="post" id="separationForm">
                    @csrf
                    {!! $emp_info !!}
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>Release Cause</strong><span
                                        class="required">*</span></label>
                                <div class="col-sm-12">
                                    {{__combo('hr_separation_causes', array('selected_value'=>@$separation_info->hr_separation_causes_id, 'attributes'=> array( 'name'=>'hr_separation_causes_id',  'id'=>'hr_separation_causes_id', 'required'=>'true', 'class'=>'form-control')))}}
                                    <div class="help-block with-errors has-feedback"></div>
                                    <div id="other_value" style="display:none;margin-top: 10px;">
                                        <input type="text" name="hr_separation_causes" id="hr_separation_causes" class="form-control" value="{{@$separation_info->hr_separation_causes}}">                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="font-normal"><strong>Release Date</strong></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" disabled="disabled"
                                           class="form-control"
                                           data-error="Please select Release Date"
                                           value="{{ @$separation_date?$separation_date:date('Y-m-d')}}"
                                           placeholder="YYYY-MM-DD" required="" autocomplete="off">
                                </div>
                                <div class="help-block with-errors has-feedback"></div>
                            </div>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            <?php
                            $total_salary = 0;
                            ?>
                            @if(!empty($chk_in_salary_wages))
                                <span class="required">* {{$chk_in_salary_wages}}</span>
                            @endif
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
                                @php($total_salary += $component->addition_amount*$salary_info->days_ratio)
                                <tr>
                                    <th>{{$component->component_name}}</th>
                                    <td class="text-right">{{number_format($component->addition_amount*$salary_info->days_ratio,2)}}</td>
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
                                    <td class="text-right">
                                        <input type="hidden" name="total_salary_structure" id="total_salary_structure" class="form-control" value="{{$total_salary}}">
                                        {{number_format($total_salary,2)}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $emp_total = $company_total = 0;
                            ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="4">Addition</th>
                                </tr>

                                <tr>
                                    <th>Employer Contribution(PF)</th>
                                    <td class="text-right">{{number_format($pf_salary->pf_amount_employee,2)}}
                                        <input type="hidden" name="pf_total_employee" id="employee_contribution" class="form-control" value="{{@$pf_salary->pf_amount_employee}}"  pattern="^[0-9]" title='Only Number' min="1" step="1">
                                    </td>            
                                </tr>
                                <tr>
                                    <th>Company Contribution(PF)</th>
                                    <td class="text-right">
                                        <input type="number" name="pf_total_company" id="company_contribution" class="form-control" value="{{@$separation_info->pf_total_company}}"  pattern="^[0-9]" title='Only Number' min="1" step="1">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Other Addition</th>
                                    <td class="text-right">
                                        <input type="number" class="form-control" name="other_addition" id="other_addition" value="{{@$separation_info->other_addition}}"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td colspan="2" class="text-right" id="addition_total">{{number_format($pf_salary->pf_amount_employee+$pf_salary->pf_amount_company,2)}}</td>
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
                                <input type="hidden" name="loan_deduction" id="loan_deduction" class="form-control" value="{{$salary_info->due_loan_amount}}">
                                </tr>
                                <tr>
                                    <th width="50%">Absent Deduction</th>
                                    <td class="text-right">
                                        <input type="number" name="absent_deduction" id="abesent_deduction" class="form-control" value="{{@$separation_info->absent_deduction}}">
                                    </td>
                                </tr>
                                <tr>
                                    <th width="50%">Other Deduction</th>
                                    <td class="text-right">
                                        <input type="number" class="form-control" name="other_deduction" id="other_deduction" value="{{@$separation_info->other_deduction}}"/>
                                    </td>
                                </tr>

                                @php($total_deduction += $salary_info->due_loan_amount)
                                <tr>
                                    <th>Total</th>
                                    <td class="text-right" id="total_deduction">{{number_format($total_deduction,2)}}</td>
                                </tr>

                            </table>
                        </div>

                        <!-- Shibly Code: -->
                        <div class="col-md-4">
                            <?php
                            $total_deduction = 0;
                            ?>
                            {{--<table class="table table-bordered">--}}
                                {{--<tr>--}}
                                    {{--<th colspan="2">Earned Leave</th>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                    {{--<th width="50%">Balance</th>--}}
                                    {{--<td class="text-right">--}}
                                        {{--<input type="number" name="earn_leave_days" id="leave_balance" class="form-control" value="{{@$separation_info->earn_leave_days}}">--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                    {{--<th width="50%">Per Day Encashment</th>--}}
                                    {{--<td class="text-right">--}}
                                        {{--<input type="number" name="encashment_rate" id="perday_encashment" class="form-control" value="{{@$separation_info->encashment_rate}}" >--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                                {{--@php($total_deduction += $salary_info->due_loan_amount)--}}
                                {{--<tr>--}}
                                    {{--<th>Totals <input type="hidden" name="encashment_amount" id="encashment_amount" class="form-control" value="{{@$separation_info->encashment_amount}}" ></th>--}}
                                    {{--<td class="text-right" id="total_earned_leave">{{number_format($total_deduction,2)}}--}}

                                    {{--</td>--}}

                                {{--</tr>--}}

                            {{--</table>--}}
                        </div>
                        <!-- Shibly Add CoDE End -->
                    </div>
                    <div class="hidden">
                        <input type="hidden" name="hr_emp_separation_id" id="hr_emp_separation_id" value="{{@$separation_info->hr_emp_separation_id}}">
                        <input type="hidden" name="separation_date" id="separation_date" value="{{@$separation_date}}">
                        <input type="hidden" name="sys_users_id" id="sys_users_id" value="{{@$employee->id}}">
                        <input type="hidden" name="fixed_salary" value="{{@$total_salary-@$pfp_achivement}}">
                        <input type="hidden" name="pfp_salary" value="{{@$pfp_achivement}}">
                        <!--<input type="hidden" name="pf_total_employee" value="{{@$emp_total}}">
                        <input type="hidden" name="pf_total_company" value="{{@$company_total}}">-->
                        <input type="hidden" name="advance_deduction" value="{{@$salary_info->due_loan_amount}}">
                    </div>
                    <div class="row">
                        {{--<div class="col-md-5">--}}
                            {{--<div class="form-group row">--}}
                                {{--<label class="col-sm-12 font-normal"><strong>Other Addition</strong></label>--}}
                                {{--<div class="col-sm-12">--}}
                                    {{--<input type="number" class="form-control" name="other_addition" id="other_addition" value="{{@$separation_info->other_addition}}"/>--}}
                                    {{--<div class="help-block with-errors has-feedback"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-5">--}}
                            {{--<div class="form-group row">--}}
                                {{--<label class="col-sm-12 font-normal"><strong>Other Deduction</strong></label>--}}
                                {{--<div class="col-sm-12">--}}
                                    {{--<input type="number" class="form-control" name="other_deduction" id="other_deduction" value="{{@$separation_info->other_deduction}}"/>--}}
                                    {{--<div class="help-block with-errors has-feedback"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>Net Payable</strong></label>
                                <div class="col-sm-12">
                                    <input type="number" name="net_payable" id="net_payable" class="form-control" readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>Remarks</strong></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="remarks">{{@$separation_info->remarks}}</textarea>
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

    $(document).ready(function () {
        calculation();

        $('#total_salary_structure,#abesent_deduction,#company_contribution,#other_addition,#other_deduction,#employee_contribution,#leave_balance,#perday_encashment,#loan_deduction').keyup(function () {
            calculation();
            var employee_contribution = $("#employee_contribution").val();
            var company_contribution = $('#company_contribution').val();
            var other_addition = $('#other_addition').val();
            var other_deduction = $('#other_deduction').val();

            var addition_total = Number(employee_contribution) + Number(company_contribution) + Number(other_addition);
            $('#addition_total').text(addition_total.toFixed(2));

            //Earned Leave
            var leave_balance = $("#leave_balance").val();
            var perday_encashment = $('#perday_encashment').val();

            var earned_leave_total = Number(leave_balance) * Number(perday_encashment);
            $('#total_earned_leave').text(earned_leave_total.toFixed(2));
            $('#encashment_amount').val(earned_leave_total.toFixed(2));

            // Loan Deduction
            var loan_deductions = $("#loan_deduction").val();
            var abesent_deductions = $('#abesent_deduction').val();

            var deduction_total = Number(loan_deductions) + Number(abesent_deductions) + Number(other_deduction);
            $('#total_deduction').text(deduction_total.toFixed(2));

            //==============
            var total_salary_structure = $('#total_salary_structure').val();
            var t_sum = Number(total_salary_structure) + Number(addition_total);

            var d_sum = Number(deduction_total);
            var res = Number(t_sum) - Number(d_sum);

            $('#net_payable').val(res.toFixed(2));
        });

        //hr_separation_causes_id
        var hr_separation_cause = $('#hr_separation_causes_id option:selected').text();
        if (hr_separation_cause == 'Other') {
            $('#other_value').show();
        }

        $('#hr_separation_causes_id').change(function () {
            var cause_val = $('#hr_separation_causes_id option:selected').text();
            var hs_cause='<?php echo isset($separation_info->hr_separation_causes) ? $separation_info->hr_separation_causes:0; ?>';
       //     alert (hs_cause);
            if (cause_val == 'Other' || cause_val == '--Select an option--') {
                $('#other_value').show();
                $("#hr_separation_causes").attr('required', true);
                if (hs_cause == '' || hs_cause==0) {                    
                    $('#hr_separation_causes').val('');
                } else {
                    $('#hr_separation_causes').val(hs_cause);
                }
            } else {
                $('#other_value').hide();
                $('#hr_separation_causes').val(cause_val);

            }
        });




    });

    function calculation() {
        var total_salary_structure = $('#total_salary_structure').val();
        ////
        var abesent_deduction = $('#abesent_deduction').val();
        var loan_deduction = $('#loan_deduction').val();
        var deduction_total = Number(abesent_deduction) + Number(loan_deduction);
        $('#total_deduction').text(deduction_total.toFixed(2));
        ////
        var company_contribution = $('#company_contribution').val();
        var employee_contribution = $('#employee_contribution').val();
        var total_addition = Number(company_contribution) + Number(employee_contribution);

        $('#addition_total').text(total_addition.toFixed(2));
        ////
        var other_addition = $('#other_addition').val();
        var other_deduction = $('#other_deduction').val();
        ////
        var leave_balance = $('#leave_balance').val();
        var perday_encashment = $('#perday_encashment').val();
        var total_total_earned_leave = Number(leave_balance) * Number(perday_encashment);
        $('#total_earned_leave').text(total_total_earned_leave.toFixed(2));
        ////
        // var t_sum = Number(total_salary_structure) + Number(company_contribution) + Number(other_addition);
        var t_sum = Number(total_salary_structure) + Number(total_addition);

        var d_sum = Number(deduction_total);
        var res = Number(t_sum) - Number(d_sum);

        $('#net_payable').val(res.toFixed(2));

    }
</script>
@endsection