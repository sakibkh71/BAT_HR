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

<?php echo employeeInfo($userInfo->id, 1); ?>

@php($shift_disable = getOptionValue('is_shift_disable'))

@if(!empty($attendance_rows) && count($attendance_rows))
    @foreach($attendance_rows as $year => $year_rows)
        @if(!empty($year_rows))
            @foreach($year_rows as $month=>$month_rows)
                    @php($monthName = date('F', mktime(0, 0, 0, $month, 10)))
                    @if(!empty($month_rows))
                        <?php
                        $status_ar = array_map(function ($ar) {
                            return !empty($ar['daily_status'])?$ar['daily_status']:'';
                        }, $month_rows);
                        $status_result = array_count_values($status_ar);
                        ?>
                         <table>
                            <tr>
                                <td width="65%"> <h4 class="mt-3 mb-2"> {{__lang('Attendance Month')}} : {{$monthName}} - {{$year}}</h4></td>
                                <td>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <td colspan="4" class="text-center"> {{__lang('Total Summary')}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">{{__lang('weekend')}}</td>
                                            <td class="text-center">{{__lang('Present')}}</td>
                                            <td class="text-center">{{__lang('Absent')}} </td>
                                            <td class="text-center">{{__lang('Weekend Present')}}</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="text-center">{{$status_result['W']??'N/A'}}</td>
                                            <td class="text-center">{{$status_result['P']??'N/A'}}</td>
                                            <td class="text-center">{{$status_result['A']??'N/A'}}</td>
                                            <td class="text-center">{{$status_result['H']??'N/A'}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    @endif

                    <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td rowspan="1" width="5%" style="vertical-align: middle;">{{__lang('SL')}}</td>
                                    <td rowspan="1" width="10%" style="vertical-align: middle;">{{__lang('Date')}}</td>
                                    <td rowspan="1" width="10%" style="vertical-align: middle;">{{__lang('Daily Status')}}</td>
                                    {{--@if(!$shift_disable)--}}
                                        {{--<td rowspan="1" width="10%" style="vertical-align: middle;">{{__lang('Shift Name')}}</td>--}}
                                        {{--<td colspan="2" width="25%" style="vertical-align: middle;">{{__lang('Shift Time')}}</td>--}}
                                    {{--@endif--}}
                                    {{--<td colspan="2" width="30%" style="vertical-align: middle;">{{__lang('Attendance Time')}}</td>--}}
                                    {{--<td rowspan="2" width="5%" style="vertical-align: middle;">{{__lang('Over Time')}}</td>--}}
                                    <td rowspan="1" width="10%" style="vertical-align: middle;">{{__lang('Remarks')}}</td>
                                </tr>
                                {{--<tr>--}}
                                    {{--@if(!$shift_disable)--}}
                                    {{--<td>{{__lang('In Time')}}</td>--}}
                                    {{--<td>{{__lang('Out Time')}}</td>--}}
                                    {{--@endif--}}
                                    {{--<td>{{__lang('In Time')}}</td>--}}
                                    {{--<td>{{__lang('Out Time')}}</td>--}}
                                {{--</tr>--}}
                            </thead>
                            <tbody>

                                @if(!empty($month_rows))
                                    @foreach($month_rows as $key=>$value)
                                        <tr>
                                            <td align="center">
                                                {{++$key}}
                                            </td>
                                            <td>{{!empty($value['day_is'])?toDated($value['day_is']):'N/A'}}</td>
                                            <td>{{!empty($value['daily_status'])?$value['day_is']>date('Y-m-d')?'':$value['daily_status']:'N/A'}}</td>

                                            {{--@if(!$shift_disable)--}}
                                            {{--<td>{{!empty($value['shift_name'])?$value['shift_name']:'N/A'}}</td>--}}
                                            {{--<td>{{!empty($value['shift_start_time'])?date('h:i:s A',strtotime($value['shift_start_time'])):'N/A'}}</td>--}}
                                            {{--<td>{{!empty($value['shift_end_time'])?date('h:i:s A',strtotime($value['shift_end_time'])):'N/A'}}</td>--}}
                                            {{--@endif--}}
                                            {{--<td>{{!empty($value['in_time'])?(date('Y-m-d',strtotime($value['in_time'])) == $value['day_is']? date('h:i:s A',strtotime($value['in_time'])): toDateTimed($value['in_time'])):'N/A'}}</td>--}}
                                            {{--<td>{{!empty($value['out_time'])?(date('Y-m-d',strtotime($value['out_time'])) == $value['day_is']? date('h:i:s A',strtotime($value['out_time'])):  toDateTimed($value['out_time'])):'N/A'}}</td>--}}
                                            {{--<td>{{!empty($value['ot_hours'])?$value['ot_hours']:'N/A'}}</td>--}}
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                    </table>
            @endforeach
        @endif
    @endforeach
@endif
<br><br><br>
@include('HR.hr_default_signing_block')