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
        <td class="text-right">BAT/HRD/{{$emp_log->user_code}}/{{date('Y')}}</td>
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
        <td class="pb5 pt5">Company: <strong>{{$emp_log->company_name ??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td class="pb5 pt5">Point: <strong>{{$emp_log->name ??'N/A'}}</strong></td>
    </tr>

</table>

<h4 class="mt-4"><strong>Sub: Letter of Increment.</strong></h4>
<p class="mt-4"> Dear <strong>{{$emp_log->name ?? 'N/A'}}</strong>, <br>
    We are very pleased to inform you that the management has decided to Increase your salary on your performance as <strong>{{$emp_log->designations_name ??'N/A'}}</strong> under Point of <strong>{{$emp_log->point_name ??'N/A'}}</strong> in {{$emp_log->company_name}}  with effect from <strong>{{!empty($emp_log->applicable_date)?toDated($emp_log->applicable_date):'N/A'}}.</strong></p>

@php( $increment = $emp_log->gross_salary - $emp_log->previous_gross )

<p>
    Your present salary Calculation will be :<br>
    Previous Salary: <strong>{{apsis_money($emp_log->previous_gross)}}</strong><br>
    Increase Salary: <strong>{{apsis_money($increment)}}</strong><br>
    Present Salary: <strong>{{apsis_money($emp_log->gross_salary)}}</strong><br>

</p>

<p>We are confident that you shall maintain the standard of good performance that you have
    already attained and uphold the zeal and enthusiasm to the highest level. Your participation will enhance SR Chemical Industries family value and will build a strong team spirit.
</p>

<p class="mt-4">Wish you all the best in the days ahead.</p>

<p  class="mt-4">Thanking You, <br><br><br></p>

<p>
    .................................. <br>
    <strong> Authorize<br>
        {{$emp_log->company_name}}.</strong>
</p>






{{--



<p>We are please to inform you that you will be receiving an increase in salary <span>{{apsis_money($emp_log->previous_gross)}}</span> to  <span>{{number_format($emp_log->gross_salary,2)}}</span>.</p>
<p>We think you for your ongoing commitment to excellence at SR Chemical Industries Ltd., and congratulate you on your
    outstanding performance! </p>
<p>Please be advised that matters relating to salary are confidential in nature, and should not be divulged to other
    employees.</p>

<p>The increase in your salary will be effective as of <span>{{toDated($emp_log->applicable_date)}}</span>.</p>
<p>Your new salary structure</p>

<p><strong>Basic:</strong> <span>{{number_format($emp_log->basic_salary,2)}}</span></p>
<p><strong>House Rent:</strong> <span>{{number_format($emp_log->house_rent_amount,2)}}</span></p>
<p><strong>Medical:</strong> <span>{{number_format($emp_log->min_medical,2)}}</span></p>
<p><strong>Food:</strong> <span>{{number_format($emp_log->min_food,2)}}</span></p>
<p><strong>TA DA:</strong> <span>{{number_format($emp_log->min_tada,2)}}</span></p>
<p style="border-top: 1px dotted #000"><strong>Gross Total:</strong> <span>{{number_format($emp_log->min_gross,2)}}</span></p>
<br>
<br>
<br>
<p>Sincerely,</p>
--}}
