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
    </style>
@endsection

@section('nav')
    @parent
    <div class="white-text teal" style="height:20px"></div>
@endsection

@section('main')
    <div class="z-depth-1 card-panel" style="max-width:550px;margin:auto">
        <form class="login-form" method="POST" action="/login/student">
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">ลงทะเบียนเข้าร่วมชมรม</h4>
                </div>
            </div>
            @if (count($errors) > 0)
                <div class="sector red white-text">
                    {{ implode(', ', $errors->all()) }}
                </div>
            @else
                <div class="sector grey lighten-4 red-text" style="font-size: 1.5rem;line-height: 1.8rem">
                    นักเรียนจะต้องดำเนินการด้วยความระมัดระวัง หากลงทะเบียนแล้วไม่สามารถยกเลิกได้
                </div>
            @endif
            <ul class="collection white-text"
                style="margin:0;{{ session()->has('error_message') ? '' : 'display:none' }}" id="error-message">
                <li class="collection-item red darken-1">{{ session('error_message') }}</li>
            </ul>
            <div class="row">
                <div class="input-field col s12">
                    <input id="username" type="number" name="citizen_id" class="validate" required min="1100000000000" max="9000000000000"/>
                    <label for="username">รหัสประจำตัวประชาชน</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="password" type="number" name="student_id" class="validate" required min="11111" max="99999"/>
                    <label for="password">รหัสประจำตัวนักเรียน (ไม่มี ใส่ 11111)</label>
                </div>
            </div>
            @if (config('core.captcha_enable'))
                <div class="row">
                    <div class="col s12 center-align">
                        {!! Recaptcha::render([ 'lang' => 'th' ]) !!}
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light orange" type="submit" name="action" style="width:100%">
                        เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </form>
    </div>

    <br />
    <div class="center-align minibox white-text" id="mini-def" style="display: none;font-size: 0.9rem">ใช้งานได้ดีที่สุดบน Mozilla Firefox หรือ Google Chrome รุ่นล่าสุดบนอุปกรณ์ที่ไม่ใช่ iOS</div>
    <div class="center-align minibox sector red darken-1 white-text" id="mini-al" style="font-size:1.3rem;line-height: 2rem;">
        <h4>ระบบอาจทำงานไม่ปกติ</h4>
        คุณกำลังใช้งานบนอุปกรณ์ที่ไม่เหมาะสม<br/>
        งานกิจกรรมฯจะไม่รับผิดชอบหากเกิดข้อผิดพลาด<br />
        ใช้งานได้ดีที่สุดบน Mozilla Firefox หรือ Google Chrome รุ่นล่าสุดบนอุปกรณ์ที่ไม่ใช่ iOS
    </div>
@endsection

@section('script')
    @parent
    <script>
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
    </script>
@endsection