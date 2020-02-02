@inject('moduleController', 'App\Http\Controllers\ModuleController')
<script>
    base_url = '{{URL::to('/')}}';
</script>
<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-warning no-shadow " href="#"><i class="fa fa-bars"></i> </a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a  href="{{URL::to('get-approval-modules')}}">
                    <i class="fa fa-users"></i>
                    My Approval
                    {{--<span class="label label-danger unread_counter">{{getUnreadNotification()}}</span>--}}
                </a>
                {{--<ul class="dropdown-menu dropdown-messages">--}}
                    {{--<li><a href="{{ URL::to('get-approval-modules') }}">--}}
                            {{--<i class="fa fa-th-large"></i> Delegation Modules--}}
                            {{--<span style="padding: 7px;" class="label label-danger pull-right unread_counter">{{getUnreadNotification()}}</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="{{URL::to('get-delegation-list')}}"><i class="fa fa-table"></i> Approved List</a> </li>--}}
                {{--</ul>--}}
            </li>
            @php($modules = session()->get('USER_MODULES'))
            @if(!empty($modules) && count($modules)>1)
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <i class="fa fa-cogs"></i>
                    {!! session::get('MODULE_LANG') !!}<b class="caret"></b>
                </a>


                <ul class="dropdown-menu dropdown-messages">
                    @foreach ($moduleController->getModuleList() as $val)
                        <li>
                            <a href="{{URL::to("/moduleChanger/".$val->id)}}">
                                <i class="{{ $val->modules_icon }}" style="font-size:10px"></i> {{ $val->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endif
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-bell"></i>  <span class="label label-primary" id="notification_numbers"></span>
                </a>
                <ul class="dropdown-menu dropdown-alerts" id="notifications_prepend" >


                    <li>
                        <div class="text-center link-block">
                            <a href="#" id="see_all_notification" class="dropdown-item">
                                <strong>See All Notifications</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    @if(Session::has('USER_NAME'))
                        <div class="feed-element">
                            @if(file_exists(asset('public'.session('USER_IMAGE'))))
                                <img alt="image" class="rounded-circle float-left" style="margin-right: 5px;" src="{{asset('public'.session('USER_IMAGE'))}}"/>
                            @else
                                <img alt="image" class="rounded-circle float-left" style="margin-right: 5px;" src="{{asset('public/img/default-user.jpg')}}"/>
                            @endif
                            <div class="media-body">
                                <strong>{{ Session::get('USER_NAME') }}</strong>
                                <br/>
                                <small class="">
                                    @if(Session::has('DESIGNATION_NAME'))
                                        {{ Session::get('DESIGNATION_NAME') }}
                                    @else
                                        User
                                    @endif
                                    <b class="caret"></b>
                                </small>
                            </div>
                        </div>
                    @endif
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{route('get-user-profile')}}">
                            <i class="fa fa-user"></i> My Profile
                        </a>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li class="text-danger">
                        <a class="" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <b><i class="fa fa-sign-out"></i> {{ __('Logout') }}</b>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<script>

    $.ajax({
        type:'get',
        data:{
            'user_id':{{session()->get('USER_ID')}},
        },
        url:'{{url('get-user-all-notifications')}}',
        success:function (data) {
            console.log(data);
           $("#notification_numbers").html(parseInt(data.length)!=0?data.length:0);
           var loop_control=0;

           var html_to_prepend='';
            $.each(data,function (i,v) {
                var title=' ';
               // alert(v.sys_approval_modules_name);
                if(v.sys_approval_modules_name === null){

                   title=v.notification_title;
                }else{

                   title=v.sys_approval_modules_name;
                }
               // alert(title);
                var content=v.content;
                var url=v.url_ref;
                var event_for=v.event_for;
                html_to_prepend+='<li>' +
                    '<div class="dropdown-item see_notification_details" data-url="'+url+'" data-event="'+event_for+'" style="overflow:hidden; text-overflow: ellipsis; cursor: pointer;" >' +
                    '<i class="fa fa-envelope fa-fw"></i>'
                     +title+'<br>'+
                    content+
                    '</div>'+
                    '</li>' +
                    ' <li class="dropdown-divider"></li>';
                loop_control++;
                if(loop_control == 5){
                  return false;
                }



            });
            $('#notifications_prepend').prepend(html_to_prepend);
            console.log(html_to_prepend);
           // $.each(data,function (i,v) {
           //     // var diff=Math.abs(new Date() - new Date(v.created_at.replace(/-/g,'/')));
           //     // console.log(diff.customFormat( "#DD#/#MM#/#YYYY# #hh#:#mm#:#ss#" )+'<br>');
           //     var url=v.url_ref;
           //     var content=v.content;
           //
           //
           // });
        }
    });

$(document).on('click','.see_notification_details',function (e) {


    var url=$(this).data('url');
    var event_for=$(this).data('event');
    $.ajax({
        type:'get',
        data:{
            url:url,
            event_for:event_for
        },
        url:'{{url('redirect_to_notification_route')}}',
        success:function (result) {

            window.location.href = result;

        }
    })
})
$(document).on('click','#see_all_notification',function () {

    window.location.href = '{{ route('see-all-notifications')}}';

    {{--$.ajax({--}}
       {{--type:'get',--}}
       {{--url:'{{url('see-all-notifications')}}',--}}
        {{----}}
    {{--});--}}
});

</script>