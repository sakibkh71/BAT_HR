<div class="ibox ">
    <div class="ibox-title">
        <h4><i class="fa fa-history"></i> {{isset($year)?$year:date('Y')}} Leave Summary </h4>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content no-padding">
        <table class="table table-striped text-lefts table-bordered">
            <thead>
            <tr>
                <th>Leave Types</th>
                <th>Entitle Days</th>
                <th>Enjoyed Day</th>
                <th>Balance Days</th>
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
                    $total_elapsed = $total_elapsed+$policy->enjoyed_leaves;
                    @endphp

                    <tr>
                        <th>{{$policy->hr_yearly_leave_policys_name ??''}}</th>
                        <td class="text-right">{{$policy->policy_days ?? 0}}</td>

                        <td class="text-right">{{$policy->enjoyed_leaves??0}}</td>

                        <td class="text-right">{{$policy->balance_leaves??0}}</td>

                    </tr>
                @endforeach
            @endif
            </tbody>
            <tfoot>
            <tr>
                <th>Total</th>
                <th class="text-right">{{@$total_policy_leave}}</th>
                <th class="text-right">{{@$total_elapsed}}</th>
                <th class="text-right">{{$total_policy_leave-$total_elapsed}}</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>