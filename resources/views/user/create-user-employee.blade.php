@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>User Entry Form</h2>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('store-employee-data')}}" method="post" oninput='cpass.setCustomValidity(cpass.value != password.value ? "Passwords do not match." : "")' id="userForm">
                            @csrf
                            <input type="hidden" name="sys_users_id" value="{{$user->id}}">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Full Name <span class="required">*</span></label>
                                            <input type="text" name="name" placeholder="Full Name"
                                                   class="form-control"
                                                   value="{{ !empty($user->name)?$user->name:old('name')}}"
                                                   data-error="Full name is required"
                                                   readonly = "readonly"
                                                   required="">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Email <span class="required">*</span></label>
                                            <input type="email" name="email" placeholder="Email"
                                                   class="form-control"
                                                   value="{{ !empty($user->email)?$user->email:old('email')}}"
                                                   data-error="Email is required" required="">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">User Code <span class="required">*</span></label>
                                            <input type="text" name="user_code" placeholder="User Code"
                                                   class="form-control"
                                                   value="{{ !empty($user->user_code)?$user->user_code:old('user_code')}}"
                                                   data-error="User Code is required"
                                                   readonly = "readonly"
                                                   required="">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">User Name <span class="required">*</span></label>
                                            <input type="text" name="username" placeholder="User Name"
                                                   class="form-control"
                                                   value="{{ !empty($user->username)?$user->username:old('username')}}"
                                                   data-error="User Name is required" required="">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Password <span class="required">*</span></label>
                                            <input type="password" name="password" placeholder="Password"
                                                   class="form-control"
                                                   value=""
                                                   data-error="Password is required" required="">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Confirm Password <span class="required">*</span></label>
                                            <input type="password" name="cpass" placeholder="Confirm Password"
                                                   class="form-control"
                                                   value=""
                                                   data-error="Passwords do not match" required="">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Default URL </label>
                                            <input type="text" name="default_url" placeholder="Default URL"
                                                   class="form-control"
                                                   value="{{ !empty($user->default_url)?$user->default_url:old('default_url')}}"
                                                   data-error="Default URL is required">
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Default Module <span class="required">*</span></label>
                                            {{__combo('modules',array('selected_value'=>!empty($user->default_module_id)?$user->default_module_id:'','attributes'=>array('name'=>'default_module_id','class'=>'form-control multi')))}}
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>--}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">User Levels Permission <span class="required">*</span></label>
                                            {{__combo('user_levels',array('selected_value'=>!empty($user->user_levels)?explode(',',$user->user_levels):'','attributes'=>array('name'=>'user_levels[]','multiple'=>1,'class'=>'form-control multi')))}}
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">User Modules Permission <span class="required">*</span></label>
                                            {{__combo('user_modules',array('selected_value'=>!empty($user->user_modules)?explode(',',$user->user_modules):'','attributes'=>array('name'=>'user_modules[]','multiple'=>1,'class'=>'form-control multi')))}}
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-4">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label class="form-label">User House Permission <span class="required">*</span></label>--}}
                                            {{--{{__combo('bat_company_all',array('selected_value'=>!empty($user->privilege_houses)?explode(',',$user->privilege_houses):'','attributes'=>array('name'=>'privilege_houses[]','multiple'=>1,'class'=>'form-control multi')))}}--}}
                                            {{--<div class="help-block with-errors has-feedback"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-md-4">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label class="form-label">User Distributor Point Permission <span class="required">*</span></label>--}}
                                            {{--{{__combo('bat_distributor_point_all',array('selected_value'=>!empty($user->privilege_points)?explode(',',$user->privilege_points):'','attributes'=>array('name'=>'privilege_points[]','multiple'=>1,'class'=>'form-control multi')))}}--}}
                                            {{--<div class="help-block with-errors has-feedback"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    <div class="col-md-12">
                                        @php echo $multiple_search_criteria; @endphp
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label"></label>
                                            <button type="submit" id="userFormSubmit" class="btn btn-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#userForm').validator();
        $(document).validator().on('submit','#userForm',function (e) {
            if (e.isDefaultPrevented()) {
                swalError("Fill up required Field");
            } else {
                e.preventDefault();
                var data = $(this).serialize()+'&sys_users_id='+'{{!empty($user->id)?$user->id:''}}';
                var url = '{{route('store-employee-data')}}';
                Ladda.bind($('#userFormSubmit'));
                var load = $('#userFormSubmit').ladda();
                makeAjaxPost(data, url, load).done(function(response) {
                    if(response.success){
                        swalSuccess();
                        var url = '<?php echo URL::to('user-list');?>';
                        window.location.replace(url);
                    }else{
                        swalError();
                    }
                });
            }
        });
    </script>
@endsection