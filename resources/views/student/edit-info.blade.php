@extends('layouts.master')

@section('main')
    <h4>แจ้งชมรมที่ถูกต้อง</h4>

        <form method="POST" action="/invalidInfo" class="select-append">
        {{ csrf_field() }}
        <div class="row" style="margin-bottom:0">
            <div class="input-field col s12">
                <select name="club" required>
                    <option value="none" selected>ไม่มีชมรม</option>
                    {!! \App\Helper::createOption(\App\Club::fetchAllClubs()) !!}
                </select>
            </div>
        </div>
            <div class="sector grey lighten-4 red-text" style="font-size: 1.5rem;line-height: 1.8rem">
                หากชมรมของนักเรียนไม่ถูกต้อง โปรดติดต่อประธานชมรม หรือ Facebook Page : TUCMC ภายในวันที่ 15 กรกฎาคม 2562
            </div>
        <button class="btn waves-effect waves-light purple fullwidth" type="submit">
            บันทึกข้อมูล
        </button>

        </form>
    <br/>
    <br />
@endsection

@section('script')
    @parent
    <script>
        $(function () {
            $('select').material_select();
        });
    </script>
@endsection