@extends('layouts.master')

@section('title', 'เกิดข้อผิดพลาด!')

@section('style')
    <style>
        main {
            text-align: center
        }

        body {
            background-color: #009688;
            animation: fadein 4s;
        }

        @keyframes fadein {
            from {
                background-color: #f44336;
            }
            to {
                background-color: #009688;
            }
        }

        h3 {
            font-weight: 900;
            text-shadow: 0.7px 0.7px #aaa;
        }

        nav, footer {
            background-color: transparent !important;
            box-shadow: none;
        }

        .grey-text a {
            color: white;
        }

        .en {
            font-family: "Roboto", Sans-Serif;
        }
    </style>
@endsection

@section('nav')
    <nav role="navigation">
        <div class="nav-wrapper container">
            <a id="logo-container" href="/" class="brand-logo th"><img src="/phrakiao.png" style="height:1.8rem"/> ระบบทะเบียนชมรม</a>
            <ul class="right hide-on-med-and-down">
                <li><a href="/">หน้าหลัก</a></li>
            </ul>
        </div>
    </nav>
@endsection

@section('main')
    <div class="z-depth-1 fullwidth grey lighten-5 row" style="padding:2.3rem;border-radius: 0.2rem;max-width:800px;margin:auto;margin-top:3rem;color: #333333">
        <div class="col s12 m3 l2 center-align">
            <i class="large material-icons red-text">error</i>
        </div>
        <div class="col s12 m9 l10 left-align">
            <h4>เกิดปัญหา! <span class="en" style="font-size:0.6em">{{ $title }}</span></h4>
            @if (isset($code))
                กรุณาติดต่อผู้ดูแลระบบ โดยแจ้งรหัส {{ $code }}
            @elseif (isset($description))
                {!! $description !!}
            @else
                โปรดลองใหม่อีกครั้ง หากปัญหายังคงอยู่โปรดแจ้งผู้ดูแลระบบโดยระบุอาการ เวลา และข้อความที่แสดง
            @endif
            <br/><br/>
        </div>
    </div>
@endsection

@section('footer')
    <footer class="page-footer">
        <div class="footer-copyright">
            <div class="container">
                <span class="hide-on-print">งานกิจกรรมพัฒนาผู้เรียน โรงเรียนเตรียมอุดมศึกษา</span>
                <div class="hide-on-screen">
                    <div class="col s7">ระบบทะเบียนชมรม โรงเรียนเตรียมอุดมศึกษา</div>
                </div>
            </div>
        </div>
    </footer>
@endsection