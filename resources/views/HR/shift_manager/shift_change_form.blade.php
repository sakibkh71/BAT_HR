@extends('layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{asset('public/css/plugins/fullcalendar/fullcalendar.css')}}"/>
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Employee Working Shift Configure</h2>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12">

                                <form action="{{route('worker-shift-configure')}}" method="post" class="ptype-form">
                                    @csrf
                                    <div class="row">
                                        {!! __getCustomSearch('worker-shift-configure', @$posted) !!}
                                        <div class="col-md-3">
                                            <label class="form-label">Date</label>
                                            <div class="input-group calendar_day">
                                                <input type="text" required
                                                       placeholder=""
                                                       class="form-control"
                                                       value="{{ isset($calendar_day)?@$calendar_day:date('Y-m-d')}}"
                                                       id="calendar_day" name="calendar_day"/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Shift</label>
                                            <div class="input-group">
                                                @php
                                                    $shiftcombo = array(
                                                        'selected_value' => $shifted,
                                                        'attributes' => array('class'=>'form-control','required' => 'required', 'id'=>'switch-shift')
                                                    )
                                                @endphp
                                                {{__combo('calendar_working_shift', $shiftcombo)}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Month</label>
                                            <div class="input-group config_month">
                                                <input type="text" required
                                                       placeholder=""
                                                       class="form-control"
                                                       value="{{ isset($config_month)?@$config_month:date('Y-m')}}"
                                                       id="config_month" name="config_month"/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 pt-4">
                                            <button type="submit" class="btn btn-primary">
                                                Configure
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>


                    </div>
                    @if($shifted!='')
                        @php($total_worker = $emp_ids==''?0:count(explode(',',$emp_ids)))
                    @if($total_worker>0)
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div id="external-events">
                                        <div id="event_area">
                                            <h5>Drag a Shift and drop into calendar.</h5>
                                            <span style="color: #9f191f;"> previous shift will change by new shift if exists.</span>
                                            @if(isset($shiftList))
                                                @php($color_class=['bg-success','bg-primary','bg-danger','bg-warning'])
                                                @foreach($shiftList as $i=>$shift)
                                                    <div style="background-color: {{$shift->bg_color}} " class="external-event ui-draggable ui-draggable-handle">{{$shift->shift_name}}</div>
                                                @endforeach
                                            @endif
                                            <hr>
                                            <div class="external-event bg-warning ui-draggable ui-draggable-handle">
                                                Weekend
                                            </div>
                                            <div class="external-event bg-danger ui-draggable ui-draggable-handle">
                                                Holiday
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <hr>
                                        <h3>{{$total_worker}} workers found</h3>
                                        <hr>
                                        <div class="scrollbox" style="height: 500px; overflow: scroll">
                                            <h4>Worker List </h4>
                                            <button type="button" class="label btn btn-success btn-xs" id="selectAll">Select All</button>
                                            <button type="button" class="label btn btn-danger btn-xs pull-right"  id="UnselectAll">Unselect All</button>
                                            <p></p>
                                            @if(@$employeeList)
                                                <table class="table-striped  table table-bordered">
                                                    <tbody>
                                                    @foreach($employeeList as $list)
                                                        <tr class="row-select-toggle selected" data-id="{{$list->id}}"><td>{{$list->name}}({{$list->user_code}})</td></tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>

                                    </div>

                                </div>
                                <div class="col-lg-9">
                                    <div id="calendar" class="fc fc-unthemed fc-ltr"></div>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="col-lg-12">
                                <h3 class="bg-warning h3">No Worker found! please select valid date and shift.</h3>
                            </div>
                        @endif
                     @else
                        <div class="col-lg-12">
                            <div id="calendar" class="fc fc-unthemed fc-ltr"></div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <style>
        .closeon{
            background-color: red;
            color:#FFF;
            padding: 2px;
        }
    </style>
    <script src="{{asset('public/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/fullcalendar/fullcalendar.min.js')}}"></script>
    <script>
        var selected_emp = [];
         selected_emp = [{{@$emp_ids}}];
        $("#calendar_day").datetimepicker({
            format:'YYYY-MM-DD'
        });
        $("#config_month").datetimepicker({
            format:'YYYY-MM',
            minDate:new Date()
        });
        $(document).on('click','.row-select-toggle',function (e) {
            $(this).toggleClass('selected');
            $obj = $(this);
            var id = $(this).data('id');
            if ($(this).hasClass( "selected" )){
                selected_emp.push(id);
            }else{
                var index = selected_emp.indexOf(id);
                selected_emp.splice(index,1);
            }
        });
        $(document).on('click','#selectAll',function (e) {
            $('.row-select-toggle').addClass('selected');
            selected_emp = [{{@$emp_ids}}];
        });
        $(document).on('click','#UnselectAll',function (e) {
            $('.row-select-toggle').removeClass('selected');
            selected_emp = [];
        });
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $(document).ready(function () {

            var eventData = '';
            @if(empty($emp_ids))
                eventData = '<?php echo json_encode($eventData) ?>';
                @endif

           // console.log(JSON.parse(eventData));

            $('#external-events div.external-event').each(function () {
                var eventObject = {
                    title: $.trim($(this).text()),
                    // resourceId: parseInt($(this).attr('employee'))
                };
                $(this).data('eventObject', eventObject);

                $(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag

                });

            });

            /* initialize the calendar
             -----------------------------------------------------------------*/
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            $('#calendar').fullCalendar({
                defaultDate: moment('{{isset($config_month)?$config_month:date('Y-m')}}'),
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,resourceDay'
                },
                resources: [],
                events: eventData ? JSON.parse(eventData) : [],
                selectable: false,
                selectHelper: true,

                editable: false,
                droppable: true, // this allows things to be dropped onto the calendar !!!
                drop: function (date, allDay) {
                    var defaultDuration = moment.duration($('#calendar').fullCalendar('option', 'defaultTimedEventDuration'));
                    var end = date.clone().add(defaultDuration); // on drop we only have date given to us
                    var originalEventObject = $(this).data('eventObject');
                    var copiedEventObject = $.extend({}, originalEventObject);
                    var resourceID = Number(new Date(date));
                    copiedEventObject.resourceId = resourceID;
                    copiedEventObject.start = date;
                    copiedEventObject.allDay = allDay;
                    copiedEventObject.backgroundColor = $(this).css("background-color");
                    copiedEventObject.borderColor = $(this).css("border-color");
                    var start_date = date.format();
                    var end_date = end.format();
                    var event_title = copiedEventObject.title;
                    setCalendarDate(event_title, start_date, end_date);
                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                },
                viewRender:function(){
                    $(".fc-other-month.fc-day-number").html('');

                },
                eventOverlap: false,
                eventDragStop: function(event, jsEvent, ui, view) {

                },
                // eventClick: function (calEvent, jsEvent, view) {
                //    console.log(calEvent);
                //     $('#calendar').fullCalendar('removeEvents', calEvent._id);
                // },
                dayClick: function(date, jsEvent, view) {

                },
                @if(!empty($emp_ids))
                eventRender: function(event, element, view) {
                    if (view.name == 'listDay') {
                        element.find(".fc-list-item-time").append("<span class='closeon fa fa-trash'></span>");
                    } else {
                        element.find(".fc-content").prepend("<span class='closeon fa fa-trash'></span>");
                    }
                    element.find(".closeon").on('click', function() {
                        $('#calendar').fullCalendar('removeEvents',event._id);
                        // console.log('delete');
                    });
                }
                @endif

            });


        });
        function setCalendarDate(event_title, start_date, end_date) {
            var emp_ids = [];
            emp_ids = selected_emp;
            var data = {
                'event_title': event_title,
                'start_date': start_date,
                'end_date': end_date,
                'emp_ids': emp_ids,
            };
            var url = "<?php echo e(route('worker-calendar-set-event')); ?>";
            makeAjaxPost(data, url, null).done(function (response) {
                swalSuccess('Calendar Configured.');
            });
        }
    </script>
@endsection