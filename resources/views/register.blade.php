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
                นักเรียนจะต้องกรอกข้อมูลด้วยความระมัดระวัง หากนักเรียนกรอกข้อมูลผิดพลาด กรุณาติดต่องานกิจกรรมพัฒนาผู้เรียนทางเพจ TUCMC
            </div>

            <div class="row">
                <div class="input-field col s6">
                    <input id="firstname" name="firstname" class="validate " required="" type="text">
                    <label for="firstname">ชื่อ (ไม่ต้องกรอกคำนำหน้า)</label>
                </div>

                <div class="input-field col s6">
                    <input id="lastname" name="lastname" class="validate " required="" type="text">
                    <label for="lastname">นามสกุล</label>
                </div>
            </div>

            <p class="red-text">
                หากนักเรียนมีชื่อกลาง ให้นักเรียนกรอกในช่อง ชื่อ
            </p>

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

            <div  class="row">
                <div class="input-field col s12">
                    <input id="id" name="id" class="validate " required="" type="number">
                    <label for="id">เลขประจำตัวนักเรียน</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input id="password" name="password" class="validate " required="" type="password">
                    <label for="password">รหัสผ่านที่จะตั้ง</label>
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
            $('select').material_select();
        });
    </script>
@endsection
