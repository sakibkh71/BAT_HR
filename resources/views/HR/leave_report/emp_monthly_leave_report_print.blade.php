<?php echo $emp_info; ?>


<table id="record_table" class="table table-striped text-lefts table-bordered">
    <thead>
    <tr>
        <td>Leave Types</td>
        <td>Entitle Days</td>
        <td>Enjoyed Day</td>
        <td>Balance Days</td>
    </tr>
    </thead>
    <tbody>
    @php
        $total_policy_leave = $total_elapsed = 0;
    @endphp
    @if(isset($leave_policys)&&!empty($leave_policys))
        @foreach($leave_policys as $policy)
            @php
                $total_policy_leave = $total_policy_leave+$policy->policy_days;
                $total_elapsed = $total_elapsed+$policy->balance_leaves;
            @endphp
            <tr>
                <td>{{@$policy->hr_yearly_leave_policys_name}}</td>
                <td class="text-right">{{@$policy->policy_days}}</td>
                <td class="text-right">{{@($policy->policy_days-$policy->balance_leaves)}}</td>
                <td class="text-right">{{@$policy->balance_leaves?$policy->balance_leaves:0}}</td>

            </tr>
        @endforeach
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td>Total</td>
        <td class="text-right">{{@$total_policy_leave}}</td>
        <td class="text-right">{{$total_policy_leave-$total_elapsed}}</td>
        <td class="text-right">{{@$total_elapsed}}</td>

    </tr>
    </tfoot>
</table>


<br>
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

