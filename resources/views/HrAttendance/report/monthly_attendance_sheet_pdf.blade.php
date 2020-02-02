    @if(isset($attendance_sheet))
    <table class="table table-bordered">
        <thead>
        <tr>
            <td>{{__lang('SL')}}</td>
            <td>{{__lang('Id no')}}</td>
            <td>{{__lang('Name')}}</td>
            <td>{{__lang('Designation')}}</td>
            <td>{{__lang('DoJ')}}</td>
            <td>{{__lang('Days of Month')}}</td>
            <td>{{__lang('Present')}}</td>
            <td>{{__lang('Leave')}}</td>
            <td>{{__lang('Holidays')}}</td>
            <td>{{__lang('Absent')}}</td>
            <td>{{__lang('Payable Days')}}</td>
            <td>{{__lang('Over Time')}}</td>
            @for($d=1;$d<=date('t',strtotime($report_month));$d++)
                <td>{{$d}}</td>
            @endfor
            <td>{{__lang('Signature')}}</td>
        </tr>
        </thead>

        @foreach($attendance_sheet as $row=>$employee)
            <tr>
                <td>{{($row+1)}}</td>
                <td class="text-nowrap">{{$employee['user_code']??'N/A'}}</td>
                <td>{{$employee['name']??'N/A'}}</td>
                <td>{{$employee['designation']??'N/A'}}</td>
                <td class="text-nowrap">{{ !empty($employee['date_of_join'])?toDated($employee['date_of_join']):'N/A'}}</td>
                <td>{{$employee['number_of_days']??0}}</td>
                <td>{{$employee['present_days']??0}}</td>
                <td>{{$employee['number_of_leave']??0}}</td>
                <td>{{$employee['number_of_holidays']??0}}</td>
                <td>{{$employee['absent_days']??0}}</td>
                <td>{{$employee['payable_days']??0}}</td>
                <td>{{$employee['ot_hours']??0}}</td>
                @for($d=1;$d<=date('t',strtotime($report_month));$d++)
                    <td style="padding: 0px !important; width: 100px;">
                        <table id="child_table" class="text-nowrap" width="100%">
                            <tr><td>{{isset($employee['daily'][$d])?$employee['daily'][$d]['daily_status']:'N/A'}}</td></tr>
                            <tr><td>{{isset($employee['daily'][$d]['in_time'])&&!empty($employee['daily'][$d]['in_time'])?toTimed($employee['daily'][$d]['in_time']):'N/A'}}</td></tr>
                            <tr><td>{{isset($employee['daily'][$d]['out_time'])&&!empty($employee['daily'][$d]['out_time'])?toTimed($employee['daily'][$d]['out_time']):'N/A'}}</td></tr>
                        </table>
                    </td>
                @endfor
                <td></td>
            </tr>
        @endforeach
    </table>
    @endif