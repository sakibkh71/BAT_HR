@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title"><h2>KPI Settings</h2></div>
                    <div class="ibox-content">
                        <form action="{{route('kpi-store', isset($kpi->kpi_config_code)?$kpi->kpi_config_code:'')}}" method="post" id="kpiForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">Config Name</label>
                                        <div class="input-group ">
                                            <input type="text" name="bat_kpi_configs_name" class="form-control" value="{{isset($kpi->bat_kpi_configs_name) ? $kpi->bat_kpi_configs_name : old('bat_kpi_configs_name')}}"  id="bat_kpi_configs_name" name="bat_kpi_configs_name" required/>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">Config Month</label>
                                        <div class="input-group">
                                            <input type="text"  name="config_month"  class="form-control months_picker" value="{{isset($kpi->config_month) ? $kpi->config_month : old('config_month')}}" id="config_month" name="config_month" required/>
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">Field Force Type</label>
                                        <div class="input-group ">
                                            @php
                                                if(!empty($kpi->selected_ff_type)){
                                                    $ff_type = explode(',',$kpi->selected_ff_type);
                                                }
                                            @endphp
                                            {{ __combo("designations", array("selected_value"=> isset($ff_type) ? $ff_type : old('selected_ff_type'), "attributes"=>array("name"=>"selected_ff_type[]","class"=>"form-control multi",  "id"=>"selected_ff_type", "required"=>true)))}}
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                @php echo $multiple_search_criteria; @endphp
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-12"> <h3>Select KPI</h3></div>
                            </div>

                            @if(!empty($components))
                            <table class="kpi-component-table">
                                @foreach($components as $component)

                                    @php($kpiCompArray=[])
                                    @if(!empty($component->kpi_components))
                                       @php($kpiCompArray = explode(',', $component->kpi_components))
                                    @endif

                                <tr class="kpi-row @if(isset($kpi_details[$component->bat_kpi_id]['bat_kpi_id']) && $kpi_details[$component->bat_kpi_id]['bat_kpi_id'] == $component->bat_kpi_id) active @elseif(isset(old("bat_kpi_id")[$component->bat_kpi_id]) && old("bat_kpi_id")[$component->bat_kpi_id] == $component->bat_kpi_id) active @endif">
                                    <td width="20%">
                                        <input type="checkbox" class="custom-check bat_kpi_id" id="kpi{{$component->bat_kpi_id??''}}" name="bat_kpi_id[{{$component->bat_kpi_id??''}}]" value="{{$component->bat_kpi_id??''}}" @if(isset($kpi_details[$component->bat_kpi_id]['bat_kpi_id']) && $kpi_details[$component->bat_kpi_id]['bat_kpi_id'] == $component->bat_kpi_id) checked @elseif(isset(old("bat_kpi_id")[$component->bat_kpi_id]) && old("bat_kpi_id")[$component->bat_kpi_id] == $component->bat_kpi_id) checked @endif>
                                        <label for="kpi{{$component->bat_kpi_id}}"><strong>{{$component->bat_kpi_name}}</strong></label>
                                    </td>
                                    <td width="20%">
                                        <div class="form-group">
                                            <label class="form-label">Weight %</label>
                                            <div class="input-group ">
                                                <input type="text" class="form-control weight number" value='{{ isset($kpi_details[$component->bat_kpi_id]["weight"]) ? $kpi_details[$component->bat_kpi_id]["weight"] : old("weight")[$component->bat_kpi_id]}}'  id="weight{{$component->bat_kpi_id??''}}" name="weight[{{$component->bat_kpi_id??''}}]"/>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </td>
                                    <td width="20%">
                                        @if(!empty($kpiCompArray) && in_array("brands", $kpiCompArray))
                                        <div class="form-group">
                                            <label class="form-label">Brands</label>
                                            <div class="input-group">
                                                @php($old_brand = old("target_brands"))
                                                @php($selected_brand = isset($kpi_details[$component->bat_kpi_id]['target_brands']) ?$kpi_details[$component->bat_kpi_id]['target_brands'] : (isset($old_brand[$component->bat_kpi_id])?$old_brand[$component->bat_kpi_id]:[]))
                                                <select name="target_brands[{{$component->bat_kpi_id}}][]" class="form-control target_brands multi" id="target_brands{{$component->bat_kpi_id}}" multiple="true">
                                                    @foreach($brands_list as $brand_key=>$brand)
                                                    <option value="{{$brand_key}}" @if(!empty($selected_brand) && in_array($brand_key, $selected_brand)) selected @endif>{{$brand}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                        @endif
                                    </td>
                                    <td width="20%">
                                        @if(!empty($kpiCompArray) && in_array("family", $kpiCompArray))
                                        <div class="form-group">
                                            <label class="form-label">Family</label>
                                            <div class="input-group">
                                                @php($old_family = old("target_familys"))
                                                @php($selected_family = isset($kpi_details[$component->bat_kpi_id]['target_familys']) ? $kpi_details[$component->bat_kpi_id]['target_familys'] : (isset($old_family[$component->bat_kpi_id])?$old_family[$component->bat_kpi_id]:[]))

                                                <select name="target_familys[{{$component->bat_kpi_id}}][]" class="form-control target_familys multi" id="target_familys{{$component->bat_kpi_id}}" multiple="true">
                                                    @foreach($family_list as $family_key=>$family)
                                                        <option value="{{$family_key}}" @if(!empty($selected_family) && in_array($family_key, $selected_family)) selected @endif>{{$family}}</option>
                                                    @endforeach
                                                </select>

                                                {{--{{ __combo("bat_familys_list", array("selected_value"=> isset($kpi_details[$component->bat_kpi_id]['target_familys']) ? $kpi_details[$component->bat_kpi_id]['target_familys'] : old("target_familys"), "attributes"=>array("name"=>"target_familys[$component->bat_kpi_id][]","class"=>"form-control target_familys multi",  "id"=>"target_familys$component->bat_kpi_id")))}}--}}
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                        @endif
                                    </td>
                                    <td width="20%">
                                        @if(!empty($kpiCompArray) && in_array("segment", $kpiCompArray))
                                        <div class="form-group">
                                            <label class="form-label">Segment</label>
                                            <div class="input-group">
                                                @php($old_segment = old("target_segments"))
                                                @php($selected_segment = isset($kpi_details[$component->bat_kpi_id]['target_segments']) ? $kpi_details[$component->bat_kpi_id]['target_segments'] : (isset($old_segment[$component->bat_kpi_id])?$old_segment[$component->bat_kpi_id]:[]))

                                                <select name="target_segments[{{$component->bat_kpi_id}}][]" class="form-control target_segments multi" id="target_segments{{$component->bat_kpi_id}}" multiple="true">
                                                    @foreach($segments_list as $segments_key=>$segments)
                                                        <option value="{{$segments_key}}" @if(!empty($selected_segment) && in_array($segments_key, $selected_segment)) selected @endif>{{$segments}}</option>
                                                    @endforeach
                                                </select>
                                                {{--{{ __combo("bat_segments_list", array("selected_value"=>  isset($kpi_details[$component->bat_kpi_id]['target_segments']) ? $kpi_details[$component->bat_kpi_id]['target_segments'] : old("target_segments"), "attributes"=>array("name"=>"target_segments[$component->bat_kpi_id][]","class"=>"form-control target_segments multi",  "id"=>"target_segments$component->bat_kpi_id")))}}--}}
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                            </table>
                            @endif
                            <div class="row">
                                <div class="col-md-12 mt-1">
                                    <button id="kpi_submit" class="btn btn-lg btn-success" type="button" data-type="{{ isset($kpi->kpi_config_code)?'update' :'new' }}">{{  isset($kpi->kpi_config_code)?'Update' :'Submit' }}</button>
                                    <button id="kpi_reset" class="btn btn-lg btn-danger ml-2" type="button" onclick=" window.location.reload();">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .kpi-component-table{
            border:none;
            width: 100%;
        }
        .kpi-component-table td{
            padding:0 10px;
            border: none;
        }
        input.custom-check + label{
            display: inline-block;
        }
        input.custom-check + label strong{
            font-size: 14px;
        }
        .kpi-row .form-group{
            pointer-events: none;
            opacity: 0.6;
        }
        .kpi-row.active .form-group{
            pointer-events: auto;
            opacity: 1;
        }
    </style>

    <script>
        (function ($) {
            $('#kpiForm').validator();

            //calculate Weight Value
            function calWeight(){
                var weightVal = 0;
                $('.weight').each(function() {
                    if ($(this).val() !='') {
                        weightVal += parseFloat($(this).val());
                    }
                });
                return weightVal;
            }

            $('.bat_kpi_id').change(function () {
                var self = $(this);
                var parent_row = self.closest('.kpi-row');

                if (self.is(':checked')) {
                    parent_row.addClass('active');
                }else{
                    parent_row.removeClass('active');
                    parent_row.find(".weight").val('');
                    parent_row.find(".target_brands").val('');
                    parent_row.find(".target_familys").val('');
                    parent_row.find(".target_segments").val('');
                    $('.multi').multiselect('rebuild');
                }
            });

            //Weight Change
            $(document).on('keyup', '.weight', function (e) {
                var total_weight =  calWeight();
                if (total_weight > 100){
                    swalError('You can provide maximum 100% of weight');
                    $(this).val('');
                }
            });

            //form submit
            $('#kpi_submit').click(function (e) {
                if (!$('#kpiForm').validator('validate').has('.has-error').length) {
                    var error = false;
                    var total_weight = 0;

                    var checked_kpi_id = $('.bat_kpi_id:checked').each(function () {
                        return parseInt($(this).val());
                    });

                    if ($('#point').val() ==''){
                        var error = 'Please Select Point';
                    }else if (checked_kpi_id.length > 0) {
                        $('.kpi-row.active').each(function () {
                            var weight = $(this).find(".weight").val();
                            total_weight = total_weight + parseFloat(weight);

                            var target_brands = $(this).find(".target_brands").val();
                            var target_familys = $(this).find(".target_familys").val();
                            var target_segments = $(this).find(".target_segments").val();

                            if (weight == '' || (target_brands == '' && target_familys == '' && target_segments == '')) {
                                error = 'Please provide Weight and Brands or Family or Segment data properly';
                            }
                        });

                        if (total_weight != 100) {
                            error = 'Total Weight not 100%'
                        }

                    } else {
                        error = 'Please Select KPI Component';
                    }

                    //If not any error submit form
                    if (!error) {
                        swalConfirm('To submit this form?').then(function (e) {
                            if(e.value) { $("#kpiForm").submit(); }
                        });
                    } else {
                        swalError(error);
                    }
                }
            });
        })(jQuery);

    </script>
@endsection
