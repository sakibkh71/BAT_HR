@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    {{csrf_field()}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h2>Leave Approval</h2>
                        <div class="ibox-tools">
                                <a class="d-none btn btn-primary btn-xs" id="all_approve" href="{{url('hr-leave-bulk-approved')}}">Approve</a>
                                <a class="d-none btn btn-primary btn-xs" id="all_decline" href="{{url('hr-leave-bulk-decline')}}">Decline</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="checkbox-clickable table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="no-sort"><input type="checkbox" name="bulk_approve" id="bulk_approve"></th>
                                    <th width="">Employee Name</th>
                                    <th width="">Application Type</th>
                                    <th width="">Leave Type</th>
                                    <th width="">Leave Date</th>
                                    <th width="">Days</th>
                                    <th width="">Applied Date</th>
                                    <th width="">Created By</th>
                                    <th width="">Created At</th>
                                    <th width="25%">Remarks</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($approval_list['results'])
                                    @foreach($approval_list['results'] as $emp)
                                        <tr id="{{$emp->hr_leave_records_id}}" class="row-select-toggle" data-code="{{$emp->hr_leave_records_id}}">
                                            <td align="center">
                                                <input type="checkbox" class="single_approve" data-code="{{$emp->hr_leave_records_id}}" value="{{$emp->hr_leave_records_id}}">
                                            </td>
                                            <td>{{$emp->name}}</td>
                                            <td>{{$emp->application_type}}</td>
                                            <td>{{$emp->leave_types}}</td>
                                            <td>{{toDated($emp->start_date)}} - {{toDated($emp->to_date)}}</td>
                                            <td class="text-center">{{$emp->leave_days}}</td>
                                            <td>{{toDated($emp->applied_date)}}</td>
                                            <td>{{getUserInfoFromId($emp->created_by)->name}}</td>
                                            <td>{{toDated($emp->created_at)}}</td>
                                            <td>{{$emp->remarks}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var selected_codes = [];
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });
            $('.dataTables-example').dataTable(
                {
                    "aaSorting": [[ 1, "desc" ]]
                }
            );

            $("#bulk_approve").change(function(){
                if(this.checked){
                    selected_codes = [];
                    $(".single_approve").each(function(){
                        this.checked=true;
                        selected_codes.push($(this).val());
                    })
                    $('#all_decline').removeClass('d-none').addClass('visible');
                    $('#all_approve').removeClass('d-none').addClass('visible');
                    $('.row-select-toggle').addClass('selected');
                }else{
                    $(".single_approve").each(function(){
                        this.checked=false;
                        selected_codes = [];
                    })
                    $('#all_approve').removeClass('visible').addClass('d-none')
                    $('#all_decline').removeClass('visible').addClass('d-none')
                    $('.row-select-toggle').removeClass('selected');
                }
            //     console.log(selected_codes);
            });

            $('#all_approve').on('click',function (e) {
                e.preventDefault();
                swalConfirm().then(function (e) {
                    if (e.value) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo URL::to('hr-leave-bulk-approved');?>',
                            data: {'code': selected_codes, 'delegation_type': 'approval'},
                            success: function (response) {
                                var all_smsg = '';
                                var count_smsg = 0;
                                var all_fmsg = '';
                                var count_fmsg = 0;
                                $.each(response.sucs_msg, function (key, value) {
                                    all_smsg += value + ", ";
                                    count_smsg++;
                                });
                                $.each(response.fail_msg, function (key, value) {
                                    all_fmsg += value + ", ";
                                    count_fmsg++;
                                });
                                var url = window.location;
                                swalRedirect(url, "" + count_smsg + " Successfully Approved and " + count_fmsg + " Failed " + all_fmsg, 'success');
                            }
                        });
                    }
                });
            });

            $('#all_decline').on('click',function (e) {
                e.preventDefault();
                swalConfirm().then(function (e) {
                    if (e.value) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo URL::to('hr-leave-bulk-decline');?>',
                            data: {'code': selected_codes, 'delegation_type': 'decline'},
                            success: function (response) {
                                var all_smsg = '';
                                var count_smsg = 0;
                                var all_fmsg = '';
                                var count_fmsg = 0;
                                $.each(response.sucs_msg, function (key, value) {
                                    all_smsg += value + ", ";
                                    count_smsg++;
                                });
                                $.each(response.fail_msg, function (key, value) {
                                    all_fmsg += value + ", ";
                                    count_fmsg++;
                                });
                                var url = window.location;
                                swalRedirect(url, "" + count_smsg + " Successfully Decline AND " + count_fmsg + " Failed " + all_fmsg, 'success');
                            }
                        });
                    }
                });
            });
            //Action when click on tr
            $(document).on('click','.row-select-toggle',function (e) {
                $obj = $(this);
                $obj.toggleClass('selected');
                var pr_code = $obj.data('code');

                if ($obj.hasClass( "selected" )){
                    $obj.find('input[type=checkbox]').prop( "checked", true );
                    selected_codes.push(pr_code);
                }else{
                    $obj.find('input[type=checkbox]').prop( "checked", false );
                    var index = selected_codes.indexOf(pr_code);
                    selected_codes.splice(index,1);
                }
                var rowCount = $('.row-select-toggle').length;

                if (rowCount == selected_codes.length && selected_codes.length >0) {
                    $("#bulk_approve").prop("checked", true);
                }else{
                    $("#bulk_approve").prop("checked", false);
                }

                if (selected_codes.length > 0){
                    $('#all_approve').removeClass('d-none').addClass('visible');
                    $('#all_decline').removeClass('d-none').addClass('visible');
                } else{
                    $('#all_decline').removeClass('visible').addClass('d-none');
                    $('#all_approve').removeClass('visible').addClass('d-none');
                }

                //console.log(selected_codes);
            });

        });

    </script>
@endsection
