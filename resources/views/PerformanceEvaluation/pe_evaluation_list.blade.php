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
                        {{--<button type="button" id="makeExcel" class="btn btn-primary btn-xs"><i class="fa fa-file-excel-o"></i> {{__lang('Excel')}}</button>--}}
                        {{--<button class="btn btn-success btn-xs" id="ConfirmationLetter"><i class="fa fa-file" aria-hidden="true"></i> PDF</button>--}}
                    </div>
                </div>
                <div class="ibox-content">
                    {{--<form action="{{url('pe-evaluation-list')}}" method="post">--}}
                        {{--<div class="col-md-12 row">--}}
                        {{--@csrf--}}
                            {{--<div class="col-md-3">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label class="font-normal"><strong>From Date</strong> <span class="required">*</span></label>--}}
                                    {{--<div class="">--}}
                                        {{--<input type="text" name="head_name" class="form-control" id="month_from">--}}
                                    {{--</div>--}}
                                    {{--<div class="help-block with-errors has-feedback"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label class="font-normal"><strong>To Date</strong> <span class="required">*</span></label>--}}
                                    {{--<div class="">--}}
                                        {{--<input type="text" name="head_name" class="form-control" id="month_to">--}}
                                    {{--</div>--}}
                                    {{--<div class="help-block with-errors has-feedback"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2">--}}
                                {{--<button style="margin-top: 28px;" class="btn btn-primary form-control" type="submit">Submit</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                    <table id="kpi_list" class="table data-table table-bordered table-striped">
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
                            @foreach($list as $info)
                                <tr class="row-select-toggle" id="{{$info->pe_evaluate_employees_id}}">
                                    <td>{{$info->user_name}}</td>
                                    <td>{{$info->year}}</td>
                                    <td>{{$info->designations_name}}</td>
                                    <td>{{$info->achievement}}</td>
                                    <td>{{$info->evaluate_by}}</td>
                                    <td>{{date('Y-m-d', strtotime($info->created_at))}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--  Modal -->
    <div class="modal fade myModal" id="" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Configuration Details</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div id="result_div">

                    </div>
                </div>
                {{--<div class="modal-footer">--}}

                {{--</div>--}}
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.data-table').dataTable({
                "pageLength":20
            } );
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

        var selected = [];

        $(document).on('click','#kpi_list tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    selected.push(id);
                    self.find('input[type=checkbox]').prop("checked", true);
                } else {
                    selected.splice(selected.indexOf(id), 1);
                    self.find('input[type=checkbox]').prop("checked", false);
                }

                var arr_length = selected.length;
                if (arr_length > 1) {
                    $('#details_view').hide();
                }
                else if (arr_length == 1) {
                    $('#details_view').show();
                }
                else {
                    $('#details_view').hide();
                }
            }
            console.log(selected);
        });

        $("#details_view").on('click', function () {

            var id = selected;
            // alert(id);

            var url = "{{URL::to('pe-evaluation-list-details')}}/"+id;

            $.get(url, function(data){
                console.log(data);
                $('#result_div').html(data);
            });

        });

        $("#makeExcel").click(function(){
            var $form = $('#attendanceForm');
            var data={};
            // data = $form.serialize() + '&' + $.param(data);
            // var month_from=$("#month_from").val();
            // var month_to=$("#month_to").val();

            // if(month_from=='' || month_to==''){
            //     swalError('Please Provide the Required Values');
            // }else {
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
            // }
        });

        $("#ConfirmationLetter").on('click', function (e) {
            var employee_id = 6348;
            if (employee_id.length === 0) {
                swalError("Please select a Employee");
                return false;
            } else {
                var url = "{{URL::to('confirmation-letter')}}/" + employee_id;
                window.open(url, '_blank');
            }
        });


    </script>
@endsection
