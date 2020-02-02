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
                    <h2>Emergency Contact List</h2>
                    <div class="ibox-tools">
                        <button type="button" id="exportExcel" class="btn btn-info btn-xs"><i class="fa fa-file-excel-o"></i> Excel</button>
                        @if(isSuperUser())
                            <div class="dropdown float-left">
                            </div>

                            <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal" id="employee_view"><i class="fa fa-eye" aria-hidden="true"></i> View List</button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('emp_emargency_con_list',1) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="empView" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="empview_wrap"></div>
        </div>
    </div>

    <!--  Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h4 class="modal-title">Emergency Contact List</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="col-md-12" id="emg_con_list">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        var selected = [];

        $(document).ready(function(){
            $('#employee_view').hide();
        });

        $('#exportExcel').click(function () {
            var url='{{route("emargency-contact-excel")}}';
            $.ajax({
                type:'get',
                url:url,
                success:function (data) {
                    console.log(data);
                    window.location.href = './public/export/' + data.file;
                    swalSuccess('Export Successfully');
                }
            });
        });

        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');

            /*add this for new customize*/
            selected = [];
            $('#table-to-print tbody tr').not($(this)).removeClass('selected');
            /* end this */

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
                    // $('#employee_edit').hide();
                    $('#employee_view').hide();
                    // $('#employee_separation').hide();
                    // $('#pdfOptions').hide();
                }
                else if (arr_length == 1) {
                    // $('#employee_separation').show();
                    $('#employee_view').show();
                    // $('#pdfOptions').show();
                    // $('#employee_edit').show();
                    // $('#employee_delete').show();
                }
                else {
                    // $('#employee_edit').hide();
                    $('#employee_view').hide();
                    // $('#employee_separation').hide();
                    // $('#pdfOptions').hide();
                    // $('#employee_delete').hide();
                }
            }
        });

        //View Config Kpi
        $("#employee_view").on('click', function (e) {

            var user_id = selected[0];
            console.log(user_id);

            var url = "{{url('emp-emargency-contract-list')}}/"+user_id;

            $.get(url, function(data, status){
                console.log(data);
                $('#emg_con_list').html(data);
            });
        });
    </script>
@endsection
