@extends('layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{asset('public/css/plugins/fullcalendar/fullcalendar.css')}}"/>
    <div class="wrapper wrapper-content">
        <div class="ibox ">
            <div class="ibox-title">
                <h2>View Company Calender </h2>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-3">
                        <div id="external-events">
                        <form id="calendar_config_form" class="" method="post" action="{{route('company-calendar-show')}}">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label class="form-label"> Company<span class="required">*</span></label>
                                {{__combo('bat_company',array('selected_value'=>@$bat_company?$bat_company:1, 'attributes'=>array('class'=>'form-control', 'id'=>'bat_company_id','required'=>'true')))}}
                            </div>
                            <div class="form-group mt-2">
                                <label class="form-label">Calendar Month<span class="required">*</span></label>
                                <div class="input-group calendar_month">
                                    <input type="text" required
                                           placeholder=""
                                           class="form-control"
                                           value="{{ isset($show_month)?@$show_month:date('Y-m')}}"
                                           id="calendar_month" name="calendar_month"/>
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group mt-3">
                                <button id="show_company_calender" type="submit" class="btn btn-primary" style="width: 100%">View Calendar</button>
                            </div>
                        </form>
                        <br>
                        <div id="event_area">
                            <div class="row mt-2">
                                <label class="form-label col-md-8 mt-2 pr-0">Working Days</label>
                                <div class="col-md-4 pl-0">
                                    <input type="number" name="number_of_working_days" value="{{isset($monthly_day_status['R'])?$monthly_day_status['R']:'0'}}"
                                           id="number_of_working_days"
                                           class="form-control" readonly="">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <label class="form-label col-md-8 mt-2 pr-0">Holidays</label>
                                <div class="col-md-4 pl-0">
                                <input type="number" name="number_of_holiday_days" value="{{isset($monthly_day_status['H'])?$monthly_day_status['H']:'0'}}"
                                       id="number_of_holidays"
                                       class="form-control" readonly="">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <label class="form-label col-md-8 mt-2 pr-0">Weekend</label>
                                <div class="col-md-4 pl-0">
                                    <input type="number" name="number_of_weekend_days" value="{{isset($monthly_day_status['W'])?$monthly_day_status['W']:'0'}}"
                                       id="number_of_weekend"
                                       class="form-control" readonly="">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <label class="form-label col-md-8 mt-2 pr-0">Active Employee</label>
                                <div class="col-md-4 pl-0">
                                    <input type="number" name="number_of_active_emp" value="{{isset($activeEmployees)?$activeEmployees:'0'}}" placeholder="" class="form-control"   id="number_of_active_emp" disabled>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                    </div>
                    <div class="col-lg-9">
                        <div id="calendar" class="fc fc-unthemed fc-ltr"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('public/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/fullcalendar/fullcalendar.min.js')}}"></script>
    <script>


        $('#calendar_config_form').validator().on('submit', function (e) {
            var company_id = $('#bat_company_id').val();
            if(company_id == ''){
                swalError("Please select Employee Company.");
                e.preventDefault();
            }
        });

        $(document).ready(function () {
            var d = new Date();
            var n =  new Date( d.getFullYear(), d.getMonth(), 1);
            $("#calendar_month").datetimepicker({
                format:'YYYY-MM',
            });

            var company_id = "{{@$emp_company}}";
            if (company_id == '') {
                $('#calendar').hide();
                $('#event_area').hide();
            } else {

                var eventData = eventList();
            }


            /* initialize the external events
             -----------------------------------------------------------------*/


            $('#external-events div.external-event').each(function () {
                var eventObject = {
                    title: $.trim($(this).text()), // use the element's text as the event title
                    resourceId: parseInt($(this).attr('employee'))
                };
                $(this).data('eventObject', eventObject);

                /*$(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag

                });*/

            });

            /* initialize the calendar
             -----------------------------------------------------------------*/
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            $('#calendar').fullCalendar({
                defaultDate: moment('{{@$show_month}}'),
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,resourceDay'
                },
                resources: [],
                events: eventData ? JSON.parse(eventData) : [],
                selectable: true,
                selectHelper: true,
                editable: false,
                droppable: true, // this allows things to be dropped onto the calendar !!!
                drop: function (date, allDay) {
                    var defaultDuration = moment.duration($('#calendar').fullCalendar('option', 'defaultTimedEventDuration'));
                    var end = date.clone().add(defaultDuration); // on drop we only have date given to us
                    // retrieve the dropped element's stored Event Object
                    var originalEventObject = $(this).data('eventObject');
                    // we need to copy it, so that multiple events don't have a reference to the same object
                    var copiedEventObject = $.extend({}, originalEventObject);

                    // assign it the date that was reported
                    copiedEventObject.start = date;
                    copiedEventObject.allDay = allDay;

                    copiedEventObject.backgroundColor = $(this).css("background-color");
                    copiedEventObject.borderColor = $(this).css("border-color");

                    // render the event on the calendar

                    var start_date = date.format();
                    var end_date = end.format();
                    var event_title = copiedEventObject.title;

                   setCalendarDate(event_title, start_date, end_date);
                    // console.log(copiedEventObject);

                },
                // eventClick: function(event, element) {
                //
                //     event.title = "CLICKED!";
                //
                //     $('#calendar').fullCalendar('updateEvent', event);
                //
                // }
            });
        });

        function eventList() {
            var company_id = "{{@$emp_company}}";
            var calender_month = "{{@$show_month}}";
            var url2 = "{{route('company-calendar-get-event')}}/" + company_id+'/'+calender_month;
            var eventData = $.ajax({
                'url': url2,
                async: false
            }).responseText;
            return eventData;
        }
        function setCalendarDate(event_title, start_date, end_date) {
            var company_id = "{{@$emp_company}}";
            var data = {
                'event_title': event_title,
                'start_date': start_date,
                'end_date': end_date,
                'company_id': company_id,
            };
            var url = "{{route('company-calendar-set-event')}}";
            makeAjaxPost(data, url, null).done(function (response) {

               swalSuccess('Calendar Configured.');
               window.location.reload();
            });
        }


        //On Click Next Previous button on Calendar

        $('body').on('click', 'button.fc-prev-button', function() {
            callRequest();
        });
        $('body').on('click', 'button.fc-next-button', function() {
            callRequest();
        });

        function callRequest() {
            var date1 = $('#calendar').fullCalendar( 'getDate' );
            var start = new Date(date1);
            var y = start.getFullYear();
            var m = start.getMonth() + 1;

            if(m <10){
                m = '0'+m;
            }

            $('#calendar_month').val(y +'-'+ m);

            $('#calendar_config_form').submit();

            return false;
        }

    </script>

@endsection