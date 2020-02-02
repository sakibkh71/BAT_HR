@extends('layouts.app')
@section('content')
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
                        <form action="{{route('employee-bulk-upload-store')}}" method="post"  class="form master-form validator" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Select Company</label>
                                        <select name="company_id" id="" class="form-control">
                                            <option value="0">Select Company</option>
                                            @foreach($companies as $key=>$val)
                                                <option value="{{$key}}">{{$val}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Upload File (CSV/Excel)</label>
                                        <input type="file" name="select_file" value="" class="form-control" placeholder="Select File (CSV)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-4">
                                        <button class="submit_button btn btn-primary" type="submit">Upload</button>

                                        <a href="{{url('/public/sample_files/employee_sample list.xlsx')}}" class="btn btn-success" download="Sample File"> Download Sample File</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection