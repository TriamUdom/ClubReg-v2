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