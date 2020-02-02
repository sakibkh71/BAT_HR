<table>
    <tr>
        <td>Date : {{toDated($advice_info->bank_advice_date)}}</td>
        <td style="width: 50%"></td>
        <td>Reference : {{$advice_info->bank_advice_ref}}</td>
    </tr>
    <tr>
        <td colspan="3" style="padding-top: 30px; line-height: 16px;">
            TO <br>
            The Branch Manager <br>
            <strong>{{$advice_info->banks_name}}</strong> <br>
            {{$advice_info->bank_branch_name}} <br>
            {{$advice_info->bank_branch_address}}
        </td>
    </tr>
    <tr>
        <td colspan="3" style="padding-top: 10px;">
           <strong>Subject : Request to salary disbursed of {{ date("F, Y", strtotime($advice_info->bank_advice_date))}} from A/C No. {{$advice_info->bank_ac_no}}</strong>
        </td>
    </tr>

    <tr>
        <td colspan="3" style="padding-top: 20px; text-align: justify">
            Dear Sir, <br>
            {{$advice_info->advice_note}}
        </td>
    </tr>
</table>
@if(!empty($salaries))
<table border="1" style="margin-top: 20px;">
    <thead>
    <tr>
        <td>{{__lang('SL No.')}} </td>
        <td>{{__lang('Name of Employee')}}</td>
        <td>{{__lang('Account No.')}}</td>
        <td>{{__lang('Salary in BDT')}}</td>
    </tr>

    </thead>
    <tbody>
    @php($total = 0)
    @foreach($salaries as $key => $item)
        @php($total +=$item->salary_amount)
        <tr>
            <td align="center">{{++$key}} </td>
            <td>{{$item->name}}</td>
            <td>{{ $item->salary_account_no ?? ''}}</td>
            <td class="text-right">{{!empty($item->salary_amount)?number_format($item->salary_amount, 2):0.00}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-right"> <strong>Total</strong> </td>
            <td class="text-right" colspan="2">{{number_format($total, 2)}}</td>
        </tr>
    </tfoot>
</table>
@endif

<br><br><br>

@include('HR.hr_default_signing_block')
<style>
    table{
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    td{
        padding: 4px;
    }
</style>