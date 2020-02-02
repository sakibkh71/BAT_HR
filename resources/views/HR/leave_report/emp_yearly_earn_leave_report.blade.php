<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Yearly Earn Leave Report</h4>
</div>
<div class="modal-body">
    <?php echo @$emp_info?>
    <br>
    <div class="row">
        <div class="col-sm-12">

            <div class="table-responsive">
                <table id="leave_report" class="table table-striped table-bordered text-lefts">
                    <tr>
                        <th>Month/ Year</th>
                        @for($i=1;$i<=12;$i++)
                            <th>{{date('F', mktime(0, 0, 0, $i, 10))}}</th>
                        @endfor
                        <th>Total Days</th>
                        <th>Earn Leave Days</th>
                        <th>Previous Leave</th>
                        <th>Total Leave</th>
                        <th>Enjoyed Days</th>
                        <th>Balance Leave</th>
                        <th>Leave Encashment</th>
                        <th>Net Balance</th>
                    </tr>
                    <tbody>
                    @if(!empty($yearly_logs))
                       @php( $yearly_earn_leaves = yearEarnLeaveEnjoy($sys_users_id))
                       @php( $cash_leaves = year_earn_leave_encash($sys_users_id))
                        @php($yearly_total=$earn_leave=$enjoy_leave=$previous_leave=$balance=$net_balance=$total_pleave=$total_earn_leave=$temp_net_leave=0)
                        @foreach($yearly_logs as $key=>$log)
                            @php($yearly_total=$earn_leave=$enjoy_leave=$balance=$cash_leave=0)
                            @php($cash_leave = isset($cash_leaves[$key])?$cash_leaves[$key]:0)
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
            </div>
            <div class="">
                <p>Compensation Days: <b>{{@$compensation_days}} Days</b></p>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <form action="{{route('leave-encashment-create')}}" method="post" class="inline">
        @csrf
        <input type="hidden" name="users" value="{{@$sys_users_id}}">
        <input type="hidden" name="net_balance" value="{{@$net_balance}}">
        <button type="submit" class="btn btn-primary"><i class="fa  fa-money"></i> Leave Encashment</button>
    </form>
    <a href="{{URL::to('emp-earn-leave-report-print')}}/{{$sys_users_id}}" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
    <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
</div>
</div>
<style>
    #leave_report tbody tr td{
        text-align: right;
    }
</style>