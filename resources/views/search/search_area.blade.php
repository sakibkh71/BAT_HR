<div class="panel panel-default " id="search_by" style='display:none'>
    <div class="panel-heading" style="overflow: hidden;">
        <span style="float: left;">SEARCH BY</span>
        <span class="top_search" style="float: right; color: #C9302C; cursor: pointer;"><span class="glyphicon glyphicon-remove"></span></span>
    </div>
    <div class="panel-body">
        <form data-toggle="validator" role="form" id="custom_search" action="" method="post">
            @php
                $starMark = '<span style="color:red; font-weight:bold">*</span>';
            @endphp
            <div class="col-lg-12">
                <div class="row">
                    @foreach($page_data['custom_search'] as $cust_src)
                        @php
                            /*dd($cust_src)*/
                        @endphp
                        @if($cust_src['type'] == 'text' || $cust_src['type'] == 'number' )
                            <div class="col-lg-4 form-group">
                                <label>{{$cust_src['label']}} {!! ($cust_src['mandatory'])?$starMark:'' !!}</label>
                                <input type="{{$cust_src['type']}}"
                                       placeholder="{{isset($cust_src['placeholder']) ? 'Search ' . $cust_src['placeholder'] : 'Search ' . $cust_src['name']}}"
                                       class="form-control {{$cust_src['class']}}"
                                       id="{{isset($cust_src['id']) ? $cust_src['id'] : $cust_src['name']}}"
                                       name="{{$cust_src['name']}}"
                                       value=""/>
                            </div>
                        @endif
                        @if($cust_src['type'] == 'daterange')
                            <div class="col-lg-4 form-group">
                                <label>{{$cust_src['label']}} {!! ($cust_src['mandatory'])?$starMark:'' !!}</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"
                                           placeholder="{{isset($cust_src['placeholder']) ?  $cust_src['placeholder'] : 'Search ' . $cust_src['name']}}"
                                           class="form-control pull-left daterange {{$cust_src['class']}}"
                                           id="{{isset($cust_src['id']) ? $cust_src['id'] : $cust_src['name']}}"
                                           name="{{$cust_src['name']}}"/>
                                </div>
                            </div>
                        @endif
                        @if($cust_src['type'] == 'select')
                            <div class="col-lg-4 form-group">
                                <label>{{$cust_src['label']}} {!! ($cust_src['mandatory'])?$starMark:'' !!}</label>
                                @if(isset($cust_src['combo_slug']))
                                    {!! __combo($cust_src['combo_slug'], $cust_src['combo_slug_conf']) !!}
                                @else
                                    <select class="form-control {{$cust_src['class']}} selectbox"
                                            id="{{isset($cust_src['id']) ? $cust_src['id'] : $cust_src['name']}}"
                                            name="{{$cust_src['name']}}"
                                            data-dataprovider = "{{$cust_src['dataprovider']}}"></select>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-lg-12 text-right">
                {{--<input search_type="download" class="btn btn-primary search_submit" type="submit" value="Export">--}}
                {{--<input search_type="show" class="btn btn-primary search_submit ladda-button" type="button" value="Search">--}}
                <button search_type="download" class="btn btn-primary search_submit" id="export-submit">Export</button>
                <button search_type="show" class="btn btn-primary search_submit" id="search-submit">Search</button>
            </div>
        </form>
    </div>
</div>



{{--@include('search.search_view_css_js')--}}
{{--@if(in_array('show',$searchAreaOption))--}}
    {{--<script>--}}
        {{--$(document).ready(function(){--}}
            {{--$("#top_search").trigger('click');--}}
        {{--});--}}
    {{--</script>--}}
{{--@endif--}}


