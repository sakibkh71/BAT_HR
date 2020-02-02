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
                        <h2>Employee Leave Management List</h2>
                        <div class="ibox-tools">
                            <button class="btn btn-success btn-xs no-display" id="view-item"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="checkbox-clickable table table-striped table-bordered table-hover emp-leave-list">
                            <thead>
                            <tr>
                                <th width="25%">Name</th>
                                <th width="">Mobile</th>
                                <th width="">Email</th>
                                <th width="">Department</th>
                                <th width="">Designation</th>
                                <th width="">Branch</th>
                                <th width="">Unit</th>
                                <th width="">Section</th>
                                <th width="5%">Leaves</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($user_info))
                                @foreach($user_info as $userinfo)
                                    <tr class="row-select-toggle" data-id="{{$userinfo->id}}">
                                        <td>{{$userinfo->name}}</td>
                                        <td>{{$userinfo->mobile}}</td>
                                        <td>{{$userinfo->email}}</td>
                                        <td>{{$userinfo->departments_name}}</td>
                                        <td>{{$userinfo->designations_name}}</td>
                                        <td>{{$userinfo->branchs_name}}</td>
                                        <td>{{$userinfo->hr_emp_unit_name}}</td>
                                        <td>{{$userinfo->hr_emp_section_name}}</td>
                                        <td class="text-center">{{$userinfo->total_leaves}}</td>
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
            var view_url = "<?php echo URL::to('get-emp-leave-history')?>/"+selected_row[0];
            window.location.replace(view_url);
        });
        
        function actionManager(selected_row){
            if(selected_row.length < 1){
                $('#view-item').fadeOut();
                /*----no selection action-----*/
            }else if(selected_row.length == 1){
                $('#view-item').fadeIn();
            }else{
                $('#view-item').fadeOut();
            }
        }
    </script>
@endsection
