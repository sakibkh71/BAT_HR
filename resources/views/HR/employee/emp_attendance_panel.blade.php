<section class="tab-pane fade active show" id="transfer" role="tabpanel" aria-labelledby="attendance-tab">
    <div class="step-header open-header" id="transfer_head">
        <h2>Attendance History</h2>

        @if(isset($employee))
            <div class="pull-right">
                    <div class="col-sm-12">
                        <label class="form-label">Select Month</label>
                        <div class="input-group date_year">
                            <input type="text"
                                   placeholder=""
                                   class="form-control"
                                   value="{{date('Y-m')}}"
                                   id="monthly_attendance" name="monthly_attendance"/>
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {{-- functionality OK --}}
                            {{-- Just Hide he button --}}
                            {{--<button type="button" id="create_attendance_sheet" class="btn btn-warning btn-xs">Attendance Sheet Create</button>--}}
                        </div>
                    </div>
                <br>
            </div>
        @endif
        <div id="attendance" class="collapsed" aria-labelledby="attendance_head" data-parent="#EmployeeAccordion">

        </div>
    </div>

    <script>

        $('#create_attendance_sheet').click(function () {
            swalConfirm('to Create Attendance Sheet for selected Month.').then(function (e) {
               if(e.value){
                   var url = '{{route('hr-manual-attendance-sheet-create')}}';
                   var data = {
                       'month':$('#monthly_attendance').val(),
                       'sys_users_id': '{{$employee->id}}'
                   };
                   makeAjaxPost(data,url,null).done(function (response) {
                       console.log(response);
                       if(response.success){
                           swalSuccess('Attendance Sheet Create Successfully.');
                           window.setTimeout(function () {
                               window.location.reload();
                           }, 2000 );
                       }else{
                           swalError(response.message)
                       }

                   });
               }
            });
        });
        $('#monthly_attendance').datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        }).on('changeDate',function (e) {
            var month = $('#monthly_attendance').val();
            monthlyAttendance(month);
        });

        function monthlyAttendance(month) {
            var url = '<?php echo URL::to('get-emp-monthly-atten');?>';
            var data = {
                'sys_users_id': '{{$employee->id}}',
                'month': month
            };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });
            makeAjaxPostText(data, url, null).done(function (response) {
                if (response) {
                    $('#attendance').html(response.data);
                }
            });
        }
        $(function () {
            var month = '{{date("Y-m")}}';
            monthlyAttendance(month);
        });

    </script>
</section>