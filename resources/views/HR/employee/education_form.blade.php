<style>
    .hr-out-of{
        padding: 10px 0 0 0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title text-left">Educational Info Entry</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>
<div class="modal-body">
    <form action="#" method="post" id="educationalForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Certification Name </strong></label>
                    <div class="col-sm-12">
                        {{__combo('educational_qualifications', array('selected_value'=> !empty($edu->educational_qualifications_name)?$edu->educational_qualifications_name:old('educational_qualifications_name'), 'attributes'=> array( 'name'=>'educational_qualifications_name', 'id'=>'educational_qualifications_name', 'class'=>'form-control')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            {{--<div class="col-md-6">--}}
                {{--<div class="form-group row" id="degreeRow" style="display: none;">--}}
                    {{--<label class="col-sm-12 font-normal"><strong>Degree Name </strong>--}}{{-- <span class="required">*</span>--}}{{--</label>--}}
                    {{--<div class="col-sm-12">--}}
                        {{--<select name="educational_degrees_name" --}}{{--required="true" --}}{{-- id="educational_degrees_name" class="form-control">--}}
                            {{--<option value="">Select Degree </option>--}}
                            {{--@if(!empty($edu->educational_degrees_name))--}}
                                {{--<option value="{{$edu->educational_degrees_name}}" selected>{{$edu->educational_degrees_name}}</option>--}}
                            {{--@endif--}}
                        {{--</select>--}}
                        {{--<div class="help-block with-errors has-feedback"></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Institute Name </strong> </label>
                    <div class="col-sm-12">
                        <input type="text" name="educational_institute_name" placeholder="Institute Name" class="form-control" value="{{ !empty($edu->educational_institute_name)?$edu->educational_institute_name:old('educational_institute_name')}}"  data-error="Educational Institute_name is required">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Education Board </strong></label>
                    <div class="col-sm-12">
                        {{__combo('hr_education_board', array('selected_value'=> !empty($edu->education_board)?$edu->education_board:old('education_board'), 'attributes'=> array( 'name'=>'education_board', 'id'=>'education_board', 'class'=>'form-control')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-normal"><strong>Passing Year </strong> </label>
                    <div class="input-group year-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="passing_year" id="passing_year" class="form-control" data-error="Please select passing year" value="{{ !empty($edu->passing_year)?$edu->passing_year:old('passing_year')}}" autocomplete="off">
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>
        {{--<div class="row">--}}
            {{--<div class="col-md-6">--}}
                {{--<div class="form-group row">--}}
                    {{--<label class="col-sm-12 font-normal"><strong>Education Category</strong></label>--}}
                    {{--<div class="col-sm-12">--}}
                        {{--{{__combo('hr_education_category', array('selected_value'=> !empty($edu->education_category)?$edu->education_category:old('education_category'), 'attributes'=> array( 'name'=>'education_category',  'id'=>'education_category', 'class'=>'form-control')))}}--}}
                        {{--<div class="help-block with-errors has-feedback"></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

        {{--</div>--}}
        <div class="row">
            <div class="col-md-5">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Fields of study</strong></label>
                    <div class="col-sm-12">
                        {{__combo('hr_education_study_filed', array('selected_value'=> !empty($edu->education_study_filed)?$edu->education_study_filed:old('education_study_filed'), 'attributes'=> array( 'name'=>'education_study_filed', 'id'=>'education_study_filed', 'class'=>'form-control')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label class="font-normal"><strong>Result Type</strong> </label>
                        <select name="result_type" id="result_type" class="form-control">
                            <option value="">Result Type</option>
                            <option value="Division" {{!empty($edu->result_type)&&$edu->result_type =='Division'?'selected':''}}>Division</option>
                            <option value="Grade" {{!empty($edu->result_type)&&$edu->result_type =='Grade'?'selected':''}}>Grade</option>
                        </select>
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                    <div class="col-sm-8 pl-0">
                        <label class="font-normal"><strong>Result</strong> </label>
                        <select name="results_division" id="results_division" class="form-control" @if(isset($edu->result_type) && $edu->result_type == 'Grade') style="display: none;"  @endif>
                            <option value="">Select Result</option>
                            <option value="1st" {{!empty($edu->results)&& $edu->results =='1st'?'selected':''}}>1st</option>
                            <option value="2nd" {{!empty($edu->results)&& $edu->results =='2nd'?'selected':''}}>2nd</option>
                            <option value="3rd" {{!empty($edu->results)&& $edu->results =='3rd'?'selected':''}}>3rd</option>
                        </select>
                        <div id="grade_row" @if(isset($edu->result_type) && $edu->result_type == 'Grade') style="display: block;"  @else style="display: none;" @endif >
                            <div class="row">
                                <div class="col-sm-4 pr-0">
                                    <input type="text" name="results" id="results" placeholder="CGPA" class="form-control" value="{{ !empty($edu->results)?$edu->results:old('results')}}">
                                </div>
                                <div class="col-sm-3 hr-out-of">OUT OF</div>
                                <div class="col-sm-5 pl-0">
                                        <select name="outof" id="outof" class="form-control">
                                        <option value="">select</option>
                                        <option value="4" {{!empty($edu->outof)&& $edu->outof =='4'?'selected':''}}>4</option>
                                        <option value="5" {{!empty($edu->outof)&& $edu->outof =='5'?'selected':''}}>5</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($edu->hr_emp_educations_id) && !empty($edu->hr_emp_educations_id))
            <input type="hidden" name="hr_emp_educations_id" value="{{$edu->hr_emp_educations_id}}">
        @endif
    </form>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="addEducation">Save</button>
</div>


<script>
    $('#educational_qualifications_name, #education_board, #education_category,#educational_degrees_name, #education_study_filed').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 250,
        onDropdownShown: function(even) {
            this.$filter.find('.multiselect-search').focus();
        },
    });
</script>