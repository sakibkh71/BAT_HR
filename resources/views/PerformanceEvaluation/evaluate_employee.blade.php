@extends('layouts.app')
@section('content')
    <style>
        .row-select-toggle{
            cursor: default;
        }
        .dropdown-item {
            margin: 0;
            padding: 5px;
        }

    </style>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">

                <div class="ibox-title">
                    <h2>Evaluate Employee</h2>

                    <input type="hidden" value="{{$user_id}}" name="hdn_user_id" id="hdn_user_id">
                    <input type="hidden" value="{{$designation_id}}" name="hdn_designation_id" id="hdn_designation_id">

                </div>
                <div class="ibox-content">
                    <form action="{{url('pe-evaluate-emp')}}" method="post" id="createTitleHeadForm">
                        <div class="col-md-12">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="font-normal"><strong>Select Year</strong> <span class="required">*</span></label>
                                        <div class="">
                                            {{--<input type="text" name="head_name" class="form-control" id="head_name">--}}
                                            <select name="search_year" id="search_year" class="form-control" >
                                                @foreach($access_year_ary as $info)
                                                    <option @if($year == $info) selected @endif value="{{$info}}">{{$info}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="font-normal"><strong>Select Designation</strong> <span class="required">*</span></label>
                                        <div class="">
                                            {{--<input type="text" name="head_name" class="form-control" id="head_name">--}}
                                            <select name="designation_id" id="designation_id" class="designation_id_cls form-control" >
                                                {{--@foreach($designation_ary as $key=>$info)--}}
                                                    {{--<option value="{{$key}}" @if($designation_id == $key) selected @endif>{{$info}}</option>--}}
                                                {{--@endforeach--}}
                                            </select>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-normal"><strong>Select Employees</strong> <span class="required">*</span></label>
                                        <div class="">
                                            {{--<input type="text" name="head_name" class="form-control" id="head_name">--}}
                                            <select name="user_id" id="emp_name_list" class="form-control" >
                                                {{--@foreach($users as $info)--}}
                                                    {{--<option @if($user_id == $info->id) selected @endif value="{{$info->id}}">{{$info->name}}</option>--}}
                                                {{--@endforeach--}}
                                            </select>
                                        </div>
                                        <div class="help-block with-errors has-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 27px;">
                                        <button class="btn btn-primary btn" type="submit" id="createHeadSubmit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="col-md-12" style="margin-top: 20px;">
                        @if(count($result_ary) > 0)
                            <form  method="POST" id="formID">
                                @csrf
                                <h3>Evaluation Question:</h3>

                                @foreach($result_ary as $key=>$val)
                                    {{--{{dd($key)}}--}}
                                    <h4 style="margin-top: 10px;"><u> {{$val['head_name']."(".$val['weight'] ."%)"}}</u></h4>
                                    @php($weight_name = "weight_".$key)
                                    <input type="hidden" value="{{$val['weight']}}" name="{{$weight_name}}">
                                    <input type="hidden" value="{{$user_id}}" name="user_id">
                                    <input type="hidden" value="{{$year}}" name="year">
                                    <input type="hidden" value="{{$config_id}}" name="config_id">
                                    @php($sl = 1)
                                    @foreach($val as $k=>$v)
                                        @if(!in_array($k, ['head_name', 'weight']))
                                            {{--{{dd($v->question)}}--}}
                                            {{--{{dd($v)}}--}}
                                            <strong>{{$sl++.". ".$v->question}}</strong> <br/>


                                            @if($v->type == 'Auto')

                                                @php($btn_name = "questionAuto_".$key."_".$k)
                                                {{--Api Call Raw Code Start --}}

                                            <?php
//                                                $url = 'https://kvstore.p.rapidapi.com/collections';
                                                $url = $v->api;
                                                // Collection object
                                                $data = [
                                                'collection' => 'RapidAPI'
                                                ];
                                                // Initializes a new cURL session
                                                $curl = curl_init($url);
                                                // Set the CURLOPT_RETURNTRANSFER option to true
                                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                                // Set the CURLOPT_POST option to true for POST request
                                                curl_setopt($curl, CURLOPT_POST, true);
                                                // Set the request data as JSON using json_encode function
                                                curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
                                                // Set custom headers for RapidAPI Auth and Content-Type header
                                                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                                                'X-RapidAPI-Host: kvstore.p.rapidapi.com',
                                                'X-RapidAPI-Key: 7xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                                                'Content-Type: application/json'
                                                ]);
                                                // Execute cURL request with all previous settings
                                                $response = curl_exec($curl);
                                                // Close cURL session
                                                curl_close($curl);
                                                echo "<div style='margin-left: 15px; margin-top: 10px;margin-bottom: 10px;'>";
                                                echo $response.PHP_EOL;
                                                echo " %<br/></div>";
                                            ?>
                                                <input type="hidden" name="{{$btn_name}}" value="{{$response.PHP_EOL}}">
                                                {{--Api Call Raw Code END --}}

                                            @else

                                                @php($btn_name = "question_".$key."_".$k)

                                                <div style="margin-left: 15px; margin-top: 10px;margin-bottom: 10px;">
                                                    <input name="{{$btn_name}}" value="{{"bad"}}" type="radio">Bad
                                                    <input name="{{$btn_name}}" value="{{"good"}}" checked type="radio" style="margin-left: 15px;">Good
                                                    <input name="{{$btn_name}}" value="{{"vgood"}}" type="radio" style="margin-left: 15px;">Very Good
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                    {{--{{dd('exit')}}--}}
                                @endforeach
                                <button type="button" class="submit-btn btn btn-success">Submit</button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="empView" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="empview_wrap"></div>
        </div>
    </div>

    <script>
        function getDesignation(year){

            var user_id = ($('#hdn_user_id').val().length > 0)?$('#hdn_user_id').val():0;
            var designation_id = ($('#hdn_designation_id').val().length > 0)?$('#hdn_designation_id').val():0;
            var url2 = "{{URL::to('pe-get-designation-by-year')}}/"+year+"/"+user_id+"/"+designation_id;

            makeAjax(url2,null).done(function (response) {
                console.log(response);
                $('#designation_id').html(response.string_designation);
                $('#emp_name_list').html(response.string_emp);
            });
        }

        function getUser(desig_id, status=null){

            var hdn_designation_id = $('#hdn_designation_id').val();
            var hdn_user_id = $('#hdn_user_id').val();
            var user_id = 0;

            if(hdn_designation_id.length > 0 && hdn_user_id.length > 0 && status != 'change_event'){
                var desig_id = hdn_designation_id;
                var user_id = hdn_user_id;
            }

            var url = "{{URL::to('pe-get-user-by-designation')}}/"+desig_id+"/"+user_id;
            $.get(url, function(data){
                console.log(data);
                $('#emp_name_list').html(data);
            });
        }

        $(document).ready(function () {

            getDesignation($('#search_year').val());

            $(document).on('change','#designation_id',function () {
                getUser($('#designation_id').val(), 'change_event');
                $('#formID').hide();
            });

            $(document).on('click','.submit-btn',function (e) {
                e.preventDefault();

                swalConfirm('Are you sure?').then(function(e) {
                    if(e.value){
                        var data = $('#formID').serialize();
                        var url = "{{url('pe-evaluate-emp-store')}}";

                        makeAjaxPost(data,url,null).done(function (response) {
                            if(response.code == 500){
                                swalError(response.msg);
                            }
                            else{
                                swalRedirect("{{url('pe-evaluation-list')}}", response.msg, 'success');
                            }
                        });
                    }
                });
            });

            $(document).on('change','#search_year',function () {
                getDesignation($('#search_year').val());
                $('#formID').hide();
            });
        });
    </script>


@endsection
