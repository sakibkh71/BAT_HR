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
                    <h2>Employee Probation List</h2>
                    <div class="ibox-tools">
                        @if(isSuperUser())
                            <div class="dropdown float-left">
                            </div>
                            <button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal" id="probation_extend"><i class="fa fa-pencil" aria-hidden="true"></i> Change Probation Period</button>
                            <button class="btn btn-success btn-xs" id="employee_active"><i class="fa fa-plus" aria-hidden="true"></i> Confirm Employee</button>
                            <button class="btn btn-primary btn-xs" id="employee_separation"><i class="fa fa-road" aria-hidden="true"></i> Action</button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('hr_emp_probation_list',1) !!}
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
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="extendProbationForm">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Probation Period</h4>
                    </div>
                    <div class="modal-body col-md-12">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Extend Probation Date</strong> <span class="required">*</span></label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="extend_date" class="form-control" id="extend_date" data-error="Please select Date" value="" placeholder="YYYY-MM-DD"  required="" autocomplete="off">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Shibly: Code Start-->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Remarks</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <textarea name="probation_remarks" id="probation_remarks" class="form-control rounded-0" id="exampleFormControlTextarea2" rows="3"></textarea>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Shibly: Code End-->
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="employee_id" id="employee_id" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn" type="button" id="probationSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Separation Modal -->

    <div class="modal fade" tabindex="-1" id="separationModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Employee Release Process</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-normal"><strong>Termination Date</strong> <span
                                        class="required">*</span></label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" name="separation_date" id="separation_date" class="form-control"
                                       data-error="Please select Release Date" value="{{ date('Y-m-d')}}"
                                       placeholder="YYYY-MM-DD" required="" autocomplete="off">
                            </div>
                            <div class="help-block with-errors has-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="leaverProcess" class="btn btn-primary">Process</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var selected = [];

        $(document).ready(function(){
            $('#probation_extend').hide();
            $('#employee_active').hide();
            $('#employee_separation').hide();
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
                    $('#probation_extend').hide();
                    $('#employee_active').hide();
                    $('#employee_separation').hide();
                    // $('#employee_separation').hide();
                    // $('#pdfOptions').hide();
                }
                else if (arr_length == 1) {
                    // $('#employee_separation').show();
                    $('#probation_extend').show();
                    $('#employee_active').show();
                    $('#employee_separation').show();
                    // $('#pdfOptions').show();
                    // $('#employee_edit').show();
                    // $('#employee_delete').show();
                }
                else {
                    // $('#employee_edit').hide();
                    $('#probation_extend').hide();
                    $('#employee_active').hide();
                    $('#employee_separation').hide();
                    // $('#employee_separation').hide();
                    // $('#pdfOptions').hide();
                    // $('#employee_delete').hide();
                }
            }
        });

        //Separation of employee
        $('#employee_separation').on('click',function (e) {
            var employee_id=selected[0];
            if(employee_id.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                var url = "{{route('check-separated')}}";
                var data = {'emp_id':selected[0]};
                var redirectUrl = window.location.href;
                makeAjaxPost(data, url).done(function (response) {
                    if (response.exist == "no") {
                        $('#separationModal').modal('show');
                    }
                    else {
                        swalRedirect(redirectUrl, 'Sorry! this employee already under a separation request', 'error');
                    }
                });
            }
        });

        $('#leaverProcess').click(function () {
            var employee_id=selected[0];
            var separation_date = $('#separation_date').val();
            if(separation_date){
                var url = "{{route('get-separation-form')}}/"+employee_id+'/'+separation_date;
                window.location.replace(url);
            }else{
                swalError('Please Select Release Date');
            }

        });

        //View Config Kpi
        $("#probation_extend").on('click', function (e) {

            var user_id = selected[0];
            console.log(user_id);

            var url = "{{url('emp-extend-probation-date')}}/"+user_id;

            $.get(url, function(data, status){
                console.log(data);
                $('#employee_id').val(data.emp_id);
                $('#extend_date').val(data.confirmation_date);
                // $('#emg_con_list').html(data);
            });
        });

        //date picker
        function datepic(){
            $('.input-group.date').datepicker({ format: "yyyy-mm-dd", autoclose:true });
        }
        datepic();

        // $('#extendProbationForm').validator('validate').has('.has-error').length;

        $(document).on('click', '#probationSubmit', function (event) {
//alert('test');
            if (!$('#extendProbationForm').validator('validate').has('.has-error').length) {
                var url = '<?php echo URL::to('extend-probation-update');  ?>'
                var $form = $('#extendProbationForm');
                var data ={};
                var redirectUrl = '{{ URL::current()}}';
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if (response.success) {
                        swalRedirect(redirectUrl, 'Probation period changed successfully', 'success');
                    }
                    else{
                        swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                    }
                    // $('#separationForm').trigger("reset");
                    // $('#separationModal').modal('hide');
                    // $('#employee_separation').hide();

                });
            }

        });

        $("#employee_active").on('click', function (e) {
            var employee_id = selected;
            if (employee_id.length === 0) {
                swalError("Please select a Employee");
                return false;
            } else {
                swalConfirm('to Confirm Active?').then(function (e) {
                    // console.log(employee_id);
                    if(e.value){
                        var url = "{{URL::to('employee-probation-to-active')}}";
                        var data = {employee_id:employee_id};
                        makeAjaxPost(data,url,null).then(function(response) {
                            var redirect_url = "{{URL::to('employee')}}";
                            swalRedirect(redirect_url,'Active Successfully','success');
                        });
                    }
                });

            }
        });
    </script>
@endsection
