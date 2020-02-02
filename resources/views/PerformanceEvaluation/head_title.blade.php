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
                    <h2>Performance Evaluation</h2>
                    <div class="ibox-tools">
                        @if(isSuperUser())
                            <div class="dropdown float-left">
                            </div>
                            <button class="btn btn-warning btn-xs" data-toggle="modal" data-target=".myModal"  id="head_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                            {{--<button class="btn btn-danger btn-xs" id="head_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>--}}
                            <button class="btn btn-success btn-xs" data-toggle="modal" data-target=".myModal" id="head_create"><i class="fa fa-plus" aria-hidden="true"></i> Create Head</button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('pe_head_title',1) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="empView" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="empview_wrap"></div>
        </div>
    </div>

    <!--  Modal -->
    <div class="modal fade myModal" id="" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="createTitleHeadForm">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Head</h4>
                    </div>
                    <div class="modal-body col-md-12">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Head Name</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <input type="text" name="head_name" class="form-control" id="head_name">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{--<div class="col-md-6">--}}
                            {{--<div class="form-group">--}}
                            {{--<label class="font-normal"><strong>Head Type</strong> <span class="required">*</span></label>--}}
                            {{--<div class="">--}}
                            {{--<select name="head_type" id="head_type" class="form-control">--}}
                            {{--<option value="">Select Type</option>--}}
                            {{--<option value="Manual">Manual</option>--}}
                            {{--<option value="Auto">Auto</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--<div class="help-block with-errors has-feedback"></div>--}}
                            {{--</div>--}}
                            {{--</div>--}}

                        </div>
                        <div class="row">
                            {{--<div class="col-md-12">--}}
                            {{--<div class="form-group">--}}
                            {{--<label class="font-normal"><strong>Has Child Kpi</strong> <span class="required">*</span></label>--}}
                            {{--<div class="">--}}
                            {{--<select name="has_child_kpi" id="has_child_kpi" class="form-control">--}}
                            {{--<option value="Yes">Yes</option>--}}
                            {{--<option value="No">No</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--<div class="help-block with-errors has-feedback"></div>--}}
                            {{--</div>--}}
                            {{--</div>--}}

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Details</strong> </label>
                                    <div class="">
                                        <textarea name="head_details" id="head_details" class="form-control rounded-0"  rows="2"></textarea>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="title_id" id="title_id" value="">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Status</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <select name="head_status" id="head_status" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="Active" selected="selected">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn" type="button" id="createHeadSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var selected = [];

        $(document).ready(function(){
            $('#head_edit').hide();
            $('#head_delete').hide();
            // $('.cls_auto_api').hide();
        });

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
                    // $('#employee_edit').hide();
                    $('#head_edit').hide();
                    $('#head_delete').hide();
                    // $('#employee_separation').hide();
                    // $('#pdfOptions').hide();
                }
                else if (arr_length == 1) {
                    // $('#employee_separation').show();
                    $('#head_edit').show();
                    $('#head_delete').show();
                    // $('#pdfOptions').show();
                    // $('#employee_edit').show();
                    // $('#employee_delete').show();
                }
                else {
                    // $('#employee_edit').hide();
                    $('#head_edit').hide();
                    $('#head_delete').hide();
                    // $('#employee_separation').hide();
                    // $('#pdfOptions').hide();
                    // $('#employee_delete').hide();
                }
            }
        });

        // $("#head_type").on('change', function () {
        //
        //     var head_type = $('#head_type').val();
        //
        //     if(head_type == 'Auto'){
        //         $('.cls_auto_api').show();
        //     }
        //     else{
        //         $('.cls_auto_api').hide();
        //     }
        // });

        $("#head_edit").on('click', function () {

            var id = selected;
            $('#title_id').val(id);

            var url = "{{URL::to('pe-get-head-data')}}/"+id;

            $.get(url, function(data){
                console.log(data.info.details);
                $('#head_name').val(data.info.pe_head_titles_name);
                // $('#head_type').val(data.info.type);
                // $('#has_child_kpi').val(data.info.has_child_kpi);
                $('#head_status').val(data.info.status);
                $('#head_details').val(data.info.details);


                // if(data.info.type == 'Auto'){
                //
                //     $('.cls_auto_api').show();
                //     $('#auto_api').val(data.info.pe_auto_apis_id);
                // }
                // else{
                //     $('.cls_auto_api').hide();
                // }
            });
        });

        $("#head_create").on('click', function () {

            $('#title_id').val('');
            $('#head_name').val('');
            // $('#head_type').val('');
            $('#head_status').val('');
            // $('#has_child_kpi').val('');
            $('#head_details').val('');
            // $('#auto_api').val('');

        });

        // $('#extendProbationForm').validator('validate').has('.has-error').length;

        $(document).on('click', '#createHeadSubmit', function (e) {
            e.preventDefault();

            var head_name = $('#head_name').val();
            // var head_type = $('#head_type').val();
            var head_status = $('#head_status').val();
            var head_details = $('#head_details').val();
            // var auto_api = $('#auto_api').val();
            // var has_child_kpi = $('#has_child_kpi').val();

            if(head_name.length > 0  && head_status.length > 0 ){
                var url = "{{URL::to('pe-store-head-title')}}";
                var $form = $('#createTitleHeadForm');
                var data ={};
                var redirectUrl = '{{ URL::current()}}';
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if (response.code == 200) {

                        if(response.insert_or_update > 0){
                            swalRedirect(redirectUrl, 'Head Update Successfully', 'success');
                        }else{
                            swalRedirect(redirectUrl, 'Head Add Successfully', 'success');
                        }

                    }
                    else{
                        swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                    }
                });
            }
            else{
                swalError("Please Fill Up Mendatory Field!");
            }
        });

    </script>
@endsection
