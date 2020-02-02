<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! getOptionValue('application_name') !!}</title>
    <link rel="shortcut icon" type="image/png" href="{{asset(getOptionValue('company_logo2'))}}"/>
    <link href="{{asset('public/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Apsisstyle.css')}}" rel="stylesheet newest">
    <style>
        .background-slider {
            position:absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            left: 0;
            top: 0;
        }
        .background-slider img {
            position:absolute;
            left:0;
            top:0;
            width: 100%;
        }
    </style>
</head>
<body class="apsis-bg">

<div class="loginscreen">
    <div class="login-container animated fadeInDownBig">
        <h1 class="brand-logo">
            <img alt="image" class="img-responsive" style="max-width: 80px" src="{{asset(getOptionValue('company_logo2'))}}"/>
        </h1>
        <h2 class="logo-name"><strong>{!! getOptionValue('company_name') !!} </strong></h2>
        <span class="subtitle">{!! getOptionValue('application_name') !!}</span>

        <form class="m-t" role="form" method="POST" action="{{ route('login') }}" id="loginFrm">
            {{ csrf_field() }}
            <div class="input-group m-b">
                <div class="input-group-prepend">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                </div>
                <input id="email" type="email" placeholder="username or email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                    <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                @endif
            </div>
            <div class="input-group m-b">
                <div class="input-group-prepend">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                </div>
                <input id="password" type="password" placeholder="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                @if ($errors->has('password'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            @if(session()->get('multi_log_message'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="error_message_multi">{{ session()->get('multi_log_message') }}</span>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success multi_log_action" action_type="no" data-dismiss="modal">No</button>
                        <a class="btn btn-primary btn-ok multi_log_action" action_type="yes">Yes</a>
                    </div>
                </div>
            @endif

            <div class="remember_me text-center">
                <input class="custom-check" type="checkbox" tabindex="3" value="remember-me" id="remember_me">
                <label for="remember_me">Remember Me</label>
            </div>
            <div class="row">
                <div class="col-sm-8 offset-sm-2">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Login') }}</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <a class="forget-pass" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>

<p class="copyright"> <small>Apsis Solutions © 2018</small> </p>


<div class="background-slider">
        <img src="{{asset('public/img/slider/british-tabacco.jpg')}}" alt="British American Tobacco" />
        <img src="{{asset('public/img/slider/British-American-Tobacco-4.jpg')}}" alt="British American Tobacco" />
        <img src="{{asset('public/img/slider/British-American-Tobacco-3.jpg')}}" alt="British American Tobacco" />
        <img src="{{asset('public/img/slider/British-American-Tobacco-2.jpg')}}" alt="British American Tobacco" />
        <img src="{{asset('public/img/slider/British-American-Tobacco-1.jpg')}}" alt="British American Tobacco" />
</div>

<script src="{{asset('public/js/jquery-3.1.1.min.js')}}"></script>
<script src="{{asset('public/js/bootstrap.js')}}"></script>
<script>
    $(function(){
        $('.background-slider img:gt(0)').hide();
        setInterval(function(){$('.background-slider :first-child').fadeOut(1500).next('img').fadeIn(1500).end().appendTo('.background-slider');}, 5000);
    });

    $(document).on('click','.multi_log_action',function(e){
        e.preventDefault();
        var action_type = $(this).attr('action_type');
        if(action_type == 'no'){
            $('.alert').hide();
        }else{
            $.ajax({
                type: "POST",
                url: '{{URL::to("multi-login-action")}}',
                data: $('#loginFrm').serialize(),
                success: function (response) {
                    if(response == true){
                        $('#loginFrm').submit();
                    }else{
                        $('.alert').show();
                        $('.error_message_multi').text('You are not this person.');
                    }
                }
            });
        }
    });

    //remember me section
    $(function() {
        if (localStorage.chkbx && localStorage.chkbx != '') {
            $('#remember_me').attr('checked', 'checked');
            $('#email').val(localStorage.usrname);
        } else {
            $('#remember_me').removeAttr('checked');
            $('#loginEmail').val('');
        }

        $('#remember_me').click(function() {
            if ($('#remember_me').is(':checked')) {
                localStorage.usrname = $('#email').val();
                localStorage.chkbx = $('#remember_me').val();
            } else {
                localStorage.usrname = '';
                localStorage.chkbx = '';
            }
        });
    });
</script>
</body>
</html>



