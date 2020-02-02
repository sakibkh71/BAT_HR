<section class="tab-pane fade active show" id="transfer" role="tabpanel" aria-labelledby="separation-tab">
    <div class="step-header open-header" id="separation_head">
        <h2>Separation Information</h2>
        @if(isset($employee)) @endif
    </div>
    <div class="step-content">
        <form action="{{route('hr-separation-causes-store')}}" method="post" id="seperatoionForm">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group row">
                        <label class="col-sm-12 font-normal"><strong>Seperation</strong><span class="required">*</span></label>
                        <div class="col-sm-12">
                            {{__combo('hr_separation_causes', array('selected_value'=> !empty($employee->hr_separation_causes_id)?$employee->hr_separation_causes_id:old('hr_separation_causes_id'), 'attributes'=> array( 'name'=>'hr_separation_causes_id',  'id'=>'hr_separation_causes_id', 'required'=>'true', 'class'=>'form-control')))}}
                            <div class="help-block with-errors has-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-normal"><strong>Separation Date</strong> <span class="required">*</span></label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="separation_date" class="form-control" data-error="Please select Separation Date" value="{{ !empty($employee->separation_date)?$employee->separation_date:old('separation_date')}}" placeholder="YYYY-MM-DD"  required="" autocomplete="off">
                        </div>
                        <div class="help-block with-errors has-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="employee_id" value="{{$employee->id??old('employee_id')}}">
                    <button class="btn btn-primary btn-lg" type="submit" id="seperationSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                    <button class="btn btn-warning btn-lg" type="button" id="cancelSepert"><i class="fa fa-close"></i>&nbsp;Cancel Separation</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        // when the form is submitted
        $("#seperatoionForm").on("submit", function(e) {
            if (!e.isDefaultPrevented()) {
                swalConfirm('Are you sure?').then(function(s){
                    if(s.value){
                        var redirectUrl = '{{ URL::current()}}';
                        var data = $('#seperatoionForm').serialize();
                        var postUrl = $('#seperatoionForm').attr('action');
                        $.ajax({
                            type: 'POST',
                            url: postUrl,
                            data: data,
                            cache: false,
                            success: function(){
                                swalRedirect(redirectUrl, 'Seperation information added successfully', 'success');
                            },
                            error: function(){
                                swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                            }
                        });
                    }
                });
                return false;
            }
        });
        

        $("#cancelSepert").on("click", function(e) {
            swalConfirm('Are you sure to cancel separation?').then(function(s) {
                if (s.value) {
                    var redirectUrl = '{{ URL::current()}}';
                    var postUrl = $('#seperatoionForm').attr('action');
                    $.ajax({
                        type: 'POST',
                        url: postUrl,
                        data: {'employee_id':'{{$employee->id}}'},
                        cache: false,
                        success: function(){
                            swalRedirect(redirectUrl, 'Seperation information added successfully', 'success');
                        },
                        error: function(){
                            swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                        }
                    });
                }
            });
        });
    </script>
</section>