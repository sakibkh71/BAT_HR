@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{$posted['increment_type']}} Increment & Promotion Report</h2>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-primary btn-xs" style="display: none" id="view_employee_log"><i class="fa fa-eye"></i> Employee History</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('hr-increment-promotion-report')}}" method="post" id="incPromoForm" class="mb-4">
                            @csrf
                            <div class="row">
                                {!! __getCustomSearch('employee_list_report', $posted) !!}
                                <div class="form-group col-md-3">
                                    <label class="form-label">Employee Category</label>
                                        {{ __combo('hr_emp_categorys',array('selected_value'=> isset($posted['hr_emp_category']) ? $posted['hr_emp_category']:'', 'attributes'=> array('class'=>'form-control multi','id'=>'hr_emp_category','name'=>'hr_emp_category'))) }}
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="form-label">Increment Type<span class="required">*</span></label>
                                    <select name="increment_type" id="increment_type" class="form-control" required>
                                        <option value="Yearly" {{isset($posted['increment_type']) && $posted['increment_type'] =='Yearly'?'selected':''}}>Yearly</option>
                                        <option value="Special" {{isset($posted['increment_type']) && $posted['increment_type'] =='Special'?'selected':''}}>Special</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="form-label">Year<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control hr_salary_month_name"
                                               value="{{isset($posted['year']) ? $posted['year']:''}}"
                                               id="year"
                                               data-date-format="yyyy-mm"
                                               name="year" required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Filter</button>
                                    <button type="button" id="makeXlsx" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Excel</button>
                                    <button type="button" id="makePdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> pdf</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $report_data_html; ?>
                                <div class="paginate mt-3">
                                    {{ $report_data->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="edit_id" style="display: none"></div>
    <style>
        .selected{
            background-color: green;
            color: #FFF;
        }
        .selected:hover{
            background-color: green !important;
            color: #FFF;
        }
    </style>
<script>
    $(function ($) {
        $("#off_day_month").datepicker( {
            format: "yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true,
        });
    });

    $(function ($) {
        $("#year").datepicker( {
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true,
        });
    });


    $('#makeXlsx').click(function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var url = "{{URL::to('hr-increment-promotion-report/xlsx')}}";
        var _token = "{{ csrf_token() }}";
        var data = $("#incPromoForm").serialize();
        makeAjaxPost(data, url, load).done(function (response) {
            window.location.href = './public/export/' + response.file;
            swalSuccess('Export Successfully');
        });
    });

    $('#makePdf').click(function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var form = $("#incPromoForm");
        var action = form.attr('action');
        form.attr('action', action+'/pdf').attr("target","_blank");
        form.submit();
        form.attr('action', action);
        form.removeAttr('target');
    });

</script>
@endsection