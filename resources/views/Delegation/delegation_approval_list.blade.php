@extends('layouts.app')
@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row" id="approval-form-container">
        <div class="col-lg-12 no-padding">
            <div class="ibox">
                <div class="ibox-title">
                    <h2>My Approval Modules</h2>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        @php($approval_exists=0)
                        @foreach($approval_modules_names as $key => $val)
                            <?php
                            if($val->custom_query) {
                                $custom_query = str_replace('@USER_ID',Auth::user()->id, $val->custom_query);
                                $total = DB::SELECT($custom_query);
                                $counted = $total[0]->Total;
                            } else {
                                $slug = $val->unique_id_logic_slug;
                                $app_count = app('App\Http\Controllers\Delegation\DelegationProcess')->waitingForApproval($slug);
                                $counted = count($app_count['results']);
                            }
                            ?>
                        @if($counted>0)
                            @php($approval_exists++)
                            <div class="col-3">
                                <div class="widget {{$val->color_class}} goto_module_url text-center"
                                     data-url="{{$val->approval_url}}"
                                     data-slug="{{$val->unique_id_logic_slug}}">
{{--                                <div class="widget {{$val->color_class}} get_module_url text-center" data-url="{{URL::to($val->approval_url)}}" >--}}
                                        <div class="m-b-md">

                                            <h1 class="m-xs"><i class="fa fa-warning"></i> {{$counted}}</h1>
                                            <h3 class="font-bold no-margins">
                                                {!! str_replace('-','<br>',$val->sys_approval_modules_name) !!}
                                            </h3>
                                        </div>
                                    </div>
                            </div>
                         @endif
                        @endforeach
                        @if($approval_exists<=0)
                            <div class="col-12">
                                <h3>No Approve Request found!</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .goto_module_url{
        cursor: pointer;
    }
</style>
<script>
    $('.goto_module_url').on('click', function (e) {
        e.preventDefault();
        var data = {
            "_token": "{{ csrf_token() }}",
            'route' : $(this).data('url'),
            'slug' : $(this).data('slug')
        };
        var url = "<?php echo URL::to('seen-approval-notification') ?>";
        makeAjaxPost(data, url).then(function (result) {
            if(result.message == 'Success'){
                window.location.href = result.url;
            }
        });
    });
</script>
@endsection
