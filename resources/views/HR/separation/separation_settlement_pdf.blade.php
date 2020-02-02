<style>
    h4{
        font-size:1em;
    }
    h5{
        font-size:0.8em;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    thead tr td{
        background:#e7e7e7;
        font-size: 0.8em;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:0.8em;
        font-weight: 300;
        padding:0;
    }
    .table-no_border{
        width: 100%;
    }
    .table-no_border td{
        border: none;
        padding:2px;
    }
    td strong{
        font-weight: bold;
    }
    .table-no_border td.border{
        border: 1px solid #ddd;
    }
    .table-bordered td td{
        padding: 2px;
    }
    .table-bordered td{
        padding: 5px;
    }
    .no-padding td{
        padding: 2px;
    }
</style>
<table class="table-no_border" style="margin-top: 50px;">
    <tr>
        <td valign="top">
            <table class="table-no_border">
                <tr>
                    <td width="160px">{{__lang('Employee Id no')}}</td>
                    <td width="5px">:</td>
                    <td class="text-left"><strong>{{ $row->user_code ?? 'N/A'}}</strong></td>
                </tr>
                <tr>
                    <td> {{__lang('Employee Name')}}</td>
                    <td>:</td>
                    <td><b>{{ $row->name ?? 'N/A'}}</b></td>
                </tr>
                <tr>
                    <td>{{__lang('Distributor Point')}}</td>
                    <td>:</td>
                    <td><strong>{{ $row->point_name ?? 'N/A'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Designation')}}</td>
                    <td>:</td>
                    <td><strong>{{ $row->designations_name ?? 'N/A'}}</strong></td>
                </tr>

                <tr>
                    <td>{{__lang('Date of Join')}}</td>
                    <td>:</td>
                    <td><strong>{{ !empty($row->date_of_join)? toDated($row->date_of_join):'N/A'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Date of Confirmation')}}</td>
                    <td>:</td>
                    <td><strong>{{ !empty($row->date_of_confirmation)? toDated($row->date_of_confirmation):'N/A'}}</strong></td>
                </tr>

                <tr>
                    <td>{{__lang('Date Release')}}</td>
                    <td>:</td>
                    <td><strong>{{ !empty($row->separation_date)? toDated($row->separation_date):'N/A'}}</strong></td>
                </tr>
                
                <tr>
                    <td>{{__lang('Release Reason')}}</td>
                    <td>:</td>
                    <td><strong>{{ !empty($row->hr_separation_causes)? $row->hr_separation_causes:'N/A'}}</strong></td>
                </tr>

    </tr>

</table>
</td>
<td width="300px" valign="top">
    <table class="table-bordered no-padding">
        <thead>
            <tr>
                <td colspan="2">{{__lang('Salary Structure')}}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="150">{{__lang('Basic')}}</td>
                <td class="text-right"><strong>{{ !empty($row->basic_salary)?number_format($row->basic_salary, 2): '0'}}</strong></td>
            </tr>
            <?php
            $total_addition = $row->basic_salary;

            ?>
            @if(!empty($salary_component))
            @foreach($salary_component as $component)
            @if($component->component_type == 'Addition')
            <?php

            $total_addition += $component->addition_amount;
            ?>
            <tr>
                <td>{{$component->component_name}}</td>
                <td class="text-right"><strong>{{ !empty($component->addition_amount)?number_format($component->addition_amount, 2): '0'}}</strong></td>
            </tr>
            @endif
            @endforeach

            @endif
            <tr>
                <td>{{__lang('Total')}}</td>
                <td class="text-right"><strong>{{ !empty($total_addition)?number_format($total_addition, 2): '0'}}</strong></td>
            </tr>

        </tbody>
    </table>
</td>

</tr>
</table>
<?php
$total_salary = 0;
$total_deduction = 0;
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <td colspan="2">{{__lang('Earning Summary')}}</td>
        </tr>
    </thead>
    <tr>
        <td width="50%">Basic Salary</td>
        <td class="text-right">{{number_format($salary_info->basic_salary,2)}}</td>
    </tr>

    <?php
    $total_salary += $salary_info->basic_salary;
    $pfp_achivement = $salary_info->target_variable_salary * ($salary_info->pfp_achievement / 100);
    $total_salary += $pfp_achivement + ($row->pf_total_employee + $row->pf_total_company + $row->other_addition + $row->encashment_amount);
    ?>
    @if(!empty($salary_component))
        @foreach($salary_component as $component)
            @if($component->component_type == 'Addition')
                <?php

                $total_salary += $component->addition_amount*$salary_info->days_ratio;
                ?>
                <tr>
                    <td>{{$component->component_name}}</td>
                    <td class="text-right"><strong>{{ !empty($component->addition_amount)?number_format($component->addition_amount*$salary_info->days_ratio, 2): '0'}}</strong></td>
                </tr>
            @endif
        @endforeach

    @endif
    <tr>
        <td width="50%">PFP Salary (Till {{toDated($row->separation_date)}})</td>
        <td class="text-right">{{number_format($pfp_achivement,2)}}</td>
    </tr>
    <tr>
        <td width="50%">Provident Fund</td>
        <td class="text-right">{{number_format($row->pf_total_employee+$row->pf_total_company,2)}}</td>
    </tr>
    <tr>
        <td width="50%">Other Addition</td>
        <td class="text-right">{{number_format($row->other_addition,2)}}</td>
    </tr>
    <tr>
        <td width="50%">Earn Leave Encahsment</td>
        <td class="text-right">{{number_format($row->encashment_amount,2)}}</td>
    </tr>
    <tr>
        <td>Total</td>
        <td class="text-right">{{number_format($total_salary,2)}}</td>
    </tr>
    <thead>
        <tr>
            <td colspan="2">Deduction</td>
        </tr>
    </thead>

    <tr>
        <td width="50%">Advance/ Loan</td>
        <td class="text-right">{{number_format($salary_info->due_loan_amount,2)}}</td>
    </tr>
    <tr>
        <td width="50%">Other Deduction</td>
        <td class="text-right">{{number_format($row->other_deduction,2)}}</td>
    </tr>
    <tr>
        <td width="50%">Absent Deduction</td>
        <td class="text-right">{{number_format($row->absent_deduction,2)}}</td>
    </tr>
    <?php
    $total_deduction += $salary_info->due_loan_amount + $row->other_deduction + $row->absent_deduction;
    $net_payable = $total_salary - $total_deduction;
    ?>
    <tr>
        <td>Total</td>
        <td class="text-right">{{number_format($total_deduction,2)}}</td>
    </tr>
    <tr>
        <td>Net Payable</td>
        <td class="text-right">{{number_format($net_payable,2)}}</td>
    </tr>
    <tr>
        <td colspan="2">
            In Words: {{number_to_words(sprintf('%0.2f',$net_payable))}} Taka Only
        </td>
    </tr>
</table>

<br><br><br>

@include('HR.hr_default_signing_block')
