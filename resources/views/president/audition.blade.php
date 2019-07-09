@extends('layouts.master')

@section('style')
    <style>
        td {
            padding: 10px 5px;
        }
    </style>
@endsection

@section('main')
    <h3 class="center-align">คำขอคัดเลือก</h3>
    <table class="highlight">
        <thead>
        <tr>
            <th>เลขประจำตัวนักเรียน</th>
            <th>นักเรียน</th>
            <th>ห้อง</th>
            <th>สถานะ</th>
            <th>คำสั่ง</th>
        </tr>
        </thead>
        <tbody>
        @foreach(($club = \App\Club::currentPresident())->auditions()->orderBy('status')->get() as $audition)
            <tr>
                <td>{{ str_pad(($user = $audition->getUser())->student_id, 5, "0", STR_PAD_LEFT) }}</td>
                <td>{{ $user->getName() }}</td>
                <td>ม.{{ $user->level }} / {{ $user->room }}</td>
                <td>{{ $audition->getStatus() }}</td>
                <td>
                    @if(!\App\Helper::isRound(\App\Helper::Round_Audition))
                        ไม่อยู่ในช่วงออดิชั่น
                    @elseif ($club->isAvailableForLevel($user->level))
                            @if ($audition->status == \App\Audition::Status_Awaiting)
                                <button class="btn waves-effect waves-light green" type="submit" onclick="updateAudition('pass');">
                                    ผ่าน
                                </button>
                                <button class="btn waves-effect waves-light red" type="submit" onclick="updateAudition('fail');">
                                    ปฏิเสธ
                                </button>
                            @elseif ($audition->status == \App\Audition::Status_Failed)
                                <button class="btn waves-effect waves-light blue-grey" type="submit" onclick="updateAudition('pass');">
                                    เปลี่ยนเป็นผ่าน
                                </button>
                            @elseif ($audition->status == \App\Audition::Status_Passed)
                                <button class="btn waves-effect waves-light blue-grey" type="submit" onclick="updateAudition('fail');">
                                    เปลี่ยนเป็นปฏิเสธ
                                </button>
                            @else
                                -
                            @endif
                    @else
                        <b class="red-text">เต็มโควตาแล้ว</b>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('script')
    @parent
    <script>
        function updateAudition(action) {
            $.ajax({
                type: "POST",
                url: '/president/audition',
                data: jQuery.param({
                    audition: '{{ $audition->id }}',
                    action: action,
                    _token: '{{ csrf_token() }}'
                }),
                success: function(data) {
                    if (data.code === 200){
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
