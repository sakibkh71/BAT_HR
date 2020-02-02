@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Salary Deduction Entry</h3>
                    </div>
                    <div class="ibox-tools">
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-8">
                                <form action="" id="deduction_entry_form" method="post" autocomplete="off">
                                    <input type="hidden" name="hr_emp_deduction_id" id="hr_emp_deduction_id"
                                           value="{{@$row->hr_emp_salary_deduction_id}}"/>

                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="form-label">Employee<span class="required">*</span></label>
                                            {{__combo('hr_active_emp_list',array('selected_value'=> @$row->sys_users_id, 'attributes'=> array('class'=>'form-control multi','id'=>'sys_users_id','name'=>'sys_users_id')))}}
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label class="form-label">{{__lang('Deduction Date')}}<span class="required">*</span></label>
                                        <div class="form-group">
                                            <div class='input-group'>
                                                <input type="text" name="deduction_date" id="deduction_date" class="form-control"
                                                       data-error="Please Enter Deduction Date"
                                                       value="{{@$row->deduction_date?$row->deduction_date:date('Y-m-d')}}"
                                                       placeholder="Deduction Date" required="" readonly>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-10">
                                        <label class="form-label">{{__lang('Deduction Amount')}}<span
                                                    class="required">*</span></label>
                                        <div class="form-group">
                                            <div class='input-group'>
                                                <input type="text" name="deduction_amount" id="deduction_amount"
                                                       class="form-control"
                                                       data-error="Please Enter Deduction Amount"
                                                       value="{{@$row->deduction_amount?$row->deduction_amount:''}}"
                                                       placeholder="Deduction Amount" required="">
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-10">
                                        <label class="form-label">{{__lang('Note')}}</label>
                                        <div class="form-group">
                                            <div class='input-group'>
                                                <textarea name="note" id="note" class="form-control"
                                                          data-error="Please Enter Details Note"
                                                          placeholder="Deduction Details">{{@$row->note?$row->note:''}}</textarea>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @php $for_update = (isset($row)) ? 1 : 0; @endphp
                                        <input type="hidden" id="for_update" value="{{$for_update}}">
                                        <div class="form-group">
                                            <div class='input-group'>
                                                @if(isset($row))
                                                    <button type="submit" class="btn btn-warning">Update</button>
                                                @else
                                                    <button type="submit" class="btn btn-success submit_btn">Save</button>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-4">
                                <h3>Salary Structure</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="50%">Fixed Salary</th>
                                        <td id="fixed_salary"></td>
                                    </tr>
                                    <tr>
                                        <th>PfP Salary</th>
                                        <td id="pfp_amount"></td>
                                    </tr>
                                    <tr>
                                        <th>Previous Due</th>
                                        <td id="previous_deduction_due"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var sys_users_id = '{{@$row->sys_users_id}}';
            if (sys_users_id) {
                empSalary(sys_users_id);
            }
        });
        $("#deduction_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
        });
        $(document).on('change', '#sys_users_id', function () {
            var sys_users_id = $(this).val();
            empSalary(sys_users_id);
        });

        function empSalary(sys_users_id) {
            $('.submit_btn').prop('disabled', false);
            var url = '{{route("emp-deduction-info")}}/' + sys_users_id;
            makeAjax(url, null).done(function (response) {
                $('#fixed_salary').text(response['emp_info'].min_gross);
                $('#pfp_amount').text(response['emp_info'].max_variable_salary);
                $('#previous_deduction_due').text(response['emp_info'].previous_loan_due);
                if($('#for_update').val() == 0) {
                    if (response['deduction_check'] != 0) {
                        $('.submit_btn').prop('disabled', true);
                        swalError("This employee has already been fined once <br> Date: "+response['deduction_check'].deduction_date+" ||  Amount: "+response['deduction_check'].deduction_amount);
                    }
                }
            });
        }

        $(document).on('submit', '#deduction_entry_form', function (e) {
            e.preventDefault();
            if($('#sys_users_id').val() == ''){
                swalError("Please, Select Employee"); return false;
            }
            var deduction_amount = parseFloat($('#deduction_amount').val());
            if(deduction_amount == ''){
                swalError("Please, Enter Amount"); return false;
            }

            var url = '{{route('hr-deduction-entry-save')}}';
            var data = $('#deduction_entry_form').serialize();
            makeAjaxPost(data, url, null).done(function (response) {
                if (response.success) {
                    var url2 = '{{route('deduction-list')}}';
                    swalRedirect(url2, 'Successfully Save');

                }
            });

        });
    </script>
@endsection