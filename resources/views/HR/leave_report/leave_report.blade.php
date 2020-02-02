@extends('layouts.app')
@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox">
                <div class="ibox-title">
                    <h2>Employee Leave Report</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <form action="{{route('hr-emp-leave-report')}}" id="leave-report_form" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group emp_code_entry">
                                            <label class="form-label">Select Employee</label>
                                            <div class="input-group">
                                                {{__combo('hr_leave_report_employee_encashment', array('selected_value'=>(isset($post_data['users']) ? $post_data['users'] : ''), 'attributes'=> array('class'=>'form-control multi','id'=>'users','name'=>'users')))}}
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 @if(!isset($post_data['users'])) no-display @endif" id="export_btn">
                                        <button class="btn btn-primary mt-4" type="button"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>  PDF Export</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(isset($post_data['users']))
                            <div class="col-md-12" id="employeeReport">
                                <div class="ibox" style="margin-bottom: 0;">
                                    <div class="ibox-title">
                                        <h4><i class="fa fa-user"></i> Employee Information</h4>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        {!! $emp_info ??'' !!}
                                    </div>
                                </div>
                                <div id="leave_summary">
                                    {!! $leave_summery ??'' !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .emp_code_entry .btn-group,.emp_code_entry button{
        width: 100% !important;
    }
</style>

@if (Session::has('success'))
    <script>
        $(document).ready(function(){
            var url = "{{URL::to('hr-emp-leave-report')}}";
            var mode = 'success';
            var msg = 'Success!';
            swalRedirect(url, msg, mode);
        });
    </script>
@endif

<script>
    $('#leave-report_form').validator();
    $('#users').multiselect({
        enableFiltering:true,
        maxHeight: 350,
        onChange: function (option, checked, select) {
            var user_code = $('#users').val();
            if(user_code.length === 0){
                swalError("Please Input a valid Employee Code");
                $('#export_btn').hide();
                $('#employeeReport').empty();
            }else{
                $('#export_btn').show();
                $('#leave-report_form').submit();
            }
        }
    });
    
    $('#export_btn').click(function () {
        $action = $('#leave-report_form').attr('action');
        $('#leave-report_form').attr('action', $action+'/pdf').attr('target',"_blank");
        $('#leave-report_form').submit();

        //After Submit remove attributes and action replace
        $('#leave-report_form').attr('action', $action).removeAttr("target");
    });
</script>
@endsection