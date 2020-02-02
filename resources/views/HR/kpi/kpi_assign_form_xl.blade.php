@extends('layouts.app')
@section('content')

    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Download Excel</h2>
                        <div class="ibox-tools">
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Select Configuration</label>
                                    <select class="form-control" id="config_name">
                                        <option value="">Select Configuration</option>
                                        @if(count($configs) > 0)
                                            @foreach($configs as $info)
                                                <option value="{{$info->bat_kpi_configs_id}}" name="{{$info->bat_kpi_configs_name}}">{{$info->bat_kpi_configs_name}}</option>
                                            @endforeach
                                        @endif
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
                        <div class="row col-md-10" style="">
                                
                            <div class="col-md-6 ff_type_cls">
                                <div class="form-group">
                                    <label class="form-label">Select FF Type</label>
                                    <select id="ff_multi_select" class="form-control  multi" multiple="multiple">
                                        @if(count($designations) > 0)
                                            @foreach($designations as $info)
                                               <option value="{{$info->designations_id}}">{{$info->designations_name}}</option> 
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <span class="text-danger">Note: After download file only insert target for employee. Please don't edit other data of excel sheet.</span>
                            </div>
                            @if(count($properties) > 0)
                                <div class="col-md-6 kpi_cls">
                                    <div class="form-group">
                                        <label class="form-label">Select KPI</label>
                                        <select id="kpi_cls_select" class="form-control">

                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                                
                        <form id="commonFormId">
                            <input type="hidden" name="kpi_config_id" value="" class="kpi_config_id">
                            <input type="hidden" name="kpi_config_name" value="" class="kpi_config_name">
                            <input type="hidden" name="property_name" value="" class="kpi_property_name">
                            <input type="hidden" name="property_id" value="" class="kpi_property_id">
                            
                            <div class="row col-md-12" style="margin-top: 15px;">
                                <div class="col-md-12">
                                    <span ><h3 class="property_name_span bg-primary" style="padding: 5px;"></h3></span>
                                </div>
                               
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label ">Target Type</label>
                                        <select class="form-control target_type_cls" name="target_type">
                                            <option value="">Select Target Type</option>
                                            <option value="brand">Brand</option>
                                            <option value="family">Family</option>
                                            <option value="segment">Segment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 full_segment_cls">
                                    <div class="form-group">
                                        <label class="form-label">Select Segment</label>
                                        <select class="form-control  multi" name="segment_name[]" multiple="multiple">
                                            @foreach($segment as $info)
                                               <option value="{{$info->id}}">{{$info->slug}}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 full_family_cls">
                                    <div class="form-group">
                                        <label class="form-label">Select Family</label>
                                        <select class="form-control  multi" name="family_name[]" multiple="multiple">
                                            @foreach($family as $info)
                                               <option value="{{$info->id}}">{{$info->slug}}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 full_brand_cls">
                                    <div class="form-group">
                                        <label class="form-label">Select Brand</label>
                                        <select class="form-control  multi" name="brand_name[]" multiple="multiple">
                                            @foreach($brand as $info)
                                               <option value="{{$info->products_id}}">{{$info->name}}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <input type="hidden" name="ff_type_name[]" class="ff_type_in_div" value="">
                                    <button type="submit" id="btnCommonId" class="btn btn-success form-submit xl-download-btn" style="margin-top: 21px;">Download Excel</button>
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function () {

        $('#ff_multi_select').multiselect({
            onChange: function() {
                // console.log($('#ff_multi_select').val());
                $('.ff_type_in_div').val($('#ff_multi_select').val());
            }
        });

        $('.config-details-tbl').hide();
        $('#commonFormId').hide();
        $('.ff_type_cls').hide();
        $('.kpi_cls').hide();
        $('.full_brand_cls').hide();
        $('.full_segment_cls').hide();
        $('.full_family_cls').hide();


        $('#kpi_cls_select').on('change', function (e) {
            
            $('.kpi_property_name').val($("#kpi_cls_select option:selected").text());
            $('.kpi_property_id').val($("#kpi_cls_select option:selected").val());
            $('.property_name_span').html($("#kpi_cls_select option:selected").text());
        });

        $('.target_type_cls').on('change', function (e) {

            if($('.target_type_cls').val() == 'brand'){
                $('.full_brand_cls').show();
                $('.full_segment_cls').hide();
                $('.full_family_cls').hide();
            }
            else if($('.target_type_cls').val() == 'family'){
                $('.full_brand_cls').hide();
                $('.full_segment_cls').hide();
                $('.full_family_cls').show();
            }
            else if($('.target_type_cls').val() == 'segment'){
                $('.full_brand_cls').hide();
                $('.full_segment_cls').show();
                $('.full_family_cls').hide();
            }
            else{
                $('.full_brand_cls').hide();
                $('.full_segment_cls').hide();
                $('.full_family_cls').hide();
            }
            
            // $('#'+form_id).validator('update');
        });

        $('#commonFormId').submit(function(e){
            e.preventDefault();

            if($('.ff_type_in_div').val().length > 0){
                
                if($('.target_type_cls').val().length > 0){

                    Ladda.bind("#btnCommonId");
                    var load = $("#btnCommonId").ladda();
                    var data = $('#commonFormId').serialize();
                    var url = "{{url('kpi-assign-xl-download')}}";
                    
                    makeAjaxPost(data,url,load).done(function (response) {
                        
                        if(response.code == 500){
                            swalError(response.msg); 
                        }
                        else{
                            window.location.href = './public/export/' + response.file;
                            swalSuccess(response.msg);
                        }
                    });
                }
                else{
                    swalError('Please Select Target Type and Products!');
                } 
            }
            else{
                swalError('Please Select Field Fource Type!');
            }     
        });  
            

        $("#config_name").on('change', function (e) {

            // $(".xl-download-btn").removeClass("disabled");
            var config_id = $('#config_name').val();
            var kpi_config_name = $(this).find('option:selected').attr("name");

            $('.kpi_config_id').val(config_id);
            $('.kpi_config_name').val(kpi_config_name);

            var url = "{{url('get-kpi-config-details')}}/"+config_id;
            $.get(url, function(data, status){
                $('#con-name').text(data.bat_kpi_configs_name);
                $('#con-peramiter').html(data.config_details);
                $('#con-time-range').html(data.start_month+"-"+data.end_month);
                $('#con-range').html(data.kpi_range);
                $('#con-scope').text(data.market_scope);
                $('.config-details-tbl').show();
                // $('.form-submit').show();
                $('.ff_type_cls').show();
                $('.kpi_cls').show();
                $('#kpi_cls_select').html(data.property_string);
                $('#commonFormId').show();
                $('.common_div_cls').hide();
                $('.kpi_property_name').val(data.property_name);
                $('.kpi_property_id').val(data.property_id);
                $('.property_name_span').html(data.property_name);

                if(data.property_ary.length > 0){
                     $.each(data.property_ary, function( key, val ){
                        // console.log(val);
                        $('.'+val).show();
                     });
                }
            });
            $('#formId').validator('update');
        }); 
    });
</script>
@endsection