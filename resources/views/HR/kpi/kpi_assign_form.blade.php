@extends('layouts.app')
@section('content')

    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>KPI Assign</h2>
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
                                                <option value="{{$info->bat_kpi_configs_id}}">{{$info->bat_kpi_configs_name}}</option>
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
                        <div class="row col-md-8" style="">
                            <form id="formId">
                                <input type="hidden" name="kpi_config_id" value="" id="kpi_config_id">
                                
                                <div class="col-md-6 ff_type_cls">
                                    <div class="form-group">
                                        <label class="form-label">Select FF Type</label>
                                        <select class="form-control  multi" name="ff_type_name[]" multiple="multiple">
                                            @if(count($designations) > 0)
                                                @foreach($designations as $info)
                                                   <option value="{{$info->designations_id}}">{{$info->designations_name}}</option> 
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                
                                
                            @if(count($properties) > 0)
                                @foreach($properties as $info)
                                <?php 
                                    $conv_string = str_replace(' ', '_', $info->bat_kpi_properties_name);
                                    $property_div_cls = "property_div_cls_".$conv_string;
                                    $property_cls_name = "name_cls_".$conv_string;
                                    $from_date_cls_name = "from_date_cls_".$conv_string;
                                    $to_date_cls_name = "to_date_cls_".$conv_string;
                                    $full_segment_cls = "full_segment_cls_".$conv_string;
                                    $segment_name = "segment_name_".$conv_string."[]";
                                    $segment_value = "segment_value_".$conv_string."[]";
                                    $full_family_cls = "full_family_cls_".$conv_string;
                                    $family_name = "family_name_".$conv_string."[]";
                                    $family_value = "family_value_".$conv_string."[]";
                                    $full_brand_cls = "full_brand_cls_".$conv_string;
                                    $brand_name = "brand_name_".$conv_string."[]";
                                    $brand_value = "brand_value_".$conv_string."[]";
                                    $target_type_cls = "target_type_cls_".$conv_string;
                                    // $ff_type_cls = "ff_type_cls_".$conv_string;
                                    $ff_type_name = "ff_type_name_".$conv_string."[]";
                                    $month_from = "month_from_".$conv_string;
                                    $month_to = "month_to_".$conv_string;
                                ?>
                                <div class="row col-md-12 {{$property_div_cls}} common_div_cls" style="margin-top: 15px;">
                                    <div class="col-md-12">
                                        <span ><h3 class="{{$property_cls_name}}  bg-primary" style="padding: 5px;">{{$info->bat_kpi_properties_name}}</h3></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Base From Date</label>
                                        <div class="input-group">
                                            <input type="text"
                                                   placeholder=""
                                                   class="form-control {{$from_date_cls_name}}"
                                                   value=""
                                                   id="month_from"
                                                   data-date-format="yyyy-mm-dd"
                                                   name="{{$month_from}}" required/>
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Base To Date</label>
                                        <div class="input-group">
                                            <input type="text"
                                                   placeholder=""
                                                   class="form-control {{$to_date_cls_name}}"
                                                   value=""
                                                   id="month_to"
                                                   data-date-format="yyyy-mm-dd"
                                                   name="{{$month_to}}" required/>
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-12 row"> -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Target Type</label>
                                            <select class="form-control {{$target_type_cls}}" name="{{$target_type_cls}}">
                                                <option value="">Select Target Type</option>
                                                <option value="brand">Brand</option>
                                                <option value="family">Family</option>
                                                <option value="segment">Segment</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 row {{$full_segment_cls}}">
                                        <span class="text-danger" style="margin-left: 15px;">Note: Select segment and insert the increase amount in percent depent on base.(Ex: 110)</span>
                                        @foreach($segment as $info)
                                        <div class="col-md-12 row" style="margin-top: 5px;">
                                            <div class="col-md-1">
                                                <input type="checkbox" class="chk-box-cls" name="{{$segment_name}}" value="{{$info->id}}">
                                            </div>
                                            <div class="col-md-4">
                                                {{$info->slug}}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="number" class="form-control beside-chk-box" name="{{$segment_value}}">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-12 row {{$full_family_cls}}">
                                        <span class="text-danger" style="margin-left: 15px;">Note: Select family and insert the increase amount in percent depent on base.(Ex: 110)</span>
                                        @foreach($family as $info)
                                        <div class="col-md-12 row" style="margin-top: 5px;">
                                            <div class="col-md-1">
                                                <input type="checkbox" class="chk-box-cls" name="{{$family_name}}" value="{{$info->id}}">
                                            </div>
                                            <div class="col-md-4">
                                                {{$info->slug}}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="number" class="form-control beside-chk-box" name="{{$family_value}}">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-12 row {{$full_brand_cls}}">
                                        <span class="text-danger" style="margin-left: 15px;">Note: Select brand and insert the increase amount in percent depent on base.(Ex: 110)</span>
                                        @foreach($brand as $info)
                                        <div class="col-md-12 row" style="margin-top: 5px;">
                                            <div class="col-md-1">
                                                <input type="checkbox" class="chk-box-cls" name="{{$brand_name}}" value="{{$info->products_id}}">
                                            </div>
                                            <div class="col-md-4">
                                                {{$info->name}}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="number" class="form-control beside-chk-box" name="{{$brand_value}}">
                                                </div>
                                                
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach

                                <button type="button" id="formBtn" class="btn btn-primary form-submit pull-right" style="margin-top: 20px; margin-right: 40px;">Submit</button>
                            @endif

                            

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function () {

        $('.config-details-tbl').hide();
        $('.form-submit').hide();
        $('.ff_type_cls').hide();
        $('.beside-chk-box').attr('disabled', 'true');

        $(document).on('click','.chk-box-cls',function (e) {

            if($(this).is(':checked')){
                $(this).parent('div').next('div').next('div').find('.beside-chk-box').removeAttr('disabled');
            }
            else{
                $(this).parent('div').next('div').next('div').find('.beside-chk-box').attr('disabled', 'true');
            }

        });

        var properties = <?php echo json_encode($properties); ?>;

        // console.log(properties);
        $.each(properties, function( key, val ){
            // alert( "Index #" + i + ": " + l );
            // console.log('name : '+val.bat_kpi_properties_name);
            var vallue = val.bat_kpi_properties_name;
            var conv_string = vallue.replace(' ', '_');
            var property_cls_name = "name_cls_"+conv_string;
            var from_date_cls_name = "from_date_cls_"+conv_string;
            var to_date_cls_name = "to_date_cls_"+conv_string;
            var full_segment_cls = "full_segment_cls_"+conv_string;
            var full_family_cls = "full_family_cls_"+conv_string;
            var full_brand_cls = "full_brand_cls_"+conv_string;
            var target_type_cls = "target_type_cls_"+conv_string;
            var property_div_cls = "property_div_cls_"+conv_string;

            $(function ($) {
                $('.'+from_date_cls_name).datepicker( {
                    format: "yyyy-mm-dd",
                    viewMode: "dates",
                    minViewMode: "dates",
                    autoclose: true,
                });

                $('.'+to_date_cls_name).datepicker( {
                    format: "yyyy-mm-dd",
                    viewMode: "dates",
                    minViewMode: "dates",
                    autoclose: true,
                });
            });

            $('.'+target_type_cls).on('change', function (e) {

                // alert($('.'+target_type_cls).val() + conv_string);
                if($('.'+target_type_cls).val() == 'brand'){
                    $('.'+full_brand_cls).show();
                    $('.'+full_segment_cls).hide();
                    $('.'+full_family_cls).hide();
                }
                else if($('.'+target_type_cls).val() == 'family'){
                    $('.'+full_brand_cls).hide();
                    $('.'+full_segment_cls).hide();
                    $('.'+full_family_cls).show();
                }
                else if($('.'+target_type_cls).val() == 'segment'){
                    $('.'+full_brand_cls).hide();
                    $('.'+full_segment_cls).show();
                    $('.'+full_family_cls).hide();
                }
                else{
                    $('.'+full_brand_cls).hide();
                    $('.'+full_segment_cls).hide();
                    $('.'+full_family_cls).hide();
                }
                
                $('#formId').validator('update');
            });


            $('.'+property_div_cls).hide();
            $('.'+full_brand_cls).hide();
            $('.'+full_segment_cls).hide();
            $('.'+full_family_cls).hide();
        });
            

        $("#config_name").on('change', function (e) {

            var config_id = $('#config_name').val();

            $('#kpi_config_id').val(config_id);
            // alert(config_id);
            var url = "{{url('get-kpi-config-details')}}/"+config_id;
            $.get(url, function(data, status){
                $('#con-name').text(data.bat_kpi_configs_name);
                $('#con-peramiter').html(data.config_details);
                $('#con-time-range').html(data.start_month+"-"+data.end_month);
                $('#con-range').html(data.kpi_range);
                $('#con-scope').text(data.market_scope);
                $('.config-details-tbl').show();
                $('.form-submit').show();
                $('.ff_type_cls').show();

                $('.common_div_cls').hide();

                if(data.property_ary.length > 0){
                     $.each(data.property_ary, function( key, val ){
                        // console.log(val);
                        $('.'+val).show();
                     });
                }
            });
            $('#formId').validator('update');
        }); 

        $(document).on('click','.form-submit',function (e){
        // $("#formId").submit(function(e){
            e.preventDefault();

            Ladda.bind('#formBtn');
            var load = $('#formBtn').ladda();

            // swalConfirm('Are you sure?').then(function(s) {
                var data = $('#formId').serialize();

                var url = "{{url('kpi-assign-form-store')}}";
                
                makeAjaxPost(data,url,load).done(function (response) {
                    if(response.code == 500){
                        swalError(response.msg); 
                    }
                    else{
                        swalRedirect("{{url('assigned-kpi-list')}}", response.msg, 'success'); 

                    }
                });
            // });
        });   

        $('#formId').validator();

    });
</script>
@endsection