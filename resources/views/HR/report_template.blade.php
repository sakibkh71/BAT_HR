    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            @if(isset($complex_header) && !empty($complex_header))
                <tr>
                    @foreach($complex_header as $col => $header)
                        @php
                            @$col = isset($header['col'])?$header['col']:0;
                            @$row = isset($header['row'])?$header['row']:0;
                            @$text = isset($header['text'])?$header['text']:'';
                        @endphp
                        <th {{$col>0?"colspan=$col":''}} {{$row>0?"rowspan=$row":''}}  {!! @$style??'' !!}>{!! ucfirst(str_replace('_', ' ', $text)) !!}</th>
                    @endforeach
                </tr>
            @endif
            @if(isset($complex_header2) && !empty($complex_header2))
                <tr>
                    @foreach($complex_header2 as $col => $header2)
                        @php
                            $col2 = isset($header2['col'])?$header2['col']:0;
                            $row2 = isset($header2['row'])?$header2['row']:0;
                            $text2 = isset($header2['text'])?$header2['text']:'';
                        @endphp
                        <th {{$col2>0?"colspan=$col2":''}} {{$row2>0?"rowspan=$row2":''}}>{!! ucfirst(str_replace('_', ' ', $text2)) !!}</th>
                    @endforeach
                </tr>
            @endif
            <tr>
                @if(isset($table_header))
                    @foreach($table_header as $col => $header)
                        <th>{!! ucfirst(str_replace('_', ' ', $header)) !!}</th>
                    @endforeach
                @else
                    @if(isset($report_data))
                        @foreach($report_data as $row)
                            @foreach($row as $col => $val)
                                <th>{!! ucfirst(str_replace('_', ' ', tdFormatter($col))) !!}</th>
                            @endforeach
                            @break
                        @endforeach
                    @endif
                @endif
            </tr>
            </thead>
            <tbody>
            @if(isset($report_data))
                @foreach($report_data as $grid_data)
                    <tr>
                        @foreach($grid_data as $col => $val)
                            {!! tdDataFormatter($col,$grid_data->$col) !!}
                        @endforeach
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
