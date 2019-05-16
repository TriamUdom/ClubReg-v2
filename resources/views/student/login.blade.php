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

        .g-recaptcha div {
            margin: auto;
        }
    </style>
@endsection

@section('nav')
    @parent
    <div class="white-text teal" style="height:20px"></div>
@endsection

@section('main')
    <div class="z-depth-1 card-panel" style="max-width:550px;margin:auto">
        <div class="row">
            <div class="input-field col s12 center">
                <h4 class="center login-form-text">ลงทะเบียนเข้าร่วมชมรม</h4>
            </div>
        </div>
        @if (count($errors) > 0)
            <div class="sector red white-text">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif
        @if (\App\Helper::isRound(\App\Helper::Round_Waiting, true))
            <div class="sector amber darken-3 white-text">
                ยังไม่เปิดให้ลงทะเบียน
                @if (\App\Helper::shouldCountdown())
                    โปรดรอประมาณ
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
                        var cTime = {{ \App\Setting::getValue('allow_register_time')-time() }};
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
                @endif
            </div>
        @else
            <div class="sector grey lighten-4 red-text" style="font-size: 1.5rem;line-height: 1.8rem">
                นักเรียนจะต้องดำเนินการด้วยความระมัดระวัง หากลงทะเบียนแล้วไม่สามารถยกเลิกได้
            </div>

            <p>นักเรียนจะต้องยืนยันตัวตนในระบบและตั้งรหัสผ่านก่อนที่จะสามารถเข้าสู่ระบบได้</p>
            <div class="row">
                <div class="col s12">
                    <a class="waves-effect waves-light btn-large green fullwidth" href="/register"><i class="material-icons left">fingerprint</i>ยืนยันตัวตนและตั้งรหัสผ่าน</a>
                </div>
            </div>

            <p>เมื่อได้ทำการยืนยันตัวตนและตั้งรหัสผ่านแล้ว สามารถเข้าสู่ระบบได้ตามปกติ</p>
            <div class="row">
                <div class="col s12">
                    <a class="waves-effect waves-light btn-large blue fullwidth" href="/login"><i class="material-icons left">lock</i>เข้าสู่ระบบ</a>
                </div>
            </div>
        @endif
        <p class="center-align">ดู<a href="/info">รายละเอียดการลงทะเบียน</a> | <a href="/contact">ติดต่อ</a></p>
    </div>

    <br/>
    <div class="center-align minibox white-text" id="mini-def" style="display: none;font-size: 0.9rem">ใช้งานได้ดีที่สุดบน Mozilla Firefox หรือ Google Chrome รุ่นล่าสุดบน Windows/Linux/macOS/Android</div>
    <div class="center-align minibox sector red darken-1 white-text" id="mini-al" style="font-size:1.3rem;line-height: 2rem;">
        <h4>ระบบอาจทำงานไม่ปกติ</h4>
        คุณกำลังใช้งานบนอุปกรณ์ที่ไม่เหมาะสม<br/>
        งานกิจกรรม ฯ จะไม่รับผิดชอบหากเกิดข้อผิดพลาด<br/>
        ใช้งานได้ดีที่สุดบน Mozilla Firefox หรือ Google Chrome รุ่นล่าสุด
        งานกิจกรรมฯจะไม่รับผิดชอบหากเกิดข้อผิดพลาด<br/>
        ใช้งานได้ดีที่สุดบน Mozilla Firefox หรือ Google Chrome รุ่นล่าสุดบนอุปกรณ์ที่ไม่ใช่ iOS
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(function () {
            if (!self.fetch) {
                // Check for old browser by checking Fetch API support, which is not present in old browsers.
                // Visit http://caniuse.com for more information
                $('#mini-def').hide();
                $('#mini-al').show();

                if (!(typeof Promise !== "undefined" && Promise.toString().indexOf("[native code]") !== -1)) {
                    // If Promise API not supported ::: super old browser
                    setTimeout(function () {
                        $('body').css('background-color', '#f44336');
                        $('.teal').removeClass('teal').addClass('red');
                    }, 1000);
                    $('.btn').removeClass('orange').addClass('grey');
                }
            } else {
                $('#mini-def').show();
                $('#mini-al').hide();
            }

            $('.login-form').submit(function (event) {
                if (grecaptcha.getResponse().length == 0) {
                    // ReCAPTCHA validation failed
                    Materialize.toast("กรุณากด \"ฉันไม่ใช่โปรแกรมอัตโนมัติ\"", 4000);
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection