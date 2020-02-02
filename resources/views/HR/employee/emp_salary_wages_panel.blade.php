<section class="ibox" id="transfer">
    <div class="ibox-title no-borders no-padding">
        <h2>Salary History</h2>
    </div>
    <div class="ibox-content no-padding">
        <div class="table-responsive">
            <table id="employee_list"
                   class="checkbox-clickable table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th rowspan="2">Salary Month</th>
                    <th rowspan="2">Distributor Point</th>
                    <th rowspan="2">Employee Name</th>
                    <th rowspan="2">Employee Code</th>
                    <th class="text-center" colspan="3">Attendance</th>
                    <th class="text-center" colspan="3">Fixed Salary</th>
                    <th class="text-center" colspan="3">PFP Salary</th>
                    <th rowspan="2">Net Salary</th>
                </tr>
                <tr>
                    <th>Present Days</th>
                    <th>Leave Days</th>
                    <th>Absent Days</th>
                    <th>Salary</th>
                    <th>(-)PF</th>
                    <th>Earn Salary</th>
                    <th>PFP Target</th>
                    <th>PFP Achieve</th>
                    <th>PFP Earn</th>
                </tr>

                </thead>
                <tbody>
                @if(!empty($employeeList))
                    @foreach($employeeList as $i=>$emp)
                        <tr class="row-select-toggle" id="{{$emp->sys_users_id}}">
                            <td align="center">
                                {{date('M-Y',strtotime($emp->hr_salary_month_name))}}
                            </td>

                            <td>{{$emp->point_name}}</td>
                            <td>{{$emp->name}}</td>
                            <td>{{$emp->user_code}}</td>
                            {{--<td>{{$emp->hr_emp_grade_name}}</td>--}}
                            <td class="text-right">{{number_format($emp->present_days,1)}}</td>
                            <td class="text-right">{{number_format($emp->number_of_leave,1)}}</td>
                            <td class="text-right">{{number_format($emp->absent_days,1)}}</td>
                            <td class="text-right">{{number_format($emp->gross,2)}}</td>
                            <td class="text-right">{{number_format($emp->pf_amount_employee,2)}}</td>
                            <td class="text-right">{{number_format($emp->net_payable,2)}}</td>
                            <td class="text-right">{{number_format($emp->pfp_target_amount,2)}}</td>
                            <td class="text-right">{{number_format($emp->pfp_achieve_ratio,2)}}%</td>
                            <td class="text-right">{{number_format($emp->pfp_earn_amount,2)}}</td>
                            <td class="text-right">{{number_format($emp->net_payable+$emp->pfp_earn_amount,2)}}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>


        </div>
    </div>
 </section>