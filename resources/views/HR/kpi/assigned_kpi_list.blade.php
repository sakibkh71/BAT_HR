@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Assigned Kpi List</h2>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('assigned-kpi-list')}}" method="post" id="assigned_kpi" class="" role="form">
                             <div class="row">
                                 @csrf
                                 <div class = "col-md-2">
                                     <label class="font-normal">Distributor Point</label>
                                     <div class="form-group">
                                     <!-- <input type = "text" class = "form-control" id = "name" placeholder = "Enter Full Name"> property_id target_month_val-->
                                        <select class = "form-control" name="target_month" id="target_month" required="" >
                                            @if(!empty($month_ary))
                                                @foreach($month_ary as $month)
                                                    <option @if($month == $target_month_val) selected="" @endif value="{{$month}}">{{$month}}</option>
                                                @endforeach
                                            @else
                                                <option value="">NO Data</option>
                                            @endif
                                        </select>
                                     </div>
                                 </div>
                                 <div class="col-md-4">
                                    <label class="font-normal">Distributor Point</label>
                                    {{__combo('bat_distributor_point_all', ['multiple' => 0, 'selected_value' =>$point, 'name'=> 'change_point',
                                    'attributes' =>['class'=>'change_point form-control','id'=>'change_point']])}}
                                 </div>
                                 <div class="col-md-2">
                                    <label class="font-normal">FF Type</label>
                                    <div class="form-group">
                                        <select name="change_designation_id" id="change_designation_id" class="form-control">
                                            <option value="152" @if($designation_id == 152) selected="selected" @endif>SR</option>
                                            <option value="151" @if($designation_id == 151) selected="selected" @endif>SS</option>
                                        </select>
                                    </div>
                                 </div>
                                 <div class="col-md-4">
                                     <button type = "submit" class = "btn btn-default" style="margin-left: 2px;margin-top: 28px;">Search</button>
                                     <button type="button" id="makeExcel" class="btn btn-success btn" style="margin-top: 28px;"><i class="fa fa-file-excel-o"></i> {{__lang('Excel')}}</button>
                                     {{--<button type="button" class="btn btn-warning  item_edit" id="pfp_salary_edit" style="display: none;margin-top: 28px;" >--}}
                                         {{--<i class="fa fa-edit" aria-hidden="true"></i> Edit PFP Salary</button>--}}
                                 </div>
                             </div>
                        </form>

                        <!-- <div class="col-sm-12">
                            <button class="btn btn-xs" id="show-custom-search"><i class="fa fa-search"></i> show search</button>
                            {{--{!! __getMasterGrid('kpi-assign-target') !!}--}}
                        </div> -->
                        <div style="overflow-x:auto; margin-top: 20px; margin-left: 10px;">
                            @if(!empty($employee_ary))
                                <table id="assign_kpi" class=" table  table-bordered ">
                                    <thead>
                                        <tr>
                                            <th rowspan="4" class="text-center">SL</th>
                                            <th rowspan="4" class="text-center">Employee Code</th>
                                            <th rowspan="4" class="text-center">Employee Name</th>
                                            <th rowspan="4" class="text-center">House Name</th>
                                            <th rowspan="4"  class="text-center">PFP Salary</th>
                                            <th rowspan="4" class="text-center">Total Achievement</th>

                                            @php($count_row_kpi = 0)

                                            @foreach($product_ary_property as $target_key=>$target_val)
                                                @foreach($target_val as $prod_key=>$pro_val)
                                                    @if($prod_key != 'weight')
                                                        @php($count_row_kpi += count($pro_val))
                                                    @endif
                                                @endforeach
                                                <th colspan="{{$count_row_kpi*2}}" class="text-center">
                                                    {{$target_key."(".number_format($target_val['weight'])."%)"}}
                                                </th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($product_ary_property as $target_key=>$target_val)
                                                @foreach($target_val as $prod_key=>$pro_val)
                                                    @if($prod_key != 'weight')
                                                        <th colspan="{{count($pro_val)*2}}" class="text-center">{{$prod_key}}</th>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($product_ary_property as $target_key=>$target_val)
                                                @foreach($target_val as $prod_key=>$pro_val)
                                                    @if($prod_key != 'weight')
                                                        @foreach($pro_val as $key=>$val)
                                                            <th colspan="2" class="text-center">{{$val->product_name}}</th>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($product_ary_property as $target_key=>$target_val)
                                                @foreach($target_val as $prod_key=>$pro_val)
                                                    @if($prod_key != 'weight')
                                                        @foreach($pro_val as $key=>$val)
                                                            <th>Target</th>
                                                            <th>Achievement</th>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>
                                    @php( $sl=1)
                                    <tbody>
                                        @foreach($employee_ary as $info)
                                        <tr class="row-select-toggle" id="{{$info['user_code']}}">
                                            <input type="hidden" id="sys_users_id_{{$info['user_code']}}" value="{{$info['sys_users_id']}}" />
                                            <td>{{$sl++}}</td>
                                            <td>{{$info['user_code']}}</td>
                                            <td id="name_{{$info['user_code']}}">{{$info['user_name']}}</td>
                                            <td>{{$info['company_name']}}</td>
                                            @php( $variable_salary= !empty($info['hr_emp_variable_salary'])? $info['hr_emp_variable_salary']: $info['variable_salary'])
                                            <td id="pfp_salary_{{$info['user_code']}}" class="text-right">{{number_format($variable_salary,2)}}</td>

                                            @php( $total_achievement_sum = 0)
                                            @foreach($product_ary_property as $key=>$prows)
                                                @if(!empty($info[$key]))
                                                    @foreach($info[$key] as $key=>$val)
                                                        @if($key == 'total_achievement')
                                                            @php( $total_achievement_sum = $total_achievement_sum + (float)$val )
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach

                                            <td class="text-right">{{number_format($total_achievement_sum*100,2)}}%</td>

                                            @foreach($product_ary_property as $key=>$prows)
                                                @if(!empty($info[$key]))
                                                    @foreach($info[$key] as $key=>$val)
                                                        @if($key != 'total_achievement' && $key != 'target_type')
                                                            <td class="text-right">{{number_format($val['target'],2)}}</td>
                                                            <td class="text-right">{{empty($val['achievement'])?0:number_format($val['achievement'],2)}}</td>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                {{"No data found!"}}
                            @endif

                        </div>
                        <div class="modal fade" id="pfpSalaryModal" tabindex="-1" role="dialog"  aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content" id="pfpSalaryContent">
                                    <!--Modal view Loaded -->

                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modal_title"></h4>
                                        <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                                                    class="sr-only">Close</span></button>

                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <br>
                                                <form class="" method="post" id="variable_salary_form" name="pfp_salary_form" autocomplete="off">
                                                    <div class="row user_found">
                                                        {{--@if(isset($emp_vsalary))--}}
                                                            {{--<input type="hidden" name="vsalary_id" id="vsalary_id" class="" value="{{$emp_vsalary->vsalary_id}}"/>--}}
                                                        {{--@endif--}}
                                                        <input type="hidden" name="sys_user_id" class="employee_id" id="employee_id" value=""/>
                                                        {{csrf_field()}}
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Month <span class="required">*</span></label>
                                                                <div class="input-group">
                                                                    <input  type="text" id="month"
                                                                           placeholder=""
                                                                           class="form-control"

                                                                           id="vsalary_month"
                                                                           name="vsalary_month" required/>
                                                                    <div class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Variable Salary Amount <span class="required">*</span></label>
                                                                <div class="input-group">
                                                                    <div class="input-group-addon">
                                                                        <i class="fa fa-money"></i>
                                                                    </div>
                                                                    <input type="number" id="pfp_id"
                                                                           placeholder=""
                                                                           class="form-control"

                                                                           id="variable_salary_amount"
                                                                           name="variable_salary_amount" required/>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </form>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" id="variable_salary_submit" class="btn btn-primary">Save</button>
                                            <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
                                        </div>
                                    </div>
                                    <script>
                                        $('#vsalary_month').datetimepicker({
                                            format: 'YYYY-MM'

                                        });
                                    </script>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<script>
    $(document).ready(function(){
        $('#assign_kpi').DataTable();
    });


    $("#makeExcel").click(function () {
        var $form = $('#assigned_kpi');
        var data={};
        data = $form.serialize() + '&' + $.param(data);
        var url='{{route("assigned-kpi-list",['type'=>'excel'])}}';
        $.ajax({
            type:'get',
            url:url,
            data:data,
            success:function (data) {
                console.log(data);
                window.location.href = './public/export/' + data.file;
                swalSuccess('Export Successfully');
            }
        });
    });

    $('#pfp_salary_edit').click(function () {
        var url = '<?php echo URL::to('get-pfp-salary-form');?>';
        var data = {};
        var month=$('#target_month').val();
        var pfp_amount='';
        var name='';
        var sys_users_id;
        var arr_length = selected.length;
        if (arr_length == 1) {
            var selected_val=$('#pfp_salary_'+selected[0]);
                pfp_amount=selected_val.html();
                name=$('#name_'+selected[0]).html();
                sys_users_id=$('#sys_users_id_'+selected[0]).val();
        }
        var modal_title_html='Variable Salary Entry - '+name;
        $('#modal_title').html(modal_title_html);
        $('#month').val(month);
        $('#month').prop( "disabled", true );
        $('#pfp_id').val(pfp_amount);
        $('#employee_id').val(sys_users_id);
        $('#pfpSalaryModal').modal('show');

    });

    $(document).on('click','#variable_salary_submit', function () {

        var data={
            'sys_users_id':$('#employee_id').val(),
            'month':$('#month').val(),
            'pfp_amount':$('#pfp_id').val()
        };

        var url='{{route('insert-monthly-variable-salary')}}';
        $.ajax({
            'type':'get',
            'url':url,
            'data':data,
            success:function (result) {
                console.log(result);
                if(result.success){

                    $('#pfp_salary_'+result.return_data.user_code).html(result.return_data.variable_salary_amount);
                    swalSuccess(result.message);
                }
            }
        })


    });

    var selected = [];

    $(document).on('click','#assign_kpi tbody tr', function () {
        var self = $(this);
        var id = self.attr('id');
        if ($(this).toggleClass('selected')) {
            if ($(this).hasClass('selected')) {
                selected.push(id);
                self.find('input[type=checkbox]').prop("checked", true);
            } else {
                selected.splice(selected.indexOf(id), 1);
                self.find('input[type=checkbox]').prop("checked", false);
            }

            var arr_length = selected.length;
            if (arr_length > 1) {
                $('#pfp_salary_edit').hide();
            }
            else if (arr_length == 1) {
                $('#pfp_salary_edit').show();
            }
            else {
                $('#pfp_salary_edit').hide();
            }
        }
        console.log(selected);
    });

</script>
@endsection
