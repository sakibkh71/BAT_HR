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
                    <h2>Paid Loan List</h2>

                </div>
                <div class="ibox-content">
                    <form action="{{url('paid-loan-list')}}" method="post" id="createTitleHeadForm">
                        <div class="col-md-12">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-normal"><strong>Select Employees</strong> <span class="required">*</span></label>
                                        <div class="">
                                            {{--<input type="text" name="head_name" class="form-control" id="head_name">--}}
                                            <select name="user_id" id="emp_name_list" class="form-control multi" >
                                                @if(count($users) > 0)
                                                    <option value="">Select User</option>
                                                    @foreach($users as $info)
                                                    <option @if($user_id == $info->id) selected @endif value="{{$info->id}}">{{$info->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 27px;">
                                        <button class="btn btn-primary btn" type="submit" id="createHeadSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="col-md-4" style="margin-top: 20px;">

                        @if(count($list) > 0)

                            <strong>Loan Amount: {{$loan_amount}}</strong>

                            <table class="table table-striped" style="margin-top: 10px;">
                                <thead>
                                @foreach($list as $info)
                                    <tr>
                                        <td style="padding-left: 10px;">{{$info->paid_amount}}</td>
                                        <td class="pull-right" style="padding-right: 10px;">{{toDated($info->created_at)}}</td>
                                    </tr>
                                @endforeach
                                </thead>
                            </table>

                            <strong>Due Amount: {{$due_amount}}</strong>
                        @else

                            <strong>No Data Found</strong>

                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="empView" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="empview_wrap"></div>
        </div>
    </div>

    <script>

    </script>


@endsection
