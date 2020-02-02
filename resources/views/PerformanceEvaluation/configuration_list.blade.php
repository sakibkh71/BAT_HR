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
                    <h2>Configuration List</h2>
                    <div class="ibox-tools">
                        <div class="dropdown float-left">
                        </div>
                        <button class="btn btn-warning btn-xs" id="excel_upload"><i class="fa fa-upload" aria-hidden="true"></i> Excel Upload</button>
                        <a href="{{URL::to('pe-download-excel-config')}}"><button class="btn btn-success btn-xs" id="excel_download"><i class="fa fa-download" aria-hidden="true"></i> Excel Download</button></a>
                        <button class="btn btn-warning btn-xs" id="config_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="btn btn-primary btn-xs"  data-toggle="modal" data-target=".myModal" id="config_view"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                        {{--<button class="btn btn-danger btn-xs" id="head_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>--}}
                        <a href="{{url('pe-create-config')}}">
                            <button class="btn btn-success btn-xs" id="head_create">
                                <i class="fa fa-plus" aria-hidden="true"></i> New Configuration
                            </button>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class=" table  table-bordered table-striped" id="conf_tbl">
                        <thead>
                            <tr>
                                <th>Config Name</th>
                                <th>Designation</th>
                                <th>Evaluate By</th>
                                <th>Year</th>
                                <th>Config Heads</th>
                            </tr>
                        </thead>
                        @if(!empty($list))
                            <tbody>
                            @foreach($list as $info)
                                <tr class="row-select-toggle"  id="{{$info['conf_id']}}">
                                    {{--{{dd($info)}}--}}
                                    <td>{{$info['conf_name']}}</td>
                                    <td>
                                        @foreach($info['designations'] as $desg_val)
                                            <strong>{{$designation_ary[$desg_val]." ,"}}</strong>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($info['evaluate_by'] as $desg_val)
                                            <strong>{{$designation_ary[$desg_val]." ,"}}</strong>
                                        @endforeach
                                    </td>
                                    <td>{{$info['year']}}</td>
                                    <td>
                                        @foreach($info as $key=>$val)
                                            @if(!in_array($key, ['conf_name', 'designations', 'year', 'conf_id', 'evaluate_by']))
                                                <strong>{{$val->pe_head_titles_name."(".$val->weight." %),"}}</strong>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        @endif
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
        $(document).ready(function() {
            $('#conf_tbl').DataTable();
            $('#config_edit').hide();
            $('#config_view').hide();
            $('#excel_download').hide();
            $('#excel_upload').hide();
        } );

        var selected = [];

        $(document).on('click','#conf_tbl tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            console.log(id);

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
                    $('#config_edit').hide();
                    $('#config_view').hide();
                    $('#excel_download').hide();
                    $('#excel_upload').hide();
                }
                else if (arr_length == 1) {
                    $('#config_edit').show();
                    $('#config_view').show();
                    $('#excel_download').show();
                    $('#excel_upload').show();
                }
                else {
                    $('#config_edit').hide();
                    $('#config_view').hide();
                    $('#excel_download').hide();
                    $('#excel_upload').hide();
                }
            }
        });

        $("#config_view").on('click', function () {

            var id = selected;

            var url = "{{URL::to('pe-get-config-view')}}/"+id;

            $.get(url, function(data){
                console.log(data);
                $('#result_div').html(data);
            });
        });

        $("#config_edit").on('click', function () {

            var id = selected;
            var url = "{{URL::to('pe-config-edit')}}/"+id;

            window.location.href = url;
        });
    </script>
@endsection
