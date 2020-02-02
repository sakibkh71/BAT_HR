<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<div class="modal-header">
    <button type="button" class="close text-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title">Increment & Promotion History</h4>
</div>
<div class="modal-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Employee Name:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{$emp_log->name}}</dd>
                            </div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Date of Birth:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{toDated($emp_log->date_of_birth)}}</dd>
                            </div>
                        </dl>

                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Gender:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{$emp_log->gender}}</dd>
                            </div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Department:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{$emp_log->departments_name}}</dd>
                            </div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Designation:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{$emp_log->designations_name}}</dd>
                            </div>
                        </dl>

                    </div>
                    <div class="col-lg-6" id="cluster_info">

                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Status:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1"><?php echo $emp_log->status=='Active'?'<span class="label label-primary">Active</span>':'<span class="label label-warning">Inactive</span>'?></dd>
                            </div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Salary Grade:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{$emp_log->hr_emp_grade_name}}</dd>
                            </div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Gross Salary:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{number_format($emp_log->min_gross,2)}}</dd>
                            </div>
                        </dl>

                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Salary Applied Date:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{toDated($emp_log->applicable_date)}}</dd>
                            </div>
                        </dl>

                    </div>
                </div>
            </div>

    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li><a class="nav-link active show" href="#increment_tab" data-toggle="tab">Increment</a></li>
                        <li><a class="nav-link" href="#promotion_tab" data-toggle="tab">Promotion</a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="increment_tab">
                        <div class="col-md-12">
                            <h3>Increment History</h3>
                            <div class="table-responsive">
                                <table class="employee_increment_list table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Applicable Date</th>
                                        <th>Previous Gross Salary</th>
                                        <th>Basic</th>
                                        <th>House Rent</th>
                                        <th>Increment Amount</th>
                                        <th>Gross Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($incrementLog))
                                        @foreach($incrementLog as $i=>$emp)
                                            <tr id="{{$emp->hr_employee_record_logs_id}}">
                                                <td>{{($i+1)}}</td>
                                                <td>{{toDated($emp->applicable_date)}}</td>
                                                <td class="text-right">{{number_format($emp->previous_gross,2)}}</td>
                                                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                                                <td class="text-right">{{number_format($emp->house_rent_amount,2)}}</td>
                                                <td class="text-right">{{number_format($emp->gross_salary-$emp->previous_gross,2)}}</td>
                                                <td class="text-right">{{number_format($emp->gross_salary,2)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="promotion_tab">
                        <div class="col-md-12">
                            <h3>Promotion History</h3>
                            <div class="table-responsive">
                                <table class="employee_increment_list table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Applicable Date</th>
                                        <th>Designation</th>
                                        <th>Grade</th>
                                        <th>Basic</th>
                                        <th>House Rent</th>
                                        <th>Gross Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($promotionLog))
                                        @foreach($promotionLog as $i=>$emp)
                                            <tr id="{{$emp->hr_employee_record_logs_id}}">
                                                <td>{{($i+1)}}</td>
                                                <td>{{toDated($emp->applicable_date)}}</td>
                                                <td>{{$emp->designations_name}}</td>
                                                <td>{{$emp->record_grade_name}}</td>
                                                <td class="text-right">{{number_format($emp->basic_salary,2)}}</td>
                                                <td class="text-right">{{number_format($emp->house_rent_amount,2)}}</td>
                                                <td class="text-right">{{number_format($emp->gross_salary,2)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-warning">Close</button>
    </div>
</div>
<script>
    $('.employee_increment_list').dataTable();
</script>