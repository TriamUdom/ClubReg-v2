@extends('layouts.master')

@section('main')
    <h4>แก้ไขชมรมของนักเรียน</h4>

    <div class="row">
        <div class="input-field col s12">
            <input id="username" name="student_id" class="validate" required="" type="text">
            <label for="username">เลขประจำตัวนักเรียน</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <label>ชมรม</label>
        </div>
        <div class="input-field col s12">
            <select name="club" id="club" autocomplete="off">
                <option value="none" selected>ไม่มีชมรม</option>
                @foreach(\App\Club::all() as $club)
                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="reason_col" class="row" style="display:none;">
        <div class="col s12">
            <label>ชนิดการสมัครเข้าชมรม</label>
        </div>
        <div class="input-field col s12">
            <select name="reason" id="reason" autocomplete="off">
                <option value="AUDITION">ออดิชั่น</option>
                <option value="CONFIRM">ยืนยันเข้าชมรมเดิม</option>
                <option value="WAR">ลงทะเบียนทั่วไป</option>
                <option value="SPECIAL">อื่นๆ / พิเศษ</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn waves-effect blue fullwidth" onclick="setClub();" style="margin-left:10px">บันทึก</button>
    <br/><br />

@endsection

@section('script')
    @parent
    <script>
        $(function () {
            $('select').material_select();
        });

        $("#club").on('change', function() {
            var club = $("#club").val();

            if (club == 'none'){
                $("#reason").prop("disabled", true);

                $("#reason_col").hide();
            }
            else{
                $("#reason").prop("disabled", false);

                $("#reason_col").show();
            }
        });

        function setClub(){
            $.ajax({
                type: "POST",
                url: '/setClub',
                data: jQuery.param({
                    student_id: $('#username').val(),
                    club: $('#club').val(),
                    reason: $('#reason').val(),
                    _token: '{{ csrf_token() }}'
                }),
                success: function(data) {
                    if (data.code == 200){
                        Materialize.toast('สำเร็จ!', 4000);
                    }
                    else{
                        Materialize.toast('มีข้อผิดพลาดเกิดขึ้น', 4000);
                    }
                },
                error: function(error){
                    Materialize.toast('มีข้อผิดพลาดเกิดขึ้น', 4000);
                },
                dataType: 'json'
            });
        }
    </script>
@endsection