@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>User List</h2>
                    </div>
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            @if(isSuperUser())
                                <button class="btn btn-warning btn-xs no-display" id="item_edit"><i class="fa fa-plus" aria-hidden="true"></i> Create User</button>
                            @endif
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            {!! __getMasterGrid('employee_users') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
$('#userTable').dataTable();
var selected_item = [];
$(document).on('click','#table-to-print tbody tr',function (e) {
    $obj = $(this);
    if(!$(this).attr('id')){ return true; }

    /*add this for new customize*/
    selected_item = [];
    $('#table-to-print tbody tr').not($(this)).removeClass('selected');
    /* end this */

    $obj.toggleClass('selected');
    var id = $obj.attr('id');
    if ($obj.hasClass( "selected" )){
        selected_item.push(id);
    }else{
        var index = selected_item.indexOf(id);
        selected_item.splice(index,1);
    }
    if(selected_item.length==1){
        $('#item_edit, #item_view').show();
    }else if(selected_item.length==0){
        $('#item_edit, #item_view').hide();
    }else{
        $('#item_edit, #item_view').hide();
    }

});

/*$(document).on('click','#item_view', function (e) {
    var $row = $('.checkbox-clickable tbody');
    var sys_users_id = $row.find('.selected').data('id');
    var data = {'sys_users_id':sys_users_id};
    var url = '<?php echo URL::to('get-user-profile');?>';
    Ladda.bind(this);
    var load = $(this).ladda();
    if (selected_item.length == 1) {
        makeAjaxPostText(data,url,load).done(function (response) {
            if(response){
                $('#medium_modal .modal-content').html(response);
                $('#medium_modal').modal('show');
            }
        });

    } else {
        swalWarning("Please select single item");
        return false;

    }
});*/

$(document).on('click','#item_edit', function (e) {

    var $row = $('#table-to-print tbody');
    var sys_users_id = $row.find('.selected').attr('id');
    var url = '<?php echo URL::to('create-user-form-employee');?>/'+sys_users_id;
    Ladda.bind(this);
    var load = $(this).ladda();
    if (selected_item.length == 1) {
        window.location.replace(url);
    } else {
        swalWarning("Please select single item");
        return false;

    }
});

    </script>
@endsection
