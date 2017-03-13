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
    <div class="z-depth-1 card-panel" style="max-width:700px;margin:auto">
        @php
            $user = \App\User::current();
        @endphp
        <h4 class="center-align">การลงทะเบียนชมรม</h4>
        <div class="row">
            <div class="col s1 center-align">
                <i class="material-icons small">person</i>
            </div>
            <div class="col s11">
                {{ $user->getName() }} ห้อง {{ $user->room }}
            </div>
        </div>
        <div class="divider"></div>
        @if ($user->hasClub())
            <p class="center-align"><i class="material-icons large green-text">check_circle</i></p>
            <p class="center-align">นักเรียนลงทะเบียนเรียนกิจกรรมชมรมในปีการศึกษา {{ config('core.current_year') }} แล้ว คือ</p>
            <h4>{{ $user->club->name }} ({{ $user->club_id }})</h4>
            <p class="center-align">หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี</p>
        @elseif (\App\Helper::isRound(\App\Helper::Round_Waiting))
            <h5 class="center-align">ไม่อนุญาตให้นักเรียนลงทะเบียนในขณะนี้</h5>
            <p class="center-align">โปรดรอประมาณ</p>
            @if (\App\Helper::shouldCountdown())
                <div class="row">
                    <div class="col s4 center">
                        <h4 class="center countdownText" id="tHour">--</h4>ชั่วโมง
                    </div>
                    <div class="col s4 center">
                        <h4 class="center countdownText" id="tMinute">--</h4>นาที
                    </div>
                    <div class="col s4 center">
                        <h4 class="center countdownText" id="tSecond">--</h4>วินาที
                    </div>
                </div>
                <script>
                    var cTime = {{ config('core.allow_register_time')-time() }};
                    var lastUpdated = Date.now();
                    function showTime() {
                        //Convert seconds to human-friendly time

                        if (Date.now() - lastUpdated > 10000 || cTime <= 1) {
                            // setInterval skipped or time out, refresh
                            location.reload();
                        }

                        var data = cTime;

                        if (data <= 0) {
                            $('#countcard').slideUp();
                            $('#authbtn').removeClass('disabled').text('ค้นหา');
                            if (data == 0) {
                                $('#authcard').slideDown();
                                $("#qform").submit();
                            }
                        } else {
                            if (data <= 600) {
                                $('#authcard').slideDown();
                                $('#authbtn').addClass('disabled').text('ยังไม่ถึงเวลาประกาศผล');
                            }

                            var hour = 0;
                            var minute = 0;

                            while (data >= 3600) {
                                hour++;
                                data -= 3600;
                            }
                            while (data >= 60) {
                                minute++;
                                data -= 60;
                            }

                            $('#tHour').text(hour);
                            $('#tMinute').text(minute);
                            $('#tSecond').text(data);

                        }
                        cTime--;
                        lastUpdated = Date.now();
                    }
                    setTimeout(function () {
                        $(function () {
                            showTime();
                            setInterval('showTime()', 1000);
                        });
                    }, 500);
                </script>
            @endif
        @else
            <p>นักเรียนยังไม่ได้ลงทะเบียนเรียนกิจกรรมชมรม</p>
            @if (\App\Helper::isRound(\App\Helper::Round_Confirm) AND $user->getPreviousClub())
                <div class="sector">
                    <form method="POST" action="/club-register/old" onsubmit="return confirm('แน่ใจหรือไม่ที่จะลงทะเบียนชมรมเดิม')">
                        {{ csrf_field() }}
                        <h5>ลงทะเบียนเข้าชมรมเดิม</h5>
                        <p>ปีการศึกษาที่ผ่านมา นักเรียนอยู่ชมรม <b>{{ ($oldClub = $user->getPreviousClub(true))->name }} ({{ $oldClub->id }})</b></p>
                        <button class="btn waves-effect waves-light fullwidth blue" type="submit" name="club" value="{{ $oldClub->id }}">
                            ใช้สิทธิเข้าชมรมเดิม
                            <i class="material-icons left">check</i>
                        </button>
                    </form>
                </div>
            @endif
            @if (\App\Helper::isRound(\App\Helper::Round_Audition))
                <div class="sector">
                    <form method="POST" action="/club-register/old">
                        {{ csrf_field() }}
                        <h5>สมัครคัดเลือกเข้าชมรม (ออดิชั่น)</h5>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="club">
                                    <option value="" disabled selected>เลือกชมรมที่ต้องการ</option>
                                    {!! \App\Helper::createOption(\App\Club::fetchAuditionClubs()) !!}
                                </select>
                                <label>ชมรมที่ต้องการสมัคร</label>
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light fullwidth purple" type="submit" name="club" value="{{ $oldClub->id }}">
                            ดูข้อมูลเพิ่มเติม
                            <i class="material-icons left">info_outline</i>
                        </button>
                    </form>
                </div>
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
