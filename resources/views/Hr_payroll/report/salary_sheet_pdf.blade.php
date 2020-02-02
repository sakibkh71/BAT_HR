
@if(isset($sheet_code))
<table style="width: 500px;" border="0">
    <tbody  >
    <tr>
        <td>Salary Month: {{$salary_month}}</td>
        <td>Sheet Type: {{$sheet_type}}</td>
    </tr>
    <tr>
        <td>Sheet Code: {{$sheet_code}}</td>
        <td>Selected FF: {{$hr_emp_salary_designations != ''?$hr_emp_salary_designations:'All'}}</td>
    </tr>
    <tr>
        <td>Distributors Point: {{$distributor_points !=''?$distributor_points:'N/A'}}</td>
    </tr>
    </tbody>
</table>

@endif

{{--if need except current month salary sheet print then use this as date('Y-m') at 2018-01--}}
@if(@$salary_month == '2018-01')
    @php($a=0)
    @if(!empty($designation_wise_array))
    @foreach($designation_wise_array as  $k=>$designations)
        <?php
        $net_fixed_salary=0;
        $net_achive=0;
        $net_salary_total=0;
        ?>
        @if($a==0)
            <div >

                @php($a++)
                @else
                    <div style="page-break-before: always">
                        @endif
    <span >FF Type: {{$designation_type_array[$k] !=''?$designation_type_array[$k]:'N/A' }}   &nbsp; &nbsp; &nbsp; &nbsp; NO. of {{$designation_type_array[$k] !=''?$designation_type_array[$k]:'N/A' }}: {{count($designations)}}</span>
        <br><br>
    <table id="employee_list" border="1"
           class="">
        <thead>
        <tr>
            <td width="5%" rowspan="2">SL No.</td>
            <td width="10%" rowspan="2">Distributor Point</td>
            <td width="13%" rowspan="2">Employee Name</td>
            <td width="10%" rowspan="2">Employee Code</td>
            {{--<td class="text-center" colspan="3">Attendance</td>--}}
            <td width="25%" class="text-center" colspan="{{count($salary_component)+2}}">Fixed Salary</td>
            <td width="20%" class="text-center" colspan="3">PFP Salary</td>
            <td width="10%" rowspan="2">Net Salary</td>
            <td width="10%" rowspan="2">Signature</td>
        </tr>
        <tr>
            {{--<td>Present Days</td>--}}
            {{--<td>Leave Days</td>--}}
            {{--<td>Absent Days</td>--}}
            <td>Basic</td>
            @if(!empty($salary_component))
                @foreach($salary_component as $component)
                    <td>{{__lang($component->component_slug)}}</td>
                @endforeach
            @endif
            <td width="10%">Total</td>
            <td>Target</td>
            <td>Achieve</td>
            <td  width="10%">End of Month (Approx.)</td>

        </tr>

        </thead>
        <tbody>
        @if(!empty($designations))
            @foreach($designations as $i=>$emp)
                <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                    <td align="center">
                        {{($i+1)}}
                    </td>

                    <td>{{@$emp->point_name}}</td>
                    <td>{{$emp->name}}</td>
                    <td>{{$emp->user_code}}</td>
                    {{--<td>{{$emp->hr_emp_grade_name}}</td>--}}
                    {{--<td class="text-right">{{number_format($emp->present_days,1)}}</td>--}}
                    {{--<td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>--}}
                    {{--<td class="text-right">{{number_format($emp->absent_days,1)}}</td>--}}
                    <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                    @if(!empty($salary_component))
                        @foreach($salary_component as $component)
                            @php($slug_name = $component->component_slug)
                            <td class="text-right">{{number_format($emp->$slug_name,2)}}</td>
                        @endforeach
                    @endif

                    <td class="text-right">{{number_format($emp->gross,2)}}</td>
                    <td class="text-right">{{number_format($emp->target_variable_salary,2)}}</td>
                    <td class="text-right">{{number_format(($emp->target_variable_salary*$emp->pfp_achievement)/100,2)}}</td>
                    <td class="text-right">{{pfp_today_achievement($emp->target_variable_salary,$emp->pfp_achievement)}}</td>
                    <td class="text-right">{{number_format($emp->gross+pfp_today_achievement($emp->target_variable_salary,$emp->pfp_achievement),2)}}</td>

                  <?php
                    $net_fixed_salary+=$emp->gross;
                    $net_achive+=(($emp->target_variable_salary*$emp->pfp_achievement)/100);
                    $net_salary_total+=($emp->gross+pfp_today_achievement($emp->target_variable_salary,$emp->pfp_achievement));
                    ?>
                    <td></td>
                </tr>
            @endforeach
        @endif
        <tr>
            <?php $colspan=5+count($salary_component)?>
            <td colspan="{{$colspan}}" class="text-center">Total</td>
            <td class="text-right">{{number_format($net_fixed_salary,2)}}</td>
            <td></td>
            <td class="text-right">{{number_format($net_achive,2)}}</td>
            <td></td>
            <td class="text-right">{{number_format($net_salary_total,2)}}</td>
            <td></td>

        </tr>
        </tbody>
    </table>


    </div>


    @endforeach
    @endif
@else

    @php($a=0)
