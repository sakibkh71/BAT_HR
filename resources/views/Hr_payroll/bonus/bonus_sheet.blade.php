@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Bonus Sheet</h2>
                        <div class="ibox-tools">
                            <button class="btn btn-success btn-xs no-display" id="send_for_approval_btn"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                            {{--<button type="button" id="create_bonus_sheet" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> New Bonus Sheet</button>--}}
                            <button type="button" id="edit_sheet_name" class="btn btn-xs btn-warning no-display"><i class="fa fa-edit"></i> Edit Bonus Sheet</button>
                            {{--<button id="make_bonus_disburse" class="btn btn-success btn-xs no-display"><i class="fa fa-money"></i> Make Bonus Disburse </button>--}}
                            <button id="edit_sheet" class="btn btn-secondary btn-xs no-display"><span class="fa fa-edit"></span> Edit Employee Bonus</button>
                            <button id="sheet_report" class="btn btn-success btn-xs no-display"><i class="fa fa-eye"></i> View </button>
                            <button id="sheet_print" class="btn btn-print btn-xs no-display"><i class="fa fa-print"></i> Print </button>
                        </div>
                    </div>
                    <div class="ibox-content">
                                {!! __getMasterGrid('hr-bonus-sheet') !!}
                    </div>
                </div>
            </div>
    </div>
    </div>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    var selected_row = [];
    var sheet_status = [];
    var bonus_status = [];

    $("#make_bonus_disburse").click(function () {
        var url='{{route('make-bonus-disburse')}}';
        var url2='{{route('hr-emp-bonus-sheet')}}';
        swalConfirm("to Make Bonus Disburse").then(function(e){
            if(e.value){

                $.ajax({
                    type:'get',
                    data:{
                        id:selected_row[0],
                    },
                    url:url,
                    success:function(data){
                        // console.log(data);
                        window.location.replace(url2);
                    }
                });
            }
        });
    });

    $(document).on('click','#table-to-print tbody tr',function (e) {

        /*add this for new customize*/
        bonus_status = [];
        sheet_status = [];
        selected_row = [];
        $('#table-to-print tbody tr').not($(this)).removeClass('selected');
        /* end this */

        $(this).toggleClass('selected');
        var id = $(this).data('id');
        var status = $(this).data('status');
        var bns = $(this).data('bonus_status');

        if ($(this).hasClass( "selected" )){
            selected_row.push(id);
            sheet_status.push(status);
            bonus_status.push(bns);
        }else{
            var index = selected_row.indexOf(id);
            var index2 = sheet_status.indexOf(status);
            selected_row.splice(index,1);
            sheet_status.splice(index2,1);
            bonus_status.splice(bonus_status.indexOf(bns),1);
        }
        // console.log(sheet_status);
        actionManager(selected_row,sheet_status, bonus_status);
    });
    function actionManager(selected_row,sheet_status, bonus_status){
        if(selected_row.length < 1){

            $('#edit_sheet_name').hide();
            $('#edit_sheet').hide();
            $('#sheet_report').hide();
            $('#generate_bank_advice').hide();
            $('#make_bonus_disburse').hide();
            $('#send_for_approval_btn').hide();
            $('#sheet_print').hide();

        }else if(selected_row.length == 1){

            if( bonus_status[0] == 106){

                $('#edit_sheet').show();
                $('#edit_sheet_name').show();
                $('#send_for_approval_btn').show();
                $('#make_bonus_disburse').hide();
                $('#generate_bank_advice').hide();

            }else if(bonus_status[0] == 108){
                $('#sheet_print').show();
                $('#generate_bank_advice').show();
                $('#make_bonus_disburse').show();
                $('#send_for_approval_btn').hide();
            }else{
                $('#send_for_approval_btn').hide();
            }

            $('#sheet_report').show();

        }else{
            $('#sheet_print').hide();
            $('#edit_sheet').hide();
            $('#edit_sheet_name').hide();
            $('#sheet_report').hide();
            $('#generate_bank_advice').hide();
            $('#make_bonus_disburse').hide();
            $('#send_for_approval_btn').hide();
        }
    }


    $('#edit_sheet_name').on('click', function () {
        var url = "<?php echo URL::to('hr-create-new-sheet');?>/"+selected_row[0];

        swalConfirm("To edit this sheet").then(function (e) {
            if(e.value){
                window.location.replace(url);
            }
        });

        /*$.ajax({
            type: 'POST',
            cache: false,
            url: '<?php echo URL::to('hr-create-new-sheet');?>/'+selected_row[0],
            success: function (success) {
                $('#medium_modal .modal-content').html(success);
                $('#medium_modal').modal('show')
            }
        });*/
    });

    $('#generate_bank_advice').on('click',function () {
        var url = "{{URL::to('hr-emp-salary-sheet-bank-advice')}}/"+selected_row[0]+"/bonus";
        swalConfirm("to Generate Salary Sheet Bank Advice").then(function (e) {
            if(e.value){
                window.location.replace(url);
            }
        });
    });

    $('#edit_sheet').on('click', function () {
        var edit_url = "{{URL::to('hr-emp-bonus-sheet-data')}}/"+selected_row[0];
        window.location.replace(edit_url);
    });
    $('#sheet_report').on('click', function () {
        var sheet_url = "{{URL::to('hr-emp-bonus-report')}}/"+selected_row[0];
        window.location.replace(sheet_url);
    });
    $('#sheet_print').on('click', function () {
        var sheet_url = "{{URL::to('hr-emp-bonus-report')}}/"+selected_row[0]+'/pdf';
        window.open(sheet_url,'_blank');
    });

    $(document).on('click','#create_bonus_sheet',function(){
        var url = '{{url('hr-create-new-sheet')}}';
        window.location.href = url;
        
        /*$.ajax({
            type: 'POST',
            cache: false,
            url: '<?php echo URL::to('hr-create-new-sheet');?>',
            success: function (success) {
                $('#medium_modal .modal-content').html(success);
                $('#medium_modal').modal('show')
            }
        });*/

    });

    $(document).on('submit','#bonus_sheet_form',function(e){
        e.preventDefault();

        var data = $('#bonus_sheet_form').serialize(); var url = '{{route('hr-bonus-sheet-create-save')}}';
        makeAjaxPost(data,url,null).done(function(response){
            console.log(response);
            if(response.success){
                var edit_url = "{{URL::to('hr-emp-bonus-sheet-data')}}/"+response.bonus_sheet_code;
                swalRedirect(edit_url,'Successfully Save');

            }
        });
    });



    //Send for approval
    $(document).on('click', '#send_for_approval_btn', function (e) {
        e.preventDefault();
        var id_slug = 'hr_bonus';
        var url = '<?php echo URL::to('bonus-delegation-process');?>';
        var job_value = selected_row;

        if(job_value.length){
            swalConfirm().then(function (e) {
                if(e.value){
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {slug:id_slug,code:job_value,'delegation_type':'send_for_approval'},
                        success: function (data) {
                            var rurl = window.location;
                            swalRedirect(rurl,data,'success');
                        },
                        failure: function() {
                            swalError('Failed');
                        }
                    });
                }
            });
        }else{
            swalWarning("Please select at least one job!");
        }
    });
</script>
@endsection