<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Monthly Leave Report ({{date('M-Y',strtotime($report_month))}})</h4>
</div>
<div class="modal-body">
<?php echo @$emp_info?>
    <br>

    <div class="row">
        <div class="col-sm-12">
            @include('HR.leave_manager.leave_summary',@$leave_policys)
        </div>
    </div>
</div>
<style>
    table tr td{
        text-align: right;
    }
</style>
<div class="modal-footer">
    <a href="{{URL::to('emp-monthly-leave-report-print')}}/{{$sys_users_id}}/{{$report_month}}" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
    <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
</div>
</div>