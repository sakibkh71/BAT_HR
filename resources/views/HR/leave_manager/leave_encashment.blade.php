@extends('layouts.app')
@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox">
                <div class="ibox-title">
                    <h2>Leave Encashment Entry</h2>
                    <div class="ibox-tools">
                        <a href="{{route('leave-encashment-history')}}" class="btn btn-primary btn-xs" id="new-item"><i class="fa  fa-list-ul" aria-hidden="true"></i> Encashment History</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <form action="{{route('leave-encashment-create')}}" id="encashment_user_form" method="post">
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

                                </div>
                            </form>
                        </div>
                        @if(isset($post_data['users']))
                        <div class="col-md-12">
                            <?php echo @$emp_info?>
                            <div class="row">
                                <div class="col-lg-6" id="cluster_info">
                                    <div class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>Encashment Balance:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{isset($encashment_records) ? $encashment_records->encashment_ballance_days : (isset($post_data['net_balance'])?$post_data['net_balance']:'') }} Days</dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            @if(isset($encashment_records->hr_leave_encashments_id))
                                <form action="{{route('leave-encashment-store', $encashment_records->hr_leave_encashments_id)}}" method="post" id="encashmentForm">
                            @else
                                <form action="{{route('leave-encashment-store')}}" method="post" id="encashmentForm">
                            @endif
                                <input type="hidden" name="user_id" id="user_id" value="{{isset($encashment_records) ? $encashment_records->sys_users_id : (isset($post_data['users'])?$post_data['users']:'')}}"/>
                                <input type="hidden" name="basic_salary" id="basic_salary" value="{{@$basic_salary??0}}"/>
                                <input type="hidden" name="net_balance" id="net_balance" value="{{isset($encashment_records) ? $encashment_records->encashment_ballance_days : (isset($post_data['net_balance'])?$post_data['net_balance']:0) }}"/>
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Appliacation Date</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control datepicker"
                                                       value="{{date('Y-m-d')}}"
                                                       id="encashment_date"
                                                       name="encashment_date" required/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Encashment Days</label>
                                            <div class="input-group ">
                                                <input type="text" class="form-control" value="{{isset($encashment_records) ? $encashment_records->encashment_days : ''}}"  id="encashment_days" name="encashment_days" required/>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label">Encashment Note</label>
                                            <div class="input-group">
                                                <textarea class="form-control" name="encashment_note" rows="2">{{isset($encashment_records) ? $encashment_records->encashment_note : ''}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-1">
                                        <button id="encash_submit" class="btn btn-success" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
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
            var url = "{{URL::to('leave-encashment-history')}}";
            var mode = 'success';
            var msg = 'Success!';
            swalRedirect(url, msg, mode);
        });
    </script>
@endif

<script>
    $('#encashmentForm').validator();
    $('#users').multiselect({
        enableFiltering:true,
        maxHeight: 350,
        onChange: function (option, checked, select) {
            var user_code = $('#users').val();
            if(user_code.length === 0){
                swalError("Please Input a valid Employee Code");
            }else{
                $('#encashment_user_form').submit();
            }
        }
    });

    $('#encashment_days').keypress(function(eve) {
        if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
            eve.preventDefault();
        }
    });

    $('#encashment_days').keyup(function(eve) {
        var net_balance = parseFloat($('#net_balance').val());
        var datval = parseFloat($(this).val());
        if (datval > net_balance){
            swalError('Sorry! you can take maximum ' +net_balance+ ' Days Encashment');
            $(this).val('');
        }
    });

</script>
@endsection