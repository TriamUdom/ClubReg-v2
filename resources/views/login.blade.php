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
        @if (count($errors) > 0)
            <div class="sector red white-text">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <form class="login-form" method="POST" action="/login">

            {{ csrf_field() }}

            <input name="mac" id="iMac" value="" type="hidden">
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">เข้าสู่ระบบ</h4>
                </div>
            </div>

            <div class="sector grey lighten-4 orange-text" style="font-size: 1.25rem;line-height: 1.8rem">
                ให้นักเรียนชั้น ม.4 ใช้เลขประจำตัวนักเรียนชนิดชั่วคราว เช่น<br/>
                นักเรียน ห้อง 77 เลขที่ 35 มีเลขประจำตัว 07735<br/>
                นักเรียน ห้อง 111 เลขที่ 9 มีเลขประจำตัว 11109<br/>
            </div>


            <div class="row">
                <div class="input-field col s12">
                    <input id="username" name="student_id" class="validate " required="" type="text">
                    <label for="username">เลขประจำตัวนักเรียน</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="password" name="password" class="validate " required="" type="password">
                    <label for="password">รหัสผ่าน</label>
                </div>
            </div>
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

@section('script')
    @parent
    <script>
        $(function () {
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