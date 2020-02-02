<div class="modal-header">

    <h4 class="modal-title text-left inline">{{isset($nominee->hr_emp_educations_id)?"Edit":"New"}}  Nominee Info</h4>

    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>
<div class="modal-body">
    <form action="#" method="post" id="nomineeForm">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group row">
                    <label class="col-sm-12 form-label">Nominee Name <span class="required">*</span></label>
                    <div class="col-sm-12">
                        <input type="text" name="nominee_name" placeholder="Nominee Name" class="form-control" value="{{ !empty($nominee->nominee_name)?$nominee->nominee_name:old('nominee_name')}}"  data-error="Nominee Name is required" required="">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label class="col-sm-12 form-label">Nominee Relationship <span class="required">*</span></label>
                    <div class="col-sm-12">
                        {{__combo('nominees_relationships',array('selected_value'=> !empty($nominee->nominee_relationship)?$nominee->nominee_relationship:"", 'attributes'=> array('class'=>'form-control multi','id'=>'nominee_relationship','name'=>'nominee_relationship')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-sm-12 form-label">Nominee Ratio <span class="required">*</span></label>
                    <div class="input-group col-sm-12">
                        <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                        <select name="nominee_ratio" id="nominee_ratio" class="form-control text-left nominee_ratiofield" required>
                            <option value="">Select Nominee Ratio</option>
                            <option value="25" @if(!empty($nominee->nominee_ratio) && $nominee->nominee_ratio == 25) selected @endif>25%</option>
                            <option value="50" @if(!empty($nominee->nominee_ratio) && $nominee->nominee_ratio == 50) selected @endif>50%</option>
                            <option value="75" @if(!empty($nominee->nominee_ratio) && $nominee->nominee_ratio == 75) selected @endif>75%</option>
                            <option value="100" @if(!empty($nominee->nominee_ratio) && $nominee->nominee_ratio ==100) selected @endif>100%</option>
                        </select>
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>
        @if(isset($nominee->hr_emp_nominees_id) && !empty($nominee->hr_emp_nominees_id))
            <input type="hidden" id="empnominee_id" name="hr_emp_nominees_id" value="{{$nominee->hr_emp_nominees_id}}">
        @endif
    </form>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="addNominee">Save</button>
</div>

<script>
$('#nominee_relationship').multiselect({
includeSelectAllOption: true,
enableFiltering: true,
enableCaseInsensitiveFiltering: true,
maxHeight: 250,
onDropdownShown: function(even) {
    this.$filter.find('.multiselect-search').focus();
},
});
</script>