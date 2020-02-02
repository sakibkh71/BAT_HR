@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <style>
        tr.Cancel{
            background: #ffd7d7 !important;
            pointer-events: none;
        }
    </style>
    <div class="wrapper wrapper-content animated showRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                            <h2><span class="font-bold employee_name">{{@$user_info->name}}</span> Leave History</h2>
                    </div>
                    <div class="ibox-content">
{{--                        @if(isset($user_info))--}}
{{--                        <div class="col-md-12 no-padding row">--}}
{{--                            <div class="col-md-5">--}}
{{--                                <span class="">Employe Mobile : &nbsp;<span class="font-bold employee_mobile">{{$user_info->mobile ?? '--'}}</span></span><br/>--}}
{{--                                <span class="">Designation : &nbsp;<span class="font-bold designation_name">{{$user_info->designations_name ?? '--'}}</span></span><br/>--}}
{{--                                <span class="">Department : &nbsp;<span class="font-bold department_name">{{$user_info->departments_name ?? '--'}}</span></span><br/>--}}
{{--                                <span class="">Branch : &nbsp;<span class="font-bold branch_name">{{$user_info->branchs_name ?? '--'}}</span></span><br/>--}}
{{--                                <span class="">Unit / Section : &nbsp;<span class="font-bold unit_name">{{$user_info->hr_emp_unit_name ?? '--'}}</span> / <span class="font-bold section_name">{{$user_info->hr_emp_section_name ?? '--'}}</span></span>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-7">--}}
{{--                                <table class="table table-striped table-bordered text-lefts">--}}
{{--                                    <thead>--}}
{{--                                    <tr>--}}
{{--                                        <th></th>--}}
{{--                                        <th>Casual</th>--}}
{{--                                        <th>Sick</th>--}}
{{--                                        <th>Earn</th>--}}
{{--                                        <th>Festival</th>--}}
{{--                                        <th>Without Pay</th>--}}
{{--                                        <th>Special</th>--}}
{{--                                        <th>Compensation</th>--}}
{{--                                        <th>Total</th>--}}
{{--                                    </tr>--}}
{{--                                    </thead>--}}
{{--                                    <tbody>--}}
{{--                                    <tr class="form-label">--}}
{{--                                        <td>TOTAL</td>--}}
{{--                                        <td class="total_leave" data-id="0" id="total_casual_leave">{{$leave_policy->casual_leave}}</td>--}}
{{--                                        <td class="total_leave" data-id="1" id="total_sick_leave">{{$leave_policy->sick_leave}}</td>--}}
{{--                                        <td class="total_leave" data-id="2" id="total_earn_leave">{{$leave_policy->earn_leave}}</td>--}}
{{--                                        <td class="total_leave" data-id="3" id="total_festival_leave">{{$leave_policy->festival_leave}}</td>--}}
{{--                                        <td class="total_leave" data-id="4" id="total_leave_without_pay">{{$leave_policy->leave_without_pay}}</td>--}}
{{--                                        <td class="total_leave" data-id="5" id="total_special_leave">{{$leave_policy->special_leave}}</td>--}}
{{--                                        <td class="total_leave" data-id="6" id="total_compansation_leave">{{$leave_policy->compansation_leave}}</td>--}}
{{--                                        <td id="total_leave">0</td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>ELAPSED</td>--}}
{{--                                        @php--}}
{{--                                            $casual_leave = 0;--}}
{{--                                            $compensation_leave = 0;--}}
{{--                                            $earn_leave = 0;--}}
{{--                                            $festival_leave = 0;--}}
{{--                                            $without_pay = 0;--}}
{{--                                            $special_leave = 0;--}}
{{--                                            $sick_leave = 0;--}}
{{--                                        @endphp--}}
{{--                                        @if(!empty($leave_records))--}}
{{--                                            @foreach($leave_records as $leave_record)--}}
{{--                                                @php($casual_leave += $leave_record->leave_types == 'Casual Leave' ? $leave_record->leave_days : 0)--}}
{{--                                                @php($sick_leave += $leave_record->leave_types == 'Sick Leave' ? $leave_record->leave_days : 0)--}}
{{--                                                @php($special_leave += $leave_record->leave_types == 'Special Leave' ? $leave_record->leave_days : 0)--}}
{{--                                                @php($without_pay += $leave_record->leave_types == 'Leave Without Pay' ? $leave_record->leave_days : 0)--}}
{{--                                                @php($earn_leave += $leave_record->leave_types == 'Earn Leave' ? $leave_record->leave_days : 0)--}}
{{--                                                @php($festival_leave += $leave_record->leave_types == 'Festival Leave' ? $leave_record->leave_days : 0)--}}
{{--                                                @php($compensation_leave += $leave_record->leave_types == 'Compensation Leave' ? $leave_record->leave_days : 0)--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                        <td class="elps_leave" data-id="0" id="elps_casual_leave">{{$casual_leave}}</td>--}}
{{--                                        <td class="elps_leave" data-id="1" id="elps_sick_leave">{{$sick_leave}}</td>--}}
{{--                                        <td class="elps_leave" data-id="2" id="elps_earn_leave">{{$earn_leave}}</td>--}}
{{--                                        <td class="elps_leave" data-id="3" id="elps_festival_leave">{{$festival_leave}}</td>--}}
{{--                                        <td class="elps_leave" data-id="4" id="elps_leave_without_pay">{{$without_pay}}</td>--}}
{{--                                        <td class="elps_leave" data-id="5" id="elps_special_leave">{{$special_leave}}</td>--}}
{{--                                        <td class="elps_leave" data-id="6" id="elps_compansation_leave">{{$compensation_leave}}</td>--}}
{{--                                        <td id="elps_leave_total">0</td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>REMAIN</td>--}}
{{--                                        <td class="rmn_leave" data-id="0" id="rmn_casual_leave">0</td>--}}
{{--                                        <td class="rmn_leave" data-id="1" id="rmn_sick_leave">0</td>--}}
{{--                                        <td class="rmn_leave" data-id="2" id="rmn_earn_leave">0</td>--}}
{{--                                        <td class="rmn_leave" data-id="3" id="rmn_festival_leave">0</td>--}}
{{--                                        <td class="rmn_leave" data-id="4" id="rmn_leave_without_pay">0</td>--}}
{{--                                        <td class="rmn_leave" data-id="5" id="rmn_special_leave">0</td>--}}
{{--                                        <td class="rmn_leave" data-id="6" id="rmn_compansation_leave">0</td>--}}
{{--                                        <td id="rmn_total_leave">0</td>--}}
{{--                                    </tr>--}}

{{--                                    </tbody>--}}
{{--                                </table>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @endif--}}
                            <form action="{{route('get-emp-leave-history')}}" method="post" class="ptype-form">
                                @csrf
                                <div class="row">
                                    {!! __getCustomSearch('leave-history', $posted) !!}
                                    <div class="col-md-3"> <label class="col-sm-12 col-form-label"></label>
                                        <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Filter</button>
                                        <button type="button" id="exportExcel" class="btn btn-info "><i class="fa fa-file-excel-o"></i> Excel</button>
                                    </div>

                                </div>
                            </form>

                            <div id="toolbar" class="pull-right" style="padding-bottom: 5px; height: 40px">
{{--                                <a href="{{route('leave-entry')}}" class="btn btn-success btn-xs" id="new-item"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add</a>--}}
                                <button class="btn btn-warning btn-xs no-display" id="view-item"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                                <button class="btn btn-primary btn-xs send_for_approval_leave" style="display: none" id_slug="hr_leave"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                                <button class="btn btn-warning btn-xs no-display" id="view-leave-report"><i class="fa fa-view" aria-hidden="true"></i> View Leave Report</button>
                                <button class="btn btn-danger btn-xs item_delete_leave" style="display: none"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                                <button class="btn btn-default btn-xs cancel_leave" style="display: none"><i class="fa fa-trash" aria-hidden="true"></i> Cancel</button>
                                @if(isset($user_info))
                                    <a href="<?php echo URL::to('leave-entry', 'u-'.$user_info->user_code)?>" class="btn btn-info btn-xs" id="view-item"><i class="fa fa-plus" aria-hidden="true"></i> New Application</a>
                                @endif
                            </div>
                            <div class="table-responsive">

                                <table data-toolbar="#toolbar"  class="checkbox-clickable table table-striped table-bordered table-hover emp-leave-list">
                                    <thead>
                                    <tr>
                                        <th width="1%">#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Applied</th>
                                        <th>Leave Type</th>
                                        <th>Leave Date</th>
                                        <th>Days</th>
                                        <th>Applied Date</th>
                                        <th>Approved Date</th>
                                        <th width="15%">Remarks</th>
                                        <th>Created By</th>
                                        <th>Delegation Step</th>
                                        <th>Delegation Person</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($leave_records))
                                        @foreach($leave_records as $i=>$leave_record)
                                            <tr class="row-select-toggle {{ isset($leave_record->status_flows_name)?$leave_record->status_flows_name:'' }}" data-id="{{$leave_record->hr_leave_records_id}}" data-todate="{{$leave_record->to_date}}" data-status="{{$leave_record->leave_status}}">
                                                <td class="text-center">{{($i+1)}}</td>
                                                <td>{{$leave_record->name}}</td>
                                                <td>{{$leave_record->user_code}}</td>
                                                <td>{{$leave_record->application_type}}</td>
                                                <td>{{$leave_record->leave_types}}</td>
                                                <td>{{toDated($leave_record->start_date)}} - {{toDated($leave_record->to_date)}}</td>
                                                <td class="text-center">{{$leave_record->leave_days}}</td>
                                                <td>{{toDated($leave_record->applied_date)}}</td>
                                                <td>{{$leave_record->approval_date?toDated($leave_record->approval_date):'N/A'}}</td>
                                                <td>{{$leave_record->remarks}}</td>
                                                <td>{{$leave_record->creator_name}}</td>
                                                <td>{{$leave_record->step_name}}</td>
                                                <td>{{$leave_record->delegation_person_name}}</td>
                                                <td>{{$leave_record->status_flows_name}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                <div class="modal fade" id="leaveReportModal" tabindex="-1" role="dialog"  aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content" id="leaveReportContent">

                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var selected_row = [];
        var send_leave = [];

        $(document).ready(function(){
            $('.emp-leave-list').dataTable();

            var total_leave = 0;
            $('.total_leave').each(function(){
                total_leave += parseInt($(this).text());
            });

            $('#total_leave').text(total_leave);
            var elapsed_total_leave = 0;
            $('.elps_leave').each(function(){
                elapsed_total_leave += parseInt($(this).text());
            });
            $('#elps_leave_total').text(elapsed_total_leave);
            remainLeaveDays();
        });

        $('#exportExcel').click(function () {
            var $form = $('.ptype-form');
            var data={};
            data = $form.serialize() + '&' + $.param(data);
            var url='{{route('get-emp-leave-history')}}/excel';

            $.ajax({
                type:'get',
                url:url,
                data:data,
                success:function (data) {
                    console.log(data);
                    window.location.href = './public/export/' + data.file;
                    swalSuccess('Export Successfully');
                }
            });
        });

        function remainLeaveDays() {
            $('.rmn_leave').each(function(n){
                var total_selector = '.total_leave[data-id='+n+']';
                var elapsed_selector = '.elps_leave[data-id='+n+']';
                var remain_days = parseInt($(total_selector).text()) - parseInt($(elapsed_selector).text());
                var total_remain_days = parseInt($('#total_leave').text()) - parseInt($('#elps_leave_total').text());
                $(this).text(remain_days);
                $('#rmn_total_leave').text(total_remain_days);
            });
        }

        $(document).on('click','.row-select-toggle',function (e) {
            $(this).toggleClass('selected');

            $obj = $(this);
            var id = $(this).data('id');

            if ($(this).hasClass( "selected" )){
                selected_row.push(id);
                send_leave.push($obj.data('status'));
            }else{
                var index = selected_row.indexOf(id);
                selected_row.splice(index,1);
                send_leave.splice($.inArray($obj.data('status'), send_leave), 1);
            }

            actionManager(selected_row);

            if(send_leave.includes(63)||send_leave.includes(64)){
                $('.send_for_approval_leave, .item_delete_leave, #view-item').hide();
            }else{
                $('.send_for_approval_leave, .item_delete_leave').show();
            }
            if(selected_row.length==0){
                $('.send_for_approval_leave, .item_delete_leave, #view-item,#view-leave-report').hide();
            }

            if(selected_row.length > 0){
                $('.cancel_leave').show();
            }else{
                $('.cancel_leave').hide();
            }

        });
        $('#view-leave-report').click(function () {
            var url = '<?php echo URL::to('get-emp-leave-report');?>';

            var data = {
                'leave_record_id':selected_row[0]
            };
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#leaveReportContent').html(response);
                }

                $('#leaveReportModal').modal('show');
            });
        });
        $('#view-item').on('click', function () {
            var view_url = "<?php echo URL::to('leave-entry')?>/l-"+selected_row[0];
            window.location.replace(view_url);
        });
        function actionManager(selected_row){
            if(selected_row.length < 1){
                $('#view-item').hide();
                $('.send_for_approval_leave, .item_delete_leave,#view-leave-report').hide();
                /*----no selection action-----*/
            }else if(selected_row.length == 1){
                $('#view-item').show();
                $('#view-leave-report').show();
                $('.send_for_approval_leave, .item_delete_leave').show();
            }else{
                $('#view-item').hide();
                $('#view-leave-report').hide();
                $('.send_for_approval_leave, .item_delete_leave').show();
            }
        }


        $(document).on('click', '.item_delete_leave', function (e) {
            e.preventDefault();
            var record_id = selected_row.toString();
            var data = {record_id:record_id};
            var url = '<?php echo URL::to('hr-leave-record-delete');?>';
            if(record_id.length) {
                swalConfirm("Delete Selected Items").then(function (e) {
                    if (e.value) {
                        makeAjaxPost(data,url,null).done(function (response) {
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


        //Cancel Leave
        $(document).on('click', '.cancel_leave', function (e) {
            e.preventDefault();
            var record_id = selected_row.toString();
            var data = {record_id:record_id};
            var url = '<?php echo URL::to('hr-leave-record-cancel');?>';
            if(record_id.length) {
                swalConfirm("Cancel Selected Leave").then(function (e) {
                    if (e.value) {
                        makeAjaxPost(data,url,null).done(function (response) {
                            var url2 = window.location;
                            if(response.success){
                                swalRedirect(url2,"Successfully Canceled",'success');
                            }else{
                                swalWarning('Operation Failed!');
                            }
                        });
                    }else{
                        load.ladda('stop');
                    }
                });

            }else{
                swalWarning("Please select at least one Leave!");
            }

        });

        //Send for approval
        $(document).on('click', '.send_for_approval_leave', function (e) {
            e.preventDefault();
            var id_slug = $(this).attr('id_slug');
            var job_value = [];
            var url = '<?php echo URL::to('go-to-leave-delegation-process');?>';

            var job_value = selected_row;
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
