<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{!! getOptionValue('application_name') !!}</title>
        <link rel="shortcut icon" type="image/png" href="{{asset(getOptionValue('company_logo2'))}}"/>
        @include('includes.assets')
        @php(date_default_timezone_set('Asia/Dhaka'))
    </head>
    <body>
        <div id="wrapper">
            @include('includes.sidebar')
            <div id="page-wrapper" class="srcil-bg">
                @include('includes.header')
                @include('includes.notifications')
                @include('includes.password_notify')
                {{--@include('includes.breadcrumb')--}}
                @yield('content')
                @include('includes.flash-message')
                @include('includes.modal')
                @include('includes.footer')
            </div>
        </div>
    </body>
    <script src="{{asset('public/js/apsisScript.js')}}"></script>
{{--    <link href="{{asset('public/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/toastr/toastr.min.js')}}"></script>
    <script>
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "swing",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        $(document).ready(function () {
            toastr.info('Please wait for the upcoming feature related push notification.','WelCome Here');
        });
    </script>--}}
</html>
