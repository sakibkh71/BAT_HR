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
                        <form action="{{route('attendance-manual-upload-store')}}" method="post"  class="form master-form validator" enctype="multipart/form-data" id="attendanceFrom">
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
                                        <button class="submit_button btn btn-primary" type="button" id="uploadAttendance">Upload</button>
                                        <a href="{{url('/public/sample_files/Sample_Manual_Attendance_File.xlsx')}}" class="btn btn-success" download="Sample Manual Attendance File"> Download Sample File</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title  bg-white">
                        <h5> {{__lang('Manual Attendance Data')}}</h5>
                        <div class="ibox-tools">
                            <button class="btn btn-xs" id="show-custom-search"><i class="fa fa-search"></i> show search</button> &nbsp;
                             <button class="submit_button btn btn-xs btn-primary" type="button" id="syncBtn">{{__lang('Confirm & Lock')}}</button>
                        </div>
                    </div>
                    <div class="ibox-content  bg-white">
                        {!! __getMasterGrid('attendance_manual_upload') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function ($) {
            $('#syncAttendance').validator();

            $(".date").datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('.dateRange').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply:true,
            });

            //When click on Upload Attendance
            $('#uploadAttendance').click(function () {
                swalConfirm('To remove old temporary data and upload new attendance data').then(function (e) {
                    if(e.value){
                       $('#attendanceFrom').submit();
                    }
                });
            });

            //Sync Attendance Data
            $('#syncBtn').click(function () {
                if($('#table-to-print tbody tr').length>0){
                    swalConfirm('To sync attendance data').then(function (e) {
                        if(e.value){
                            var  day_is = $('input[name="WH-DR-day_is"]').val();
                            var  employee_id = $('#employee_code_list').val();

                            var data = {'day_is':day_is, 'employee_id':employee_id};
                            var url = '{{route('sync-manual-attendance')}}';

                            makeAjaxPost(data,url,null).done(function (response) {
                                var redirect ='{{route('attendance-manual-upload')}}';
                                if (response.status =='success'){
                                    swalRedirect(redirect, 'Successfully sync attendance', 'success');
                                }else{
                                    swalRedirect(redirect, 'Something wrong', 'error');
                                }
                            });

                        }
                    });
                }else{
                    swalError('Sorry! You have no un sync data');
                }
            });


        })(jQuery);


    </script>

@endsection