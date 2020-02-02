<style>
    .hr-out-of{
        padding: 10px 0 0 0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
    }
</style>

<div class="modal-header">
    <h4 class="modal-title text-left">Insurance Info Entry</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>

<div class="modal-body">
    <form action="#" method="post" id="insuranceForm">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Claim Type</strong></label>
                    <div class="col-sm-12">
                        <select class="form-control " name="claim_type" id="claim_type" style="width:100%;" data-error="Please Select Claim Type" required>
                            <option value="">--Select an Option--</option>
                            <option value="Life" @if(isset($insurance)) @if($insurance->claim_type=='Life') selected @endif @endif>Life</option>
                            <option value="Property"  @if(isset($insurance)) @if($insurance->claim_type=='Property') selected @endif @endif>Property</option>
                            <option value="Marine" @if(isset($insurance)) @if($insurance->claim_type=='Marine') selected @endif @endif>Marine</option>
                            <option value="Fire" @if(isset($insurance)) @if($insurance->claim_type=='Fire') selected @endif @endif>Fire</option>
                            <option value="Liability" @if(isset($insurance)) @if($insurance->claim_type=='Liability') selected @endif @endif>Liability</option>
                            <option value="Guarantee" @if(isset($insurance)) @if($insurance->claim_type=='Guarantee') selected @endif @endif>Guarantee</option>
                            <option value="Social" @if(isset($insurance)) @if($insurance->claim_type=='Social') selected @endif @endif>Social</option>
                        </select>
                    </div>
                    &nbsp;&nbsp; <div class=" help-block with-errors has-feedback"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Claim Date</strong></label>
                    <div class="input-group  date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="claim_date" id="claim_date" class="form-control" data-error="Please select claim date" value="{{ !empty($insurance->claim_date)?$insurance->claim_date:old('claim_date')}}" autocomplete="off" required>
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Claim Amount</strong></label>
                    <div class="col-sm-12">
                        <input type="number" name="claim_amount" placeholder="claim_amount" class="form-control" value="{{!empty($insurance->claim_amount)?$insurance->claim_amount:old('claim_amount')}}" data-error="Claim amount required" required />
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Claim Status</strong></label>
                    <div class="col-sm-12">
                        <select class="form-control " name="claim_status" id="claim_status" style="width:100%;" data-error="Please Select Claim Status" required>
                            <option value="" >--Select an Option--</option>
                            <option value="Pending" @if(isset($insurance)) @if($insurance->claim_status=='Pending') selected @endif @endif>Pending</option>
                            <option value="Paid" @if(isset($insurance)) @if($insurance->claim_status=='Paid') selected @endif @endif>Paid</option>
                            <option value="Pending" @if(isset($insurance)) @if($insurance->claim_status=='Pending') selected @endif @endif>Pending</option>
                            <option value="Rejected" @if(isset($insurance)) @if($insurance->claim_status=='Rejected') selected @endif @endif>Rejected</option>

                        </select>
                    </div>
                    &nbsp;&nbsp; <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Claim Details</strong></label>
                    <div class="col-sm-12">
                      <textarea class="form-control "  name="claim_details" id="claim_details" value="{{!empty($insurance->claim_details)?$insurance->claim_details:old('claim_amount')}}" data-error="Claim Details Required" required></textarea>
                    </div>
                    &nbsp;&nbsp;  <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>
        @if(isset($insurance->hr_insurane_claim_id) && !empty($insurance->hr_insurane_claim_id))
            <input type="hidden" name="insurance_id" value="{{$insurance->hr_insurane_claim_id}}">
        @endif

    </form>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="addInsurance">Save</button>
</div>
