<style>
    h4{
        font-size:1em;
    }
    h5{
        font-size:0.8em;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    thead tr td{
        background:#e7e7e7;
        font-size: 0.8em;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:0.8em;
        font-weight: 300;
        padding: 5px 10px;
    }
    strong{
        font-weight: bold;
    }
</style>

<table>
    <tr>
        <td width="20%"><strong>Branch</strong></td>
        <td>: {{isset($branchs)?$branchs:'N/A'}}</td>
        <td width="20%"><strong>Employee Category</strong></td>
        <td>: {{ isset($categories)?$categories:'N/A' }} </td>
    </tr>
    <tr>
        <td><strong>Eligible Month</strong></td>
        <td>: {{ isset($eligible_month) ? $eligible_month:'N/A' }} </td>
        <td><strong>Department</strong></td>
        <td>: {{isset($hr_emp_departments)?$hr_emp_departments:'N/A'}} </td>
    </tr>
</table>


@if(!empty($employeeList))
    <h4 class="mt-3 mb-2">  </h4>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <td rowspan="2" style="vertical-align: middle">#</td>
                <td rowspan="2" style="vertical-align: middle">Employee Name</td>
                <td rowspan="2" style="vertical-align: middle">Department</td>
                <td rowspan="2" style="vertical-align: middle">Designation</td>
                <td rowspan="2" style="vertical-align: middle">Category</td>
                <td rowspan="2" style="vertical-align: middle">Last<br>Increment Date</td>
                <td colspan="6" class="align-middle text-center">Current Salary</td>
                <td colspan="3" class="align-middle text-center">Proposed Increment Salary</td>
            </tr>
            <tr>
                <td style="vertical-align: middle">Basic</td>
                <td style="vertical-align: middle">House Rent</td>
                <td style="vertical-align: middle">Medical</td>
                <td style="vertical-align: middle">Food</td>
                <td style="vertical-align: middle">TA/DA</td>
                <td style="vertical-align: middle">Gross Total</td>
                <td style="vertical-align: middle">Proposed Ratio(%)</td>
                <td style="vertical-align: middle">Proposed Amount</td>
                <td style="vertical-align: middle">Proposed Gross Total</td>
            </tr>
        </thead>
        <tbody>
        @foreach ($employeeList as $key => $item)
            @php
                if(!isset($increment_ratio) || $increment_ratio == ''){
                    $ratio ='';
                }elseif($increment_ratio == 0) {
                    if ($item->default_salary_applied == 1) {
                        $ratio = ($item->grade_yearly_increment / 100);
                    } elseif ($item->default_salary_applied != 1 && $item->yearly_increment != '') {
                        $ratio = ($item->yearly_increment / 100);
                    } else {
                        $ratio = ($item->grade_yearly_increment / 100);
                    }
                } else {
                    $ratio = ($increment_ratio / 100);
                }
                $increment_amount = $ratio ==''?'':(number_format($item->basic_salary*$ratio,2));
                $new_gross = $ratio ==''?'':(number_format($item->min_gross+($item->basic_salary*$ratio),2));
            @endphp
            <tr>
                <td>{{++$key}}</td>
                <td>{{$item->name ?? 'N/A'}}  {{!empty($item->user_code)?'('. $item->user_code .')':'N/A'}}</td>
                <td>{{$item->departments_name}}</td>
                <td>{{$item->designations_name}}</td>
                <td>{{ $item->hr_emp_category_name }}</td>
                <td>{{toDated($item->applicable_date)}}</td>
                <td>{{ number_format($item->basic_salary,2)}}</td>
                <td>{{ number_format($item->house_rent_amount,2)}}</td>
                <td>{{number_format($item->min_medical,2)}}</td>
                <td>{{number_format($item->min_food,2)}}</td>
                <td>{{number_format($item->min_tada,2)}}</td>
                <td>{{number_format($item->min_gross,2)}}</td>
                <td>{{($ratio!=''?$ratio*100:'')}}</td>
                <td>{{$increment_amount}}</td>
                <td>{{$new_gross}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
<br><br><br>
@include('HR.hr_default_signing_block')