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
        .checkAttendance{
            visibility: hidden;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Route List</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ibox">

                                <div class="ibox-content  bg-white">
                                    {{--<form action="{{route('route-list-with-ff')}}" method="post" id="">--}}
                                        {{--@csrf--}}
                                        {{--<div class="row">--}}
                                            {{--<div class="col-md-4">--}}
                                                {{--<label class="font-normal">Distributor Point</label>--}}
                                                {{--{{__combo('bat_distributor_point_all', ['multiple' => 0, 'selected_value' =>$point])}}--}}
                                            {{--</div>--}}

                                            {{--<div class="col-md-3">--}}
                                                {{--<label class="font-normal">FF Type</label>--}}
                                                {{--<div class="form-group">--}}
                                                    {{--<select name="designation_id" id="designation_id" class="form-control">--}}
                                                        {{--<option value="152" @if($designation_id == 152) selected="selected" @endif>SR</option>--}}
                                                        {{--<option value="151" @if($designation_id == 151) selected="selected" @endif>SS</option>--}}
                                                    {{--</select>--}}

                                                {{--</div>--}}
                                            {{--</div>--}}

                                            {{--<div class="col-md-2">--}}
                                                {{--<div class="form-group" style="margin-top:28px;">--}}
                                                    {{--<button class="btn btn-primary btn" name="submit" type="submit">{{__lang('Search')}}</button>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</form>--}}
                                    <div class="row">
                                        <table class="table table-striped table-bordered" id="example">
                                            <thead>
                                            <tr>
                                                <th>Starting Date</th>
                                                <th>Distributor Point</th>
                                                <th>Route Number</th>
                                                <th>Previous Employee</th>
                                                <th>New Employee</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($logs) > 0)
                                                @foreach($logs as $info)
                                                    <tr>
                                                        <td>{{$info->date}}</td>
                                                        <td>{{$info->point_name}}</td>
                                                        <td>{{$info->bat_route_number}}</td>
                                                        <td>{{$info->inactive_emp_name}}</td>
                                                        <td>{{$info->active_emp_name}}</td>
                                                        <td>{{toDated($info->created_at)}}</td>
                                                        <td>{{toDated($info->updated_at)}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .toggle.btn{
            min-width: 120px;
        }
    </style>
    <script>

        $('#example').dataTable();


    </script>
@endsection

