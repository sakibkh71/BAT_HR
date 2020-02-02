@extends('layouts.app')
@section('content')
    {{--<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">--}}
    {{--<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>--}}
    {{--<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>--}}
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Bonus Sheet Preparation</h2>
                        <div class="ibox-tools">

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="ibox">

                                    <form method="post" action="{{route('hr-emp-bonus-generate')}}" id="bonus_form"
                                          data-toggle="validator">
                                        <div class="row">
                                            @csrf
                                            {!! __getCustomSearch('eid-bonus-generate', @$posted) !!}
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Bonus Eligible Date<span
                                                            class="required">*</span> </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                    <input required type="text" name="eligible_date" id="eligible_date"
                                                           value="{{isset($eligible_date)?$eligible_date:date('Y-m-d')}}"
                                                           class="form-control"/>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Bonus Sheet<span class="required">*</span></label>
                                                    {{__combo('bonus_sheet',array('selected_value'=>@$posted['bonus_sheet_code'],'attributes'=>array('required'=>'required','name'=>'bonus_sheet_code','class'=>'form-control multi','id'=>'bonus_sheet_code')))}}
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Policy Type<span class="required">*</span></label>
                                                <div class="input-group">
                                                    <select required class="form-control" id="bonus_policy_type" name="bonus_policy_type">
                                                        <option {{isset($posted['bonus_policy_type'])&&$posted['bonus_policy_type']=='Company Policy'?'selected':''}} value="Company Policy">Company Policy</option>
                                                        <option {{isset($posted['bonus_policy_type'])&&$posted['bonus_policy_type']=='Manual'?'selected':''}} value="Manual">Manual</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 manual no-display">
                                                <label class="form-label">Bonus Eligible Based On<span class="required">*</span></label>
                                                <div class="input-group">
                                                    <select required class="form-control" id="bonus_eligible_based_on" name="bonus_eligible_based_on">
                                                        <option {{isset($bonus_eligible_based_on)&&$bonus_eligible_based_on=='date_of_join'?'selected':''}} value="date_of_join">Date of Join</option>
                                                        <option {{isset($bonus_eligible_based_on)&&$bonus_eligible_based_on=='date_of_confirmation'?'selected':''}} value="date_of_confirmation">Date of Confirmation</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 manual no-display">
                                                <label class="form-label">Bonus Based On<span class="required">*</span></label>
                                                <div class="input-group">
                                                    <select required class="form-control" id="bonus_based_on" name="bonus_based_on">
                                                        <option {{isset($bonus_based_on)&&$bonus_based_on=='gross'?'selected':''}} value="gross">Gross</option>
                                                        <option {{isset($bonus_based_on)&&$bonus_based_on=='basic'?'selected':''}} value="basic">Basic</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label class="form-label"></label>
                                                <div class="input-group">
                                                    <button id="btn_add_employee_list" type="submit"
                                                            class="btn btn-success"><i class="fa fa-search"></i>
                                                        Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                            @if(!empty($employeeList))
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" style="height: 400px; overflow: scroll">
                                        <table id="employee_list"
                                               class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th>SL No.</th>
                                                <th>ID No.</th>
                                                <th>Name</th>
                                                {{--<th>Department</th>--}}
                                                <th>Designation</th>
                                                <th>Grade</th>
                                                <th>DoJ</th>
                                                <th>Gross Salary</th>
                                                <th>Basic Salary</th>
                                                <th>Bonus Policy</th>
                                                <th>Entitle Bonus</th>
                                                {{--<th>Stamp</th>--}}
                                                {{--<th>Net Payable Bonus</th>--}}
                                                {{--<th></th>--}}
                                            </tr>

                                            </thead>
                                            <tbody>
                                            @php($total_earn=$total_stamp=$total_payable=$i=0)
                                            @foreach($employeeList as $emp)
                                                @if($emp->earn_bonus>0)
                                                    <?php
                                                    $i++;
                                                    $stamp_amount = $emp->earn_bonus>=10000?10:'0';
                                                    $payable_bonus = isset($emp->payable_bonus)?($emp->payable_bonus-$stamp_amount):($emp->earn_bonus-$stamp_amount);
                                                    $total_earn += $emp->earn_bonus;
                                                    $total_stamp += $stamp_amount;
                                                    $total_payable += $payable_bonus;
                                                    ?>
                                                    <tr class="row-select-toggle emp_id"
                                                        data-id="{{$emp->id}}" id="{{$emp->id}}"
                                                        data-bonus_id="{{isset($emp->hr_emp_bonus_id)?$emp->hr_emp_bonus_id:''}}"
                                                        data-eligible_based="{{$emp->bonus_eligible_based_on}}"
                                                        data-bonus_based_on="{{$emp->bonus_based_on}}">
                                                        <td align="center">
                                                            {{($i)}}
                                                        </td>
                                                        <td>{{$emp->user_code}}</td>
                                                        <td>{{$emp->name}}</td>
                                                        {{--                                                    <td>{{@$emp->employeeDepartment->departments_name}}</td>--}}
                                                        <td>{{@$emp->employeeDesignation->designations_name}}</td>
                                                        <td>{{@$emp->employeeGrade->hr_emp_grade_name}}</td>
                                                        <td>{{toDated($emp->date_of_join)}}</td>

                                                        <td class="text-right min_gross" data-amount="{{$emp->min_gross}}">{{number_format($emp->min_gross,1)}}</td>
                                                        <td class="text-right basic_salary" data-amount="{{$emp->basic_salary}}">{{number_format($emp->basic_salary,2)}}</td>
                                                        <td>{!! $emp->bonus_policy !!}</td>
                                                        <td class="text-right entitle_amount" data-amount="{{$emp->earn_bonus}}">{{number_format($emp->earn_bonus,2)}}</td>
                                                        {{--                                                    <td class="text-right stamp_amount" data-amount="{{$stamp_amount}}">{{$stamp_amount}}</td>--}}
                                                        {{--<td class="text-right payable_amount">{{number_format($payable_bonus,2)}}</td>--}}
                                                        {{--<td class="text-right"><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash"></i> </button> </td>--}}
                                                    </tr>
                                                @endif
                                            @endforeach

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td class="text-right font-bold" colspan="9">Total</td>
                                                <td class="text-right font-bold">{{number_format($total_earn,2)}}</td>
                                                {{--                                            <td class="text-right font-bold">{{number_format($total_stamp,2)}}</td>--}}
                                                {{--                                            <td class="text-right font-bold">{{number_format($total_payable,2)}}</td>--}}
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                </div>
                                        <div class="col-md-12">
                                            @if(($posted))
                                                <form method="post" action="" id="bonus_form_submit" data-toggle="validator">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-3">
                                                            <label class="form-label"></label>
                                                            <div class="input-group">
                                                                <button id="submit_bonus_form" type="submit"
                                                                        class="btn btn-success">Save Bonus</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                            @endif
                                </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // $('#employee_list').dataTable();
        $(function ($) {

            @if((@$posted['bonus_policy_type'])=='Manual')
                $('.manual').show();
            @endif
        });
        $('#bonus_policy_type').on('change',function () {
            var bonus_policy_type = $(this).val();
            if(bonus_policy_type == 'Manual'){
                $('.manual').show();
            }else{
                $('.manual').hide();
            }
        });
        $('#eligible_date').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });



        $(document).on('submit','#bonus_form_submit',function (e) {
            e.preventDefault();
            var emp_id = [];
            var bonus_id = [];
            var eligible_based = [];
            var bonus_based_on = [];
            var bonus_amount = [];
            var stamp_amount = [];
            $('.emp_id').each(function (i, v) {
                emp_id.push($(this).data('id'));
            });
            $('.emp_id').each(function (i, v) {
                bonus_id.push($(this).data('bonus_id'));
            });
            $('.emp_id').each(function (i, v) {
                eligible_based.push($(this).data('eligible_based'));
            });
            $('.emp_id').each(function (i, v) {
                bonus_based_on.push($(this).data('bonus_based_on'));
            });
            $('.entitle_amount').each(function (i, v) {
                bonus_amount.push($(this).data('amount'));
            });
            $('.stamp_amount').each(function (i, v) {
                stamp_amount.push($(this).data('amount'));
            });
            swalConfirm("Confirm to save this.").then(function (e) {
                if(e.value){
                    var url = "{{route('hr-bonus-submit')}}";
                    var bonus_policy_type = $('#bonus_policy_type').val();
                    var bonus_sheet_code = $('#bonus_sheet_code').val();
                    var bonus_title = $('#bonus_title').val();
                    var eligible_date = $('#eligible_date').val();
                    var data = {
                        bonus_sheet_code: bonus_sheet_code,
                        bonus_policy_type: bonus_policy_type,
                        emp_id: emp_id,
                        bonus_amount: bonus_amount,
                        eligible_based: eligible_based,
                        bonus_based_on: bonus_based_on,
                        stamp_amount: stamp_amount,
                        eligible_date: eligible_date,
                        bonus_id: bonus_id,
                    };
                    makeAjaxPost(data,url,null).done(function (response) {
                        if(response.success){
                            swalRedirect('{{route('hr-emp-bonus-sheet')}}');
                        }

                    });
                }

            });

        });
    </script>
@endsection