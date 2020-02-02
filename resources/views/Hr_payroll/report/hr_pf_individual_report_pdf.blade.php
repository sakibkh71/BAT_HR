
<?php echo $emp_info; ?>

<div class="col-xs-1" align="center"><u>From <?php echo $selected_val['start_month']; ?> To <?php echo $selected_val['end_month']; ?></u></div>
<br/>
<table id="employee_list"
       class="checkbox-clickable table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <td>SL No.</td>
            <td>Month</td>
            <td >Company Amount</td>
            <td >Employee Amount</td>
            <td>Total</td>
        </tr>

    </thead>
    <tbody>

        @if(!empty($opening_balance) )
        <tr class="row-select-toggle" id="">
            <td align="center">
            </td>
            <td>Opening Balance</td>
            <td>{{number_format($opening_balance->amount_company,2)}}</td>
            <td>{{number_format($opening_balance->amount_employee,2)}}</td>
            <td>{{number_format(($opening_balance->amount_company+$opening_balance->amount_employee),2)}}</td>
            <?php $gTotal= ($opening_balance->amount_company + $opening_balance->amount_employee); ?>
        </tr>
        @endif
        <?php
        $grandCompanyTotal = !empty($opening_balance) ? $opening_balance->amount_company : 0;
        $grandEmployeeTotal = !empty($opening_balance) ? $opening_balance->amount_employee : 0;
        ?>
        @if(!empty($employeewise_pfinfo))
        @foreach($employeewise_pfinfo as $i=>$emp)
        <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
            <td align="center">
                {{($i+1)}}
            </td>
            <td>{{$emp->hr_salary_month_name}}</td>
            <td>{{number_format($emp->pf_amount_company,2)}}</td>
            <td>{{number_format($emp->pf_amount_employee,2)}}</td>
            <td>{{number_format(($emp->pf_amount_company+$emp->pf_amount_employee),2)}}</td>
            <?php
            $grandCompanyTotal += $emp->pf_amount_company;
            $grandEmployeeTotal += $emp->pf_amount_employee;
            $gTotal+=$emp->pf_amount_company+$emp->pf_amount_employee;
            ?>

        </tr>
        @endforeach
        @endif

        <tr class="row-select-toggle" id="">
            <td align="center">
            </td>
            <td> Closing Balance</td>
            <td>{{number_format($grandCompanyTotal,2)}}</td>
            <td>{{number_format($grandEmployeeTotal,2)}}</td>
            <td>{{number_format(($grandCompanyTotal+$grandEmployeeTotal),2)}}</td>
            <?php $gTotal+=$grandCompanyTotal+$grandEmployeeTotal; ?>
        </tr>
        <tr class="row-select-toggle" id="">
            <td> </td>
            <td> </td>
            <td> Total Balance</td>
            <td>{{number_format($grandCompanyTotal+$grandEmployeeTotal,2)}}</td>
            <td>{{number_format($gTotal,2)}}</td>
        </tr>
    </tbody>
</table>
