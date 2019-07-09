<!doctype html>
<html>
<head>
    <title>@yield('title', 'ระบบทะเบียนชมรม โรงเรียนเตรียมอุดมศึกษา')</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#009688"/>
    <meta name="google" content="notranslate"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css" rel="stylesheet"/>
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
            <!-- Dropdown Structure -->
            <ul id="dropdown1" class="dropdown-content">
                <li <?= Request::is('info') ? 'class="active"' : '' ?>><a href="/info">รายละเอียด</a></li>
                <li <?= Request::is('contact') ? 'class="active"' : '' ?>><a href="/contact">ติดต่อ</a></li>
                @if (session()->has('student') OR session()->has('president'))
                    <li><a href="/logout">ออกจากระบบ</a></li>
                @endif
            </ul>
            <ul id="nav-mobile" class="side-nav">
                <li <?= Request::is('/') ? 'class="active"' : '' ?>><a href="/">หน้าหลัก</a></li>
                <li <?= Request::is('info') ? 'class="active"' : '' ?>><a href="/info">รายละเอียด</a></li>
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
    <!--
    @if (session()->has('student') OR session()->has('president'))
        <div id="bottomstick">#RegClubsTriam</div>
    @endif
    -->
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
    <script>
        $(function () {
            $(".button-collapse").sideNav();
            $(".dropdown-button").dropdown();

            @if (session('notify'))
                Materialize.toast("{{session('notify')}}", 4000);
            @endif
        });
    </script>
    @if (isset($_SERVER["REQUEST_TIME_FLOAT"]))
    <!-- It took {{ LARAVEL_START-$_SERVER["REQUEST_TIME_FLOAT"] }} milliseconds to start the framework -->
    @endif
    <!-- It took {{ (microtime(true)-LARAVEL_START)*1000 }} milliseconds (since the framework started) to process this page --->
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
