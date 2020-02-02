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
                    <td width="150">{{__lang('Target PFP Salary')}}</td>
                    <td class="text-right"><strong>{{ !empty($row->pfp_target_amount)?number_format($row->pfp_target_amount, 2): '0'}}</strong></td>
                </tr>

                <tr>
                    <td width="150">{{__lang('Achieve PFP Ratio')}}</td>
                    <td class="text-right"><strong>{{ !empty($row->pfp_achieve_ratio)?number_format($row->pfp_achieve_ratio, 2).'%': '0'}}</strong></td>
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
        <td width="100%" valign="top"  style="border-left: none">
            <table class="table-bordered">

                <tr>
                    <td>{{__lang('Earn PFP Salary')}}</td>
                    <td align="right" class="border"><strong>{{ !empty($row->pfp_earn_amount)?number_format($row->pfp_earn_amount, 2): '0'}}</strong></td>
                </tr>
                <tr>
                    <td>Total</td>
                    @php($totalEarning =  $row->pfp_earn_amount)
                    <td align="right" class="border"><strong>{{number_format($totalEarning, 2)}}</strong></td>
                </tr>

            </table>
        </td>
    </tr>
        <tr>
            <td colspan="2" style="padding:10px 0; border: none">
                <strong>In Word :</strong>  {{number_to_words($totalEarning)}} {{getOptionValue('default_currency').' only'??'N/A'}}
            </td>
        </tr>
    </tbody>
</table>

<br><br><br>

@include('HR.hr_default_signing_block')
