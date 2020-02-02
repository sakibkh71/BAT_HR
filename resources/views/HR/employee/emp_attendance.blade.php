<div class="table-responsive">
    <table class="checkbox-clickable table table-striped table-bordered table-hover emp-attendance-list">
        <thead>
        <tr>
            <th rowspan="2">Date</th>
            {{--<th rowspan="2">Shift Name</th>--}}
            <th rowspan="2">Daily Status</th>{{--
            <th colspan="2">Shift Time</th>--}}
            {{--<th colspan="2">Attendance Time</th>--}}
           {{-- <th rowspan="2">Overtime</th>--}}
        </tr>
        {{--<tr>--}}
            {{--<th>In Time</th>--}}
            {{--<th>Out Time</th>--}}{{----}}
            {{--<th>In Time</th>--}}
            {{--<th>Out Time</th>--}}
        {{--</tr>--}}
        </thead>
        <tbody>
        @foreach($attendance_history as $row)
        <tr class="item" data-approved_status="{{$row->approved_status??''}}"  day_is="{{$row->day_is??''}}">
            <td>{{!empty($row->day_is)?todated($row->day_is):''}}</td>
           {{-- <td>{{!empty($row->shift_name)?$row->shift_name:'N/A'}}</td>--}}
            <td>{{$row->day_is<=date('Y-m-d')?$row->daily_status:''}}</td>
          {{--  <td>{{!empty($row->shift_start)?date('h:i:s A',strtotime($row->shift_start)):'N/A'}}</td>
            <td>{{!empty($row->shift_end)?date('h:i:s A',strtotime($row->shift_end)):'N/A'}}</td>--}}
            {{--<td>{{!empty($row->in_time)?(date('Y-m-d',strtotime($row->in_time)) == $row->day_is? date('h:i:s A',strtotime($row->in_time)): toDateTimed($row->in_time)):''}}</td>--}}
            {{--<td>{{!empty($row->out_time)?(date('Y-m-d',strtotime($row->out_time)) == $row->day_is? date('h:i:s A',strtotime($row->out_time)): toDateTimed($row->out_time)):''}}</td>--}}
            {{--<td>{{!empty($row->ot_hours)?$row->ot_hours:'N/A'}}</td>--}}
        </tr>
        @endforeach
        </tbody>
    </table>
</div>