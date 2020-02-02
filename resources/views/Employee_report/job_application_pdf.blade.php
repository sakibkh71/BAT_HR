<style>
    h4{
        font-size:1rem;
    }
    h5{
        font-size:1.1rem;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    thead tr td{
        background:#e7e7e7;
        font-size: 0.9rem;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:0.9rem;
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

</style>
<p>
To <br>
Manager <br>
Human Resource Department <br>
SR Chemical Industries Ltd (Unit-01) <br>
Rajapur, Mirzapur, Sherpur, Bogura. <br>
</p>
<h4><strong>Subject: Prayer for the job in the post of  {{$employee->designations_name??'N/A'}}</strong></h4>

<p>
    Sir, <br>
    With due respect I have known from the trusted source that your organization will recruit some efficient & eligible manpower for the post of <strong>{{$employee->designations_name??'N/A'}}</strong> I am one of the candidates for the post. For your kind consideration I submit all my necessary information bellow.
</p>

<table class="mb-4">
    <tr>
        <td colspan="2" class="pb5">Name : <strong> {{$employee->name??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">Father's/Husband's name: <strong>{{$employee->father_name??'N/A'}}</strong></td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">Mother's/Wife's name:  <strong>{{$employee->mother_name??'N/A'}}</strong> </td>
    </tr>
</table>
<table class="mt-2">
    <tr>
        <td class="pb5"><strong>Present Address:</strong> </td>
        <td class="pb5"><strong>Permanent Address: </strong></td>
    </tr>
    <tr>
        <td class="pb5">Village : {{$employee->present_village??'N/A'}}</td>
        <td class="pb5">Village : {{$employee->permanent_village??'N/A'}}</td>
    </tr>
    <tr>
        <td class="pb5">Post: {{$employee->present_po??'N/A'}} @if(!empty($employee->present_post_code)) ({{$employee->present_post_code}}) @endif</td>
        <td class="pb5">Post: {{$employee->permanent_po??'N/A'}} @if(!empty($employee->permanent_post_code)) ({{$employee->permanent_post_code}}) @endif</td>
    </tr>
    <tr>
        <td class="pb5">P.S: {{$employee->present_thana??'N/A'}}</td>
        <td class="pb5">P.S: {{$employee->permanent_thana??'N/A'}}</td>
    </tr>
    <tr>
        <td class="pb5">District: {{$employee->present_district??'N/A'}}</td>
        <td class="pb5">District: {{$employee->permanent_district??'N/A'}}</td>
    </tr>
    <tr>
        <td class="pb5">Mobile no: {{$employee->mobile??'N/A'}}</td>
        <td class="pb5">Mobile no:{{$employee->mobile??'N/A'}} </td>
    </tr>
</table>
<table class="mt-2">
    <tr>
        <td colspan="2"  class="pb5">
            Education Qualification (Last Degree): <strong>
            @if(!empty($employee->educational_qualifications_name)){{ $employee->educational_qualifications_name }} @endif
            @if(!empty($employee->educational_degrees_name)) in  {{ $employee->educational_degrees_name }} @endif
            @if(!empty($employee->educational_institute_name)) from  {{ $employee->educational_institute_name }}  @endif
            </strong>
            <br><br>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">Nationality: {{$employee->nationality ?? 'N/A'}}</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">Religion: {{$employee->religion ?? 'N/A'}}</td>
    </tr>
    <tr>
        @php($dob = new DateTime($employee->date_of_birth))
        @php($now = new DateTime())
        @php($difference = $now->diff($dob)))
        @php($age = $difference->y)
        <td colspan="2" class="pb5">Date of Birth (with age): @if(!empty($employee->date_of_birth)) {{ toDated($employee->date_of_birth) }} ( {{ $age }} ) @endif</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">Experience: <br>
            @if(!empty($professions))
                @foreach($professions as $profession)
                    <p> <strong>{{ $profession->designation_name }}</strong> under Department of <strong>{{ $profession->department_name }}</strong> in  <strong>{{ $profession->organization_name }}</strong> From  <strong>{{toDated($profession->from_date)}}</strong> to <strong>@if( $profession->is_continue == 1) Continue  @else {{toDated($profession->to_date)}} @endif</strong> </p>
                @endforeach
            @endif
        </td>
    </tr>

</table>
<table class="mt-2">
    <tr>
        <td colspan="2" class="pb5"> <h5>Reference:</h5></td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">Organization Employee Info:</td>
    </tr>

    <tr>
        <td class="pb5">
            <table>
                <tr>
                    <td class="pb5">Name: {{$reference_user->name ?? 'N/A'}}</td>
                    <td class="pb5">Designation :  {{$reference_user->designations_name ?? 'N/A'}}</td>
                </tr>
                <tr>
                    <td class="pb5"> Department:  {{$reference_user->departments_name ?? 'N/A'}}</td>
                    <td class="pb5">Address (District): {{$reference_user->present_district ?? 'N/A'}}</td>
                </tr>
                <tr>
                    <td class="pb5"> Mobile no: {{$reference_user->mobile ?? 'N/A'}}</td>
                    <td>&nbsp; </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<p>So, this is my humble request to you that please considering my CV for the post of  {{$employee->designations_name ?? 'N/A'}} In your organization.</p>
<p class="mt-4">Date : .................... </p>
<p class="mt-4">Signature : ...............</p>
<p class="mt-4">Name : ...................</p>