<table id="employee_increment_list" class="table data-table table-bordered table-hover">
    <thead>
        <tr>
            <th rowspan="2" class="align-middle text-nowrap">Employee Names</th>
            <th rowspan="2" class="align-middle text-nowrap">Code</th>            
            <th rowspan="2" class="align-middle text-nowrap">Designation</th>           
            <th rowspan="2" class="align-middle text-nowrap">Joining Date</th>
            <th colspan="2" class="text-center text-nowrap">Current Salary</th>
            <th colspan="3" class="text-center text-nowrap">Proposed Increment Salary</th>
            <th rowspan="2" class="align-middle text-nowrap"></th>
        </tr>
        <tr>
            <th class="align-middle text-nowrap">Basic</th>            
            <th class="align-middle text-nowrap">Gross Total</th>
            <th class="align-middle text-nowrap" width="80px">Ratio(%)</th>
            <th class="align-middle text-nowrap" width="80px">Total Amount</th>
            <th class="align-middle text-nowrap" width="80px">Gross Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $ratio = 0;
        $emp_item = '';
        foreach ($employeeInfo as $key => $item) {
            if( isset($item->gross_salary) && isset($item->previous_gross)){
                $ratio = ($item->gross_salary - $item->previous_gross) * 100 / $item->previous_gross;
            } elseif(!isset($post_data['increment_ratio']) || $post_data['increment_ratio'] == '') {
                $ratio = '';
            } elseif ($post_data['increment_ratio'] == 0) {
                if ($item->default_salary_applied == 1) {
                    $ratio = ($item->grade_yearly_increment / 100);
                } elseif ($item->default_salary_applied != 1 && $item->yearly_increment != '') {
                    $ratio = ($item->yearly_increment / 100);
                } else {
                    $ratio = ($item->grade_yearly_increment / 100);
                }
            } else {
                $ratio = ($post_data['increment_ratio']);
            }

            if (!isset($post_data['based_on']) || $post_data['based_on'] == 'gross') {
                $increment_amount = $ratio == '' ? '' : (number_format($item->min_gross * $ratio, 2));
                $new_gross = $ratio == '' ? '' : (number_format($item->min_gross + ($item->min_gross * $ratio), 2));
            } else {
                $increment_amount = $ratio == '' ? '' : (number_format($item->basic_salary * $ratio, 2));
                $new_gross = $ratio == '' ? '' : (number_format($item->min_gross + ($item->basic_salary * $ratio), 2));
            }

            if(isset($item->gross_salary)){
                $new_gross = $item->gross_salary;
            }

            if(isset($item->increment_amount)){
                $increment_amount = $item->increment_amount;
            }
        ?>
            <tr class="increment_item" emp_id="{{$item->id}}" data-log_id="{{$item->hr_employee_record_logs_id??''}}">
                <td class="text-left"><input type="hidden" name="emp_id" class="emp_id"  value="{{$item->id}}">{{$item->name}}</td>
                <td class="text-left">{{$item->user_code}}</td>       
                <td class="text-left">{{$item->designations_name}}</td>        
                <td class="text-left">{{toDated($item->date_of_join)}}</td>
                <td class="text-right min_basic" data-amount="{{$item->basic_salary}}">{{number_format($item->basic_salary,2)}}</td>    

                <td class="text-right min_gross" data-amount="{{$item->min_gross}}">{{ number_format($item->min_gross,2)}}</td>
                <td class="text-right"  width="130px"><div class="form-group"><input type="text" name="increment_ratio" class="form-control increment_ratio text-right number" value="{{($ratio!=''?$ratio:'')}}" required></div></td>
                <td class="text-right"  width="130px"><div class="form-group"><input type="text" name="increment_amount" class="form-control increment_amount text-right number" value="{{$increment_amount}}" required> </div></td>
                <td class="text-right"  width="130px"><div class="form-group"><input type="text" name="new_gross_salary" class="form-control new_gross_salary text-right number" value="{{$new_gross}}" required></div></td>
                <td class="text-right"  width="30px"><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash"></i> </button> </td>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<script>
$('#employee_increment_list').dataTable({"pageLength": 200});
</script>
