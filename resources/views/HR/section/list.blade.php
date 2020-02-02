@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>

     <style>
        .row-select-toggle{
            cursor: default;
        }
    </style>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Section List</h2>
                    </div>

                    <div class="ibox-title">
                        <div class="ibox-tools">
                            @if(isSuperUser())
                                <a href="{{route('create-employee-section')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus" aria-hidden="true"></i> New Section</a>
                                <button class="btn btn-warning btn-xs" id="section_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                <button class="btn btn-danger btn-xs" id="section_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" id="examples">
                                <thead>
                                <tr>
                                    <th class="d-none"></th>
                                    <th class="no-sort"></th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sections as $list)
                                    <tr class="row-select-toggle" id="{{ $list->hr_emp_sections_id ?? ''}}">
                                        <td style="display: none">{{ $list->hr_emp_sections_id ?? ''}}</td>
                                        <td>
                                            <input type="checkbox" class="item-selection" value="{{ $list->hr_emp_sections_id ?? ''}}">
                                        </td>
                                        <td>{{ $list->hr_emp_section_name ?? 'N/A'}}</td>
                                        <td>{{ $list->departments_name ?? 'N/A'}}</td>
                                        <td>{!! $list->description ?? 'N/A' !!} </td>
                                        <td>{{ $list->status ?? 'N/A'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

         $('.dataTables-example').dataTable({
            "bFilter": false,
            "bInfo": false,
            "lengthChange": false
        });

         (function ($) {

             $('#section_edit').hide();
             $('#section_delete').hide();

             var selected = [];

             $('#examples tbody').on('click', '.row-select-toggle', function () {
                 var self = $(this);
                 var id = self.attr('id');
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
                         $('#section_edit').hide();
                         $('#section_view').hide();
                     }
                     else if (arr_length == 1) {
                         $('#section_edit').show();
                         $('#section_delete').show();
                     }
                     else {
                         $('#section_edit').hide();
                         $('#section_delete').hide();
                     }
                 }

             });


             //Edit Employee
             $("#section_edit").on('click', function (e) {
                 var section_id = selected[0];
                 //alert(selected);
                 if (section_id.length === 0) {
                     swalError("Please select a Section");
                     return false;
                 } else {
                     window.location = '<?php echo URL::to('create-employee-section');?>/' + section_id;
                 }
             });

            // delete employee
             $("#section_delete").on('click', function (e) {
                 var section_id = selected;
                 if (section_id.length === 0) {
                     swalError("Please select a section");
                     return false;
                 } else {
                     swalConfirm('to Confirm Delete?').then(function (e) {
                         if(e.value){
                             var url = "{{URL::to('employee-section-delete')}}/" + section_id;
                             var data = {ids:section_id};
                             makeAjaxPost(data,url,null).then(function(response) {
                                 var redirect_url = "{{URL::to('hr-employee-section')}}";
                                 swalRedirect(redirect_url,'Delete Successfully','success');
                             });
                         }
                     });

                 }
             });

         })(jQuery);

    </script>
@endsection
