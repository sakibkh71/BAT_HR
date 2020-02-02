<style>
    h4{
        font-size:1.5em;
    }
    h5{
        font-size:1.1em;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    thead tr td{
        background:#e7e7e7;
        font-size: 1.1rem;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:1.1rem;
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
        font-size:1.1rem;
    }
    p{
        padding-bottom: 10px;
    }
</style>
<p>
    বরাবর <br>
    ব্যবস্থাপক <br>
    মানব সম্পদ বিভাগ <br>
    এস, আর, কেমিক্যাল ইন্ডাস্ট্রিজ লি: (ইউনিট-০১) <br>
    রাজাপুর, শেরপুর, মির্জাপুর, বগুড়া । <br>
</p>

<h4>বিষয় : চাকুরীর জন্য আবেদন।</h4>

<p>
জনাব, <br>
যথাযথ সম্মান পূর্বক নিবেদন এই যে, বিশ্বস্ত সূত্রে জানতে পারলাম আপনার প্রতিষ্ঠানে <strong>{{$employee->designations_name??'N/A'}}</strong> পদে কিছু সংখ্যক লোক নিয়োগ করা হবে। আমি উক্ত পদের জন্য একজন প্রার্থী হিসেবে আমার প্রয়োজনীয় তথ্যাবলী পেশ করলাম।
</p>
<table class="mb-4">
    <tr>
        <td colspan="2" class="pb5">বিষয় : শ্রমিকদের নিয়োগপত্র</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5"> কর্মীর নাম : {{ !empty($employee->name_bangla)?$employee->name_bangla : (!empty($employee->name)?$employee->name:'N/A')}} </td>
    </tr>
    <tr>
        <td colspan="2" class="pb5"> পিতার নাম : {{ !empty($employee->father_name_bangla)?$employee->father_name_bangla : (!empty($employee->father_name)?$employee->father_name:'N/A')}} </td>
    </tr>
    <tr>
        <td colspan="2" class="pb5"> মাতার নাম : {{ !empty($employee->mother_name_bangla)?$employee->mother_name_bangla : (!empty($employee->mother_name)?$employee->mother_name:'N/A')}}</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">স্বামী বা স্ত্রীর নাম (প্রযোজ্য ক্ষেত্রে) : {{ !empty($employee->spouse_name_bangla)?$employee->spouse_name_bangla : (!empty($employee->spouse_name)?$employee->spouse_name:'N/A')}}</td>
    </tr>
    <tr>
        <td class="pb5"> বর্তমান ঠিকানা : </td>
        <td class="pb5"> স্থায়ী ঠিকানা : </td>
    </tr>
    <tr>
        <td class="pb5">নাম :  {{ !empty($employee->name_bangla)?$employee->name_bangla : (!empty($employee->name)?$employee->name:'N/A')}} </td>
        <td class="pb5">নাম :  {{ !empty($employee->name_bangla)?$employee->name_bangla : (!empty($employee->name)?$employee->name:'N/A')}} </td>
    </tr>
    <tr>
        <td class="pb5">প্রযত্নে :	 {{ !empty($employee->father_name_bangla)?$employee->father_name_bangla : (!empty($employee->father_name)?$employee->father_name:'N/A')}} </td>
        <td class="pb5">প্রযত্নে : {{ !empty($employee->father_name_bangla)?$employee->father_name_bangla : (!empty($employee->father_name)?$employee->father_name:'N/A')}} </td>
    </tr>
    <tr>
        <td class="pb5">গ্রাম : {{$employee->present_village??'N/A'}}</td>
        <td class="pb5">গ্রাম : {{$employee->permanent_village??'N/A'}}</td>
    </tr>
    <tr>
        <td class="pb5">পোষ্ট : {{$employee->present_po??'N/A'}} @if(!empty($employee->present_post_code)) ({{$employee->present_post_code}}) @endif</td>
        <td class="pb5">পোষ্ট :  {{$employee->permanent_po??'N/A'}} @if(!empty($employee->permanent_post_code)) ({{$employee->permanent_post_code}}) @endif</td>
    </tr>
    <tr>
        <td class="pb5">থানা : {{$employee->present_thana??'N/A'}}  </td>
        <td class="pb5">থানা : {{$employee->permanent_thana??'N/A'}} </td>
    </tr>
    <tr>
        <td class="pb5"> জেলা : {{$employee->present_district??'N/A'}}</td>
        <td class="pb5"> জেলা : {{$employee->permanent_district??'N/A'}}</td>
    </tr>
</table>




<table class="mt-2">
    <tr>
        <td colspan="2"  class="pb5">
            শিক্ষাগত যোগ্যতা:
            @if(!empty($employee->educational_qualifications_name)){{ $employee->educational_qualifications_name }} @endif
            @if(!empty($employee->educational_degrees_name)) in  {{ $employee->educational_degrees_name }} @endif
            @if(!empty($employee->educational_institute_name)) from  {{ $employee->educational_institute_name }}  @endif
            <br><br>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">জাতীয়তা: {{$employee->nationality ?? 'N/A'}}</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">ধর্ম: {{$employee->religion ?? 'N/A'}}</td>
    </tr>
    <tr>
        @php($dob = new DateTime($employee->date_of_birth))
        @php($now = new DateTime())
        @php($difference = $now->diff($dob)))
        @php($age = $difference->y)
        <td colspan="2" class="pb5">জন্ম তারিখ(বয়স সহ): @if(!empty($employee->date_of_birth)) {{ toDated($employee->date_of_birth) }} ( {{ $age }} ) @endif</td>
    </tr>
    <tr>
        <td colspan="2" class="pb5">অভিজ্ঞতা: <br>
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
        <td colspan="2" class="pb5"><h5>রেফারেন্স(সুপারীশকারী): </h5></td>
    </tr>

    <tr>
        <td class="pb5">
            <table>
                <tr>
                    <td class="pb5">কর্মরত পরিচিত ব্যক্তির নাম : {{ !empty($reference_user->name_bangla)?$reference_user->name_bangla : (!empty($reference_user->name)?$reference_user->name:'N/A')}}</td>
                    <td class="pb5">পদবী :   <small>{{$reference_user->designations_name ?? 'N/A'}}</small></td>
                </tr>
                <tr>
                    <td class="pb5">বিভাগ : <small> {{$reference_user->departments_name ?? 'N/A'}}</small></td>
                    <td class="pb5"> ঠিকানা : {{ $reference_user->present_district??'N/A' }} </td>
                </tr>
                <tr>
                    <td class="pb5"> মোবাইল নং : <small> {{$reference_user->mobile ?? 'N/A'}}</small> </td>
                    <td>&nbsp; </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<p>অতএব বিনীত নিবেদন এইযে, উপরোক্ত বিষয়াদি বিবেচনা পূর্বক আমাকে উল্লেখিত পদে নিয়োগ প্রদান করেত মহাশয়ের যেন মর্জি হয়।</p>
<table style="margin-top: 40px">
    <tr>
        <td class="pb5" width="60%" valign="top">
            তারিখ:  ....................
        </td>
        <td class="pb5"> <h5>নিবেদক :</h5><br><br>
            স্বাক্ষর: .................................<br><br>
            নাম : ....................................<br><br>
        </td>
    </tr>
</table>
