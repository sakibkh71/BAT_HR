@include('dropdown_grid.dropdown_grid')
@if(isset($mode) && $mode=='view')
<style>
    .form-control, .form-control:disabled, .form-control[readonly]{
        background: none;
        border: none;
        box-shadow: none;
        cursor: default;
        padding: 0;
        pointer-events: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    input.custom-check + label, .acc-select-toggle, .nominee-select-toggle, .emp-select-toggle, .edu-select-toggle{
        pointer-events: none;
    }
    .required, .input-group-addon{
        display: none;
    }
    button.multiselect.dropdown-toggle.btn.btn-default{
        background: none;
        box-shadow: none;
        border: none;
        cursor: default;
    }
    button.multiselect .caret{
        display: none;
        cursor: default;
    }
    .dropdown-menu.show,.view-mode .input-group.date .input-group-addon{
        display: none;
    }
    #present_thana_btn, #permanent_thana_btn{
        padding: 0 !important;
        background: none;
        color: #000;
        box-shadow: none;
        border: none;
        margin-bottom: 5px;
        font-weight: 600;
        cursor: default;
    }
    #present_thana_btn .fa, #permanent_thana_btn .fa,
    .same_as_present, #newEdu, #deleteEdu, #editEdu, #newAccInfo, #deleteAccInfo, #editAccInfo, #newEmploymentInfo, #deleteEmploymentInfo, #editEmploymentInfo,#addNominee,#newInsurance,#deleteInsurance,#editInsurance,#newEmargencyContract,#deleteEmargencyContract,#editEmargencyContract,#addMfsAccount, #newNomineeInfo, #submitSalaryInfo,  #newNomineeInfo, #deleteNomineeInfo, #editNomineeInfo, #submitOfficialInfo, #newCnv, .deleteCnv{
        display: none !important;
    }
    .step-content{
        background:#fff;
    }


