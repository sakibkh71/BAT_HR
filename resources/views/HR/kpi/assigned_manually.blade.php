@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        
                        <h2>Assigned Kpi Manually</h2>
                        <div class="ibox-tools">
                            <a href="{{url('kpi-assign-form')}}">
                            <button type="button"  class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> Assign Kpi Manually</button></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        
                        
                        <div class="row col-md-12" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Select Configuration</label>
                                    <select class="form-control" id="config_name">
                                        <option value="">Select Configuration</option>
                                        @if(count($configs) > 0)
                                            @foreach($configs as $info)
                                                <option value="{{$info->bat_kpi_configs_id}}">{{$info->bat_kpi_configs_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 ff_type_cls">
                                <div class="form-group">
                                    <label class="form-label">FF type</label>
                                    <select class="form-control" id="ff_type_id">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row col-md-12">
                            <table class="table config-details-tbl">
                                <tr class="bg-primary">
                                    <th>Name</th>
                                    <th>Peramiters</th>
                                    <th>Time Range</th>
                                    <th>Config. Range</th>
                                    <th>Market Scope</th>
                                </tr>
                                <tr>
                                    <td><span id="con-name"></span></td>
                                    <td><span id="con-peramiter"></span></td>
                                    <td><span id="con-time-range"></span></td>
                                    <td><span id="con-range"></span></td>
                                    <td><span id="con-scope"></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="row col-md-8 property_cls" style="margin-top: 15px;">
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {

        $('.config-details-tbl').hide();
        $('.ff_type_cls').hide();

        $("#config_name").on('change', function (e) {

            $('.property_cls').hide();
            var config_id = $('#config_name').val();
            var ff = 'ff';

            $('#kpi_config_id').val(config_id);
            // alert(config_id);
            var url = "{{url('get-kpi-config-details')}}/"+config_id+"/"+ff;
            $.get(url, function(data, status){
                $('#con-name').text(data.bat_kpi_configs_name);
                $('#con-peramiter').html(data.config_details);
                $('#con-time-range').html(data.start_month+"<br/>TO<br/>"+data.end_month);
                $('#con-range').html(data.kpi_range);
                $('#con-scope').text(data.market_scope);

                $('.config-details-tbl').show();
                $('.ff_type_cls').show();
                $('#ff_type_id').html(data.ff_type_option);
            });
        }); 

        $("#ff_type_id").on('change', function (e) {

            $('.property_cls').show();
            var config_id = $('#config_name').val();
            var designation_id = $('#ff_type_id').val();

            var designation_id = designation_id.length > 0?designation_id:0;

            var url = "{{url('get-kpi-assign-view')}}/"+config_id+"/"+designation_id;
            
            $.get(url, function(data, status){
                console.log(data); 
                $('.property_cls').html(data);   
            });
        }); 
    });
</script>
@endsection