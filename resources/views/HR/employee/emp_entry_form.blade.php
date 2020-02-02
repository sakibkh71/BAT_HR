@extends('layouts.app')
@section('content')
    <style>
        .tooltip {
            z-index: 100000000;
        }
        .form-btn-group ul li a{
            color: #694f0f;
            border: 1px solid #d0a73a;
            border-bottom: none;
            border-right: none;
            border-radius: 10px 10px 0 0;
            background: #fdba12;
        }
    </style>

    @php($tab=isset($tab)?$tab:'basic')
    @if(isset($mode) && $mode=='view' && !empty($employee->id))
        @php($tab_url = url('employee').'/'.$employee->id)
    @elseif(!empty($employee->id))
        @php($tab_url = route('employee-entry').'/'.$employee->id)
    @else
        @php($tab_url = route('employee-entry'))
    @endif
    <style>
        .ibox-title h2{
            font-size: 18px;
        }
        .ibox-title h2 span{
            margin-left: 10px;
            font-weight: 700;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title pad10">
                        <h2 class="inline">{{ !isset($mode)?(!empty($employee->name)?'Edit':'New'):''}} Employee Information  <span>{{ !empty($employee->name)?$employee->name:''}} {{ !empty($employee->user_code)?'('.$employee->user_code.')':''}}</span></h2>
                        <div class="ibox-tools">
                            <ul class="nav-tab" role="tablist">
                                @if(isset($employee->user_code))
                                <li><a class="btn {{($tab == 'basic')?'btn-warning active':'btn-xs btn-dark'}}" id="basic-tab" href="{{$tab_url}}/basic{{ isset($mode)? '/'.$mode:''}}" role="tab" aria-controls="basic" >Basic Info</a></li>
                                    <li><a class="btn {{($tab == 'salary')?'btn-warning active':'btn-xs btn-dark'}}" id="salary-tab"  href="{{$tab_url}}/salary{{ isset($mode)? '/'.$mode:''}}" role="tab" aria-controls="salary" aria-selected="false">Salary Info History</a></li>
                                        {{--Functionality OK -- but only hide the button--}}
                                    <li><a class="btn {{($tab == 'leave')?'btn-warning active':'btn-xs btn-dark'}}" id="leave-tab" class="" href="{{$tab_url}}/leave{{ isset($mode)? '/'.$mode:''}}" role="tab" aria-controls="leave">Leave History</a></li>
                                    <li><a class="btn {{($tab == 'attendance')?'btn-warning active':'btn-xs btn-dark'}}" id="attendance-tab" href="{{$tab_url}}/attendance{{ isset($mode)? '/'.$mode:''}}" role="tab" aria-controls="attendance" aria-selected="false">Attendance History</a></li>
                                    <li><a class="btn {{($tab == 'promotion')?'btn-warning active':'btn-xs btn-dark'}}" id="promotion-tab"  href="{{$tab_url}}/promotion{{ isset($mode)? '/'.$mode:''}}" role="tab" aria-controls="promotion" aria-selected="false">Promotion & Increment History</a></li>
                                @endif
                                {{--<li><a class="{{($tab == 'variable_salary')?'active':''}}" id="variable_salary-tab" href="{{$tab_url}}/variable_salary" role="tab" aria-controls="variable_salary" aria-selected="false">Variable Salary</a></li>--}}
                                {{--<li><a class="{{($tab == 'transfer')?'active':''}}" id="transfer-tab"  href="{{$tab_url}}/transfer" role="tab" aria-controls="transfer" aria-selected="false">Transfer</a></li>--}}
                                {{--<li><a class="{{($tab == 'separation')?'active':''}}" id="separation-tab"  href="{{$tab_url}}/separation" role="tab" aria-controls="separation" aria-selected="false">Separation</a></li>--}}
                                {{--<li><a class="{{($tab == 'shiftcalendar')?'active':''}}" id="shiftcalendar-tab" href="{{$tab_url}}/shiftcalendar" role="tab" aria-controls="shiftcalendar" aria-selected="false">Shift Calendar</a></li>--}}

                            </ul>
                        </div>
                    </div>
                    <div class="tab-content form-section">
                        <div class="ibox-content pad10">
                            @if($tab == 'basic')
                                @include('HR/employee/basic_form')
                            @elseif($tab == 'leave')
                                @include('HR/employee/emp_leave_panel')
                            @elseif($tab == 'salary')
                                @include('HR/employee/emp_salary_wages_panel')
                            @elseif($tab == 'promotion')
                                @include('HR/employee/inc_pro_panel')
                            @elseif($tab == 'transfer')
                                @include('HR/employee/emp_transfer_panel')
                            @elseif($tab == 'attendance')
                                @include('HR/employee/emp_attendance_panel')
                            {{--@elseif($tab == 'separation')--}}
                                {{--@include('HR/employee/emp_separation_panel')--}}
                            @elseif($tab == 'shiftcalendar')
                                @include('HR/employee/shift_calendar_panel')
                            {{--@elseif($tab == 'variable_salary')--}}
                                {{--@include('HR/employee/variable_salary')--}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Scripts -->
    <script>
        $('.clockpicker').clockpicker({
            autoclose: true
        });

        $(document).on('change','#hr_working_shifts_id',function () {
            var shift_id = $(this).val();
            var url = '{{url("hr-working-shift-time")}}';
            var data = {'shift':shift_id};
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });
            makeAjaxPost(data,url).done(function (response) {
                if(response){
                    $('#start_time').val(response.start_time);
                    $('#end_time').val(response.end_time);
                }
            });
        });
        //Employee ID When add any personal information or Edit
        var employeeId = '{{!empty($employee->id)?$employee->id:'null'}}';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        $('#parmanent_district option:contains("Choose Option")').text('Select District');

        //Datepicker
        function yearpicker(){
            $('.year-group.date').datepicker({
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years",
                autoclose:true
            });
        }
        yearpicker();


        //Datepicker
        function datepic(){
            $('.input-group.date').datepicker({ format: "yyyy-mm-dd", autoclose:true });
        }
        datepic();

        //Bootstrap Form Validator
        $('#basicForm').validator();
        $('#officialForm').validator();

        /*
         * Instant preview Image when click upload
         */
        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#'+id)
                        .attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#userImage').click(function (e) {
            $('#userImgBtn').trigger('click');
        });
        $('#userSign').click(function (e) {
            $('#signBtn').trigger('click');
        });



        /*
        * Get Degree when change Qualification
        -------------------------------------*/
        $(document).on('change', '#educational_qualifications_name', function(){
            var name = $(this).val();
            var url = '<?php echo URL::to('get-degree-list');?>';
            var data = {'name' : name};
            makeAjaxPost(data, url).done(function(response){
                var options = '<option value="">Select Degree</option>';
                if (response.success && response.data.length >0){
                    $('#degreeRow').show();
                    jQuery.each(response.data, function (i, val) {
                        options += '<option value="'+val.educational_degrees_name+'">'+val.educational_degrees_name+'</option>';
                    });
                }else{
                    $('#degreeRow').hide();
                }
                $('#educational_degrees_name').html(options);
                $('#educational_degrees_name').multiselect("rebuild");
            });
        });

        /*
        * New Insurance Modal Open
        * */

        $('#newInsurance').click(function () {
            var url = '<?php echo URL::to('get-insurance-form');?>';
            var data = {};
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#insuranceContent').html(response);
                }
                datepic();
                $('#insuranceModal').modal('show');
            });
        });

        /*
        * New Emargency Contract Modal Open
        * */

        $('#newEmargencyContract').click(function () {
            var url = '<?php echo URL::to('get-emargency-contract-form');?>';

            var data = {};
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#emargencyContractContent').html(response);
                }
                yearpicker();
                $('#emargencyContracModal').modal('show');
            });
        });


        /*
        * Insurance Section row select and delete row or Edit item
        * */
        var select_insurance=[];
        $(document).on('click', '.insurance-select-toggle', function(e){
            var self = $(this);
            var id = self.attr('id');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    select_insurance.push(id);

                    //self.find('input[type=checkbox]').prop("checked", true);
                } else {
                    select_insurance.splice(select_insurance.indexOf(id), 1);
                    //self.find('input[type=checkbox]').prop("checked", false);
                }

                var arr_length = select_insurance.length;

                if (arr_length == 1) {
                    $('#editInsurance').show();

                }
                else {
                    $('#editInsurance').hide();
                }

                if (arr_length > 0) {
                    $('#deleteInsurance').show();
                }
                else{
                    $('#deleteInsurance').hide();
                }
            }
        });

        /*
        * Emargency Contract Section row select and delete row or Edit item
        * */
        var select_emg_con=[];
        $(document).on('click', '.emargency-contract-select-toggle', function(e){
            var self = $(this);
            var id = self.attr('id');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    select_emg_con.push(id);

                    //self.find('input[type=checkbox]').prop("checked", true);
                } else {
                    select_emg_con.splice(select_emg_con.indexOf(id), 1);
                    //self.find('input[type=checkbox]').prop("checked", false);
                }

                var arr_length = select_emg_con.length;

                if (arr_length == 1) {
                    $('#editEmargencyContract').show();
                }
                else {
                    $('#editEmargencyContract').hide();
                }

                if (arr_length > 0) {
                    $('#deleteEmargencyContract').show();
                }
                else{
                    $('#deleteEmargencyContract').hide();
                }
            }
        });

        /*
        *  get Insurance Edit Modal
        * */
        $('#editInsurance').click(function () {
            if (select_insurance.length == 1){

                var url = '<?php echo URL::to('get-insurance-form');?>';
                var data = {'id': parseInt(select_insurance[0])};

                makeAjaxPostText(data, url).done(function(response){
                    if(response){

                        $('#insuranceContent').html(response);

                    }

                    $('#insuranceModal').modal('show');
                });
            } else {
                swalError('Please select education item row first')
            }
        });

        /*
        *  get emargency contract Edit Modal
        * */
        $('#editEmargencyContract').click(function () {
            if (select_emg_con.length == 1){
                var url = '<?php echo URL::to('get-emargency-contract-form');?>';
                var data = {'id': select_emg_con[0]};
                makeAjaxPostText(data, url).done(function(response){

                    if(response){
                        $('#emargencyContractContent').html(response);

                    }

                    $('#emargencyContracModal').modal('show');
                });
            } else {
                swalError('Please select emargency contract item row first')
            }
        });
        /*
        * Add or Edit Insurance Form
        * */
        $(document).on('click', '#addInsurance', function (event) {
            //alert(employeeId);
            if (!$('#insuranceForm').validator('validate').has('.has-error').length) {

                if (employeeId != 'null') {
                    var url = '{{route('submit-insurance-info')}}';
                    var $form = $('#insuranceForm');

                    var data = {
                        'sys_users_id': employeeId
                    };
                    data = $form.serialize() + '&' + $.param(data);
                    makeAjaxPost(data, url).done(function (response) {
                        if (response.success) {
                            var table='';
                            $.each(response.data , function (key,value) {
                                var insurance_detail=value.claim_details == null ? 'N/A':value.claim_details;
                                table+='<tr class="insurance-select-toggle" id="'+value.hr_insurane_claim_id+'"><td>'+value.claim_type+'</td><td>'+value.claim_date+'</td><td>'+value.claim_amount+'</td><td>'+insurance_detail+'</td><td>'+value.claim_status+'</td>';
                            });
                            $('#insuranceInfoWrap').html(table);
                            swalSuccess(response.message);

                        }

                        $('#insuranceForm').trigger("reset");
                        $('#insuranceModal').modal('hide');
                        $('#editInsurance').hide();
                        $('#deleteInsurance').hide();
                        select_insurance=[];
                    });
                } else {
                    swalError("Sorry! you need to add personal information first");
                }
            }



        });

        /*
       * Add or Edit Emargency Contract Form
       * */
        $(document).on('click', '#addEmargencyContract', function (event) {
            //alert(employeeId);
            if (!$('#emargencyContractForm').validator('validate').has('.has-error').length) {

                if (employeeId != 'null') {
                    var url = '{{route('submit-emargency-contract-info')}}';
                    var $form = $('#emargencyContractForm');

                    var data = {
                        'sys_users_id': employeeId
                    };
                    data = $form.serialize() + '&' + $.param(data);
                    makeAjaxPost(data, url).done(function (response) {
                        if (response.success) {
                            var table='';
                            $.each(response.data , function (key,value) {
                                var is_primary = value.is_primary==1?'Primary':'';
                                table+='<tr class="emargency-contract-select-toggle" id="'+value.id+'"><td>'+value.name+'</td><td>'+value.mobile+'</td><td>'+value.relation+'</td><td>'+value.address+'</td><td>'+is_primary+'</td>';
                            });
                            $('#emargencyContractInfoWrap').html(table);
                            swalSuccess(response.message);

                        }

                        $('#emargencyContractForm').trigger("reset");
                        $('#emargencyContracModal').modal('hide');
                        $('#editEmargencyContract').hide();
                        $('#deleteEmargencyContract').hide();
                        select_emg_con=[];
                    });
                } else {
                    swalError("Sorry! you need to add personal information first");
                }
            }



        });

        /*
       * Delete Insurance Row in Insurance Table
       -------------------------------------*/
        $(document).on('click', '#deleteInsurance', function (event) {

            if (select_insurance.length > 0){
                swalConfirm('to delete this items').then(function (s) {
                    if (s.value){
                        var url = '{{route('delete-insurance-info')}}';
                        var data = {
                            'ids' : select_insurance
                        };
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.success){
                                $.each( response.data, function( key, value ) {
                                    $('#insuranceInfoWrap tr#'+value).remove();
                                });
                            }
                            $('#deleteInsurance').hide();
                            $('#editInsurance').hide();
                            select_insurance =[];
                        });
                    }else{
                        $('#deleteInsurance').hide();
                        $('#editInsurance').hide();
                        select_insurance =[];
                    }
                });
            }else{
                swalError("Sorry! Please Select Insurance Item");
            }
        });

        /*
       * Delete Emargency Contract Row in Insurance Table
       -------------------------------------*/
        $(document).on('click', '#deleteEmargencyContract', function (event) {

            if (select_emg_con.length > 0){
                swalConfirm('to delete this items').then(function (s) {
                    if (s.value){
                        var url = '{{route('delete-emargency-contract-info')}}';
                        var data = {
                            'ids' : select_emg_con
                        };
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.success){
                                $.each( response.data, function( key, value ) {
                                    $('#emargencyContractInfoWrap tr#'+value).remove();
                                });
                            }
                            $('#deleteEdu').hide();
                            $('#editEdu').hide();
                            select_emg_con =[];
                        });
                    }else{
                        $('#deleteEmargencyContract').hide();
                        $('#editEmargencyContract').hide();
                        select_emg_con =[];
                    }
                });
            }else{
                swalError("Sorry! Please Select Insurance Item");
            }
        });


        /*
         * Education Section row select and delete row or Edit item
         --------------------------------------------------------------------*/
        var select_edu = [];
        $(document).on('click', '.edu-select-toggle', function(e){
            var self = $(this);
            var id = self.attr('id');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    select_edu.push(id);
                    //self.find('input[type=checkbox]').prop("checked", true);
                } else {
                    select_edu.splice(select_edu.indexOf(id), 1);
                    //self.find('input[type=checkbox]').prop("checked", false);
                }

                var arr_length = select_edu.length;

                if (arr_length == 1) {
                    $('#editEdu').show();
                }
                else {
                    $('#editEdu').hide();
                }

                if (arr_length > 0) {
                    $('#deleteEdu').show();
                }
                else{
                    $('#deleteEdu').hide();
                }
            }
        });


        /*
         * New Educational Modal Open
         -------------------------------------*/
        $('#newEdu').click(function () {
            var url = '<?php echo URL::to('get-edu-form');?>';
            var data = {};
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#eduContent').html(response);
                }
                yearpicker();
                $('#eduModal').modal('show');
            });
        });

        $(document).on('keyup', '#results', function () {
            var rstype = $('#result_type').val();
            var rsval = $(this).val();
            if (rstype.length<1){
                swalError('Please select Result Type');
            }
            if (rstype == 'Grade'){
                if (rsval < 2 || rsval > 5){
                    swalError('Please provide grading point between 2 to 5');
                    $('#results').val('');
                }
            }
        });

        $(document).on('change', '#result_type', function (e) {
           var rstype = $('#result_type').val();
           if (rstype == 'Division') {
               $('#grade_row').hide();
               $('#results_division').show();
           }else{
               $('#grade_row').show();
               $('#results_division').hide();
           }
           $('#results_division').val('');
           $('#results').val('');
           $('#outof').val('');
        });

        /*
         * Edit Educational Information
         -------------------------------------*/
        $('#editEdu').click(function () {
            if (select_edu.length == 1){
                var url = '<?php echo URL::to('get-edu-form');?>';
                var data = {'id': select_edu[0]};
                makeAjaxPostText(data, url).done(function(response){
                    if(response){
                        $('#eduContent').html(response);
                    }
                    yearpicker();
                    $('#eduModal').modal('show');
                });
            } else {
                swalError('Please select education item row first')
            }
        });

        /*
         * Add / Edit Education Row in Education Table
         -------------------------------------*/
        $(document).on('click', '#addEducation', function (event) {
            if (!$('#educationalForm').validator('validate').has('.has-error').length) {

                var result_type = $('#result_type').val();
                var results_division = $('#results_division').val();
                var results = $('#results').val();
                var outof = $('#outof').val();

                if (result_type == 'Division' && results_division =='') {
                    swalError('Sorry! please provide correct information');
                }else if (result_type == 'Grade' && ( results == '' || outof == '') ) {
                    swalError('Sorry! please provide correct information');
                }else{
                    if (employeeId !='null'){
                        var url = '{{route('submit-education-info')}}';
                        var $form = $('#educationalForm');

                        var data = {
                            'sys_users_id' : employeeId
                        };
                        data = $form.serialize() + '&' + $.param(data);
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.success){
                                var table = '';
                                $.each( response.data, function( key, value ) {
                                    if(value.result_type == 'Grade'){
                                        var outof = 'out of (' + value.outof + ')';
                                    }else{
                                        var outof = '';
                                    }
                                    table += '<tr class="edu-select-toggle" id="'+value.hr_emp_educations_id+'"><td>'+ ( value.educational_qualifications_name != null ? value.educational_qualifications_name : 'N/A') +'</td><td>'+(value.educational_institute_name != null ? value.educational_institute_name : 'N/A') +'</td><td>'+ (value.education_board != null ? value.education_board : 'N/A')+'</td><td>'+(value.passing_year != null ? value.passing_year : 'N/A')+'</td><td>'+(value.education_study_filed != null ? value.education_study_filed : 'N/A')+'</td><td>'+ (value.result_type != null ? value.result_type : 'N/A')+'</td><td>'+(value.results!= null ? value.results : 'N/A')+' ' + outof +'</td></tr>';
                                });
                                $('#eduInfoWrap').html(table);
                                swalSuccess(response.message);
                            }

                            $('#educationalForm').trigger("reset");
                            $('#eduModal').modal('hide');
                            $('#editEdu').hide();
                            $('#deleteEdu').hide();
                            select_edu =[];
                        });
                    }else{
                        swalError("Sorry! you need to add personal information first");
                    }
                }
            }
        });

        /*
         * Delete Education Row in Education Table
         -------------------------------------*/
        $(document).on('click', '#deleteEdu', function (event) {

            if (select_edu.length > 0){
                swalConfirm('to delete this items').then(function (s) {
                    if (s.value){
                        var url = '{{route('delete-education-info')}}';
                        var data = {
                            'ids' : select_edu
                        };
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.success){
                                $.each( response.data, function( key, value ) {
                                    $('#eduInfoWrap tr#'+value).remove();
                                });
                            }
                            $('#deleteEdu').hide();
                            $('#editEdu').hide();
                            select_edu =[];
                        });
                    }else{
                        $('#deleteEdu').hide();
                        $('#editEdu').hide();
                        select_edu =[];
                    }
                });
            }else{
                swalError("Sorry! Please Select Educational Item");
            }
        });



        /*
        * Accountas Section row select and delete row or Edit item
        --------------------------------------------------------------------*/
        var select_acc = [];
        $(document).on('click', '.acc-select-toggle', function(e){
            var self = $(this);
            var id = self.attr('id');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    select_acc.push(id);
                } else {
                    select_acc.splice(select_acc.indexOf(id), 1);
                }

                var acc_length = select_acc.length;

                if (acc_length == 1) {
                    $('#editAccInfo').show();
                }
                else {
                    $('#editAccInfo').hide();
                }

                if (acc_length > 0) {
                    $('#deleteAccInfo').show();
                }
                else{
                    $('#deleteAccInfo').hide();
                }
            }
        });

        /*
        * Add/Edit MFS Account Information
        * */
    $('#addMfsAccount').click(function () {
        if (!$('#mfsForm').validator('validate').has('.has-error').length) {
            if(employeeId !='null'){
                // alert(employeeId);
                var url='{{route("hr_submit_mfs_account")}}';
                var $form = $('#mfsForm');
                var data = {
                    'sys_users_id' : employeeId
                };
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if(response.success) {
                        swalSuccess('MFS Account Added Sucessfully.');
                    }
                });
            } else {
                swalError("Sorry! MFS Information Needed");
            }

        }
    });

        /*
         * New Bank Account Modal Open
         -------------------------------------*/
        $('#newAccInfo').click(function () {
            var url = '<?php echo URL::to('get-acc-form');?>';
            var data = {};
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#accountContent').html(response);
                    $('[data-toggle="tooltip"]').tooltip();
                }
                $('#accountModal').modal('show');
            });
        });

        /*
         * Edit Accounts Information
         -------------------------------------*/
        $('#editAccInfo').click(function () {
            if (select_acc.length == 1){
                var url = '<?php echo URL::to('get-acc-form');?>';
                var data = {'id': select_acc[0]};
                makeAjaxPostText(data, url).done(function(response){
                    if(response){
                        $('#accountContent').html(response);
                    }
                    $('#accountModal').modal('show');
                });
            } else {
                swalError('Please select education item row first')
            }
        });


        /*
        * Get Branch List Depend on Banks ID
        -------------------------------------*/
        $(document).on('change', '#banks_id', function(){
            var id = $(this).val();
            var url = '<?php echo URL::to('get-branch-list');?>/'+id;
            var data = {'id' : id};

            makeAjaxPost(data, url).done(function (response) {
                var options = '<option value="">Select Branch</option>';
                if (response.success && response.data.length >0){
                    jQuery.each(response.data, function (i, val) {
                        options += '<option value="'+val.bank_branch_name+'">'+val.bank_branch_name+'</option>';
                    });
                }
                $('#branch_name').html(options);
                $('#branch_name').multiselect("rebuild");
            });
        });

        /*
         * Add / Edit Bank Account Information
         -------------------------------------*/
        $(document).on('click', '#addAccount', function (event) {
            if (!$('#accountForm').validator('validate').has('.has-error').length) {

                if (employeeId !='null'){
                    var url = '{{route('submit-account-info')}}';
                    var $form = $('#accountForm');
                    var data = {
                        'sys_users_id' : employeeId
                    };

                    data = $form.serialize() + '&' + $.param(data);

                    makeAjaxPost(data, url).done(function (response) {
                        //console.log(response.data);
                        if (response.success && response.data.length > 0){
                            var table = '';
                            $.each( response.data, function( key, value ) {
                                var salaryac = value.is_active_account==1?'Yes':'No';
                                table += '<tr class="acc-select-toggle" id="'+value.hr_emp_bank_accounts_id+'"><td>'+value.bank_name+'</td><td>'+value.branch_name+'</td><td>'+value.bank_account_types_name+'</td><td>'+value.account_number+'</td><td>'+ salaryac +'</td></tr>';
                            });
                            $('#accInfoWrap').html(table);
                            swalSuccess(response.message);
                        }
                        $('#accountForm').trigger("reset");
                        $('#accountModal').modal('hide');
                        $('#editAccInfo').hide();
                        $('#deleteAccInfo').hide();
                        select_acc =[];
                    });
                }else{
                    swalError("Sorry! you need to add personal information first");
                }

            }
        });

        $(document).on('change', '#bank_account_types_id', function () {
            var banktype = $(this).val();
            if (employeeId !='null'){
                var data = {
                    'sys_users_id' : employeeId,
                    'bank_account_types_id' : banktype
                };
                var url = "{{route('check-account-type')}}";
                makeAjaxPost(data, url).done(function (response) {
                   if(response.data){
                       if (response.data == "exist"){
                          $('#bank_account_types_id').val('');
                          $('#bank_account_types_id').multiselect('rebuild');
                          swalError("Sorry! this accounts type already exist");
                       }
                   }
                });
            }else{
                swalError("Sorry! you need to add personal information first");
            }
        });


        /*
         * Delete Education Row in Education Table
         -------------------------------------*/
        $(document).on('click', '#deleteAccInfo', function (event) {
            if (select_acc.length > 0){
                swalConfirm('to delete this items').then(function (s) {
                    if (s.value){
                        var url = '{{route('delete-acc-info')}}';
                        var data = {
                            'ids' : select_acc
                        };
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.success){
                                $.each( response.data, function( key, value ) {
                                    $('#accInfoWrap tr#'+value).remove();
                                });
                            }
                            $('#deleteAccInfo').hide();
                            $('#editAccInfo').hide();
                            select_acc =[];
                        });
                    }else{
                        $('#deleteAccInfo').hide();
                        $('#editAccInfo').hide();
                        select_acc =[];
                    }
                });
            }else{
                swalError("Sorry! Please Select Educational Item");
            }
        });


        /*
        * Employment Section row select and delete row or Edit item
        --------------------------------------------------------------------*/
        var select_emp = [];
        $(document).on('click', '.emp-select-toggle', function(e){
            var self = $(this);
            var id = self.attr('id');
            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    select_emp.push(id);
                } else {
                    select_emp.splice(select_emp.indexOf(id), 1);
                }

                var emp_length = select_emp.length;

                if (emp_length == 1) {
                    $('#editEmploymentInfo').show();
                }
                else {
                    $('#editEmploymentInfo').hide();
                }

                if (emp_length > 0) {
                    $('#deleteEmploymentInfo').show();
                }
                else{
                    $('#deleteEmploymentInfo').hide();
                }
            }
        });


        /*
       * New Employment Information Modal Open
       -------------------------------------*/
        $('#newEmploymentInfo').click(function () {
            var url = '<?php echo URL::to('get-emp-form');?>';
            var data = {};
            makeAjaxPostText(data, url).done(function(response){
                if(response){
                    $('#empContent').html(response);
                }
                datepic();
                $('#employmentModal').modal('show');
            });
        });

        /*
         * Edit Accounts Information
         -------------------------------------*/
        $('#editEmploymentInfo').click(function () {
            if (select_emp.length == 1){
                var url = '<?php echo URL::to('get-emp-form');?>';
                var data = {'id': select_emp[0]};
                makeAjaxPostText(data, url).done(function(response){
                    if(response){
                        $('#empContent').html(response);
                    }
                    datepic();
                    $('#employmentModal').modal('show');
                });
            } else {
                swalError('Please select education item row first')
            }
        });

        /*
         * Delete Employment Profession Row in Education Table
         --------------------------------------------------------*/
        $(document).on('click', '#deleteEmploymentInfo', function (event) {
            if (select_emp.length > 0){
                swalConfirm('to delete this items').then(function (s) {
                    if (s.value){
                        var url = '{{route('delete-emp-info')}}';
                        var data = {
                            'ids' : select_emp
                        };
                        makeAjaxPost(data, url).done(function (response) {
                            if (response.success){
                                $.each( response.data, function( key, value ) {
                                    $('#employmentInfoWrap tr#'+value).remove();
                                });
                            }
                            $('#deleteEmploymentInfo').hide();
                            $('#editEmploymentInfo').hide();
                            select_emp =[];
                        });
                    }else{
                        $('#deleteEmploymentInfo').hide();
                        $('#editEmploymentInfo').hide();
                        select_emp =[];
                    }
                });
            }else{
                swalError("Sorry! Please Select Educational Item");
            }
        });

        //if Checked Continue then to date will blank
        $(document).on('change', '#is_continue', function(){
            if($(this).is(':checked')){
                $('#to_date').val('');
            }
        });

        //if set To date then Continue will unchecked
        $(document).on('change', '#to_date', function(){
            if($(this).val() !=''){
                $('#is_continue').prop('checked', false);
            }
        });


        /*
         * Add New Row in Employment History Table
         -------------------------------------*/
        $(document).on('click', '#addProfession', function (event) {

            if (!$('#employmentForm').validator('validate').has('.has-error').length) {

                if (employeeId !='null'){
                    var url = '{{route('submit-employment-info')}}';
                    var $form = $('#employmentForm');
                    var data = {
                        'sys_users_id' : employeeId
                    };

                    data = $form.serialize() + '&' + $.param(data);

                    makeAjaxPost(data, url).done(function (response) {

                        //console.log(response.data);

                        if (response.success && response.data.length > 0){
                            var table = '';
                            $.each( response.data, function( key, value ) {
                                var tod = value.is_continue == 0? moment(value.to_date).format("DD MMM, YYYY"):'Continue';
                                table += '<tr class="emp-select-toggle" id="'+value.hr_emp_professions_id+'"><td>'+value.organization_name+'</td><td>'+value.bat_company_id+'</td><td>'+value.designation_name+'</td><td>'+
                                    moment(value.from_date).format("DD MMM, YYYY") +' to '+ tod +'</td><td>'+value.responsibilities+'</td></tr>';
                            });
                            $('#employmentInfoWrap').html(table);
                            swalSuccess(response.message);
                        }
                        $('#employmentForm').trigger("reset");
                        $('#employmentModal').modal('hide');
                        $('#editEmploymentInfo').hide();
                        $('#deleteEmploymentInfo').hide();
                        select_emp =[];
                    });
                }else{
                    swalError("Sorry! you need to add personal information first");
                }

            }

        });


        //collups btn actions coustomize
        $('.accordion-btn[data-toggle="collapse"]').click(function (event) {
            event.preventDefault();
            if($(this).hasClass( "collapsed" )){
                $('.step-header').removeClass('open-header');
                $(this).closest('.step-header').addClass('open-header');
            }else{
                $(this).closest('.step-header').removeClass('open-header');
            }
        });


        //Get Provission Period devends on employee category
        $('#hr_emp_categorys_id').change(function (e) {
            var empcatid = $(this).val();
            var url = '{{route('get-provision-period')}}',
                data = {'empcatid':empcatid};
            makeAjaxPost(data, url, null).done(function (response) {
                if (response.success && response.data !=null){
                    $('#provision_period').val(response.data.provision_period);
                    if ($('#date_of_join').val() !='') {
                        var d = moment($('#date_of_join').val());
                        var pdate = response.data.provision_period;
                        var cdate = moment(d).add(pdate, 'M');
                        var confirm = moment(cdate).format("YYYY-MM-DD");
                        $('#date_of_confirmation').datepicker({format: 'yyyy-mm-dd'}).datepicker('setDate', confirm);
                        $('#basicForm').validator('update');
                    }
                }else{
                    $('#date_of_join').val('');
                    $('#date_of_confirmation').val('');
                    $('#basicForm').validator('update');
                }
            });
        });
        //auto close date of confirmation
        $(document).on('change', '#date_of_confirmation', function(e){
            $(this).datepicker('hide');
        });


        //Set Confirmation date when cange join date based on provission period
        $('#date_of_join').change(function () {
            var d = moment($(this).val());
            var pdate = $('#provision_period').val();
            var cdate = moment(d).add(pdate, 'M');
            var confirm = moment(cdate).format("YYYY-MM-DD");
            $('#date_of_confirmation').datepicker({format: 'yyyy-mm-dd'}).datepicker('setDate', confirm);
            $('#basicForm').validator('update');
        });

        //check if confirm date is manually input
        $('#date_of_confirmation').change(function () {
            if ($('#date_of_join').val() !='') {
                var d = moment($('#date_of_join').val());
                var c =  moment($(this).val());
                if (d>c){
                    swalError("Sorry! Join date can't greater than confirm date");
                    $(this).val('');
                }
            }else{
                swalError("Please Join date select first");
            }
        });

        /*
         * Salary and Wages section scripts
         ******************************************************************************/
        //Get Salary Data when cange on grade
        function salaryLoad(grade){
          //  var grade =  $('#hr_emp_grades_id').val();
            var url = '{{route('get-grade-info')}}',
                data = {'grade':grade};
            makeAjaxPost(data, url, null).done(function (response) {

                if (!jQuery.isEmptyObject(response.addition)){

                    var adition='';
                    var aditionVal = 0;
                    $.each(response.addition, function( key, value ) {
                        var aut_check = value.auto_applicable == "YES"?'checked':'';
                        if (value.auto_applicable == "YES") { aditionVal += parseFloat(value.addition_amount);}
                        adition +='<div class="col-md-3 remove_additional_info"  ><div class="form-group"><label class="font-normal"><strong>'+ value.component_name +'</strong><input type="hidden" name="component_name['+ value.component_slug +']" value="'+ value.component_name +'"></label>';
                        adition +='<div class="input-group"  ><input type="text" name="salary_component['+ value.component_slug +']" data-autoapply="'+ value.auto_applicable +'" data-type="'+ value.component_type +'" data-id="'+ value.component_slug +'" class="form-control input_money" value="'+ value.addition_amount +'">';
                        adition +='<span class="input-group-addon no-display "><input type="checkbox" name="component_autoapply['+ value.component_slug +']" value="YES" class="pull-left auto_aply_field mr-1" '+ aut_check +' data-id="'+ value.component_slug +'">  Add to Gross </span> <input type="hidden" name="component_type['+ value.component_slug +']" value="'+ value.component_type +'"><input type="hidden" name="component_slug[]" value="'+ value.component_slug +'"></div></div></div>';
                    });
                   // adition +='<hr><div class="col-md-12 text-right"> Total Addition Amount = <strong class="totalAddition">'+aditionVal+'</strong><input type="hidden" id="total_adition" value="'+aditionVal+'"></div><hr>';
                    $('.remove_additional_info').remove();

                    $(adition).insertBefore("#insert_before");
                }else{

                    $('').insertBefore("#insert_before");

                    $('.remove_additional_info').remove();
                }

                if (!jQuery.isEmptyObject(response.deduct)) {
                    var deduct='';
                    var deductVal = 0;
                    $.each(response.deduct, function( key, value ) {
                        var aut_check = value.auto_applicable == "YES"?'checked':'';
                        if (value.auto_applicable == "YES") { deductVal += parseFloat(value.deduction_amount);}
                        deduct +='<div class="col-md-3"><div class="form-group"><label class="font-normal"><strong>'+ value.component_name +'</strong><input type="hidden" name="component_name['+ value.component_slug +']" value="'+ value.component_name +'"></label>';
                        deduct +='<div class="input-group"><input type="text" name="salary_component['+ value.component_slug +']" data-autoapply="'+ value.auto_applicable +'" data-type="'+ value.component_type +'" data-id="'+ value.component_slug +'" class="form-control input_money" value="'+ value.deduction_amount +'">';
                        deduct +='<span class="input-group-addon"><input type="checkbox" name="component_autoapply['+ value.component_slug +']" value="YES" class="pull-left auto_aply_field mr-1" '+ aut_check +'  data-id="'+ value.component_slug +'">  Add to Gross</span><input type="hidden" name="component_type['+ value.component_slug +']" value="'+ value.component_type +'"> <input type="hidden" name="component_slug[]" value="'+ value.component_slug +'"></div></div></div>';
                    });
                    deduct +='<hr><div class="col-md-12 text-right"> Total Deductions Amount = <strong class="totalDeduction">'+deductVal+'</strong><input type="hidden" id="total_deduction" value="'+deductVal+'"></div><hr>';
                    $('#deductionalOption').html(deduct);
                }else{
                    $('#deductionalOption').html('');
                }

                if (!jQuery.isEmptyObject(response.variable)) {
                    var variable='<div class="row">';
                    var variableVal = 0;
                    $.each(response.variable, function( key, value ) {
                        var aut_check = value.auto_applicable == "YES"?'checked':'';
                        //if (value.auto_applicable == "YES") { variableVal += parseFloat(value.addition_amount);}
                        variableVal += parseFloat(value.addition_amount);

                        variable +='<div class="col-md-12" style="display:none"><div class="form-group"><label class="font-normal"><strong>'+ value.component_name +'</strong><input type="hidden" name="component_name['+ value.component_slug +']" value="'+ value.component_name +'"></label>';
                        variable +='<div class="input-group"><input type="text" name="salary_component['+ value.component_slug +']" data-autoapply="'+ value.auto_applicable +'" data-type="'+ value.component_type +'" data-id="'+ value.component_slug +'" class="form-control" value="'+ value.addition_amount +'">';
                        variable +='<span class="input-group-addon"><input type="checkbox" name="component_autoapply['+ value.component_slug +']" value="YES" class="pull-left mr-1" '+ aut_check +'  data-id="'+ value.component_slug +'">  Add to Gross</span><input type="hidden" name="component_type['+ value.component_slug +']" value="'+ value.component_type +'"> <input type="hidden" name="component_slug[]" value="'+ value.component_slug +'"></div></div></div>';
                    });
                    variable +='<div class="col-md-12"><div class="form-group"><label class="font-normal"><strong>Variable Salary</strong></label><div class="input-group"><span class="input-group-addon"> <i class="fa fa-money"></i> </span> <input type="number" step="any" min="0" name="max_variable_salary" id="max_variable_salary" class="form-control text-left" placeholder="Variable Salary" autocomplete="off" value="'+variableVal+'"></div></div></div></div>';
                    $('#variableOption').html(variable).show();
                }else{
                    $('#variableOption').html('').hide();
                }

                if (response.data !=null){
                    $('#min_gross').val(response.data.gross_salary);
                    $('#basic_salary').val(response.data.basic_salary);

                    $('#yearly_increment').val(response.data.yearly_increment);

                    if (response.data.insurance_applicable == 1){
                        $('#insurance_applicable').prop('checked', true);
                        $('#insurance_amount').val(response.data.insurance_amount);
                        $('#insurance_input').removeClass('no-display');
                    }else{
                        $('#insurance_applicable').prop('checked', false);
                        $('#insurance_amount').val(0);
                        $('#insurance_input').addClass('no-display');
                    }

                    if (response.data.pf_applicable == 1){

                        var rate = parseFloat((response.data.pf_amount*100)/response.data.basic_salary).toFixed(2);

                        $('#pf_applicable').prop('checked', true);
                        $('#pf_amount').val(response.data.pf_amount);
                        $('#pf_rate').val(rate);
                        $('#pf_input').removeClass('no-display');
                        $('#pfr_input').removeClass('no-display');
                    }else{
                        $('#pf_applicable').prop('checked', false);
                        $('#pf_amount').val(0);
                        $('#pf_rate').val(0);
                        $('#pf_input').addClass('no-display');
                        $('#pfr_input').addClass('no-display');
                    }

                    if (response.data.gf_applicable == 1){
                        $('#gf_applicable').prop('checked', true);
                        $('#gf_amount').val(response.data.gf_amount);
                        $('#gf_input').removeClass('no-display');
                    }else{
                        $('#gf_applicable').prop('checked', false);
                        $('#gf_amount').val(0);
                        $('#gf_input').addClass('no-display');
                    }
                }
                else{
                    $('#min_gross').val('');
                    $('#basic_salary').val('');
                }
            });
        };

        $('#hr_emp_grades_id').change(function () {
            var grade =  $('#hr_emp_grades_id').val();
            alert(grade);
            salaryLoad(grade);
        });

        $(document).on('change','#designations_id',function(){

            var grade_array = @json($designationWiseGradeArray);
            var designations =$('#designations_id').val();
            console.log(designations);
            console.log(grade_array);
            var grade_id = grade_array[designations];


            if(grade_id !=null){
                $('#hr_emp_grades_id').val(grade_id);
                salaryLoad(grade_id);
                $('#basic_salary_options').show();
                $('.salary_error').hide();
            }else{
                $('#basic_salary_options').hide();
                $('.salary_error').show();
                $('.salary_error').html('Please Provide Salary info');
            }

            console.log(grade_id);
          //  alert(grade_id);
        });

        //Calculate Salary
        var canculateSalary = function(){
            var deductAmount = 0;
            var aditionAmount = 0;
            var basicSalary = parseFloat($('#basic_salary').val())||0;
            var gross = parseFloat($('#min_gross').val())||0;
           // var variable = parseFloat($('#max_variable_salary').val())||0;

            $('.input_money').each(function() {
                if ($(this).data('autoapply') == "YES"){
                    if($(this).data('type')=="Deduction"){
                        deductAmount += parseFloat($(this).val());
                    }else if($(this).data('type')=="Addition"){
                        aditionAmount += parseFloat($(this).val());
                    }
                }
            });

            gross =  parseFloat(basicSalary + aditionAmount - deductAmount);

            $('#min_gross').val(gross);
            $('#total_adition').val(aditionAmount);
            $('#total_deduction').val(deductAmount);
            $('.totalAddition').text(aditionAmount);
            $('.totalDeduction').text(deductAmount);
        };

        //ON Change Checkbox of Auto Apply calculate Salary
        $(document).on('click', '.auto_aply_field', function (e) {
            var id = $(this).data('id');
            if($(this).is(":checked")){
                $('.input_money[data-id="'+id+'"]').data( "autoapply", "YES" );
            }else{
                $('.input_money[data-id="'+id+'"]').data( "autoapply", "NO" );
            }
            canculateSalary();
        });

        //Prevent Text on Money Field Salary
        $(document).on('keypress', '.input_money', function(eve) {
            if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
                eve.preventDefault();
            }
        });

        //Onchange Deduction Input field
        $(document).on('change', '.input_money', function () {
            canculateSalary();
        });



        /*
         * Add New Row in Employment History Table
         -------------------------------------*/
        $(document).on('click', '#submitOfficialInfo', function (event) {


            if (!$('#officialForm').validator('validate').has('.has-error').length) {

                if (employeeId !='null'){
                    var url = '{{route('store-official-info')}}';
                    var $form = $('#officialForm');
                    var data = {
                        'sys_users_id' : employeeId
                    };
                    data = $form.serialize() + '&' + $.param(data);
                    makeAjaxPost(data, url).done(function (response) {
                        if(response.success){
                            swalSuccess('Official Information Updated Successfully.');
                        }
                    });
                }else{
                    swalError("Sorry! you need to add personal information first");
                }
            }
        });

        //other_conveyance
        $('#other_conveyance').change(function (e) {
            if($(this).prop('checked')){
                $('#conveyance_area').show();
            } else {
                $('#conveyance_area').hide();
            }
        });

        $('#newCnv').click(function () {
            var row =   '<tr>\n' +
                ' <td>\n' +
                '<div class="form-group">'+
                '<input type="text" name="conveyance_title[]" id="conveyance_title" required placeholder="Conveyance Title" class="form-control" value="">\n' +
                '</div>'+
                '</td>\n' +
                '<td>\n' +
                '<div class="form-group">'+
                '<input type="number" name="conveyance_amount[]" id="conveyance_amount" required placeholder="Amount" class="form-control input_money" value="">\n' +
                '</div>'+
                '</td>\n' +
                '<td>\n' +
                '<div class="form-group">'+
                '<button type="button" class="btn btn-danger btn-xs deleteCnv"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>\n' +
                '</div>'+
                '</td>\n' +
                '</tr>';
            $('#cnvInfoWrap').append(row);
        });
        $(document).on('click','.deleteCnv',function () {
            $row = $(this).closest('tr');
            swalConfirm().then(function (v) {
                if(v.value){
                    $row.remove();
                }
            });

        });

        $(document).on('click', '#submitSalaryInfo', function (event) {
            if (!$('#salaryForm').validator('validate').has('.has-error').length) {

                if (employeeId !='null'){
                    var url = '{{route('store-salary-info')}}';
                    var $form = $('#salaryForm');
                    var data = {
                        'sys_users_id' : employeeId
                    };
                    data = $form.serialize() + '&' + $.param(data);
                    makeAjaxPost(data, url).done(function (response) {
                        if(response.success){
                            swalSuccess('Salary Information Updated Successfully.');
                        }
                    });
                }else{
                    swalError("Sorry! you need to add personal information first");
                }
            }
        });


        //pf_applicable
        $('#pf_applicable').change(function (e) {
            if($(this).prop('checked')){
                $('#pf_input').removeClass('no-display');
                $('#pfr_input').removeClass('no-display');
            } else {
                $('#pf_input').addClass('no-display');
                $('#pfr_input').addClass('no-display');
                $('#pf_amount').val(0);
                $('#pf_rate').val(0);
            }
        });

        $('#pf_rate').change(function (e) {
            var rate = parseFloat($(this).val());
            var basic = parseFloat($('#basic_salary').val());
            var aval = parseFloat(( basic * rate ) / 100).toFixed(2);
            $('#pf_amount').val(aval);
        });

        $('#pf_amount').change(function (e) {
            var aval = parseFloat($(this).val());
            var basic = parseFloat($('#basic_salary').val());
            var rate = parseFloat((aval*100)/basic ).toFixed(2);
            $('#pf_rate').val(rate);
        });

        $('#basic_salary').change(function (e) {
            var rate = parseFloat($('#pf_rate').val());
            var basic = parseFloat($(this).val());
            var aval = parseFloat(( basic * rate ) / 100).toFixed(2);
            $('#pf_amount').val(aval);
        });


        //insurance_applicable
        $('#insurance_applicable').change(function (e) {
            if($(this).prop('checked')){
                $('#insurance_input').removeClass('no-display');
            } else {
                $('#insurance_input').addClass('no-display');
                $('#insurance_amount').val(0);
            }
        });

        //gf_applicable
        $('#gf_applicable').change(function (e) {
            if($(this).prop('checked')){
                $('#gf_input').removeClass('no-display');
            } else {
                $('#gf_input').addClass('no-display');
                $('#gf_amount').val(0);
            }
        });

    </script>


    <script>
        @if(!empty(Session::get('succ_msg')))
            var popupId = "{{ uniqid() }}";
            if(!sessionStorage.getItem('shown-' + popupId)) {
                swalSuccess("{{Session::get('succ_msg')}}").then(function () {
                    location.reload();
                });
            }
            sessionStorage.setItem('shown-' + popupId, '1');
        @endif
    </script>

    <script src="{{asset('public/js/plugins/intlTelInput/intlTelInput.min.js')}}"></script>
    <script>
        $('#mobile').keypress(function(eve) {
            if ((eve.which != 43 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0) ) {
                eve.preventDefault();
            }
        });

        $('#user_code').blur(function() {
            var code = $(this).val();
            if (code !='') {
                var url = "{{URL::to('check-user-code')}}";
                var _token = "{{ csrf_token() }}";
                var data = {_token:_token,user_code:code, id:employeeId};
                makeAjaxPost(data,url,null).then(function (s) {
                  if(s.result =='exist'){
                      swalError('Sorry! The Employee Id already Exist, Please use Another Employee Id');
                      $('#user_code').addClass('error');
                  }else{
                      $('#user_code').removeClass('error');
                  }
                });
            }
        });

        $('#email').blur(function() {
            var email = $(this).val();
            var url = "{{URL::to('check-email-exist')}}";
            var _token = "{{ csrf_token() }}";
            var data = {_token:_token,email:email, id:employeeId};
            makeAjaxPost(data,url,null).then(function (s) {
                if(s.result =='exist'){
                    swalError('Sorry! this Email already Exist, Please another Email');
                    $('#email').addClass('error');
                }else{
                    $('#email').removeClass('error');
                }
            });
        });

        /*
   *  Some validation check in the basic form
   * */
        var form_validation_mobile=true;
        var form_validation_nid=true;
        var form_validation_bc=true;
        var form_validation_passport=true;
        var form_validation_dl=true;
        //mobile
        $("#mobile").blur(function () {
            var mobile_value=$("#mobile").val();
            var employee_id='New';
            if(employeeId != 'null'){
                employee_id=employeeId;
            }


            var data={
                'sys_users_id': employee_id,
                'type':'mobile_check',
                'value':mobile_value
            };
            var url='{{route('check-basic-employee-uniqueness')}}';

            $.ajax({
                type:'post',
                data:data,
                url:url,
                async:false ,
                success:function (data) {
                    console.log(data);
                    if(data==0){
                        var html='<span style="color:red">Mobile number already exists</span>';
                        document.getElementById('mobile_error').innerHTML=html;
                        form_validation_mobile=false;
                    }
                    else if(data==1){
                        document.getElementById('mobile_error').innerHTML='';
                        form_validation_mobile=true;
                    }

                }
            });


        });

        //nid
        $("#nid").blur(function () {
            var nid_value=$("#nid").val();
            console.log('previous nid: '+nid_value);
            var employee_id='New';
            if(employeeId != 'null'){
                employee_id=employeeId;
            }


            var data={
                'sys_users_id': employee_id,
                'type':'nid_check',
                'value':nid_value
            };
            var url='{{route('check-basic-employee-uniqueness')}}';

            $.ajax({
                type:'post',
                data:data,
                url:url,
                async:false ,
                success:function (data) {
                    console.log(data);
                    if(data==0){
                        var html='<span style="color:red">NID already exists</span>';
                        document.getElementById('nid_error').innerHTML=html;
                        form_validation_nid=false;
                    }
                    else if(data==1){
                        document.getElementById('nid_error').innerHTML='';
                        form_validation_nid=true;
                    }

                }
            });


        });

        //birth Certificate

        $("#birth_certificate").blur(function () {
            var birth_certificate_value=$("#birth_certificate").val();
            var employee_id='New';
            if(employeeId != 'null'){
                employee_id=employeeId;
            }


            var data={
                'sys_users_id': employee_id,
                'type':'birth_certificate_check',
                'value':birth_certificate_value
            };
            var url='{{route('check-basic-employee-uniqueness')}}';

            $.ajax({
                type:'post',
                data:data,
                url:url,
                async:false ,
                success:function (data) {
                    console.log(data);
                    if(data==0){
                        var html='<span style="color:red">Birth Certificate already exists</span>';
                        document.getElementById('birth_certificate_error').innerHTML=html;
                        form_validation_bc=false;
                    }
                    else if(data==1){
                        document.getElementById('birth_certificate_error').innerHTML='';
                        form_validation_bc=true;
                    }

                }
            });


        });

        //passport
        $("#passport").blur(function () {
            var passport_value=$("#passport").val();
            var employee_id='New';
            if(employeeId != 'null'){
                employee_id=employeeId;
            }


            var data={
                'sys_users_id': employee_id,
                'type':'passport_check',
                'value':passport_value
            };
            var url='{{route('check-basic-employee-uniqueness')}}';

            $.ajax({
                type:'post',
                data:data,
                url:url,
                async:false ,
                success:function (data) {
                    console.log(data);
                    if(data==0){
                        var html='<span style="color:red">Passport already exists</span>';
                        document.getElementById('passport_error').innerHTML=html;
                        form_validation_passport=false;

                    }
                    else if(data==1){
                        document.getElementById('passport_error').innerHTML='';
                        form_validation_passport=true;
                    }

                }
            });


        });

        //driving License
        $("#driving_license").blur(function () {
            var driving_license_value=$("#driving_license").val();
            var employee_id='New';
            if(employeeId != 'null'){
                employee_id=employeeId;
            }


            var data={
                'sys_users_id': employee_id,
                'type':'driving_licence_check',
                'value':driving_license_value
            };
            var url='{{route('check-basic-employee-uniqueness')}}';

            $.ajax({
                type:'post',
                data:data,
                url:url,
                async:false ,
                success:function (data) {
                    console.log(data);
                    if(data==0){
                        var html='<span style="color:red">Driving License already exists</span>';
                        document.getElementById('driving_license_error').innerHTML=html;
                        form_validation_dl=false;
                    }
                    else if(data==1){
                        document.getElementById('driving_license_error').innerHTML='';
                        form_validation_dl=true;
                    }

                }
            });


        });




        $('#basicInfoSubmit').click(function (e) {


            var grade_array = @json($designationWiseGradeArray);
            var designations =$('#designations_id').val();

            var grade_id = grade_array[designations];
           if(grade_id !=null){
               $('.salary_error').hide();
               if(form_validation_mobile == false || form_validation_nid == false || form_validation_bc== false || form_validation_passport == false || form_validation_dl == false){
                   return false;
               }
               else {
                   if ($('#basicForm').find('.error').length !== 0) {
                       swalError('Sorry! please fill up the form with correct information')
                   } else {


                       var nid_value = $("#nid").val();
                       var birth_certificate_value = $("#birth_certificate").val();
                       var passport_value = $("#passport").val();
                       var driving_license_value = $("#driving_license").val();
                       if (nid_value == '' && birth_certificate_value == '' && passport_value == '' && driving_license_value == '') {
                           swalError('Must Provide NID  or Birth Certificate or Passport or Driving License');
                       } else {


                           if ($('#user_code').val() == '') {
                               swalConfirm("You can't provide Employee ID, System will generate Employee ID").then(function (s) {
                                   if (s.value) {

                                       $('#basicForm').submit();
                                   }
                               });

                           } else {

                               $('#basicForm').submit();
                           }
                       }
                   }
               }
           }else{

               $('.salary_error').show();
               $('.salary_error').html('Please Provide Salary info');
               return false;
           }



        });

        $(document).on('change', '#same_as_present', function (e) {
            var pal = $('#present_address_line').val();
            var pdist = $('#present_district').val();
            var pt = $('#present_thana').val();
            var post = $('#present_po').val();
            var pcode = $('#present_post_code').val();
            var pv = $('#present_village').val();
            if ($(this).prop('checked')){
                if (pdist =='' || pt=='' || post=='' ||  pv=='') {
                    swalError('Sorry! please fil up present address first');
                    $(this).prop( "checked", false );
                }else{
                    var pwrap = $(this).closest('.border-left');
                    pwrap.find('.form-group').removeClass('has-error');
                    pwrap.find('.has-feedback').empty();
                    $('#permanent_thana_btn').hide();
                    $('#permanent_thana_btn').parent().prepend('<span class="thana">Thana</span>');


                    $('#permanent_address_line').val(pal).attr('readonly', 'readonly');
                    $('#permanent_district').val(pdist).attr('readonly', 'readonly');
                    $('#permanent_thana').val(pt).attr('readonly', 'readonly');
                    $('#permanent_po').val(post).attr('readonly', 'readonly');
                    $('#permanent_post_code').val(pcode).attr('readonly', 'readonly');
                    $('#permanent_village').val(pv).attr('readonly', 'readonly');
                    $('#permanent_district').multiselect("rebuild");
                    $('#permanent_district').closest(".form-group").find('.multiselect').addClass('disabled');
                    $("#basicForm").validator('update');
                }
            }else{
                $('#permanent_thana_btn').show();
                $('#permanent_thana_btn').parent().find('.thana').remove();
                $('#permanent_address_line').val('').removeAttr("readonly");
                $('#permanent_district').val('').removeAttr("readonly");
                $('#permanent_thana').val('').removeAttr("readonly");
                $('#permanent_po').val('').removeAttr("readonly");
                $('#permanent_post_code').val('').removeAttr("readonly");
                $('#permanent_village').val('').removeAttr("readonly");
                $('#permanent_district').multiselect("rebuild");
                $("#basicForm").validator('update');
            }
        })





    </script>
@endsection
