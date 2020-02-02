<div class="modal-header">
    <h2 class="text-left full-width font-bold">{{$employee->name??'N/A'}} <strong class="pull-right">Employee Code : {{$employee->user_code??'N/A'}}</strong></h2>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>
<div class="modal-body">
    <h4>Personal Info</h4>
    <div class="section-divider"></div>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Name </strong> </div>
                    <div class="col-md-7">{{$employee->name??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Name <br> (in bengali) </strong> </div>
                    <div class="col-md-7">{{$employee->name_bangla??'N/A'}}</div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Father's Name</strong> </div>
                    <div class="col-md-7">{{$employee->father_name??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Father's Name <br>(in bengali) </strong> </div>
                    <div class="col-md-7">{{$employee->father_name_bangla??'N/A'}} </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Mother's Name</strong> </div>
                    <div class="col-md-7">{{$employee->mother_name??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Mother's Name <br>(in bengali) </strong> </div>
                    <div class="col-md-7">{{$employee->mother_name_bangla??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Date of Birth </strong> </div>
                    <div class="col-md-7">{{toDated($employee->date_of_birth)}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Marital Status </strong> </div>
                    <div class="col-md-7">{{$employee->marital_status ?? 'N/A'}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Blood Group </strong> </div>
                    <div class="col-md-7">{{$employee->blood_group ?? 'N/A'}} </div>
                </div>
            </div>

        </div>

        <div class="col-md-5">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Gender </strong> </div>
                    <div class="col-md-7">{{$employee->gender ?? 'N/A'}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>NID </strong> </div>
                    <div class="col-md-7">{{$employee->nid??'N/A'}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Passport </strong> </div>
                    <div class="col-md-7">{{$employee->passport??'N/A'}}</div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Spouse Name  </strong> </div>
                    <div class="col-md-7">{{$employee->spouse_name??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Spouse <br> (in bengali) </strong> </div>
                    <div class="col-md-7">{{$employee->spouse_name_bangla??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>Religion </strong> </div>
                    <div class="col-md-7">{{$employee->religion??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong> Nationality  </strong> </div>
                    <div class="col-md-7">{{$employee->nationality??'N/A'}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 pt-2"><strong>TIN </strong> </div>
                    <div class="col-md-7">{{$employee->tin??'N/A'}} </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="employee-img">
                <div class="employee-avater" style="cursor: default">
                <img src="{{ !empty($employee->user_image)&&file_exists('public/img/'.$employee->user_image)? URL::to('public/img/'.$employee->user_image) : asset('public/img/default-user.jpg')}}" alt="" style="max-width: 100%;">
                </div>
                <div class="employee-signature"  style="cursor: default">
                    <img src="{{ !empty($employee->user_sign)? URL::to('public/img/'.$employee->user_sign) : asset('public/img/default-signature.jpg')}}" alt="" style="max-width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2 class="mt-3">Present Address</h2>
            <div class="section-divider"></div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>District </strong> </div>
                    <div class="col-md-6">{{$employee->present_district}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>Thana </strong> </div>
                    <div class="col-md-6">{{$employee->present_thana}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>Present Post Office </strong> </div>
                    <div class="col-md-6">{{$employee->present_po}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>village/House/Road No. </strong></div>
                    <div class="col-md-6">{{$employee->present_address??'N/A'}}</div>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <h2 class="mt-3">Permanent Address</h2>
            <div class="section-divider"></div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>District </strong> </div>
                    <div class="col-md-6">{{$employee->permanent_district}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>Thana </strong> </div>
                    <div class="col-md-6">{{$employee->permanent_thana}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>Present Post Office </strong> </div>
                    <div class="col-md-6">{{$employee->permanent_po}} </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3  pt-2"><strong>village/House/Road No. </strong></div>
                    <div class="col-md-6">{{$employee->permanent_address??'N/A'}} </div>
                </div>
            </div>

        </div>
    </div>


    @if(count($nominees) > 0)
    <h2 class="mt-3">Nominee Information</h2>
    <div class="section-divider"></div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Nominee Name</th>
                    <th>Bangla Name</th>
                    <th>Relationship</th>
                    <th>Nominee Ratio</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($nominees as $nominee)
                    <tr>
                        <td>{{$nominee->nominee_name??'N/A'}}</td>
                        <td>{{$nominee->nominee_name_bangla??'N/A'}}</td>
                        <td>{{$nominee->nominee_relationship??'N/A'}}</td>
                        <td>{{$nominee->nominee_ratio??'N/A'}} %</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif


    <h4 class="mt-3">Office Information</h4>
    <div class="section-divider"></div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Working Shift </strong> </div>
                    <div class="col-md-7">{{$employee->shift_name ??'N/A'}} </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Start Time </strong> </div>
                    <div class="col-md-7">{{$employee->start_time??'N/A'}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>End Time</strong> </div>
                    <div class="col-md-7">{{$employee->end_time??'N/A'}}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Employee Category</strong> </div>
                    <div class="col-md-7">{{$employee->hr_emp_category_name}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Joining Date </strong> </div>
                    <div class="col-md-7">{{ toDated($employee->date_of_join)??'N/A'}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Confirmation Date</strong> </div>
                    <div class="col-md-7">{{ toDated($employee->date_of_confirmation)??'N/A'}}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Designation </strong> </div>
                    <div class="col-md-7">{{$employee->designations_name}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Department </strong> </div>
                    <div class="col-md-7">{{$employee->departments_name}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Unit</strong> </div>
                    <div class="col-md-7">{{$employee->hr_emp_unit_name}} </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Section </strong> </div>
                    <div class="col-md-7">{{$employee->hr_emp_section_name}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Supervisor </strong> </div>
                    <div class="col-md-7">{{ $employee->supervisor_name??'N/A'}} </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4  pt-2"><strong>Reference Person</strong> </div>
                    <div class="col-md-7">{{ $employee->referance_name??'N/A'}} </div>
                </div>
            </div>
        </div>
    </div>
    <h4 class="mt-3">Educational Information</h4>
    <div class="section-divider"></div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Qualification</th>
                    <th>Degree</th>
                    <th>Institution</th>
                    <th>Passing Year</th>
                    <th>Major Subject</th>
                    <th>Result Type</th>
                    <th>Result</th>
                </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-3">Employment Information</h4>
    <div class="section-divider"></div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Organization Name</th>
                        <th>Department</th>
                        <th>Sub Department</th>
                        <th>Designation</th>
                        <th>Duration</th>
                        <th>Responsibility</th>
                    </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="{{route('employee-entry',$employee->id)}}" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
    <a href="{{route('employee.pdf',$employee->id)}}" class="btn btn-success" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> Print</a>
    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
</div>

