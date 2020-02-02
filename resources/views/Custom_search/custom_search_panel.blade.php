<div class="col-md-12 mb-2 row">
    <div class="col-md-6">
        <div class="input-group search_option_group">
            @php($search_attribute = array(
                'class' => 'search_option_filter',
                'id' => $search_slug,
                'multiple' => 'multiple'
            ))
            {{ Form::select('', $search_options, array(), $search_attribute) }}
        </div>
    </div>
</div>
{{--=======================================--}}
<style>
    .btn-custom-search, .btn-custom-search:focus{
        box-shadow: none;
        height: calc(1.5rem) !important;
        border-top: 0;
        border-left: 0;
        border-right: 0;
        border-bottom: 1px solid #1ab394;
        background: transparent;
        padding-left: 0px !important;
        padding-top: 0px !important;
        padding-bottom: 0px !important;
    }
    button.btn-custom-search .caret {
        margin-top: 7px;
        position: absolute;
        right: 0px;
        top: 1px;
    }
</style>
{{--=======================================--}}
<script>
    $('.search_option_filter').multiselect({
        enableHTML: true,
        selectAllValue: 'multiselect-all',
        nonSelectedText: 'Select options to filter',
        nSelectedText: 'Items has been selected to filter',
        buttonClass:'btn btn-custom-search',
        buttonWidth: 'auto',
        enableFiltering: false,
        buttonText: function(options, select) {
            var search_icon = '<b><span class="glyphicon glyphicon-search"></span> </b>';
            if (options.length == 0){
                return search_icon + '<b>' + this.nonSelectedText + '</b> <b class="caret"></b>';
            }else{
                if (options.length > 4) {
                    return search_icon + '<b> ' +options.length + ' ' + this.nSelectedText + '</b> <b class="caret"></b>';
                }else{
                    var selected = search_icon + '<b> Filter by - </b>';
                    options.each(function() {
                        var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).html();
                        if($(select).hasClass('multiselect-icon')){
                            var icon = $(this).data('icon');
                            label = '<b><span class="glyphicon ' + icon + '"></span> </b>' + label;
                        }
                        selected += label + ', ';
                    });
                    return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
                }
            }
        },
        onChange: function(element, checked) {
            var brands = $('.search_option_filter option:selected');
            var selected = [];
            $(brands).each(function(index, brand){
                selected.push([$(this).val()]);
            });
            var slug = "{{$search_slug}}";
            var data = {slug:slug, data:selected};
            var url = '{{URL::to("session-search-filter")}}';
            makeAjaxPostText(data, url).then(function(result){
                displaySearchBox(selected);
            });
        }
    });
    function displaySearchBox(selected){
        $('.search_box').hide();
        $.each(selected, function( index, value ) {
            $('#'+value).show();
        });
    }
</script>
{{--=======================================--}}
@if(isset($searched_value) && !empty($searched_value))
    @php($is_post = true)
@else
    @php($is_post = false)
