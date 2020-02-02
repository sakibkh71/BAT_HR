@extends('layouts.app')
@section('content')
    <style>
        ul li{
            list-style: none;
        }
    </style>
    <script src="{{asset('public/js/plugins/bootstrap-validator/validator.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h2>Menu Privilege for Level</h2>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-9">
                                <form action="{{route('menu-privilege')}}" method="post" id="navForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-normal" for="modules_id"><strong>Menu From Which Module</strong> <span class="required">*</span></label>
                                                {{__combo('modules', array('selected_value'=>isset($post['modules_id'])?$post['modules_id']:'', 'attributes'=> array( 'name'=>'modules_id', 'required'=>true, 'id'=>'modules_id', 'class'=>'form-control multi')))}}
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-normal" for="modules_id"><strong>Privilege for which Level</strong> <span class="required">*</span></label>
                                                {{__combo('levels', array('selected_value'=>isset($post['user_level_id'])?$post['user_level_id']:'', 'attributes'=> array( 'name'=>'user_level_id', 'required'=>true, 'id'=>'user_level_id', 'class'=>'form-control multi')))}}
                                                <div class="help-block with-errors has-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group pt-1 mt-4">
                                                <button type="submit" class="btn btn-primary full-width">See Menu Privilege</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-3  pt-1 mt-4">
                                <button type="button" id="setPrivilage" class="btn btn-success full-width">Set Privilege</button>
                            </div>
                        </div>
                        <div class="ibox-content mt-2 pt-2">
                            <form action="{{route('set-privilege')}}" method="post" id="setPrivilegeForm">
                                @csrf
                                {{__combo('levels', array('selected_value'=>isset($post['user_level_id'])?$post['user_level_id']:'', 'attributes'=> array('name'=>'user_level_id', 'class'=>'no-display')))}}
                                {{__combo('modules', array('selected_value'=>isset($post['modules_id'])?$post['modules_id']:'', 'attributes'=> array( 'name'=>'modules_id', 'class'=>'no-display')))}}
                            {!! $menus !!}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('public/js/plugins/nestable/jquery.nestable.js')}}"></script>
    <script>
        var makeNav ='';
          $(document).ready(function(){
            //Form Validator
            $('#navForm').validator();


            /*
             * Save privilege  Order
             */
            $('#setPrivilage').click(function (e) {
                e.preveltDefault;
                $('#setPrivilegeForm').submit();
            });



            @if(!empty(Session::get('succ_msg')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    swalSuccess("{{Session::get('succ_msg')}}");
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif

        });

    </script>
@endsection
