@if(isset($sheet_code))
    <table style="width: 500px; font-size: 12px;" border="0">
        <tbody  >
        <tr>
            <td>      Salary Month: {{$salary_month}}</td>
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
@php($a=0)
@foreach($designation_wise_array as  $k=>$designations)
    <?php
    $net_pfp_target_amount=0;
    $net_pfp_earn_amount=0;
    ?>
    @if($a==0)
        <div >

            @php($a++)
            @else
                <div style="page-break-before: always">
                    @endif
                    <span >FF Type: {{$designation_type_array[$k] !=''?$designation_type_array[$k]:'N/A' }}   &nbsp; &nbsp; &nbsp; &nbsp; NO. of {{$designation_type_array[$k] !=''?$designation_type_array[$k]:'N/A' }}: {{count($designations)}}</span>
                    <br><br>

                    <table id="employee_list" border="1">
                        <thead>
                        <tr>
                            <td>SL No.</td>

                            <td>Distributor Point</td>
                            <td>Employee Name</td>
                            <td>Employee Code</td>
                            <td>{{__lang('PFP Target Amount')}}</td>
                            <td>{{__lang('PFP Achieve Ratio')}}</td>
                            <td>{{__lang('PFP Earn Amount')}}</td>
                        </tr>

                        </thead>
                        <tbody>
                        @if(!empty($designations))
                            @foreach($designations as $i=>$emp)
                                <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                                    <td align="center">
                                        {{($i+1)}}
                                    </td>

                                    <td>{{$emp->point_name}}</td>
                                    <td>{{$emp->name}}</td>
                                    <td>{{$emp->user_code}}</td>
                                    <td class="text-right">{{number_format($emp->pfp_target_amount,2)}}</td>
                                    <td class="text-right">{{number_format($emp->pfp_achieve_ratio,2)}}%</td>
                                    <td class="text-right">{{number_format($emp->pfp_earn_amount,2)}}</td>
                                    <?php
                                    $net_pfp_target_amount+=$emp->pfp_target_amount;
                                    $net_pfp_earn_amount+=$emp->pfp_earn_amount;
                                    ?>
                                </tr>
                            @endforeach
                        @endif

                        <tr>
                            <td colspan="4">Total</td>
                            <td>{{number_format($net_pfp_target_amount,2)}}</td>
                            <td></td>
                            <td>{{number_format($net_pfp_earn_amount,2)}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>

                </div>

                @endforeach
            <br><br>   <br><br>   <br><br>
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
