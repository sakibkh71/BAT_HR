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
        font-size: 0.8rem;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:0.8em;
        font-weight: 300;
    }
    strong{
        font-weight: bold;
    }
    .underline{
        text-decoration: underline;
    }
    .ptb10{
        padding: 10px 0;
    }
    .pb5{
        padding-bottom: 5px;
    }
    .pt5{
        padding-top: 5px;
    }
    body, p, li{
        font-size: 0.9rem;
    }
    .font-bold{
        font-weight: bold;
    }
    .list-number{
        list-style: decimal;
    }
    .list-alpha{
        list-style: lower-alpha;
    }
    .dot-line{
        border-top:1px dotted #000;
        margin-top: 50px;
        padding-top: 5px;
        display:inline;
    }
    .row{
        display: block;
        width: 100%;
        overflow: hidden;
    }
    .col-md-6{
        float: left;
        width: 50%;
        margin: 0;
        padding: 0;
    }
</style>


<table>
    <tr>
        <td>Date :  <strong>{{ !empty($emp_log->applicable_date)?toDated($emp_log->applicable_date):''}}</strong></td>
        <td class="text-right">SRCIL/HRD/{{$emp_log->user_code}}/{{date('Y')}}</td>
    </tr>
</table>

<table class="mt-4">
    <tr>
        <td class="pb5 pt5">Name : <strong>{{$emp_log->name ?? 'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">ID No: <strong>{{$emp_log->user_code ?? 'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Designation: <strong>{{$emp_log->designations_name ??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Department: <strong>{{$emp_log->departments_name ??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Section: <strong>{{$emp_log->hr_emp_section_name ??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Unit: <strong>{{$emp_log->hr_emp_unit_name ??'N/A'}}</strong></td>
    </tr>
</table>

<h4 class="mt-4"><strong>Sub: Letter of Transfer.</strong></h4>

<br>
<p>Dear Mr./Mrs. <span>{{$emp_log->name}}</span>,</p>
<p>We are inform you that you will be receiving an Transfer notice .</p>
<p>We think you for your ongoing commitment to excellence at SR Chemical Industries Ltd., and congratulate you on your
    outstanding performance! </p>


<p>The Transfer will be effective as of <span>{{toDated($emp_log->applicable_date)}}</span>.</p>
<p>Your new Transfer Position</p>


<table class="mt-4">
    <tr>
        <td class="pb5 pt5">Branch: <strong> {{$emp_log->branchs_name}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Section:<strong>{{$emp_log->hr_emp_section_name}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Unit:<strong>{{$emp_log->hr_emp_unit_name}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Department:<strong>{{$emp_log->departments_name}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Section: <strong>{{$emp_log->hr_emp_section_name ??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Designation: <strong>{{$emp_log->designations_name}}</strong></td>
    </tr>
</table>

<p>Sincerely,</p>
