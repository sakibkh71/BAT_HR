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
                        <div class="ibox-tools">
                            <button class="btn btn-success btn-xs" id="new-item"><i class="fa fa-plus" aria-hidden="true"></i> New</button>
                            <button class="btn btn-success btn-xs no-display" id="view-item"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                            <button class="btn btn-info btn-xs no-display" id="edit-item"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                            <button class="btn btn-danger btn-xs no-display" id="delete-item"><i class="fa fa-remove" aria-hidden="true"></i> Delete</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="checkbox-clickable table table-striped table-bordered table-hover emp-leave-list">
                            <thead>
                            <tr>
                                <th width="">Month</th>
                                <th width="">Emp Category</th>
                                <th width="">Working Days</th>
                                <th width="">Holidays</th>
                                <th width="">Weekend</th>
                                <th width="">Active Employee</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($all_data))
                                @foreach($all_data as $info)
                                    <tr class="row-select-toggle" data-id="{{$info->hr_salary_month_configs_id}}">
                                        <td>{{$info->month}}, {{$info->year}}</td>
                                        <td>{{$info->hr_emp_category_name}}</td>
                                        <td>{{$info->number_of_working_days}}</td>
                                        <td>{{$info->number_of_holidays}}</td>
                                        <td>{{$info->number_of_weekend}}</td>
                                        <td>{{$info->number_of_active_emp}}</td>
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
            $('.emp-leave-list').dataTable();
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
            var view_url = "<?php echo URL::to('show-hr-employee-salary-month-details-info')?>/"+selected_row[0];
            window.location.replace(view_url);
        });

        $('#edit-item').on('click', function () {
            var view_url = "<?php echo URL::to('salary-month-config')?>/"+selected_row[0];
            window.location.replace(view_url);
        });

        $('#new-item').on('click', function () {
            var view_url = "<?php echo URL::to('salary-month-config')?>";
            window.location.replace(view_url);
        });

        $('#delete-item').on('click', function () {
            Ladda.bind(this);
            var load = $(this).ladda();
            var view_url = "<?php echo URL::to('delete-hr-employee-salary-month-details-info')?>/"+selected_row[0];
            var ajax_success_url = '{{URL::to('hr-employee-salary-month-config-list')}}';
            swalConfirm("Are you sure you want to delete this?").then((e) => {
                if(e.value){
                    makeAjaxText(view_url,load).then((response) =>{
                        if(response){
                            swalRedirect(ajax_success_url,'successfully Deleted.');
                        }else{
                            swalError();
                        }
                    });
                }
            });
        });
        
        function actionManager(selected_row){
            if(selected_row.length < 1){
                $('#view-item').fadeOut();
                $('#edit-item').fadeOut();
                $('#delete-item').fadeOut();
            }else if(selected_row.length == 1){
                $('#view-item').fadeIn();
                $('#edit-item').fadeIn();
                $('#delete-item').fadeIn();
            }else{
                $('#view-item').fadeOut();
                $('#edit-item').fadeOut();
                $('#delete-item').fadeOut();
            }
        }
    </script>
@endsection
