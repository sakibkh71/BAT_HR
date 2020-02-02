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
