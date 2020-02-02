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
    .pad5{
        padding: 5px;
    }
    .pb5{
        padding-bottom: 5px;
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

<p>
    Date: {{!empty($employee->date_of_confirmation)? toDated($employee->date_of_confirmation):'N/A'}}<br>
    SRCIL/HRD/{{$employee->user_code}}
</p>


<p>
  <strong>Mr. {{$employee->name??'N/A'}} <br></strong>
    {{$employee->designations_name ?? 'N/A'}} <br>
    {{$employee->departments_name ?? 'N/A'}} <br>
    SR Chemical Industries Ltd. <br>
    SR Group <br><br>
</p>

<h4><strong>Sub: Letter of CONFIRMATION.</strong> <br><br></h4>

<p><strong>Dear Mr. {{$employee->name?? 'N/A'}},</strong> <br></p>

<p>
We are very pleased to inform that the Management has decided to confirm your service as <strong>{{$employee->designations_name ?? ''}} under Department of {{$employee->departments_name ?? ''}}</strong>  in SR Chemical Industries Limited with effect from <strong>{{toDated($employee->date_of_confirmation)}} </strong> .
<br><br>

We are confident that you shall maintain the standard of good performance that you have already attained and uphold the zeal and enthusiasm to the highest level. Your participation will enhance SR Chemical Industries family value and will build a strong team spirit.
<br><br>
Wish you all the best in the days ahead. <br><br>
</p>

<p>
Thanking You, <br>
<strong>
Md. Nuruzzaman <br>
CEO <br>
SR Chemical Industries Ltd.
</strong>
</p>
