@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Eid Bonus History</h2>
                        <div class="ibox-tools">

                        </div>
                    </div>
                    <div class="ibox-content">

                                    <form method="post" action="{{route('hr-emp-bonus-history')}}" id="bonus_form"
                                          data-toggle="validator">
                                        <div class="row">
                                            @csrf
                                            {!! __getCustomSearch('eid-bonus-generate', @$posted) !!}
                                            <div class="form-group col-md-3">
                                                <label class="form-label"></label>
                                                <div class="input-group">
                                                    <button id="btn_add_employee_list" type="submit"
                                                            class="btn btn-success btn-lg"><i class="fa fa-search"></i>
                                                        Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                        </div>


                            <div class="col-md-12">
                                <div class="pull-right">
                                    <button class="btn btn-success btn-xs" id="edit_item"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                                    <button class="btn btn-danger btn-xs" id="delete_item"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                                </div>

                                <div class="table-responsive">
                                    <table id="employee_list"
                                           class="table table-bordered table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>ID No.</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Grade</th>
                                            <th>DoJ</th>
                                            <th>Gross Salary</th>
                                            <th>Basic Salary</th>
                                            <th>Entitle Bonus</th>
                                            <th>Stamp</th>
                                            <th>Net Payable Bonus</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @if(!empty($employeeList))
                                        @foreach($employeeList as $i=>$emp)
                                                <tr class="row-select-toggle emp_id" data-id="{{$emp->id}}" id="{{$emp->id}}" data-record_id="{{$emp->hr_emp_bonus_id}}">
                                                    <td align="center">
                                                    {{($i+1)}}
                                                    </td>
                                                    <td>{{$emp->user_code}}</td>
                                                    <td>{{$emp->name}}</td>
                                                    <td>{{$emp->departments_name}}</td>
                                                    <td>{{$emp->designations_name}}</td>
                                                    <td>{{$emp->hr_emp_grade_name}}</td>
                                                    <td>{{toDated($emp->date_of_join)}}</td>
                                                    <td class="text-right min_gross">{{number_format($emp->gross_salary,1)}}</td>
                                                    <td class="text-right basic_salary">{{number_format($emp->basic_salary,2)}}</td>
                                                    <td class="text-right">{{$emp->earn_bonus}}</td>
                                                    <td class="text-right">{{$emp->stamp}}</td>
                                                    <td class="text-right payable_amount">{{number_format($emp->earn_bonus-$emp->stamp,2)}}</td>
                                                </tr>
                                        @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="paginate mt-3">
                                        {{ $employeeList->links() }}
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
    </div>
    <script>
        var selected_row = [];
        $(document).on('click','.row-select-toggle',function (e) {
            $(this).toggleClass('selected');
            var record_id = $(this).data('record_id');
            if ($(this).hasClass( "selected" )){
                selected_row.push(record_id);
            }else{
                var index = selected_row.indexOf(record_id);
                selected_row.splice(index,1);
            }
        });

        $(document).on('click', '#delete_item', function (e) {
            e.preventDefault();
            Ladda.bind(this);
            var load = $(this).ladda();
            var data = {bonus_record:selected_row};
            var url = '<?php echo URL::to('hr-bonus-delete');?>';
            if(selected_row.length) {
                swalConfirm("Delete Selected Items").then(function (e) {
                    if (e.value) {
                        makeAjaxPost(data,url,load).done(function (response) {
                            var url2 = window.location;
                            if(response.success){
                                swalRedirect(url2,"Successfully Delete",'success');
                            }else{
                                swalWarning('Operation Failed!');
                            }
                        });
                    }else{
                        load.ladda('stop');
                    }
                });

            }else{
                swalWarning("Please select at least one job!");
            }

        });

        $(document).on('click', '#edit_item', function (e) {
            e.preventDefault();
            Ladda.bind(this);
            var load = $(this).ladda();
            var data = {bonus_record:selected_row,'_token':$('input[name="_token"]').val()};
            var url = '<?php echo URL::to('hr-emp-bonus-edit');?>';
            if(selected_row.length) {
                swalConfirm("Edit Selected Items").then(function (e) {
                    if (e.value) {
                        $.redirectPost(url,data);
                    }else{
                        load.ladda('stop');
                    }
                });

            }else{
                swalWarning("Please select at least one job!");
            }

        });
        $.extend(
            {
                redirectPost: function(location, args)
                {
                    var form = $('<form></form>');
                    form.attr("method", "post");
                    form.attr("action", location);

                    $.each( args, function( key, value ) {
                        var field = $('<input></input>');

                        field.attr("type", "hidden");
                        field.attr("name", key);
                        field.attr("value", value);

                        form.append(field);
                    });
                    $(form).appendTo('body').submit();
                }
            });
    </script>
@endsection