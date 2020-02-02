@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Monthly Attendance Sheet</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('monthly-attendance-sheet')}}" method="post" id="employeeListForm" class="mb-4">
                            @csrf
                            @php($report_month = isset($posted['month']) ? $posted['month']:date('Y-m'))
                            <div class="row">
                                {!! __getCustomSearch('monthly-attendance-sheet', @$posted) !!}
                                <div class="form-group col-md-3">
                                    <label class="form-label">Month<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control month"
                                               value="{{$report_month}}"
                                               id="month"
                                               data-date-format="yyyy-mm"
                                               name="month" required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3 no-display">
                                    <label class="form-label"> Report Type<span class="required">*</span></label>
                                        <select name="report_type" id="report_type" class="form-control multi">
                                            <option value="without_ot_hours"  {{isset($posted['report_type']) && $posted['report_type'] == 'without_ot_hours' ? 'selected':''}}>Without Out Time</option>
                                            <option value="all_components"  {{isset($posted['report_type']) && $posted['report_type'] == 'all_components' ? 'selected':''}}>With Time</option>
                                        </select>
                                </div>
                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Filter</button>

                                    <button type="button" id="makeXlsx" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Excel</button>
                                   {{-- <button type="button" id="makepdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> pdf</button>--}}
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12" style="overflow: scroll">
                                @if(isset($attendance_sheet))
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>{{__lang('SL')}}</th>
                                        <th>{{__lang('Id No')}}</th>
                                        <th>{{__lang('Name')}}</th>
                                        <th>{{__lang('Designation')}}</th>
                                        <th>{{__lang('Date of Join')}}</th>
                                        <th>{{__lang('Days of Month')}}</th>
                                        <th>{{__lang('Present')}}</th>
                                        <th>{{__lang('Leave')}}</th>
                                        <th>{{__lang('Holidays')}}</th>
                                        <th>{{__lang('Absent')}}</th>
                                        <th>{{__lang('Payable Days')}}</th>
                                       {{-- @if($posted['report_type'] !='without_ot_hours')
                                        <th>{{__lang('Over Time')}}</th>
                                        @endif--}}
                                        @for($d=1;$d<=date('t',strtotime($report_month));$d++)
                                            <th>{{$d}}</th>
                                        @endfor
                                        <th>Signature</th>
                                    </tr>
                                    </thead>
                                    @php($page = (!empty($_GET['page'])?$_GET['page']-1:0)*10)

                                    <?php foreach($attendance_sheet as $row=>$employee){ ?>
                                        <tr>

                                            <?php

                                            $appendItem ='';
                                            $att_arr = array();
                                                for($d=1;$d<=date('t',strtotime($report_month));$d++){

                                                    $att_arr[] = isset($employee["daily"][$d])?$employee["daily"][$d]["daily_status"]:'NA';

                                                    $appendItem .= '<td class="no-padding"><table id="child_table" class="text-nowrap child-table" width="100%"><tr> <td>';

                                                    $concat_month = $report_month."-".sprintf('%02d',$d);
                                                    if($concat_month <= date("Y-m-d")){
                                                        $appendItem .= isset($employee["daily"][$d]["daily_status"])?$employee["daily"][$d]["daily_status"]:"";
                                                    }
                                                    else{
                                                        $appendItem .= "";
                                                    }

                                                    if($posted['report_type'] =='all_components'){
                                                        $appendItem .= '<tr><td>';
                                                        $appendItem .= isset($employee['daily'][$d]['in_time'])&&!empty($employee['daily'][$d]['in_time'])?toTimed($employee['daily'][$d]['in_time']):'';
                                                        $appendItem .= '</td></tr><tr><td>';
                                                        $appendItem .= isset($employee['daily'][$d]['out_time'])&&!empty($employee['daily'][$d]['out_time'])?toTimed($employee['daily'][$d]['out_time']):'';
                                                        $appendItem .= '</td></tr>';
                                                        $appendItem .='</td></tr></table></td>';
                                                    }
//
                                                    if($posted['report_type'] =='without_ot_hours'){
                                                        $appendItem .='</td></tr></table></td>';
                                                    }
                                                }


                                                $attArray = array_count_values(array_filter($att_arr));
                                                $number_of_day = date('t',strtotime($report_month));
                                                $absent = array_key_exists('A',$attArray)?$attArray['A']:0;
                                                $present = array_key_exists('P',$attArray)?$attArray['P']:0;
                                                $leave = array_key_exists('LV',$attArray)?$attArray['LV']:0;
                                                $holiday = array_key_exists('H',$attArray)?$attArray['H']:0;
                                            ?>


                                            <td>{{ $page+ $row+1}}</td>
                                            <td class="text-nowrap">{{$employee['user_code']??'N/A'}}</td>
                                            <td>{{ $employee['name']??'N/A' }}</td>
                                            <td>{{ $employee['designation']??'N/A' }}</td>
                                            <td class="text-nowrap">{{ !empty($employee['date_of_join'])?toDated($employee['date_of_join']):'N/A' }}</td>
                                            <td class="text-right">{{ $number_of_day }}</td>
                                            <td class="text-right">{{ $present }}</td>
                                            <td class="text-right">{{ $leave }}</td>
                                            <td class="text-right">{{ $holiday }}</td>
                                            <td class="text-right">{{ $absent }}</td>
                                            <td class="text-right">{{ intval($number_of_day-$absent) }}</td>
                                            {{--@if($posted['report_type'] !='without_ot_hours')
                                            <td class="text-right">{{ $employee['ot_hours']??0 }}</td>
                                            @endif--}}
                                            {!! $appendItem  !!}
                                            <td></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                                    {{ $paginate_data->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<style>
    .child-table{
        border: none;
        width: 100%;
        border-collapse: collapse;
    }
    .child-table td{
        border: none;
        border-bottom: 1px solid #ddd;
        padding: 0;
    }
    .child-table tr:last-child td{
        border-bottom: none;
    }
</style>
    <script>
        $("#month").datepicker( {
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            //startDate: '+0d',
            autoclose: true,
        });
        $('#makepdf').click(function () {
            var form = $('#employeeListForm');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

        $('#makeXlsx').click(function () {
            Ladda.bind(this);
            var load = $(this).ladda();
            var url = "{{URL::to('monthly-attendance-sheet/xlsx')}}";
            var _token = "{{ csrf_token() }}";
            var data = $("#employeeListForm").serialize();
            makeAjaxPost(data, url, load).done(function (response) {
                window.location.href = './public/export/' + response.file;
                swalSuccess('Export Successfully');
            });
        });


     </script>
@endsection