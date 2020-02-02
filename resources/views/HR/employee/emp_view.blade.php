
<table width="100%">
    <tr>
        <td width="80%">
            Employee Code: {{@$employee->user_code}}
            <br>
            {{@$employee->name}}
            <br>
            {{@$employee->present_address}},
            {{@$employee->present_po}}<br>
            {{@$employee->present_thana}},
            {{@$employee->present_district}}
            <br>
            {{@$employee->mobile}}
            <br>
            {{@$employee->email}}
        </td>

        <td width="20%" style="text-align: right" align="right">
            <img title="No Image" style="border: 1px solid #000; padding: 5px" height="150" width="150" src="{{ !empty($employee->user_image)&&file_exists('public/img/'.$employee->user_image)? URL::to('public/img/'.$employee->user_image) : ''}}"/>
        </td>
    </tr>
    <tr>
        <td class="panel_head" colspan="4">
            <h5>Personal Info</h5>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding-top: 20px;">
           <table width="100%" border="0">
               <tr>
                   <td width="17%">Name:</td>
                   <td width="35%">{{$employee->name}}</td>
                   <td width="15%">Date of Birth:</td>
                   <td width="35%">{{toDated($employee->date_of_birth)}}</td>
               </tr>
               <tr>
                   <td>Father Name:</td>
                   <td>{{$employee->father_name}}</td>
                   <td>NID:</td>
                   <td>{{$employee->nid}}</td>
               </tr>
               <tr>
                   <td>Mother Name:</td>
                   <td>{{$employee->mother_name}}</td>
                   <td>Passport:</td>
                   <td>{{$employee->passport}}</td>
               </tr>
               <tr>
                   <td>Marital Status:</td>
                   <td>{{$employee->marital_status}}</td>
                   <td>Spouse Name:</td>
                   <td>{{$employee->spouse_name}}</td>
               </tr>
               <tr>
                   <td>Gender:</td>
                   <td>{{$employee->gender}}</td>
                   <td>Religion:</td>
                   <td>{{$employee->religion}}</td>
               </tr>
               <tr>
                   <td>Blood Group:</td>
                   <td>{{$employee->blood_group}}</td>

               </tr>
               <tr>
                   <td>Home District:</td>
                   <td>{{$employee->permanent_district}}</td>

               </tr>
               <tr>
                   <td>Present Address:</td>
                   <td colspan="3">
                       {{@$employee->present_address}},
                       {{@$employee->present_po}},
                       {{@$employee->present_thana}},
                       {{@$employee->present_district}}
                   </td>

               </tr>
               <tr>
                   <td>Permanent Address:</td>
                   <td colspan="3">
                       {{@$employee->permanent_address}},
                       {{@$employee->permanent_po}},
                       {{@$employee->permanent_thana}},
                       {{@$employee->permanent_district}}
                   </td>

               </tr>
           </table>
        </td>

    </tr>
    <tr>
        <td class="panel_head" colspan="4">
            <h5>Nominee Info</h5>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding-top: 20px;">
            <table width="100%" border="1">
                <tr>
                    <td>Nominee Name</td>
                    <td>Bangla Name</td>
                    <td>Relationship</td>
                    <td>Nominee Ratio</td>
                </tr>
    @if(!empty($nominees) && count($nominees) > 0)
        @foreach($nominees as $each)
            <tr class="nominee-select-toggle" id="{{$each->hr_emp_nominees_id}}">
                <td>{{!empty($each->nominee_name)?$each->nominee_name:'N/A'}}</td>
                <td>{{!empty($each->nominee_name_bangla)?$each->nominee_name_bangla:'N/A'}}</td>
                <td>{{!empty($each->nominee_relationship)?$each->nominee_relationship:'N/A'}}</td>
                <td class="nominee_ratio" data-id="{{$each->hr_emp_nominees_id}}" data-ratio="{{!empty($each->nominee_ratio)?$each->nominee_ratio:0}}">{{!empty($each->nominee_ratio)?$each->nominee_ratio:'N/A'}}</td>
            </tr>
        @endforeach
    @endif
        </table>
    </td>
    </tr>
    <tr>
        <td class="panel_head" colspan="4">
            <h5>Official Info</h5>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding-top: 20px;">
            <table width="100%" border="0">
                <tr>
                    <td width="17%">Designation:</td>
                    <td width="35%">{{$employee->designations_name}}</td>
                    <td width="15%">Department:</td>
                    <td width="35%">{{$employee->departments_name}}</td>
                </tr>
                <tr>
                    <td>Unit:</td>
                    <td>{{$employee->hr_emp_unit_name}}</td>
                    <td>Section:</td>
                    <td>{{$employee->hr_emp_section_name}}</td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>{{$employee->hr_emp_category_name}}</td>
                    <td>Joining Date:</td>
                    <td>{{toDated($employee->date_of_join)}}</td>
                </tr>
                <tr>
                    <td>Shift:</td>
                    <td>{{$employee->shift_name}}</td>
                    <td>Time:</td>
                    <td>{{$employee->start_time}} - {{$employee->end_time}}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="panel_head" colspan="4">
            <h5>Educational Info</h5>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding-top: 20px;">
            <table width="100%" border="1">
                <tr class="table_header">
                    <td>Qualification</td>
                    <td>Degree</td>
                    <td>Institution</td>
                    <td>Passing Year</td>
                    <td>Major Subject</td>
                    <td>Result Type</td>
                    <td>Result</td>
                </tr>
                @if(!empty($education) && count($education) > 0)
                    @foreach($education as $edu)
                        <tr>
                            <td>{{!empty($edu->educational_qualifications_name)?$edu->educational_qualifications_name:'N/A'}}</td>
                            <td>{{!empty($edu->educational_degrees_name)?$edu->educational_degrees_name:'N/A'}}</td>
                            <td>{{!empty($edu->educational_institute_name)?$edu->educational_institute_name:'N/A'}}</td>
                            <td>{{!empty($edu->passing_year)?$edu->passing_year:'N/A'}}</td>
                            <td>{{!empty($edu->educational_majors_name)?$edu->educational_majors_name:'N/A'}}</td>
                            <td>{{!empty($edu->result_type)?$edu->result_type:'N/A'}}</td>
                            <td>{{!empty($edu->results)?$edu->results:'N/A'}}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </td>
    </tr>
    <tr>
        <td class="panel_head" colspan="4">
            <h5>Employment Info</h5>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding-top: 20px;">
            <table width="100%" border="1">
                <tr class="table_header">
                    <td>Organization Name</td>
                    <td>Department</td>
                    <td>Sub Department</td>
                    <td>Designation</td>
                    <td>Duration</td>
                    <td>Responsibility</td>
                </tr>
                @if(!empty($emp_professions) && count($emp_professions) > 0)
                    @foreach($emp_professions as $emp)
                        <tr class="emp-select-toggle" id="{{$emp->hr_emp_professions_id}}">
                            <td>{{!empty($emp->organization_name)?$emp->organization_name:'N/A'}}</td>
                            <td>{{!empty($emp->department_name)?$emp->department_name:'N/A'}}</td>
                            <td>{{!empty($emp->sub_department)?$emp->sub_department:'N/A'}}</td>
                            <td>{{!empty($emp->designation_name)?$emp->designation_name:'N/A'}}</td>
                            <td>{{!empty($emp->from_date)?toDated($emp->from_date):'N/A' }} to {{!empty($emp->is_continue)? ' Continue ' : (!empty($emp->from_date)?toDated($emp->from_date):'N/A') }}</td>
                            <td>{{!empty($emp->responsibilities)?$emp->responsibilities:'N/A'}}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="4" id="sign_area">
            <img title="No Sign" style="border: 1px dotted #000; padding: 5px" height="20" width="80" src="{{ !empty($employee->user_sign)&&file_exists('public/img/'.$employee->user_sign)? URL::to('public/img/'.$employee->user_sign) : ''}}"/>
            <br>
            ---------------------------
            <br>
            <span>Signature</span>
        </td>
    </tr>
</table>

<style>
    .panel_head{
        border-bottom: 1px solid #000;
        width: 100%;
        padding: 2px;
        padding-top: 15px;
    }
    .panel_head h5{
        margin-bottom: 10px;
    }

    .table_header td{
        font-weight: bold;
        font-size: 13px;
    }
    #sign_area{
        text-align: right;
        padding-top: 50px;
    }
</style>