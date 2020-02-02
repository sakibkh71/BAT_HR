@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Bonus Report</h2>
                        <div class="ibox-tools">
                            <h2>Bonus Sheet: <b>{{@$bonus_sheet}}</b></h2>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <form method="post" action="" id="bonus_form"
                              data-toggle="validator">
                            <div class="row">
                                @csrf
                                {!! __getCustomSearch('eid-bonus-generate', @$posted) !!}

                                <div class="form-group col-md-3">
                                    <label class="form-label"></label>
                                    <div class="input-group">
                                        <button id="btn_add_employee_list" type="submit" class="btn btn-success btn-xs"><i class="fa fa-search"></i> Filter</button>
                                        @if($bonus_sheet_status ==108)
                                        &nbsp;<button type="button" id="makepdf" class="btn btn-warning btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                            @endif
                                    </div>

                                </div>
                            </div>
                        </form>
                            <?php echo $report_data_html; ?>
                    </div>



                        {{--<div class="col-md-12 mt-2">{{ $report_data->links() }}</div>--}}

                </div>
            </div>
        </div>
    </div>
    <script>
        $('#makepdf').click(function () {
            var form = $('#bonus_form');
            var action = window.location.href;
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

    </script>
@endsection