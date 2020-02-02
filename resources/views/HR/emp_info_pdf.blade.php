<table id="emp_info" widtd="100%" class="table borderless">
    <tr>
        <td><strong>Employee Name </strong></td>
        <td width="10px"> :</td>
        <td>{{$emp_log->name}}</td>
        <td widtd="100"></td>
        <td><strong>Designation </strong></td>
        <td width="10px"> :</td>
        <td>{{$emp_log->designations_name}}</td>
    </tr>
    <tr>
        <td><strong>Employee ID </strong></td>
        <td width="10px"> :</td>
        <td>{{$emp_log->user_code}}</td>
        <td widtd="100"></td>
        <td><strong>Distributor House </strong></td>
        <td width="10px"> :</td>
        <td>{{$emp_log->distributor_house}}</td>
    </tr>
    <tr>
        <td><strong>Date of Join </strong></td>
        <td width="10px"> :</td>
        <td>{{todated($emp_log->date_of_join)}}</td>
        <td widtd="100"></td>
        <td><strong>Distributor Point </strong></td>
        <td width="10px"> :</td>
        <td>{{$emp_log->distributor_point}}</td>
    </tr>
    {{--<tr>--}}
        {{--<td colspan="4"></td>--}}
        {{--<td><strong>Category </strong></td>--}}
        {{--<td width="10px"> :</td>--}}
        {{--<td>{{$emp_log->hr_emp_category_name}}</td>--}}
    {{--</tr>--}}

</table>
<style>
    .borderless td, .borderless th {
        border: none;
        padding: 3px;
    }
</style>