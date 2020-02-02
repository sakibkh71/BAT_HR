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


{{--<h4 class="text-center"><strong>Factory:</strong> Rajapur, Mirzapur, Sherpur, Bogura. <br>
    Corporate Office: SR Villa, 46, Lake Drive road, Nikunja – 1, Khilkhet, Dhaka. <br>
    A Sister Concern of SR Group</h4>

<h3 class="underline text-center mb-4">Private &amp; Confidential</h3>--}}

<table class="mb-4">
    <tr>
        <td colspan="2" class="pb5"> Ref # SRCIL/HRD/ {{$employee->user_code??'N/A'}}</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5"> Date: {{ date('d F, Y') }} </td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">  Mr./Mrs. {{ $employee->name ?? 'N/A' }} </td>
    </tr>
    <tr>
        <td class="pb5">  Village: {{ $employee->present_village ?? 'N/A' }} </td>
        <td class="pb5">  Post: {{ $employee->present_po ?? 'N/A' }} </td>
    </tr>
    <tr>
        <td class="pb5">  P.S: {{ $employee->present_thana ?? 'N/A' }} </td>
        <td class="pb5">   District: {{ $employee->present_district ?? 'N/A' }} </td>
    </tr>
</table>

<h4 class="font-bold">Dear Mr./Mrs. <u>{{$employee->name??'N/A'}}</u> ,</h4>
<p> With reference to your application &amp; interviews, SR Chemical Industries  Limited is pleased to appoint you as <strong>{{$employee->designations_name??'N/A'}}</strong> under <strong>{{$employee->hr_emp_section_name??'N/A'}}</strong> department <strong>{{$employee->departments_name??'N/A'}}</strong> on the following terms and conditions:</p>

<h5 class="font-bold">1. Probationary Period:</h5>
<p>You will be on probation for a period of six (6) months. At the end of the
    probation you will be confirmed if your service is found to be satisfactory and
    up to the standard required by the management. During the probation
    period, if your service is not found satisfactory, your services may be
    terminated by management at any time without assigning any reason.</p>

<h5 class="font-bold"> 2. Exclusivity of Employment:</h5>
<p>You are required to work exclusively for organization, and not to engage
    in any outside employment/business activities without the expressed
    written permission of the company. Furthermore, neither the company’s
    name nor any of its facilities may be used for any purpose what so ever
    other than for our own business requirements. </p>

<h5 class="font-bold">3. Working Hours &amp; Leave:</h5>
<p>Our working hours and weekly off day will be followed by company
    policy &amp; production schedule. You will be allowed to enjoy the leave
    facilities as per company leave policy and country labor laws as may be
    applicable from time to time. </p>
<p> Your leave calculation will bellow</p>

<table>
    <tr>
        <td>Casual Leave:</td>
        <td>10 (Ten) days with pay.</td>
    </tr>
    <tr>
        <td>Sick Leave:</td>
        <td>14 (Fourteen) days with pay.</td>
    </tr>
    <tr>
        <td>Festival Leave:</td>
        <td>12 (Twelve) days with pay.</td>
    </tr>
    <tr>
        <td>Holiday:</td>
        <td>01 (One) day with pay (For six working days).</td>
    </tr>
    <tr>
        <td>Earn leave:</td>
        <td>01 (One) day (For Eighteen (18) working days).</td>
    </tr>
    <tr>
        <td>Leave without pay (As per company policy):</td>
        <td>180 (Hundred &amp; Eighty) days maximum.</td>
    </tr>
    <tr>
        <td>Special leave (As per company policy):</td>
        <td>180 (Hundred &amp; Eighty) days maximum.</td>
    </tr>
</table>

<h5 class="font-bold mt-4">4. Festival Bonus:</h5>
<p>You will get two (02) festival bonus per annum equal to one (01) gross salary, after successfully complete your service one (01) year from your date of joining.</p>

<h5 class="font-bold">5. Resignation:</h5>
<p>If you want to leave this company-</p>
<ul>
    <li class="list-number">You must submit 15 (Fifteen) days prior notice if you are performing on probationary period, or </li>
    <li class="list-number">You must submit 01 (One) month prior notice or 01 (One) month gross salary If you are performing as a permanent employee.</li>
</ul>

<h5 class="font-bold">6. Termination:</h5>
<p>If your service is not found satisfactory during probationary period, your employment will be terminated by employer without prior any notice period.</p>
<p> or, during contractual period your employment will be terminated by
    employer at one (1) month prior notice or on payment of one month
    gross pay.
</p>
<h5 class="font-bold">7. Handover / Takeover:</h5>
<p>Upon the termination of your employment, you will return to the company all papers and documents or other property which may at that time be in your possession, relating to the business or affairs of the company or any of its associates or branches and will not retain any copy or extract there from.</p>

<h5 class="font-bold">8. Confidentiality:</h5>
<p>You shall abide by the following Code of Conduct; any violation of this Code of Conduct shall be treated as misconduct and you shall be dealt with severely:</p>

<ul>
    <li class="list-alpha mb-3">During your employment with us and thereafter, you will keep strict
        secrecy regarding the business of the company. You will not divulge to any
        person, firm or company, whosoever, your salary, increments and all
        confidential information of any description without first obtaining written
        permission from the management.
    </li>
    <li class="list-alpha mb-3">You shall be the full-time employee of the management and shall not engage yourself in any other work, profession or employment either honorary or otherwise during the period of your employment without first obtaining written permission from the management. </li>
    <li class="list-alpha mb-3">You will conform to all rules and regulations in force from time to time and shall carry out all other lawful orders / instructions / directions of your supervisors as are given to you in connection with the day to day discharge of your duties while in employment of this company.
        We sincerely welcome you to our organization as a new member of SR
        Chemical Industries Limited and wish you every success in your career
        progress with us. We are confident that we will receive your best efforts
        in the profitable development of our operation in Bangladesh. <br><br>
        If the above offer of your employment in SR Chemical Industries Limited and its terms and conditions are acceptable to you, you may sign the duplicate of this Letter of Appointment as a token of your acceptance and return to the, SR Chemical Industries Limited, 46 Lake Road, Nikunja-1, Khilkhet, Dhaka-1229.
        <br> Wishing you successful career progression with SR Chemical Industries
        Limited.
    </li>
</ul>
<p  style="margin-top: 100px; margin-bottom: 50px;">Regards</p>
<table>
    <tr>
        <td  class="text-left">..........................................................</td>
        <td class="text-right">..........................................................</td>
    </tr>
    <tr>
        <td  class="text-left"> Md. Nuruzzaman, <br> CEO <br>SR Chemical Industries Ltd</td>
        <td  class="text-right"> I accept the terms & conditions</td>
    </tr>
</table>