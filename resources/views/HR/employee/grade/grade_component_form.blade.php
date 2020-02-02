<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">@if(isset($compoment_info)) Edit Component @else Component Entry @endif - {{$hr_emp_grade_name}}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <form action="{{route('store-grade-info')}}" method="post" id="basicFormComponent" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-normal"><strong>Grade Name</strong> <span class="required">*</span></label>
                                <div class="form-group">
                                    <input type="text" name="hr_emp_grade_name" placeholder="Component Name" class="form-control" value="{{ !empty($hr_emp_grade_name)?$hr_emp_grade_name:old('hr_emp_grade_name')}}"  data-error="Component Name  is required" required="" readonly="">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="font-normal"><strong>Component Name</strong> <span class="required">*</span></label>
                                <div class="form-group">
                                    <input type="text" name="component_name" placeholder="Component Name" class="form-control" value="{{ !empty($compoment_info->component_name)?$compoment_info->component_name:old('component_name')}}"  data-error="Component Name  is required" required="" autocomplete="off">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="font-normal"><strong>Component Type</strong> <span class="required">*</span></label>
                                <div class="form-group">
                                    {{getEnumOptions(array('table'=>'hr_grade_components','selected_value'=>isset($compoment_info->component_type)?$compoment_info->component_type: '', 'field'=>'component_type','id'=>'component_type','name'=>'component_type'))}}
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 col-md-2">
                                <div class="form-group tooltip-demo mt-4">
                                    <label for="is_ratio"><input class="float-left mt-1 mr-1" type="checkbox" id="is_ratio" name="is_ratio" value="1" @if( isset($compoment_info->ratio_of_basic) && $compoment_info->ratio_of_basic > 0) checked @endif >Ratio?</label>
                                    <a data-toggle="tooltip" data-placement="top" title="Are you sure to  apply ratio of basic salary?"><i class="fa fa-info-circle"></i></a>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3" id = "ratio_of_basic_div" @if( isset($compoment_info->ratio_of_basic) && $compoment_info->ratio_of_basic > 0) style="display: block" @else  style="display: none;" @endif>
                                <label class="font-normal"><strong>Ratio of Basic</strong> <span class="required">*</span></label>
                                <div class="form-group">
                                    <input type="text" name="ratio_of_basic" id = "ratio_of_basic" placeholder="Ratio of Basic" class="form-control input_money" value="{{ !empty($compoment_info->ratio_of_basic)?$compoment_info->ratio_of_basic:old('ratio_of_basic')}}"  data-error="Ratio of Basic  is required" required="" autocomplete="off">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="font-normal"><strong>Amount</strong> <span class="required">*</span></label>
                                <div class="form-group">
                                    <input type="text" name="amount" id="amount" placeholder="Amount" class="form-control input_money" @if(isset($compoment_info->component_type)&&($compoment_info->component_type) == 'Deduction') value="{{ !empty($compoment_info->deduction_amount)?$compoment_info->deduction_amount:old('amount')}}" @else value="{{ !empty($compoment_info->addition_amount)?$compoment_info->addition_amount:old('amount')}}" @endif  data-error="Amount  is required" required="" autocomplete="off">
                                    <div class="help-block with-errors has-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group tooltip-demo mt-4">
                                    <label for="auto_applicable"><input class="float-left mt-1 mr-1" type="checkbox" id="auto_applicable" name="auto_applicable" value="1" {{ !empty($compoment_info->auto_applicable) && $compoment_info->auto_applicable == 'YES'?'checked':''}}>Auto Applicable ?</label>
                                    <a data-toggle="tooltip" data-placement="top" title="Are you sure to  apply auto applicable?"><i class="fa fa-info-circle"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Component Note</label>
                                    <div class="input-group">
                                        <textarea class="form-control" name="component_note" rows="2">{{isset($compoment_info->component_note)?$compoment_info->component_note:old('component_note')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="hr_grade_components_id" value="{{ isset($compoment_info->hr_grade_components_id)?$compoment_info->hr_grade_components_id:old('hr_grade_components_id')}}">
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="grade_component_submit" class="btn btn-primary">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>
