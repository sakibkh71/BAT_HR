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
                    <h2>Training List</h2>
                    <div class="ibox-tools">
                        <div class="dropdown float-left">
                        </div>
                        <button class="btn btn-warning btn-xs" data-toggle="modal" data-target=".myModal"  id="edit_training"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="btn btn-success btn-xs" data-toggle="modal" data-target=".myModal" id="add_training"><i class="fa fa-plus" aria-hidden="true"></i> Add Training</button>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class=" table  table-bordered table-striped" id="conf_tbl">
                        <thead>
                        <tr>
                            <th>Training Name</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Auto Assign</th>
                            <th>Total Hours</th>
                            <th>Location</th>
                            <th>Web Link</th>
                            <th>Fees</th>
                        </tr>
                        </thead>
                        @if(!empty($list))
                            <tbody>
                            @foreach($list as $info)
                                <tr class="row-select-toggle"  id="{{$info->training_list_id}}">

                                    <td>{{$info->name}}</td>
                                    <td>{{$info->details}}</td>
                                    <td>
                                        {{toDated($info->start_date) }} <strong>TO</strong> {{toDated($info->end_date)}}
                                    </td>
                                    <td>
                                        {{$info->assign_to_new_emp}}
                                    </td>
                                    <td>{{$info->hours}}</td>
                                    <td>{{$info->location}}</td>
                                    <td>
                                        @if(!empty($info->web_link))
                                            <a target="_blank" href="{{$info->web_link}}">{{$info->web_link}}</a>
                                            @else
                                            {{'NA'}}
                                        @endif

                                    </td>
                                    <td>{{$info->fees}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--  Modal -->
    <div class="modal fade myModal" id="" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="addTrainingForm">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Training</h4>
                    </div>
                    <div class="modal-body col-md-12">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Training Name</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <input type="text" name="training_name" class="form-control" id="training_name">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Start Date <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control start_date"
                                               {{-- value="{{$start_date}}" --}}
                                               id="start_date"
                                               data-date-format="yyyy-mm-dd"
                                               name="start_date" required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">End Date <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control end_date"
                                               {{--value="{{$end_date}}"--}}
                                               id="end_date"
                                               data-date-format="yyyy-mm-dd"
                                               name="end_date" required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Auto Assign New Employee</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <select name="auto_assign" id="auto_assign" class="form-control">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Number of Hours</strong> </label>
                                    <div class="">
                                        <input type="text" name="number_hours" class="form-control" id="number_hours">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Location</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <textarea name="location" id="location" class="form-control rounded-0"  rows="2"></textarea>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Details</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <textarea name="details" id="details" class="form-control rounded-0"  rows="2"></textarea>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Web Link</strong> </label>
                                    <div class="">
                                        <input type="text" name="web_link" class="form-control" id="web_link">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Training Fees</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <input type="text" name="training_fee" class="form-control" id="training_fee">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="training_id" id="training_id" value="0">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Status</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <select name="status" id="status" class="form-control">
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
                        <button class="btn btn-primary btn" type="button" id="addTrainingSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#conf_tbl').DataTable();
            $('#edit_training').hide();


            $(function ($) {
                $("#start_date").datepicker( {
                    format: "yyyy-mm-dd",
                    viewMode: "dates",
                    minViewMode: "dates",
                    //startDate: '+0d',
                    autoclose: true,
                });
            });

            $(function ($) {
                $("#end_date").datepicker( {
                    format: "yyyy-mm-dd",
                    viewMode: "dates",
                    minViewMode: "dates",
                    //startDate: '+0d',
                    autoclose: true,
                });
            });
        } );

        var selected = [];

        $(document).on('click','#conf_tbl tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            console.log(id);

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
                    $('#edit_training').hide();
                }
                else if (arr_length == 1) {
                    $('#edit_training').show();
                }
                else {
                    $('#edit_training').hide();
                }
            }
        });

        $(document).on('click', '#addTrainingSubmit', function (e) {
            e.preventDefault();

            var training_name = $('#training_name').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var auto_assign = $('#auto_assign').val();
            var number_hours = $('#number_hours').val();
            var location = $('#location').val();
            var details = $('#details').val();
            var web_link = $('#web_link').val();
            var training_fee = $('#training_fee').val();
            var status = $('#status').val();

            if(training_name.length > 0
                && start_date.length > 0
                && end_date.length > 0
                && location.length > 0
                && details.length > 0
                && training_fee.length > 0
            ){
                swalConfirm('Are you sure?').then(function(e) {
                    if(e.value){
                        var url = "{{URL::to('training-list')}}";
                        var $form = $('#addTrainingForm');
                        var data ={};
                        var redirectUrl = '{{ URL::current()}}';
                        data = $form.serialize() + '&' + $.param(data);
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.code == 200) {

                                if(response.insert_or_update > 0){
                                    swalRedirect(redirectUrl, 'Training Update Successfully', 'success');
                                }else{
                                    swalRedirect(redirectUrl, 'Training Add Successfully', 'success');
                                }

                            }
                            else{
                                swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                            }
                        });
                    }
                });
            }
            else{
                swalError("Please Fill Up Mendatory Field!");
            }
        });

        $("#add_training").on('click', function () {
            $('.modal-title').html('Add Training');
            $('#training_name').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#auto_assign').val('No');
            $('#number_hours').val('');
            $('#location').val('');
            $('#details').val('');
            $('#web_link').val('');
            $('#training_fee').val(0);
            $('#status').val('Active');
            $('#training_id').val(0);
        });

        $("#edit_training").on('click', function () {

            var id = selected;

            var url = "{{URL::to('training-list-edit')}}/"+id;
            // alert(url);
            makeAjax(url,null).done(function(response){
                console.log(response.training);
                $('#training_name').val(response.training.name);
                $('#start_date').val(response.training.start_date);
                $('#end_date').val(response.training.end_date);
                $('#auto_assign').val(response.training.assign_to_new_emp);
                $('#number_hours').val(response.training.hours);
                $('#location').val(response.training.location);
                $('#details').val(response.training.details);
                $('#web_link').val(response.training.web_link);
                $('#training_fee').val(response.training.fees);
                $('#status').val(response.training.status);
                $('#training_id').val(response.training.training_list_id);
                $('.modal-title').html('Edit Training');
            });
        });
    </script>
@endsection
