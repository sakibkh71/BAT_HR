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
            <td colspan="6" class="text-center" style="vertical-align: middle;">Current Position</td>
            <td colspan="5" class="text-center" style="vertical-align: middle;">Proposed Position</td>
        </tr>
        <tr>
            <td class="align-middle text-center">Category</td>
            <td class="align-middle text-center">Branch</td>
            <td class="align-middle text-center">Department</td>
            <td class="align-middle text-center">Section</td>
            <td class="align-middle text-center">Unit</td>
            <td class="align-middle text-center">Designation</td>
            <td class="align-middle text-center">Proposed Branch</td>
            <td class="align-middle text-center">Proposed Department</td>
            <td class="align-middle text-center">Proposed Section</td>
            <td class="align-middle text-center">Proposed Unit</td>
            <td class="align-middle text-center">Proposed Designation</td>
        </tr>

        </thead>
        <tbody>
        @foreach ($employeeList as $key => $item)
            <tr>
                <td>{{++$key}}</td>
                <td>{{$item->name??'N/A'}} {{!empty($item->user_code)?'('.$item->user_code.')':'N/A'}}</td>
                <td>{{$item->hr_emp_category_name??'N/A'}}</td>
                <td>{{$item->branchs_name??'N/A'}}</td>
                <td>{{$item->departments_name??'N/A'}}</td>
                <td>{{$item->hr_emp_section_name??'N/A'}}</td>
                <td>{{$item->hr_emp_unit_name??'N/A'}}</td>
                <td>{{$item->designations_name??'N/A'}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
<br><br><br>
@include('HR.hr_default_signing_block')