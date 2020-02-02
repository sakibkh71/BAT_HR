<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
<table id="employee_transfer_list" class="table table-bordered table-hover apsis_table">
    <thead>
        <tr>
            <th rowspan="2" class="align-middle">Employee Name</th>
            <th rowspan="2" class="align-middle">Code</th>
            <th rowspan="2" class="align-middle">Category</th>
            <th colspan="6" class="text-center">Current Position</th>
            <th colspan="5" class="text-center change_area">Proposed Position</th>
            <th rowspan="2"></th>
        </tr>
        <tr>
            <th>Applicable Date</th>
            <th>Branch</th>
            <th>Department</th>
            <th>Section</th>
            <th>Unit</th>
            <th>Designation</th>
            <th class="change_area2 text-nowrap" style="min-width: 150px">Proposed Branch</th>
            <th class="change_area2 text-nowrap" style="min-width: 150px">Proposed Department</th>
            <th class="change_area2 text-nowrap" style="min-width: 150px">Proposed Section</th>
            <th class="change_area2 text-nowrap" style="min-width: 150px">Proposed Unit</th>
            <th class="change_area2 text-nowrap" style="min-width: 150px">Proposed Designation</th>
        </tr>
    </thead>
    <tbody>
    @if(!empty($employeeInfo))
        @foreach ($employeeInfo as $key => $item)
            <tr id="row{{$item->id}}" emp_id="{{$item->id}}" data-emp_id="{{$item->id}}">
                <td class="text-left"><input type="hidden" name="emp_id" class="emp_id" value="{{$item->id}}">{{$item->name}}</td>
                <td class="text-left">{{$item->user_code}}</td>
                <td class="text-left">{{$item->hr_emp_category_name}}</td>
                <td class="text-left">{{toDated($item->applicable_date)}}</td>
                <td class="text-left">{{$item->branchs_name}}</td>
                <td class="text-left">{{$item->departments_name}}</td>
                <td class="text-left">{{$item->hr_emp_section_name}}</td>
                <td class="text-left">{{$item->hr_emp_unit_name}}</td>
                <td class="text-left">{{$item->designations_name}}</td>
                <td class="text-left change_area2"><div class="form-group"> {{ __combo('branchs', array('selected_value' =>'','attributes'=>array('class'=>'form-control multi new_branchs','required'=>'required')))}} </div></td>
                <td class="text-left change_area2"><div class="form-group"> {{ __combo('departments', array('selected_value' => '','attributes'=>array('class'=>'form-control multi new_departments','required'=>'required')))}} </div></td>
                <td class="text-left change_area2"><div class="form-group"> {{ __combo('hr_emp_sections', array('selected_value' => '','attributes'=>array('class'=>'form-control multi new_hr_emp_sections','required'=>'required')))}} </div></td>
                <td class="text-left change_area2"><div class="form-group"> {{ __combo('hr_emp_units', array('selected_value' => '','attributes'=>array('class'=>'form-control multi new_hr_emp_units','required'=>'required')))}} </div></td>
                <td class="text-left change_area2"><div class="form-group"> {{ __combo('designations', array('selected_value' => '','attributes'=>array('class'=>'form-control multi new_designation','required'=>'required')))}} </div></td>
                <td class="text-right"><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash"></i> </button> </td>
        @endforeach
    @endif
    </tbody>
</table>
<script>
    $('apsis_table').dataTable();
</script>


