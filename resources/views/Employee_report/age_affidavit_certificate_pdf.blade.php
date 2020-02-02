<style>
    h4{
        font-size:1.5rem;
    }
    h5{
        font-size: 1rem;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    thead tr td{
        background:#e7e7e7;
        font-size: 1rem;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size: 1rem;
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
        font-size: 1rem;
    }
    .pt10{
        padding-top: 10px
    }
    .pb10{
        padding-bottom: 10px;
    }
    .page{
        width: 100%; height: 1000px;
    }
</style>
<div class="page">
    <table class="mb-4">
        <tr>
            <td colspan="2" class="pb5">ক্রমিক নং: ................... </td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">তারিখ : ................... </td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">নাম : {{ !empty($employee->name_bangla)?$employee->name_bangla : (!empty($employee->name)?$employee->name:'N/A')}} </td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">পিতার নাম :  {{ !empty($employee->father_name_bangla)?$employee->father_name_bangla : (!empty($employee->father_name)?$employee->father_name:'N/A')}}  </td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">মাতার নাম : {{ !empty($employee->mother_name_bangla)?$employee->mother_name_bangla : (!empty($employee->mother_name)?$employee->mother_name:'N/A')}} </td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">লিঙ্গ : {{ $employee->gender ?? 'N/A' }} </td>
        </tr>
        <tr>
            <td colspan="2" class="pt10 pb5"> বর্তমান ঠিকানা : </td>
        </tr>
        <tr>
            <td class="pb5">গ্রাম : {{$employee->present_village??'N/A'}} </td>
            <td class="pb5">পোষ্ট : {{$employee->present_po??'N/A'}} @if(!empty($employee->present_post_code)) ({{$employee->present_post_code}}) @endif </td>
        </tr>
        <tr>
            <td class="pb5">থানা : {{$employee->present_thana??'N/A'}} </td>
            <td class="pb5">জেলা : {{$employee->present_district??'N/A'}} </td>
        </tr>

        <tr>
            <td colspan="2" class="pb5 pt10"> স্থায়ী ঠিকানা  : </td>
        </tr>
        <tr>
            <td class="pb5">গ্রাম :  {{$employee->permanent_village??'N/A'}}</td>
            <td class="pb5">পোষ্ট : {{$employee->permanent_po??'N/A'}} @if(!empty($employee->permanent_post_code)) ({{$employee->permanent_post_code}}) @endif</td>
        </tr>
        <tr>
            <td class="pb5">থানা :  {{$employee->permanent_thana??'N/A'}}</td>
            <td class="pb5">জেলা : {{$employee->permanent_district??'N/A'}}</td>
        </tr>
        <tr>
            @php($dob = new DateTime($employee->date_of_birth))
            @php($now = new DateTime())
            @php($difference = $now->diff($dob)))
            @php($age = $difference->y)
            <td colspan="2" class="pb5 pt10"> জন্ম তারিখ ( শিক্ষা সনদ/ জন্ম সনদ অনুসারে) : @if(!empty($employee->date_of_birth)) {{ toDated($employee->date_of_birth) }} ( {{ $age }} ) @endif</td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">দৈহিক সক্ষমতা : ...............................</td>
        </tr>
        <tr>
            <td colspan="2" class="pb5"> সনাক্তকরণ চিহ্ন : ...............................</td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">রক্তের গ্রুপ : {{$employee->blood_group??'N/A'}}</td>
        </tr>
        <tr>
            <td colspan="2" class="pb5">কর্মীর স্বীকাররোক্তি : <br>
            <p>আমি সজ্ঞানে, সুস্থ- মস্তিকে আমার বয়স নির্ধারনের জন্য সর্বনিম্নে স্বাক্ষরকারী ডাক্তারকে ডাক্তারী পরীক্ষা করার জন্য অনুমতি প্রদান করলাম।</p></td>
        </tr>
    </table>
    <table>
        <tr>
            <td  class="text-left">...................................</td>
            <td class="text-right">..................................</td>
        </tr>
        <tr>
            <td  class="text-left"> সংশ্লিষ্ট ব্যক্তির স্বাক্ষর/ টিপসহি	</td>
            <td  class="text-right"> রেজিস্টার্ড চিকিৎসকের স্বাক্ষর</td>
        </tr>
    </table>
</div>
@php($dob = new DateTime($employee->date_of_birth))
@php($now = new DateTime())
@php($difference = $now->diff($dob))
@php($age = $difference->y)

<div class="page">
    <h2 class="text-center"><u>বয়স ও সক্ষমতার প্রত্যয়নপত্র</u></h2>
    <p>ক্রমিক নং: ...................</p>
    <p>তারিখ : ...................</p>
    <p> আমি এই মর্মে প্রত্যয়ন করিতেছি যে (নাম) {{ !empty($employee->name_bangla)?$employee->name_bangla : (!empty($employee->name)?$employee->name:'N/A')}}<br>
        পিতা :  {{ !empty($employee->father_name_bangla)?$employee->father_name_bangla : (!empty($employee->father_name)?$employee->father_name:'N/A')}} &nbsp;&nbsp;&nbsp;&nbsp;
        মাতা : {{ !empty($employee->mother_name_bangla)?$employee->mother_name_bangla : (!empty($employee->mother_name)?$employee->mother_name:'N/A')}}<br>

        বর্তমান ঠিকানা :  গ্রাম : {{$employee->present_village??'N/A'}} &nbsp;&nbsp;&nbsp;&nbsp;  পোষ্ট : {{$employee->present_po??'N/A'}} @if(!empty($employee->present_post_code)) ({{$employee->present_post_code}}) @endif<br>
        থানা : {{$employee->present_thana??'N/A'}}   &nbsp;&nbsp;&nbsp;&nbsp; জেলা : {{$employee->present_district??'N/A'}}  কে আমি পরীক্ষা করেছি।</p>

    <p>তিনি প্রতিষ্ঠানে নিযুক্ত হতে ইচ্ছুক, এবং আমার পরীক্ষা হতে এইরুপ পাওয়া গিয়াছে যে তাহার বয়স  @if(!empty($employee->date_of_birth)) {{ toDated($employee->date_of_birth) }} ( {{ $age }} ) @endif  বৎসর এবং তিনি প্রতিষ্ঠানে প্রাপ্ত বয়স্ক/ কিশোর হিসাবে নিযুক্ত হবার যোগ্য।<br>
        তাহার সনাক্তকরণের চিহ্ন 	।</p>
    <table style="margin-top: 40px">
        <tr>
            <td  class="text-left">...................................</td>
            <td class="text-right">..................................</td>
        </tr>
        <tr>
            <td  class="text-left">সংশ্লিষ্ট ব্যক্তির স্বাক্ষর/ টিপসহি	</td>
            <td  class="text-right"> রেজিস্টার্ড চিকিৎসকের স্বাক্ষর </td>
        </tr>
    </table>

</div>