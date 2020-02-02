@extends('layouts.app')
@section('content')
@include('dropdown_grid.dropdown_grid')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Employee Grade Component List</h2>
                    <div class="ibox-tools">
                        @if(isSuperUser())
                            <button class="btn btn-success btn-xs" id="newcomponent"><i class="fa fa-plus-circle" aria-hidden="true"></i> ADD New Component</button>
                            <button class="btn btn-warning btn-xs" id="grade_component_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                            <button class="btn btn-danger btn-xs" id="employee_grade_component_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                        <table id = "table-to-print" class="table table-bordered table-striped table-hover dataTables">
                        <thead>
                            <tr>
                                <th>Grade Name</th>
                                <th>Component Name</th>
                                <th>Component Type</th>
                                <th>Component Slug</th>
                                <th>Ratio of Basic</th>
                                <th>Adition Amount</th>
                                <th>Deduction Amount</th>
                                <th>Auto Applicable</th>
                                <th>Component Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($emp_grade_component_info))
                                @foreach($emp_grade_component_info as $ai)
                                    <tr id="{{$ai->hr_grade_components_id}}">
                                        <td>{{$ai->hr_emp_grade_name}}</td>
                                        <td>{{$ai->component_name}}</td>
                                        <td>{{$ai->component_type}}</td>
                                        <td>{{$ai->component_slug}}</td>
                                        <td>{{$ai->ratio_of_basic}}</td>
                                        <td>{{$ai->addition_amount}}</td>
                                        <td>{{$ai->deduction_amount}}</td>
                                        <td>{{$ai->auto_applicable}}</td>
                                        <td>{{$ai->component_note}}</td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="9" class="text-center">No records found.</td>
                                </tr>
                               @endif
                        </tbody>
                    </table>
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
        $('.dataTables1').dataTable({
            "bFilter": false,
            "bInfo": false,
            "lengthChange": false,
            "pageLength": 20
        });

        //Change Choise Option
        var selected = [];

        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            // alert(id);

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
                    $('#grade_component_edit').hide();
                    $('#pdfOptions').hide();
                }
                else if (arr_length == 1) {
                    $('#employee_view').show();
                    $('#pdfOptions').show();
                    $('#grade_component_edit').show();
                    $('#employee_grade_component_delete').show();
                }
                else {
                    $('#grade_component_edit').hide();
                    $('#pdfOptions').hide();
                    $('#employee_grade_component_delete').hide();
                }
            }

        });

        (function ($) {
            $('#grade_component_edit').hide();
            $('#employee_grade_component_delete').hide();
            $('#pdfOptions').hide();

            //Edit Employee
            $("#grade_component_edit").on('click', function (e) {
                var hr_grade_components_id = selected[0];
               if (hr_grade_components_id.length === 0) {
                    swalError("Please select a Grade Component");
                    return false;
               } else {
                   var url = '<?php echo URL::to('grade-component-form');?>';
                   var data = {
                       'hr_emp_grades_id':'{{$emp_grade_info->hr_emp_grades_id}}',
                       'salary':'{{$emp_grade_info->basic_salary}}',
                       'hr_emp_grade_name':'{{$emp_grade_info->hr_emp_grade_name}}',
                       'hr_grade_components_id':hr_grade_components_id,
                   };
                   Ladda.bind(this);
                   var load = $(this).ladda();
                   makeAjaxPostText(data,url,load).done(function(response){
                       if(response){
                           $('#medium_modal .modal-content').html(response);
                           $('#medium_modal').modal('show');
                       }
                   });
               }
            });



            //Add New Grade Component
            $(document).on('click','#newcomponent',function () {
                var url = '<?php echo URL::to('grade-component-form');?>';
                var data = {
                    'hr_emp_grades_id':'{{$emp_grade_info->hr_emp_grades_id}}',
                    'salary':'{{$emp_grade_info->basic_salary}}',
                    'hr_emp_grade_name':'{{$emp_grade_info->hr_emp_grade_name}}'
                };
                Ladda.bind(this);
                var load = $(this).ladda();
                makeAjaxPostText(data,url,load).done(function(response){
                    if(response){
                        $('#medium_modal .modal-content').html(response);
                        $('#medium_modal').modal('show');
                    }
                });
            });

            //Store Component
            $(document).on('click','#grade_component_submit',function () {
                var $form = $('#basicFormComponent');
                var data = {
                    'hr_emp_grades_id' : ('{{$emp_grade_info->hr_emp_grades_id}}')
                };
                data = $form.serialize() + '&' + $.param(data);
                var url = '{{route('store-grade-component-info')}}';
                makeAjaxPost(data, url).done(function (response) {
                    swalRedirect(window.location,'Grade Component added Successfully.','success');
                });
            });

            //Change Ratio
            $(document).on('change','#is_ratio',function () {
                if($("#is_ratio").is(':checked')){
                    $("#ratio_of_basic_div").show();  // checked
                    $('#amount').attr('readonly', true);
                    calculateAmount();
                }
                else{
                    $("#ratio_of_basic_div").hide();  // unchecked
                    $('#mount').attr('readonly', false).val(0);
                }
            });

            $(document).on('change','#ratio_of_basic',function () {
                calculateAmount();
            });

            var calculateAmount = function () {
                var ratio_of_basic = $("#ratio_of_basic").val();
                var basic_salary = ('{{$emp_grade_info->basic_salary}}');
                var amount = (ratio_of_basic/100) * basic_salary;
                $("#amount").val(amount);
            };

            // delete Grade Component
            $("#employee_grade_component_delete").on('click', function (e) {
                var hr_grade_components_id = selected;
                if (hr_grade_components_id.length === 0) {
                    swalError("Please select a Grade Component");
                    return false;
                } else {
                    swalConfirm('to Confirm Delete?').then(function (e) {
                        if(e.value){
                            var url = "{{URL::to('employee-grade-component-delete')}}";
                            var data = {hr_grade_components_id:hr_grade_components_id};
                            makeAjaxPost(data,url,null).then(function(response) {
                                swalSuccess('Delete Successfully');
                            });
                        }
                    });

                }
            });


            //Prevent Text on Money Field Salary
            $(document).on('keypress', '.input_money', function(eve) {
                if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
                    eve.preventDefault();
                }
            });

        })(jQuery);

</script>
@endsection
