<style>
    h4{
        font-size:1em;
    }
    h5{
        font-size:0.8em;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    thead tr td{
        background:#e7e7e7;
        font-size: 0.7em;
        font-weight: bold;
        padding:2px 5px;
        text-align: center;
    }
    td{
        font-size:0.7em;
        font-weight: 300;
    }
    table.padding td{
        padding: 5px;
    }
    table.border td{
        border: 1px solid #e7e7e7;
        padding: 5px;
    }
    hr{
        width: 100%;
        border-top: 1px solid #e7e7e7;
    }
    p{font-size:0.8em;}
    strong{
        font-weight: 700;
    }
</style>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('SL#')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('User Code')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('User Name')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('Designation')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('DoJ')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('Shift Name')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('Daily Status')}}</td>
        <td colspan="2" width="15%" style="vertical-align: middle;">{{__lang('Shift Time')}}</td>
        <td colspan="2" width="15%" style="vertical-align: middle;">{{__lang('Attendance Time')}}</td>
        <td rowspan="2" style="vertical-align: middle;">{{__lang('Over Time')}}</td>
    </tr>
    <tr>
        <td>{{__lang('In Time')}}</td>
        <td>{{__lang('Out Time')}}</td>
        <td>{{__lang('In Time')}}</td>
        <td>{{__lang('Out Time')}}</td>
    </tr>
    </thead>
    <tbody>
    @if(!empty($attendance_rows))
        @foreach($attendance_rows as $key=>$value)
            <tr>
                <td align="center">
                    {{++$key}}
                </td>
                <td>{{!empty($value->user_code)?$value->user_code:'N/A'}}</td>
                <td>{{!empty($value->username)?$value->username:'N/A'}}</td>
                <td>{{!empty($value->designations_name)?$value->designations_name:'N/A'}}</td>
                <td>{{!empty($value->date_of_join)?toDated($value->date_of_join):'N/A'}}</td>
                <td>{{!empty($value->shift_name)?$value->shift_name:'N/A'}}</td>
                <td>{{!empty($value->status)?$value->status:'N/A'}}</td>
                <td>{{!empty($value->shift_start)?date('h:i:s A',strtotime($value->shift_start)):'N/A'}}</td>
                <td>{{!empty($value->shift_end)?date('h:i:s A',strtotime($value->shift_end)):'N/A'}}</td>
                <td>{{!empty($value->start_date_time)?(date('Y-m-d',strtotime($value->start_date_time)) == $value->day_is? date('h:i:s A',strtotime($value->start_date_time)): toDateTimed($value->start_date_time)):'N/A'}}</td>
                <td>{{!empty($value->end_date_time)?(date('Y-m-d',strtotime($value->end_date_time)) == $value->day_is? date('h:i:s A',strtotime($value->end_date_time)): toDateTimed($value->end_date_time)):'N/A'}}</td>
                <td>{{!empty($value->ot_hours)?$value->ot_hours:'N/A'}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>



