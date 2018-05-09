@extends('layouts.master')

@section('style')
    <style>
        h4 {
            text-align: center
        }

        h1 {
            text-align: center;
            font-size: 4rem;
        }

        body {
            background-color: #009688
        }

        input[type='number'] {
            -moz-appearance: textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .countdownText {
            font-size: 2rem;
        }
    </style>
@endsection

@section('nav')
    @parent
    <div class="white-text teal" style="height:20px"></div>
@endsection

@section('main')
    <div class="z-depth-1 card-panel grey lighten-5" style="max-width:700px;margin:auto">
        @php
            $club = \App\Club::currentPresident();
        @endphp
        <h4 class="center-align">แผงควบคุมของประธานชมรม</h4>
        <div class="row" style="margin-bottom: 0.8rem;">
            <div class="col s1 center-align">
                <i class="material-icons small">local_activity</i>
            </div>
            <div class="col s11">
                <span style="font-size: 1.5rem">{{ $club->name }} ({{ $club->english_name }})</span><br/>
                <span style="font-size: 1rem">
                    ประธานครูที่ปรึกษา: {{ $club->getAdviserName() }} | ประธานชมรม: {{ $club->getPresidentName() }}
                    <br />
                    ประเภทการรับสมัคร:
                    @if (!$club->is_active)
                        <span class="red-text">ไม่เปิดรับ</span>
                    @elseif ($club->is_audition)
                        <span class="cyan-text">คัดเลือก</span>
                    @else
                        <span class="light-green-text">ไม่คัดเลือก</span>
                    @endif
                    @if (!\App\Helper::isRound(\App\Helper::Round_Closed) AND (!\App\Helper::isRound(\App\Helper::Round_War) OR \App\Helper::isRound(\App\Helper::Round_Audition)) AND $club->is_audition)
                    <br />สถานที่คัดเลือก: {{ $club->audition_location ?? '???' }}
                    @endif
                    <br />สถานที่ทำการเรียนการสอน: {{ $club->location ?? '???' }} (<a href="/president/settings">ดูเพิ่มเติม/แก้ไข</a>)
                </span>
            </div>
        </div>
        <div class="divider"></div>

        <div class="sector">
            <h5>สมาชิกชมรม</h5>
            <p>
                จำนวนสมาชิก {{ $club->countMember() }} คน | จำนวนสูงสุดที่รับได้ {{ $club->max_member }} คน<br />
                ม.4: {{ $club->members()->where('level', 4)->count() }} คน, ม.5: {{ $club->members()->where('level', 5)->count() }} คน, ม.6: {{ $club->members()->where('level', 6)->count() }} คน
            </p>
            @if ($club->countMember() > 0)
                <div class="row" style="margin-bottom:0">
                    <div class="col s12 m7">
                        <a class="btn waves-effect fullwidth cyan" href="/president/members">ดูรายชื่อสมาชิก</a>
                    </div>
                    <div class="col s12 m5">
                        <a class="btn waves-effect fullwidth amber" href="/president/fm3304">ดาวน์โหลด FM33-04</a>
                    </div>
                </div>
            @endif
        </div>

        @if ($club->is_audition AND \App\Helper::isRound(\App\Helper::Round_Audition))
            <div class="sector">
                <h5>คำขอคัดเลือก</h5>
                มีคนรอการตอบรับ {{ $auditionCount = $club->auditions()->where('status', \App\Audition::Status_Awaiting)->count() }} คน
                @if ($auditionCount)
                    <a class="btn waves-effect fullwidth blue" href="/president/audition" style="margin-top: 8px;">ดูรายชื่อ</a>
                @endif
            </div>
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
