@if(!empty($attendance_rows) && count($attendance_rows))
    @foreach($attendance_rows as $year => $year_rows)
        @if(!empty($year_rows))
            @foreach($year_rows as $month=>$month_rows)
                <div class="border">
                    <div class="ibox">
                    <div class="ibox-title">
                        @php($monthName = date('F', mktime(0, 0, 0, $month, 10)))
                        <h3> Attendance Month : {{$monthName}} - {{$year}}</h3>
                        <div class="ibox-tools">
                            <a class="collapse-ajax">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if(!empty($month_rows))
                            <?php

                                $status_ar = array_map(function ($ar) {
                                    return !empty($ar['daily_status'])?$ar['daily_status']:'';
                                }, $month_rows);
                                $status_result = array_count_values($status_ar);


                            ?>
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="5" class="text-center">{{__lang('Total Summary')}}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">{{__lang('Present (P)')}}</th>
                                            <th class="text-center">{{__lang('Absent (A)')}}</th>
                                            <th class="text-center">{{__lang('Weekend (W)')}}</th>
                                            <th class="text-center">{{__lang('Holiday (H)')}}</th>
                                            <th class="text-center">{{__lang('Leave (Lv)')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">{{$status_result['P']??0}}</td>
                                            <td class="text-center">{{$status_result['A']??0}}</td>
                                            <td class="text-center">{{$status_result['W']??0}}</td>
                                            <td class="text-center">{{$status_result['H']??0}}</td>
                                            <td class="text-center">{{$status_result['Lv']??0}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th rowspan="2" width="2%" class="text-center align-middle">{{__lang('SL')}}</th>
                                    <th rowspan="2" width="10%" class="text-center align-middle">{{__lang('Date')}}</th>
                                    <th rowspan="2" width="10%" class="text-center align-middle">{{__lang('Daily Status')}}</th>
                                    @if(!getOptionValue('is_shift_disable'))
                                    <th rowspan="2" width="10%" class="text-center align-middle">{{__lang('Shift Name')}}</th>
                                    <th colspan="2" width="25%" class="text-center align-middle">{{__lang('Shift Time')}}</th>
                                    @endif
                                    {{--<th colspan="2" width="30%" class="text-center align-middle">{{__lang('Attendance Time')}}</th>--}}
                                    {{--<th rowspan="2" width="5%" class="text-center align-middle">{{__lang('Over Time')}}</th>--}}
                                </tr>
                                <tr>
                                    @if(!getOptionValue('is_shift_disable'))
                                    <th class="text-center align-middle">{{__lang('In Time')}}</th>
                                    <th class="text-center align-middle">{{__lang('Out Time')}}</th>
                                    @endif
                                    {{--<th class="text-center align-middle">{{__lang('In Time')}}</th>--}}
                                    {{--<th class="text-center align-middle">{{__lang('Out Time')}}</th>--}}
                                </tr>
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
                                            @if(!getOptionValue('is_shift_disable'))
                                                <td>{{!empty($value['shift_name'])?$value['shift_name']:'N/A'}}</td>
                                                <td>{{!empty($value['shift_start_time'])?date('h:i:s A',strtotime($value['shift_start_time'])):'N/A'}}</td>
                                                <td>{{!empty($value['shift_end_time'])?date('h:i:s A',strtotime($value['shift_end_time'])):'N/A'}}</td>
                                            @endif
                                            {{--<td>{{!empty($value['in_time'])?(date('Y-m-d',strtotime($value['in_time'])) == $value['day_is']? date('h:i:s A',strtotime($value['in_time'])): toDateTimed($value['in_time'])):'N/A'}}</td>--}}
                                            {{--<td>{{!empty($value['out_time'])?(date('Y-m-d',strtotime($value['out_time'])) == $value['day_is']? date('h:i:s A',strtotime($value['out_time'])):  toDateTimed($value['out_time'])):'N/A'}}</td>--}}
                                            {{--<td>{{!empty($value['ot_hours'])?$value['ot_hours']:'N/A'}}</td>--}}
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
            @endforeach
        @endif
    @endforeach
@endif

