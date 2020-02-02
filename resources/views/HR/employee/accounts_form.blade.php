<div class="modal-header">
    <h4 class="modal-title text-left">{{isset($acc->hr_emp_bank_accounts_id)?"Edit":"New"}}  Account Info</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>
<div class="modal-body">
    <form action="#" method="post" id="accountForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Bank Name </strong> <span class="required">*</span></label>
                    <div class="col-sm-12 position-relative">
                        {{__combo('bank', array('selected_value'=> !empty($acc->banks_id)?$acc->banks_id:old('banks_id'), 'attributes'=> array( 'name'=>'banks_id', 'required'=>'required', 'id'=>'banks_id', 'class'=>'form-control multi')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Branch Name </strong> <span class="required">*</span></label>
                    <div class="col-sm-12">
                        <select name="branch_name" required="true" id="branch_name" class="form-control multi">
                            <option value="">Select Branch</option>
                            @if(!empty($acc->branch_name))
                                <option value="{{$acc->branch_name}}" selected>{{$acc->branch_name}}</option>
                            @endif
                        </select>
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Account Type </strong> <span class="required">*</span></label>
                    <div class="col-sm-12">
                        {{__combo('bank_account_types', array('selected_value'=> !empty($acc->bank_account_types_id)?$acc->bank_account_types_id:old('bank_account_types_id'), 'attributes'=> array( 'name'=>'bank_account_types_id', 'required'=>'required', 'id'=>'bank_account_types_id', 'class'=>'form-control multi')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <label for="is_active_account"><input type="checkbox" id="is_active_account" name="is_active_account" class="pull-left mt-1 mr-1" value="1" @if(isset($acc->is_active_account) && $acc->is_active_account == 1) checked @endif> is salary disburse  <i class="fa  fa-info-circle" data-toggle="tooltip" title="if you check it Salary will disburse this account"  data-placement="right"></i></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Account Number </strong> <span class="required">*</span></label>
                    <div class="col-sm-12">
                        <input type="text" name="account_number" placeholder="Account Number" class="form-control" value="{{ !empty($acc->account_number)?$acc->account_number:old('account_number')}}"  data-error="Account Number" required="">
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>

            </div>
        </div>
        @if(isset($acc->hr_emp_bank_accounts_id) && !empty($acc->hr_emp_bank_accounts_id))
            <input type="hidden" name="hr_emp_bank_accounts_id" value="{{$acc->hr_emp_bank_accounts_id}}">
        @endif
    </form>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="addAccount">Save</button>
</div>
<script>
    $('#banks_id').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 350,
        onDropdownShown: function(even) {
            this.$filter.find('.multiselect-search').focus();
        },
    });
    $('#branch_name').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 350,
        onDropdownShown: function(even) {
            this.$filter.find('.multiselect-search').focus();
        },
    });
    $('#bank_account_types_id').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 350,
        onDropdownShown: function(even) {
            this.$filter.find('.multiselect-search').focus();
        },
    });
</script>