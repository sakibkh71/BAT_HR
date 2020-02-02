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
                    <h2>Company Organogram</h2>
                    <div class="ibox-tools">
                        @if(isSuperUser())
                            {{--<a href="{{url('employee-grade-entry')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus" aria-hidden="true"></i>Add New</a>--}}
                            <button class="btn btn-warning btn-xs" id="grade_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>

                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('hr_company_organogram',1) !!}
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

        $('#hr_emp_categorys_id option:contains("Choose Option")').text('Employee Category');

        var selected = [];

        $(document).on('click','#table-to-print tbody tr', function () {
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
                    $('#grade_edit').hide();
                    $('#pdfOptions').hide();
                }
                else if (arr_length == 1) {
                    $('#employee_view').show();
                    $('#pdfOptions').show();
                    $('#grade_edit').show();
                    $('#employee_grade_delete').show();
                    $('#add_component').show();
                }
                else {
                    $('#employee_grade_edit').hide();
                    $('#pdfOptions').hide();
                    $('#employee_grade_delete').hide();
                    $('#grade_edit').hide();
                    $('#add_component').hide();
                }
            }

        });
        (function ($) {
            $('#employee_grade_delete').hide();
            $('#pdfOptions').hide();
            $('#grade_edit').hide();
            $('#add_component').hide();


            //Edit Employee
            $("#grade_edit").on('click', function (e) {
                var company_id = selected[0];

                if (company_id.length === 0) {
                    swalError("Please select a Grade");
                    return false;
                } else {
                    window.location = '<?php echo URL::to('company-organogram-edit');?>/' + company_id;
                }
            });



        })(jQuery);

    </script>
@endsection
