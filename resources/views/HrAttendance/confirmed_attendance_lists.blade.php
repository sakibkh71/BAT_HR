@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <link href="{{asset('public/css/plugins/clockpicker/clockpicker.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/clockpicker/clockpicker.js')}}"></script>
    <script src="{{asset('public/js/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('public/js/plugins/bootstrap_toggle/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('public/js/bootstrap-checkbox.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/plugins/bootstrap_toggle/bootstrap-toggle.min.css')}}">
    <style>
        table.dataTable{
            border-collapse: collapse !important;
        }
        .locked{
            background: #ffa3a3 !important;
            color: #fff;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{$title}}</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('approved-attendance-list')}}" method="post" id="approved-attendance-search-form">
                            @csrf

                            <div class="row">
                                {!! __getCustomSearch('approved-attendance', $posted) !!}
                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> {{__lang('Filter')}}</button>
                                    <a class="btn btn-warning btn-xs btn" href="{{url('approved-attendance-list')}}"><i class="fa fa-resolving"></i> {{__lang('Reset')}}</a>
                                    <a href="javascript:;" class="btn btn-info btn-xs btn" id="locket_selected_attendance_histroy"><i class="fa fa-lock"></i> {{__lang('Locked')}}</a>
                                    <button type="button" class="btn btn-info btn-xs no-display" id="edit-attendance"><i class="fa fa-pencil" aria-hidden="true"></i> {{__lang('Edit')}}</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="checkbox-clickable table table-striped table-bordered table-hover emp-attendance-list">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">{{__lang('SL')}}#</th>
                                        <th rowspan="2">{{__lang('User Code')}}</th>
                                        <th rowspan="2">{{__lang('Category')}}</th>
                                        <th rowspan="2">{{__lang('Shift')}}</th>
                                        <th rowspan="2">{{__lang('Section')}}</th>
                                        <th rowspan="2">{{__lang('status')}}</th>
                                        <th rowspan="2">{{__lang('Day')}}</th>
                                        <th rowspan="2">{{__lang('Start Time')}}</th>
                                        <th rowspan="2">{{__lang('End Time')}}</th>
                                        <th rowspan="2" style="text-align: right;">{{__lang('Break Time')}}</th>
                                        <th rowspan="2" style="text-align: right;">{{__lang('Over Time')}}</th>
                                        <th rowspan="2" style="text-align: right;">{{__lang('Total Working Time')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($confirmed_attendance_history))
                                        @foreach($confirmed_attendance_history as $i=>$value)
                                            <tr @if($value->approved_status !='locked') class="item" @else class="locked" @endif
                                                hr_emp_attendance_id="{{$value->hr_emp_attendance_id}}" approved_status="{{$value->approved_status}}"  code="{{$value->user_code}}" day_is="{{$value->day_is}}">
                                                <td align="center">
                                                    {{($i+1)}}
                                                </td>
                                                <td>{{!empty($value->user_code)?$value->user_code:'N/A'}}</td>
                                                <td>{{!empty($value->hr_emp_category_name)?$value->hr_emp_category_name:'N/A'}}</td>
                                                <td>{{!empty($value->shift_name)?$value->shift_name:'N/A'}}</td>
                                                <td>{{!empty($value->hr_emp_section_name)?$value->hr_emp_section_name:'N/A'}}</td>
                                                <td>{{!empty($value->daily_status)?$value->daily_status:N/A}}</td>
                                                <td>{{!empty($value->day_is)?$value->day_is:'N/A'}}</td>
                                                <td>{{!empty($value->in_time)?$value->in_time:"N/A"}}</td>
                                                <td>{{!empty($value->out_time)?$value->out_time:'N/A'}}</td>
                                                <td class="text-right">{{!empty($value->break_time)?$value->break_time:'N/A'}}</td>
                                                <td class="text-right">{{!empty($value->ot_hours)?$value->ot_hours:'N/A'}}</td>
                                                <td class="text-right">{{!empty($value->total_work_time)?$value->total_work_time:'N/A'}}</td>
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
    </div>


    <div class="modal inmodal fade" id="attendance_edit_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <h4 class="modal-title">{{__lang('Attendance History Update')}}</h4>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-attendance-history')}}" id="update-attendance-form" method="post">
                        <input type="hidden" class="get_id" value="" name="get_id">
                        @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('User Code')}} </strong><span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <div class="input-group date">
                                        <input type="text" readonly="readonly" name="user_code" id="user_code" class="form-control" value="" required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('Start Date')}} </strong><span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="date" name="start_date" id="start_date" class="form-control" data-error="Start date is required" value="" required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('Start Time')}} </strong> <span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <div class="input-group clockpicker">
                                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                        <input type="text" name="start_time" id="start_time" class="form-control" data-error="Start Time is required" value="{{ !empty($employee->start_time)?$employee->start_time:'09:00'}}" required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('End Date')}} </strong><span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="date" name="end_date" id="end_date" class="form-control" data-error="End date is required" value="" required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('End Time')}} </strong> <span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <div class="input-group clockpicker">
                                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                        <input type="text" name="end_time" id="end_time" class="form-control" data-error="End Time is required" value="" required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('Break Time')}} </strong> <span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <input type="number" style="text-align: left;" class="form-control" value="" name="break_time" id="break_time">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('Over Time')}} </strong> <span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <input type="number" style="text-align: left;" class="form-control" value="" name="over_time" id="over_time">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('Status')}}</strong> <span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <select name="approved_status" class="form-control" id="approved_status">
                                        <option value="unlocked">{{__lang('Unlocked')}}</option>
                                        <option value="locked">{{__lang('Locked')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 font-normal"><strong>{{__lang('Is Salary Enabled')}}? </strong> <span class="required">*</span></label>
                                <div class="col-sm-12">
                                    <input type="hidden" name="is_salary_enabled" value="0" />
                                    <input type="checkbox" style="display: none;" checked="checked" id="is_salary_enabled" data-group-cls="btn-group-justified" name="is_salary_enabled" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{__lang('Close')}}</button>
                    <button type="button" class="btn btn-info pull-left" id="confirm_edit">{{__lang('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .clockpicker-popover {
            z-index: 999999 !important;
        }

        .modal-position {
            position: fixed;
            left: 50% !important;
            top: 50% !important;
            transform: translate(-50%, -50%);
            overflow: hidden;
        }
    </style>

    <script>

        $(function ($) {
            //Date Range Picker
            $('#date_range').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply: true,
            });
        });

        var selected_row = [];
        $(document).ready(function(){
            $('.emp-attendance-list').dataTable();
        });

        //Attendance Item Lock
        $(document).on("click","#locket_selected_attendance_histroy",function () {
            Ladda.bind(this);
            var load = $(this).ladda();

            var date_range = $('#date_range').val();

            if(date_range !=''){
                date_range = 'Date Range: '+$('#date_range').val()+',';
            }
            var sys_users = $('#sys_users option:selected').text();

            if(sys_users !=''){
                sys_users = 'User: '+$('#sys_users option:selected').text()+',';
            }
            var hr_emp_categorys = $('#hr_emp_categorys option:selected').text();

            if(hr_emp_categorys !=''){
                hr_emp_categorys = 'Category: '+$('#hr_emp_categorys option:selected').text();
            }

            var selectItem = $( "tr.item.selected" ).map(function() {
                return $( this ).attr('hr_emp_attendance_id');
            }).get();

            if (selectItem.length > 0) {
                var data = {'hr_emp_attendance_ids':selectItem};
            }else{
                var data = $('#approved-attendance-search-form').serialize();
            }

            var _token = "{{ csrf_token() }}";
            var url = "{{URL::to('locked-selected-attendance-history')}}";
            var success_url = "{{URL::to('approved-attendance-list')}}";
            var swal_message = "Are you sure you want to Locked attendance under selection of "+date_range+sys_users+hr_emp_categorys;

            swalConfirm(swal_message).then(function (s) {
                if(s.value){
                    makeAjaxPostText(data, url, load).done(function(result){
                        if(result == "updated"){
                            swalRedirect(success_url,'Successfully Updated!','success')
                        }
                    });
                }
            });

        });

        $(document).on('click','.item',function (e) {
            $(this).toggleClass('selected');
            var id = $(this).attr('hr_emp_attendance_id');
            $('.get_id').val(id);
            if ($(this).hasClass( "selected" )){
                selected_row.push(id);
            }else{
                var index = selected_row.indexOf(id);
                selected_row.splice(index,1);
            }
            actionManager(selected_row);
        });

        $(document).on('click','#edit-attendance',function (e) {
            var id = $('tr.item.selected').attr('hr_emp_attendance_id');
            var LocStatus = $('tr.item.selected').attr('approved_status');

            if(id==undefined || id==''){
                swalError('Sorry! please select Attendance row');
            }else if(LocStatus =="locked"){
                swalError('Sorry! this item is locked');
            }else{
                window.location.replace('{{URL::to('attendance-entry')}}/'+id);
            }

            /*Ladda.bind(this);
            var load = $(this).ladda();
            var _token ='{{ csrf_token() }}';
            var data = {'_token':_token,id:id};
            var url = "{{URL::to('get-hr-emp-attendance-details')}}";
            makeAjaxPost(data, url, load).done(function(result){
                console.log(result);
                if(result){
                    $('#start_time').val(result.start_time);
                    $('#start_date').val(result.start_date);
                    $('#end_date').val(result.end_date);
                    $('#end_time').val(result.end_time);
                    $('#break_time').val(result.break_time);
                    $('#user_code').val(result.user_code);
                    $('#over_time').val(result.ot_hours);
                    $('#approved_status').val(result.approved_status);
                }
                load.ladda('stop');
            });

            $('#attendance_edit_modal').modal('show');*/
        });

        function actionManager(selected_row){
            var LocStatus = $('tr.item.selected').attr('approved_status');

            if(selected_row.length < 1){
                $('#edit-attendance').fadeOut();
            }else if(selected_row.length == 1 && LocStatus !="locked"){
                $('#edit-attendance').fadeIn();
            }else{
                $('#edit-attendance').fadeOut();
            }
        }

        $(document).on('click','#confirm_edit',function (e) {
            Ladda.bind(this);
            var load = $(this).ladda();
            var id = $('.get_id').val();
            var _token = "{{ csrf_token() }}";
            var data = $('#update-attendance-form').serialize();
            var url = "{{URL::to('update-attendance-history')}}";
            var success_url = "{{URL::to('approved-attendance-list')}}";
            makeAjaxPostText(data, url, load).done(function(result){
                $('#attendance_edit_modal').modal('hide');
                location.reload(success_url);
            });

        });

        $('#makepdf').click(function () {
            var form = $('#dailyAttendanceSheetForm');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

    </script>
@endsection