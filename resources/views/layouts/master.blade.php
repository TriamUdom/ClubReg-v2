<!doctype html>
<html>
<head>
    <title>@yield('title', 'ระบบทะเบียนชมรม โรงเรียนเตรียมอุดมศึกษา')</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#009688"/>
    <meta name="google" content="notranslate"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css" integrity="sha256-e22BQKCF7bb/h/4MFJ1a4lTRR2OuAe8Hxa/3tgU5Taw=" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet"/>
    <link href="/css/app.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="/favicon.png"/>
    <!--
    Developed by TUCMC
    Siwat Techavoranant (TU78) <keenthekeen>
    Piyachet Kanda (TU79) <piyachetk>
    Possawat Suksai (TU81) <iammarkps>
    -->
    <!--
    If you want to join us contact: cmc@triamudom.club!
    -->
    @yield('style')
</head>
<body>
@section('nav')
    <nav class="teal" role="navigation">
        <div class="nav-wrapper container">
            <a id="logo-container" href="/" class="brand-logo th">
                <img src="/phrakiao.png" style="height:1.8rem; vertical-align: middle"/>
                ระบบทะเบียนชมรม
                <span class="hide-on-med-and-down">โรงเรียนเตรียมอุดมศึกษา</span>
            </a>
            <ul class="right hide-on-med-and-down">
                <li <?= Request::is('/') ? 'class="active"' : '' ?>><a href="/">หน้าหลัก</a></li>
                <li><a class="dropdown-button" href="#!" data-activates="dropdown1">เพิ่มเติม <i class="material-icons right">arrow_drop_down</i></a></li>
            </ul>
            <ul id="dropdown1" class="dropdown-content">
                {{--<li <?= Request::is('info') ? 'class="active"' : '' ?>><a href="/info">รายละเอียด</a></li>--}}
                <li <?= Request::is('contact') ? 'class="active"' : '' ?>><a href="/contact">ติดต่อ</a></li>
                @if (session()->has('student') OR session()->has('president'))
                    <li><a href="/logout">ออกจากระบบ</a></li>
                @endif
            </ul>
            <ul id="nav-mobile" class="side-nav">
                <li <?= Request::is('/') ? 'class="active"' : '' ?>><a href="/">หน้าหลัก</a></li>
                {{--<li <?= Request::is('info') ? 'class="active"' : '' ?>><a href="/info">รายละเอียด</a></li>--}}
                <li <?= Request::is('contact') ? 'class="active"' : '' ?>><a href="/contact">ติดต่อ</a></li>
                @if (session()->has('student') OR session()->has('president'))
                    <li><a href="/logout">ออกจากระบบ</a></li>
                @endif
            </ul>
            <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
        </div>
    </nav>
@show

@yield('beforemain')

<main class="container">
    @yield('main')
</main>

@section('footer')
    <footer class="page-footer teal">
        <div class="footer-copyright">
            <div class="container">
                <span class="hide-on-screen">ระบบทะเบียนชมรม </span><a href="https://clubs.triamudom.ac.th">งานกิจกรรมพัฒนาผู้เรียน โรงเรียนเตรียมอุดมศึกษา</a>
                {!! session()->has('userid') ? '| เข้าสู่ระบบในชื่อ '.session('username') : '' !!}
            </div>
        </div>
    </footer>
@show

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js" integrity="sha256-uWtSXRErwH9kdJTIr1swfHFJn/d/WQ6s72gELOHXQGM=" crossorigin="anonymous"></script>
    <script>
        $(function () {
            $(".button-collapse").sideNav();
            $(".dropdown-button").dropdown();

            @if (session('notify'))
                Materialize.toast("{{session('notify')}}", 4000);
            @endif
        });
    </script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-74722377-5', 'auto');

        @if (session()->has('president'))
            ga('set', 'userId', '{{ sha1('PRESIDENT'.session('president')) }}');
        @elseif (session()->has('student'))
            ga('set', 'userId', '{{ sha1('STUDENT'.session('student')) }}');
        @endif

        ga('send', 'pageview');

    </script>
@show
</body>
</html>
