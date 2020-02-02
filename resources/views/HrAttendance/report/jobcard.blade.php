@extends('layouts.app')
@section('content')
   <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{__lang('Individual Attendance')}}</h2>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content pad10">
                        <div class="row">
                            <div class="col-sm-5 col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label"><strong>{{__lang('Date Range')}}: </strong><span style="color:red; font-weight:bold">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="date_range" value="" class="form-control pull-left daterange" placeholder="YYYY-MM-DD" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-3">
                                <label class="col-form-label"><strong>{{__lang('Employee Code')}} : </strong><span style="color:red; font-weight:bold">*</span></label>
                                <div class="form-group emp_code_entry">
                                        {{__combo('hr_leave_report_employee',array('selected_value'=> '', 'attributes'=> array('class'=>'form-control multi','id'=>'user_code','name'=>'user_code[]')))}}
                                </div>
                                <input type="hidden" name="user_id" class="employee_id" value=""/>
                            </div>
                            <div class="col-sm-2 col-md-1 pl-0 mt-4 pt-2">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-info" id="user_code_search" data-style="zoom-out"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox pt-0 pad10 pb-2" id="employeeinfo" style="display: none">
                    <div class="border">
                    <div class="ibox-title">
                        <h4><i class="fa fa-user"></i> {{__lang('Employee Information')}}</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="col-sm-12" >
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Employee Name')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-name"> :  </span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Employee ID')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-id"> :  </span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Date of Join')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-doj"> : </span></div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Designation')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-desig"> : </span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Distributors Point')}}</strong></label>
                                        <div class="col-sm-8"><span id="bat_dpid"> : </span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Distributors House')}}</strong></label>
                                        <div class="col-sm-8"><span id="company_name"> : </span></div>
                                    </div>

                                    {{--<div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Department')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-dep"> : </span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Section')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-sec"> : </span></div>
                                    </div>--}}

                                    <div class="row mb-2">
                                        <label class="col-sm-4"><strong>{{__lang('Date')}}</strong></label>
                                        <div class="col-sm-8"><span id="emp-dat"> : </span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="filter_row" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <div class="row">
                                    @if(!getOptionValue('is_shift_disable'))
                                    <div class="col-md-3">
                                        <label for=""><strong>{{__lang('Working Shift')}}</strong></label>
                                        {{__combo('atten_report_working_shift',array('selected_value'=>'', 'attributes'=> array('class'=>'form-control multi','id'=>'shift','name'=>'shift',  'multiple'=>"multiple")))}}
                                    </div>
                                    @endif

                                    <div class="col-md-3">
                                        <label for=""> <strong>{{__lang('Daily Status')}}</strong></label>
                                        <select class="form-control multi" id="daily_status" name="daily_status" multiple="">
                                            <option value="P">{{__lang('Present (P)')}}</option>
                                            <option value="A">{{__lang('Absent (A)')}}</option>
                                            {{--<option value="L">{{__lang('Late (L)')}}</option>--}}
                                            <option value="W">{{__lang('Weekend (W)')}}</option>
                                            {{--<option value="WP">{{__lang('Weekend Present (WP)')}}</option>--}}
                                            <option value="H">{{__lang('Holiday (H)')}}</option>
                                            {{--<option value="HP">{{__lang('Holiday Present (HP)')}}</option>--}}
                                            <option value="Lv">{{__lang('Leave (Lv)')}}</option>
                                            {{--<option value="EO">{{__lang('Early Out (EO)')}}</option>--}}
                                        </select>
                                    </div>

                                    <div class="col-md-3" style="margin-top: 30px;">
                                        <button type="button" class="btn btn-primary" id="filter"><i class="fa fa-search"></i>{{__lang('Filter')}}</button>
                                        <form class="inline" method="post" action="{{URL::to('job-card-data/pdf')}}" id="pdfForm" target="_blank">
                                            @csrf
                                            <input type="hidden" name="code" value="" id="form_code">
                                            <input type="hidden" name="date_range" value="" id="form_date">
                                            <input type="hidden" name="shift[]" value="" id="form_shift">
                                            <input type="hidden" name="daily_status[]" value="" id="form_status">
                                            <button type="button" id="makepdf" class="btn btn-success"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="pad10  pt-0" id="jobcart_list">
                </div>
            </div>
        </div>
    </div>

    <script>


        $(function ($) {

            $(document).on('click', '#filter', function () {
                $( "#user_code_search" ).trigger( "click" );
            });

            $('#user_code').on("keyup", function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $( "#user_code_search" ).trigger( "click" );
                }
            });

            $('#user_code_search').on('click', function () {
                var user_code = $('#user_code').val();
                var date_range = $('.daterange').val();
                var shift = $('#shift').val() || '';
                var daily_status = $('#daily_status').val() || '';

                var resdate = date_range.split(" - ");


                if(user_code.length === 0){
                    swalError("Please Input a valid Employee Code");
                }else if(date_range.length === 0){
                    swalError("Please Select Date");
                }else{
                    Ladda.bind(this);
                    var load = $(this).ladda();
                    var url = "{{URL::to('job-card-data')}}";
                    var _token = "{{ csrf_token() }}";
                    var data = {_token:_token,code:user_code, date_range:date_range, shift:shift, daily_status:daily_status};
                    makeAjaxPost(data,url,null).then(function (s) {
                        if (s.status == "error" ){
                            $('#emp-name').html(':');
                            $('#emp-id').html(': ');
                            $('#emp-doj').html(': ');
                            $('#emp-dep').html(': ');
                            $('#emp-desig').html(': ');
                            $('#emp-unit').html(': ');
                            $('#bat_dpid').html(': ');
                            $('#company_name').html(': ');
                            $('#emp-sec').html(': ');
                            $('#emp-dat').html(': ');
                            $('#jobcart_list').empty();
                            $("#filter_row").hide();
                            swalError('sorry! we can\'t find user for this code')
                        }else{
                            //print user info
                            if (s.userInfo !=null){
                                $('#employeeinfo').show();
                                $('#emp-name').html(': '+s.userInfo.name);
                                $('#emp-id').html(': '+s.userInfo.user_code);
                                $('#emp-doj').html(': '+ moment(s.userInfo.date_of_join).format('DD MMM, YYYY'));
                                $('#emp-dep').html(': '+s.userInfo.departments_name);
                                $('#emp-desig').html(': '+s.userInfo.designations_name);
                                $('#bat_dpid').html(': '+s.userInfo.distributors_point);
                                $('#company_name').html(': '+s.userInfo.company_name);
                                $('#emp-unit').html(': '+s.userInfo.hr_emp_unit_name);
                                $('#emp-sec').html(': '+s.userInfo.hr_emp_section_name);
                                $('#emp-dat').html(': '+ moment(resdate[0]).format('DD MMM, YYYY')+' - '+moment(resdate[1]).format('DD MMM, YYYY'));
                            }else{
                                $('#employeeinfo').hide();
                                $('#emp-name').html(':');
                                $('#emp-id').html(': ');
                                $('#emp-doj').html(': ');
                                $('#emp-dep').html(': ');
                                $('#emp-desig').html(': ');
                                $('#bat_dpid').html(': ');
                                $('#company_name').html(': ');
                                $('#emp-unit').html(': ');
                                $('#emp-sec').html(': ');
                                $('#emp-dat').html(': ');
                            }

                            //Print Result report
                            if (s.attendance !=null && s.attendance != ""){
                                $('#jobcart_list').html(s.attendance);
                            }else{
                                $('#jobcart_list').html('<div class="col-md-12"><div class="alert alert-warning" role="alert">sorry! we can\'t find any attendance record</div></div>');
                            }
                            $("#filter_row").show();
                        }
                        load.ladda('stop');
                    });
                }
            });
        });


        $('#makepdf').click(function () {
            var code = $('#user_code').val();
            var date_range = $('.daterange').val();
            var shift = $('#shift').val() || '';
            var daily_status = $('#daily_status').val() || '';

            $('#form_code').val(code);
            $('#form_date').val(date_range);
            $('#form_shift').val(shift);
            $('#form_status').val(daily_status);

            $('#pdfForm').submit();

        });


        /*$('.date_range').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            autoApply:true,
        }).on('apply.daterangepicker', function(ev, picker) {
            var start = moment(picker.startDate.format('YYYY-MM-DD'));
            var end   = moment(picker.endDate.format('YYYY-MM-DD'));
            var diff = end.diff(start, 'days');
            if (diff > 360){
                $(this).addClass('error');
            } else{
                $(this).removeClass('error')
            }
        });*/


        // Collapse ibox function
        $(document).on('click', '.collapse-ajax', function (e) {
            e.preventDefault();
            var ibox = $(this).closest('div.ibox');
            var button = $(this).find('i');
            var content = ibox.children('.ibox-content');
            content.slideToggle(200);
            button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
            ibox.toggleClass('').toggleClass('border-bottom');
            setTimeout(function () {
                ibox.resize();
                ibox.find('[id^=map-]').resize();
            }, 50);
            return false;
        });
     </script>
@endsection