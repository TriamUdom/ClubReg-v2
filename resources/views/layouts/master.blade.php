<!doctype html>
<html>
<head>
    <!-- Created by Siwat Techavoranant in 2016 -->
    <title>@yield('title', 'ระบบทะเบียนชมรม โรงเรียนเตรียมอุดมศึกษา')</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#009688"/>
    <meta name="google" content="notranslate"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/css/materialize.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet" />
    <link href="/css/app.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="/favicon.png"/>
    @yield('style')
</head>
<body>
@section('nav')
    <nav class="teal" role="navigation">
        <div class="nav-wrapper container">
            <a id="logo-container" href="/" class="brand-logo th"><img src="/phrakiao.png" style="height:1.8rem"/> ระบบทะเบียนชมรม</a>
            <ul class="right hide-on-med-and-down">
                <li <?= Request::is('/') ? 'class="active"' : '' ?>><a href="/">หน้าหลัก</a></li>
                {!! session()->has('student') ? '<li><a href="/logout">ออกจากระบบ</a></li>' : '' !!}
            </ul>
            <ul id="nav-mobile" class="side-nav">
                <li <?= Request::is('/') ? 'class="active"' : '' ?>><a href="/">หน้าหลัก</a></li>
                {!! session()->has('student') ? '<li><a href="/logout">ออกจากระบบ</a></li>' : '' !!}
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/js/materialize.min.js"></script>
    <script>
        $(function () {
            $(".button-collapse").sideNav();

            @if (session('notify'))
                Materialize.toast("{{session('notify')}}", 4000);
            @endif
        });
    </script>
@show
</body>
</html>
