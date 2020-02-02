@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                <div class="col-lg-12 no-padding">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h2>{{$title}}</h2>
                            <div class="ibox-tools">
                                <button class="btn btn-danger btn-xs" id="back-item"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Back</button>
                            </div>
                        </div>
                        <div class="ibox-content">
                                <table>
                                    <tr>
                                        <td width="200px"><strong>Month</strong></td>
                                        <td width="15px">:</td>
                                        <td width="20%"> {{$info->month}} </td>
                                        <td width="200px"><strong>Year</strong></td>
                                        <td  width="15px">: </td>
                                        <td width="20%"> {{$info->year}} </td>
                                    </tr>
                                    <tr>
                                        <td width="200px"><strong>Employee Category</strong></td>
                                        <td  width="15px">: </td>
                                        <td width="20%"> {{$info->hr_emp_category_name}} </td>
                                        <td width="200px"><strong>Working Days</strong></td>
                                        <td width="15px">:</td>
                                        <td width="20%"> {{$info->number_of_working_days}} </td>

                                    </tr>
                                    <tr>
                                        <td width="200px"><strong>Holidays</strong></td>
                                        <td width="15px">:</td>
                                        <td width="20%"> {{$info->number_of_holidays}} </td>
                                        <td width="200px"><strong>Weekend</strong></td>
                                        <td width="15px">:</td>
                                        <td width="20%"> {{$info->number_of_weekend}} </td>

                                    </tr>
                                    <tr>
                                        <td width="200px"><strong>Created Date</strong></td>
                                        <td width="15px">:</td>
                                        <td width="20%"> {{$info->created_at}} </td>
                                        <td width="200px"><strong>Created By</strong></td>
                                        <td width="15px">:</td>
                                        <td width="20%"> {{$info->name}} </td>
                                    </tr>
                                </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>



<script>
    $('#back-item').on('click', function () {
        var view_url = "<?php echo URL::to('hr-employee-salary-month-config-list')?>";
        window.location.replace(view_url);
    });
</script>
@endsection