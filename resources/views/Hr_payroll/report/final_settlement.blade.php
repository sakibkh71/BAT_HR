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
                    <td>{{__lang('Grade')}}</td>
                    <td>:</td>
                    <td><strong>{{ $row->hr_emp_grade_name ?? 'N/A'}}</strong></td>
                </tr>

                <tr>
                    <td>{{__lang('Date of Join')}}</td>
                    <td>:</td>
                    <td><strong>{{ !empty($row->date_of_join)? toDated($row->date_of_join):'N/A'}}</strong></td>
                </tr>

                </tr>

            </table>
        </td>
        <td width="300px" valign="top">
            <table class="table-bordered">
                <thead>
                    <tr>
                        <td colspan="2">{{__lang('Salary Information')}}</td>
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
                    @if(!empty($salary_components_addition))

                        @foreach($salary_components_addition as $component)
                            @if($component['auto_applicable'] == 'YES')
                                <?php
                                    $total_addition += $component['addition_amount'];
                                ?>
                                <tr>
                                    <td>{{$component['component_name']}}</td>
                                    <td class="text-right"><strong>{{ !empty($component['addition_amount'])?number_format($component['addition_amount'], 2): '0'}}</strong></td>
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
<table class="table-bordered" style="margin-top: 20px;">
    <thead>
        <tr>
            <td style="padding: 5px; text-align: left"><strong>{{__lang('Earning')}}</strong></td>
        </tr>
    </thead>
    <tbody>
        <tr>

        <td valign="top"  style="border-left: none">
            <table class="table-bordered" width="100%">
                <tr>
                    <td>{{__lang('Salary')}}</td>
                    <td width="10%" align="right" class="border"><strong>{{ !empty($total_addition)?number_format($total_addition, 2): '0'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('PFP Earn Amount')}}</td>
                    <td align="right" class="border"><strong>{{ !empty($row->pfp_earn_amount)?number_format($row->pfp_earn_amount, 2): '0'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Arrear')}}</td>
                    <td align="right" class="border"><strong>{{ !empty($row->arrear)?number_format($row->arrear, 2): '0'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Attendance Bonus')}}</td>
                    <td align="right" class="border"><strong>{{ !empty($row->attendance_bonus)?number_format($row->attendance_bonus, 2): '0'}}</strong></td>
                </tr>
                <tr>
                    <td class="text-right" style="padding-right: 10px">Total</td>
                    @php($totalEarning = $total_addition + $row->pfp_earn_amount + $row->arrear + $row->attendance_bonus)
                    <td align="right" class="border"><strong>{{number_format($totalEarning, 2)}}</strong></td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table class="table-bordered" style="margin-top: 20px;">
    <thead>
        <tr>
            <td style="padding: 5px; text-align: left"><strong>{{__lang('Deduction')}}</strong></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td valign="top"  style="border-left: none; width: 100%">
                <table width="100%" class="table-bordered">
                    @php($total_deduction = 0)
                    @if(!empty($salary_components_deduction))

                        @foreach($salary_components_deduction as $component)
                            @if($component['auto_applicable'] == 'YES')
                                <?php $total_deduction += $component['deduction_amount']?>
                                <tr>
                                    <td>{{$component['component_name']}}</td>
                                    <td align="right" class="border">{{$component['deduction_amount']}}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                        <tr>
                            <td>PF Deduction</td>
                            <td align="right" class="border">{{number_format($row->advance_deduction,2)}}</td>
                        </tr>
                        <tr>
                            <td>PF Deduction</td>
                            <td align="right" class="border">{{number_format($row->pf_amount_employee,2)}}</td>
                        </tr>
                        <tr>
                            <td>Absent Deduction</td>
                            <td align="right" class="border">{{number_format($row->absent_deduction,2)}}</td>
                        </tr>
                        <tr>
                            <td>Other {{$row->other_deduction_cause?'('.$row->other_deduction_cause.')':''}}</td>
                            <td align="right" class="border">{{number_format($row->other_deduction,2)}}</td>
                        </tr>
                        <?php
                        $total_deduction = $total_deduction + $row->advance_deduction + $row->pf_amount_employee + $row->absent_deduction + $row->other_deduction;
                        ?>
                        <tr>
                            <td>{{__lang('Total')}}</td>
                            <td align="right" class="border"><strong>{{ number_format($total_deduction, 2) }}</strong></td>
                        </tr>
                    <?php $net_salary = $totalEarning - $total_deduction;?>
                    <tr>
                        <td style="text-align: right; padding-right: 10px">{{__lang('Net Amount (in BDT)')}}</td>
                        <td align="right"><strong>{{ number_format($net_salary, 2) }}</strong></td>
                    </tr>
            </table>
            </td>
        </tr>

        <tr>
            <td style="padding:10px 0; border: none">
                <strong>In Word :</strong>  {{number_to_words($net_salary)}} {{getOptionValue('default_currency').' only'??'N/A'}}
            </td>
        </tr>
    </tbody>
</table>
<br><br><br>

@include('HR.hr_default_signing_block')
