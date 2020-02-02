@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    {{csrf_field()}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h2>Employee Grade List</h2>
                        <div class="ibox-tools">
                            <a href="{{route('hr-emp-grade-entry')}}" class="btn btn-primary btn-xs" id="new-entry"><i class="fa fa-plus"></i> New Item</a>
                            <button class="btn btn-info btn-xs ladda-button hide" id="edit-row" ata-style="expand-right"><span class="ladda-label"><i class="fa fa-pencil"></i> Edit Item</span><span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div></button>
                            <button class="btn btn-danger btn-xs hide" id="delete-rows"><i class="fa fa-minus-circle"></i> Delete Item</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="checkbox-clickable table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th width="">Employee Grade Name</th>
                                    <th width="">Basic Salary</th>
                                    <th width="">House Rent Amount</th>
                                    <th width="">House Rent</th>
                                    <th width="">Min Medical</th>
                                    <th width="">Min TA/DA</th>
                                    <th width="">Min Food</th>
                                    <th width="">Min Gross</th>
                                    <th width="25%">Yearly Increment</th>
                                    <th width="25%">Attendance Bonus</th>
                                    <th width="25%">Ot Aplplicable</th>
                                    <th width="25%">PF Aplplicable</th>
                                    <th width="25%">Insurance Aplplicable</th>
                                    <th width="25%">Description</th>
                                    <th width="25%">status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($emp_grades)
                                    @foreach($emp_grades as $emp)
                                        <tr id="{{$emp->hr_emp_grades_id}}" class="row-select-toggle" data-id="{{$emp->hr_emp_grades_id}}">
                                            <td>{{ $emp->hr_emp_grade_name }}</td>
                                            <td>{{!empty($emp->basic_salary) ? number_format($emp->basic_salary,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->house_rent_amount) ? number_format($emp->house_rent_amount,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->house_rent) ? number_format($emp->house_rent,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->min_medical) ? number_format($emp->min_medical,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->min_tada) ? number_format($emp->min_tada,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->min_food) ? number_format($emp->min_food,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->min_gross) ? number_format($emp->min_gross,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->yearly_increment) ? number_format($emp->yearly_increment,2) : 'N/A'}}</td>
                                            <td>{{!empty($emp->attendance_bonus) ? number_format($emp->attendance_bonus,2) : 'N/A'}}</td>
                                            <td>{{ $emp->ot_applicable }}</td>
                                            <td>{{ $emp->pf_applicable }}</td>
                                            <td>{{ $emp->insurance_applicable }}</td>
                                            <td>{{ $emp->description }}</td>
                                            <td>{{ $emp->status }}</td>
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
    <script>
        var ids = [];
        $(document).ready(function() {
            $('#edit-row').hide();
            $(document).ready(function() {
                var dtTable = $('.master-grid').DataTable({
                    "aaSorting": []
                });
            });
        });
        /*----------------------------------------------------*/
        $(document).on('click','.row-select-toggle',function (e) {
            if($(this).toggleClass('selected')){
                var id = $(this).data('id');
                if($(this).hasClass('selected')) {
                    ids.push(id);
                } else {
                    ids.splice(ids.indexOf(id),1);
                }
                if(ids.length == 1) {
                    $('#edit-row').show();
                    $('#url-link-edit').show();
                } else {
                    $('#edit-row').hide();
                    $('#url-link-edit').hide();
                }

                if (ids.length >= 1) {
                    $('#delete-rows').show();
                }else{
                    $('#delete-rows').hide();
                }
            }
        });

        /*----------------------------------------------------*/

        $('#edit-row').click(function () {
            if (ids.length == 1) {
                window.location.replace("{{URL::to('hr-emp-grade-entry')}}/"+ids[0]);
            }else{
                swalError('Sorry! please select row first');
            }
        });

        $('#delete-rows').click(function () {
            swalConfirm()
            if (ids.length >=0) {
                swalConfirm('To approve this.').then(function (s) {
                    if (s.value) {
                        var idar = [];
                        $(".row-select-toggle.selected").each(function () {
                            var val = $(this).data('id');
                            idar.push(val);
                        });

                        var url = "{{route('hr-emp-grade-destroy')}}";
                        var _token = "{{ csrf_token() }}";
                        var data = {_token: _token, ids: idar};

                        var redirecturl = "{{route('hr-emp-grade')}}"

                        makeAjaxPost(data, url, null).then(function (s) {
                            if (s.status == 'success') {
                                swalRedirect(redirecturl, 'Employee Grade Item Delete successfully')
                            } else {
                                swalError('Something wrong!');
                            }
                        });
                    }
                });
            }else{
                swalError('Sorry! please select row first');
            }
        });




    </script>
@endsection
