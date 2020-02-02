<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Yearly Leave Report - {{@$report_year}}</h4>
</div>
<div class="modal-body">
    <?php echo @$emp_info?>
    <br>
        <div class="row">
        <div class="col-sm-12">

                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-lefts">
                        <tr>
                            <th>Month</th>
                            @if(isset($leave_policys))
                                @foreach($leave_policys as $policy)
                                    <th>{{$policy->hr_yearly_leave_policys_name}}</th>
                                @endforeach
                            @endif
                            <th>Total</th>
                            <th>Remarks</th>
                        </tr>
                        <tbody>

                            @for($i=1;$i<=12;$i++)
                                @php
                                    $total_leave=0;
                                @endphp
                                {{--<tr>--}}
                                    {{--<th>{{date("F", mktime(0, 0, 0, $i, 10))}}</th>--}}
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
                            <th>Total</th>
                            @php($gtotal=0)
                            @if(isset($leave_policys))
                                @foreach($leave_policys as $policy)
                                    @php($gtotal+=array_sum(array_column($leave_records,$policy->hr_yearly_leave_policys_name)))
                                    <th class="text-right">{{array_sum(array_column($leave_records,$policy->hr_yearly_leave_policys_name))}}</th>
                                @endforeach
                            @endif
                            <th class="text-right">{{@$gtotal}}</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <a href="{{URL::to('hr-emp-yearly-leave-report')}}/{{$sys_users_id}}/{{$report_year}}" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>