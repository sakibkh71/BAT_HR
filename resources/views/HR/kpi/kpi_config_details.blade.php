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

        .kpi-nox{
            -webkit-box-shadow: 0 0 10px 1px rgba(0,0,0,0.1);
            box-shadow: 0 0 10px 1px rgba(0,0,0,0.1);
            background: #fff;
            margin: 20px 0;
        }
        .item-list{
            width: 100%;
            border:none;
        }
        .item-list tr:nth-child(even){
            background: #e7e7e7
        }
        .item-list td, .item-list th{
            padding: 5px;
        }
        .file-chooser{
            float: left;
            width: 60%;
        }
        .btn-upload{
            float: right;
        }

    </style>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Kpi Detail</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-6" id="cluster_info">
                            <table>
                                <tr>
                                    <th>Kpi Config Name</th>
                                    <td> : {{$kpi_config->bat_kpi_configs_name}}</td>
                                </tr>
                                <tr>
                                    <th>Kpi Config Code</th>
                                    <td> : {{$kpi_config->kpi_config_code}}</td>
                                </tr>
                                <tr>
                                    <th>Config Month</th>
                                    <td> : {{$kpi_config->config_month}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6" id="cluster_info">
                            <table>
                                <tr>
                                    <th>Company Name</th>
                                    <td> : {{$kpi_config->company_name}}</td>
                                </tr>
                                <tr>
                                    <th>Point Name</th>
                                    <td> : {{$kpi_config->point_name}}</td>
                                </tr>
                                <tr>
                                    <th>Designations</th>
                                    <td> : {{$kpi_config->designation}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($kpi_config_details as $detail)
                        <div class="col-md-6">
                            <div class="ibox kpi-nox">
                                <div class="ibox-title"><h5><strong>KPI Name </strong>: {{!empty($detail->bat_kpi_name) ?$detail->bat_kpi_name:'N/A' }}</h5></div>
                                <div class="ibox-content">
                                    <table class="item-list">
                                        <tr>
                                            <td width="30%"><strong>Weight </strong></td>
                                            <td>:</td>
                                            <td>{{!empty($detail->weight) ?$detail->weight:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Target Brand  </strong></td>
                                            <td>:</td>
                                            <td>{{!empty($detail->target_product) ?$detail->target_product:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Target Family  </strong></td>
                                            <td>:</td>
                                            <td>{{!empty($detail->target_family) ?$detail->target_family:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Target Segment </strong></td>
                                            <td>:</td>
                                            <td>{{!empty($detail->target_segments) ?$detail->target_segments:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th><strong>Download Excel </strong></th>
                                            <td>:</td>
                                            <td>
                                                <button class="btn btn-success btn-xs btn_download"  data-config_details_id="{{$detail->bat_kpi_config_details_id}}"  data-type="{{$list_type}}" @if($list_type=='point') data-location="{{$kpi_config->dpid}}" @elseif($list_type=='house') data-location="{{$kpi_config->point_ids}}" @endif><i class="fa fa-download" aria-hidden="true"></i> Download</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><strong>Upload Excel </strong></th>
                                            <td>:</td>
                                            <td>
                                                @if($detail->uploaded_file == null)
                                                    <form action="{{route('upload-kpi-target')}}" name="target_excel_submit_form_{{$detail->bat_kpi_config_details_id}}" id="target_excel_submit_form_{{$detail->bat_kpi_config_details_id}}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="file" name="file" class="file-chooser" />
                                                        @if($list_type=='point')
                                                            <input type="hidden" name="kpi_configs_id" value="{{$kpi_config->bat_kpi_configs_id}}" />
                                                            <input type="hidden" name="kpi_config_code" value="{{$kpi_config->kpi_config_code}}" />
                                                        @elseif($list_type=='house')
                                                            <input type="hidden" name="kpi_config_code" value="{{$kpi_config->kpi_config_code}}" />
                                                            <input type="hidden" name="point_ids" value="{{$kpi_config->point_ids}}" />
                                                        @endif
                                                        <input type="hidden" name="target_month" value="{{$kpi_config->config_month}}" />
                                                        <input  type="hidden" name="bat_kpi_id" value="{{$detail->bat_kpi_id}}"/>
                                                        <input type="hidden" name="bat_kpi_config_details_id" value="{{$detail->bat_kpi_config_details_id}}"/>
                                                        <input type="hidden" name="kpi_type" value="{{$list_type}}" />

                                                        <button type="submit" name="submit_target_excel" class="btn btn-primary btn-xs btn-upload"> Upload Now</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Delete Excel</th>
                                            <td>:</td>
                                            <td>
                                                @if(strtotime($kpi_config->config_month) >  strtotime(date("Y-m")))
                                                    @if($detail->uploaded_file != null)
                                                        <form action="#" name="delete_excel_target_{{$detail->bat_kpi_config_details_id}}" id="delete_excel_target_{{$detail->bat_kpi_config_details_id}}">
                                                            @csrf
                                                            @if($list_type=='point')
                                                                <input type="hidden" name="kpi_configs_id" value="{{$kpi_config->bat_kpi_configs_id}}" />
                                                                <input type="hidden" name="point_id" value="{{$kpi_config->dpid}}" />
                                                            @elseif($list_type=='house')
                                                                <input type="hidden" name="kpi_config_code" value="{{$kpi_config->kpi_config_code}}" />
                                                                <input type="hidden" name="point_ids" value="{{$kpi_config->point_ids}}" />
                                                            @endif
                                                            <input type="hidden" name="target_month" value="{{$kpi_config->config_month}}" />
                                                            <input  type="hidden" name="bat_kpi_id" value="{{$detail->bat_kpi_id}}"/>
                                                            <input type="hidden" name="kpi_type" value="{{$list_type}}" />
                                                            <input type="hidden" name="bat_kpi_config_details_id" value="{{$detail->bat_kpi_config_details_id}}"/>
                                                            <button name="delete_target_excel" class="btn btn-danger btn-xs delete_target_excel"> Delete</button>
                                                        </form>
                                                    @else
                                                        <div class="alert alert-warning" role="alert" style="padding: 5px; margin: 0;"> Sorry! there is no file for delete </div>
                                                    @endif
                                                @else
                                                    <div class="alert alert-warning" role="alert"  style="padding: 5px; margin: 0;"> Sorry! You can't delete file for this month</div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(document).on("click",".delete_target_excel",function(e) {
            e.preventDefault();

            var id_val = "delete_excel_target_"+$(this).prev('input:hidden').val();
            // alert(id_val);

            swalConfirm('to Confirm Delete Uploaded File?').then(function (e) {
                if(e.value){
                    var data = $('#'+id_val).serialize();
                    var url = "{{url('delete-kpi-target')}}";

                    makeAjaxPost(data,url,null).done(function (response) {
                        if(response.code == 200){
                            swalSuccess(response.msg);
                        }
                        else{
                            swalError(response.msg);
                        }

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    });
                }
            });
        });


        $(document).on('click','.btn_download',function () {

            var kpi_config_detail_id = $(this).data('config_details_id');
            var location_id = $(this).data('location');
            var type = $(this).data('type');

            $.ajax({
                type:'get',
                data:{
                    'kpi_config_detail_id':kpi_config_detail_id,
                    'location_id':location_id,
                    'type':type
                },
                url:'{{url('download-kpi-detail-excel')}}',
                success:function (data) {
                   console.log(data);

                   @if($list_type=='point') window.location.href = './../../public/export/' + data.file;
                   @elseif($list_type=='house')
                    window.location.href = './../../../public/export/' + data.file;
                    @endif
                    swalSuccess('Export Successfully');
                }
            });
        });
    </script>

@endsection