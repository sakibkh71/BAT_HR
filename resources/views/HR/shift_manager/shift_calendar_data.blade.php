    <link rel="stylesheet" type="text/css" href="{{asset('public/css/plugins/fullcalendar/fullcalendar.css')}}"/>
    <div class="ibox ">
        <div class="ibox-content">
            <div id="calendar" class="fc fc-unthemed fc-ltr"></div>
        </div>
    </div>

    <script src="{{asset('public/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/fullcalendar/fullcalendar.min.js')}}"></script>
    <script>


        $('#employee_id').on('change', function (e) {
            var calendar_month = $('#calendar_month').val();
            if(calendar_month == ''){
                swalError("Please select Month.");
                e.preventDefault();
            }else{
                var val = $(this).val();
                var url = "<?php echo URL::to('shift-change-calendar')?>/"+calendar_month+'/'+val;
                window.location.replace(url);
            }

        });

        $(document).ready(function () {
            $("#calendar_month").datetimepicker({
                format:'YYYY-MM'
            });
            var sys_users_id = "{{@$sys_users_id}}";
            if (sys_users_id == '') {
                $('#calendar').hide();
                $('#event_area').hide();
            } else {
                var eventData = eventList();
                $('#calendar').show();

            }

            $('#external-events div.external-event').each(function () {
                var eventObject = {
                    title: $.trim($(this).text()), // use the element's text as the event title
                    resourceId: parseInt($(this).attr('employee'))
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
                defaultDate: moment('{{@$calendar_day}}'),
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
                    var originalEventObject = $(this).data('eventObject');
                    var copiedEventObject = $.extend({}, originalEventObject);
                    copiedEventObject.start = date;
                    copiedEventObject.allDay = allDay;
                    copiedEventObject.backgroundColor = $(this).css("background-color");
                    copiedEventObject.borderColor = $(this).css("border-color");
                    var start_date = date.format();
                    var end_date = end.format();
                    var event_title = copiedEventObject.title;
                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                }
            });

        });

        function eventList() {
            var sys_users_id = "{{@$sys_users_id}}";
            var url2 = "{{route('employee-calendar-get-event')}}/" + sys_users_id;
            var eventData = $.ajax({
                'url': url2,
                async: false
            }).responseText;
            return eventData;
        }

    </script>
