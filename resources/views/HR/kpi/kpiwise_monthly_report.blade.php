@extends('layouts.app')
@section('content')
    <style>
        .row-select-toggle{
            cursor: default;
        }
        .dropdown-item {
            margin: 0;
            padding: 5px;
        }

    </style>
    <script src="{{asset('public/js/plugins/bootstrap_toggle/bootstrap-toggle.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/plugins/bootstrap_toggle/bootstrap-toggle.min.css')}}">
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">

                <div class="ibox-title">
                    <h2>KPI Monthly Summary</h2>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ibox">
                                <div class="ibox-content  bg-white">
                                    <form action="{{route('monthly-kpi-wise-achievement')}}" method="post" id="attendanceForm">
                                        @csrf
                                        <div class="row">
                                            {!! __getCustomSearch('kpi-monthly-summary', $posted) !!}
                                            <div class="col-md-3">
                                                <label class="font-normal"><strong>{{__lang('Month From')}} </strong><span class="required">*</span></label>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" name="month_from" class="form-control" id="month_from" data-error="Please select Date" value="{{!empty($month_from)?$month_from:''}}" placeholder="YYYY-MM"  required="" autocomplete="off">
                                                    </div>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal"><strong>{{__lang('Month To')}} </strong><span class="required">*</span></label>
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" name="month_to" class="form-control" id="month_to" data-error="Please select Date" value="{{!empty($month_to)?$month_to:''}}" placeholder="YYYY-MM"  required="" autocomplete="off">
                                                    </div>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group" style="margin-top:28px;">
                                                    <button class="btn btn-primary btn" name="submit" type="submit">{{__lang('Search')}}</button>
                                                    <button type="button" id="makeExcel" class="btn btn-success btn"><i class="fa fa-file-excel-o"></i> {{__lang('Excel')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">

                                        @if(!empty($result_ary) && !empty($property_ary))
                                            <table class="table table-bordered" id="report_view">
                                                <thead>
                                                <tr>
                                                    <th rowspan="3" class="text-center">Employee Name</th>
                                                    <th rowspan="3" class="text-center">Designation</th>
                                                    <th rowspan="3" class="text-center">Distributor Point</th>
                                                    @if(!empty($month_ary))
                                                        <?php
                                                            $count_p_ary = count($property_ary)*3;
                                                        ?>
                                                        @foreach($month_ary as $key=>$prows)
                                                            <th colspan="{{$count_p_ary}}" class="text-center">{{date('M, Y',strtotime($key))}}</th>
                                                        @endforeach
                                                    @endif
                                                </tr>
                                                <tr>
                                                    @foreach($month_ary as $key=>$prows)
                                                        @foreach($property_ary as $key_property_ary=>$val_property_ary)
                                                            <th colspan="3" class="text-center">{{$key_property_ary}}</th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach($month_ary as $key=>$prows)
                                                        @foreach($property_ary as $key_property_ary=>$val_property_ary)
                                                            <th class="text-center">Target</th>
                                                            <th class="text-center">Achievement</th>
                                                            <th class="text-center">Achievement Ratio(%)</th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($result_ary as $key=>$val)
                                                    <tr>
                                                        <td>{{$val['name']}}</td>
                                                        <td>{{$val['designation']}}</td>
                                                        <td>{{$val['point_name']}}</td>

                                                        @foreach($month_ary as $month_info)
                                                            @if(array_key_exists($month_info, $val))
                                                                @foreach($val[$month_info] as $month_key => $month_val)
                                                                    <td class="text-right">{{number_format($month_val['target'], 2)}}</td>
                                                                    <td class="text-right">{{number_format($month_val['achieve'], 2)}}</td>
                                                                    <td class="text-right">{{number_format($month_val['achieve_ratio'], 2)}}%</td>
                                                                @endforeach
                                                            @else
                                                                @php(dd($val))
                                                                <td>--</td>
                                                                <td>--</td>
                                                                <td>--</td>

                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#report_view').dataTable({
            scrollX : true
        });

        $(document).ready(function(){

            $("#month_from").datepicker( {
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true,
            });

            $("#month_to").datepicker( {
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true,
            });
        });

        $("#makeExcel").click(function(){
            var $form = $('#attendanceForm');
            var data={};
            data = $form.serialize() + '&' + $.param(data);
            var month_from=$("#month_from").val();
            var month_to=$("#month_to").val();

            if(month_from=='' || month_to==''){
                swalError('Please Provide the Required Values');
            }else {
                var url = '{{route("monthly-kpi-wise-achievement",['type'=>'excel'])}}';
                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    success: function (data) {
                        console.log(data);
                        window.location.href = './public/export/' + data.file;
                        swalSuccess('Export Successfully');
                    }
                });
            }
        });


        $(".xl_download").on('click', function (e) {
            e.preventDefault();
            Ladda.bind('xl_download');
            var load = $('xl_download').ladda();

            swalConfirm('Confirm to download excel?').then(function (e) {
                if(e.value){
                    var url = "{{URL::to('kpi-monthly-summary-xl')}}";
                    var value_data = $('.result_ary').val();
                    var all_month = $('.all_month').val();
                    var data = {value_data: value_data, all_month: all_month};
                    makeAjaxPost(data,url,load).then(function(response) {

                        if(response.code == 500){
                            swalError(response.msg);
                        }
                        else{
                            window.location.href = './public/export/' + response.file;
                            swalSuccess(response.msg);
                        }
                    });
                }
            });
        });
    </script>
@endsection
