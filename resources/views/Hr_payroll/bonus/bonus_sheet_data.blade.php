@extends('layouts.app')
@section('content')
    @include('dropdown_grid.dropdown_grid')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                                <h2 style="display: inline-block">Bonus Sheet : <b style="margin-right: 20px">{!! @$employeeList[0]->bonusConfig->bonus_sheet_name !!}</b>
                                    {{__dropdown_grid($slug = 'hr_emp_bonus_add_list',$data = array(
                                                               'selected_value'=>'', 'name'=>' Manual Employee Add','addbuttonid'=>'employee_selection','attributes'=>array('class'=>'btn  btn-xs btn-primary float-right','id'=>'add_item')))}}
                                </h2>

                        <div class="ibox-tools">
                            <h2>Bonus Type: <b>{!! @$employeeList[0]->bonusConfig->bonus_type !!}</b></h2>

                        </div>

                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table id="employee_list"
                                   class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>SL No.</th>
                                    <th>ID No.</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Grade</th>
                                    <th>DoJ</th>
                                    <th>DoC</th>
                                    <th>Gross Salary</th>
                                    <th>Basic Salary</th>
                                    <th>Bonus Policy</th>
                                    <th>Entitle Bonus</th>
                                    <th class="no-display">Stamp</th>
                                    <th>Net Payable Bonus</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @if(!empty($employeeList))
                                    @foreach($employeeList as $i=>$emp)
                                        <tr class="row-select-toggle emp_id"
                                            data-id="{{$emp->employee->id}}" id="{{$emp->employee->id}}"
                                            data-bonus_id="{{$emp->hr_emp_bonus_id}}"
                                            data-eligible_based="{{$emp->bonus_eligible_based_on}}"
                                            data-bonus_based_on="{{$emp->bonus_based_on}}">
                                            <td align="center">
                                                {{($i+1)}}
                                            </td>
                                            <td>{{@$emp->employee->user_code}}</td>
                                            <td>{{$emp->employee->name}}</td>
                                            {{--<td>{{@$emp->department->departments_name}}</td>--}}
                                            <td>{{@$emp->designation->designations_name}}</td>
                                            <td>{{@$emp->grade->hr_emp_grade_name}}</td>
                                            <td>{{toDated($emp->employee->date_of_join)}}</td>
                                            <td>{{toDated($emp->employee->date_of_confirmation)}}</td>
                                            <td class="text-right min_gross">{{number_format($emp->gross_salary,1)}}</td>
                                            <td class="text-right basic_salary">{{number_format($emp->basic_salary,2)}}</td>
                                            <td>{{$emp->bonus_payable_policy?$emp->bonus_payable_policy:'Manual'}}</td>
                                            <td class="text-right"><input type="text" value="{{$emp->earn_bonus}}" class="form-control text-right entitle_amount"> </td>
                                            <td class="text-right no-display"><input type="number" value="{{$emp->stamp}}" class="form-control text-right stamp_amount"> </td>
                                            <td class="text-right payable_amount">{{number_format($emp->earn_bonus-$emp->stamp,2)}}</td>
                                            <td><button data-record_id="{{$emp->hr_emp_bonus_id}}" class="btn btn-danger btn-xs" id="delete_item"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                        </tr>
                                    @endforeach
                                    <input type="hidden" value="{!! @$employeeList[0]->hr_emp_bonus_sheet_id !!}" name="sheet_id" id="sheet_id">
                                @endif
                                </tbody>
                            </table>
                        </div>
                        @if(!empty($employeeList) && isset($employeeList[0]))
                        <form method="post" action="" id="bonus_form_submit" data-toggle="validator">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <button type="button" id="submit" class="submit_bonus_form btn btn-primary">Save</button>
                                    {{--<button type="button" id="submit_close" class="submit_bonus_form btn btn-success">Confirm & Lock</button>--}}

                                </div>

                            </div>
                        </form>
                            @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>

        $(document).on('click', '#add_item', function (e) {
            grid_modal_show($(this));
        });

        $('body').on('click', '#employee_selection', function () {
            var gridselectedItems = getSelectedItems();
            var sheet_code='{{$sheet_code}}';

            $.ajax({
               type:'get',
               data:{
                   'selected_user_ids':gridselectedItems,
                   'sheet_code':sheet_code
               },
                url:'{{url('add-employee-manually-for-bonus')}}',
                success:function(data){
                    console.log(data);
                    var edit_url = "{{URL::to('hr-emp-bonus-sheet-data')}}/"+sheet_code;
                    swalRedirect(edit_url,'Successfully Save');

                }
            });


        });

        $(document).on('click', '#delete_item', function (e) {
            e.preventDefault();
            Ladda.bind(this);
            var load = $(this).ladda();
            var bonus_record = $(this).data('record_id');
            var data = {bonus_record:bonus_record};
            var url = '<?php echo URL::to('hr-bonus-delete');?>';
                swalConfirm("Delete Selected Items").then(function (e) {
                    if (e.value) {
                        makeAjaxPost(data,url,load).done(function (response) {
                            var url2 = window.location;
                            if(response.success){
                                swalRedirect(url2,"Successfully Delete",'success');
                            }else{
                                swalWarning('Operation Failed!');
                            }
                        });
                    }else{
                        load.ladda('stop');
                    }
                });
        });


        function payable_cal(THIS) {
            var row = THIS.closest("tr");
            var entitle_amount = parseFloat(row.find(".entitle_amount").val());
            var stamp_amount = parseFloat(row.find(".stamp_amount").val());
            stamp_amount = stamp_amount?stamp_amount:0;
            var payable_amount = entitle_amount - stamp_amount;
            row.find(".payable_amount").text(payable_amount.toFixed(2) == 'NaN' ? '' : payable_amount.toFixed(2));
        }

        $(function ($) {
            $(document).on("input", ".stamp_amount,.entitle_amount", function () {
                payable_cal($(this));
            });

        });

        $(document).on('click', '.remove_row', function () {
            $row = $(this).closest('tr');
            var emp_id = $row.attr('id');

            if (emp_id) {
                swalConfirm('To Remove This').then(function (e) {
                    if (e.value) {
                        $row.remove();
                    }
                });
            }
        });


        $(document).on('click','.submit_bonus_form',function (e) {
            e.preventDefault();
            var submit_type = $(this).attr('id');
            var sheet_id = $('#sheet_id').val();
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
                bonus_amount.push($(this).val());
            });
            swalConfirm("Confirm to save this.").then(function (e) {
                if(e.value){
                    var url = "{{route('hr-bonus-sheet-update')}}";
                    var bonus_policy_type = $('#bonus_policy_type').val();
                    var hr_emp_bonus_sheet_id = $('#hr_emp_bonus_sheet_id').val();
                    var bonus_title = $('#bonus_title').val();
                    var eligible_date = $('#eligible_date').val();
                    var data = {
                        sheet_id:sheet_id,
                        submit_type:submit_type,
                        hr_emp_bonus_sheet_id: hr_emp_bonus_sheet_id,
                        bonus_policy_type: bonus_policy_type,
                        emp_id: emp_id,
                        bonus_amount: bonus_amount,
                        eligible_based: eligible_based,
                        bonus_based_on: bonus_based_on,
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