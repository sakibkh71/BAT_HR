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
    }
</style>

<?php echo employeeInfo($user_id, 1); ?>

<h4> Leave Summary </h4>


<table class="table table-striped text-lefts table-bordered">
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
                $total_policy_leave = $total_policy_leave+$policy->policy_leave_days;
                $total_elapsed = $total_elapsed+$policy->total_elapsed;
            @endphp
            <tr>
                <td>{{@$policy->hr_yearly_leave_policys_name}}</td>
                <td class="text-right">{{@$policy->policy_leave_days}}</td>
                <td class="text-right">{{@$policy->total_elapsed?$policy->total_elapsed:0}}</td>
                <td class="text-right">{{@($policy->policy_leave_days-$policy->total_elapsed)}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td>Total</td>
        <td class="text-right">{{@$total_policy_leave}}</td>
        <td class="text-right">{{@$total_elapsed}}</td>
        <td class="text-right">{{$total_policy_leave-$total_elapsed}}</td>
    </tr>
    </tfoot>
</table>
<br><br><br>
@include('HR.hr_default_signing_block')