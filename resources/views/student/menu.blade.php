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
    <div class="z-depth-1 card-panel grey lighten-5" style="max-width:900px;margin:auto">
        @php
            /** @var $user \App\User */
        @endphp
        <h4 class="center-align">การลงทะเบียนชมรม</h4>
        <div class="row" style="margin-bottom: 0.8rem;">
            <div class="col s1 center-align">
                <i class="material-icons small">person</i>
            </div>
            <div class="col s11">
                {{ $user->getName() }} ห้อง {{ $user->room }}
                @if (!$user->hasClub())
                    <p style="margin:0;font-size: 0.9rem">ยังไม่ได้ลงทะเบียนเรียนกิจกรรมชมรม</p>
                @endif
            </div>
        </div>
        <div class="divider"></div>
        @if ($user->hasClub())
            <p class="center-align"><i class="material-icons large green-text">check_circle</i></p>
            <p class="center-align">นักเรียนลงทะเบียนเรียนกิจกรรมชมรมในปีการศึกษา {{ config('core.current_year') }} แล้ว คือ</p>
            <h4>{{ $user->club->name }} ({{ $user->club_id }})</h4>
            <p class="center-align">หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี</p>
        @elseif (\App\Helper::isRound(\App\Helper::Round_Closed))
            <h5 class="center-align" style="margin-top:2rem">ยังไม่ถึงเวลาลงทะเบียนชมรม</h5>
            <!-- <p class="center-align">โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี</p> -->
        @elseif (\App\Helper::isRound(\App\Helper::Round_Waiting))
            <h5 class="center-align" style="margin-top:2rem">ไม่อนุญาตให้นักเรียนลงทะเบียนในขณะนี้</h5>
            @if (\App\Helper::shouldCountdown())
                <p class="center-align">โปรดรอประมาณ</p>
                <div class="row">
                    <div class="col s3 center">
                        <h4 class="center countdownText" id="tDay">--</h4>วัน
                    </div>
                    <div class="col s3 center">
                        <h4 class="center countdownText" id="tHour">--</h4>ชั่วโมง
                    </div>
                    <div class="col s3 center">
                        <h4 class="center countdownText" id="tMinute">--</h4>นาที
                    </div>
                    <div class="col s3 center">
                        <h4 class="center countdownText" id="tSecond">--</h4>วินาที
                    </div>
                </div>
                <script>
                    var cTime = {{ \App\Setting::getValue('allow_register_time') - time() }};
                    var lastUpdated = Date.now();
                    function showTime() {
                        //Convert seconds to human-friendly time

                        if (Date.now() - lastUpdated > 10000 || cTime <= 1) {
                            // setInterval skipped or time out, refresh
                            location.reload();
                        }

                        var data = cTime;

                        if (data <= 600) {
                            $('#authcard').slideDown();
                            $('#authbtn').addClass('disabled').text('ยังไม่ถึงเวลาประกาศผล');
                        }

                        var day = 0;
                        var hour = 0;
                        var minute = 0;

                        while (data >= 86400) {
                            day++;
                            data -= 86400;
                        }
                        while (data >= 3600) {
                            hour++;
                            data -= 3600;
                        }
                        while (data >= 60) {
                            minute++;
                            data -= 60;
                        }

                        $('#tDay').text(day);
                        $('#tHour').text(hour);
                        $('#tMinute').text(minute);
                        $('#tSecond').text(data);

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
            @else
                <p class="center-align">กรุณาลองใหม่ภายหลัง</p>
            @endif
        @else
            @if (\App\Helper::isRound(\App\Helper::Round_Confirm) AND $user->getPreviousClub())
                <div class="sector">
                    <h5>ลงทะเบียนเข้าชมรมเดิม</h5>
                    <p>ปีการศึกษาที่ผ่านมา นักเรียนอยู่ชมรม <b>{{ ($oldClub = $user->getPreviousClub(true))->name }} ({{ $oldClub->id }})</b></p>
                    @if ($oldClub->isAvailableForConfirm())
                        <p>นักเรียนมีสิทธิเข้าชมรมเดิมได้ทันที โดยไม่ต้องคัดเลือกหรือลงทะเบียนร่วมกับนักเรียนใหม่หรือนักเรียนชมรมอื่น</p>
                        <p class="red-text">เมื่อใช้สิทธิ์ในการลงทะเบียนไปแล้ว จะไม่สามารถเข้าชมรมอื่นได้</p>
                        <form method="POST" action="/club-register/old" onsubmit="return confirm('แน่ใจหรือไม่ที่จะลงทะเบียนชมรมเดิม? เมื่อเลือกแล้วไม่สามารถเปลี่ยนได้')">
                            {{ csrf_field() }}
                            <button class="btn waves-effect waves-light fullwidth blue" type="submit" name="club" value="{{ $oldClub->id }}">
                                ใช้สิทธิเข้าชมรมเดิม
                                <i class="material-icons left">check</i>
                            </button>
                        </form>
                    @else
                        <p class="red-text"><span style="font-size: 1.3rem">ชมรมรับนักเรียนเดิมเต็มอัตราส่วนแล้ว</span> (นักเรียนสามารถลงทะเบียนใหม่เสมือนไม่ได้อยู่ชมรมนี้อยู่ก่อน)</p>
                    @endif
                </div>
                <div class="sector">
                    <div class="sector grey lighten-4 red-text" style="font-size: 1.5rem;line-height: 1.8rem">
                        นักเรียนจะต้องดำเนินการด้วยความระมัดระวัง หากนักเรียนลงทะเบียนเรียนชมรมที่ไม่มีการคัดเลือกไปแล้ว จะไม่สามารถแก้ไขหรือยกเลิกได้ และจะไม่สามารถกดตอบรับการคัดเลือกได้อีก
                    </div>
                    <form method="POST" action="/club-register" class="select-append">
                        {{ csrf_field() }}
                        <h5>สมัครคัดเลือกเข้าชมรม</h5>
                        <div class="row" style="margin-bottom:0">
                            <div class="input-field col s12">
                                <select name="club" required>
                                    <option value="" disabled selected>เลือกชมรมที่ต้องการ</option>
                                    {!! \App\Helper::createOption(\App\Club::fetchAllClubs()) !!}
                                </select>
                                <label>ชมรมที่ต้องการสมัคร</label>
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light purple fullwidth" type="submit">
                            ต่อไป
                        </button>
                    </form>

                    <br/>
                    <p><span style="font-size:1.3rem">ชมรมที่ได้ลงทะเบียนเพื่อเข้าคัดเลือก (ออดิชั่น)</span>
                    <ul class="collection">
                        @if(empty($user->auditions))
                            ไม่พบชมรมที่ได้ลงทะเบียนเพื่อเข้าคัดเลือก (ออดิชั่น)
                        @else
                            @foreach($user->auditions as $audition)
                                <li class="collection-item">
                                    <b class="title">{{ $audition->club->name }}</b>
                                    <a href="/club-register/{{ $audition->club->id }}">ดูข้อมูล</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            @elseif (\App\Helper::isRound(\App\Helper::Round_Register))
                <div class="sector">
                    <div class="sector grey lighten-4 red-text" style="font-size: 1.5rem;line-height: 1.8rem">
                        นักเรียนจะต้องดำเนินการด้วยความระมัดระวัง หากนักเรียนลงทะเบียนเรียนชมรมที่ไม่มีการคัดเลือกไปแล้ว จะไม่สามารถแก้ไขหรือยกเลิกได้ และจะไม่สามารถกดตอบรับการคัดเลือกได้อีก
                    </div>
                    <form method="POST" action="/club-register" class="select-append">
                        {{ csrf_field() }}
                        <h5>สมัครคัดเลือกเข้าชมรม</h5>
                        <div class="row" style="margin-bottom:0">
                            <div class="input-field col s12">
                                <select name="club" required>
                                    <option value="" disabled selected>เลือกชมรมที่ต้องการ</option>
                                    {!! \App\Helper::createOption(\App\Club::fetchAllClubs()) !!}
                                </select>
                                <label>ชมรมที่ต้องการสมัคร</label>
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light purple fullwidth" type="submit">
                            ต่อไป
                        </button>
                    </form>

                    <br/>
                    <p><span style="font-size:1.3rem">ชมรมที่ได้ลงทะเบียนเพื่อเข้าคัดเลือก (ออดิชั่น)</span>
                    <ul class="collection">
                        @if(empty($user->auditions))
                            ไม่พบชมรมที่ได้ลงทะเบียนเพื่อเข้าคัดเลือก (ออดิชั่น)
                        @else
                            @foreach($user->auditions as $audition)
                                <li class="collection-item">
                                    <b class="title">{{ $audition->club->name }}</b>
                                    <a href="/club-register/{{ $audition->club->id }}">ดูข้อมูล</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            @elseif (\App\Helper::isRound(\App\Helper::Round_Audition))
                <div class="sector">
                    @if($user->auditions->count() != 0)
                        <h5 class="center">รอบการคัดเลือก (ออดิชั่น)</h5>
                        <p><span style="font-size:1.3rem">สถานะการคัดเลือก</span> <span style="font-size:1rem">(ให้นักเรียนไปรับการคัดเลือกตามชมรมที่นักเรียนสมัคร หากชมรมรับนักเรียน นักเรียนจะต้องกดยืนยันหรือปฏิเสธภายในเวลาที่กำหนด มิฉะนั้นระบบจะตัดสิทธิ์นั้นและนักเรียนจะต้องลงทะเบียนในรอบ 2)</span>
                        </p>
                        <ul class="collection">
                            @foreach($user->auditions as $audition)
                                <li class="collection-item">
                                    <b class="title">{{ $audition->club->name }}</b> {{ $audition->getStatus() }} @if($audition->status == \App\Audition::Status_Awaiting)<a href="/club-register/{{ $audition->club->id }}">ดูข้อมูล</a>@endif
                                    @if ($audition->status == \App\Audition::Status_Passed)
                                        @if ($audition->club->isAvailableForLevel($user->level))
                                            <form method="POST" action="/club-register/confirm-audition">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="audition" value="{{ $audition->id }}"/>
                                                <button class="btn waves-effect waves-light green" type="submit" name="action" value="join" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะเข้า{{ $audition->club->name }}? เมื่อเลือกแล้วจะไม่สามารถเปลี่ยนเป็นชมรมอื่นได้อีก')">
                                                    ยืนยันลงทะเบียนเข้าชมรม
                                                </button>
                                                <button class="btn waves-effect waves-light red" type="submit" name="action" value="reject" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะปฏิเสธ{{ $audition->club->name }}? เมื่อปฏิเสธแล้วจะไม่สามารถเลือกชมรมนี้ได้อีก')">
                                                    สละสิทธิ์
                                                </button>
                                            </form>
                                        @else
                                            <b class="red-text">ชมรมเต็มแล้ว ไม่สามารถตอบรับเข้าชมรมได้</b>
                                        @endif
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <h5 class="center-align" style="margin-top:2rem">ไม่พบประวัติการออดิชั่น</h5>
                        <p class="center-align">หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี</p>
                    @endif
                </div>
            @elseif (\App\Helper::isRound(\App\Helper::Round_Glean) AND count($user->getAuditions()) > 0)
                <div class="sector">
                    <form method="POST" action="/club-register" class="select-append">
                        {{ csrf_field() }}
                        <h5>ลงทะเบียนเข้าชมรม สำหรับผู้ไม่ผ่านออดิชั่นหรือผู้ที่สละสิทธิ์ทุกชมรมที่ออดิชั่นผ่าน</h5>
                        <p class="red-text">เมื่อนักเรียนลงทะเบียนชมรมไปแล้ว จะไม่สามารถแก้ไขการลงทะเบียนชมรมได้อีก</p>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="club" required>
                                    <option value="" disabled selected>เลือกชมรมที่ต้องการ</option>
                                    {!! \App\Helper::createOption(\App\Club::fetchWarClubs()) !!}
                                </select>
                                <label>ชมรมที่ต้องการลงทะเบียน</label>
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light fullwidth indigo" type="submit">
                            ต่อไป
                        </button>
                    </form>
                </div>
            @elseif (\App\Helper::isRound(\App\Helper::Round_Glean))
                    <h5 class="center-align" style="margin-top:2rem">นักเรียนไม่สามารถลงทะเบียนในรอบที่ 2 ได้เนื่องจากไม่เคยมีประวัติการไปออดิชั่น</h5>
                    <p class="center-align">หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี</p>
            @elseif (!\App\Helper::isRound(\App\Helper::Round_Confirm) AND !\App\Helper::isRound(\App\Helper::Round_Register))
                    <h5 class="center-align" style="margin-top:2rem">นักเรียนไม่สามารถลงทะเบียนในช่วงเวลานี้ได้</h5>
                    <p class="center-align">หากนักเรียนประสบปัญหาหรือมีข้อสงสัย โปรดติดต่องานกิจกรรมพัฒนาผู้เรียน ตึก 50 ปี</p>
            @endif
        @endif
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('select').material_select();
            $('form.select-append').submit(function () {
                window.location.href = $(this).prop('action') + '/' + $(this).find('select').val();
                return false;
            });
        });
    </script>
@endsection
