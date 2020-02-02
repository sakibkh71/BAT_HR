@extends('layouts.app')
@section('content')
    <style>
        .row-select-toggle{
            cursor: default;
        }
        .dropdown-item {
            margin: 0;
            padding: 5px;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Employee Notifications</h2>
                    <div class="ibox-tools">
                        <button  class="btn btn-primary btn-xs" id="see_notification"><i class="fa fa-eye" aria-hidden="true">&nbsp;</i>See Notification</button>
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('emp_all_notification',1) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="empView" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="empview_wrap"></div>
        </div>
    </div>

<script>

    var selected = [];
    $('#see_notification').hide();
    $(document).on('click','#table-to-print tbody tr', function () {
        var self = $(this);
        var id = self.attr('id');

        /*add this for new customize*/
        selected = [];
        $('#table-to-print tbody tr').not($(this)).removeClass('selected');
        /* end this */

        if ($(this).toggleClass('selected')) {
            if ($(this).hasClass('selected')) {
                selected.push(id);
                self.find('input[type=checkbox]').prop("checked", true);
            } else {
                selected.splice(selected.indexOf(id), 1);
                self.find('input[type=checkbox]').prop("checked", false);
            }

            var arr_length = selected.length;
            if (arr_length > 1) {
                $('#see_notification').hide();

            }
            else if (arr_length == 1) {
                $('#see_notification').show();

            }
            else {
                $('#see_notification').hide();

            }
        }

    });
    $(document).on('click','#see_notification',function () {
       var notification_id=selected[0];
      // alert(notification_id);
        $.ajax({
           type:'get',
           data:{
               notification_id:notification_id
           },
            url:'{{url('redirect-to-single-notification')}}',
           success:function (url) {
               window.location.href = url;
           }
        });
    });

</script>
@endsection
