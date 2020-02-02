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
                        <h2>Employee List</h2>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-primary btn-xs" style="display: none" id="view_employee_log"><i class="fa fa-eye"></i> Employee History</button>

                        </div>
                    </div>


                    <div class="ibox-content">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="employee_increment_list" class="checkbox-clickable table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2"></th>
                                        <th rowspan="2">Employee Name</th>
                                        <th rowspan="2">Department</th>
                                        <th rowspan="2">Designation</th>
                                        <th rowspan="2">Grade</th>
                                        <th rowspan="2">Applicable Date</th>
                                        <th colspan="7" class="text-center">Current Salary</th>
                                    </tr>
                                    <tr>
                                        <th>Basic</th>
                                        <th>House Rent</th>
                                        <th>Medical</th>
                                        <th>Food</th>
                                        <th>TA DA</th>
                                        <th>Gross Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($employeeList))
                                        @foreach($employeeList as $i=>$emp)
                                            <tr id="{{$emp->id}}">
                                                <td align="center">
                                                    {{($i+1)}}
                                                </td>
                                                <td>{{$emp->name}}</td>
                                                <td>{{$emp->departments_name}}</td>
                                                <td>{{$emp->designations_name}}</td>
                                                <td>{{$emp->hr_emp_grade_name}}</td>
                                                <td>{{toDated($emp->applicable_date)}}</td>
                                                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                                                <td class="text-right">{{number_format($emp->house_rent_amount,2)}}</td>
                                                <td class="text-right">{{number_format($emp->min_medical,2)}}</td>
                                                <td class="text-right">{{number_format($emp->min_food,2)}}</td>
                                                <td class="text-right">{{number_format($emp->min_tada,2)}}</td>
                                                <td class="text-right">{{number_format($emp->min_gross,2)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="edit_id" style="display: none"></div>
    <style>
        .selected{
            background-color: green;
            color: #FFF;
        }
        .selected:hover{
            background-color: green !important;
            color: #FFF;
        }
    </style>
<script>
    $('#employee_increment_list').dataTable();
    var selected_emp = [];
    $(document).on('click','.checkbox-clickable tbody tr',function (e) {
        $obj = $(this);
        if(!$(this).attr('id')){
            return true;
        }
        $obj.toggleClass('selected');
        var id = $obj.attr('id');
        if ($obj.hasClass( "selected" )){

            $obj.find('input[type=checkbox]').prop( "checked", true );

            selected_emp.push(id);


        }else{
            $obj.find('input[type=checkbox]').prop( "checked", false );
            var index = selected_emp.indexOf(id);
            selected_emp.splice(index,1);

        }
        $('#edit_id').text(selected_emp);
        // console.log(selected_emp);
        if(selected_emp.length==1){
            $('#view_employee_log').show();
            $('#add_link').hide();
        }else if(selected_emp.length==0){
            $('#add_link').show();
            $('#view_employee_log').hide();
        }else{
            $('#view_employee_log').hide();
            $('#add_link').hide();
        }
    });

    $(document).on('click','#view_employee_log', function (e) {
        Ladda.bind(this);
        var load = $(this).ladda();
        var emp_id = $('#edit_id').text();

        if (selected_emp.length == 1) {
            var url= '<?php echo URL::to('get-hr-emp-history');?>/'+emp_id;
            makeAjaxText(url,load).done(function (response) {
                $('#large_modal .modal-content').html(response);
                $('#large_modal').modal('show');
                load.ladda('stop');
            });

        } else {
            swalWarning("Please select single item");
            return false;

        }
    });
</script>
@endsection