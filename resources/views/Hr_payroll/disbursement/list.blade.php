@extends('layouts.app')
@section('content')
    {{--{{dd(Session::all())}}--}}
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Loan & Advance Salary</h3>
                    </div>
                    <div class="ibox-tools">
                        {{--<button class="btn btn-xs" id="show-custom-search"><i class="fa fa-search"></i> show search</button>--}}
                        <button class="btn btn-success btn-xs no-display" id="loan_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="btn btn-primary btn-xs no-display" id="send_for_approval_btn"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                        <button class="btn btn-danger btn-xs no-display" id="loan_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        <a href="{{route('new-loan-entry')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus-circle"></i> New Loan Entry</a>
                        <button class="btn btn-print btn-xs btn-success" data-toggle="modal" data-target=".myModal" id="loan_paid"><i class="fa fa-print" aria-hidden="true"></i> Paid Loan Amount</button>
                        <button class="btn btn-print btn-xs no-display" id="loan_print"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
                    </div>
                    <div class="ibox-content">
                        {!! __getMasterGrid('loan_employee_list') !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!--  Modal -->
    <div class="modal fade myModal" id="" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="loanPaidForm">
                    <div class="modal-header">
                        <h4 class="modal-title">Paid Loan Amonut</h4>
                    </div>
                    <div class="modal-body col-md-12">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Paid Amonut</strong> <span class="required">*</span> </label>
                                    <div class="">
                                        <input type="number" name="amount" id="amount" class="form-control">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="loan_id" id="loan_id" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn" type="button" id="loanPaidSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var selected = [];
        var locstatus =[];
        var loan_status =[];

        $(document).ready(function(){
            $('#loan_paid').hide();
            $('#loan_id').val('');
        })

        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            var lcs = self.data('lock_status');
            var ls = self.data('loan_status');

            /*add this for new customize*/
            selected = [];
            locstatus =[];
            loan_status =[];
            $('#table-to-print tbody tr').not($(this)).removeClass('selected');
            /* end this */

            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    selected.push(id);
                    locstatus.push(lcs);
                    loan_status.push(ls);
                } else {
                    selected.splice(selected.indexOf(id), 1);
                    locstatus.splice(locstatus.indexOf(lcs), 1);
                    loan_status.splice(locstatus.indexOf(ls), 1);
                }

                var arr_length = selected.length;

                if (arr_length > 1) {
                    $('#loan_edit').hide();
                    $('#loan_lock').hide();
                    $('#loan_print').hide();
                    $('#loan_paid').hide();
                    if(locstatus.includes(1)){
                        $('#loan_delete').hide();
                        $('#send_for_approval_btn').hide();
                    }else if(!loan_status.includes(99) && !loan_status.includes(100)){
                        $('#loan_delete').show();
                        $('#send_for_approval_btn').show();
                    }else{
                        $('#loan_delete').hide();
                        $('#send_for_approval_btn').hide();
                    }
                }
                else if (arr_length == 1) {

                    if ($('#table-to-print tr.selected').data('loan_status') == 98){
                        $('#loan_edit').show();
                        $('#loan_lock').show();
                        $('#loan_delete').show();
                        $('#send_for_approval_btn').show();
                        $('#loan_print').hide();
                    }
                    else if($('#table-to-print tr.selected').data('loan_status') == 100){
                        $('#loan_edit').hide();
                        $('#loan_lock').hide();
                        $('#loan_delete').hide();
                        $('#send_for_approval_btn').hide();
                        $('#loan_print').show();
                        $('#loan_paid').show();

                        $('#loan_id').val(id);
                    }
                    else{
                        $('#loan_edit').hide();
                        $('#loan_lock').hide();
                        $('#loan_delete').hide();
                        $('#send_for_approval_btn').hide();
                        $('#loan_print').show();
                    }
                }
                else {
                    $('#loan_edit').hide();
                    $('#loan_delete').hide();
                    $('#loan_lock').hide();
                    $('#loan_print').hide();
                    $('#send_for_approval_btn').hide();
                    $('#loan_paid').hide();
                }
            }

        });

        $("#loan_edit").on('click', function (e) {
            var id = selected[0];
            if (id.length === 0) {
                swalError("Please select an Item");
                return false;
            } else {
                window.location = '<?php echo URL::to('new-loan-entry');?>/' + id;
            }
        });

        $("#loan_delete").on('click', function (e) {
            var loan_id = selected;
            if (loan_id.length === 0) {
                swalError("Please select an Item");
                return false;
            } else {
                swalConfirm('to Confirm Delete?').then(function (e) {
                    if(e.value){
                        var url = "{{route('loan-delete')}}";
                        var data = {hr_emp_loan_id:loan_id};
                        makeAjaxPost(data,url,null).then(function(response) {
                            var redirect_url = "{{route('loan-list')}}";
                            swalRedirect(redirect_url,'Delete Successfully','success');
                        });
                    }
                });

            }
        });

        $("#loan_lock").on('click', function (e) {
            var loan_id = selected;
            if (loan_id.length === 0) {
                swalError("Please select an Item");
                return false;
            } else {
                swalConfirm('to Lock this item?').then(function (e) {
                    if(e.value){
                        var url = "{{route('loan-lock')}}";
                        var data = {hr_emp_loan_id:loan_id};
                        makeAjaxPost(data,url,null).then(function(response) {
                            var redirect_url = "{{route('loan-list')}}";
                            swalRedirect(redirect_url,'Lock Successfully','success');
                        });
                    }
                });

            }
        });

        $("#loan_print").on('click', function (e) {
            var loan_id = selected;
            if (loan_id.length === 0) {
                swalError("Please select an Item");
                return false;
            } else {
                swalConfirm('to print this item?').then(function (e) {
                    if(e.value){
                        var url = "{{route('loan-print')}}/"+selected[0];
                        window.open(url, '_blank');
                    }
                });

            }
        });

        //Send for approval
        $(document).on('click', '#send_for_approval_btn', function (e) {
            e.preventDefault();
            var id_slug = 'hr_loan';
            var url = '<?php echo URL::to('loan-delegation-process');?>';
            if(selected.length){
                swalConfirm().then(function (e) {
                    if(e.value){
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {slug:id_slug,code:selected,'delegation_type':'send_for_approval'},
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

        $(document).on('click', '#loanPaidSubmit', function (e) {
            e.preventDefault();

            var amount = $('#amount').val();

            if(amount.length > 0 ){
                swalConfirm('to Confirm Paid Loan?').then(function (e) {
                    if (e.value) {
                        var url = "{{URL::to('store-paid-loan')}}";
                        var $form = $('#loanPaidForm');
                        var data = {};
                        var redirectUrl = '{{ URL::current()}}';
                        data = $form.serialize() + '&' + $.param(data);
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.code == 200) {

                                swalRedirect(redirectUrl, response.msg, 'success');

                            }
                            else {
                                swalRedirect(redirectUrl, response.msg, 'error');
                            }
                        });
                    }
                });
            }
            else{
                swalError("Please Fill Up Mendatory Field!");
            }
        });
    </script>
@endsection