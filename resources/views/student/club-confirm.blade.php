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
        <a href="/" class="blue-text"><i class="small material-icons" style="vertical-align: middle">arrow_back</i> เมนู</a>
        <h4 class="center-align">{{ $club->name }}</h4>
        <h5 class="center-align">{{ $club->english_name }}</h5>
        <p>&ensp;{!! nl2br($club->description) !!}</p>
        <p>สถานะ:
            @if ($club->isAvailableForLevel($student->level))
                @if ($club->isAvailable(true) == 2)
                    <b class="green-text">มีที่นั่งว่าง (Available)</b> <p>มีที่นั่งว่าง : {{ $club->seatsAvailable($student->level) }} คน</p>
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
                <p>สถานที่คัดเลือก: {{ $club->audition_location ?? '???' }}</p>
                <p>เวลาคัดเลือก: {{ $club->audition_time ?? '???' }}</p>
                <p>รายละเอียดการคัดเลือก: {{ $club->audition_instruction ?? '???' }}</p>
                @if ($student->auditions()->where('club_id', $club->id)->count() <= 0)
                <form method="POST" action="/club-register/audition">
                    {{ csrf_field() }}
                    <button class="btn waves-effect waves-light fullwidth cyan" type="submit" name="club" value="{{ $club->id }}">
                         ยืนยันการสมัครคัดเลือก (ออดิชั่น)
                    </button>
                </form>
                    @else
                    <p class="red-text center-align">สมัครคัดเลือกไปแล้ว</p>
                    @endif
            @else
                <form method="POST" action="/club-register/apply" onsubmit="return confirm('ยืนยันการลงทะเบียนชมรม {{ $club->name }}? เมื่อนักเรียนลงทะเบียนชมรมไปแล้ว จะไม่สามารถเข้ารับการออดิชั่นหรือแก้ไขการลงทะเบียนชมรมได้อีก')">
                    {{ csrf_field() }}
                    <button class="btn waves-effect waves-light fullwidth amber" type="submit" name="club" value="{{ $club->id }}">
                        ยืนยันลงทะเบียนเข้าเรียนชมรม
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
