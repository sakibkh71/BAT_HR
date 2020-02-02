@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        
                        <h2>Assigned Kpi Using Excel</h2>
                        <div class="ibox-tools">
                            <a href="{{url('kpi-assign-form-xl')}}">
                            <button type="button"  class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> Excel Download</button></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        
                        <form action="{{route('kpi-assign-xl-upload')}}" method="post"  class="form master-form validator" enctype="multipart/form-data" id="attendanceFrom" onsubmit="$('#loading').show();">

                            <div class="row col-md-12 row">
                            @csrf
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Upload File</label>
                                        <input type="file" name="select_file" value="" class="form-control" placeholder="Select File (CSV)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-4">
                                        <button class="submit_button btn btn-primary" type="submit" id="uploadAttendance">Upload</button>
                                        <div id="loading" style="display:none">Uploading...</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


<script>
    $(document).ready(function () {

        
    });
</script>
@endsection