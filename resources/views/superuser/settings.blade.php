@extends('layouts.master')

@section('main')
    <form action="/settings" method="POST">
        {{ csrf_field() }}
        <h4>แก้ไขการตั้งค่าของระบบทะเบียนชมรม</h4>
        @if (count($errors) > 0)
            <div class="sector red darken-2 white-text">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <div class="row">
            <div class="input-field col s12">
                <input id="maintenance" name="maintenance" type="checkbox" {{ \App\Setting::getValue('maintenance') ? 'checked="checked"' : ''}}/>
                <label for="maintenance">Maintenance Mode</label>
            </div>
        </div>

        <br/>

        <div class="row">
            <div class="input-field col s12">
                <input id="superuser_list" name="superuser_list" type="text" class="validate" value="{{ implode(', ', \App\Setting::getValue('superuser_list'))}}"/>
                <label for="superuser_list">Superusers (ใช้ , คั่น)</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <label>Registration Phase</label>
            </div>
            <div class="input-field col s12">
                <select class="browser-default" name="round">
                    <option value="WAITING" {{ \App\Helper::isRound(\App\Helper::Round_Waiting) ? 'selected' : '' }}>พัก/รอ</option>
                    <option value="CONFIRM" {{ \App\Helper::isRound(\App\Helper::Round_Confirm) ? 'selected' : '' }}>สมาชิกเก่ายืนยันสิทธิ์</option>
                    <option value="REGISTER" {{ \App\Helper::isRound(\App\Helper::Round_Register) ? 'selected' : '' }}>รอบ 1 (สมัครทุกชมรม)</option>
                    <option value="AUDITION" {{ \App\Helper::isRound(\App\Helper::Round_Audition) ? 'selected' : '' }}>รอบ 1.5 (ออดิชั่น/ยืนยัน)</option>
                    <option value="GLEAN" {{ \App\Helper::isRound(\App\Helper::Round_Glean) ? 'selected' : '' }}>รอบ 2 (เก็บตก)</option>
                    <option value="CLOSED" {{ \App\Helper::isRound(\App\Helper::Round_Closed) ? 'selected' : '' }}>ปิด</option>
                </select>
            </div>
        </div>

        <br/>

        <button type="submit" class="btn waves-effect blue fullwidth" style="margin-left:10px">บันทึก</button>
        <br/><br />
    </form>

@endsection

@section('script')
    @parent
    <script>
        $(function () {
            $('select').formSelect();
        });
    </script>
@endsection