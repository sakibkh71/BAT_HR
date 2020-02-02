<!-- <div class="ibox-content">
    <div class="row"> -->

        <input type="hidden" id="hdn_val" name="" value="{{$exclude}}">
        <input type="hidden" id="option_key_js" name="" value="{{$option_key_js}}">

        @if(in_array('geo_location_1', $option_key))
            <div class="col-sm-3">
                <label><b>@if(!empty($option_value[1])){{$option_value[1]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_1[]" multiple="multiple" data-error="Status mandatory" required="" id="geo_loc_1">
                    @foreach($geo_location_1_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        @endif
        @if(in_array('geo_location_2', $option_key))
            <div class="col-sm-3" id="div_geo_loc_2">
                <label><b>@if(!empty($option_value[2])){{$option_value[2]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_2[]" multiple="multiple"  data-error="Status mandatory" required="" id="geo_loc_2">
                    @foreach($geo_location_2_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(in_array('geo_location_3', $option_key))
            <div class="col-sm-3" id="div_geo_loc_3">
                <label><b>@if(!empty($option_value[3])){{$option_value[3]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_3[]" multiple="multiple"  data-error="Status mandatory" required="" id="geo_loc_3">
                    @foreach($geo_location_3_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif   
        @if(in_array('geo_location_4', $option_key))
            <div class="col-md-4" id="div_geo_loc_4">
                <label><b>@if(!empty($option_value[4])){{$option_value[4]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_4[]" multiple="multiple"  data-error="Status mandatory" required="" id="geo_loc_4">
                    @foreach($geo_location_4_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(in_array('geo_location_5', $option_key))
            <div class="col-md-4" id="div_geo_loc_5">
                <label><b>@if(!empty($option_value[5])){{$option_value[5]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_5[]" multiple="multiple" data-error="Status mandatory" required="" id="geo_loc_5">
                    @foreach($geo_location_5_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(in_array('geo_location_6', $option_key))
            <div class="col-md-4" id="div_geo_loc_6">
                <label><b>@if(!empty($option_value[6])){{$option_value[6]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_6[]" multiple="multiple" data-error="Status mandatory" required="" id="geo_loc_6">
                    @foreach($geo_location_6_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        
        @if(in_array('geo_location_7', $option_key))
            <div class="col-sm-3" id="div_geo_loc_7">
                <label><b>@if(!empty($option_value[7])){{$option_value[7]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_7[]" multiple="multiple" data-error="Status mandatory" required="" id="geo_loc_7">
                    @foreach($geo_location_7_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(in_array('geo_location_8', $option_key))
            <div class="col-sm-3" id="div_geo_loc_8">
                <label><b>@if(!empty($option_value[8])){{$option_value[8]}}@endif</b></label>
                <select class="form-control geo_loc_cls" name="geo_loc_8[]" multiple="multiple" data-error="Status mandatory" required="" id="geo_loc_8">
                    @foreach($geo_location_8_ary as $info)
                        <option value="{{$info['id']}}">{{$info['name']}}</option>
                    @endforeach
                </select>
            </div>
        @endif 
        
    <!-- </div> -->
<!-- </div> -->

<script src="{{asset('public/js/locationDropDown.js')}}"></script>