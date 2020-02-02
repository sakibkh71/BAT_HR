<table id="employee_list"
       class="table table-bordered">
    <thead>
    <tr>
        <td rowspan="2">SL No.</td>

        <td rowspan="2">Distributor Point</td>
        <td rowspan="2">Employee Name</td>
        <td rowspan="2">Employee Code</td>
        <td rowspan="2">Basic Salary</td>
        <td colspan="2" class="text-center">PF Amount</td>
        <td rowspan="2">Total PF Amount</td>
    </tr>
    <tr>
        <td>Employee Contribution</td>
        <td>Company Contribution</td>
    </tr>

    </thead>
    <tbody>
    @if(!empty($employeeList))
        @foreach($employeeList as $i=>$emp)
            <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                <td align="center">
                    {{($i+1)}}
                </td>

                <td>{{$emp->point_name}}</td>
                <td>{{$emp->name}}</td>
                <td>{{$emp->user_code}}</td>
                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                <td class="text-right">{{number_format($emp->pf_amount_employee,2)}}</td>
                <td class="text-right">{{number_format($emp->pf_amount_company,2)}}</td>
                <td class="text-right">{{number_format($emp->pf_amount_employee+$emp->pf_amount_company,2)}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>