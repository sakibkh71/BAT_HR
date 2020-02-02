<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Leave Entry - {{$emp_log->name}}</h4>
    {{--{{dd($emp_log)}}--}}
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <br>
            <form class="" method="post" id="leave_form" autocomplete="off">
                <div class="row user_found">
                    @if(isset($emp_leave_records))
                        <input type="hidden" name="hr_leave_records_id" id="hr_leave_records_id" class="hidden" value="{{$emp_leave_records->hr_leave_records_id}}"/>
                    @endif
                    <input type="hidden" name="user_id" class="employee_id" value="{{$emp_log->user_code}}"/>
                    {{csrf_field()}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Application Type <span class="required">*</span></label>
                            <div class="input-group">
                                <select class="form-control" name="application_type">
                                    <option value="Pre-Applied">Pre-Applied</option>
                                    <option value="Post-Applied">Post-Applied</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Leave Type <span class="required">*</span></label>
                            <div class="input-group">
                                {{__combo('hr_yearly_leave_policy',array('selected_value'=>@$emp_leave_records->leave_types, 'attributes'=> array('class'=>'form-control multi', 'id'=>'leave_type', 'required'=>'true')))}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Leave Date <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text"
                                       placeholder=""
                                       class="form-control daterange"
                                       id="leave_date"
                                       name="leave_date"
                                       value="{{isset($emp_leave_records) ? $emp_leave_records->start_date .' - '.$emp_leave_records->to_date : ''}}"
                                       required/>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Balance Leave</label>
                            <div class="input-group ">
                                <input type="text"
                                       placeholder=""
                                       class="form-control" readonly
                                       id="balance_leave"
                                       name="balance_leave"
                                       value=""/>
                            </div>
                        </div>
                        {{--<div class="card">--}}
                            {{--<div class="card-body text-center form-label">--}}
                                {{--<h2><span id="leave_days_text">{{isset($emp_leave_records) ? number_format($emp_leave_records->leave_days) : '1'}}</span> Day(s)</h2>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Application Date <span class="required">*</span></label>
                            <div class="input-group date">
                                <input type="text"
                                       placeholder=""
                                       class="form-control date"
                                       value="{{isset($emp_leave_records) ? $emp_leave_records->applied_date : date('Y-m-d')}}"
                                       id=""
                                       required
                                       name="application_date" required/>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Leave Days </label>
                            <div class="input-group ">
                                <input type="text"
                                       placeholder=""
                                       class="form-control"
                                       required
                                       readonly
                                       id="leave_days"
                                       name="leave_days"
                                       value="{{isset($emp_leave_records) ? $emp_leave_records->leave_days : '1'}}"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Remarks</label>
                            <div class="input-group">
                                <textarea class="form-control" name="remarks" rows="2">{{isset($emp_leave_records) ? $emp_leave_records->remarks : ''}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" id="leave_submit" class="btn btn-primary">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>
<script>
    $(function () {
        datepic();
    });

    $(document).ready(function () {
        // $('#leave_type').change(function () {
        $(document).on('change','#leave_type',function () {
                var uid = $('.employee_id').val(); //user_code
                var leave_type = $('#leave_type').val();

                // alert(uid+"--"+leave_type);
                ajax_call(uid, leave_type);
        });
    });

    $('#leave_date').daterangepicker({
        autoApply: true,
        locale: {
            format: 'YYYY-MM-DD'
        },

    }).on('apply.daterangepicker', function(ev, picker) {
        var start = picker.startDate.format('YYYY-MM-DD');
        var end = picker.endDate.format('YYYY-MM-DD');
        var diff =  Math.floor(( Date.parse(end) - Date.parse(start) ) / 86400000) + 1;
        $('#leave_days').val(diff);
        $('#leave_days_text').text(diff);
    });

    function ajax_call(uid = null, leave_type = null) {
        //console.log(uid,leave_type);
        $.ajax({
            type: 'post',
            url: '<?php echo URL::to('get-emp-leave-total'); ?>',
            async: false,
            data: {uid: uid, leave_type: leave_type},
            success: function (response) {
                $('#balance_leave').val(response);
            }
        });
    }
</script>