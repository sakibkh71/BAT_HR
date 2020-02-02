@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{__lang('Salary Disbursement')}}</h2>
                        <div class="ibox-tools">
                            {{--<a href="{{route('hr-create-new-salary-sheet')}}" id="create_salary_sheet" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> New Sheet</a>--}}
                            {{--<button type="button" id="edit_sheet_name" class="btn btn-xs btn-danger no-display"><i class="fa fa-edit"></i> Edit Salary Sheet</button>--}}
                            <button id="generate_bank_advice" class="btn btn-primary btn-xs no-display"><span class="fa fa-recycle"></span> Generate Bank Advice</button>
                            <button id="show_bank_advice" class="btn btn-success btn-xs no-display"><i class="fa fa-money"></i> View Bank Advice </button>
                            <button id="sheet_data" class="btn btn-primary btn-xs no-display"><i class="fa fa-eye"></i> View Salary Sheet</button>
                            {{--<button id="sheet_report" class="btn btn-success btn-xs no-display"><i class="fa fa-print"></i> Print </button>--}}
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

        $(document).on('click','#table-to-print tbody tr',function (e) {
            /*add this for new customize*/
            selected_row = [];
            sheet_status = [];
            $('#table-to-print tbody tr').not($(this)).removeClass('selected');
            /* end this */

            $(this).toggleClass('selected');
            var id = $(this).data('sheet_code');
            var status = $(this).data('salary_sheet_status');

            if ($(this).hasClass( "selected" )){
                selected_row.push(id);
                sheet_status.push(status);
            }else{
                var index = selected_row.indexOf(id);
                var index2 = sheet_status.indexOf(status);
                selected_row.splice(index,1);
                sheet_status.splice(index2,1);
            }
            // console.log(sheet_status);
            actionManager(selected_row,sheet_status);
        });

        function actionManager(selected_row,sheet_status){
            if(selected_row.length < 1){
                $('#sheet_report, #sheet_data').hide();
                $('#generate_bank_advice, #edit_sheet_name').hide();
                $('#sheet_report, #show_bank_advice').hide();
            }else if(selected_row.length == 1){
                if( sheet_status == '92'||sheet_status == '93'){
                    $('#generate_bank_advice, #edit_sheet_name').show();
                }else{
                    $('#show_bank_advice').show();
                }
                $('#sheet_report, #sheet_data').show();
            }else{
                $('#generate_bank_advice, #edit_sheet_name, #sheet_data').hide();
                $('#sheet_report').hide();
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

    </script>
@endsection