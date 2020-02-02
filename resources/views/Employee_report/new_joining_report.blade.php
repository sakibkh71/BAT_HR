@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <link href="{{asset('public/css/plugins/clockpicker/clockpicker.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/clockpicker/clockpicker.js')}}"></script>
    <script src="{{asset('public/js/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('public/js/plugins/bootstrap_toggle/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('public/js/bootstrap-checkbox.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/plugins/bootstrap_toggle/bootstrap-toggle.min.css')}}">
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
                        <h2>Employee New Joining Status</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('new-joining-status')}}" method="post" id="njsListForm" class="mb-4">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Month<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control hr_salary_month_name"
                                               value="{{isset($posted['hr_join_month']) ? $posted['hr_join_month']:''}}"
                                               id="hr_join_month"
                                               data-date-format="yyyy-mm"
                                               name="hr_join_month" required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="form-group col-md-4">--}}
                                    {{--<label class="form-label">Units</label>--}}
                                        {{--{{__combo('hr_emp_salary_units',array('selected_value'=> isset($posted['hr_emp_units_id'])?$posted['hr_emp_units_id']:'', 'attributes'=> array('class'=>'form-control hr_emp_units_id multi','id'=>'hr_emp_units_id','name'=>'hr_emp_units_id[]')))}}--}}
                                {{--</div>--}}
                                {{--<div class="form-group col-md-4">--}}
                                    {{--<label class="form-label">Employee Category</label>--}}
                                        {{--{{__combo('hr_emp_categorys_multi',array('selected_value'=> isset($posted['hr_emp_categorys'])?$posted['hr_emp_categorys']:'', 'attributes'=> array('class'=>'form-control hr_emp_categorys multi','id'=>'hr_emp_categorys','name'=>'hr_emp_categorys[]')))}}--}}
                                {{--</div>--}}

                               {{-- __getCustomSearch('new_joining_status_report', $posted) --}}
                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Filter</button>
                                    <button type="button" id="makepdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $report_data_html; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function ($) {
            $("#hr_join_month").datepicker( {
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                //startDate: '+0d',
                autoclose: true,
            });
        });

        $('#makepdf').click(function () {
            var form = $('#njsListForm');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });
    </script>
@endsection