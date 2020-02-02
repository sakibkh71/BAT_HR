<table id="employee_list"
       class="checkbox-clickable table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th rowspan="2">SL No.</th>

        <th rowspan="2">Distributor Point</th>
        <th rowspan="2">Employee Name</th>
        <th rowspan="2">Employee Code</th>
        <th class="text-center" colspan="3">Attendance</th>
        <th class="text-center" colspan="{{count($salary_component)+2}}">Fixed Salary</th>
        <th class="text-center" colspan="3">PFP Salary</th>
        <th rowspan="2">Net Salary</th>
    </tr>
    <tr>
        <th>Present Days</th>
        <th>Leave Days</th>
        <th>Absent Days</th>
        <th>Basic</th>
        @if(!empty($salary_component))
            @foreach($salary_component as $component)
                <th>{{__lang($component->component_slug)}}</th>
            @endforeach
        @endif
        <th>Total</th>
        <th>Target</th>
        <th>Achieve</th>
        <th>End of Month (Approx.)</th>

    </tr>

    </thead>
    <tbody>
    @if(!empty($employeeList))
        @foreach($employeeList as $i=>$emp)
            <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                <td align="center">
                    {{($i+1)}}
                </td>

                <td>{{@$emp->point_name}}</td>
                <td>{{$emp->name}}</td>
                <td>{{$emp->user_code}}</td>
                {{--<td>{{$emp->hr_emp_grade_name}}</td>--}}
                <td class="text-right">{{number_format($emp->present_days,1)}}</td>
                <td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>
                <td class="text-right">{{number_format($emp->absent_days,1)}}</td>
                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                @if(!empty($salary_component))
                    @foreach($salary_component as $component)
                        @php($slug_name = $component->component_slug)
                        <td class="text-right">{{number_format($emp->$slug_name,2)}}</td>
                    @endforeach
                @endif
@php($pfp_today_achievement = pfp_today_achievement($emp->target_variable_salary,$emp->pfp_achievement))

                <td class="text-right">{{number_format($emp->gross,2)}}</td>
                <td class="text-right">{{number_format($emp->target_variable_salary,2)}}</td>
                <td class="text-right">{{number_format(($emp->target_variable_salary*$emp->pfp_achievement)/100,2)}}</td>
                <td class="text-right">{{number_format($pfp_today_achievement,2)}}</td>
                <td class="text-right">{{number_format($emp->gross+$pfp_today_achievement,2)}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>