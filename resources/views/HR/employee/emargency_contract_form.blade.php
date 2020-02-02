<style>
    .hr-out-of{
        padding: 10px 0 0 0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
    }
</style>

<div class="modal-header">
    <h4 class="modal-title text-left">Emergency Contact Info Entry</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>

<div class="modal-body">
    <form action="#" method="post" id="emargencyContractForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Name</strong></label>
                    <div class="col-sm-12">
                        <input type="text" name="emg_con_name" placeholder="Name" class="form-control" value="{{!empty($info->name)?$info->name:old('emg_con_name')}}" data-error="Contract Name Required" required />
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Mobile Number</strong></label>
                    <div class="col-sm-12">
                        <input type="text" name="emg_con_mobile" placeholder="Mobile Number" class="form-control" value="{{!empty($info->mobile)?$info->mobile:old('emg_con_mobile')}}" data-error="Contract Mobile Number Required" required />
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 form-label">Relationship <span class="required">*</span></label>
                    <div class="col-sm-12">
                        {{__combo('nominees_relationships',array('selected_value'=> !empty($info->relation)?$info->relation:"", 'attributes'=> array('class'=>'form-control multi','id'=>'nominee_relationship','name'=>'emg_con_relation')))}}
                        <div class="help-block with-errors has-feedback"></div>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-12 font-normal"><strong>Address</strong></label>
                    <div class="col-sm-12">
                        <input type="text" name="emg_con_address" placeholder="Address" class="form-control" value="{{!empty($info->address)?$info->address:old('emg_con_address')}}" data-error="Contract Address Required" required />
                    </div>
                    <div class="help-block with-errors has-feedback"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="text-left" style="margin-top: 10px">
                        <input class="custom-check" type="checkbox"  name="is_primary_contract" id="is_primary_contract" tabindex="3" value="1" {{ !empty($info->is_primary) && $info->is_primary == 1?'checked':'' }}>
                        <label for="is_primary_contract">Is Primary Contact!</label>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($info->id) && !empty($info->id))
            <input type="hidden" name="emg_con_id" value="{{$info->id}}">
        @endif

    </form>
</div>
<div class="modal-footer justify-content-right">
    <button type="button" class="btn btn-w-m btn-danger btn-lg" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-w-m btn-primary btn-lg" data-product_id="" id="addEmargencyContract">Save</button>
</div>