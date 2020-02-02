@extends('layouts.app')
@section('content')
    {{--{{dd(Session::all())}}--}}
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Salary Deduction</h3>
                    </div>
                    <div class="ibox-tools">
                        {{--<button class="btn btn-xs" id="show-custom-search"><i class="fa fa-search"></i> show search</button>--}}
                        <button class="btn btn-success btn-xs no-display" id="deduction_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="btn btn-primary btn-xs no-display" id="send_for_approval_btn"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                        <button class="btn btn-danger btn-xs no-display" id="deduction_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        <a href="{{route('new-deduction-entry')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus-circle"></i> New Loan Entry</a>
                    </div>
                    <div class="ibox-content">
                        {!! __getMasterGrid('deduction_employee_list') !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        var selected = [];
        var locstatus =[];
        var deduction_status =[];
        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            var lcs = self.data('lock_status');
            var ls = self.data('deduction_status');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    selected.push(id);
                    locstatus.push(lcs);
                    deduction_status.push(ls);
                } else {
                    selected.splice(selected.indexOf(id), 1);
                    locstatus.splice(locstatus.indexOf(lcs), 1);
                    deduction_status.splice(locstatus.indexOf(ls), 1);
                }

                var arr_length = selected.length;
                if (arr_length > 1) {
                    $('#deduction_edit').hide();
                    if(locstatus.includes(1)){
                        $('#deduction_delete').hide();
                        $('#send_for_approval_btn').hide();
                    }else if(!deduction_status.includes(111) && !deduction_status.includes(112)){
                        $('#deduction_delete').show();
                        $('#send_for_approval_btn').show();
                    }else{
                        $('#deduction_delete').hide();
                        $('#send_for_approval_btn').hide();
                    }
                }
                else if (arr_length == 1) {
                    if ($('#table-to-print tr.selected').data('deduction_status') == 110){
                        $('#deduction_edit').show();
                        $('#deduction_delete').show();
                        $('#send_for_approval_btn').show();
                    }else{
                        $('#deduction_edit').hide();
                        $('#deduction_delete').hide();
                        $('#send_for_approval_btn').hide();
                    }
                }
                else {
                    $('#deduction_edit').hide();
                    $('#deduction_delete').hide();
                    $('#send_for_approval_btn').hide();
                }
            }

        });

        $("#deduction_edit").on('click', function (e) {
            var id = selected[0];
            if (id.length === 0) {
                swalError("Please select an Item");
                return false;
            } else {
                window.location = '<?php echo URL::to('new-deduction-entry');?>/' + id;
            }
        });

        $("#deduction_delete").on('click', function (e) {
            var deduction_id = selected;
            if (deduction_id.length === 0) {
                swalError("Please select an Item");
                return false;
            } else {
                swalConfirm('to Confirm Delete?').then(function (e) {
                    if(e.value){
                        var url = "{{route('deduction-delete')}}";
                        var data = {hr_emp_deduction_id:deduction_id};
                        makeAjaxPost(data,url,null).then(function(response) {
                            var redirect_url = "{{route('deduction-list')}}";
                            swalRedirect(redirect_url,'Delete Successfully','success');
                        });
                    }
                });

            }
        });

        //Send for approval
        $(document).on('click', '#send_for_approval_btn', function (e) {
            e.preventDefault();
            var id_slug = 'hr_deducti';
            var url = '<?php echo URL::to('deduction-delegation-process');?>';
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
    </script>
@endsection