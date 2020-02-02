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
        font-size: 0.8em;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:0.8em;
        font-weight: 300;
    }
</style>

<div class="ibox">
            <div class="ibox-title">
                {!! isset($emp_info)?$emp_info:'' !!}
            </div>
            <div class="ibox-content">
                <table id="record_table" class="table table-striped text-lefts table-bordered">
                    <thead>
                    <tr>
                        <td rowspan="2" style="vertical-align: middle">Date</td>
                        <td rowspan="2" style="vertical-align: middle">Shift Name</td>
                        <td rowspan="2" style="vertical-align: middle">Daily Status</td>
                        <td colspan="2">Shift Info</td>
                        <td colspan="2">Attendance Info</td>
                    </tr>
                    <tr>
                        <td>Start Time</td>
                        <td>End Time</td>
                        <td>Start Time</td>
                        <td>End Time</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attendance_rows as $row)
                        <tr>
                            <td>{{ toDated($row->day_is)}}</td>
                            <td>{{$row->shift_name}}</td>
                            <td>{{ !empty($row->daily_status)?$row->daily_status:'N/A'}}</td>
                            <td>{{!empty($row->shift_start_time)?date('h:i:s A',strtotime($row->shift_start_time)):'N/A'}}</td>
                            <td>{{!empty($row->shift_end_time)?date('h:i:s A',strtotime($row->shift_end_time)):'N/A'}}</td>
                            <td>{{!empty($row->in_time)?(date('Y-m-d',strtotime($row->in_time)) == $row->day_is ? date('h:i:s A',strtotime($row->in_time)): toDateTimed($row->in_time)):'N/A'}}</td>
                            <td>{{!empty($row->out_time)?(date('Y-m-d',strtotime($row->out_time)) == $row->day_is ? date('h:i:s A',strtotime($row->out_time)):  toDateTimed($row->out_time)):'N/A'}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
