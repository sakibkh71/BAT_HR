@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Advance Loan Entry</h3>
                    </div>
                    <div class="ibox-tools">
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-8">
                                <form action="" id="loan_entry_form" method="post" autocomplete="off">
                                    <input type="hidden" name="hr_emp_loan_id" id="hr_emp_loan_id"
                                           value="{{@$row->hr_emp_loan_id}}"/>

                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="form-label">Employee<span class="required">*</span></label>
                                            {{__combo('hr_active_emp_list',array('selected_value'=> @$row->sys_users_id, 'attributes'=> array('class'=>'form-control multi','id'=>'sys_users_id','name'=>'sys_users_id')))}}
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label class="form-label">{{__lang('Loan Date')}}<span class="required">*</span></label>
                                        <div class="form-group">
                                            <div class='input-group'>
                                                <input type="text" name="loan_date" id="loan_date" class="form-control"
                                                       data-error="Please Enter Loan Date"
                                                       value="{{@$row->loan_date?$row->loan_date:date('Y-m-d')}}"
                                                       placeholder="Loan Date" required="">
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label class="form-label">Loan Type <span class="required">*</span></label>
                                        <div class="form-group">
                                            <select class="form-control" name="loan_type" required id="loan_type">
                                                <option {{@$row->loan_type=='Advance Salary'?'selected':''}} value="Advance Salary">
                                                    Advance Salary
                                                </option>
                                                <option {{@$row->loan_type=='Loan'?'selected':''}} value="Loan">Loan
                                                </option>
                                            </select>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-10">
                                        <label class="form-label">{{__lang('Loan Amount')}}<span
                                                    class="required">*</span></label>
                                        <div class="form-group">
                                            <div class='input-group'>
                                                <input type="text" name="loan_amount" id="loan_amount"
                                                       class="form-control"
                                                       data-error="Please Enter Loan Amount"
                                                       value="{{@$row->loan_amount?$row->loan_amount:''}}"
                                                       placeholder="Loan Amount" required="">
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div id="loan_area">
                                    <div class="col-md-10">
                                        <label class="form-label">Loan Duration <span class="required">*</span></label>
                                        <div class="form-group">
                                            <select class="form-control" name="loan_duration" id="loan_duration">
                                                @for($i=1;$i<=12;$i++)
                                                    <option {{@$row->loan_duration==$i?'selected':''}} value="{{$i}}">{{$i}}</option>
                                                @endfor
                                            </select>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                        <div class="col-md-10">
                                            <label class="form-label">{{__lang('Installment')}}<span
                                                        class="required">*</span></label>
                                            <div class="form-group">
                                                <div class='input-group'>
                                                    <input type="text" id="loan_installment"
                                                           class="form-control"
                                                           value="{{@$row->loan_amount?sprintf('%.2f',$row->loan_amount/$row->loan_duration):''}}"
                                                           placeholder="Installment Amount" required="">
                                                </div>
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label class="form-label">{{__lang('Note')}}</label>
                                        <div class="form-group">
                                            <div class='input-group'>
                                                <textarea name="note" id="note" class="form-control"
                                                          data-error="Please Enter Details Note"
                                                          placeholder="Loan Details">{{@$row->note?$row->note:''}}</textarea>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class='input-group'>
                                                @if(isset($row))
                                                    <button type="submit" class="btn btn-warning">Update</button>
                                                @else
                                                    <button type="submit" class="btn btn-success">Save</button>
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
                                        <td id="previous_loan_due"></td>
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

        $(document).on('keyup','#loan_amount',function () {
            var loan_amount = $(this).val();
            var loan_duration = $('#loan_duration').val();
            $('#loan_installment').val((loan_amount/loan_duration).toFixed(2));
        });
        $(document).on('change','#loan_duration',function () {
            var loan_duration = $(this).val();
            var loan_amount = $('#loan_amount').val();
            $('#loan_installment').val((loan_amount/loan_duration).toFixed(2));
        });
        $(document).ready(function () {
            var sys_users_id = '{{@$row->sys_users_id}}';
            if (sys_users_id) {
                empSalary(sys_users_id);
            }
            var loan_type = $('#loan_type').val();
            if(loan_type == 'Loan'){
                $('#loan_area').show();
            }else{
                $('#loan_duration').val(1);
                $('#loan_area').hide();
            }
        });
        $("#loan_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
        });

        $(document).on('change','#loan_type',function () {
           var loan_type = $('#loan_type').val();
           if(loan_type == 'Loan'){
               $('#loan_area').show();
           }else{
               $('#loan_duration').val(1);
               $('#loan_area').hide();
           }
        });
        $(document).on('change', '#sys_users_id', function () {
            var sys_users_id = $(this).val();
            empSalary(sys_users_id);
        });

        function empSalary(sys_users_id) {
            var url = '{{route("emp-loan-info")}}/' + sys_users_id;
            makeAjax(url, null).done(function (response) {
                $('#fixed_salary').text(response.min_gross);
                $('#pfp_amount').text(response.max_variable_salary);
                $('#previous_loan_due').text(response.previous_loan_due);
            });
        }

        $(document).on('submit', '#loan_entry_form', function (e) {
            e.preventDefault();
            var loan_type = $('#loan_type').val();
            var fixed_salary = parseFloat($('#fixed_salary').text());
            var edit_amount = "{{@$row->loan_amount}}";
            var previous_loan_due = parseFloat($('#previous_loan_due').text());
            var loan_amount = parseFloat($('#loan_amount').val());
            if (loan_type =='Advance Salary' && (loan_amount > (fixed_salary - previous_loan_due) + edit_amount)) {
                swalWarning('Advance salary can\'t greater then Fixed Salary');
            }else{
                var url = '{{route('hr-loan-entry-save')}}';
                var data = $('#loan_entry_form').serialize();
                makeAjaxPost(data, url, null).done(function (response) {
                    if (response.success) {
                        var url2 = '{{route('loan-list')}}';
                        swalRedirect(url2, 'Successfully Save');

                    }
                });
            }

        });
    </script>
@endsection