</style>
@endif
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<section class="tab-pane fade active show" id="basic" role="tabpanel" aria-labelledby="basic-tab">
    <div class="accordion" id="EmployeeAccordion">
        <!-- ###############################################
                        BASIC INFORMATION
          #####################################################-->

        <div class="step-header open-header" id="headBasic">
            <h2 class="accordion-btn" type="button" data-toggle="collapse" data-target="#collapseBasic" aria-expanded="true" aria-controls="headBasic"> Personal <span class="indector"><i class="btn-icon"></i></span></h2>
        </div>

        <div id="collapseBasic" class="collapse show" aria-labelledby="headBasic" data-parent="#EmployeeAccordion">
            <div class="step-content">
                <form action="{{route('store-personal-info')}}" method="post" id="basicForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            {{--<div class="row">--}}
                            {{--<div class="col-md-6">--}}
                                {{--<label class="font-normal"><strong>Employee ID</strong></label>--}}
                                            {{--<div class="form-group">--}}
                                        {{--<input type="text" name="user_code" id="user_code" placeholder="Employee ID" class="form-control" value="{{ !empty($employee->user_code)?$employee->user_code:old('user_code')}}">--}}
                                                     {{--</div>--}}
                                                                                    {{--</div>--}}
                                                                                {{--</div>--}}
                            <div class="row">
                                <div class="col-md-6">
                                                    <label class="font-normal"><strong>Full Name </strong><span class="required">*</span></label>
                                                    <div class="form-group">
                                                        <input type="text" name="name" placeholder="Full Name English" class="form-control" value="{{ !empty($employee->name)?$employee->name:old('name')}}" data-error="Full name is required" required="">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="font-normal"><strong>Father's Name </strong> <span class="required">*</span></label>
                                                    <div class="form-group">
                                                        <input type="text" name="father_name" placeholder="Father's Name English" class="form-control" value="{{ !empty($employee->father_name)?$employee->father_name:old('father_name')}}"  data-error="Father Name  is required" required="">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="font-normal"><strong>Mother's Name </strong> <span class="required">*</span></label>
                                                    <div class="form-group">
                                                        <input type="text" name="mother_name" placeholder="Mother's Name English" class="form-control" value="{{ !empty($employee->mother_name)?$employee->mother_name:old('mother_name')}}"  data-error="Mother Name is required" required="">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="font-normal"><strong>Spouse Name </strong></label>
                                                    <div class="form-group">
                                                        <input type="text" name="spouse_name" placeholder="Spouse Name" class="form-control " value="{{ !empty($employee->spouse_name)?$employee->spouse_name:old('spouse_name')}}">
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Mobile </strong> <span class="required">*</span></label>
                                                        <input type="tel" name="mobile" id="mobile" placeholder="Mobile" class="form-control" value="{{ !empty($employee->mobile)?$employee->mobile:'+880'}}"  data-error="Mobile is required" required="">
                                                        <div id="mobile_error" class="help-block with-errors has-feedback"></div>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Date of Birth </strong> <span class="required">*</span></label>
                                                        <div class="input-group date">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            <input type="text" name="date_of_birth" class="form-control" data-error="Please select Birth Date" value="{{ !empty($employee->date_of_birth)?$employee->date_of_birth:old('date_of_birth')}}" placeholder="YYYY-MM-DD"  required="" autocomplete="off">
                                                        </div>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal"><strong>Blood Group </strong></label>
                                                    <select name="blood_group" id="blood_group" class="form-control multi" data-error="Please select Blood Group" data-error="Blood Group is required" autocomplete="off">
                                                        <option value="">Blood Group</option>
                                                        <option value="A+" {{!empty($employee->blood_group) && $employee->blood_group =='A+'?"selected":''}}>A+</option>
                                                        <option value="A-" {{!empty($employee->blood_group) && $employee->blood_group =='A-'?"selected":''}}>A-</option>
                                                        <option value="B+" {{!empty($employee->blood_group) && $employee->blood_group =='B+'?"selected":''}}>B+</option>
                                                        <option value="B-" {{!empty($employee->blood_group) && $employee->blood_group =='B-'?"selected":''}}>B-</option>
                                                        <option value="O+" {{!empty($employee->blood_group) && $employee->blood_group =='O+'?"selected":''}}>O+</option>
                                                        <option value="O-" {{!empty($employee->blood_group) && $employee->blood_group =='O-'?"selected":''}}>O-</option>
                                                        <option value="AB+" {{!empty($employee->blood_group) && $employee->blood_group =='AB+'?"selected":''}}>AB+</option>
                                                        <option value="AB-" {{!empty($employee->blood_group) && $employee->blood_group =='AB-'?"selected":''}}>AB-</option>
                                                    </select>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Gender</strong> <span class="required">*</span></label>
                                                        <select name="gender" id="gender" class="form-control multi" data-error="Please select Gender" required="" autocomplete="off">
                                                            <option value="">Gender</option>
                                                            <option value="Female" {{!empty($employee->gender) && $employee->gender =='Female'?"selected":''}}>Female</option>
                                                            <option value="Male" {{!empty($employee->gender) && $employee->gender =='Male'?"selected":''}}>Male</option>
                                                        </select>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Religion</strong> <span class="required">*</span></label>
                                                        <select name="religion" id="religion" class="form-control multi" data-error="Please select Religion"  required="" autocomplete="off">
                                                            <option value="">Religion</option>
                                                            <option value="Buddhist" {{!empty($employee->religion) && $employee->religion =='Buddhist'?"selected":''}}>Buddhist</option>
                                                            <option value="Christian" {{!empty($employee->religion) && $employee->religion =='Christian'?"selected":''}}>Christian</option>
                                                            <option value="Hindu" {{!empty($employee->religion) && $employee->religion =='Hindu'?"selected":''}}>Hindu</option>
                                                            <option value="Islam" {{!empty($employee->religion) && $employee->religion =='Islam'?"selected":''}}>Islam</option>
                                                            <option value="Others" {{!empty($employee->religion) && $employee->religion =='Others'?"selected":''}}>Others</option>
                                                        </select>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-normal"><strong>Marital Status</strong> <span class="required">*</span></label>
                                                        <select name="marital_status" id="marital_status" class="form-control multi" data-error="Please select marital status"  autocomplete="off">
                                                            <option value="">Marital Status</option>
                                                            <option value="Married" {{!empty($employee->marital_status) && $employee->marital_status =='Married'?"selected":''}}>Married</option>
                                                            <option value="Unmarried" {{!empty($employee->marital_status) && $employee->marital_status =='Unmarried'?"selected":''}}>Unmarried</option>
                                                            <option value="Devorced" {{!empty($employee->marital_status) && $employee->marital_status =='Devorced'?"selected":''}}>Devorced</option>
                                                            <option value="Widow" {{!empty($employee->marital_status) && $employee->marital_status =='Widow'?"selected":''}}>Widow</option>
                                                            <option value="Single" {{!empty($employee->marital_status) && $employee->marital_status =='Single'?"selected":''}}>Single</option>
                                                        </select>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>

                                            </div>


                                        </div>


                                        <div class="col-md-4 text-right">
                                            <div class="avatar-area text-center">
                                                <div class="employee-avater">
                                                    <div @if(!isset($mode)) id="userImage" @endif style="width: 100%; min-height: 50px;">
                                                        <img src="{{ !empty($employee->user_image)&&file_exists('public/img/'.$employee->user_image)? URL::to('public/img/'.$employee->user_image) : asset('public/img/default-user.jpg')}}" alt="" style="max-height: 300px" @if(!isset($mode)) id="userpic" @endif>
                                                    </div>
                                                    <input type='file' name="user_image" @if(!isset($mode)) onchange="readURL(this, 'userpic');" @endif id="userImgBtn" />
                                                        <small>(300px X 300px)</small>
                                                </div>
                                                <div class="employee-signature">
                                                    <div @if(!isset($mode)) id="userSign" @endif style="width: 100%;">
                                                          <img onclick="changeIt(this.src)" src="{{ !empty($employee->user_sign)? URL::to('public/img/'.$employee->user_sign) : asset('public/img/default-signature.jpg')}}" alt="" height="80" id="signPic">
                                                    </div>
                                                    <input type='file' name="user_sign" @if(!isset($mode))  onchange="readURL(this, 'signPic');" @endif id="signBtn"/>
                                                           <small>(150px X 80px)</small>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span class="font-italic font-bold"><span style="color:red;">* </span>Please provide atleast one of the following fields: (NID, Birth Certificate or Driving license)</span>
                                        </div>
                                        <div class="col-md-12" style="height: 10px;"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-normal"><strong>NID </strong> <span class="required"></span></label>
                                                <input type="text" name="nid" id="nid" placeholder="NID" class="form-control" value="{{ !empty($employee->nid)?$employee->nid:old('nid')}}" >
                                                <div id="nid_error" class="help-block with-errors has-feedback"></div>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-normal"><strong>Birth Certificate </strong> <span class="required"></span></label>
                                                <input type="text" name="birth_certificate" id="birth_certificate" placeholder="Birth Certificate" class="form-control" value="{{ !empty($employee->birth_certificate_no)?$employee->birth_certificate_no:old('birth_certificate_no')}}" >
                                                <div id="birth_certificate_error" class="help-block with-errors has-feedback"></div>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-normal"><strong>Passport </strong> <span class="required"></span></label>
                                                <input type="text" name="passport" id="passport" placeholder="Passport" class="form-control" value="{{ !empty($employee->passport)?$employee->passport:old('passport')}}" >
                                                <div id="passport_error" class="help-block with-errors has-feedback"></div>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-normal"><strong>Driving License </strong> <span class="required"></span></label>
                                                <input type="text" name="driving_license" id="driving_license" placeholder="Driving License" class="form-control" value="{{ !empty($employee->driving_license)?$employee->driving_license:old('driving_license')}}" >
                                                <div id="driving_license_error" class="help-block with-errors has-feedback"></div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-divider"></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @include('includes.address_panel',array('reference'=>'Present','key'=>'present',
                                            'district'=>isset($employee->present_district)?$employee->present_district:'',
                                            'thana'=>isset($employee->present_thana)?$employee->present_thana:'',
                                            'address_line'=>isset($employee->present_address_line)?$employee->present_address_line:'' ))
                                        </div>

                                        <div class="col-md-6 border-left">
                                            @include('includes.address_panel',array('reference'=>'Permanent','key'=>'permanent',
                                            'district'=>isset($employee->permanent_district)?$employee->permanent_district:'',
                                            'thana'=>isset($employee->permanent_thana)?$employee->permanent_thana:'',
                                            'address_line'=>isset($employee->permanent_address_line)?$employee->permanent_address_line:''))
                                        </div>
                                    </div>

                                    <div class="section-divider"></div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 font-normal"><strong>{{__lang('Distributor House')}}</strong> <span class="required">*</span></label>
                                                <div class="col-sm-12">
                                                    {{__combo('bat_company', array('selected_value'=> !empty($employee->bat_company_id)?$employee->bat_company_id:old('bat_company_id'), 'attributes'=> array( 'name'=>'bat_company_id', 'id'=>'bat_company_id', 'required'=>true, 'class'=>'form-control multi')))}}
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 font-normal"><strong>{{__lang('Distributor Point')}}</strong> <span class="required">*</span></label>
                                                <div class="col-sm-12">
                                                    <select class="form-control multi" name="bat_dpid" id="bat_distributor_point">
                                                        <option value="">--Select--</option>
                                                        @if(isset($emp_dPoints)) {
                                                        @foreach($emp_dPoints as $point)
                                                        <option value="{{$point->id}}" {{ !empty($point->id) && $employee->bat_dpid == $point->id ? 'selected' : '' }}>{{$point->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 font-normal"><strong>Designation</strong> <span class="required">*</span></label>
                                                <div class="col-sm-12">
                                                    {{__combo('designation', array('selected_value'=> !empty($employee->designations_id)?$employee->designations_id:old('designations_id'), 'attributes'=> array( 'name'=>'designations_id', 'required'=>true, 'id'=>'designations_id','class'=>'form-control multi')))}}
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--<div class="col-md-3 route_div">--}}
                                            {{--<div class="form-group row">--}}
                                                {{--<label class="col-sm-12 font-normal"><strong>{{__lang('Select_Route')}}</strong></label>--}}
                                                {{--<div class="col-sm-12">--}}
                                                    {{--@if(!empty($emp_sr_id))--}}
                                                        {{--@php($emp_sr_id = $emp_sr_id->number)--}}
                                                    {{--@else--}}
                                                        {{--@php($emp_sr_id = '')--}}
                                                    {{--@endif--}}

                                                    {{--<select class="form-control multi" name="route_id[]" id="bat_route_id"  @if(!empty($employee->designations_id) && $employee->designations_id == 151 )multiple="multiple" @endif>--}}
                                                        {{--@if(!empty($emp_routes))--}}
                                                            {{--@if(!empty($employee->designations_id) && $employee->designations_id == 152 )--}}
                                                                {{--<option value="">--Select Route--</option>--}}
                                                            {{--@endif--}}

                                                            {{--@foreach($emp_routes as $info)--}}
                                                                {{--<option value="{{$info->number}}" @if($emp_sr_id == $info->number) selected="selected" @endif>{{$info->number}}</option>--}}
                                                            {{--@endforeach--}}
                                                        {{--@endif--}}
                                                    {{--</select>--}}
                                                    {{--<div class="help-block with-errors has-feedback"></div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 font-normal"><strong>Joining Date </strong> <span class="required">*</span></label>
                                                <div class="col-sm-12">
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" name="date_of_join" id="date_of_join" class="form-control" data-error="Joining date is required" value="{{ !empty($employee->date_of_join)?$employee->date_of_join:old('date_of_join')}}" placeholder="YYYY-MM-DD" required="" autocomplete="off">
                                                    </div>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 font-normal"><strong>{{__lang('Confirmation_date')}}</strong> <span class="required">*</span></label>
                                                <div class="col-sm-12">
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" name="date_of_confirmation" id="date_of_confirmation" class="form-control" data-error="Confirmation date is required" value="{{ !empty($employee->date_of_confirmation)?$employee->date_of_confirmation:old('date_of_confirmation')}}" required="" placeholder="YYYY-MM-DD" autocomplete="off">
                                                    </div>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                        <!---

                                       Salary info copy starts


                                        -->
                    <div class="section-divider"></div>

                                        <div class="row" >
                                            <div class="col-md-12"> <h3><b>Salary Info</b></h3></div>
                                            <div class="help-block with-errors has-feedback col-md-12 salary_error" style="color: red"></div>

                                            <div class="col-md-12 row" id="basic_salary_options">
{{--                                                                                        <div class="col-md-4">--}}
{{--                                                                                            <div class="form-group row">--}}
{{--                                                                                                <label class="col-sm-12 font-normal"><strong>Grade</strong></label>--}}
{{--                                                                                                <div class="col-sm-12">--}}
{{--                                                                                                    {{__combo('hr_emp_grades', array('selected_value'=> !empty($employee->hr_emp_grades_id)?$employee->hr_emp_grades_id:old('hr_emp_grades_id'), 'attributes'=> array( 'name'=>'hr_emp_grades_id',  'id'=>'hr_emp_grades_id', 'class'=>'form-control multi')))}}--}}

{{--                                                                                                    <div class="help-block with-errors has-feedback"></div>--}}
{{--                                                                                                </div>--}}
{{--                                                                                            </div>--}}
{{--                                                                                        </div>--}}
                                            <input type="hidden" name="hr_emp_grades_id" id="hr_emp_grades_id" value="{{!empty($employee->hr_emp_grade_id)?$employee->hr_emp_grade_id:old('hr_emp_grades_id')}}">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="font-normal"><strong>Basic Salary </strong> <span class="required">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                        <input type="text" name="basic_salary" id="basic_salary" placeholder="Basic Salary" class="basic_salary input_money form-control" value="{{ !empty($employee->basic_salary)?$employee->basic_salary:old('basic_salary')}}" required="">
                                                    </div>
                                                    <div class="help-block with-errors has-feedback"></div>
                                                </div>
                                            </div>
                                            {{--</div>--}}

                                            {{--<div class="row">--}}
                                            {{--<div class="col-md-12">--}}
                                            {{--<h4>Addition</h4>--}}
                                            {{--<hr class="mt-0">--}}
                                            {{--<div id="additionalOption" >--}}
                                            @if(isset($salary_components['Addition']) && count($salary_components['Addition']) > 0)
                                                @php($additionVal=0)
                                                @foreach($salary_components['Addition'] as $additionItem)
                                                    @if($additionItem['auto_applicable']=='YES')
                                                        @php( $additionVal += $additionItem['addition_amount'])
                                                    @endif
                                                    <div class="col-md-4 remove_additional_info" >
                                                        <div class="form-group">
                                                            <label class="font-normal"><strong>{{$additionItem['component_name']}}</strong><input type="hidden" name="component_name[{{$additionItem['component_slug']}}]" value="{{$additionItem['component_name']}}"></label>
                                                            <div class="input-group">
                                                                <input type="text" name="salary_component[{{$additionItem['component_slug']}}]" data-autoapply="{{$additionItem['auto_applicable']}}" data-type="{{$additionItem['component_type']}}" data-id="{{$additionItem['component_slug']}}" class="form-control input_money" value="{{$additionItem['addition_amount']}}">
                                                                <span class="input-group-addon no-display"><input type="checkbox" name="component_autoapply[{{$additionItem['component_slug']}}]" value="{{$additionItem['auto_applicable']}}" class="pull-left auto_aply_field mr-1" @if($additionItem['auto_applicable'] =='YES') checked="" @endif data-id="{{$additionItem['component_slug']}}">  Add to Gross </span>
                                                                <input type="hidden" name="component_type[{{$additionItem['component_slug']}}]" value="{{$additionItem['component_type']}}"><input type="hidden" name="component_slug[]" value="{{$additionItem['component_slug']}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <hr class="no-display"> <div class="col-md-12 text-right no-display"> Total Addition Amount = <strong class="totalAddition">{{$additionVal}}</strong><input type="hidden" id="total_adition" value="{{$additionVal}}"></div><hr class="no-display">
                                            @endif
                                            {{--</div>--}}
                                            {{--</div>--}}
                                            <div  class="col-md-12 @if(isset($salary_components['Deduction']) && count($salary_components['Deduction']) > 0) display @else no-display @endif">
                                                <h4>Deductionshr_emp_grades_id</h4> <hr class="mt-0">
                                                <div id="deductionalOption" class="row">
                                                    @if(isset($salary_components['Deduction']) && count($salary_components['Deduction']) > 0)
                                                        @php($deductionVal=0)
                                                        @foreach($salary_components['Deduction'] as $deductionItem)
                                                            @if($deductionItem['auto_applicable']=='YES')
                                                                @php($deductionVal += $deductionItem['deduction_amount'])
                                                            @endif
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="font-normal"><strong>{{$deductionItem['component_name']}}</strong><input type="hidden" name="component_name[{{$deductionItem['component_slug']}}]" value="{{$deductionItem['component_name']}}"></label>
                                                                    <div class="input-group">
                                                                        <input type="text" name="salary_component[{{$deductionItem['component_slug']}}]" data-autoapply="{{$deductionItem['auto_applicable']}}" data-type="{{$deductionItem['component_type']}}" data-id="{{$deductionItem['component_slug']}}" class="form-control input_money" value="{{$deductionItem['deduction_amount']}}">
                                                                        @if($deductionItem['component_slug']!=getOptionValue('pf_slug'))
                                                                            <span class="input-group-addon"><input type="checkbox" name="component_autoapply[{{$deductionItem['component_slug']}}]" value="{{$deductionItem['auto_applicable']}}" class="pull-left auto_aply_field mr-1" @if($deductionItem['auto_applicable'] =='YES') checked="" @endif data-id="{{$deductionItem['component_slug']}}">  Add to Gross </span>
                                                                        @endif
                                                                        <input type="hidden" name="component_type[{{$deductionItem['component_slug']}}]" value="{{$deductionItem['component_type']}}"><input type="hidden" name="component_slug[]" value="{{$deductionItem['component_slug']}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <hr><div class="col-md-12 text-right"> Total Deduction Amount = <strong class="totalDeduction">{{$deductionVal}}</strong><input type="hidden" id="total_deduction" value="{{$deductionVal}}"></div><hr>
                                                    @endif
                                                </div>
                                            </div>
                                            {{--</div>--}}
                                            {{--<div class="row">--}}
                                            <div id="additionOption">
                                            </div>
                                            <div class="col-md-4" id="insert_before">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 font-normal"><strong>{{__lang('Gross_salary')}} </strong> <span class="required">*</span></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="min_gross" id="min_gross" readonly="" placeholder="Gross Salary" class="form-control input_money" value="{{ !empty($employee->min_gross)?$employee->min_gross:old('min_gross')}}" required="">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="variableOption"  @if(!isset($salary_components['Variable'])) style="display: none;" @endif>
                                                <div class="row">
                                                    @if(isset($salary_components['Variable']) && count($salary_components['Variable']) > 0)
                                                        @php($variableVal=0)
                                                        @foreach($salary_components['Variable'] as $variableItem)
                                                            @php($variableVal += $variableItem['addition_amount'])
                                                            <div class="col-md-12" style="display:none">
                                                                <div class="form-group">
                                                                    <label class="font-normal"><strong>{{$variableItem['component_name']}}</strong><input type="hidden" name="component_name[{{$variableItem['component_slug']}}]" value="{{$variableItem['component_name']}}"></label>
                                                                    <div class="input-group">
                                                                        <input type="text" name="salary_component[{{$variableItem['component_slug']}}]" data-autoapply="NO" data-type="Variable" data-id="{{$variableItem['component_slug']}}" class="form-control" value="{{$variableItem['addition_amount']}}">
                                                                        <span class="input-group-addon"><input type="checkbox" name="component_autoapply[{{$variableItem['component_slug']}}]" value="YES" class="pull-left mr-1" data-id="{{$variableItem['component_slug']}}">  Add to Gross</span>
                                                                        <input type="hidden" name="component_type[{{$variableItem['component_slug']}}]" value="Variable">
                                                                        <input type="hidden" name="component_slug[]" value="{{$variableItem['component_slug']}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="font-normal"><strong>Variable Salary</strong></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                                                    <input type="number" step="any" min="0" name="max_variable_salary" id="max_variable_salary" class="form-control text-left" placeholder="Variable Salary" autocomplete="off" value="{{ !empty($employee->max_variable_salary)?$employee->max_variable_salary:old('max_variable_salary')}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-3">
                                                <div class="form-group ">

                                                    <div class="text-left" style="margin-top: 10px">
                                                        <input class="custom-check" type="checkbox"  name="pf_applicable" id="pf_applicable" tabindex="3" value="1" {{ !empty($employee->pf_applicable) && $employee->pf_applicable == 1?'checked':'' }}>
                                                        <label for="pf_applicable">{{__lang('Provident Fund Applicable')}} <a data-toggle="tooltip" data-placement="top" title="Are you sure to Provident Fund Applicable ?"><i class="fa fa-info-circle"></i></a></label>
                                                    </div>



                                                </div>

                                            </div>
                                        </div>




                                        </div>




                                        <!--
                                        Salary info copy ends

                                        -->

                                        @if(!isset($employee->id))
                                        {{--<div class="col-md-3">--}}
                                            {{--<div class="form-group tooltip-demo mt-4">--}}
                                                {{--<label for="attendance_apply"><input class="float-left mt-1 mr-1" type="checkbox" id="attendance_apply" name="attendance_apply" value="1" {{ !empty($employee->attendance_apply) && $employee->attendance_apply == 1?'checked':''}}> Attendance apply ?</label>--}}
                                                {{--<a data-toggle="tooltip" data-placement="top" title="Are you sure to attendance apply?"><i class="fa fa-info-circle"></i></a>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        @endif
                                        <div class="col-md-3"  style="display: none;">
                                            <div class="form-group tooltip-demo mt-4">
{{--                                                <label for="leave_apply"><input class="float-left mt-1 mr-1" type="checkbox" id="leave_apply" name="leave_apply" value="1" {{empty($employee->name)?'checked':'' }} {{ !empty($employee->leave_policy_apply) && $employee->leave_policy_apply == 1?'checked':''}}> Leave Policy Apply ?</label>--}}
                                                <label for="leave_apply"><input class="float-left mt-1 mr-1" type="checkbox" id="leave_apply" name="leave_apply" value="1" checked> Leave Policy Apply ?</label>
                                                <a data-toggle="tooltip" data-placement="top" title="Are you sure to Leave apply?"><i class="fa fa-info-circle"></i></a>
                                            </div>
                                        </div>

                                    <div class="section-divider"></div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if(isset($employee->id) && !empty($employee->id))
                                            <input type="hidden" name="employee_id" value="{{$employee->id}}">
                                            @endif

                                            @if(!isset($mode))
                                            <button class="btn btn-primary btn-lg" type="button" id="basicInfoSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                                            @endif
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>

                            <!-- ###############################################
                                            NOMINEES INFORMATION
                              #####################################################-->
        @if(isset($employee->user_code))
                            <div class="step-header" id="headBank">
                                <h2 class="accordion-btn collapsed" type="button"  @if(isset($employee->id) && !empty($employee->id)) data-toggle="collapse" data-target="#collapseNominee" @endif aria-expanded="true" aria-controls="headNominee"> Nominee Information <span class="indector"><i class="btn-icon"></i></span></h2>

                            </div>
                            <div id="collapseNominee" class="collapse" aria-labelledby="headNominee" data-parent="#EmployeeAccordion">
                                <div class="step-content">
                                    {{--<div class="row">--}}
                                    {{--<div class="col-md-12 text-right mb-2">--}}
                                    {{--<button class="btn btn-success btn-xs" id="newNomineeInfo"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>--}}
                                    {{--<button class="btn btn-danger btn-xs hide" id="deleteNomineeInfo"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; Delete</button>--}}
                                    {{--<button class="btn btn-warning btn-xs hide" id="editNomineeInfo"><i class="fa fa-edit" aria-hidden="true"></i> &nbsp; Edit</button>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}

                                    <form action="#" method="post" id="nomineeForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label">Nominee Name <span class="required">*</span></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="nominee_name" placeholder="Nominee Name" class="form-control" value="{{ !empty($nominees->nominee_name)?$nominees->nominee_name:old('nominee_name')}}"  data-error="Nominee Name is required" required="">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label">Nominee Relationship <span class="required">*</span></label>
                                                    <div class="col-sm-12">
                                                        {{__combo('nominees_relationships',array('selected_value'=> !empty($nominees->nominee_relationship)?$nominees->nominee_relationship:"", 'attributes'=> array('class'=>'form-control multi','id'=>'nominee_relationship','name'=>'nominee_relationship')))}}
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label">NID/Pass./Dri. License </label>
                                                    <div class="col-sm-12">

                                                        <select name="id_type" id="id_type" class="form-control">
                                                            <option value="">Please Select</option>
                                                            <option @if($nominees_id_type == 'NID') selected @endif value="NID">NID</option>
                                                            <option @if($nominees_id_type == 'PASSPORT') selected @endif value="PASSPORT">Passport</option>
                                                            <option @if($nominees_id_type == 'DRIVING LICENSE') selected @endif value="DRIVING LICENSE">Driving License</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-3" id="id_div">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 form-label label-title"></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="id_number" placeholder="Number" class="form-control" value="{{ !empty($nominees->id_number)?$nominees->id_number:old('id_number')}}"  data-error="NID is required">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(isset($nominees->hr_emp_nominees_id) && !empty($nominees->hr_emp_nominees_id))
                                            <input type="hidden" id="empnominee_id" name="hr_emp_nominees_id" value="{{$nominees->hr_emp_nominees_id}}">
                                            @endif
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    @if(!empty($nominees))
                                                    <div class="col-sm-12"> <button class="btn btn-primary btn-lg " type="button" id="addNominee"><i class="fa fa-check"></i>&nbsp;Update</button></div>
                                                    @else
                                                    <div class="col-sm-12"> <button class="btn btn-primary btn-lg " type="button" id="addNominee"><i class="fa fa-check"></i>&nbsp;Submit</button></div>

                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>


                                </div>
                            </div>

                            <!-- ###############################################
                                            SALARY INFORMATION
                              #####################################################-->

{{--                            <div class="step-header" id="headSalary">--}}
{{--                                <h2 class="accordion-btn collapsed" type="button" @if(isset($employee->id) && !empty($employee->id)) data-toggle="collapse" data-target="#collapseSalary" @endif aria-expanded="true" aria-controls="headSalary"> Salary Info <span class="indector"><i class="btn-icon"></i></span></h2>--}}
{{--                            </div>--}}
{{--                            <div id="collapseSalary" class="collapse" aria-labelledby="headSalary" data-parent="#EmployeeAccordion">--}}
{{--                                <div class="step-content">--}}
{{--                                    <form action="#" method="post" id="salaryForm">--}}
{{--                                        @csrf--}}
{{--                                        <div class="row" id="basic_salary_options">--}}
{{--                                            <div class="col-md-3">--}}
{{--                                                <div class="form-group row">--}}
{{--                                                    <label class="col-sm-12 font-normal"><strong>Grade</strong></label>--}}
{{--                                                    <div class="col-sm-12">--}}
{{--                                                        {{__combo('hr_emp_grades', array('selected_value'=> !empty($employee->hr_emp_grades_id)?$employee->hr_emp_grades_id:old('hr_emp_grades_id'), 'attributes'=> array( 'name'=>'hr_emp_grades_id',  'id'=>'hr_emp_grades_id', 'class'=>'form-control multi')))}}--}}
{{--                                                     --}}
{{--                                                        <div class="help-block with-errors has-feedback"></div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <input type="hidden" name="hr_emp_grades_id" id="hr_emp_grades_id">--}}
{{--                                            <div class="col-md-3">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label class="font-normal"><strong>Basic Salary </strong> <span class="required">*</span></label>--}}
{{--                                                    <div class="input-group">--}}
{{--                                                        <span class="input-group-addon"> <i class="fa fa-money"></i> </span>--}}
{{--                                                        <input type="text" name="basic_salary" id="basic_salary" placeholder="Basic Salary" class="basic_salary input_money form-control" value="{{ !empty($employee->basic_salary)?$employee->basic_salary:old('basic_salary')}}" required="">--}}
{{--                                                    </div>--}}
{{--                                                    <div class="help-block with-errors has-feedback"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            --}}{{--</div>--}}

{{--                                            --}}{{--<div class="row">--}}
{{--                                            --}}{{--<div class="col-md-12">--}}
{{--                                            --}}{{--<h4>Addition</h4>--}}
{{--                                            --}}{{--<hr class="mt-0">--}}
{{--                                            --}}{{--<div id="additionalOption" >--}}
{{--                                            @if(isset($salary_components['Addition']) && count($salary_components['Addition']) > 0)--}}
{{--                                            @php($additionVal=0)--}}
{{--                                            @foreach($salary_components['Addition'] as $additionItem)--}}
{{--                                            @if($additionItem['auto_applicable']=='YES')--}}
{{--                                            @php( $additionVal += $additionItem['addition_amount'])--}}
{{--                                            @endif--}}
{{--                                            <div class="col-md-3 remove_additional_info" >--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label class="font-normal"><strong>{{$additionItem['component_name']}}</strong><input type="hidden" name="component_name[{{$additionItem['component_slug']}}]" value="{{$additionItem['component_name']}}"></label>--}}
{{--                                                    <div class="input-group">--}}
{{--                                                        <input type="text" name="salary_component[{{$additionItem['component_slug']}}]" data-autoapply="{{$additionItem['auto_applicable']}}" data-type="{{$additionItem['component_type']}}" data-id="{{$additionItem['component_slug']}}" class="form-control input_money" value="{{$additionItem['addition_amount']}}">--}}
{{--                                                        <span class="input-group-addon no-display"><input type="checkbox" name="component_autoapply[{{$additionItem['component_slug']}}]" value="{{$additionItem['auto_applicable']}}" class="pull-left auto_aply_field mr-1" @if($additionItem['auto_applicable'] =='YES') checked="" @endif data-id="{{$additionItem['component_slug']}}">  Add to Gross </span>--}}
{{--                                                        <input type="hidden" name="component_type[{{$additionItem['component_slug']}}]" value="{{$additionItem['component_type']}}"><input type="hidden" name="component_slug[]" value="{{$additionItem['component_slug']}}">--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            @endforeach--}}
{{--                                            <hr class="no-display"> <div class="col-md-12 text-right no-display"> Total Addition Amount = <strong class="totalAddition">{{$additionVal}}</strong><input type="hidden" id="total_adition" value="{{$additionVal}}"></div><hr class="no-display">--}}
{{--                                            @endif--}}
{{--                                            --}}{{--</div>--}}
{{--                                            --}}{{--</div>--}}
{{--                                            <div  class="col-md-12 @if(isset($salary_components['Deduction']) && count($salary_components['Deduction']) > 0) display @else no-display @endif">--}}
{{--                                                <h4>Deductionshr_emp_grades_id</h4> <hr class="mt-0">--}}
{{--                                                <div id="deductionalOption" class="row">--}}
{{--                                                    @if(isset($salary_components['Deduction']) && count($salary_components['Deduction']) > 0)--}}
{{--                                                    @php($deductionVal=0)--}}
{{--                                                    @foreach($salary_components['Deduction'] as $deductionItem)--}}
{{--                                                    @if($deductionItem['auto_applicable']=='YES')--}}
{{--                                                    @php($deductionVal += $deductionItem['deduction_amount'])--}}
{{--                                                    @endif--}}
{{--                                                    <div class="col-md-3">--}}
{{--                                                        <div class="form-group">--}}
{{--                                                            <label class="font-normal"><strong>{{$deductionItem['component_name']}}</strong><input type="hidden" name="component_name[{{$deductionItem['component_slug']}}]" value="{{$deductionItem['component_name']}}"></label>--}}
{{--                                                            <div class="input-group">--}}
{{--                                                                <input type="text" name="salary_component[{{$deductionItem['component_slug']}}]" data-autoapply="{{$deductionItem['auto_applicable']}}" data-type="{{$deductionItem['component_type']}}" data-id="{{$deductionItem['component_slug']}}" class="form-control input_money" value="{{$deductionItem['deduction_amount']}}">--}}
{{--                                                                @if($deductionItem['component_slug']!=getOptionValue('pf_slug'))--}}
{{--                                                                <span class="input-group-addon"><input type="checkbox" name="component_autoapply[{{$deductionItem['component_slug']}}]" value="{{$deductionItem['auto_applicable']}}" class="pull-left auto_aply_field mr-1" @if($deductionItem['auto_applicable'] =='YES') checked="" @endif data-id="{{$deductionItem['component_slug']}}">  Add to Gross </span>--}}
{{--                                                                @endif--}}
{{--                                                                <input type="hidden" name="component_type[{{$deductionItem['component_slug']}}]" value="{{$deductionItem['component_type']}}"><input type="hidden" name="component_slug[]" value="{{$deductionItem['component_slug']}}">--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    @endforeach--}}
{{--                                                    <hr><div class="col-md-12 text-right"> Total Deduction Amount = <strong class="totalDeduction">{{$deductionVal}}</strong><input type="hidden" id="total_deduction" value="{{$deductionVal}}"></div><hr>--}}
{{--                                                    @endif--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            --}}{{--</div>--}}
{{--                                            --}}{{--<div class="row">--}}
{{--                                            <div id="additionOption">--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-3" id="insert_before">--}}
{{--                                                <div class="form-group row">--}}
{{--                                                    <label class="col-sm-12 font-normal"><strong>{{__lang('Gross_salary')}} </strong> <span class="required">*</span></label>--}}
{{--                                                    <div class="col-sm-12">--}}
{{--                                                        <input type="text" name="min_gross" id="min_gross" readonly="" placeholder="Gross Salary" class="form-control input_money" value="{{ !empty($employee->min_gross)?$employee->min_gross:old('min_gross')}}" required="">--}}
{{--                                                        <div class="help-block with-errors has-feedback"></div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-3" id="variableOption"  @if(!isset($salary_components['Variable'])) style="display: none;" @endif>--}}
{{--                                                 <div class="row">--}}
{{--                                                    @if(isset($salary_components['Variable']) && count($salary_components['Variable']) > 0)--}}
{{--                                                    @php($variableVal=0)--}}
{{--                                                    @foreach($salary_components['Variable'] as $variableItem)--}}
{{--                                                    @php($variableVal += $variableItem['addition_amount'])--}}
{{--                                                    <div class="col-md-12" style="display:none">--}}
{{--                                                        <div class="form-group">--}}
{{--                                                            <label class="font-normal"><strong>{{$variableItem['component_name']}}</strong><input type="hidden" name="component_name[{{$variableItem['component_slug']}}]" value="{{$variableItem['component_name']}}"></label>--}}
{{--                                                            <div class="input-group">--}}
{{--                                                                <input type="text" name="salary_component[{{$variableItem['component_slug']}}]" data-autoapply="NO" data-type="Variable" data-id="{{$variableItem['component_slug']}}" class="form-control" value="{{$variableItem['addition_amount']}}">--}}
{{--                                                                <span class="input-group-addon"><input type="checkbox" name="component_autoapply[{{$variableItem['component_slug']}}]" value="YES" class="pull-left mr-1" data-id="{{$variableItem['component_slug']}}">  Add to Gross</span>--}}
{{--                                                                <input type="hidden" name="component_type[{{$variableItem['component_slug']}}]" value="Variable">--}}
{{--                                                                <input type="hidden" name="component_slug[]" value="{{$variableItem['component_slug']}}">--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    @endforeach--}}

{{--                                                    <div class="col-md-12">--}}
{{--                                                        <div class="form-group">--}}
{{--                                                            <label class="font-normal"><strong>Variable Salary</strong></label>--}}
{{--                                                            <div class="input-group">--}}
{{--                                                                <span class="input-group-addon"> <i class="fa fa-money"></i> </span>--}}
{{--                                                                <input type="number" step="any" min="0" name="max_variable_salary" id="max_variable_salary" class="form-control text-left" placeholder="Variable Salary" autocomplete="off" value="{{ !empty($employee->max_variable_salary)?$employee->max_variable_salary:old('max_variable_salary')}}">--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    @endif--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            --}}{{--<div class="col-md-3">--}}
{{--                                            --}}{{--<div class="form-group">--}}
{{--                                            --}}{{--<label class="font-normal"><strong>Yearly Increment  ( % ) </strong></label>--}}
{{--                                            --}}{{--<div class="input-group">--}}
{{--                                            --}}{{--<span class="input-group-addon"> % </span>--}}
{{--                                            --}}{{--<input type="number" step="any" name="yearly_increment" id="yearly_increment" placeholder="Yearly Increment" class="form-control text-left input_money" value="{{ !empty($employee->yearly_increment)?$employee->yearly_increment:old('yearly_increment')}}">--}}
{{--                                            --}}{{--</div>--}}
{{--                                            --}}{{--<div class="help-block with-errors has-feedback"></div>--}}
{{--                                            --}}{{--</div>--}}
{{--                                            --}}{{--</div>--}}

{{--                                        </div>--}}
{{--                                        <div class="row">--}}
{{--                                            --}}{{--<div class="col-md-3">--}}
{{--                                            --}}{{--<div class="form-group">--}}
{{--                                            --}}{{--<div class="text-left" style="margin-top: 10px">--}}
{{--                                            --}}{{--<input class="custom-check" name="insurance_applicable" id="insurance_applicable" type="checkbox" tabindex="3" value="1" {{ isset($employee->insurance_applicable) && $employee->insurance_applicable == 1?'checked':'' }} >--}}
{{--                                            --}}{{--<label for="insurance_applicable">Insurance Applicable</label>--}}
{{--                                            --}}{{--</div>--}}
{{--                                            --}}{{--</div>--}}
{{--                                            --}}{{--<div class="input-group @if( !isset($employee->insurance_applicable)) no-display @endif" id="insurance_input"><input type="number"  step="any" min="0" id="insurance_amount" name="insurance_amount" class="form-control text-left input_money" value="{{ $employee->insurance_amount??0 }}"></div>--}}
{{--                                        --}}{{--</div>--}}

{{--                                        <div class="col-md-3 mt-3">--}}
{{--                                            <div class="form-group ">--}}

{{--                                                <div class="text-left" style="margin-top: 10px">--}}
{{--                                                    <input class="custom-check" type="checkbox"  name="pf_applicable" id="pf_applicable" tabindex="3" value="1" {{ !empty($employee->pf_applicable) && $employee->pf_applicable == 1?'checked':'' }}>--}}
{{--                                                    <label for="pf_applicable">{{__lang('Provident Fund Applicable')}} <a data-toggle="tooltip" data-placement="top" title="Are you sure to Provident Fund Applicable ?"><i class="fa fa-info-circle"></i></a></label>--}}
{{--                                                </div>--}}

{{--                                                --}}{{--<div class="col-md-4 @if( !isset($employee->pf_applicable)) no-display @endif" id="pfr_input">--}}
{{--                                                --}}{{--<div class="input-group">--}}
{{--                                                --}}{{--@if(isset($employee))--}}
{{--                                                --}}{{--@php($rate = ($employee->basic_salary>0) && ($employee->pf_amount>0)? (($employee->pf_amount*100)/$employee->basic_salary):0)--}}
{{--                                                --}}{{--@else--}}
{{--                                                --}}{{--@php($rate = 0)--}}
{{--                                                --}}{{--@endif--}}
{{--                                                --}}{{--<input type="number" step="any" name="pf_rate" id="pf_rate" placeholder="0" class="form-control text-left input_money" value="{{number_format($rate,2)}}">--}}
{{--                                                --}}{{--<span class="input-group-addon"> % </span>--}}
{{--                                                --}}{{--</div>--}}
{{--                                                --}}{{--</div>--}}

{{--                                            </div>--}}
{{--                                            --}}{{--<div class="input-group @if( !isset($employee->pf_applicable)) no-display @endif" id="pf_input">--}}
{{--                                            --}}{{--<input type="number" id="pf_amount"  step="any" min="0" name="pf_amount" class="form-control text-left input_money" value="{{ $employee->pf_amount??0 }}">--}}
{{--                                            --}}{{--</div>--}}
{{--                                        </div>--}}
{{--                                        --}}{{--<div class="col-md-3 mt-3">--}}
{{--                                            --}}{{--<div class="form-group">--}}
{{--                                                --}}{{--<div class="text-left" style="margin-top: 10px">--}}
{{--                                                    --}}{{--<input class="custom-check" type="checkbox"  name="late_deduction_applied" id="late_deduction_applied" tabindex="3" value="1" {{ !empty($employee->late_deduction_applied) && $employee->late_deduction_applied == 1?'checked':'' }}>--}}
{{--                                                    --}}{{--<label for="late_deduction_applied">Late Deduction Applied</label>--}}
{{--                                                --}}{{--</div>--}}
{{--                                            --}}{{--</div>--}}
{{--                                        --}}{{--</div>--}}
{{--                                        --}}{{--<div class="col-md-3 mt-3">--}}
{{--                                            --}}{{--<div class="form-group">--}}
{{--                                                --}}{{--<div class="text-left" style="margin-top: 10px">--}}
{{--                                                    --}}{{--<input class="custom-check" type="checkbox"  name="gf_applicable" id="gf_applicable" tabindex="3" value="1" {{ !empty($employee->gf_applicable) && $employee->gf_applicable == 1?'checked':'' }}>--}}
{{--                                                    --}}{{--<label for="gf_applicable">Gratuity Fund Applicable</label>--}}
{{--                                                --}}{{--</div>--}}
{{--                                            --}}{{--</div>--}}

{{--                                        --}}{{--</div>--}}

{{--                                        --}}{{--<div class="col-md-3 mt-3">--}}
{{--                                            --}}{{--<div class="form-group">--}}
{{--                                                --}}{{--<div class="text-left" style="margin-top: 10px">--}}
{{--                                                    --}}{{--<input class="custom-check" type="checkbox"  name="other_conveyance" id="other_conveyance" tabindex="3" value="1" {{ !empty($employee->other_conveyance) && $employee->other_conveyance == 1?'checked':'' }}>--}}
{{--                                                    --}}{{--<label for="other_conveyance">Other Conveyance</label>--}}
{{--                                                --}}{{--</div>--}}
{{--                                            --}}{{--</div>--}}
{{--                                        --}}{{--</div>--}}
{{--                                </div>--}}

{{--                                <div class="row" id="conveyance_area" style="display: {{ !empty($employee->other_conveyance) && $employee->other_conveyance == 1?'':'none' }};">--}}
{{--                                    <table class="table table-bordered table-hover">--}}
{{--                                        <thead>--}}
{{--                                            <tr>--}}
{{--                                                <th>Other Conveyance Detail</th>--}}
{{--                                                <th width="300">Amount</th>--}}
{{--                                                <th width="1">--}}
{{--                                                    <button type="button" id="newCnv" class="btn btn-primary btn-xs newCnv"><i class="fa fa-plus-circle" aria-hidden="true"></i> New</button>--}}
{{--                                                </th>--}}
{{--                                            </tr>--}}
{{--                                        </thead>--}}
{{--                                        <tbody id="cnvInfoWrap">--}}
{{--                                            @if(!empty($others_conveyance) && count($others_conveyance) > 0)--}}
{{--                                            @foreach($others_conveyance as $cnv)--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <div class="form-group">--}}
{{--                                                        <input type="text" name="conveyance_title[]" id="conveyance_title" required placeholder="Conveyance Title" class="form-control" value="{{$cnv->conveyance_title}}">--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="form-group">--}}
{{--                                                        <input type="number" required="" name="conveyance_amount[]" id="conveyance_amount" placeholder="Amount" class="form-control input_money" value="{{$cnv->conveyance_amount}}">--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="form-group">--}}
{{--                                                        <button type="button" class="btn btn-danger btn-xs deleteCnv"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}

{{--                                            @endforeach--}}
{{--                                            @else--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <input type="text" name="conveyance_title[]" id="conveyance_title" required placeholder="Conveyance Title" class="form-control" value="">--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <input type="number" name="conveyance_amount[]" id="conveyance_amount" required placeholder="Amount" class="form-control input_money" value="">--}}
{{--                                                </td>--}}
{{--                                                <td>--}}

{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            @endif--}}
{{--                                        </tbody>--}}
{{--                                    </table>--}}
{{--                                </div>--}}

{{--                                <div class="section-divider"></div>--}}

{{--                                <div class="row">--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <button class="btn btn-primary btn-lg" type="button" id="submitSalaryInfo"><i class="fa fa-check"></i>&nbsp;Submit</button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                </form>--}}
{{--                            </div>--}}
{{--                        </div>--}}


                        <!--##############################################
                                          INSURANCE INFORMATION
                         #################################################-->

                        <div class="step-header" id="headInsuranceInfo">
                            <h2  class="accordion-btn collapsed" type="button" @if(isset($employee->id) && !empty($employee->id))  data-toggle="collapse" data-target="#collapseInsurance" @endif  aria-expanded="true" aria-controls="headInsuranceInfo"> Insurance Info <span class="indector"><i class="btn-icon"></i></span></h2>
                        </div>
                        <div id="collapseInsurance" class="collapse" aria-labelledby="headInsuranceInfo" data-parent="#EmployeeAccordion">
                            <div class="step-content">
                                <div class="row">
                                    <div class="col-md-12 text-right mb-2">
                                        <button class="btn btn-success btn-xs" id="newInsurance"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                                        <button class="btn btn-danger btn-xs hide" id="deleteInsurance"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; Delete</button>
                                        <button class="btn btn-warning btn-xs hide" id="editInsurance"><i class="fa fa-edit" aria-hidden="true"></i> &nbsp; Edit</button>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Claim Type</th>
                                            <th>Claim Date</th>
                                            <th>Claim Amount</th>
                                            <th>Claim Details</th>
                                            <th>Claim Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="insuranceInfoWrap">
                                        @if(!empty($insurance_info) && count($insurance_info)>0)
                                        @foreach($insurance_info as $insurance)
                                        <tr class="insurance-select-toggle" id="{{$insurance->hr_insurane_claim_id}}">
                                            <td>{{!empty($insurance->claim_type) && $insurance->claim_details !='null'?$insurance->claim_type:'N/A'}} </td>
                                            <td>{{!empty($insurance->claim_date) && $insurance->claim_details !='null'?$insurance->claim_date:'N/A'}} </td>
                                            <td>{{!empty($insurance->claim_amount) && $insurance->claim_details !='null'?$insurance->claim_amount:'N/A'}} </td>
                                            <td>{{!empty($insurance->claim_details) && $insurance->claim_details !='null' ?$insurance->claim_details:'N/A'}} </td>
                                            <td>{{!empty($insurance->claim_status) && $insurance->claim_details !='null'?$insurance->claim_status:'N/A'}} </td>
                                        </tr>
                                        @endforeach

                                        @endif
                                    </tbody>
                                </table>
                                <div class="modal fade" id="insuranceModal" tabindex="-1" role="dialog"  aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content" id="insuranceContent">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--##############################################
                                        Emargency Contract
                       #################################################-->

                        <div class="step-header" id="emargencyContractInfo">
                            <h2 class="accordion-btn collapsed" type="button" @if(isset($employee->id) && !empty($employee->id))  data-toggle="collapse" data-target="#collapseEmargencyContract" @endif  aria-expanded="true" aria-controls="emargencyContractInfo"> Emergency Contact Info <span class="indector"><i class="btn-icon"></i></span></h2>
                        </div>
                        <div id="collapseEmargencyContract" class="collapse" aria-labelledby="emargencyContractInfo" data-parent="#EmployeeAccordion">
                            <div class="step-content">
                                <div class="row">
                                    <div class="col-md-12 text-right mb-2">
                                        <button class="btn btn-success btn-xs" id="newEmargencyContract"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                                        <button class="btn btn-danger btn-xs hide" id="deleteEmargencyContract"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; Delete</button>
                                        <button class="btn btn-warning btn-xs hide" id="editEmargencyContract"><i class="fa fa-edit" aria-hidden="true"></i> &nbsp; Edit</button>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Relationship</th>
                                            <th>Address</th>
                                            <th>Is Primary</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emargencyContractInfoWrap">
                                        @if(!empty($emargency_contract) && count($emargency_contract)>0)
                                        @foreach($emargency_contract as $info)
                                        <tr class="emargency-contract-select-toggle" id="{{$info->id}}">
                                            <td>{{!empty($info->name)?$info->name:'N/A'}} </td>
                                            <td>{{!empty($info->mobile)?$info->mobile:'N/A'}} </td>
                                            <td>{{!empty($info->relation)?$info->relation:'N/A'}} </td>
                                            <td>{{!empty($info->address)?$info->address:'N/A'}} </td>
                                            <td>{{ $info->is_primary == 1?'Primary':''}} </td>
                                        </tr>
                                        @endforeach

                                        @endif
                                    </tbody>
                                </table>
                                <div class="modal fade" id="emargencyContracModal" tabindex="-1" role="dialog"  aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content" id="emargencyContractContent">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ###############################################
                                        EDUCATIONAL INFORMATION
                          #####################################################-->

                        <div class="step-header" id="headEducational">
                            <h2 class="accordion-btn collapsed" type="button" @if(isset($employee->id) && !empty($employee->id))  data-toggle="collapse" data-target="#collapseEducational" @endif  aria-expanded="true" aria-controls="headEducational"> Education <span class="indector"><i class="btn-icon"></i></span></h2>
                        </div>
                        <div id="collapseEducational" class="collapse" aria-labelledby="headEducational" data-parent="#EmployeeAccordion">
                            <div class="step-content">
                                <div class="row">
                                    <div class="col-md-12 text-right mb-2">
                                        <button class="btn btn-success btn-xs" id="newEdu"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                                        <button class="btn btn-danger btn-xs hide" id="deleteEdu"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; Delete</button>
                                        <button class="btn btn-warning btn-xs hide" id="editEdu"><i class="fa fa-edit" aria-hidden="true"></i> &nbsp; Edit</button>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Certification</th>
                                            {{--<th>Degree</th>--}}
                                            <th>Institute</th>
                                            <th>Education Board</th>
                                            {{--<th>Education Category</th>--}}
                                            <th>Passing Year</th>
                                            <th>Fields of study</th>
                                            <th>Result Type</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                    <tbody id="eduInfoWrap">
                                        @if(!empty($education) && count($education) > 0)
                                        @foreach($education as $edu)
                                        <tr class="edu-select-toggle" id="{{$edu->hr_emp_educations_id}}">
                                            <td>{{!empty($edu->educational_qualifications_name)?$edu->educational_qualifications_name:'N/A'}}</td>
                                            {{--<td>{{!empty($edu->educational_degrees_name)?$edu->educational_degrees_name:'N/A'}}</td>--}}
                                            <td>{{!empty($edu->educational_institute_name)?$edu->educational_institute_name:'N/A'}}</td>
                                            <td>{{!empty($edu->education_board)?$edu->education_board:'N/A'}}</td>
                                            {{--<td>{{!empty($edu->education_category)?$edu->education_category:'N/A'}}</td>--}}
                                            <td>{{!empty($edu->passing_year)?$edu->passing_year:'N/A'}}</td>
                                            <td>{{!empty($edu->education_study_filed)?$edu->education_study_filed:'N/A'}}</td>
                                            <td>{{!empty($edu->result_type)?$edu->result_type:'N/A'}}</td>
                                            <td>
                                                @if(!empty($edu->result_type) && $edu->result_type =='Division')
                                                {{!empty($edu->results)?$edu->results:'N/A'}}
                                                @else
                                                {{!empty($edu->results)?$edu->results:'N/A'}} out of {{!empty($edu->outof)?$edu->outof:''}}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <!-- Education Modal -->
                                <div class="modal fade" id="eduModal" tabindex="-1" role="dialog"  aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content" id="eduContent">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- ###############################################
                                        BANK ACCOUNT INFORMATION
                          #####################################################-->

                        <div class="step-header" id="headBank">
                            <h2  class="accordion-btn collapsed" type="button"  @if(isset($employee->id) && !empty($employee->id)) data-toggle="collapse" data-target="#collapseBank" @endif aria-expanded="true" aria-controls="headBank">Bank & MFS Account <span class="indector"><i class="btn-icon"></i></span></h2>
                        </div>
                        <div id="collapseBank" class="collapse" aria-labelledby="headBank" data-parent="#EmployeeAccordion">
                            <div class="step-content">
                                <div class="row">
                                    <div class="col-md-4" style="border-right: 1px solid #e7e7e7;">
                                        <h3>MFS Account (Bkash A/C Info)</h3>
                                        <form action="#" method="post" id="mfsForm">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 font-normal"><strong>Account Name</strong> </label>
                                                        <div class="col-sm-12">
                                                            <input type="text" name="mfs_account_holder_name" placeholder="" class="form-control" value="@if(isset($mfs_info->mfs_account_name)) {{$mfs_info->mfs_account_name}}  @endif" data-error="MFS Account Holder Name Required" required/>
                                                            <div class="help-block with-errors has-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 font-normal"><strong> Account Number</strong> </label>
                                                        <div class="col-sm-12">
                                                            <input type="text" name="mfs_account_number" placeholder="" class="form-control" value="@if(isset($mfs_info->salary_account_no)) {{$mfs_info->salary_account_no}}  @endif" data-error="MFS Account Number Required" required />
                                                            <div class="help-block with-errors has-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12"> <button class="btn btn-primary btn-lg " type="button" id="addMfsAccount"><i class="fa fa-check"></i>&nbsp;Submit</button></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-8">
                                        <h3>Bank Account</h3>
                                        <div class="row">
                                            <div class="col-md-12 text-right mb-2">
                                                <button class="btn btn-success btn-xs" id="newAccInfo"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                                                <button class="btn btn-danger btn-xs hide" id="deleteAccInfo"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; Delete</button>
                                                <button class="btn btn-warning btn-xs hide" id="editAccInfo"><i class="fa fa-edit" aria-hidden="true"></i> &nbsp; Edit</button>
                                            </div>
                                        </div>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Bank</th>
                                                    <th>{{__lang('Branch')}}</th>
                                                    <th>Account Type</th>
                                                    <th>Account Number</th>
                                                    <th>Is salary disburse</th>
                                                </tr>
                                            </thead>
                                            <tbody id="accInfoWrap">

                                                @if(!empty($bankaccounts) && count($bankaccounts) > 0)
                                                @foreach($bankaccounts as $bank)
                                                <tr class="acc-select-toggle" id="{{$bank->hr_emp_bank_accounts_id}}">
                                                    <td>{{!empty($bank->bank_name)?$bank->bank_name:'N/A'}}</td>
                                                    <td>{{!empty($bank->branch_name)?$bank->branch_name:'N/A'}}</td>
                                                    <td>{{!empty($bank->bank_account_types_name)?$bank->bank_account_types_name:'N/A'}}</td>
                                                    <td>{{!empty($bank->account_number)?$bank->account_number:'N/A'}}</td>
                                                    <td>{{ isset($bank->is_active_account) && $bank->is_active_account == 1?'Yes':'No'}}</td>
                                                </tr>
                                                @endforeach
                                                @endif

                                            </tbody>
                                        </table>
                                        <!-- Education Modal -->
                                        <div class="modal fade" id="accountModal" tabindex="-1" role="dialog"  aria-hidden="true">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content" id="accountContent">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ###############################################
                                        EMPLOYMENT INFORMATION
                          #####################################################-->

                        <div class="step-header" id="headEmployment">
                            <h2 class="accordion-btn collapsed" type="button"  @if(isset($employee->id) && !empty($employee->id)) data-toggle="collapse" data-target="#collapseEmployment" @endif  aria-expanded="true" aria-controls="headEmployment">Employment History <span class="indector"><i class="btn-icon"></i></span></h2>
                        </div>
                        <div id="collapseEmployment" class="collapse" aria-labelledby="headEmployment" data-parent="#EmployeeAccordion">
                            <div class="step-content">
                                <div class="row">
                                    <div class="col-md-12 text-right mb-2">
                                        <button class="btn btn-success btn-xs" id="newEmploymentInfo"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                                        <button class="btn btn-danger btn-xs hide" id="deleteEmploymentInfo"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; Delete</button>
                                        <button class="btn btn-warning btn-xs hide" id="editEmploymentInfo"><i class="fa fa-edit" aria-hidden="true"></i> &nbsp; Edit</button>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Organization Name</th>
                                            <th>House</th>
                                            <th>Designation</th>
                                            <th>Duration</th>
                                            <th>Responsibility</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employmentInfoWrap">
                                        @if(!empty($emp_professions) && count($emp_professions) > 0)
                                        @foreach($emp_professions as $emp)
                                        <tr class="emp-select-toggle" id="{{$emp->hr_emp_professions_id}}">
                                            <td>{{!empty($emp->organization_name)?$emp->organization_name:'N/A'}}</td>
                                            <td>{{!empty($emp->company_name)?$emp->company_name:'N/A'}}</td>
                                            <td>{{!empty($emp->designation_name)?$emp->designation_name:'N/A'}}</td>
                                            {{--<td>{{$emp->from_date}}</td>--}}
                                            <td>{{!empty($emp->from_date)?toDated($emp->from_date):'N/A' }} to {{!empty($emp->is_continue)? ' Continue ' : (!empty($emp->from_date)?toDated($emp->from_date):'N/A') }}</td>
                                            <td>{{!empty($emp->responsibilities)?$emp->responsibilities:'N/A'}}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <!-- Employment Modal -->
                                <div class="modal fade" id="employmentModal" tabindex="-1" role="dialog"  aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content" id="empContent">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            @endif
                    </div>
                    </section>

                    <style>
                        .multiselect{
                            width: 100% !important;
                        }
                    </style>
                    <script>



    /*
       * Accountas Section row select and delete row or Edit item
       --------------------------------------------------------------------*/
    var select_nominee = [];

    $( document ).ready(function() {

        var designation_id = $('#designations_id').val();
        if(designation_id == 151) {

            $('#bat_route_id').val(@json($emp_ss_id));
        }


        // $('#id_div').hide();
        nid_show_hide();
    });
    $(document).on('change', '#id_type', function(e){

        nid_show_hide();
    });

    function nid_show_hide(){
        if($('#id_type').val().length > 0){
            $('#id_div').show();
            if($('#id_type').val() == 'NID'){
                $('.label-title').html('NID Number');
            }
            else if($('#id_type').val() == 'PASSPORT'){
                $('.label-title').html('Passport Number');
            }
            else if($('#id_type').val() == 'DRIVING LICENSE'){
                $('.label-title').html('Driving License Number');
            }
            else{
                $('.label-title').html('');
            }
        }
        else{
            $('#id_div').hide();
        }
    }

    $(document).on('change', '#designations_id, #bat_distributor_point', function(e){
        // alert($('#bat_distributor_point').val());
        var designation_id = $('#designations_id').val();

        if(designation_id == 151 || designation_id == 152){
            // alert($('#bat_distributor_point').val().length);
            if($('#bat_distributor_point').val() > 0){

                if(designation_id == 151){
                    $('#bat_route_id').attr({"multiple": 'Multipe'});
                }
                else{
                    $('#bat_route_id').removeAttr("multiple");
                }

                var bat_distributor_point = $('#bat_distributor_point').val();
                var url = "{{route('find-route-for-ss-sr')}}/"+bat_distributor_point+'/'+designation_id;
                // alert(url);
                makeAjax(url,null).done(function(response){
                    if(response){
                        // console.log(response);
                        var point_option = '';
                        if(designation_id == 152) {
                            point_option += '<option value="">--Select Route --</option>';
                        }
                        $.each( response, function( key, value ) {
                            point_option += '<option value="'+value.number+'">'+value.number+'</option>';
                        });
                        $('#bat_route_id').html(point_option);

                        // $('.route_div').show();
                    }
                    $('#bat_route_id').multiselect('rebuild');
                });
            }
            else{
                swalError("Please Select Distributor Point.");
            }
        }
        else{
            // alert('--');
            var point_option = "<option value=''>No data found!</option>";
            $('#bat_route_id').html(point_option);
            $('#bat_route_id').multiselect('rebuild');
            // $('.route_div').hide();
        }
    });

    $(document).on('click', '.nominee-select-toggle', function(e){
        var self = $(this);
        var id = self.attr('id');
        if ($(this).toggleClass('selected')) {
            if ($(this).hasClass('selected')) {
                select_nominee.push(id);
            } else {
                select_nominee.splice(select_nominee.indexOf(id), 1);
            }

            var acc_length = select_nominee.length;

            if (acc_length == 1) {
                $('#editNomineeInfo').show();
            }
            else {
                $('#editNomineeInfo').hide();
            }
            if (acc_length > 0) {
                $('#deleteNomineeInfo').show();
            }
            else{
                $('#deleteNomineeInfo').hide();
            }
        }
    });

    /*
     * New Nominee Modal Open
     -------------------------------------*/
    $('#newNomineeInfo').click(function () {
        var url = '<?php echo URL::to('hr-get-nominee-form'); ?>';
        var data = {};
        makeAjaxPostText(data, url).done(function(response){
            if(response){
                $('#medium_modal .modal-content').html(response);
            }
            $('#medium_modal').modal('show');
        });
    });

    /*
    * nominee realtionship multiselect
    */
    $('#nominee_relationship').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 250
    });

    /*
    * save nominee info
    * */
    $(document).on('click', '#addNominee', function (event) {

        if (!$('#nomineeForm').validator('validate').has('.has-error').length) {

            if (employeeId !='null'){

                var url = '{{route('hr-submit-nominee-info')}}';
                var $form = $('#nomineeForm');
                var data = {
                    'sys_users_id' : employeeId
                };
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if(response.success){
                        @if(isset($nominees->hr_emp_nominees_id) && !empty($nominees->hr_emp_nominees_id))
                        swalSuccess('Nominee Information Updated Successfully.');
                        @else
                        swalSuccess('Nominee Information Added Successfully.');
                        @endif
                    }
                    // if (response.success && response.data.length > 0){
                    //     var table = '';
                    //     $.each( response.data, function( key, value ) {
                    //         table += '<tr class="nominee-select-toggle" id="'+value.hr_emp_nominees_id+'"><td>'+value.nominee_name+'</td><td>'+value.nominee_relationship+'</td><td class="nominee_ratio" data-id="'+value.hr_emp_nominees_id+'" data-ratio="'+value.nominee_ratio+'">'+value.nominee_ratio+' %</td></tr>';
                    //     });
                    //     $('#nomineeInfoWrap').html(table);
                    // }
                    // $('#nomineeForm').trigger("reset");
                    // $('#medium_modal').modal('hide');
                    // $('#editNomineeInfo').hide();
                    // $('#deleteNomineeInfo').hide();
                    // select_nominee =[];
                });
            }else{
                swalError("Sorry! you need to add personal information first");
            }

        }

    });

    /*
     * Edit Accounts Information
     -------------------------------------*/
    $('#editNomineeInfo').click(function () {
        if (select_nominee.length == 1){
            var url = '<?php echo URL::to('hr-get-nominee-form'); ?>';
            var data = {'id': select_nominee[0]};
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#medium_modal .modal-content').html(response);
                }
                $('#medium_modal').modal('show');
            });
        } else {
            swalError('Please select Nominee item row first')
        }
    });

    $(document).on('click', '#deleteNomineeInfo', function (event) {
        if (select_nominee.length > 0){
            swalConfirm('to delete this items').then(function (s) {
                if (s.value){
                    var url = '{{route('hr-delete-nominee-info')}}';
                    var data = {
                        'ids' : select_nominee
                    };
                    makeAjaxPost(data, url).done(function (response) {
                        if (response.success){
                            $.each( response.data, function( key, value ) {
                                $('#nomineeInfoWrap tr#'+value).remove();
                            });
                        }
                        $('#deleteNomineeInfo').hide();
                        $('#editNomineeInfo').hide();
                        select_nominee =[];
                    });
                }else{
                    $('#deleteNomineeInfo').hide();
                    $('#editNomineeInfo').hide();
                    select_nominee =[];
                }
            });
        }else{
            swalError("Sorry! Please Select Nominee Item");
        }
    });


    /*$(function ($) {
        $("form").on("input", ".sub_salary", function () {
            var min_medical = parseFloat($("#min_medical").val());
            var min_food = parseFloat($("#min_food").val());
            var min_tada = parseFloat($("#min_tada").val());
            var house_rent = parseFloat($("#house_rent").val());
            var old_gross = parseFloat($("#min_gross").data('old_gross'));
            var convince_bill = parseFloat(min_food+min_medical+min_tada);
            var min_gross = $('#min_gross').val();
            // var min_gross = parseFloat(basic_salary+house_rent_amount+min_food+min_medical+min_tada);
            var basic_salary = parseFloat((min_gross - convince_bill)/(1.5));
            var house_rent_amount = parseFloat(basic_salary*parseFloat(house_rent/100));
            $('#basic_salary').val(basic_salary.toFixed(2));
            $('#house_rent_amount').val(house_rent_amount.toFixed(2));
        });
    });*/

    /*$(function ($) {
        $("form").on("input", ".basic_salary, .house_rent, .house_rent_amount", function () {
            var basic_salary = parseFloat($("#basic_salary").val());
            if($(this).hasClass('house_rent_amount')){
                var house_rent_amount = parseFloat($("#house_rent_amount").val());
                var house_rent = ((house_rent_amount/basic_salary)*100);
                $("#house_rent").val(house_rent.toFixed(2) == 'NaN' ? '0' : house_rent.toFixed(2));
            }else{
                var house_rent = parseFloat($("#house_rent").val());
                var house_rent_amount = ((house_rent * basic_salary)/100);
                $("#house_rent_amount").val(house_rent_amount.toFixed(2) == 'NaN' ? '0' : house_rent_amount.toFixed(2));
            }
            var basic_salary = parseFloat($("#basic_salary").val());
            var house_rent_amount = parseFloat($("#house_rent_amount").val());
            var min_medical = parseFloat($("#min_medical").val());
            var min_food = parseFloat($("#min_food").val());
            var min_tada = parseFloat($("#min_tada").val());

            var min_gross = parseFloat(basic_salary+house_rent_amount+min_food+min_medical+min_tada);
            $("#min_gross").val(house_rent.toFixed(2) == 'NaN' ? '0' : min_gross.toFixed(2));
        });
    });*/

    /*(function ($) {
        $("form").on("input", "#min_gross", function () {
            var salary_grade = $("#hr_emp_grades_id").val();
            if(salary_grade) {
                var min_medical = parseFloat($("#min_medical").val());
                var min_food = parseFloat($("#min_food").val());
                var min_tada = parseFloat($("#min_tada").val());
                var min_gross = parseFloat($("#min_gross").val());
                var convince = parseFloat(min_medical+min_food+min_tada);
                var basic = parseFloat((min_gross-convince)/(1.5));
                var house_rent = parseFloat($("#house_rent").val());
                var house_rent_amount = parseFloat((house_rent*basic)/100);
                $("#basic_salary").val((basic).toFixed(2) == 'NaN' ? '0' : (basic).toFixed(2));
                $("#house_rent_amount").val((house_rent_amount).toFixed(2) == 'NaN' ? '0' : (house_rent_amount).toFixed(2));
            }else{
                swalWarning('Please select Salary Grade.');
            }
        });
    })(jQuery);*/

    /*
        Nominee Ratio calculate limit
    */
    (function ($) {
        $(document).on('change', '.nominee_ratiofield', function () {
            var edit_id = $('#empnominee_id').val()||0;
            var nval = parseInt($(this).val());
            var existval = 0;

            $('.nominee_ratio').each(function () {
                if(edit_id != $(this).data('id')){
                    existval += parseInt($(this).data('ratio'));
                }
            });

            var total = parseInt(existval + nval);
           // alert(total);
            if (total>100){
                swalError("sorry! you can't share ratio greater then 100%");
                $(this).val('');
            }
        });
    })(jQuery);

    //Onchange Department load Section
    (function ($) {
        $(document).on('change', '#bat_company_id', function(e) {
            var bat_company_id = $(this).val();
            var url = "{{route('dp-by-dh')}}/"+bat_company_id;
            makeAjax(url,null).done(function(response){
                if(response){
                    var point_option = '<option value="">--Select Point --</option>';
                    $.each( response.data, function( key, value ) {
                        point_option += '<option value="'+value.id+'">'+value.name+'</option>';
                    });
                    $('#bat_distributor_point').html(point_option);
                }
                $('#bat_distributor_point').multiselect('rebuild');
            });
            $('#basicForm').validator('update');
        });
    })(jQuery);

    (function ($) {
        //Change Working Shift Dropdown
        $(document).on('change', 'input[name="is_roaster"]', function (e) {
            var is_roster = $(this).val();
            var data = {is_rotable: is_roster};
            var url = "{{route('working-shifts-by-roaster-type')}}";

            makeAjaxPost(data, url).done(function (response) {
                if (response) {
                    var section_option = '<option value="">-- Select Working Shift  --</option>';
                    $.each(response.data, function (key, value) {
                        section_option += '<option value="' + value.hr_working_shifts_id + '">' + value.shift_name + '</option>';
                    });
                    $('#hr_working_shifts_id').html(section_option);
                }
                $('#hr_working_shifts_id').multiselect('rebuild');
            });

            //action for Attendance apply
            if (is_roster == 1) {
                $('#attendance_apply').prop("checked", false).prop('disabled', true);
            } else {
                $('#attendance_apply').prop('disabled', false);
            }
            $('#start_time').val('');
            $('#end_time').val('');
        })

    })(jQuery);

</script>
