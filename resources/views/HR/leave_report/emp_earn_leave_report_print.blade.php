<?php echo $emp_info; ?>

<table id="record_table" class="table table-striped table-bordered text-lefts">
    <thead>
    <tr>
        <td>Month/ Year</td>
        @for($i=1;$i<=12;$i++)
            <td>{{date('F', mktime(0, 0, 0, $i, 10))}}</td>
        @endfor
        <td>Total Days</td>
        <td>Earn Leave Days</td>
        <td>Previous Leave</td>
        <td>Total Leave</td>
        <td>Enjoyed Days</td>
        <td>Balance Leave</td>
        <td>Leave Encashment</td>
        <td>Net Balance</td>
    </tr>
    </thead>

    <tbody>
    @if(!empty($yearly_logs))
        @php( $yearly_earn_leaves = yearEarnLeaveEnjoy($sys_users_id))
        @php($yearly_total=$earn_leave=$enjoy_leave=$previous_leave=$balance=$cash_leave=$net_balance=$total_pleave=$total_earn_leave=$temp_net_leave=0)
        @foreach($yearly_logs as $key=>$log)
            @php($yearly_total=$earn_leave=$enjoy_leave=$balance=$cash_leave=0)
            <tr>
                <td>{{$key}}</td>
                @for($i=1;$i<=12;$i++)
                    @php($yearly_total += isset($log[$i]['present'])?$log[$i]['present']:0)
                    @php($enjoy_leave = isset($yearly_earn_leaves[$key])?$yearly_earn_leaves[$key]:0)
                    <td>{{isset($log[$i]['present'])?number_format($log[$i]['present']):0}}</td>
                @endfor

                <?php
                $earn_leave = number_format($yearly_total/18,2);

                $previous_leave = number_format($total_pleave,2);
                $total_earn_leave =$earn_leave+$temp_net_leave;
                $balance = number_format($total_earn_leave-$enjoy_leave,2);
                $net_balance = number_format($balance-$cash_leave,2);

                $total_pleave += $net_balance;
                ?>
                <td>{{$yearly_total}}</td>
                <td>{{$earn_leave}}</td>
                <td>{{$temp_net_leave}}</td>
                <td>{{$total_earn_leave}}</td>
                <td>{{$enjoy_leave}}</td>
                <td>{{number_format($balance,2)}}</td>
                <td>{{$cash_leave}}</td>
                <td>{{$net_balance}}</td>
            </tr>
            @php($temp_net_leave = $net_balance)
        @endforeach
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td colspan="20"></td>
        <td class="text-right font-bold">{{@$net_balance}}</td>
    </tr>
    </tfoot>
</table>


<br>
<p>Compensation Days: <b>{{@$compensation_days}} Days</b></p>
<br>
@include('HR.hr_default_signing_block')
<style>
    #record_table tr td {
        padding: 3px;
    }
    #record_table tr td{
        text-align: right;
    }

    #record_table thead tr td{
        text-align: left;
    }
    #record_table thead tr td, #record_table tfoot tr td {
        font-weight: bold;
    }
</style>

