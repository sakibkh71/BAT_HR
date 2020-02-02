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
@if(!empty($attendance_rows) && count($attendance_rows))
    @foreach($attendance_rows as $shift => $shift_rows)
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $shift}}</h5>
                </div>
                <div class="ibox-content">
                    <table id="record_table" class="table table-striped text-lefts table-bordered">
                        <thead>
                        <tr>
                            <td rowspan="2" style="vertical-align: middle">SL#</td>
                            <td rowspan="2" style="vertical-align: middle">Employee Name</td>
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
                            @if(!empty($shift_rows))
                                @foreach($shift_rows as $key => $rows)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$rows['name']}} @if(!empty($rows['name'])) ({{$rows['name']}}) @endif</td>
                                        <td>{{$rows['shift_name'] ?? 'N/A'}}</td>
                                        <td>{{$rows['daily_status'] ?? 'N/A'}}</td>
                                        <td>{{$rows['shift_start_time'] ?? 'N/A'}}</td>
                                        <td>{{$rows['shift_end_time'] ?? 'N/A'}}</td>
                                        <td>{{$rows['in_time'] ?? 'N/A'}}</td>
                                        <td>{{$rows['out_time'] ?? 'N/A'}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

    @endforeach
@endif