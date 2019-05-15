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

        <form class="login-form" method="POST" action="/register">

            {{ csrf_field() }}

            <input name="mac" id="iMac" value="" type="hidden">
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">ยืนยันตัวตนเพื่อตั้งรหัสผ่าน</h4>
                </div>
            </div>

            <div class="sector grey lighten-4 red-text">
                นักเรียนจะต้องกรอกข้อมูลด้วยความระมัดระวัง หากนักเรียนกรอกข้อมูลผิดพลาด กรุณาติดต่องานกิจกรรมพัฒนาผู้เรียน ณ ตึก 50 ปี
            </div>

            <div class="row">
                <div class="input-field col s6">
                    <input id="firstname" name="firstname" class="validate " required="" type="text">
                    <label for="firstname">ชื่อ</label>
                </div>

                <div class="input-field col s6">
                    <input id="lastname" name="lastname" class="validate " required="" type="text">
                    <label for="lastname">นามสกุล</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <select autocomplete="off" id="level" name="level">
                        <option value="" disabled selected>กรุณาเลือกระดับชั้น</option>
                        <option value="4">ม.4</option>
                        <option value="5">ม.5</option>
                        <option value="6">ม.6</option>
                    </select>
                    <label>ระดับชั้น</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s6">
                    <input id="room" name="room" class="validate " required="" type="number">
                    <label for="room">ห้องเรียน</label>
                </div>

                <div class="input-field col s6">
                    <input id="number" name="number" class="validate " required="" type="number">
                    <label for="number">เลขที่</label>
                </div>
            </div>

            <div id="m5-6" class="row" style="display:none;">
                <div class="input-field col s12">
                    <input disabled id="id" name="id" class="validate " required="" type="number">
                    <label for="id">เลขประจำตัวนักเรียน</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input id="password" name="password" class="validate " required="" type="password">
                    <label for="password">รหัสผ่านที่จะตั้ง (ความยาว 6 หลัก)</label>
                </div>

                <div class="input-field col s12">
                    <input id="password_val" name="password_val" class="validate " required="" type="password">
                    <label for="password_val">ยืนยันรหัสผ่าน</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light green" type="submit" name="action" style="width:100%">
                        ยืนยันตัวตน
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

            $('select').material_select();

            $("#level").on('change', function() {
                var level = $("#level").val();

                if (level == 4){
                    $("#id").prop("disabled", true);

                    $("#m5-6").hide();
                }
                else if (level == 5 || level == 6){
                    $("#id").prop("disabled", false);

                    $("#m5-6").show();
                }
            });
        });
    </script>
@endsection