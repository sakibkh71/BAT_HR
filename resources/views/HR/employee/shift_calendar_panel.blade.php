<section class="tab-pane fade active show" id="transfer" role="tabpanel" aria-labelledby="attendance-tab">
    <div class="step-header open-header" id="transfer_head">
        <h2>Shift Calender History</h2>
        @if(isset($employee))
            <div class="pull-right">
                    <div class="col-sm-12">
                        <label class="form-label">Month</label>
                        <div class="input-group date_year">
                            <input type="text"
                                   placeholder=""
                                   class="form-control"
                                   value="{{date('Y-m')}}"
                                   id="monthly_attendance" name="monthly_attendance"/>
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                <br>
            </div>
        @endif
        <div id="shiftcalender">

        </div>
    </div>

    <script>
        $('#monthly_attendance').datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        }).on('changeDate',function (e) {
            var month = $('#monthly_attendance').val();
            getShiftCalender(month);
        });
        function getShiftCalender(month) {
            var url = '<?php echo URL::to('emp-shift-calendar');?>';
            var data = {
                'user_id': '{{$employee->id}}',
                'month': month
            };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });
            makeAjaxPostText(data, url, null).done(function (response) {
                if (response) {
                    $('#shiftcalender').html(response.data);
                }
            });
        }
        $(function () {
            var month = '{{date("Y-m")}}';
            getShiftCalender(month);
        });

    </script>
</section>