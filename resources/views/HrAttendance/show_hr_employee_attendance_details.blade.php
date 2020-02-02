@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                <div class="col-lg-12 no-padding">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h2>{{$title}}</h2>
                            @if(!empty($info))
                            <div class="ibox-tools">
                                <button class="btn btn-info btn-xs" id="draft_attendance_process"><i class="fa fa-check" aria-hidden="true"></i>Regular Process</button>
                                <button class="btn btn-success btn-xs" id="all_draft_attendance_process"><i class="fa fa-check" aria-hidden="true"></i>Force Process</button>
                            </div>
                            @endif
                        </div>
                        <div class="ibox-content">
                            <div class="col-md-12">
                                <div class="table-responsive">
                            <table class="checkbox-clickable table table-striped table-bordered table-hover emp-attendance-list">
                                <thead>
                                <tr>
                                    <th rowspan="2">{{__lang('SL')}}#</th>
                                    <th rowspan="2">{{__lang('Employee Name')}}</th>
                                    <th rowspan="2">{{__lang('Status')}}</th>
                                    <th rowspan="2">{{__lang('Day')}}</th>
                                    <th rowspan="2">{{__lang('In Time')}}</th>
                                    <th rowspan="2">{{__lang('Out Time')}}</th>
                                    <th rowspan="2">{{__lang('Break Time')}}</th>
                                    <th rowspan="2">{{__lang('Over Time')}}</th>
                                    <th rowspan="2">{{__lang('Total Working Time')}}</th>
                                    <th rowspan="2">{{__lang('Duplicate')}}</th>
                                    <th rowspan="2">{{__lang('Approved Status')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($info))
                                    @foreach($info as $i=>$value)
                                        <tr class="item" <?php if($value->duplicate != NULL){ ?> style="background-color:#ff001a ;color:white;" <?php } ?>  code="{{$value->user_code}}" day_is="{{$value->day_is}}" in_time="{{$value->in_time}}">
                                            <td align="center">
                                                {{($i+1)}}
                                            </td>
                                            <td>{{ $value->name ?? 'N/A' }} {{ !empty($value->user_code)? '( '. $value->user_code .' )':'N/A'}}</td>
                                            <td>{{$value->daily_status ?? 'N/A'}}</td>
                                            <td>{{ !empty($value->day_is)?toDated($value->day_is):'N/A' }}</td>
                                            <td>{{ $value->in_time !=null ? (date('Y-m-d',strtotime($value->in_time)) == $value->day_is? date('h:i:s A',strtotime($value->in_time)): toDateTimed($value->in_time)):'Null'}}</td>
                                            <td>{{ $value->out_time !=null ? (date('Y-m-d',strtotime($value->out_time)) == $value->day_is? date('h:i:s A',strtotime($value->out_time)): toDateTimed($value->out_time)):'Null'}}</td>
                                            <td>{{$value->break_time}}</td>
                                            <td>{{$value->ot_hours}}</td>
                                            <td>{{$value->total_work_time}}</td>
                                            <td  align="center" <?php if($value->duplicate != NULL){ ?> style="color: #fff;" <?php } ?>>{{$value->duplicate?'Yes':'No'}}</td>
                                            <td <?php if($value->approved_status == 'locked'){ ?> style="color: #8cce3b;" <?php } ?>>{{$value->approved_status?$value->approved_status:'unlocked'}}</td>
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

    <!-- Modal For Open Existing Attendance -->
    <div id="open_existing_attendance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" id="existing_attendance" style="width: 980px;">
            <div class="modal-content" style="overflow: hidden; padding-bottom: 15px;">
                <div class="modal-header">
                    <span style="overflow: hidden !important;">
                        <h3 class="modal-title pull-left" style="color: red;">Existing Confirmed Attendance List</h3>
                        <div class="pull-right">
                            <button style="margin-left: 20px;" class="btn btn-primary btn-xs" id="close_modal"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Back</button>
                        </div>
                    </span>
                </div>
                <div class="modal-body show_existing_attendance" style="overflow: hidden;">
                </div>
            </div>
        </div>
    </div>
<script>
    var selected_temp_attendance = [];
    $(document).on('click','.attendance',function (e) {
        $obj = $(this);
        if(!$(this).attr('code')){
            return true;
        }
        $obj.toggleClass('selected');
        var id = $obj.attr('code');
        if ($obj.hasClass("selected")){
            $obj.find('input[type=checkbox]').prop( "checked", true );
            selected_temp_attendance.push(id);
        }else{
            $obj.find('input[type=checkbox]').prop( "checked", false );
            var index = selected_temp_attendance.indexOf(id);
            selected_temp_attendance.splice(index,1);
        }
        actionManager(selected_temp_attendance);
    });

    $('#close_modal').on('click', function () {
        swalConfirm("Please Prepare Your Excel/CSV with valid Data").then((e) => {
            if(e.value){
                $('#open_existing_attendance').modal("hide");
            }
        });
    });


    $('#draft_attendance_process').on('click', function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var action_type = "process";
        var data = {action_type:action_type,"_token":"{{ csrf_token() }}"};
        var url = "<?php echo URL::to('attendance-process')?>";
        swalConfirm("Are you sure you want to confirm employee attendance?").then((e) => {
            if(e.value){
                makeAjaxPostText(data,url,load).then((response) =>{
                    if(response){
                        swalRedirect('','Employee Attendance Information Successfully Stored!.');
                    }else{
                        swalError();
                    }
                });
            }
        });
    });

    $('#all_draft_attendance_process').on('click', function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var action_type = "force-process";
        var data = {action_type:action_type,"_token":"{{ csrf_token() }}"};
        var url = "<?php echo URL::to('attendance-process')?>";
        swalConfirm("Are you sure you want to confirm employee attendance?").then((e) => {
            if(e.value){
                makeAjaxPostText(data,url,load).then((response) =>{
                    if(response){
                        swalRedirect('','Employee Attendance Information Successfully Stored!.');
                    }else{
                        swalError();
                    }
                });
            }
        });
    });

    function actionManager(selected_temp_attendance){
        if(selected_temp_attendance.length < 1){
            $('#draft_attendance_process').fadeIn();
            $('#all_draft_attendance_process').fadeOut();
        }else if(selected_temp_attendance.length == 0){
            $('#all_draft_attendance_process').fadeIn();
            $('#draft_attendance_process').fadeOut();
        }else{
            $('#draft_attendance_process').fadeOut();
            $('#all_draft_attendance_process').fadeIn();
        }
    }
</script>
@endsection