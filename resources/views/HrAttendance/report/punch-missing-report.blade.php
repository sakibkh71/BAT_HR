@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2> {{ucwords($posted['report_type'])}} {{ucwords(str_replace("_"," ", $posted['punch_type']))}}  {{__lang('Report')}} </h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('punch-missing-report')}}" method="post" id="punchMissingForm" class="mb-4">
                            @csrf
                            @php($report_month = isset($posted['month']) ? $posted['month']:'')
                            <div class="row">
                                {!! __getCustomSearch('monthly-attendance-sheet', @$posted) !!}

                                <div class="form-group col-md-3">
                                    <label class="form-label">{{__lang('Report Type')}}<span class="required">*</span></label>
                                        <select name="report_type" id="report_type" class="form-control multi">
                                            <option value="daily" {{isset($posted['report_type']) && $posted['report_type'] == 'daily' ? 'selected':''}}>Daily</option>
                                            <option value="monthly"  {{isset($posted['report_type']) && $posted['report_type'] == 'monthly' ? 'selected':''}}>Monthly</option>
                                        </select>
                                </div>

                                <div class="form-group col-md-3" id="daily" style="{{isset($posted['report_type']) && $posted['report_type'] == 'daily' ? 'display:block':'display:none'}}">
                                    <label class="form-label">{{__lang('Day')}} <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control month"
                                               value="{{$posted['day']}}"
                                               id="day"
                                               data-date-format="yyyy-mm"
                                               name="day"/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3" id="monthly" style="{{isset($posted['report_type']) && $posted['report_type'] == 'monthly' ? 'display:block':'display:none'}}">
                                    <label class="form-label">{{__lang('Month')}}</label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control month"
                                               value="{{$report_month}}"
                                               id="month"
                                               data-date-format="yyyy-mm"
                                               name="month"/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="form-label">{{__lang('Punch Type')}} <span class="required">*</span></label>
                                        <select name="punch_type" id="punch_type" class="form-control multi">
                                            <option value="inpunch_missing" {{isset($posted['punch_type']) && $posted['punch_type'] == 'inpunch_missing' ? 'selected':''}}>Inpunch Missing</option>
                                            <option value="outpunch_missing"  {{isset($posted['punch_type']) && $posted['punch_type'] == 'outpunch_missing' ? 'selected':''}}>Outpunch Missing </option>
                                        </select>
                                </div>

                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> {{__lang('Filter')}}</button>
                                    <button type="button" id="makeXlsx" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i>{{__lang('Excel')}}</button>
                                    <button type="button" id="makepdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> {{__lang('PDF')}}</button>
                                </div>

                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12" style="overflow: scroll">
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
<style>
    .child-table{
        border: none;
        width: 100%;
        border-collapse: collapse;
    }
    .child-table td{
        border: none;
        border-bottom: 1px solid #ddd;
        padding: 0;
    }
    .child-table tr:last-child td{
        border-bottom: none;
    }
</style>
    <script>
        $("#month").datepicker( {
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            //startDate: '+0d',
            autoclose: true,
        });

        $("#day").datepicker( {
            format: "yyyy-mm-dd",
            autoclose: true,
        });

        $('#makepdf').click(function () {
            var form = $('#punchMissingForm');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

        $('#makeXlsx').click(function () {
            Ladda.bind(this);
            var load = $(this).ladda();
            var url = "{{URL::to('punch-missing-report/xlsx')}}";
            var _token = "{{ csrf_token() }}";
            var data = $("#punchMissingForm").serialize();
            makeAjaxPost(data, url, load).done(function (response) {
                window.location.href = './public/export/' + response.file;
                swalSuccess('Export Successfully');
            });
        });

        $(document).on('change', '#day', function (e) {
            var val = $(this).val();
            if (val !=''){
                $('#month').val('');
            }
        });

        $(document).on('change', '#month', function (e) {
            var val = $(this).val();
            if (val !=''){
                $('#day').val('');
            }
        })

        $(document).on('change', '#report_type', function (e) {
            var val = $(this).val();
            if (val =='daily'){
                $('#monthly').hide();
                $('#daily').show();
            }else{
                $('#monthly').show();
                $('#daily').hide();
            }
        })

     </script>
@endsection