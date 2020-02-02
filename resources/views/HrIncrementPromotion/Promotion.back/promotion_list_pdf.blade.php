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
        <td width="15%"><strong>Branch</strong></td>
        <td>: {{isset($branchs)? implode(" , ",$branchs) :'N/A'}}</td>
        <td width="20%"><strong>Employee Category</strong></td>
        <td>: {{ isset($categories)? implode(" , ",$categories) :'N/A' }} </td>
    </tr>
    <tr>
        <td><strong>Salary Grade</strong></td>
        <td>: {{ isset($grades) ? implode(" , ",$grades):'N/A' }} </td>
        <td><strong>Department</strong></td>
        <td>: {{isset($departments) ? implode(" , ",$departments):'N/A'}} </td>
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
            <td rowspan="2" style="vertical-align: middle">Grade</td>
            <td rowspan="2" style="vertical-align: middle">Category</td>
            <td colspan="6" style="vertical-align: middle">Current Salary</td>
            <td colspan="4" class="align-middle text-center">Proposed Promotion Salary & Designation</td>
        </tr>
        <tr>
            <td class="align-middle text-center">Basic</td>
            <td class="align-middle text-center">House Rent</td>
            <td class="align-middle text-center">Medical</td>
            <td class="align-middle text-center">Food</td>
            <td class="align-middle text-center">TA/DA</td>
            <td class="align-middle text-center">Gross Total</td>

            <td class="align-middle text-center">Proposed Designation</td>
            <td class="align-middle text-center">Proposed Grade</td>
            <td class="align-middle text-center">Proposed Increment Amount</td>
            <td class="align-middle text-center">Proposed Gross Total</td>
        </tr>

        </thead>
        <tbody>
        @foreach ($employeeList as $key => $item)
            <tr>
                <td>{{++$key}}</td>
                <td>{{$item->name??'N/A'}} {{!empty($item->user_code)?'('.$item->user_code.')':'N/A'}}</td>
                <td>{{$item->departments_name??'N/A'}}</td>
                <td>{{$item->designations_name??'N/A'}}</td>
                <td>{{$item->hr_emp_grade_name??'N/A'}}</td>
                <td>{{$item->hr_emp_category_name??'N/A'}}</td>
                <td>{{!empty($item->basic_salary)?number_format($item->basic_salary,2):'N/A'}}</td>
                <td>{{!empty($item->house_rent_amount)? number_format($item->house_rent_amount,2):'N/A'}}</td>
                <td>{{!empty($item->min_medical)? number_format($item->min_medical,2):'N/A'}}</td>
                <td>{{!empty($item->min_food)? number_format($item->min_food,2):'N/A'}}</td>
                <td>{{!empty($item->min_tada)? number_format($item->min_tada,2):'N/A'}}</td>
                <td>{{!empty($item->min_gross)? number_format($item->min_gross,2):'N/A'}}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
<br><br><br>
@include('HR.hr_default_signing_block')