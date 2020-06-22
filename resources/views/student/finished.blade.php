@extends('layouts.master')

@section('style')
    <style>
        h4 {
            text-align: center
        }

        .teal {
            box-shadow: none;
            background-color: transparent !important;
        }

        body {
            background-image: url(https://farm5.staticflickr.com/4891/39745614053_43855205bc_o_d.jpg);
            background-size: cover;
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
            $student = \App\User::current();
            $clubId = $student->club_id;
        @endphp
        <p class="center-align"><i class="material-icons large green-text">check_circle</i></p>
        <p class="center-align">{{ $user->getName() }} ห้อง {{ $user->room }}<br />ได้ลงทะเบียนเรียนกิจกรรมชมรมในปีการศึกษา {{ config('core.current_year') }} แล้ว คือ</p>
        <h4>{{ $user->club->name }} ({{ $user->club_id }})</h4>
        <p class="center-align">กรุณาถ่ายภาพหน้าจอเพื่อเก็บไว้เป็นหลักฐาน</p>
    <!-- @if ($student->confirmed == true)
            <p class="center green-text">นักเรียนได้ทำการยืนยันข้อมูลเรียบร้อยแล้ว​</p>
        @else
            <div>
                <form method="POST" action="/confirm">
                    {{ csrf_field() }}
                    <div class="center">
                        <button class="btn waves-effect waves-light green" type="submit" name="action" value="confirm" onClick="return confirm('ยืนยันว่าข้อมูลนักเรียนถูกต้องแล้ว เมื่อทำการยืนยันจะไม่สามารถแก้ไขได้อีก')">
                            ข้อมูลถูกต้อง
                        </button>
                        <button class="btn waves-effect waves-light amber" type="submit" name="action" value="invalid">
                            ข้อมูลไม่ถูกต้อง
                        </button>
                    </div>

                </form>
            </div>
        @endif -->
        <p class="center-align">ขอให้นักเรียนมีความสุขในการเข้าร่วมกิจกรรมชมรม</p>
    </div>

    <br/>
    <div class="center-align grey-text text-lighten-3" style="font-size: 0.9rem">
        หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี<br />
        ภาพการปล่อยจรวด โดย <a href="https://flic.kr/p/23ybEkP">SpaceX</a>
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
