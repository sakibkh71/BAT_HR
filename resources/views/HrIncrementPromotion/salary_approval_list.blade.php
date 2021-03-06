@extends('layouts.app')
@section('content')
    {{csrf_field()}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h2>Salary Approval</h2>
                        <div class="ibox-tools">
                                <a class="d-none btn btn-primary btn-xs all_approval" id="all_approve" href="{{url('hr-salary-bulk-approved')}}">All Approve</a>
                                <a class="d-none btn btn-primary btn-xs single_approval" id="all_approve" href="{{url('hr-salary-bulk-approved')}}">Approve</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="checkbox-clickable table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="no-sort"><input type="checkbox" name="bulk_approve" id="bulk_approve"></th>
                                    <th>Employee Names</th>

                                    <th>Designation</th>
                                    <th>Grade</th>
                                    <th>Applicable Date</th>
                                    <th>Increment/Promotion</th>
                                    <th>Current Gross Salary</th>
                                    <th>Increment Amount</th>
                                    <th>New Gross Salary</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($approval_list['results'])
                                    @foreach($approval_list['results'] as $emp)
                                        <tr id="{{$emp->hr_employee_record_logs_id}}" class="row-select-toggle" data-code="{{$emp->hr_employee_record_logs_id}}">
                                            <td align="center">
                                                <input type="checkbox" class="single_approve" data-code="{{$emp->hr_employee_record_logs_id}}" value="{{$emp->hr_employee_record_logs_id}}" data-status="{{ $emp->hr_log_status }}">
                                            </td>
                                            <td>{{$emp->name}}</td>
                                            <td>{{$emp->designations_name}}</td>
                                            <td>{{$emp->hr_emp_grade_name}}</td>
                                            <td>{{toDated($emp->applicable_date)}}</td>
                                            <td>{{ucwords(str_replace('_',' ',$emp->record_type))}}</td>
                                            <td class="text-right">{{number_format($emp->previous_gross,2)}}</td>
                                            <td class="text-right">{{number_format($emp->gross_salary-$emp->previous_gross,2)}}</td>
                                            <td class="text-right">{{number_format($emp->gross_salary,2)}}</td>
                                            <td class="text-right">{{getUserInfoFromId($emp->created_by)->name}}</td>
                                            <td class="text-right">{{toDated($emp->created_at)}}</td>
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
                    });
                    $('.single_approval').removeClass('d-none').addClass('visible');
                    $('.all_approval').removeClass('d-none').addClass('visible');
                    $('.single_approval').removeClass('visible').addClass('d-none');
                    $('#prq_approve').addClass('d-none');
                    $('.row-select-toggle').addClass('selected');
                }else{
                    $(".single_approve").each(function(){
                        this.checked=false;
                        selected_codes = [];
                    });
                    $('#all_approve').removeClass('visible').addClass('d-none')
                    $('#prq_approve').removeClass('d-none').addClass('visible');
                    $('.row-select-toggle').removeClass('selected');
                }
            });

            $(document).on('click','#all_approve',function (e) {               
                e.preventDefault();
                swalConfirm().then(function (e) {
                    if (e.value) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo URL::to('hr-salary-bulk-approved');?>',
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
                                swalRedirect(url, "" + count_smsg + " Successfully Approved AND " + count_fmsg + " Failed " + all_fmsg, 'success');
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

                if(selected_codes.length == 1){
                    $('.single_approval').removeClass('d-none').addClass('visible');
                    $('.all_approval').removeClass('visible').addClass('d-none');
                    $('#prq_approve').addClass('d-none');
                }
                else if (selected_codes.length > 1){
                    $('.all_approval').removeClass('d-none').addClass('visible');
                    $('.single_approval').removeClass('visible').addClass('d-none');
                    $('#prq_approve').addClass('d-none');
                }else{
                    $('.all_approve').removeClass('visible').addClass('d-none');
                    $('.single_approval').removeClass('visible').addClass('d-none');
                    $('#prq_approve').removeClass('d-none').addClass('visible');
                }
            });
        });
    </script>
@endsection
