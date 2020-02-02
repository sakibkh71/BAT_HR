<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>APSIS ENGINE</title>
    <link rel="shortcut icon" type="image/png" href="{{asset('public/img/srcil_icon.png')}}"/>
    @include('includes.assets')
    @php(date_default_timezone_set('Asia/Dhaka'))
    <link href="{{asset('public/css/hovicon.css')}}" rel="stylesheet newest">
    <script src="{{asset('public/js/polygonizr.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>
</head>
<body>

<div class="module-wrapper">
    <div class="header-main">
        <div class="brand-logo"><img src="{{asset('public/img/srcil_logo2.png')}}" alt=""></div>
    </div>
    <div class="module-wrap">
        <div class="module-row">
            <div class="row">
                @if(!empty($moduleList))
                    @foreach($moduleList as $module)
                <div class="col-md-4 col-lg-3">
                    <a href="{{URL::to('moduleChanger/'.$module->id)}}" class="module-item">
                        <span class="hovicon data-tilt {{$module->style_class ?? 'effect-8'}}"><i class="{{$module->modules_icon ?? 'fa fa-list'}}"></i></span>
                        <h2>{{ $module->name??'N/A'}}</h2>
                    </a>
                </div>
                    @endforeach
                @endif
            </div>
            <div id="site-landing" class="sparkanimate"></div>
        </div>
    </div>
    <footer class="footer-main">
        <strong>Copyright</strong> Apsis Solutions Limited &copy; 2019
    </footer>

    <script>
        $('#site-landing').polygonizr({
            nodeRelations: 4,
            nodeDotColor: "240, 255, 250",
            nodeLineColor: "240, 255, 250",
            nodeFillColor: "240, 255, 250",
            nodeFillGradientColor: "177, 214, 125",
        });
        $('.data-tilt').tilt();
    </script>
</div>
</body>
</html>