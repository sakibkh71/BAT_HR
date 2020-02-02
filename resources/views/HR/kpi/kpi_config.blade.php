@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        
                        <h2>Kpi Config</h2>
                        <div class="ibox-tools">
                            <a href="{{url('kpi-config-create-form')}}">
                            <button type="button"  class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> New Configuration</button></a>
                            <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal" id="kpi_view"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                            <button class="btn btn-warning btn-xs" id="kpi_edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                            <button class="btn btn-danger btn-xs" id="kpi_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                           
                        {!! __getMasterGrid('bat_kpi_configs') !!}
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Kpi config modal -->

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
              <h4 class="modal-title">Kpi Config Information</h4>
            </div>
            <div class="modal-body col-md-12">
                <div class="col-md-12">
                    <div><b>Configuration Name</b> : <span id="con-name"></span></div>
                    <div><b>Time Range</b> : <span id="con-time-range"></span></div>
                    <div><b>KPI Details</b> : <span id="con-peramiter"></span></div>
                    <div><b>Configuration Range</b> : <span id="con-range"></span></div>
                    <div><b>Market Scope</b> : <span id="con-scope"></span></div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
<script>
    $(document).ready(function () {

        $('#kpi_view').hide();
        $('#kpi_edit').hide();
        $('#kpi_delete').hide();
        //$('#pdfOptions').hide();

        $('#hr_emp_categorys_id option:contains("Choose Option")').text('Employee Category');

        var selected = [];

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
                    $('#kpi_edit').hide();
                    $('#kpi_view').hide();
                    $('#kpi_delete').hide();
                }
                else if (arr_length == 1) {
                    $('#kpi_view').show();

                    var kpi_config_id = selected[0];
                    var url = "{{url('kpi-config-edit-validation')}}/"+kpi_config_id;
                    $.get(url, function(data, status){
                        if(data == 'notEdit'){
                            $('#kpi_edit').hide();
                            $('#kpi_delete').hide();
                        }
                        else{
                            $('#kpi_edit').show();
                            $('#kpi_delete').show();
                        }
                    });
                }
                else {
                    $('#kpi_edit').hide();
                    $('#kpi_view').hide();
                    $('#kpi_delete').hide();
                }
            }

        });


        //View Config Kpi
        $("#kpi_view").on('click', function (e) {
            $('#con-name').text('');
            $('#con-peramiter').html('');
            $('#con-time-range').html('');
            $('#con-range').html('');
            $('#con-scope').text('');
            var kpi_config_id = selected[0];

            var url = "{{url('kpi-config-view')}}/"+kpi_config_id;

            $.get(url, function(data, status){
                console.log(data);
                $('#con-name').html(data.bat_kpi_configs_name);
                $('#con-peramiter').html(data.config_details);
                $('#con-time-range').html(data.time_range);
                $('#con-range').html(data.kpi_range);
                $('#con-scope').html(data.market_scope);
            });
        });

        //Edit COnfig kpi
        $("#kpi_edit").on('click', function (e) {
            var kpi_config_id = selected[0];
            //alert(selected);
           if (kpi_config_id.length === 0) {
                swalError("Please select a row");
                return false;
           } else {
                window.location = '<?php echo URL::to('kpi-config-create-form');?>/' + kpi_config_id;
           }
        });

        
        $("#kpi_delete").on('click', function (e) {
            var kpi_config_id = selected;
            //alert(selected);

            console.log(kpi_config_id);

            if (kpi_config_id.length === 0) {
                swalError("Please select a Employee");
                return false;
            } else {
                swalConfirm('to Confirm Delete?').then(function (e) {
                   if(e.value){
                       var url = "{{URL::to('kpi-config-delete')}}";
                       var data = {kpi_config_id: kpi_config_id};
                       makeAjaxPost(data,url,null).then(function(response) {
                           var redirect_url = "{{URL::to('kpi-config')}}";
                           swalRedirect(redirect_url,'Delete Successfully','success');
                       });
                   }
                });

            }
        });
    });
</script>
@endsection