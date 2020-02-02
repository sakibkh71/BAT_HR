@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Employee Working Shift Change Approval</h2>
                        <div class="ibox-tools">
                            <button class="btn btn-success btn-xs no-display" id="view-item"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                            <button class="btn btn-info btn-xs" id="select-all"><i class="fa fa-boxes" aria-hidden="true"></i> Select All</button>
                            <button class="btn btn-info btn-xs no-display" id="approve">Approve</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="checkbox-clickable table table-striped table-bordered table-hover data-table">
                            <thead>
                            <tr>
                                <th width="">Effective Date</th>
                                <th width="">Current Shift</th>
                                <th width="">Requested Shift</th>
                                <th width="">Shifted Employee</th>
                                <th width="">Created By</th>
                                <th width="">Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($shift_infos))
                                @foreach($shift_infos as $shift_info)
                                    <tr class="row-select-toggle" data-id="{{$shift_info->hr_emp_vs_shift_log_id}}">
                                        <td>{{toDated($shift_info->start_date)}}</td>
                                        <td>{{!empty($shift_info->previous_shift_id) ? $shifts[$shift_info->previous_shift_id] : '-'}}</td>
                                        <td>{{$shifts[$shift_info->hr_working_shifts_id]}}</td>
                                        <td>{{$shift_info->total_emps}}</td>
                                        <td>{{getUserInfoFromId($shift_info->created_by)->name}}</td>
                                        <td>{{toDated($shift_info->created_at)}}</td>
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
    <script>
        var selected_row = [];
        $(document).ready(function(){
            $('.data-table').DataTable({
                "aaSorting": [[ 1, "desc" ]]
            });
            actionManager(selected_row);
        });
        $(document).on('click','.row-select-toggle',function (e) {
            $(this).toggleClass('selected');
            var id = $(this).data('id');
            if ($(this).hasClass( "selected" )){
                selected_row.push(id);
            }else{
                var index = selected_row.indexOf(id);
                selected_row.splice(index,1);
            }
            actionManager(selected_row);
        });
        $('#view-item').on('click', function () {
            var view_url = "<?php echo URL::to('get-emp-leave-history')?>/"+selected_row[0];
            window.location.replace(view_url);
        });
        $('#select-all').on('click', function () {
            $('.row-select-toggle').toggleClass('selected');
            $('.row-select-toggle').each(function() {
                var id = $(this).data('id');
                if ($(this).hasClass( "selected" )){
                    selected_row.push(id);
                }else{
                    var index = selected_row.indexOf(id);
                    selected_row.splice(index,1);
                }
            });
            actionManager(selected_row);
        });
        function actionManager(selected_row){
            //            console.log(selected_row);
            if(selected_row.length < 1){
                $('#view-item').fadeOut();
                $('#approve').fadeOut();
                $('.emp-ids').val('');
                /*----no selection action-----*/
            }else if(selected_row.length == 1){
                $('#view-item').fadeIn();
                $('#approve').fadeIn();
            }else{
                $('#view-item').fadeOut();
                $('#approve').fadeIn();
            }
        }
        /*-------------------------------------------------------*/
        $('#approve').on('click', function (e) {
            if(selected_row.length > 0){
                swalConfirm('Are you sure to Approve the selected items?').then(function (result) {
                    if(result.value){
                        var formdata = {id:selected_row};
                        var url = '<?php echo URL::to('shift-submit');?>';
                        Ladda.bind($('#approve'));
                        var load = $('#approve').ladda();
                        makeAjaxPost(formdata, url, load).then(function(data){
                            if(data.mode == 'success')
                                swalRedirect(null, 'Approval Process Completed', 'success');
                        });
                    }
                });
            }else{
                swalWarning("No Employee has been Selected");
            }
        });
    </script>
@endsection