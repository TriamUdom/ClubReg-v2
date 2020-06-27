<!doctype html>
<html>
<head>
    <title>@yield('code') @yield('title') - ระบบทะเบียนชมรม โรงเรียนเตรียมอุดมศึกษา</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#009688"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css" integrity="sha256-e22BQKCF7bb/h/4MFJ1a4lTRR2OuAe8Hxa/3tgU5Taw=" crossorigin="anonymous" />
    <link rel="shortcut icon" href="/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet" />
    <style>
        body {
            font-family: 'Kanit', serif;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background-color: #009688;
        }

        main {
            flex: 1 0 auto;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        .brand-logo {
            font-size: 1.5rem !important
        }

        .light {
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
        }

        h1 {
            font-size: 80px
        }

        h2 {
            font-size: 30px;
            margin-bottom: 15px
        }

        h3 {
            font-size: 25px
        }

        main {
            text-align: center
        }

        .footer-copyright a {
            color: lightgrey;
        }

        .footer-copyright a:hover {
            text-decoration: underline
        }
    </style>
</head>
<body>
<nav class="teal white-text" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="/" class="brand-logo"><img src="/phrakiao.png" style="height:1.8rem"/>  ระบบทะเบียนชมรม</a>
        <ul class="right hide-on-med-and-down">
            <li><a href="/">หน้าหลัก</a></li>
        </ul>
    </div>
</nav>
<div class="white-text teal" style="height:30px"></div>

<main class="container">
    <div class="red darken-2 white-text" style="padding: 20px;margin-bottom:20px; padding-top:50px; padding-bottom:50px;">
        @section('content')
            <h1 class="center-align light" id="title">@yield('title')</h1>
            <h2 class="center-align">@yield('description')</h2>
        @show
    </div>
    @section('button')
        <a href="/" class="waves-effect waves-light btn blue darken-2 center-align" style="width:80%;max-width:350px;margin-top:20px">กลับไปยังหน้าหลัก</a>
    @show
</main>

<footer class="page-footer teal">
    <div class="footer-copyright">
        <div class="container">
            งานกิจกรรมพัฒนาผู้เรียน โรงเรียนเตรียมอุดมศึกษา
        </div>
    </div>
</footer>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-88470919-6', 'auto');
    ga('send', 'event', 'Error', 'occur', '@yield('code')');
    ga('send', 'pageview');
</script>
@yield('script')
</body>
</html>
