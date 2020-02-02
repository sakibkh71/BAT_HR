@foreach($results as $value)
    @php($day_is = date('Y-m-d',strtotime($value->in_time)))
<tr>
    <td>{{$value->user_code}}</td>
    <td>{{$value->name}}</td>
    <td>{{apsisDate($day_is)}}</td>
    <td>{{!empty($value->in_time)?(date('Y-m-d',strtotime($value->in_time)) == $day_is ? date('h:i:s A',strtotime($value->in_time)): toDateTimed($value->in_time)):'N/A'}}</td>
    <td>{{!empty($value->out_time)?(date('Y-m-d',strtotime($value->out_time)) == $day_is? date('h:i:s A',strtotime($value->out_time)):  toDateTimed($value->out_time)):'N/A'}}</td>
</tr>
@endforeach
