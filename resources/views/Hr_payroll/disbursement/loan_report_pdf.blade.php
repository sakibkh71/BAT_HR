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
                    <td class="text-left"><strong>{{ $report_data->user_code ?? 'N/A'}}</strong></td>
                </tr>
                <tr>
                    <td> {{__lang('Employee Name')}}</td>
                    <td>:</td>
                    <td><b>{{ $report_data->name ?? 'N/A'}}</b></td>
                </tr>
                <tr>
                    <td>{{__lang('Distributor Point')}}</td>
                    <td>:</td>
                    <td><strong>{{ $report_data->point_name ?? 'N/A'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Designation')}}</td>
                    <td>:</td>
                    <td><strong>{{ $report_data->designations_name ?? 'N/A'}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Grade')}}</td>
                    <td>:</td>
                    <td><strong>{{ $report_data->hr_emp_grade_name ?? 'N/A'}}</strong></td>
                </tr>

                <tr>
                    <td>{{__lang('Date of Join')}}</td>
                    <td>:</td>
                    <td><strong>{{ !empty( $report_data->date_of_join)? toDated( $report_data->date_of_join):'N/A'}}</strong></td>
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
                    <td class="text-right"><strong>{{ !empty( $report_data->basic_salary)?number_format( $report_data->basic_salary, 2): '0'}}</strong></td>
                </tr>
                @php($total_addition = $report_data->basic_salary)

                @if(!empty($salary_components))
                    @foreach($salary_components as $component)
                        @if($component->auto_applicable == 'YES')
                            @php($total_addition += $component->addition_amount)
                            <tr>
                                <td>{{$component->component_name}}</td>
                                <td class="text-right"><strong>{{ !empty($component->addition_amount)?number_format($component->addition_amount, 2): '0' }}</strong></td>
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
<table class="table-bordered" style="margin-top: 20px; margin-bottom:60px;">
    <thead>
        <tr>
            <td style="padding: 5px; text-align: left"><strong>{{__lang('Loan Summary')}}</strong></td>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td valign="top"  style="border-left: none">
            <table class="table-bordered" width="100%">
                <tr>
                    <td>{{__lang('Loan Date')}}</td>
                    <td width="20%" align="right" class="border"><strong>{{toDated($report_data->loan_date)}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Loan Type')}}</td>
                    <td align="right" class="border"><strong>{{$report_data->loan_type}}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Loan Amount')}}</td>
                    <td align="right" class="border"><strong>{{ $report_data->loan_amount }}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Paid Amount')}}</td>
                    <td align="right" class="border"><strong>{{ $report_data->paid_amount }}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Loan Duration')}}</td>
                    <td align="right" class="border"><strong>{{ $report_data->loan_duration }} Month</strong></td>
                </tr>
                @if($report_data->loan_type !='Advance Salary'))
                <tr>
                    <td>{{__lang('Monthly Payment')}}</td>
                    <td align="right" class="border"><strong>{{ $report_data->monthly_payment }}</strong></td>
                </tr>
                @endif
                 <tr>
                    <td>{{__lang('Due Amount')}}</td>
                    <td align="right" class="border"><strong>{{ $report_data->due_amount }}</strong></td>
                </tr>
                <tr>
                    <td>{{__lang('Note')}}</td>
                    <td align="right" class="border"><strong>{{ $report_data->note }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2">{{__lang('In Word')}} : {{number_to_words($report_data->loan_amount)}}</td>
                </tr>

               {{--
                <tr>
                    <td class="text-right" style="padding-right: 10px">Total</td>

                    <td align="right" class="border"><strong> </strong></td>
                </tr>--}}
            </table>
        </td>
    </tr>
    </tbody>
</table>
@include('HR.hr_default_signing_block')