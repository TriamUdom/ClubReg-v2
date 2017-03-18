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
@endsection
