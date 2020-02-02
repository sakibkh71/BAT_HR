@extends('layouts.app')
@section('content')
    @include('dropdown_grid.dropdown_grid')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2 style="display: inline-block">Bonus Sheet Create</h2>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('hr-bonus-sheet-create-save')}}" id="bonus_sheet_form" method="post">
                            @csrf
                            <input type="hidden" name="bonus_sheet_code" id="bonus_sheet_code" value="{{@$sheet_info->bonus_sheet_code}}"/>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Sheet Name<span class="required">*</span></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <input type="text" name="bonus_sheet_name" id="bonus_sheet_name" class="form-control"
                                            data-error="Please Enter Sheet Name" value="{{@$sheet_info->bonus_sheet_name}}" placeholder="Bonus Sheet" required=""
                                            autocomplete="off">
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sheet Type<span class="required">*</span></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <select name="bonus_type" id="bonus_type" class="form-control">
                                                <option {{@$sheet_info->bonus_type == 'Festival Bonus'?'selected':''}} value="Festival Bonus">Festival Bonus</option>
                                                <option {{@$sheet_info->bonus_type == 'Performance Bonus'?'selected':''}} value="Performance Bonus">Performance Bonus</option>
                                                <option {{@$sheet_info->bonus_type == 'Other Bonus'?'selected':''}} value="Other Bonus">Other Bonus</option>
                                            </select>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{__lang('Bonus Preparation Date')}}<span class="required">*</span></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                             <span class="input-group-addon"><i
                                                         class="fa fa-calendar"></i></span>
                                            <input type="text" name="preparation_date" id="preparation_date" class="form-control"
                                                   data-error="Please Enter Bonus Preparation Date" value="{{@$sheet_info->bonus_preparation_date?$sheet_info->bonus_preparation_date:date('Y-m-d')}}" placeholder="Bonus Preparation Date" required="">
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{__lang('Distributor Point')}}<span class="required">*</span></label>
                                    <div class="form-group">
                                        {!! __combo('bat_distributor_point_multi',array('selected_value'=>explode(',',@$sheet_info->bat_dpid),'attributes'=>array('multiple'=>true,'class'=>'from-control multi','id'=>'bat_point','required'=>'required'))) !!}
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{__lang('FF Type')}}</label>
                                    <div class="form-group">
                                        {!! __combo('hr_emp_salary_designations',array('selected_value'=>explode(',',@$sheet_info->selected_designations),'attributes'=>array('multiple'=>true,'name'=>'selected_designations[]','class'=>'from-control multi','id'=>'selected_designations'))) !!}
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bonus Calculation Date<span class="required">*</span></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input required type="text" name="calculation_date" id="calculation_date"
                                                data-error="Please Enter Bonus Calculation Date"
                                                value="{{isset($sheet_info->bonus_calculation_date)?$sheet_info->bonus_calculation_date:date('Y-m-d')}}"
                                                class="form-control"/>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div> 

                                <div class="col-md-3 @if(isset($sheet_info->bonus_type) &&  $sheet_info->bonus_type != 'Festival Bonus') display @else no-display @endif others_type">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label">Bonus Eligible Based On </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <select class="form-control" name="bonus_eligible_based_on" id="bonus_eligible_based_on" data-error="Please Select a Value" @if(isset($sheet_info->bonus_eligible_based_on)) disabled @endif>
                                                <option value="">Select an Option</option>
                                                <option value="date_of_confirmation" @if(isset($sheet_info) && $sheet_info->bonus_eligible_based_on=='date_of_confirmation') selected @endif>date of confirmation</option>
                                                <option value="date_of_join" @if(isset($sheet_info) && $sheet_info->bonus_eligible_based_on=='date_of_join') selected  @endif>date of join</option>
                                            </select>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 @if(isset($sheet_info->bonus_type) &&  $sheet_info->bonus_type != 'Festival Bonus') display @else no-display @endif others_type"">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label">Number Of Month </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <input type="number"  name="number_of_month" id="number_of_month" placeholder="0" class="form-control text-left input_money"  value="{{isset($sheet_info->number_of_month)?$sheet_info->number_of_month:''}}">
                                            <span class="input-group-addon"> Month </span>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                    </div>
                                </div>


                                <div class="col-md-3 @if(isset($sheet_info->bonus_type) &&  $sheet_info->bonus_type != 'Festival Bonus') display @else no-display @endif others_type">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label">Bonus Based On<span class="required">*</span></label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <select class="form-control" name="bonus_based_on" id="bonus_based_on">
                                                <option value="">Select an Option</option>
                                                <option value="basic" {{ isset($sheet_info) && $sheet_info->bonus_based_on == 'basic'?'selected':'' }}>Basic</option>
                                                <option value="gross" {{ isset($sheet_info->bonus_based_on) && $sheet_info->bonus_based_on == 'gross'?'selected':'' }}>Gross</option>
                                            </select>
                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 @if(isset($sheet_info->bonus_type) &&  $sheet_info->bonus_type != 'Festival Bonus') display @else no-display @endif others_type"">
                                    <div class="form-group row">
                                        <label class="col-sm-12 form-label">Bonus Ratio <span class="required">*</span> </label>
                                        <div class="col-sm-12 ">
                                            <div class="input-group">
                                            <input type="number"  name="bonus_ratio" id="bonus_ratio" placeholder="0" class="form-control text-left input_money" value="{{isset($sheet_info->bonus_ratio)?$sheet_info->bonus_ratio:''}}">
                                            <span class="input-group-addon"> % </span>

                                        </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row"> 
                                <div class="col-md-3">
                                    <label class="form-label"></label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>                         
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script> 
        $('#bonus_sheet_form').validator();

        $("#preparation_date").datepicker( {
            format: "yyyy-mm-dd",
            autoclose: true,
        });

        $('#calculation_date').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        @if(empty(@$sheet_info->selected_designations)||@$sheet_info->selected_designations=='All')
            $("#selected_designations option").attr("selected", "selected");
        @endif

        $('.multi').multiselect('rebuild');
        $('.multi').parent().find('.btn-group').css('width','100%');

        $(document).on('change', '#bonus_type', function(e) {
            var bonsutype = $(this).val();
            if (bonsutype =='Festival Bonus') {
                $('.others_type').hide();               
                $('#bonus_eligible_based_on').removeAttr('required');
                $('#number_of_month').removeAttr('required');
                $('#bonus_based_on').removeAttr('required');
                $('#bonus_ratio').removeAttr('required');  
            }else{
                $('.others_type').show(); 
                 $('#bonus_eligible_based_on').attr("required","");
                $('#number_of_month').attr("required","");
                $('#bonus_based_on').attr("required","");
                $('#bonus_ratio').attr("required","");
            }
            $("#bonus_sheet_form").validator('update'); 
        })
    </script>
@endsection
 
 
