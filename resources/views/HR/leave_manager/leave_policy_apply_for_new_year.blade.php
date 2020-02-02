@extends('layouts.app')
@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox">
                <div class="ibox-title">
                    <h2>Leave policy auto apply for new year</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <form class="" method="post" id="leaveForm">
                                <div class="row">
                                    {{csrf_field()}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Leave Year <span class="required">*</span></label>
                                            <div class="input-group ">
                                                <input type="text" placeholder="" class="form-control" id="leave_year" name="leave_year" value="{{$posted['leave_year']??''}}" required/>
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Employee Company <span class="required">*</span></label>
                                            <div class="input-group">
                                                {{__combo('bat_company', array('selected_value'=> !empty($posted['bat_company_id'])?$posted['bat_company_id']:'', 'attributes'=>array('name'=>'bat_company_id','class'=>'form-control', 'id'=>'bat_company_id', 'required'=>'')))}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <button class="btn btn-success" id="btnFind" type="button" data-style="zoom-out"> <i class="fa fa-serach"></i> Find Leaves</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    @if(!empty($result))
                                        <table class="table table-striped table-bordered table-hover master-grid">
                                            <thead>
                                                <tr>
                                                    <th>Policy Year</th>
                                                    <th>Company</th>
                                                    <th>Policy Name</th>
                                                    <th>Policy Leave Days</th>
                                                    <th>Is Carry</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($result as $tiem)
                                                    <tr>
                                                        <td>{{$tiem->hr_yearly_leave_policys_year}}</td>
                                                        <td>{{$tiem->company_name}}</td>
                                                        <td>{{$tiem->hr_yearly_leave_policys_name}}</td>
                                                        <td>{{$tiem->policy_leave_days}}</td>
                                                        <td>{{$tiem->is_carry}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(!empty($result))
                        <div class="col-md-6">
                            <div class="ibox">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Apply for <span class="required">*</span></label>
                                            <div class="input-group ">
                                                <input type="text" placeholder="" class="form-control" id="apply_year" name="apply_year" value="{{$posted['apply_year']??''}}" required/>
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Apply Company <span class="required">*</span></label>
                                            <div class="input-group">
                                                {{__combo('bat_company', array('selected_value'=> !empty($posted['apply_emp_company'])?$posted['apply_emp_company']:'', 'attributes'=>array('name'=>'apply_emp_company','class'=>'form-control multi', 'id'=>'apply_emp_company', 'multiple'=>'true','required'=>'')))}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <button class="btn btn-success mt-4" id="applyNextYear"><i class="fa fa-check"></i> Apply Now</button>
                                    </div>
                                </div>
                                <div class="ibox-content no-padding">
                                    <div id="newLeave" class="mt-4"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .emp_code_entry .btn-group{
        width: 100%;
    }
</style>
<script>

    //Datepicker
    $('#leave_year').datepicker({
        minViewMode: "years",
        format: "yyyy",
        autoclose: true
    });

    $('#apply_year').datepicker({
        minViewMode: "years",
        format: "yyyy",
        startDate: '+0d',
        autoclose: true
    });



    $('#leaveForm').validator();
    
    $('#btnFind').click(function (e) {
        e.preventDefault();
        var year =  $('#leave_year').val();
        var bat_company =  $('#bat_company_id').val();
        if (year ==''){
            swalError('Sorry, Please select Year');
        } else if(bat_company == '') {
            swalError('Sorry, Please select Company');
        }else{
            $('#leaveForm').submit();
        }
    });

    $('#applyNextYear').click(function (e) {

        var leaveyear = $('#leave_year').val();
        var bat_company_id = $('#bat_company_id').val();
        var aply_for = $('#apply_year').val();
        var apply_emp_company = $('#apply_emp_company').val();

        if(aply_for == ''){
            swalError('Apply for is required');
        }
        else if(apply_emp_company == ''){
            swalError('Apply Company');
        }else {
            var url = "{{URL::to('apply-leave-policy')}}";
            var _token = "{{ csrf_token() }}";
            var data = {
                _token: _token,
                leave_year: leaveyear,
                apply_cat: apply_emp_company,
                bat_company: bat_company_id,
                aply_for: aply_for
            };

            makeAjaxPost(data, url, null).then(function (s) {
                if (s.status == 'success') {
                    var row = '<table class="table table-striped table-bordered table-hover master-grid"><thead><tr><th>Policy Year</th><th>Policy Name</th><th>Policy Leave Days</th><th>Is Carry</th></tr></thead><tbody>';
                    $(s.data).each(function (index, value) {
                        row += '<tr><td>' + value.hr_yearly_leave_policys_year + '</td><td>' + value.hr_yearly_leave_policys_name + '</td><td>' + value.policy_leave_days + '</td><td>' + value.is_carry + '</td></tr>';
                    });
                    row += '</tbody></table>';
                    $('#newLeave').html(row);
                    swalSuccess('Success!, Leave Policy applied successfully');
                } else {
                    swalError('Sorry! Something wrong happened, please try again');
                }
            });
        }

    });
    /*
        $('#btnFind').click(function (e) {
            e.preventDefault();
            getLeavInfo();
        });

        $('#hr_emp_category_id').change(function (e) {
            e.preventDefault();
            getLeavInfo();
        });

        function getLeavInfo() {
            var year =  $('#leave_year').val();
            var category =  $('#hr_emp_category_id').val();
            var _token = "{{ csrf_token() }}";
        
        if (year !='' | category != '') {
            var url = "{{URL::to('get-leave-info-by-category')}}";
            var _token = "{{ csrf_token() }}";
            var data = {_token:_token, year:year,category:category};

            makeAjaxPost(data,url,null).then(function (s) {
                console.log(s.result);
            });
        }else{
            swalError('Sorry, Please select Year and Category');
        }
    }*/

</script>
@endsection