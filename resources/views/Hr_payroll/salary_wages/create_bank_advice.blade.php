@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Generate Bank Advice</h3>
                    </div>
                    <div class="ibox-content">

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-lg-6" id="cluster_info">

                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>@if($type=='salary') Salary Month @elseif($type=='bonus') Bonus Sheet Name @endif</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">@if($type=='salary'){{$salary_sheet->salary_month}} @elseif($type=='bonus') {{$bonus_sheet->bonus_sheet_name}}  @endif</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>Sheet Code:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">@if($type=='salary'){{$salary_sheet->hr_emp_salary_sheet_code}} @elseif($type=='bonus') {{$bonus_sheet->bonus_sheet_code}} @endif</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>Distributor Points:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">@if($type=='salary'){{$salary_sheet->distributor_points}} @elseif($type=='bonus') {{$bonus_sheet->distributor_points}} @endif</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="col-lg-6" id="cluster_info">

                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>Sheet Type:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">@if($type=='salary')  {{$salary_sheet->salary_sheet_type?$salary_sheet->salary_sheet_type:'N/A'}} @elseif($type=='bonus') {{$bonus_sheet->bonus_type?$bonus_sheet->bonus_type:'N/A'}}  @endif</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>Selected FF:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">@if($type=='salary') {{$salary_sheet->selected_designations?$salary_sheet->selected_designations:'All'}}  @elseif($type=='bonus'){{$bonus_sheet->selected_designations?$bonus_sheet->selected_designations:'All'}}  @endif</dd>
                                        </div>
                                    </dl>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>FF Type</th>
                                            <th>Number of FF</th>
                                            <th>Salary Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @php($total_emp=$total_salary=0)

                                    @if(!empty($employeeList))

                                        @foreach($employeeList as $emp)
                                            <?php
                                                $total_emp += $emp->total_employee;
                                                if($type=='salary'){
                                                    $total_salary += $emp->net_salary;
                                                }
                                                else if($type=='bonus'){
                                                    $total_salary+=$emp->net_bonus;
                                                }

                                            ?>
                                            <tr>
                                                <td>{{$emp->designations_name}}</td>
                                                <td class="text-right">{{$emp->total_employee}}</td>
                                                <td class="text-right">@if($type=='salary'){{apsis_money($emp->net_salary)}} @elseif($type=='bonus') {{apsis_money($emp->net_bonus)}} @endif</td>
                                            </tr>
                                            @endforeach
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-right">{{$total_emp}}</th>
                                            <td class="text-right">{{apsis_money($total_salary)}}</td>
                                        </tr>
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <form action="" id="bank_advice_form" method="post" autocomplete="off">

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{__lang('Bank Name')}} <span class="required">*</span></label>
                                        <div class="form-group">
                                            @if($type=='salary')
                                            {!! __combo('bank',array('selected_value'=>explode(',',@$salary_sheet->banks_id),'attributes'=>array('class'=>'from-control multi','id'=>'banks_id','required'=>'required'))) !!}
                                            @elseif($type=='bonus')
                                                {!! __combo('bank',array('selected_value'=>explode(',',@$bonus_sheet->banks_id),'attributes'=>array('class'=>'from-control multi','id'=>'banks_id','required'=>'required'))) !!}
                                            @endif

                                                <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{__lang('Bank Branch')}} <span
                                                    class="required">*</span></label>
                                        <div class="form-group">
                                            @if($type=='salary')
                                            {!! __combo('bank_branch',array('selected_value'=>explode(',',@$salary_sheet->branch_id),'attributes'=>array('class'=>'from-control multi','name'=>'branchs_id','id'=>'branch_name','required'=>'required'))) !!}
                                            @elseif($type=='bonus')
                                                {!! __combo('bank_branch',array('selected_value'=>explode(',',@$bonus_sheet->branch_id),'attributes'=>array('class'=>'from-control multi','name'=>'branchs_id','id'=>'branch_name','required'=>'required'))) !!}

                                            @endif
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Date<span class="required">*</span> </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="bank_advice_date" value="" placeholder=""
                                                   id="bank_advice_date" required
                                                   class="form-control">
                                        </div>

                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Bank A/C No.<span class="required">*</span> </label>
                                        <input type="text" name="bank_ac_no" value="" placeholder=""
                                               id="bank_ac_no" required
                                               class="form-control">
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Bank Advice Note <span class="required">*</span>
                                        </label>
                                        <textarea name="advice_note" value="" placeholder=""
                                                  id="advice_note" required
                                                  class="form-control"></textarea>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Authorize Note
                                        </label>
                                        <textarea name="authorize_note" value="" placeholder=""
                                                  id="authorize_note"
                                                  class="form-control"></textarea>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="total_amount" id="total_amount" value="{{$total_salary}}">
                                <input type="hidden" name="number_of_employees" id="number_of_employees" value="{{$total_emp}}">

                                @if($type=='salary')
                                <input type="hidden" name="bank_advice_ref" id="bank_advice_ref" value="{{$salary_sheet->hr_emp_salary_sheet_code}}">
                                <input type="hidden" name="hr_emp_salary_sheet_id" id="hr_emp_salary_sheet_id" value="{{$salary_sheet->hr_emp_salary_sheet_id}}">
                               @elseif($type=='bonus')

                                <input type="hidden" name="bank_advice_ref" id="bank_advice_ref" value="{{$bonus_sheet->bonus_sheet_code}}">
                                 <input type="hidden" name="hr_emp_bonus_sheet_id" id="hr_emp_bonus_sheet_id" value="{{$bonus_sheet->hr_emp_bonus_sheet_id}}">

                               @endif
                               <input type="hidden" name="operation_type" id="operation_type" value="{{$type}}" >
                                <button type="submit" id="bank_advice_submit" class="btn btn-primary">Confirm</button>


                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        $("#bank_advice_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
        });
        /*
        * Get Branch List Depend on Banks ID
        -------------------------------------*/
        $(document).on('change', '#banks_id', function () {
            var id = $(this).val();
            var url = '<?php echo URL::to('get-branch-list');?>/' + id;
            var data = {'id': id};

            makeAjaxPost(data, url).done(function (response) {
                var options = '<option value="">Select Branch</option>';
                if (response.success && response.data.length > 0) {
                    jQuery.each(response.data, function (i, val) {
                        options += '<option value="' + val.bank_branchs_id + '">' + val.bank_branch_name + '</option>';
                    });
                }
                $('#branch_name').html(options);
                $('#branch_name').multiselect("rebuild");
            });
        });
        $(document).on('submit', '#bank_advice_form', function (e) {
            e.preventDefault();
            var url = '{{route('hr-salary-sheet-bank-advice-save')}}';
            var data = $('#bank_advice_form').serialize();
            makeAjaxPost(data, url, null).done(function (response) {
                @if($type=='salary')
                var url2 = '{{route('hr-salary-disbursement')}}';
                @elseif($type=='bonus')
                var url2 = '{{route('hr-emp-bonus-sheet')}}';
                @endif
                if (response.success==true) {
                    swalRedirect(url2, 'Successfully Save');
                }else{
                    swalRedirect(url2, response.success,'warning');
                }
            });
        });
    </script>
@endsection