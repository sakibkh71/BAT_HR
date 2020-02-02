<style>
    .hr-out-of{
        padding: 10px 0 0 0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title text-left">Leave Report</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>

<div class="modal-body">








    <div class="row">
        <div class="col-lg-6" id="cluster_info">
            <dl class="row mb-0">
                <div class="col-sm-4 text-sm-right">
                    <dt>Employee Name:</dt>
                </div>
                <div class="col-sm-8 text-sm-left">
                    <dd class="mb-1">{{$emp_info->name}}</dd>
                </div>
            </dl>
            <dl class="row mb-0">
                <div class="col-sm-4 text-sm-right">
                    <dt>Employee ID:</dt>
                </div>
                <div class="col-sm-8 text-sm-left">
                    <dd class="mb-1">{{$emp_info->user_code}}</dd>
                </div>
            </dl>
            <dl class="row mb-0">
                <div class="col-sm-4 text-sm-right">
                    <dt>Date of Join:</dt>
                </div>
                <div class="col-sm-8 text-sm-left">
                    <dd class="mb-1">{{$emp_info->date_of_join}}</dd>
                </div>
            </dl>
        </div>
        <div class="col-lg-6" id="cluster_info">
            <dl class="row mb-0">
                <div class="col-sm-4 text-sm-right">
                    <dt>Designation:</dt>
                </div>
                <div class="col-sm-8 text-sm-left">
                    <dd class="mb-1">{{$emp_info->designations_name}}</dd>
                </div>
            </dl>
            <dl class="row mb-0">
                <div class="col-sm-4 text-sm-right">
                    <dt>Distributor House:</dt>
                </div>
                <div class="col-sm-8 text-sm-left">
                    <dd class="mb-1">{{$emp_info->distributor_house}}</dd>
                </div>
            </dl>
            <dl class="row mb-0">
                <div class="col-sm-4 text-sm-right">
                    <dt>Distributor Point:</dt>
                </div>
                <div class="col-sm-8 text-sm-left">
                    <dd class="mb-1">{{$emp_info->distributor_point}}</dd>
                </div>
            </dl>
            {{--<dl class="row mb-0">--}}
            {{--<div class="col-sm-4 text-sm-right">--}}
            {{--<dt>Category:</dt>--}}
            {{--</div>--}}
            {{--<div class="col-sm-8 text-sm-left">--}}
            {{--<dd class="mb-1">{{$emp_log->hr_emp_category_name}}</dd>--}}
            {{--</div>--}}
            {{--</dl>--}}
        </div>
    </div>
    <div style="height: 30px;"></div>
    <table id="record_table" class="table table-striped text-lefts table-bordered">
        <thead>
        <tr>
            <td><b>Leave Types</b></td>
            <td><b>Entitle Days</b></td>
            <td><b>Leave Taken</b></td>
            <td><b>Leave Balance</b></td>
        </tr>
        </thead>
        <tbody>

        <?php
        $total_policy_leave = 0;
        $total_elapsed = 0;

        ?>

        {{--    $.each(data.leave_policys,function(i,v){--}}
        {{--    total_policy_leave = v.policy_days;--}}
        {{--    total_elapsed = v.enjoyed_leaves;--}}

        @foreach($leave_policys as $v)

            <?php
            $total_policy_leave+= $v->policy_days;
            $total_elapsed  +=$v->enjoyed_leaves;
            ?>
            <tr>
                <td>{{$v->hr_yearly_leave_policys_name}}</td>
                <td class="text-right">{{$v->policy_days}}</td>
                <td class="text-right">{{$v->enjoyed_leaves?$v->enjoyed_leaves:0}}</td>
                <td class="text-right">{{$v->policy_days-$v->enjoyed_leaves}}</td>
            </tr>
        @endforeach

        </tbody>
        <tfoot>
        <tr>
            <td>Total</td>
            <td class="text-right"></td>
            <td class="text-right">{{$total_elapsed}}</td>
            <td class="text-right">{{$total_policy_leave}}</td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="leave_report_pdf">Print</button>
</div>
<script>
    $(document).on('click','#leave_report_pdf',function(){


        var user_id = {{$emp_info->id}};

        var redirectWindow = window.open('{{url('/')}}'+'/emp_leave_report_print/'+user_id, '_blank');
        redirectWindow.location;




    });
</script>
