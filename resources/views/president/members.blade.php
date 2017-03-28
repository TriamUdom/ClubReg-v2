@extends('layouts.master')

@section('style')
    <style>
        td {
            padding: 10px 5px;
        }
    </style>
@endsection

@section('main')
    <h3 class="center-align">รายชื่อสมาชิก</h3>
    <table class="highlight">
        <thead>
        <tr>
            <th>รหัสนักเรียน</th>
            <th>ชื่อ</th>
            <th>ชั้น</th>
            <th>ห้อง</th>
        </tr>
        </thead>
        <tbody>
        @foreach(($club = \App\Club::currentPresident())->members as $member)
            <tr>
                <td>{{ $member->student_id ?? '-' }}</td>
                <td>{{ $member->getName() }}</td>
                <td>{{ $member->level }}</td>
                <td>{{ $member->room }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection