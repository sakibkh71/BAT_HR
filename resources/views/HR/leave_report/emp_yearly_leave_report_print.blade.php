<?php echo $emp_info;?>

<table id="record_table" class="table table-bordered">
    <thead>
    <tr>
        <td>Month</td>
        @if(isset($leave_policys))
            @foreach($leave_policys as $policy)
                <td>{{$policy->hr_yearly_leave_policys_name}}</td>
            @endforeach
        @endif
        <td>Total</td>
        <td>Remarks</td>
    </tr>
    </thead>
    <tbody>

        @for($i=1;$i<=12;$i++)
            @php
                $total_leave=0;
            @endphp
            {{--<tr>--}}
                {{--<td>{{date("F", mktime(0, 0, 0, $i, 10))}}</td>--}}
                @foreach($leave_policys as $policy)
                    @php
                        if(isset($leave_records[$i])){
                            $total_leave = array_sum($leave_records[$i]);
                        }
                    @endphp
                    {{--<td class="text-right">{{isset($leave_records[$i][$policy->hr_yearly_leave_policys_name])?$leave_records[$i][$policy->hr_yearly_leave_policys_name]:0}}</td>--}}
                @endforeach
                {{--<td class="text-right">{{@$total_leave}}</td>--}}
                {{--<td></td>--}}
            {{--</tr>--}}
        @endfor
    </tbody>
    <tfoot>
    <tr>
        <td>Total</td>
        @php($gtotal=0)
        @if(isset($leave_policys))
            @foreach($leave_policys as $policy)
                @php($gtotal+=array_sum(array_column($leave_records,$policy->hr_yearly_leave_policys_name)))
                <th class="text-right">{{array_sum(array_column($leave_records,$policy->hr_yearly_leave_policys_name))}}</td>
            @endforeach
        @endif
        <th class="text-right">{{@$gtotal}}</td>
        <td></td>
    </tr>
    </tfoot>
</table>
<br>
<br>
@include('HR.hr_default_signing_block')
<style>
    #record_table tr td{
        padding: 3px;
    }
   #record_table thead tr td,#record_table tfoot tr td{
      font-weight: bold;
   }
</style>

