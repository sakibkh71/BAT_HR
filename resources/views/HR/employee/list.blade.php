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
                        <h2>Employee List</h2>
                        <div class="ibox-tools">
                            <div class="dropdown float-left">
                                <button type="button" id="exportExcel" class="btn btn-info btn-xs"><i class="fa fa-file-excel-o"></i> Excel</button>
                                <button class="btn btn-xs" id="show-custom-search"><i class="fa fa-search"></i> show search</button>
                                <button class="btn btn-success btn-xs no-display" id="ConfirmationLetter"><i class="fa fa-file" aria-hidden="true"></i> Employment Letter</button>
                                <button class="btn btn-success btn-xs no-display" id="employee_view"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                                <button class="btn btn-warning btn-xs no-display" id="employee_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>{{--
                                <button class="btn btn-danger btn-xs no-display" id="employee_delete"><i class="fa fa-trash" aria-hidden="true"></i> Inactive</button>--}}
                                <button class="btn btn-primary btn-xs no-display" id="employee_active"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i> Active</button>
                            </div>
                    </div>
                    </div>
                    <div class="ibox-content">
                        {!! __getMasterGrid('hr_emp_list') !!}
                    </div>
                </div>
            </div>
    </div>

    <div class="modal fade" id="empView" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="empview_wrap"></div>
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
        $('#hr_emp_categorys_id option:contains("Choose Option")').text('Employee Category');
        var selected = [];
        var statusArr = [];

        $(document).on('click','#table-to-print tbody tr', function () {
            selected = [];
            statusArr = [];
            var self = $(this);
            var id = self.attr('id');
            var status = self.attr('data-status');

            /*add this for new customize*/
            selected = [];
            statusArr = [];
            $('#table-to-print tbody tr').not($(this)).removeClass('selected');
            /* end this */

            if ($(this).toggleClass('selected')) {
                    if ($(this).hasClass('selected')) {
                        selected.push(id);
                        statusArr.push(status);
                    } else {
                        selected.splice(selected.indexOf(id), 1);
                        statusArr.splice(statusArr.indexOf(status), 1);
                    }
                    console.log(selected);
                    var arr_length = selected.length;
                    if (arr_length == 1) {
                        $('#employee_separation').show();
                        $('#employee_view').show();
                        $('#pdfOptions').show();
                        $('#employee_edit').show();
                        $('#ConfirmationLetter').show();

                        //checking status
                        if(statusArr[0] == 'Inactive'){
                            $('#employee_active').show();
                        }
                        else if(statusArr[0] == 'Active'){
                            $('#employee_delete').show();
                        }
                    }
                    else {
                        $('#employee_edit').hide();
                        $('#ConfirmationLetter').hide();
                        $('#employee_view').hide();
                        $('#employee_separation').hide();
                        $('#pdfOptions').hide();
                        $('#employee_delete').hide();
                        $('#employee_active').hide();
                    }
                }
        });

        (function ($) {
            datepic();
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

            //date picker
            function datepic(){
                $('.input-group.date').datepicker({ format: "yyyy-mm-dd", autoclose:true });
            }
            datepic();


            // when the separation form is submitted
            $(document).on('click', '#separationSubmit', function (event) {

                if (!$('#separationForm').validator('validate').has('.has-error').length) {
                    var url = '<?php echo URL::to('hr-separation-causes-store');  ?>'
                     var $form = $('#separationForm');
                    var data ={};
                    var redirectUrl = '{{ URL::current()}}';
                    data = $form.serialize() + '&' + $.param(data);
                    makeAjaxPost(data, url).done(function (response) {
                        if (response.success) {
                            swalRedirect(redirectUrl, 'Seperation information added successfully', 'success');

                        }
                        else{
                            swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                        }
                        $('#separationForm').trigger("reset");
                        $('#separationModal').modal('hide');
                        $('#employee_separation').hide();

                    });
                }

            });

            //cancel separation
            $(document).on('click', '#cancelSeparation', function (event) {
                swalConfirm('Are you sure to cancel separation?').then(function(s) {
                    if (s.value) {
                        var employee_id=$("#employee_id").val();
                        var redirectUrl = '{{ URL::current()}}';
                        $.ajax({
                            type: 'POST',
                            url:  '<?php echo URL::to('hr-separation-causes-store');  ?>',
                            data: {'employee_id':employee_id},
                            cache: false,
                            success: function(){
                                swalRedirect(redirectUrl, 'Seperation information added successfully', 'success');
                            },
                            error: function(){
                                swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                            }
                        });
                    }
                });
            });


            //Edit Employee
            $("#employee_edit").on('click', function (e) {
                var employee_id = selected[0];
               if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
               } else {
                    window.location = '<?php echo URL::to('employee-entry');?>/' + employee_id;
               }
            });

            //View Employee
            $("#employee_view").on('click', function (e) {
                var employee_id = selected[0];
                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    var url = "{{URL::to('employee')}}/" + employee_id +'/basic/view';
                    window.location = url;
                }
            });



            // delete employee
            $("#employee_delete").on('click', function (e) {
                var employee_id = selected[0];
                var employee_status = selected[1];
                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    swalConfirm('to Confirm Inactive?').then(function (e) {

                       if(e.value){
                           var url = "{{URL::to('employee-delete')}}/" + employee_id;
                           var data = {employee_id:employee_id, employee_status: employee_status};
                           makeAjaxPost(data,url,null).then(function(response) {
                               var redirect_url = "{{URL::to('employee')}}";
                               swalRedirect(redirect_url,'Delete Successfully','success');
                           });
                       }
                    });

                }
            });

            // delete employee
            $("#employee_active").on('click', function (e) {
                var employee_id = selected[0];
                var employee_status = selected[1];

                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    swalConfirm('to Confirm Change Status?').then(function (e) {

                        if(e.value){
                            var url = "{{URL::to('employee-delete')}}/" + employee_id+'/'+employee_status;
                            var data = {employee_id:employee_id, employee_status: employee_status};
                            makeAjaxPost(data,url,null).then(function(response) {
                                var redirect_url = "{{URL::to('employee')}}";
                                swalRedirect(redirect_url,'Update Successfully','success');
                            });
                        }
                    });

                }
            });


            /*
             * Date Range Picker
             */
            $('#dateRange').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply:true,
            });


            //Application Letter
            $("#AppointmentLetter").on('click', function (e) {
                var employee_id = selected[0];
                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    var url = "{{URL::to('appointment-letter')}}/" + employee_id;
                    window.open(url, '_blank');
                }
            });

            //Confirmation Letter
            $("#ConfirmationLetter").on('click', function (e) {
                var employee_id = selected[0];
                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    var url = "{{URL::to('confirmation-letter')}}/" + employee_id;
                    window.open(url, '_blank');
                }
            });

            //Job Application Letter
            $("#JobApplicationLetter").on('click', function (e) {
                var employee_id = selected[0];
                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    var url = "{{URL::to('job-application')}}/" + employee_id;
                    window.open(url, '_blank');
                }
            });


            //Age and Affidavit Certificate
            $("#AgeAffidavitCertificate").on('click', function (e) {
                var employee_id = selected[0];
                if (employee_id.length === 0) {
                    swalError("Please select a Employee");
                    return false;
                } else {
                    var url = "{{URL::to('age-affidavit-certificate')}}/" + employee_id;
                    window.open(url, '_blank');
                }
            });

        })(jQuery);

        $('#exportExcel').click(function () {
            var url='{{route("employee-info-excel")}}';
            $.ajax({
                type:'get',
                url:url,
                success:function (data) {
                    console.log(data);
                    window.location.href = './public/export/' + data.file;
                    swalSuccess('Export Successfully');
                }
            });
        });

    </script>
@endsection
