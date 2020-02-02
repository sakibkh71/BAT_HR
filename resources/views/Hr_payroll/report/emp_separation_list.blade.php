@extends('layouts.app')
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox-title">
                    <h2>Employee Settlement List</h2>
                    <div class="ibox-tools">
                        <button class="btn btn-primary btn-xs" id="separation_view"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                    </div>
                </div>
                <div class="ibox-content">
                    {!! __getMasterGrid('hr_leaver_emp_list',1) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Separation Modal -->

    <div class="modal fade" tabindex="-1" id="separationModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Employee Leaver Process</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="hidden" id="hr_emp_separation_id" value=""/>
                            <label class="font-normal"><strong>Leaver Date</strong> <span
                                        class="required">*</span></label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" name="separation_date" id="separation_date" class="form-control"
                                       data-error="Please select Leaver Date" value=""
                                       placeholder="YYYY-MM-DD" required="" autocomplete="off">
                            </div>
                            <div class="help-block with-errors has-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="leaverProcess" class="btn btn-primary">Process</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#separation_confirm, #separation_view, #separation_edit').hide();
        $('#separation_date').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true });
        var selected = [];
        var separation_dates = [];
        var emp_ids = [];
        var leaver_confirms = [];

        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');
            var separation_date = self.data('separation_date');
            var emp_id = self.data('sys_users_id');
            var is_confirm = self.data('is_confirm');

            /*add this for new customize*/
            selected = [];
            separation_dates = [];
            emp_ids = [];
            leaver_confirms = [];

            $('#table-to-print tbody tr').not($(this)).removeClass('selected');
            /* end this */

            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    selected.push(id);
                    separation_dates.push(separation_date);
                    emp_ids.push(emp_id);
                    leaver_confirms.push(is_confirm);
                } else {
                    selected.splice(selected.indexOf(id), 1);
                    separation_dates.splice(separation_dates.indexOf(separation_date), 1);
                    emp_ids.splice(emp_ids.indexOf(emp_id), 1);
                    leaver_confirms.splice(leaver_confirms.indexOf(is_confirm), 1);
                }

                var arr_length = selected.length;
                if (arr_length > 1) {
                    $('#separation_edit').hide();
                    $('#separation_view').hide();
                }
                else if (arr_length == 1) {
                    $('#separation_view').show();
                    $('#separation_edit').show();
                }
                else {
                    $('#separation_view').hide();
                    $('#separation_edit').hide();
                }
                if(leaver_confirms.includes(1)){
                    $('#separation_confirm').hide();
                    $('#separation_edit').hide();
                }else{
                    $('#separation_view').hide();
                    $('#separation_confirm').show();
                }
                // console.log(leaver_confirms);
            }

        });

        $('#separation_confirm').on('click',function (e) {
            Ladda.bind(this);
            var load = $(this).ladda();
           var data = {
               hr_emp_separation_id: selected
           };
           var url = '{{route('emp-separation-confirm')}}';
            if(selected.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                swalConfirm('to Confirm Leaver Selected Employees').then(function (e) {
                    if(e.value){
                        makeAjaxPost(data,url,load).done(function (response) {
                            if(response.success){
                                swalSuccess('Confirm Leaver Success');
                                window.location.reload();
                            }else{
                                swalError();
                            }
                        });
                        load.ladda('stop');
                    }else{
                        load.ladda('stop');
                    }
                });

            }
        });

        $('#separation_view').on('click',function (e) {
            var hr_emp_separation_id=selected[0];
            if(selected.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                var url = "{{route('separation-settlement-pdf')}}/"+hr_emp_separation_id;
                window.open(url,'_blank');
            }
        });

        $('#separation_edit').on('click',function (e) {
            var separation_date=separation_dates[0];
            var hr_emp_separation_id=selected[0];
            if(selected.length === 0){
                swalError('Please Select an Employee');
                return false;
            } else{
                $('#separation_date').val(separation_date);
                $('#hr_emp_separation_id').val(hr_emp_separation_id);
                $('#separationModal').modal('show');

            }
        });

        $('#leaverProcess').click(function () {
            var employee_id=emp_ids[0];
            var hr_emp_separation_id=selected[0];
            var separation_date = $('#separation_date').val();

            if(separation_date){
                var url = "{{route('get-separation-form')}}/"+employee_id+'/'+separation_date+'/'+hr_emp_separation_id;
                window.location.replace(url);
            }else{
                swalError('Please Select Leaver Date');
            }

        });
    </script>
@endsection
