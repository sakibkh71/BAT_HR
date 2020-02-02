@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{__lang('Salary Sheet')}}</h2>
                        <div class="ibox-tools">
{{--                            <a href="{{route('hr-create-new-salary-sheet')}}" id="create_salary_sheet" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> New Sheet</a>--}}
                            <button class="btn btn-success btn-xs no-display" id="send_for_approval_btn"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                            <button type="button" id="edit_sheet_name" class="btn btn-xs btn-warning no-display"><i class="fa fa-edit"></i> Edit Salary Sheet</button>
                            {{--<button id="generate_bank_advice" class="btn btn-primary btn-xs no-display"><span class="fa fa-recycle"></span> Generate Bank Advice</button>--}}
                            {{--<button id="show_bank_advice" class="btn btn-primary btn-xs no-display"><i class="fa fa-money"></i> View Bank Advice </button>--}}
                            {{--<button id="make_salary_disburse" class="btn btn-success btn-xs no-display"><i class="fa fa-money"></i> Salary Disburse </button>--}}
                            <button id="sheet_data" class="btn btn-primary btn-xs no-display"><i class="fa fa-eye"></i> View </button>
                            <button id="sheet_report" class="btn btn-success btn-xs no-display"><i class="fa fa-print"></i> Print </button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        {!! __getMasterGrid('hr-salary-sheet') !!}
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
    var sheet_code = [];

    $(document).on('click','#table-to-print tbody tr',function (e) {

        /*add this for new customize*/
        selected_row = [];
        sheet_status = [];
        sheet_code = [];
        $('#table-to-print tbody tr').not($(this)).removeClass('selected');
        /* end this */

        $(this).toggleClass('selected');
        var id = $(this).data('sheet_code');
        var status = $(this).data('salary_sheet_status');
        var code = $(this).data('sheet_code');
        if ($(this).hasClass( "selected" )){
            selected_row.push(id);
            sheet_status.push(status);
            sheet_code.push(code);
        }else{
            var index = selected_row.indexOf(id);
            var index2 = sheet_status.indexOf(status);
            selected_row.splice(index,1);
            sheet_status.splice(index2,1);
            sheet_code.splice(sheet_code.indexOf(code),1);
        }
        // console.log(sheet_status);
        actionManager(selected_row,sheet_status);
    });
    function actionManager(selected_row,sheet_status){
        if(selected_row.length < 1){
            $('#sheet_report, #sheet_data').hide();
            $('#generate_bank_advice, #edit_sheet_name,#make_salary_disburse').hide();
            $('#sheet_report, #show_bank_advice').hide();
            $('#send_for_approval_btn').hide();
        }else if(selected_row.length == 1){
            if( sheet_status == '92'){
                $('#generate_bank_advice, #edit_sheet_name,#make_salary_disburse').show();
                $('#send_for_approval_btn').show();
            }else{
                $('#show_bank_advice,#sheet_report').show();
                $('#send_for_approval_btn').hide();
            }
            $(' #sheet_data').show();
        }else if(selected_row.length >= 1){
            $('#send_for_approval_btn').hide();

            if(!sheet_status.includes(93) && !sep_status.includes(94)){
                $('#send_for_approval_btn').show();
            }else{
                $('#send_for_approval_btn').hide();
            }

        }else{
            $('#generate_bank_advice, #edit_sheet_name, #sheet_data,#make_salary_disburse').hide();
            $('#sheet_report').hide();
            $('#send_for_approval_btn').hide();
        }
    }
    $('#edit_sheet_name').on('click', function () {
        var url = '{{url('hr-create-new-salary-sheet')}}/'+selected_row[0];
        window.location.replace(url);
    });
    $('#generate_bank_advice').on('click', function () {
        var url = "{{URL::to('hr-emp-salary-sheet-bank-advice')}}/"+selected_row[0];
        swalConfirm("to Generate Salary Sheet Bank Advice").then(function (e) {
           if(e.value){
               window.location.replace(url);
           }
        });
    });

    $("#make_salary_disburse").on('click',function(){
       var url='{{route('make-salary-disburse')}}';
       var url2='{{route('hr-salary-sheet')}}';
       swalConfirm("to Make Salary Disburse").then(function(e){
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

    $('#sheet_report').on('click', function () {
        var sheet_url = "{{URL::to('hr-salary-wages-emp-list')}}/"+selected_row[0]+'/pdf';
        window.open(sheet_url,'_blank');
    });
    $('#sheet_data').on('click', function () {
        var sheet_url = "{{URL::to('hr-salary-wages-emp-list')}}/"+selected_row[0];
        window.location.replace(sheet_url);
    });


    $(document).on('click','#create_salary_sheet',function(){
        $.ajax({
            type: 'POST',
            cache: false,
            url: '{{url('hr-create-new-salary-sheet')}}',
            success: function (success) {
                $('#medium_modal .modal-content').html(success);
                $('#medium_modal').modal('show')
            }
        });

    });

    $(document).on('click', '#show_bank_advice', function () {
        var sheet_url = "{{URL::to('hr-emp-salary-sheet-bank-advice-pdf')}}/"+selected_row[0];
        window.open(sheet_url);
        return false;
    });


    //Send for approval
    $(document).on('click', '#send_for_approval_btn', function (e) {
        e.preventDefault();
        var id_slug = 'slry_code';
        var url = '<?php echo URL::to('salary-sheet-delegation-process');?>';
        var job_value = sheet_code;

        if(job_value.length){
            swalConfirm().then(function (e) {
                if(e.value){
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {slug:id_slug,code:job_value,'delegation_type':'send_for_approval'},
                        success: function (data) {
                            var url = window.location;
                            swalRedirect(url,data,'success');
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