@foreach($designation_wise_array as  $k=>$designations)
    <?php
    $net_fixed_salary=0;
    $net_pf_amount=0;
    $net_salary=0;
    $loan_total=0;
    $salary_deduction = 0;
    ?>
    @if($a==0)
        <div >

            @php($a++)
            @else
                <div style="page-break-before: always">
                    @endif
 <span>FF Type: {{$designation_type_array[$k] !=''?$designation_type_array[$k]:'N/A' }}   &nbsp; &nbsp; &nbsp; &nbsp; NO. of {{$designation_type_array[$k] !=''?$designation_type_array[$k]:'N/A' }}: {{count($designations)}}</span>
<br><br>
<table id="employee_list" border="1">
    <thead>
    <tr>
        <td rowspan="2">SL No.</td>

        <td rowspan="2">Distributor Point</td>
        <td rowspan="2">Employee Name</td>
        <td  rowspan="2">Employee Code</td>
        <td rowspan="2" width="10">Present Days</td>
        <td rowspan="2"  width="10">Leave Days</td>
        <td rowspan="2"  width="10">Absent Days</td>
        {{--<td rowspan="2">FF Name</td>--}}
        {{--<td rowspan="2">Leave Days</td>--}}
        {{--<td rowspan="2">Absent Days</td>--}}
        <td colspan="{{count($salary_component)+2}}" class=" text-center">Fixed Salary</td>
{{--        <td rowspan="2" class="bg_deduction">(-) {{__lang('PF Amount')}}</td>--}}
{{--        <td rowspan="2" class="bg_deduction">(-) {{__lang('Loan Amount')}}</td>--}}
        <td colspan="3" class="bg_deduction text-center">Deduction</td>
        <td rowspan="2">Net Salary</td>
        <td rowspan="2" width="80">Signature</td>
    </tr>
    <tr>
        <td>{{__lang('Basic')}}</td>
        @if(!empty($salary_component))
            @foreach($salary_component as $component)
                <td>{{__lang($component->component_slug)}}</td>
            @endforeach
        @endif
        <td>{{__lang('Total')}}</td>
        <td class="bg_deduction"  width="10">{{__lang('PF Amount')}}</td>
        <td class="bg_deduction"  width="10">Advance Loan</td>
        <td class="bg_deduction"  width="10">Salary Deduction</td>
    </tr>

    </thead>
    <tbody>
    {{--@if(!empty($employeeList))--}}
        {{--@foreach($employeeList as $i=>$emp)--}}

        @if(!empty($designations))
            @foreach($designations as $i=>$emp)

            <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                <td align="center">
                    {{($i+1)}}
                </td>

                <td>{{$emp->point_name}}</td>
                <td>{{$emp->name}}</td>
                <td>{{$emp->user_code}}</td>
                {{--<td>{{$emp->designations_name}}</td>--}}
                {{--<td class="text-right">{{number_format($emp->present_days,1)}}</td>--}}
                {{--<td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>--}}
                {{--<td class="text-right">{{number_format($emp->absent_days,1)}}</td>--}}
                <td class="text-right">{{number_format($emp->present_days,1)}}</td>
                <td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>
                <td class="text-right">{{number_format($emp->absent_days,1)}}</td>
                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>

                @if(!empty($salary_component))
                    @foreach($salary_component as $component)
                        @php($slug_name = $component->component_slug)
                        <td class="text-right">{{number_format($emp->$slug_name,2)}}</td>
                    @endforeach
                @endif
                <td class="text-right">{{number_format($emp->gross,2)}}</td>

                <td class="text-right bg_deduction2">{{number_format($emp->pf_amount_employee,2)}}</td>
                {{--<td class="text-right bg_deduction2">{{number_format($emp->absent_deduction,2)}}</td>--}}
                <td class="text-right bg_deduction2">{{number_format($emp->advance_deduction,2)}}</td>
                <td class="text-right bg_deduction2">{{number_format($emp->other_deduction,2)}}</td>

                {{--<td class="text-right bg_deduction2">{{number_format(($emp->absent_deduction+$emp->advance_deduction+$emp->other_deduction+$emp->card_lost_deduction+$emp->stamp_amount+$emp->pf_amount_employee+$emp->insurance_amount),2)}}</td>--}}
                <td class="text-right">{{number_format($emp->net_payable,2)}}</td>
                <td height="50"></td>
                <?php
                $net_fixed_salary+=$emp->gross;
                $net_pf_amount+=$emp->pf_amount_employee;
                $net_salary+=$emp->net_payable;
                $loan_total+=$emp->advance_deduction;
                $salary_deduction+=$emp->other_deduction;
                ?>
            </tr>


        @endforeach
    @endif

    <tr>
        <?php $colspan=8+count($salary_component)?>
        <td colspan="{{$colspan}}" class="text-center">Total</td>
         <td class="text-right">{{number_format($net_fixed_salary,2)}}</td>
            <td class="text-right">{{number_format($net_pf_amount,2)}}</td>
            <td class="text-right">{{number_format($loan_total,2)}}</td>
            <td class="text-right">{{number_format($salary_deduction,2)}}</td>
            <td class="text-right">{{number_format($net_salary,2)}}</td>
        <td></td>
    </tr>
    </tbody>
</table>


  </div>

@endforeach

@endif
<br><br> <br><br> <br><br>
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
