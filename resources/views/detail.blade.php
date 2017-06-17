@extends('layouts.master')

@section('style')
    <style>
        h4 {
            margin-top: 2rem;
            font-size: 2rem
        }
    </style>
@endsection

@section('main')
    <h3 class="center-align">รายละเอียดชมรม</h3>
    @foreach(\App\Club::orderBy('is_audition', 'DESC')->orderBy('name')->get() as $club)
        <h4>{{ $club->name }}</h4>
        <p>&emsp;{!! nl2br($club->description) !!}</p>
        <p><b>ประเภท</b>: @if (!$club->is_active)
                <span class="red-text">ไม่เปิดรับ</span>
            @elseif ($club->is_audition)
                <span class="cyan-text">คัดเลือก</span>
            @else
                <span class="light-green-text">ไม่คัดเลือก</span>
            @endif</p>
        @unless(empty($club->audition_location))
            <p><b>สถานที่คัดเลือก</b>: {{ $club->audition_location }}</p>
        @endunless
        <p><b>รับสมาชิกไม่เกิน</b>: {{ $club->max_member }} คน</p>
        <div class="divider"></div>
    @endforeach
@endsection