@endif
@foreach($search_fields as $search_field)
    @switch($search_field->operation_type)
        @case('WHERE LIKE') @php($operation_type = 'WH-LK-') @break
        @case('WHERE EQUAL') @php($operation_type = 'WH-EQ-') @break
        @case('WHERE IN') @php($operation_type = 'WH-IN-') @break
        @case('WHERE DATERANGE') @php($operation_type = 'WH-DR-') @break
        @case('WHERE DATETIME') @php($operation_type = 'WH-DT-') @break
        @case('WHERE RANGE') @php($operation_type = 'WH-RG-') @break
        @case('HAVING LIKE') @php($operation_type = 'HV-LK-') @break
        @case('HAVING EQUAL') @php($operation_type = 'HV-EQ-') @break
        @case('HAVING IN') @php($operation_type = 'HV-IN-') @break
        @case('HAVING DATERANGE') @php($operation_type = 'HV-DR-') @break
        @case('HAVING DATETIME') @php($operation_type = 'HV-DT-') @break
        @case('HAVING RANGE') @php($operation_type = 'HV-RG-') @break
        @default @php($operation_type = 'WH-LK-')
    @endswitch
    @if($prefix == true)
        @php($input_name = $operation_type.$search_field->input_name)
    @else
        @php($input_name = $search_field->input_name)
    @endif
    @php($label_name = ucwords(str_replace('_',' ', $search_field->label_name)))
    @php($placeholder = $search_field->placeholder)
    @php($class_name = $search_field->input_class)
    @php($label_class = $search_field->label_class)
    @php($id_name = $search_field->input_id)
    @php($input_type = $search_field->input_type)
    @php($required = $search_field->required)
    @php($dropdown_slug = $search_field->dropdown_slug)
    @php($dropdown_options = $search_field->dropdown_options)
    @php($dropdown_view = $search_field->dropdown_view)
    @php($dropdown_grid_name = $search_field->dropdown_grid_name)
    @php($single_compare = $search_field->single_compare)
    @php($default_value = $search_field->default_value)
    @php($multiple = $search_field->multiple)
    @php($field_value = $default_value)
    @php($gridVal = 'col-md-'.$search_field->column_space)
    {{-----------------------------}}
    {{--after posting the searching key have to return the selected data as in `searched_value` array with the index of same input name--}}
    {{-----------------------------}}
    @php($compare_option = [])
    @if($single_compare == 0)
        @php($compare_option['between'] = 'BETWEEN')
    @endif
    @php($compare_option['='] = '=')
    @php($compare_option['<='] = '<=')
    @php($compare_option['<'] = '<')
    @php($compare_option['>='] = '>=')
    @php($compare_option['>'] = '>')
    {{-----------------------------}}
    @if($input_type == 'checkbox')
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                <br/>
                @php($field_value = $is_post && $field_value == null && isset($searched_value[$input_name]) ? $searched_value[$input_name] : 0)
                <input type="checkbox" id="{{$id_name}}" name="" value="{{$field_value}}" class="custom-check" {{$field_value == 1 ? 'checked': ''}}>
                <label class="{{$label_class}}" for="{{$id_name}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <input class="{{$id_name}}" type="hidden" name="{{$input_name}}" value="{{$field_value}}">
            </div>
        </div>
    {{--===========================================================================================--}}
    @elseif($input_type == 'date')
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                @if(!$is_post && $default_value == null)
                    @php($field_value = null)
                @elseif(!$is_post && validateDate($field_value) == false)
                    @php($field_value = currentDate())
                @else
                    @php($field_value =  isset($searched_value[$input_name]) ? $searched_value[$input_name] : null)
                @endif
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    <input type="text"
                           name="{{$input_name}}"
                           value="{{$field_value}}" class="{{$class_name}} datepicker"
                           placeholder="{{$placeholder}}"
                            {{ $required == 1 ? 'required' : '' }}>
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    @elseif($input_type == 'month')
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                @if(!$is_post && $default_value == null)
                    @php($field_value = null)
                @elseif(!$is_post && validateDate($field_value) == false)
                    @php($field_value = currentDate())
                @else
                    @php($field_value =  isset($searched_value[$input_name]) ? $searched_value[$input_name] : null)
                @endif
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    <input type="text"
                           name="{{$input_name}}"
                           value="{{$field_value}}" class="{{$class_name}} monthpicker"
                           placeholder="{{$placeholder}}"
                            {{ $required == 1 ? 'required' : '' }}>
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    @elseif($input_type == 'year')
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                @if(!$is_post && $default_value == null)
                    @php($field_value = null)
                @elseif(!$is_post && validateDate($field_value) == false)
                    @php($field_value = currentDate())
                @else
                    @php($field_value =  isset($searched_value[$input_name]) ? $searched_value[$input_name] : null)
                @endif
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    <input type="text"
                           name="{{$input_name}}"
                           value="{{$field_value}}" class="{{$class_name}} yearpicker"
                           placeholder="{{$placeholder}}"
                            {{ $required == 1 ? 'required' : '' }}>
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    {{--===========================================================================================--}}
    @elseif($input_type == 'date_range')
        @php($field_value = $is_post && isset($searched_value[$input_name]) ? $searched_value[$input_name] : $default_value)
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    <input type="text"
                           name="{{$input_name}}"
                           value="{{$field_value}}" class="{{$class_name}} daterange"
                           placeholder="{{$placeholder}}"
                            {{ $required == 1 ? 'required' : '' }}>
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    {{--===========================================================================================--}}
    @elseif($input_type == 'number')
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                @php($field_value = $is_post && isset($searched_value[$input_name]) ? $searched_value[$input_name] : '')
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    <input type="number"
                           name="{{$input_name}}"
                           value="{{$field_value}}" class="{{$class_name}}"
                           placeholder="{{$placeholder}}"
                            {{ $required == 1 ? 'required' : '' }}>
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    {{--===========================================================================================--}}
    @elseif($input_type == 'number_range')
        @if($field_value == $is_post && isset($searched_value[$input_name]))
            @php($start_field_value = $searched_value[$input_name.'_start'])
            @php($condition_field_value = $searched_value[$input_name.'_condition'])
            @php($end_field_value = $searched_value[$input_name.'_end'])
        @else
            @php($start_field_value = $default_value)
            @php($condition_field_value = '')
            @php($end_field_value = $default_value)
        @endif
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    @if($single_compare == 0)
                        <input type="text"
                               name="{{$input_name.'_start'}}"
                               value="{{$start_field_value}}" class="{{$class_name}}"
                               placeholder="{{$placeholder}}"
                                {{ $required == 1 ? 'required' : '' }}>
                    @endif
                    {{ Form::select($input_name.'_condition', $compare_option, $condition_field_value, array('class' => 'form-control')) }}
                    <input type="text"
                           name="{{$input_name.'_end'}}"
                           value="{{$end_field_value}}" class="{{$class_name}}"
                           placeholder="{{$placeholder}}"
                            {{ $required == 1 ? 'required' : '' }}>
                </div>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    {{--===========================================================================================--}}
    @elseif($input_type == 'dropdown')
        @if($dropdown_view == 'grid')
            @php($dropdown_grid = url('modalgrid/'.$dropdown_grid_name))
            <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
                <div class="form-group">
                    <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                    <div class="input-group" >
                        <input type="text" class="{{$class_name}}" readonly>
                        <span class="input-group-append">
                            <span class="btn btn-info load_grid" data-style="zoom-in" data-gridurl="{{$dropdown_grid}}"><i class="fa fa-list"></i></span>
                        </span>
                    </div>
                </div>
            </div>
        @else
            @if($dropdown_slug != '')
                @php($field_value = $is_post && isset($searched_value[$input_name]) ? $searched_value[$input_name] : $default_value)
                <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
                    <div class="form-group">
                        <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                        @php($arr = array('name'=>$input_name.'[]', 'selected_value'=>$field_value))
                        {!! __combo($dropdown_slug, $arr) !!}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            @else
                @php($option_arr = [])
                @php($field_value = $is_post && isset($searched_value[$input_name]) ? $searched_value[$input_name] : $default_value)
                <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
                    {{--@debug($input_name)--}}
                    <div class="form-group">
                        <label class="{{$label_class}}">{{$label_name}}</label>
                        <?php
                            $enum_options = $dropdown_options;
                            if (!empty($enum_options)) {
                                foreach (explode(',', $enum_options) as $options) {
                                    $option_arr[$options] = ucfirst($options);
                                }
                            }
                        ?>
                        @if($multiple == 1)
                            {{Form::select($input_name.'[]', $option_arr, $field_value, array('class' => ''.$class_name, 'multiple' => $multiple))}}
                        @else
                            {{Form::select($input_name, $option_arr, $field_value, array('class' => ''.$class_name))}}
                        @endif
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            @endif
        @endif
    {{--===========================================================================================--}}
    @elseif($input_type == 'autocomplete')
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                @php($field_value = $is_post && isset($searched_value[$input_name]) ? $searched_value[$input_name] : $default_value)
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <div class="input-group">
                    <input type="text" id="{{$id_name}}"
                           class="{{$class_name}} custom-search-autocomplete"
                           data-slug="{{$id_name}}"
                           data-name="{{$input_name}}"
                           data-value="{{$field_value}}"
                           data-editvalueurl="<?php echo URL::to('custom-search-autocomplete-query/edit/'.$search_field->sys_search_panel_details_id);?>"
                           data-autocompleteurl="<?php echo URL::to('custom-search-autocomplete-query/search/'.$search_field->sys_search_panel_details_id);?>"/>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>

    @elseif($input_type == 'geo_location')
        @php($serachArray = ['location'=>'Point', 'point_name'=>$input_name])
        <div class="col-md-12 search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
        @include('location_access.search_criteria_multiple', $serachArray)
        </div>
    {{--===========================================================================================--}}
    @else
        <div class="{{$gridVal}} search_box no-display" id="{{$search_field->sys_search_panel_details_id}}">
            <div class="form-group">
                @php($field_value = $is_post && isset($searched_value[$input_name]) ? $searched_value[$input_name] : $default_value)
                <label class="{{$label_class}}">{!! $required == 1 ? '<span class="required">*</span>' : '' !!}{{$label_name}}</label>
                <input type="text"
                       name="{{$input_name}}"
                       value="{{$field_value}}" class="{{$class_name}}"
                       placeholder="{{$placeholder}}"
                        {{ $required == 1 ? 'required' : '' }}>
                <div class="help-block with-errors has-feedback"></div>
            </div>
        </div>
    @endif
