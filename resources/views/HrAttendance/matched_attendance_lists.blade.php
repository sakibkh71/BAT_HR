<table class="checkbox-clickable table table-striped table-bordered table-hover emp-attendance-list">
    <thead>
    <tr>
        <th rowspan="2">{{__lang('SL')}}#</th>
        <th rowspan="2">{{__lang('User Code')}}</th>
        <th rowspan="2">{{__lang('status')}}</th>
        <th rowspan="2">{{__lang('Day')}}</th>
        <th rowspan="2">{{__lang('Start Time')}}</th>
        <th rowspan="2">{{__lang('End Time')}}</th>
        <th rowspan="2">{{__lang('Break Time')}}</th>
        <th rowspan="2">{{__lang('Over Time')}}</th>
        <th rowspan="2">{{__lang('Total Working Time')}}</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($attendance_history))
        @foreach($attendance_history as $i=>$value)
            <tr class="item" code="{{$value->user_code}}" day_is="{{$value->day_is}}">
                <td align="center">
                    {{($i+1)}}
                </td>
                <td>{{!empty($value->user_code)?$value->user_code:'N/A'}}</td>
                <td>{{!empty($value->status)?$value->status:'N/A'}}</td>
                <td>{{!empty($value->day_is)?$value->day_is:'N/A'}}</td>
                <td>{{!empty($value->start_date_time)?$value->start_date_time:'N/A'}}</td>
                <td>{{!empty($value->end_date_time)?$value->end_date_time:'N/A'}}</td>
                <td>{{!empty($value->break_time)?$value->break_time:'N/A'}}</td>
                <td>{{!empty($value->ot_hours)?$value->ot_hours:'N/A'}}</td>
                <td>{{!empty($value->total_work_time)?$value->total_work_time:'N/A'}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
