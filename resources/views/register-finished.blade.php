@extends('layouts.master')

@section('style')
    <style>
        h4 {
            text-align: center
        }

        body {
            background-color: #009688
        }

        #thebox {
            background: rgba(250,250,250,0.95);
        }
    </style>
@endsection

@section('nav')
    @parent
    <div style="height:15px"></div>
@endsection

@section('main')
    <div class="z-depth-1 card-panel" style="max-width:900px;margin:auto" id="thebox">
        @php
            /** @var $user \App\User */
        @endphp
        <p class="center-align"><i class="material-icons large green-text">check_circle</i></p>
        <p class="center-align">
            {{ $user->getName() }} ห้อง {{ $user->room }} เลขที่ {{ $user->number }}<br />
            ได้ยืนยันตัวตนในระบบทะเบียนชมรมแล้ว
        </p>

        <p class="center-align">กรุณาถ่ายภาพหน้าจอเพื่อเก็บไว้เป็นหลักฐาน</p>
    </div>

    <br/>
    <div class="center-align grey-text text-lighten-3" style="font-size: 0.9rem">
        หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
