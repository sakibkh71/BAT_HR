@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{$pagetitle}}</h2>
                    </div>
                    <div class="ibox-content pad10">
                        <form action="{{route('store-employee-section-store', isset($section->hr_emp_sections_id)?$section->hr_emp_sections_id:'')}}" method="post" id="sectionForm"  data-toggle="validator" data-disable="false">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Working Section Name <span class="required">*</span></label>
                                        <div class="input-group">
                                            <input type="text" required name="hr_emp_section_name" id="hr_emp_section_name" class="form-control" value="{{isset($section->hr_emp_section_name)?$section->hr_emp_section_name:''}}">
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Department <span class="required">*</span></label>
                                        {{__combo('departments',array('selected_value'=>isset($section->departments_id)?$section->departments_id:'','attributes'=>array('class'=>'form-control multi','required'=>'required')))}}
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <div class="input-group">
                                            <textarea class="form-control" name="description" id="description">{{isset($section->description)?$section->description:''}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-control multi" name="status" required>
                                            <option value="">--Select Status--</option>
                                            <option value="Active" {{isset($section->status) && $section->status =="Active" ? 'selected' : ''}}>Active</option>
                                            <option value="Inactive" {{isset($section->status) && $section->status =="Inactive" ? 'selected' : ''}}>Inactive</option>
                                        </select>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label> <br>
                                    <button type="submit" class="btn btn-primary btn-lg">{{isset($data->hr_emp_sections_id)?'Update':'Save'}} </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Scripts -->
    <script>
        (function ($) {
            $(document).ready(function(){
                $('.summernote').summernote();
            });
        }(jQuery));
    </script>

@endsection
