@extends('layouts.master')

@section('style')
    <style>
        body {
            background-color: #009688
        }
    </style>
@endsection

@section('nav')
    @parent
    <div class="white-text teal" style="height:20px"></div>
@endsection

@section('main')
    @php
        $student = \App\User::current();
    @endphp
    <div class="z-depth-1 card-panel" style="max-width:700px;margin:auto">
        <a href="/" class="blue-text"><i class="small material-icons">arrow_back</i> เมนู</a>
        <h4 class="center-align">{{ $club->name }}</h4>
        <h5 class="center-align">{{ $club->english_name }}</h5>
        <p>&ensp;{!! nl2br($club->description) !!}</p>
        <p>สถานะ:
            @if ($club->isAvailableForLevel($student->level))
                @if ($club->isAvailable(true) == 2)
                    <b class="green-text">มีที่นั่งว่าง (Available)</b>
                @elseif (!$club->isAvailable())
                    <b class="red-text">เต็ม (Full)</b>
                @else
                    <b class="amber-text">เกือบเต็ม (Almost Full)</b>
                @endif
            @else
                <b class="red-text">รับนักเรียนเต็มอัตราส่วนระดับชั้นแล้ว</b>
            @endif
        </p>
        @if ($club->isAvailableForLevel($student->level))

            @if ($club->is_audition)
                <form method="POST" action="/club-register/audition">
                    {{ csrf_field() }}
                    <button class="btn waves-effect waves-light fullwidth cyan" type="submit" name="club" value="{{ $club->id }}">
                        สมัครคัดเลือก (ออดิชั่น)
                    </button>
                </form>
            @else
                <form method="POST" action="/club-register/apply" onsubmit="return confirm('แน่ใจหรือไม่ที่จะลงทะเบียน{{ $club->name }}? เมื่อเลือกแล้วไม่สามารถเปลี่ยนได้')">
                    {{ csrf_field() }}
                    <button class="btn waves-effect waves-light fullwidth amber" type="submit" name="club" value="{{ $club->id }}">
                        ลงทะเบียนเข้าชมรม
                    </button>
                </form>
            @endif
        @endif
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('select').material_select();
        });
    </script>
@endsection