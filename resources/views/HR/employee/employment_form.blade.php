<div class="modal-header">
    <h4 class="modal-title text-left">Employment History</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>
<div class="modal-body">
    <form action="#" method="post" id="employmentForm">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Organization Name</strong> </label>
                    <div class="col-sm-12">
                        <input type="text" name="organization_name" placeholder="Organization Name" class="form-control" value="{{ !empty($emp->organization_name)?$emp->organization_name:old('organization_name')}}">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Company</strong> </label>
                    <div class="col-sm-12">
                        {{__combo('bat_company', array( 'selected_value'=> !empty($emp->bat_company_id)?$emp->bat_company_id:old('designation_name'),'attributes'=> array( 'name'=>'previous_company_name', 'placeholder'=>'Select Company', 'id'=>'previous_company_name', 'class'=>'form-control multi')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Designation</strong> </label>
                    <div class="col-sm-12">
                        {{__combo('hr_employee_designation', array('selected_value'=> !empty($emp->designation_name)?$emp->designation_name:old('designation_name'), 'attributes'=> array( 'name'=>'designation_name', 'placeholder'=>'Select Designation', 'id'=>'designation_name', 'class'=>'form-control multi')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            {{--<div class="col-md-4">--}}
                {{--<div class="form-group row">--}}
                    {{--<label class="col-sm-12 font-normal"><strong>Department Name</strong> --}}{{--<span class="required">*</span>--}}{{--</label>--}}
                    {{--<div class="col-sm-12">--}}
                        {{--{{__combo('departments_name', array('selected_value'=> !empty($emp->department_name)?$emp->department_name:old('department_name'), 'attributes'=> array( 'name'=>'department_name', 'placeholder'=>'Select Designation', 'id'=>'department_name', 'class'=>'form-control multi')))}}--}}
                        {{--<div class="help-block with-errors has-feedback"></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-normal"><strong>From Date </strong> </label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="from_date" class="form-control" data-error="Please select Birth Date" value="{{ !empty($emp->from_date)?$emp->from_date:old('from_date')}}" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-normal"><strong>End Date </strong></label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="to_date" class="form-control" id="to_date" value="{{ !empty($emp->to_date) && $emp->to_date !='0000-00-00'?$emp->to_date:old('to_date')}}" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group pt-2 mt-4">
                    <div class="text-left">
                        <input class="custom-check" type="checkbox" tabindex="3" value="1" name="is_continue" id="is_continue" {{ !empty($emp->is_continue)?'checked':''}}>
                        <label for="is_continue">Continue</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Responsibilities</strong> {{--<span class="required">*</span>--}}</label>
                    <div class="col-sm-12">
                        <textarea name="responsibilities" id="responsibilities" rows="4" class="form-control" placeholder="Responsibilities">{{ !empty($emp->responsibilities)?$emp->responsibilities:old('responsibilities')}}</textarea>
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($emp->hr_emp_professions_id) && !empty($emp->hr_emp_professions_id))
            <input type="hidden" name="hr_emp_professions_id" value="{{$emp->hr_emp_professions_id}}">
        @endif
    </form>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="addProfession">Save</button>
</div>
<script>
    $('#department_name, #designation_name,#previous_company_name').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 250,
        onDropdownShown: function(even) {
            this.$filter.find('.multiselect-search').focus();
        },
    });
</script>