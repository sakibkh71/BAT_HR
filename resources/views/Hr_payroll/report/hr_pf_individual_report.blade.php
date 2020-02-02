@extends('layouts.app')
@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox">
                <div class="ibox-title">
                    <h3>Individual PF Report Sheet</h3>
                </div>
                <div class="ibox-content">
                    <div class="col-sm-12">
                        <form id="salary_sheet_form" action="{{route('hr-pf-individual-report')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong><span class="required">*</span> Start Month
                                            </strong></label>
                                        <div class="col-sm-12 input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="salary_month" id="salary_month" class="form-control" value="<?php echo isset($_POST['salary_month']) ? $_POST['salary_month'] : ''; ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong><span class="required">*</span> End Month
                                            </strong></label>
                                        <div class="col-sm-12 input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="end_month"  id="end_month" required class="form-control" value="<?php echo isset($_POST['end_month']) ? $_POST['end_month'] : ''; ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label"><strong>Select Employee</strong></label>
                                        <div class="col-sm-12">
                                            <?php
                                                $selected_value = '';
                                                if (isset($_POST['id'])) { $selected_value = $_POST['id']; }
                                            ?>
                                            {{__combo('pf_employee_list', array('selected_value'=> $selected_value, 'attributes'=>['class'=>'multi']))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter </button>
                                    <button type="button" id="makepdf" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(isset($opening_balance) && isset($employeewise_pfinfo))
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="checkbox-clickable table table-bordered table-striped table-hover apsis_table">
                                <thead>
                                    <tr>
                                        <th>SL No.</th>
                                        <th>Month</th>
                                        <th>Company Amount</th>
                                        <th>Employee Amount</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($opening_balance) )
                                        <tr>
                                            <td align="center"></td>
                                            <td >Opening Balance</td>
                                            <td class="text-right">{{number_format($opening_balance->amount_company,2)}}</td>
                                            <td class="text-right">{{number_format($opening_balance->amount_employee,2)}}</td>
                                            <td class="text-right">{{number_format(($opening_balance->amount_company+$opening_balance->amount_employee),2)}}</td>
                                        </tr>
                                        @php($gTotal = $opening_balance->amount_company + $opening_balance->amount_employee)
                                    @endif
                                   @php($grandCompanyTotal = !empty($opening_balance) ? $opening_balance->amount_company : 0)
                                   @php($grandEmployeeTotal = !empty($opening_balance) ? $opening_balance->amount_employee : 0)

                                    @if(!empty($employeewise_pfinfo))
                                        @foreach($employeewise_pfinfo as $i=>$emp)
                                            <tr id="{{$emp->sys_users_id}}">
                                                <td align="center"> {{++$i}} </td>
                                                <td>{{$emp->hr_salary_month_name}}</td>
                                                <td class="text-right">{{number_format($emp->pf_amount_company,2)}}</td>
                                                <td class="text-right">{{number_format($emp->pf_amount_employee,2)}}</td>
                                                <td class="text-right">{{number_format(($emp->pf_amount_company+$emp->pf_amount_employee),2)}}</td>
                                            </tr>
                                            @php($grandCompanyTotal += $emp->pf_amount_company)
                                            @php($grandEmployeeTotal += $emp->pf_amount_employee)
                                            @php($gTotal += $emp->pf_amount_company + $emp->pf_amount_employee)
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td align="center"></td>
                                        <td > Closing Balance</td>
                                        <td class="text-right">{{number_format($grandCompanyTotal,2)}}</td>
                                        <td class="text-right">{{number_format($grandEmployeeTotal,2)}}</td>
                                        <td class="text-right">{{number_format(($grandCompanyTotal+$grandEmployeeTotal),2)}}</td>
                                    </tr>
                                    @php($gTotal += $grandCompanyTotal + $grandEmployeeTotal)
                                    <tr>
                                        <td> </td>
                                        <td> </td>
                                        <td> Total Balance</td>
                                        <td class="text-right">{{number_format($grandCompanyTotal+$grandEmployeeTotal,2)}}</td>
                                        <td class="text-right"> {{number_format($gTotal,2)}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#salary_month').datetimepicker({
        'format': 'YYYY-MM'
    });
    $('#end_month').datetimepicker({
        'format': 'YYYY-MM'
    });
    $(document).on('change', '#pf_employee_list', function () {
        alert('dsgd')
    });
    $('#makepdf').click(function () {
        var form = $('#salary_sheet_form');
        var action = form.attr('action');
        form.attr('action', action + '/pdf').attr("target", "_blank");
        form.submit();
        form.attr('action', action);
        form.removeAttr('target');
    });
</script>
@endsection
