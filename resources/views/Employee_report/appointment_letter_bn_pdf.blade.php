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
        font-size: 1rem;
        font-weight: bold;
        padding: 5px 10px;
        text-align: center;
    }
    td{
        font-size:1rem;
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
    .list-none{
        list-style: none;
    }
    .default-list{
        display: block;
    }
    .default-list li{
        display: list-item;
        list-style: none;
        margin-bottom: 10px;
    }
</style>
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

<p> জনাব  <u>{{ !empty($employee->name_bangla)?$employee->name_bangla : (!empty($employee->name)?$employee->name:'N/A')}}</u>, <br>
    আপনার	{{ !empty($employee->applicable_date)?toDated($employee->applicable_date):'N/A' }}	ইং তারিখের আবেদন পত্র ও পরবর্তী স্বাক্ষাতকারের পরিপ্রেক্ষিতে আপনাকে	{{ !empty($employee->date_of_join)?toDated($employee->date_of_join):'N/A' }}	ইং তারিখ হতে
    {{$employee->departments_name??'N/A'}} বিভাগের 	{{$employee->hr_emp_section_name??'N/A'}}	শাখায় 	{{$employee->designations_name??'N/A'}}		পদে 	{{$employee->hr_emp_grade_name??'N/A'}}		নং গ্রেডে নিম্নোক্ত শর্ত সাপেক্ষে নিয়োগ প্রদান করা হলো ।
</p>

<h4>শর্তাবলী :-</h4>
<ul class="default-list">
    <li>
        <p>আপনার শিক্ষানবীশ নিয়োগকাল তিন (৩) মাস হবে। শিক্ষানবীশকালে আপনার কর্মদক্ষতা, আচরণ, আনুগত্য ও কর্তব্যপরায়নতা সন্তোষজনক না হলে চিঠি প্রদান পূর্বক আপনার শিক্ষানবীশকাল আরও তিন (৩) মাস বর্ধিত করা হবে। শিক্ষানবীশকাল অতিবাহিত হওয়ার পর আপনাকে স্থায়ী শ্রমিক হিসেবে গন্য করা হবে এবং এর প্রেক্ষিতে কোন চিঠি প্রদান করা হবে না।</p>
        <p>০২. আপনার মজুরী / বেতন কাঠামো হবে নিম্নরুপ :</p>

        <table style="width: 50%; margin-left: 20%">
            <tr>
                <td>মূল মজুরী / বেতন	</td>
                <td>:</td>
                <td>{{apsis_money_bn($employee->basic_salary??0, null, null)}} টাকা</td>
            </tr>
            <tr>
                <td>বাড়ী ভাড়া ভাতা</td>
                <td>:</td>
                <td>{{apsis_money_bn($employee->house_rent_amount??0, null, null)}}  টাকা</td>
            </tr>
            <tr>
                <td>চিকিৎসা ভাতা	</td>
                <td>:</td>
                <td> {{apsis_money_bn($employee->min_medical??0, null, null)}}  টাকা</td>
            </tr>
            <tr>
                <td>যাতায়াত ভাতা</td>
                <td>:</td>
                <td> {{apsis_money_bn($employee->min_tada??0, null, null)}}  টাকা</td>
            </tr>
            <tr>
                <td>খাদ্য ভাতা	</td>
                <td>:</td>
                <td> {{apsis_money_bn($employee->min_food??0, null, null)}}   টাকা</td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            <tr>
                <td>সর্বমোট	</td>
                <td>:</td>
                @php($total = $employee->basic_salary + $employee->house_rent_amount + $employee->min_medical +$employee->min_tada + $employee->min_food )
                <td> {{apsis_money_bn($total??0, null, null)}} টাকা</td>
            </tr>
        </table>
    </li>
    <li>০৩. আপনার মাসিক মজুরী পরবর্তী মাসের সাত (৭) কর্মদিবসের মধ্যে প্রদান করা হবে।</li>
    <li>০৪. আপনার বাৎসরিক বেতন বৃদ্ধির হার হবে বাংলাদেশ শ্রম আইন সংশোধনী-২০১৩ অনুযায়ী মূল বেতনের পাঁচ শতাংশ (৫%)।</li>
    <li>০৫. আপনার প্রতিদিনের সাধারন কর্ম ঘন্টার মেয়াদ সর্বোচ্চ আট(৮) ঘন্টা। পরবর্তী সময় অতিরিক্ত কর্ম ঘন্টা হিসেবে বিবেচিত হবে।</li>
    <li>০৬. আপনার অতিরিক্ত কাজের ঘন্টা নিম্নোক্ত পদ্ধতিতে হিসেবের মাধ্যমে প্রদান করা হবে। <br> (মূল মজুরী/ ২০৮)*২ *মোট অতিরিক্ত ঘন্টা।
        <br> *ওভার টাইম বলতে প্রতিদিন আট (৮) ঘন্টার বেশি বা সাপ্তাহিক আট চল্লিশ (৪৮) ঘন্টার বেশি কাজ করাকে বুঝাবে । মজুরী এবং ওভার টাইম এর টাকা একসাথে প্রদান করা হবে। </li>
    <li>০৭. আপনি কোম্পানীর প্রচলিত নিয়ম অনুযায়ী 	{{apsis_money_bn( $employee->attendance_bonus??0, null, null)}}  টাকা হাজিরা ভাতা প্রদান করা হবে।</li>
    <li>০৮. আপনাকে আইনানুগ উৎসব ভাতা প্রদান করা হবে।</li>
    <li>০৯. প্রতি কর্ম দিবসে মধ্যাহ্ন বিরতি হিসাবে আপনাকে এক (১) ঘন্টা প্রার্থনা, খাওয়া ও বিশ্রামের জন্য প্রদান করা হবে।</li>
    <li>১০. চাকুরীতে প্রবেশের পর থেকেই আপনি সরকার কর্তৃক নির্ধারিত বিধি অনুযায়ী গ্রুপ বীমার অন্তর্ভূক্ত হবেন/ চাকুরী স্থায়ী হবার পর সরকার কর্তৃক নির্ধারিত বিধি অনুযায়ী গ্রুপ বীমার অন্তর্ভূক্ত হবেন।</li>

    <li>১১. চাকুরীতে নিযুক্তির পর আপনাকে নিম্নোক্ত নিয়মে ছুটি প্রদান করা হবে : <br>
        ক. নৈমিত্তিক ছুটি : পূর্ণ মজুরীতে বৎসরে দশ (১০) দিন। এই ছুটি জমা রেখে পরবর্তী বছর ভোগ করা যাবে না। <br>
        খ. পীড়া ছুটি : পূর্ণ মজুরীতে বৎসরে চৌদ্দ (১৪) দিন। এই ছুটি জমা রেখে পরবর্তী বছর ভোগ করা যাবে না। <br>
        গ. উৎসব ছুটি : পূর্ণ মজুরীতে বৎসরে চৌদ্দ (১৩) দিন। এই ছুটি জমা রেখে পরবর্তী বছর ভোগ করা যাবে না। <br>
        ঘ. বাৎসরিক ছুটি : একটানা বার (১২) মাস চাকুরী পূর্ণ হলে, পূর্ববর্তী বছরের প্রতি আঠার (১৮) কর্মদিবসের জন্য এক (১) দিন হিসাবে পাওনা ছুটি পরবর্তী বছরের যেকোন সময় ভোগ করা যাবে। তবে এই ছুটি এক সাথে চল্লিশ (৪০) দিনের বেশি জমা থাকবে না। <br>
    </li>

    <li>১২. কোন স্থায়ী শ্রমিক চাকুরী হতে স্বেচ্ছায় অবসর গ্রহন করতে চাইলে কর্তৃপক্ষ বরাবর ষাট (৬০) দিনের অগ্রিম নোটিশ প্রদান করতে হবে। অন্যান্য শ্রমিকের ক্ষেত্রে ত্রিশ (৩০) দিনের অগ্রিম নোটিশ প্রদান করতে হবে। তবে যদি কেউ বিনা নোটিশে চাকুরী হতে ইস্তফা দিতে চায় সেক্ষেত্রে উক্ত শ্রমিককে নোটিশ মেয়াদের মজুরী কর্তৃপক্ষকে প্রদান করতে হবে।
    </li>
    <li >১৩. মালিক কর্তৃক কোন স্থায়ী শ্রমিকের চাকুরী অবসানের ক্ষেত্রে কর্তৃপক্ষ স্থায়ী শ্রমিকের ক্ষেত্রে একশত (১২০) দিনের নোটিশ প্রদান করবেন, অথবা নোটিশ মেয়াদের সমপরিমান উক্ত শ্রমিককে প্রদান করবেন।</li>
    <li>১৪. কোন শ্রমিক চাকুরী হতে স্বেচ্ছায় অবসর গ্রহনের ক্ষেত্রে প্রতিষ্ঠানে তাহার চাকুরীর মেয়াদ নিরবিচ্ছিন্ন ভাবে নুন্যতম পাঁচ (৫) বছর বা দশ (১০) বছরের কম হলে প্রত্যেক বছরের জন্য চৌদ্দ (১৪) দিনের এবং দশ (১০) বছর বা তার বেশি হলে প্রত্যেক বছরের জন্য ত্রিশ (৩০) দিনের মূল মজুরী ক্ষতিপূরণ হিসেবে প্রদান করতে হবে।  </li>
    <li>১৫. যদি কোন শ্রমিক চাকুরী হতে ইস্তফা দেওয়ার ক্ষেত্রে নোটিশ প্রদান না করে অথবা নোটিশ মেয়াদের সম পরিমান মজুরী প্রদান না করে চাকুরী হতে ইস্তফা দেয়, সেক্ষেত্রে তিনি কোন ধরনের কোন সুবিধা পাবেন না।</li>
    <li>১৬. শ্রমিক কর্তৃক স্বেচ্ছায় চাকুরীর অবসান বা মালিক কর্তৃক শ্রমিকের চাকুরীর অবসান ব্যতিত অন্য কোন ভাবে শ্রমিকের চাকুরীর অবসান ঘটলে বাংলাদেশ শ্রম আইন অনুযায়ী অন্যান্য সকল সুবিধা সুবিধা প্রদেয় হবে।</li>
    <li>১৭. কোম্পানীর প্রয়োজনে আপনাকে অন্য কোন বিভাগ বা অঙ্গ প্রতিষ্ঠানে সম প্রকৃতির কাজে বদলী করতে পারবে।</li>
    <li>১৮. অন্যান্য বিষয়াদি ও সুযোগ সুবিধা যা অত্র পত্রে বর্ণিত হয় নাই তা শ্রম আইন বা বিধি অনুযায়ী পরিচালিত হবে।</li>
    <li>১৯. আপনার জীবন বৃত্তান্তে প্রদত্ত স্থায়ী বা বর্তমান ঠিকানার পরিবর্তন করলে তা সাথে সাথে কর্তৃপক্ষকে লিখিতভাবে অবহিত করতে হবে।</li>
</ul>

<p>উপরে বর্ণিত শর্তাদি যদি আপনার নিকট গ্রহণযোগ্য হয়, তাহা হলে এই পত্রের অনুলিপিতে আপনার সম্মতিসূচক স্বাক্ষর প্রদানপূর্বক পত্রটি গ্রহনের জন্য নির্দেশ প্রদান/অনুরোধ করা যাচ্ছে।</p>

<table style="margin-top: 10px;">
    <tr>
        <td> অনুমোদনকারী কর্তৃপক্ষ</td>
        <td class="text-right">(উপরিউক্ত বিষয়গুলো বুঝিয়া, স্ব-জ্ঞানে স্ব-প্রনোদিত হয়ে <br>স্বাক্ষর করিলাম এবং গ্রহন করিলাম)
            <br><br><br><br></td>
    </tr>
    <tr>
        <td  class="text-left">..........................................................</td>
        <td class="text-right">..........................................................</td>
    </tr>
    <tr>
        <td class="text-left"> সহ : ব্যবস্থাপক/উপ: ব্যবস্থাপক/ব্যবস্থাপক  <br>মানব সম্পদ বিভাগ/প্রশাসন বিভাগ </td>
        <td class="text-right"> স্বাক্ষর( শ্রমিক/ কর্মচারী </td>
    </tr>
</table>