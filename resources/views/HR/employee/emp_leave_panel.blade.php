<section class="tab-pane fade active show" id="leave" role="tabpanel" aria-labelledby="leave_info-tab">
    <div class="step-header open-header" id="leave_info">
        <div class="row">
            <div class="col-md-4">
                <h2>Leave Information</h2>
            </div>
            <div class="col-md-8">
                {{--{{dd($employee)}}--}}
                @if(isset($employee))
                    <form action="{{URL::to('employee-entry/'.$employee->id.'/leave')}}" method="post" class="float-left" style="width: 150px" id="yearFilterForm">
                        @csrf
                        <div class="input-group date_year">
                            <input type="text"
                                   placeholder=""
                                   class="form-control"
                                   value="{{ !empty($post_year)?$post_year:date('Y')}}"
                                   id="leave_year" name="leave_year"/>
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-success float-left" id="yearlyLeave"><i class="fa fa-recycle" aria-hidden="true"></i> &nbsp; Yearly Leave</button>
                    <div class="pull-right">
{{--                        <button class="btn btn-success btn-xs" id="newLeave"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>--}}
                        <button id="editLeave" class="btn btn-warning btn-xs item_edit_leave ladda-button" style="display: none"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                        <button class="btn btn-primary btn-xs send_for_approval_leave" style="display: none" id_slug="hr_leave"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                        <button class="btn btn-danger btn-xs item_delete_leave ladda-button" style="display: none"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                    </div>
                @endif
            </div>
        </div>
        @if(isset($employee))
            <div id="leave_info_area" class="collapse show" aria-labelledby="leave" data-parent="#EmployeeAccordion">
                <div class="wrapper wrapper-content animated fadeInRight">
                    <div class="row">
                        <div class="col-lg-12 no-padding">
                            <div class="ibox">
                                <div class="ibox-content table-responsive">
                                    <div class="row">
                                        <div class="col-xl-9 col-md-8">
                                            <h3>Leave History</h3>
                                            <div class="table-responsive">
                                            <table class="checkbox-clickable-leave table table-striped table-bordered table-hover emp-leave-list">
                                                <thead>
                                                <tr>
                                                    <th width="">Applied</th>
                                                    <th width="">Leave Type</th>
                                                    <th width="">Leave Date</th>
                                                    <th width="">Days</th>
                                                    <th width="">Applied Date</th>
                                                    <th width="">Approved Date</th>
                                                    <th width="25%">Remarks</th>
                                                    <th width="">Status</th>
                                                    <th width="">Delegation Person</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!empty($leave_records))
                                                    @foreach($leave_records as $leave_record)
                                                        <tr class="row-select-toggle" id="{{$leave_record->hr_leave_records_id}}" data-status="{{$leave_record->leave_status}}">
                                                            <td>{{$leave_record->application_type}}</td>
                                                            <td>{{$leave_record->leave_types}}</td>
                                                            <td>{{toDated($leave_record->start_date)}} - {{toDated($leave_record->to_date)}}</td>
                                                            <td class="text-center">{{$leave_record->leave_days}}</td>
                                                            <td>{{toDated($leave_record->applied_date)}}</td>
                                                            <td>{{$leave_record->approval_date>0?toDated($leave_record->approval_date):''}}</td>
                                                            <td>{{$leave_record->remarks}}</td>
                                                            <td>{{$leave_record->status_flows_name}}</td>
                                                            <td>{{$leave_record->delegation_person_name}}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-4">
                                            @include('HR.leave_manager.leave_summary',@$leave_policys)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

    <div id="leave_edit_id" style="display: none"></div>
    <script>

        $(document).on('click','#yearlyLeave',function () {
            var url = '<?php echo URL::to('get-emp-yearly-leave');?>';
            var data = {
                'sys_users_id':'{{$employee->id}}',
                'year':$('#leave_year').val()
            };
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data,url,load).done(function (response) {
                if(response){
                   $('#large_modal .modal-content').html(response);
                    $('#large_modal').modal('show');
                }

            });
        });
        $(function () {

            $('#leave_year').datepicker({
                viewMode: "years",
                minViewMode: "years",
                format: "yyyy",
                autoclose:true
            });
            $('.emp-leave-list').dataTable();
            var url1 = '<?php echo URL::to('get-emp-info');?>/{{$employee->user_code}}';
            var basic_info_ajax = makeAjax(url1, null).then(function(data) {
                if(data.success == 0){
                    swalError("No Employee Found for this code.");
                }
            });
        });

        $(document).on('click','#newLeave',function () {
            var url = '<?php echo URL::to('get-emp-leave-form');?>';
            var data = {'emp_id':employeeId};
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data,url,load).done(function(response){
                if(response){
                    $('#medium_modal .modal-content').html(response);
                    $('#medium_modal').modal('show');
                }
            });
        });

        $(document).on('click','#editLeave',function () {
            var url = '<?php echo URL::to('get-emp-leave-form');?>';
            var record_id = $('#leave_edit_id').text();
            var data = {'emp_id':employeeId,'record_id':record_id};
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data,url,load).done(function(response){
                if(response){
                    $('#medium_modal .modal-content').html(response);
                    $('#medium_modal').modal('show');
                }
            });
        });

        $(document).on('click', '#leave_submit', function (event) {
            event.preventDefault();

            if (!$('#leave_form').validator('validate').has('.has-error').length) {
                var user_code = '{{$employee->user_code}}';
                var leave_id = $('#hr_leave_records_id').val();
                var check_data = {user_code:user_code, leave_id:leave_id};
                var check_url = '{{route('check-pending-leave-exist')}}';
                makeAjaxPost(check_data, check_url, null).then(function (resp) {
                    if (resp.pending =="no") {
                        var leave_balance = $("#balance_leave").val();
                        var leave_days = $("#leave_days").val();

                        if (Number(leave_days) <= Number(leave_balance)) {
                            var $form = $('#leave_form');
                            var data = {
                                'user_id': employeeId
                            };
                            data = $form.serialize() + '&' + $.param(data);
                            var url = '<?php echo URL::to('save-leave-info');?>';
                            makeAjaxPost(data, url).done(function (response) {
                                if (response) {
                                    swalSuccess('Leave Successfully.');
                                    $('#medium_modal').modal('hide');
                                    window.location.reload();
                                }
                            });
                        }
                        else {
                            swalError("You can not select days more than balance leaves.");
                        }

                    }else{
                        swalError('Leave request already exist for this employee, <br> please do the action first for this leave.');
                    }
                });
            }

        });

        var selected_emp_leave = [];
        var send_leave = [];
        $(document).on('click','.checkbox-clickable-leave tbody tr',function (e) {
            $obj = $(this);
            if(!$(this).attr('id')){
                return true;
            }
            $obj.toggleClass('selected');
            var id = $obj.attr('id');
            if ($obj.hasClass( "selected" )){
                selected_emp_leave.push(id);
                send_leave.push($obj.data('status'));
            }else{
                var index = selected_emp_leave.indexOf(id);
                selected_emp_leave.splice(index,1);
                send_leave.splice($.inArray($obj.data('status'), send_leave), 1);

            }
            $('#leave_edit_id').text(selected_emp_leave);

            if(selected_emp_leave.length==1){
                $('.send_for_approval_leave, .item_delete_leave, .item_edit_leave').show();
            }else if(selected_emp_leave.length==0){
                $('.send_for_approval_leave, .item_delete_leave, .item_edit_leave').hide();
            }else{
                $('.send_for_approval_leave, .item_delete_leave').show();
                $('.item_edit_leave').hide();
            }
            // console.log(selected_emp_leave);
            if(send_leave.includes(63)||send_leave.includes(64)){
                $('.send_for_approval_leave, .item_delete_leave, .item_edit_leave').hide();
            }else{
                $('.send_for_approval_leave, .item_delete_leave').show();
            }
            if(selected_emp_leave.length==0){
                $('.send_for_approval_leave, .item_delete_leave, .item_edit_leave').hide();
            }
        });

        $(document).on('click', '.item_delete_leave', function (e) {
            e.preventDefault();
            Ladda.bind(this);
            var load = $(this).ladda();
            var record_id = $('#leave_edit_id').text();
            var data = {record_id:record_id};
            var url = '<?php echo URL::to('hr-leave-record-delete');?>';
            if(record_id.length) {
                swalConfirm("Delete Selected Items").then(function (e) {
                    if (e.value) {
                        makeAjaxPost(data,url,load).done(function (response) {
                            var url2 = window.location;
                            if(response.success){
                                swalRedirect(url2,"Successfully Delete",'success');
                            }else{
                                swalWarning('Operation Failed!');
                            }
                        });
                    }else{
                        load.ladda('stop');
                    }
                });

            }else{
                swalWarning("Please select at least one job!");
            }

        });

        //Send for approval
        $(document).on('click', '.send_for_approval_leave', function (e) {
            e.preventDefault();
            var id_slug = $(this).attr('id_slug');
            var job_value = [];
            var url = '<?php echo URL::to('go-to-leave-delegation-process');?>';

            var job_value = $('#leave_edit_id').text();
            job_value = job_value.split(',');
            if(job_value.length){
                swalConfirm().then(function (e) {
                    if(e.value){
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {slug:id_slug,code:job_value,'delegation_type':'send_for_approval'},
                            success: function (data) {
                                var url = window.location;
                                swalRedirect(url,data,'success');
                            },
                            failure: function() {
                                swalError('Failed');
                            }
                        });
                    }
                });
            }else{
                swalWarning("Please select at least one job!");
            }
        });

        $('#leave_year').change(function () {
            $('#yearFilterForm').submit();
        })

    </script>
