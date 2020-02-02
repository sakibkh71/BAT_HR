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
                    <h2>KPI Questions</h2>
                    <div class="ibox-tools">
                        @if(isSuperUser())
                            <div class="dropdown float-left">
                            </div>
                            <button class="btn btn-warning btn-xs" data-toggle="modal" data-target=".myModal"  id="head_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                            {{--<button class="btn btn-danger btn-xs" id="head_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>--}}
                            <button class="btn btn-success btn-xs" data-toggle="modal" data-target=".myModal" id="head_create"><i class="fa fa-plus" aria-hidden="true"></i> Create New Question</button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('pe_kpi_questions',1) !!}
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
                <form action="#" method="post" id="createQuestionForm">
                    <div class="modal-header">
                        <h4 class="modal-title">Create KPI Question</h4>
                    </div>
                    <div class="modal-body col-md-12">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Question</strong> <span class="required">*</span> </label>
                                    <div class="">
                                        <textarea name="question" id="question" class="form-control rounded-0"  rows="2"></textarea>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="font-normal"><strong>Head Type</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <select name="head_type" id="head_type" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="Manual">Manual</option>
                                            <option value="Auto">Auto</option>
                                        </select>
                                    </div>
                                <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-06">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Status</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <select name="question_status" id="question_status" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>

                        </div>
                        <div class="row cls_auto_api">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Api</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <select name="auto_api" id="auto_api" class="form-control">
                                            <option value="">Select Api</option>
                                            @foreach($api_list as $info)
                                                <option value="{{$info->pe_auto_apis_id}}">{{$info->pe_auto_apis_title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Details</strong> </label>
                                    <div class="">
                                        <textarea name="question_details" id="question_details" class="form-control rounded-0"  rows="2"></textarea>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="question_id" id="question_id" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn" type="button" id="createQuestionSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
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
            $('.cls_auto_api').hide();
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

        $("#head_type").on('change', function () {

            var head_type = $('#head_type').val();

            if(head_type == 'Auto'){
                $('.cls_auto_api').show();
            }
            else{
                $('.cls_auto_api').hide();
            }
        });

        $("#head_edit").on('click', function () {

            var id = selected;
            $('#question_id').val(id);

            var url = "{{URL::to('pe-get-kpi-question-data')}}/"+id;

            $.get(url, function(data){
                console.log(data.info.details);
                $('#question').val(data.info.question);
                $('#question_details').val(data.info.details);
                $('#question_status').val(data.info.status);
                $('#head_type').val(data.info.type);
                if(data.info.type == 'Auto'){

                    $('.cls_auto_api').show();
                    $('#auto_api').val(data.info.pe_auto_apis_id);
                }
                else{
                    $('.cls_auto_api').hide();
                }
            });
        });

        $("#head_create").on('click', function () {

            $('#question').val('');
            $('#question_details').val('');
            $('#question_status').val('');
            $('#question_id').val('');
        });

        // $('#extendProbationForm').validator('validate').has('.has-error').length;

        $(document).on('click', '#createQuestionSubmit', function (e) {
            e.preventDefault();

            var question = $('#question').val();
            var question_details = $('#question_details').val();
            var question_status = $('#question_status').val();

            if(question.length > 0 && question_status.length > 0 ){
                var url = "{{URL::to('pe-store-kpi-question')}}";
                var $form = $('#createQuestionForm');
                var data ={};
                var redirectUrl = '{{ URL::current()}}';
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if (response.code == 200) {

                        if(response.insert_or_update > 0){
                            swalRedirect(redirectUrl, 'Question Update Successfully', 'success');
                        }else{
                            swalRedirect(redirectUrl, 'Question Add Successfully', 'success');
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
