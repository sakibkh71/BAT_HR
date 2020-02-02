<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>

<table id="employee_promotion_list" class="table data-table table-bordered table-hover">
    <thead>
        <tr>
            <th rowspan="2" class="align-middle text-nowrap">Employee Names</th>
            <th rowspan="2" class="align-middle text-nowrap">Code</th>
            <th rowspan="2" class="align-middle text-nowrap">Joining Date</th>
            <th colspan="4" class="text-center text-nowrap">Current Position</th>
            <th colspan="4" class="text-center text-nowrap">Proposed Position</th>
            <th rowspan="2" class="align-middle text-nowrap"></th>
        </tr>
        <tr>
            <th class="align-middle text-nowrap">Point</th>
            <th class="align-middle text-nowrap">Designation</th>
            <th class="align-middle text-nowrap">Basic</th>
            <th class="align-middle text-nowrap">Gross Total</th>

            <th class="align-middle text-nowrap" width="130px">Point</th>
            <th class="align-middle text-nowrap" width="100px">Designation</th>
            <th class="align-middle text-nowrap" width="70px"> Increment Amount</th>
            <th class="align-middle text-nowrap" width="100px">Gross Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $emp_item = '';
        foreach ($employeeInfo as $key => $item) {
                $increment_amount = isset($item->increment_amount)?$item->increment_amount:0;
                $new_gross = isset($item->gross_salary)?$item->gross_salary:$item->min_gross;
                $new_designation = isset($item->log_designations_id)?$item->log_designations_id:$item->designations_id;
                $new_grades = isset($item->log_hr_emp_grades_id)?$item->log_hr_emp_grades_id:$item->hr_emp_grades_id;
                $new_point = isset($item->log_bat_dpid)?$item->log_bat_dpid:$item->bat_dpid;
        ?>
            <tr class="promotion_item" data-emp_id="{{$item->id??''}}" data-log_id="{{$item->hr_employee_record_logs_id??''}}">
                <td class="text-left"><input type="hidden" name="emp_id" class="emp_id"  value="{{$item->id}}">{{$item->name}}</td>
                <td class="text-left">{{$item->user_code}}</td>
                <td class="text-left">{{toDated($item->date_of_join)}}</td>
                <td class="text-left">{{$item->point_name}}</td>
                <td class="text-left">{{$item->designations_name}}</td>
                <td class="text-right min_basic" data-amount="{{$item->basic_salary}}">{{number_format($item->basic_salary,2)}}</td>
                <td class="text-right min_gross" data-amount="{{$item->min_gross}}">{{ number_format($item->min_gross,2)}}</td>

                <td class="text-left"><div class="form-group">{{ __combo('bat_dp_for_promotion',array('selected_value'=> $new_point, 'attributes'=> array('class'=>'form-control new_emp_point',  'required'=>true, 'name'=>'new_emp_point[]')))}}</div></td>
                <td class="text-left"><div class="form-group">{{ __combo('designations',array('selected_value'=>$new_designation, 'attributes'=> array('class'=>'form-control new_designations', 'required'=>true, 'name'=>'new_designations[]')))}}</div></td>
                <td class="text-right"><div class="form-group"><input type="text" name="increment_amount" class="form-control increment_amount text-right number" value="{{$increment_amount??''}}" required></div></td>
                <td class="text-right"><div class="form-group"><input type="text" name="new_gross_salary" class="form-control new_gross_salary text-right number" value="{{$new_gross??''}}"  required></div></td>
                <td class="text-right"><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash"></i> </button> </td>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<script>
$('#employee_promotion_list').dataTable({"pageLength": 200});
</script>
