<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
<table id="employee_increment_list" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th rowspan="2" class="align-middle">Employee Name</th>
        <th rowspan="2" class="align-middle">Code</th>
        <th rowspan="2" class="align-middle">Department</th>
        <th rowspan="2" class="align-middle">Current Designation</th>
        <th rowspan="2" class="align-middle">Current Grade</th>
        <th rowspan="2" class="align-middle">Cagetory</th>
        <th colspan="6" class="align-middle text-center">Current Salary</th>
        <th colspan="4" class="align-middle text-center">Proposed  Promotion Salary & Designation</th>
        <th rowspan="2"></th>
    </tr>
    <tr>
        <th class="align-middle">Basic</th>
        <th class="align-middle">House Rent</th>
        <th class="align-middle">Medical</th>
        <th class="align-middle">Food</th>
        <th class="align-middle">TA DA</th>
        <th class="align-middle">Gross Total</th>
        <th class="change_area2 align-middle text-nowrap">Proposed Designation</th>
        <th class="change_area2 align-middle text-center text-nowrap" style="min-width: 200px;">Proposed Grade</th>
        <th class="change_area2 align-middle text-nowrap">Proposed <br>Increment Amount</th>
        <th class="change_area2 align-middle text-nowrap">Proposed Gross Total</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($employeeInfo as $key => $item)
            <tr id="row{{$item->id}}" emp_id="{{$item->id}}">
            <td class="text-left"><input type="hidden" name="emp_id" class="emp_id" value="{{$item->id}}">{{$item->name}}</td>
            <td class="text-left">{{ $item->user_code}}</td>
            <td class="text-left">{{ $item->departments_name}}</td>
            <td class="text-left">{{$item->designations_name}}</td>
            <td class="text-left">{{ $item->hr_emp_grade_name}}</td>
            <td class="text-left">{{$item->hr_emp_category_name}}</td>
            <td class="text-right min_basic" data-amount="{{$item->basic_salary}}">{{number_format($item->basic_salary,2)}}</td>
            <td class="text-right house_rent_amount" data-amount="{{$item->house_rent_amount}}">{{number_format($item->house_rent_amount,2)}}</td>
            <td class="text-right min_medical" data-amount="{{$item->min_medical}}">{{number_format($item->min_medical,2)}}</td>
            <td class="text-right min_food" data-amount="{{$item->min_food}}">{{number_format($item->min_food,2)}}</td>
            <td class="text-right min_tada" data-amount="{{$item->min_tada}}">{{number_format($item->min_tada,2)}}</td>
            <td class="text-right min_gross" data-amount="{{$item->min_gross}}">{{ number_format($item->min_gross,2)}}</td>
            <td class="text-left change_area2"><div class="form-group"> {{ __combo('designations', array('selected_value' => ''/*$item->designations_id*/,'attributes'=>array('class'=>'form-control multi new_designation','required'=>'required')))}}</div></td>
            <td class="text-left change_area2"><div class="form-group">{{__combo('salary_grade', array('selected_value' =>'' /*$item->hr_emp_grades_id*/,'attributes'=>array('class'=>'form-control multi new_salary_grade')))}}</div></td>
            <td class="text-right change_area2"><div class="form-group"><input type="number" name="increment_amount" class="form-control increment_amount" required value=""></div></td>
            <td class="text-right change_area2"><div class="form-group"><input type="number" name="new_gross_salary" class="form-control new_gross_salary" required value="{{--{{$item->min_gross}}--}}"></div></td>
            <td class="text-right"><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash"></i> </button> </td>
        @endforeach
    </tbody>
</table>
<script>
    $('#employee_increment_list').dataTable();
</script>
