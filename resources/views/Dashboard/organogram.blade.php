@if(!empty($data))
<div id="jstree1">
    <ul>
        @foreach($data as $item)
        <li class="jstree-open">{{$item['house']}}
            <ul>
                @if(!empty($item['ss']))
                @foreach($item['ss'] as $ss)
                <li>{{ $ss['name']}}
                    @if(!empty($ss['sr']))
                    <ul>
                        @foreach($ss['sr'] as $sr)
                            <li data-sr_id='{{$sr['srid']}}'>{{$sr['sr_name']}}</li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
                @endif

                {{--<li data-jstree='"type":"html"}'> affix.html</li> --}}
            </ul>
        </li>
        @endforeach
    </ul>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#jstree1').jstree({
            'core' : {
                'check_callback' : true
            },
            'plugins' : [ 'types', 'dnd' ],
            'types' : {
                'default' : {
                    'icon' : 'fa fa-folder'
                },
                'html' : {
                    'icon' : 'fa fa-file-code-o'
                },
                'svg' : {
                    'icon' : 'fa fa-file-picture-o'
                },
                'css' : {
                    'icon' : 'fa fa-file-code-o'
                },
                'img' : {
                    'icon' : 'fa fa-file-image-o'
                },
                'js' : {
                    'icon' : 'fa fa-file-text-o'
                }
            }
        });

    });
</script>
@endif