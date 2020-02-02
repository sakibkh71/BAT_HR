@extends('layouts.app')
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Employee Release  List</h2>
                    <div class="ibox-tools">
                        <button class="btn btn-primary btn-xs no-display" id="send_for_approval_btn"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                        <button class="btn btn-primary btn-xs" id="separation_view"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
                        <button class="btn btn-warning btn-xs" id="separation_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="btn btn-success btn-xs" id="separation_undo"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Undo</button>
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('hr_leaver_emp_list',1) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Separation Modal -->

    <div class="modal fade" tabindex="-1" id="separationModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Employee Release  Process</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="hidden" id="hr_emp_separation_id" value=""/>
                            <label class="font-normal"><strong>Release  Date</strong> <span
                                        class="required">*</span></label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" name="separation_date" id="separation_date" class="form-control"
                                       data-error="Please select Release  Date" value=""
                                       placeholder="YYYY-MM-DD" required="" autocomplete="off">
                            </div>
                            <div class="help-block with-errors has-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="leaverProcess" class="btn btn-primary">Process</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#separation_confirm, #separation_view, #separation_edit, #separation_undo').hide();
        $('#separation_date').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true
        });

        //initial array
        var selected = [];
        var separation_dates = [];
        var emp_ids = [];
        var leaver_confirms = [];
        var sep_status = [];

        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            var separation_date = self.data('separation_date');
            var emp_id = self.data('sys_users_id');
            var is_confirm = self.data('is_confirm');
            var sst = self.data('separation_status');


            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    selected.push(id);
                    separation_dates.push(separation_date);
                    emp_ids.push(emp_id);
                    leaver_confirms.push(is_confirm);
                    sep_status.push(sst);
                } else {
                    selected.splice(selected.indexOf(id), 1);
                    separation_dates.splice(separation_dates.indexOf(separation_date), 1);
                    emp_ids.splice(emp_ids.indexOf(emp_id), 1);
                    sep_status.splice(sep_status.indexOf(sst), 1);
                    leaver_confirms.splice(leaver_confirms.indexOf(is_confirm), 1);
                }

                var arr_length = selected.length;



                if (arr_length > 1) {
                    $('#separation_edit').hide();
                    $('#separation_undo').hide();
                    $('#separation_view').hide();

                    if(!sep_status.includes(103) && !sep_status.includes(104)){
                        $('#send_for_approval_btn').show();
                    }else{
                        $('#send_for_approval_btn').hide();
                    }
                }
                else if (arr_length == 1) {

                    $('#separation_view').show();
                    if ($('#table-to-print tr.selected').data('separation_status') == 102){
                        $('#send_for_approval_btn').show();
                        $('#separation_edit').show();
                        $('#separation_undo').show();
                    }else{
                        $('#send_for_approval_btn').hide();
                        $('#separation_edit').hide();
                        $('#separation_undo').hide();
                    }
                }
                else {
                    $('#separation_view').hide();
                    $('#separation_edit').hide();
                    $('#separation_undo').hide();
                    $('#send_for_approval_btn').hide();
                }
            }
        });



        $('#separation_confirm').on('click',function (e) {
            Ladda.bind(this);
            var load = $(this).ladda();
           var data = {
               hr_emp_separation_id: selected
           };
           var url = '{{route('emp-separation-confirm')}}';
            if(selected.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                swalConfirm('to Confirm Release Selected Employees').then(function (e) {
                    if(e.value){
                        makeAjaxPost(data,url,load).done(function (response) {
                            if(response.success){
                                swalSuccess('Confirm Release Success');
                                window.location.reload();
                            }else{
                                swalError();
                            }
                        });
                        load.ladda('stop');
                    }else{
                        load.ladda('stop');
                    }
                });

            }
        });

        $('#separation_view').on('click',function (e) {
            var hr_emp_separation_id=selected[0];
            if(selected.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                var url = "{{route('separation-settlement-pdf')}}/"+hr_emp_separation_id;
                window.open(url,'_blank');
            }
        });

        $('#separation_edit').on('click',function (e) {
            var separation_date=separation_dates[0];
            var hr_emp_separation_id=selected[0];
            if(selected.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                $('#separation_date').val(separation_date);
                $('#hr_emp_separation_id').val(hr_emp_separation_id);
                $('#separationModal').modal('show');

            }
        });

        $('#separation_undo').on('click',function (e) {
            var separation_date=separation_dates[0];
            var hr_emp_separation_id=selected[0];
            var emp_id = emp_ids[0];

            // alert(separation_date+"---"+hr_emp_separation_id+'--'+emp_id);
            if (selected.length === 0) {
                swalError("Please select a Employee");
                return false;
            } else {
                swalConfirm('You want to back this employee in employee list?').then(function (e) {
                    if(e.value){

                        var redirect_url = "{{URL::to('employee')}}";
                        // var data = {kpi_config_id: kpi_config_id};
                        {{--makeAjaxPost(data,url,null).then(function(response) {--}}
                            {{--var redirect_url = "{{URL::to('kpi-config')}}";--}}
                            {{--swalRedirect(redirect_url,'Delete Successfully','success');--}}
                        {{--});--}}
                        var url = "{{url('separation-undo')}}/"+hr_emp_separation_id+'/'+emp_id;

                        $.get(url, function(data){
                            if(data.status == 200){
                                swalRedirect(redirect_url,'Separation canceled successfully','success');
                            }
                            else{
                                swalError('Try again! Process not done yet!');
                            }

                        });
                    }
                });

            }
        });

        $('#leaverProcess').click(function () {
            var employee_id=emp_ids[0];
            var hr_emp_separation_id=selected[0];
            var separation_date = $('#separation_date').val();

            if(separation_date){
                var url = "{{route('get-separation-form')}}/"+employee_id+'/'+separation_date+'/'+hr_emp_separation_id;
                window.location.replace(url);
            }else{
                swalError('Please Select Release Date');
            }

        });


        //Send for approval
        $(document).on('click', '#send_for_approval_btn', function (e) {
            e.preventDefault();
            var id_slug = 'hr_sep';
            var url = '<?php echo URL::to('separation-delegation-process');?>';
            var job_value = selected;

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
    </script>
@endsection
