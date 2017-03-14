@extends('layouts.master')

@section('style')
    <style>
        td {padding: 10px 5px;}
    </style>
@endsection

@section('main')
    <h4 class="center-align">รายชื่อชมรมทั้งหมด</h4>
    <table class="highlight">
        <thead>
        <tr>
            <th>รหัสวิชา</th>
            <th>ชื่อ</th>
            <th>ประเภท</th>
            <th>จำนวนคน</th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Club::all() as $club)
            <tr>
                <td>{{ $club->id }}</td>
                <td title="{{ $club->english_name }}">{{ $club->name }}</td>
                <td>
                    @if (!$club->is_active)
                        <span class="red-text">ไม่เปิดรับ</span>
                    @elseif ($club->is_audition)
                        <span class="cyan-text">คัดเลือก</span>
                    @else
                        <span class="light-green-text">ไม่คัดเลือก</span>
                    @endif
                </td>
                <td>
                    <b class="@if ($club->isAvailable(true) == 2) green-text @elseif(empty($club->isAvailable(true))) red-text @else amber-text @endif">
                        {{ $club->countMember() }}
                    </b>
                    / {{ $club->max_member }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection