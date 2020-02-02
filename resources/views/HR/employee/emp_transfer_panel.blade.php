    <section class="tab-pane fade active show" id="transfer" role="tabpanel" aria-labelledby="transfer-tab">
    <div class="step-header open-header" id="transfer_head">
        <h2>Transfer Information</h2>
        @if(isset($employee))
            <div class="pull-right">
                <button class="btn btn-success btn-xs" id="newTransfer"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                <button type="button" class="btn btn-primary btn-xs" style="display: none" id="view_transfer_letter"><i class="fa fa-eye"></i> Letter View</button>
                <button class="btn btn-primary btn-xs send_for_approval_transfer" style="display: none" id_slug="hr_tfr"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                <button class="btn btn-danger btn-xs item_delete_tfr ladda-button" style="display: none"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
            </div>
            <div id="transfer" class="collapsed" aria-labelledby="transfer_head" data-parent="#EmployeeAccordion">
                <div class="table-responsive">
                    <table id="employee_transfer_list"
                           class="checkbox-clickable-transfer table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th rowspan="2">#</th>

                            <th colspan="5" class="text-center">Current Position</th>
                            <th colspan="6" class="text-center">Transfer Position</th>
                            <th rowspan="2">Status</th>
                        </tr>
                        <tr>

                            <th>Branch</th>
                            <th>Department</th>
                            <th>Section</th>
                            <th>Unit</th>
                            <th>Designation</th>

                            <th>Applicable Date</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Section</th>
                            <th>Unit</th>
                            <th>Designation</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div id="transfer_edit_id" style="display: none"></div>
            </div>
        @endif
    </div>

</section>

<style>
    .selected{
        background-color: green;
        color: #FFF;
    }
    .selected:hover{
        background-color: green !important;
        color: #FFF;
    }
</style>
<script>

    var selected_emp_transfer = [];
    var send_transfer = [];
    $(document).on('click','.checkbox-clickable-transfer tbody tr',function (e) {
        $obj = $(this);
        if(!$(this).attr('id')){
            return true;
        }
        $obj.toggleClass('selected');
        var id = $obj.attr('id');
        if ($obj.hasClass( "selected" )){
            selected_emp_transfer.push(id);
            send_transfer.push($obj.data('status'));
        }else{
            var index = selected_emp_transfer.indexOf(id);
            selected_emp_transfer.splice(index,1);
            send_transfer.splice($.inArray($obj.data('status'), send_transfer), 1);

        }
        $('#transfer_edit_id').text(selected_emp_transfer);
        if(selected_emp_transfer.length==1){
            $('#view_transfer_letter').show();
        }else if(selected_emp_transfer.length==0){
            $('#view_transfer_letter').hide();
        }else{
            $('.send_for_approval_transfer, .item_delete_tfr, .item_edit').show();
            $('#view_transfer_letter').hide();
        }
        if(send_transfer.includes(59)||send_transfer.includes(60)){
            $('.send_for_approval_transfer, .item_delete_tfr, .item_edit').hide();
        }else{
            $('.send_for_approval_transfer, .item_delete_tfr, .item_edit').show();
        }
    });

    //Send for approval
    $(document).on('click', '.send_for_approval_transfer', function (e) {
        e.preventDefault();
        var id_slug = $(this).attr('id_slug');
        var job_value = [];
        var url = '<?php echo URL::to('go-to-hr-delegation-process');?>';

        var job_value = $('#transfer_edit_id').text();
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

    $(document).on('click','#view_transfer_letter', function (e) {

        var log_id = $('#transfer_edit_id').text();
        var data = {'log_id':log_id};

        if (selected_emp_transfer.length == 1) {
            var url= '<?php echo URL::to('get-hr-transfer-letter');?>/'+log_id;
            swalConfirm('To view Increment Letter.').then(function (e) {
                if(e.value){
                    window.open(url,'_blank');
                }
            });
        } else {
            swalWarning("Please select single item");
            return false;
        }
    });

    $(document).on('click', '.item_delete_tfr', function (e) {
        e.preventDefault();
        Ladda.bind(this);
        var load = $(this).ladda();
        var log_id = $('#transfer_edit_id').text();
        var data = {log_id:log_id};
        var url = '<?php echo URL::to('hr-record-delete');?>';
        if(log_id.length) {
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

    // transfer & promotion
    // $('.employee_transfer_list').dataTable();
    $(document).on('click','#newTransfer',function () {
        var url = '<?php echo URL::to('get-emp-transfer-form');?>';
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

    
    $(document).on('click', '#transfer_submit', function (event) {
        if (!$('#transfer_form').validator('validate').has('.has-error').length) {

            if (employeeId !='null'){
                var url = '{{route('hr-store-transfer')}}';
                var $form = $('#transfer_form');
                var data = {
                    'sys_users_id' : employeeId
                };
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if(response.success){
                        swalSuccess('Transfer Successfully.');
                        $('#medium_modal').modal('hide');
                        transfer_list();

                    }
                });
            }else{
                swalError("Sorry! you need to add personal information first");
            }
        }
    });
    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "yyyy-mm-dd"
    });

    function transfer_list() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        var url2 = '{{route('hr-transfer-history')}}';
        var data2 = {
            'sys_users_id' : employeeId
        };
        $.ajax({
            url:url2,
            type:'post',
            data:data2,
            async:false,
            success:function (response) {
                if (response) {
                    $('#employee_transfer_list tbody').html(response.data);
                    // $('#employee_transfer_list').DataTable();
                }
            }
        });
    }
    $(function () {
        transfer_list();
    });
</script>