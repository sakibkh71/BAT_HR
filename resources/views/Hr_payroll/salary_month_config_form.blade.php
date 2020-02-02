@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <form action="#" data-toggle="validator" method="post" id="hr-employee-salary-info-form">
            {{csrf_field()}}
            <div class="row">
                <div class="col-lg-12 no-padding">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h2>{{$title}}</h2>
                            <div class="ibox-tools">
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Month<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                           placeholder=""
                                           class="form-control hr_salary_month_name"
                                           value="{{isset($info->year) ? $info->year.'-'.date('m',strtotime($info->month)):''}}"
                                           id="month"
                                           data-date-format="yyyy-mm"
                                           name="hr_salary_month_name" {{isset($info->hr_salary_month_configs_id)?"readonly":''}} required/>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Employee Category<span class="required">*</span> </label>
                                    {{__combo($slug = 'hr_emp_categorys', $data = array('selected_value'=> isset($info->hr_emp_categorys_id)?$info->hr_emp_categorys_id:'','attributes'=> array( 'required'=>'required', 'id'=>'hr_emp_category_id', 'class'=>'form-control')))}}
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Working Days<span class="required">*</span></label>
                                    <input type="number" name="number_of_working_days" value="{{isset($info->number_of_working_days)?$info->number_of_working_days:''}}"
                                           id="number_of_working_days"
                                           required="required"
                                           class="form-control" readonly="">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Holidays<span class="required">*</span></label>
                                    <input type="number" name="number_of_holidays" value="{{isset($info->number_of_holidays)?$info->number_of_holidays:''}}" placeholder=""
                                           id="number_of_holidays"
                                           required="required"
                                           class="form-control"  readonly="">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Weekend<span class="required">*</span></label>
                                    <input type="number" name="number_of_weekend" value="{{isset($info->number_of_weekend)?$info->number_of_weekend:''}}" placeholder=""
                                           id="number_of_weekend"
                                           required="required"
                                           class="form-control"  readonly="">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Active Employee</label>
                                    <input type="number" name="number_of_active_emp" value="{{isset($info->number_of_active_emp)?$info->number_of_active_emp:''}}" placeholder="" class="form-control"   id="number_of_active_emp" disabled>
                                </div>
                            </div>
                            <div class="row m-1">
                                <div class="form-group">
                                  <?php
                                  if(!empty($info)){ ?>
                                      <input type="hidden" name="existing_id" value="{{$info->hr_salary_month_configs_id}}">
                                      <button type="button" class="btn btn-primary btn-md" id="hr-employee-salary-month-info-submit">Update</button>
                                  <?php
                                  }else{ ?>
                                  <button type="button" class="btn btn-primary btn-md" id="hr-employee-salary-month-info-submit">Save</button>
                                  <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<script>
$(document).ready(function () {
    <?php if(!isset($info->hr_salary_month_configs_id)){ ?>
    $(".hr_salary_month_name").datepicker( {
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        startDate: '+0d',
        autoclose: true,
    }).on('changeDate', function(ev) {
        changefieldvalue();
    });
    <?php } ?>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    //form validation
    $('#hr-employee-salary-info-form').validator();

    //get days form change category
    $('#hr_emp_category_id').change(function (e) {
        var cat = $(this).val();
        var month = $('#month').val();
        if (month ==''){
            swalError("Please select month first");
        }else if(cat ==''){
            swalError("Please select employee category");
        }else{
            var data = { month:month, cat:cat };
            var url = '{{route('days-company-calender')}}';
            makeAjaxPost(data, url, null).done(function(response){
                if (response.status == "success"){
                    var day_H = response.data.H || 0 ;
                    var day_R = response.data.R || 0 ;
                    var day_W = response.data.W || 0 ;
                    var active_emp = response.data.active_emp || 0 ;
                    $('#number_of_holidays').val(day_H);
                    $('#number_of_working_days').val(day_R);
                    $('#number_of_weekend').val(day_W);
                    $('#number_of_active_emp').val(active_emp);
                }
            });
        }
    });

    //change when date picker change
    function changefieldvalue() {
        $('#hr_emp_category_id').val('');
        $('#number_of_working_days').val('');
        $('#number_of_holidays').val('');
        $('#number_of_weekend').val('');
        return false;
    };

    $(document).on('click','#hr-employee-salary-month-info-submit',function () {
        Ladda.bind(this);
        var load = $(this).ladda();
        var frm_data = $('#hr-employee-salary-info-form').serialize();
        var url = '{{URL::to('hr-employee-salary-month-info-save')}}';
        var validity_url = '{{URL::to('hr-employee-montly-holiday-check')}}';
        var ajax_success_url = '{{URL::to('hr-employee-salary-month-config-list')}}';
        makeAjaxPostText(frm_data,validity_url,load).then((isvalidresponse) =>{
            if(isvalidresponse == "matched"){
                makeAjaxPost(frm_data,url,load).then((response) =>{
                    if(response.status =='exist'){
                        swalError('Configuration already exist based on provided data');
                    }else if(response.status =='success'){
                        if (response.result == 'updated'){
                            swalRedirect(ajax_success_url,'successfully Updated.');
                        }else if(response.result == 'created'){
                            swalRedirect(ajax_success_url,'successfully Inserted.');
                        }
                    }else{
                        swalError();
                    }
                });
            }else{
                swalError('Total Accounted Days did"t with Month!');
            }
        });
    });
});
</script>
@endsection