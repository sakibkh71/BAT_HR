@extends('layouts.app')
@section('content')
    <style>
        table.dataTable{
            border-collapse: collapse !important;
        }
        .locked{
            background: #ffa3a3 !important;
            color: #fff;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Staff Off day summary</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('employee-off-day-summary')}}" method="post" id="OffDaySummaryForm" class="mb-4">
                            @csrf
                            <div class="row">
                                {!! __getCustomSearch('employee_list_report', $posted) !!}
                                <div class="form-group col-md-3">
                                    <label class="form-label">Month<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control hr_salary_month_name"
                                               value="{{isset($posted['off_day_month']) ? $posted['off_day_month']:''}}"
                                               id="off_day_month"
                                               data-date-format="yyyy-mm"
                                               name="off_day_month" required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label">Amount Status</label>
                                    <div class="input-group">
                                        <select class="form-control" name="amount_status" id="amountStatus">
                                            <option value="without_amount" @if(isset($posted['amount_status']) && $posted['amount_status']=='without_amount') selected @endif>Without Amount</option>
                                            <option value="with_amount" @if(isset($posted['amount_status']) && $posted['amount_status']=='with_amount') selected @endif>With Amount</option>
                                        </select>
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

    <script>

        $(function ($) {
            $("#off_day_month").datepicker( {
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                //startDate: '+0d',
                autoclose: true,
            });
        });


        $('#makepdf').click(function () {
            var form = $('#OffDaySummaryForm');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

        $('#makeXlsx').click(function () {
            Ladda.bind(this);
            var load = $(this).ladda();
            var url = "{{URL::to('employee-off-day-summary/xlsx')}}";
            var _token = "{{ csrf_token() }}";
            var data = $("#OffDaySummaryForm").serialize();

            makeAjaxPost(data, url, load).done(function (response) {
                window.location.href = './public/export/' + response.file;
                swalSuccess('Export Successfully');
            });
        });

        $('#makePdf').click(function () {
            Ladda.bind(this);
            var load = $(this).ladda();
            var form = $("#OffDaySummaryForm");
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

    </script>
@endsection