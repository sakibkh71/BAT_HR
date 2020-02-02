@extends('layouts.app')
@section('content')
    <style>
        .row-select-toggle{
            cursor: default;
        }
        .dropdown-item {
            margin: 0;
            padding: 5px;
        }
    </style>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                <h2>Daily Kpi Achievement</h2>
                    <div class="ibox-tools">

                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{route('daily-kpi-achievement')}}" method="post" id="daily_achievement_form" name="daily_achievement_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Date Range</label>
                                    <div class="input-group">
                                        <input type="text"
                                               placeholder=""
                                               class="form-control daterange"
                                               id="achievement_date"
                                               name="achievement_date"
                                               value="{{@$selected_achievement_date}}"
                                               required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                @php echo $multiple_search_criteria; @endphp
{{--                                <label class="form-label">{{__lang('Distributor Point')}}<span class="required">*</span></label>--}}
{{--                                <div class="form-group">--}}
{{--                                    {!! __combo('bat_distributor_point_multi',array('selected_value'=>@$selected_point,'attributes'=>array('multiple'=>true,'class'=>'from-control multi','id'=>'bat_point','name'=>'bat_point[]','required'=>'required'))) !!}--}}
{{--                                    <div class="help-block with-errors has-feedback"></div>--}}
{{--                                </div>--}}
                            </div>
                            <div class="col-md-2 mt-4">
                                <button type = "submit" class = "btn btn-default" id="search_daily_achievement" style="margin-left: 5px;">Search</button>
                            </div>
                        </div>
                    </form>
                    @if(isset($inside_search) && $inside_search==1)
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered text-lefts data-table">
                                <thead>
                                <tr>
                                    <th rowspan="3">Point</th>
                                    <th rowspan="3">Route</th>
                                    <th rowspan="3">SR Name</th>
                                    <th rowspan="3">SS Name</th>
                                    @foreach($header_array as $kpi_id=>$kpi_values)
                                      @php($kpi_name_colspan=0)
                                        @foreach($kpi_values as $target_type=>$target_cat_array)
                                            <?php $kpi_name_colspan+=count($target_cat_array); ?>

                                       @endforeach
                                        <th colspan="{{$kpi_name_colspan}}" class="text-center">{{$kpi_name_array[$kpi_id]}}</th>
                                    @endforeach
                                </tr>

                                <tr>
                                    @foreach($header_array as $kpi_id=>$kpi_values)

                                        @foreach($kpi_values as $target_type=>$target_cat_array)
                                            <th colspan="{{count($target_cat_array)}}" class="text-center">{{$target_type}}</th>

                                        @endforeach

                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($header_array as $kpi_id=>$kpi_values)

                                        @foreach($kpi_values as $target_type=>$target_cat_array)
                                          @foreach($target_cat_array as $target_id=>$target_val)

                                              <th>{{$target_val}}</th>
                                          @endforeach

                                        @endforeach

                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($general_info_array as $dpid=>$info)
                                    @foreach($info as $route_number=>$info_array)
                                        <tr>
                                            <td>{{$info_array['point_name']}}</td>
                                            <td>{{$info_array['route_number']}}</td>
                                            <td>{{$info_array['sr_user_name']}} {{$info_array['sr_user_code']?'('.$info_array['sr_user_code'].')':''}}</td>
                                            <td>{{$info_array['ss_user_name']}} {{$info_array['ss_user_code']?'('.$info_array['ss_user_code'].')':''}}</td>

                                            @foreach($header_array as $kpi_id=>$kpi_values)

                                                @foreach($kpi_values as $target_type=>$target_cat_array)
                                                    @foreach($target_cat_array as $target_id=>$target_val)
                                                        <?php $achievement=@$route_wise_achievement_array[$dpid][$route_number][$kpi_id][$target_type][$target_id]; ?>
                                                        <td class="text-right">{{number_format($achievement,2)}}</td>

                                                    @endforeach

                                                @endforeach

                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


<script>
    var start_date='';
    var end_date='';
    // $('#achievement_date').on('apply.daterangepicker', function (ev, picker) {
    //     start_date = picker.startDate.format('YYYY-MM-DD');
    //     end_date = picker.endDate.format('YYYY-MM-DD');
    //
    //    // console.log(start_date,end_date);
    //     //var diff = Math.floor((Date.parse(end) - Date.parse(start)) / 86400000) + 1;
    //
    // });
    // $(document).on('click','#search_daily_achievement',function () {
    //   //  console.log(start_date,end_date);
    //     console.log($('#achievement_date').val());
    // });
    @if(empty(@$selected_point))
       $("#bat_point option").attr("selected", "selected");

    @endif
   </script>
   @endsection
