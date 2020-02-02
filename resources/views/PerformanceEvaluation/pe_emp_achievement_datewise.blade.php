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
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Evaluation List</h2>
                    <div class="ibox-tools">
                        <button class="btn btn-success btn-xs " data-toggle="modal" data-target=".myModal" id="details_view"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{url('pe-emp-achievement-datewise')}}" method="post">
                        <div class="col-md-12 row">
                            @csrf
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-normal"><strong>Select Employee</strong> <span class="required">*</span></label>
                                    <div class="">
                                        {{--<input type="text" name="head_name" class="form-control" id="head_name">--}}
                                        <select name="user_id" id="emp_name_list" class="form-control multi" >
                                            @foreach($users as $key=>$info)
                                            <option @if($user_id == $key) selected @endif value="{{$key}}">{{$info}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-normal"><strong>From Date</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <input type="text" value="{{$date_from}}" name="date_from" class="form-control" id="month_from">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-normal"><strong>To Date</strong> <span class="required">*</span></label>
                                    <div class="">
                                        <input type="text" value="{{$date_to}}" name="date_to" class="form-control" id="month_to">
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button style="margin-top: 28px;" class="btn btn-primary form-control" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                    @if(!empty($list))
                        <table id="kpi_list" class="table table-bordered table-striped" style="margin-top: 25px;">
                            <thead>
                            <tr>
                                <th width="15%">Name</th>
                                <th width="25%">Year</th>
                                <th width="15%">Designation</th>
                                <th width="10%">Achievement</th>
                                <th width="15%">Evaluate By</th>
                                <th width="15%">Evaluation Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($count_list = count($list))
                            @php($initial = 0)
                            @foreach($list as $info)
                                <tr class="row-select-toggle" id="{{$info->pe_evaluate_employees_id}}">
                                    <td>{{$info->user_name}}</td>
                                    <td>{{$info->year}}</td>
                                    <td>{{$info->designations_name}}</td>
                                    <td>{{$info->achievement}}</td>
                                    <td>{{$info->evaluate_by}}</td>
                                    <td>{{date('Y-m-d', strtotime($info->created_at))}}</td>
                                    @php($initial += $info->achievement)
                                </tr>
                            @endforeach
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="pull-right">Average Achievement: </td>
                                    <td><strong>
                                            @if($initial > 0){{sprintf('%0.2f', ($initial/$count_list))}}@endif
                                        </strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // $('.data-table').dataTable({
            //     "pageLength":20
            // } );

            $('#details_view').hide();

            $("#month_from").datepicker( {
                format: "yyyy-mm-dd",
                viewMode: "dates",
                minViewMode: "dates",
                autoclose: true,
            });

            $("#month_to").datepicker( {
                format: "yyyy-mm-dd",
                viewMode: "dates",
                minViewMode: "dates",
                autoclose: true,
            });
        });
    </script>
@endsection
