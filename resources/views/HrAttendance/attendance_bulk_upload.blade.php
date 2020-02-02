@extends('layouts.app')
@section('content')
    <script src="{{asset('public/js/plugins/bootstrap_toggle/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('public/js/bootstrap-checkbox.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/plugins/bootstrap_toggle/bootstrap-toggle.min.css')}}">
    <style>
        .ovhwrap button{ overflow: hidden}
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{$title}}  </h2>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('attendance-bulk-upload-store')}}" method="post"  class="form master-form validator" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Upload File (CSV/Excel)</label>
                                        <input type="file" name="select_file" value="" class="form-control" placeholder="Select File (CSV)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-4">
                                        <button class="submit_button btn btn-primary" type="submit">Upload</button>

                                        <a href="{{url('/public/documents/attendance/log_sample_file.xlsx')}}" class="btn btn-success" download="Sample Log File"> Download Sample File</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title  bg-white">
                        <h5> {{__lang('Device Data')}}</h5>
                    </div>
                    <div class="ibox-content  bg-white">
                        <form action="" method="post"  class="form master-form validator" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="font-normal"><strong>{{__lang('Date')}} </strong><span class="required">*</span></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input  type="text" name="day_is" id="day_is" class="form-control dateRange" data-error="Please select start time" value="{{!empty($attendance->day_is)?$attendance->day_is:old('day_is')}}" placeholder="Date"  required="" autocomplete="off">
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group ovhwrap">
                                        <label class="font-normal"><strong>{{__lang('Select Employee ')}} </strong><span class="required">*</span></label>
                                        {{ __combo('employee_list',array('selected_value'=> '', 'attributes'=> array('class'=>'form-control multi','id'=>'employee_id','name'=>'employee_id[]'))) }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-4" style="padding-top: 5px">
                                        <button class="submit_button btn btn-primary" type="submit">Sync</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="ibox-content  bg-white">
                        <table class="checkbox-clickable table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID No.</th>
                                    <th>Name</th>
                                    <th>Log Time</th>
                                    <th>Device Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1005</td>
                                    <td>Jakir</td>
                                    <td>2019-05-13 21:06:00</td>
                                    <td>1557914757</td>
                                </tr>
                                <tr>
                                    <td>1005</td>
                                    <td>Jakir</td>
                                    <td>2019-05-13 21:06:00</td>
                                    <td>1557914757</td>
                                </tr>
                                <tr>
                                    <td>1005</td>
                                    <td>Jakir</td>
                                    <td>2019-05-13 21:06:00</td>
                                    <td>1557914757</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title  bg-white">
                        <h5> {{__lang('System Data')}}</h5>
                    </div>
                    <div class="ibox-content  bg-white">
                        <form action="" method="post"  class="form master-form validator" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="font-normal"><strong>{{__lang('Date')}} </strong><span class="required">*</span></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input  type="text" name="day_is" id="day_is" class="form-control dateRange" data-error="Please select start time" value="{{!empty($attendance->day_is)?$attendance->day_is:old('day_is')}}" placeholder="Date"  required="" autocomplete="off">
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group ovhwrap">
                                        <label class="font-normal"><strong>{{__lang('Select Employee ')}} </strong><span class="required">*</span></label>
                                        {{ __combo('employee_list',array('selected_value'=> '', 'attributes'=> array('class'=>'form-control multi','id'=>'employee_id','name'=>'employee_id[]'))) }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-4" style="padding-top: 5px">
                                        <button class="submit_button btn btn-primary" type="submit">Push</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="ibox-content  bg-white">
                        <table class="checkbox-clickable table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID No.</th>
                                <th>Name</th>
                                <th>Log Time</th>
                                <th>Device Id</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1005</td>
                                <td>Jakir</td>
                                <td>2019-05-13 21:06:00</td>
                                <td>1557914757</td>
                            </tr>
                            <tr>
                                <td>1005</td>
                                <td>Jakir</td>
                                <td>2019-05-13 21:06:00</td>
                                <td>1557914757</td>
                            </tr>
                            <tr>
                                <td>1005</td>
                                <td>Jakir</td>
                                <td>2019-05-13 21:06:00</td>
                                <td>1557914757</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function ($) {
            $(".date").datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('.dateRange').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply:true,
            });
        })(jQuery);


    </script>

@endsection