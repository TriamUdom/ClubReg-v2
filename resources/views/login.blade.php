@extends('layouts.master')

@section('style')
    <style>
    h4 {text-align:center}
    h1 {text-align: center;font-size:4rem;}
    body {background-color: #009688}
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
        <form class="login-form" method="POST" action="/login">
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">ลงทะเบียนเข้าร่วมชมรม</h4>
                </div>
            </div>
            <ul class="collection">
                <li class="collection-item amber lighten-3">เข้าสู่ระบบเพื่อดำเนินการต่อ</li>
            </ul>
            @if (count($errors) > 0)
                <ul class="collection white-text">
                    <li class="collection-item red darken-1">เกิดข้อผิดพลาดในข้อมูล
                        ({{ implode(', ', $errors->all()) }})
                    </li>
                </ul>
            @endif
            <ul class="collection white-text"
                style="margin:0;{{ session()->has('error_message') ? '' : 'display:none' }}" id="error-message">
                <li class="collection-item red darken-1">{{ session('error_message') }}</li>
            </ul>
            <div class="row margin">
                <div class="input-field col s12">
                    <input id="username" type="number" name="username" class="validate" required min="1100000000000" max="9000000000000" />
                    <label for="username">รหัสประจำตัวประชาชน</label>
                </div>
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                    <input id="password" type="password" name="password" class="validate" required/>
                    <label for="password">วัน/เดือน/ปีเกิด (DD/MM/YYYY)</label>
                </div>
            </div>
            @if (config('core.captcha_enable'))
                <div class="row">
                    <div class="col s12">
                        {!! Recaptcha::render([ 'lang' => App::getLocale() ]) !!}
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light red" type="submit" name="action" style="width:100%">
                        เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