@endforeach
<script>
    $(document).ready(function(){
        var sess_data_arr = [];
        var session_data = "{{$session_filter}}";
        if(session_data.length > 0){
            sess_data_arr = session_data.split(',');
            $(".search_option_filter").val(sess_data_arr);
            $('.search_option_filter').multiselect("refresh");
            displaySearchBox(sess_data_arr);
        }
    });
    $('.custom-search-autocomplete').each(function() {
        var $el = $(this);
        var slug = $el.data('slug');
        var value = $el.data('value');
        var name = $el.data('name');
        var source = $el.data('autocompleteurl');
        if(value.length !== 0){
            var edit_source = $el.data('editvalueurl')+'/'+value;
            makeAjax(edit_source).done(function(response){
                $('#'+slug).val(response.data_option);
            });
        }
        var hint_html = '';
        var shade_style = 'color: #CCC; width: 100%; position: absolute; background: transparent; z-index: 1;';
        hint_html += '<input class="form-control autocomplete-shade" id="shade-'+slug+'" disabled="disabled" style="'+shade_style+'"/>';
        hint_html += '<input type="hidden" name="'+name+'[]" class="autocomplete-value" value="'+value+'" id="value-'+slug+'" value=""/>';
        $el.after(hint_html);
        $el.autocomplete({
            selectFirst: true,
            autoFocus: true,
            serviceUrl: source,
            onSelect: function(suggestion) {
                var slug = $el.data('slug');
                $('#value-'+slug).val(suggestion.data);
            },
            onHint: function (hint) {
                var slug = $el.data('slug');
                $('#shade-'+slug).val(hint);
            },
            onInvalidateSelection: function() {
                console.log('invalids');
                var slug = $el.data('slug');
                $('#value-'+slug).val('');
            }
        });
    });
    function makeAjax(url, load) {
        return $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            cache: false,
            beforeSend: function(){
                if(typeof(load) != "undefined" && load !== null){
                    load.ladda('start');
                }
            }
        }).always(function() {
            if(typeof(load) != "undefined" && load !== null){
                load.ladda('stop');
            }
        }).fail(function() {
            swalError();
        });
    }
</script>