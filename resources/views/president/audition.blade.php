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
            <th>รหัสนักเรียน</th>
            <th>นักเรียน</th>
            <th>ห้อง</th>
            <th>สถานะ</th>
            <th>คำสั่ง</th>
        </tr>
        </thead>
        <tbody>
        @foreach(($club = \App\Club::currentPresident())->auditions()->orderBy('status')->get() as $audition)
            <tr>
                <td>{{ ($user = $audition->user)->student_id }}</td>
                <td>{{ $user->getName() }}</td>
                <td>ม.{{ $user->level }} / {{ $user->room }}</td>
                <td>{{ $audition->getStatus() }}</td>
                <td>

                    @if ($club->isAvailableForLevel($user->level))
                        <form method="POST" action="/president/audition">
                            {{ csrf_field() }}
                            <input type="hidden" name="audition" value="{{ $audition->id }}"/>

                            @if ($audition->status == \App\Audition::Status_Awaiting)
                                <button class="btn waves-effect waves-light green" type="submit" name="action" value="pass">
                                    ผ่าน
                                </button>
                                <button class="btn waves-effect waves-light red" type="submit" name="action" value="fail">
                                    ปฏิเสธ
                                </button>
                            @elseif ($audition->status == \App\Audition::Status_Failed)
                                <button class="btn waves-effect waves-light blue-grey" type="submit" name="action" value="pass">
                                    เปลี่ยนเป็นให้ผ่าน
                                </button>
                            @elseif ($audition->status == \App\Audition::Status_Passed)
                                <button class="btn waves-effect waves-light blue-grey" type="submit" name="action" value="fail">
                                    เปลี่ยนเป็นปฏิเสธ
                                </button>
                            @else
                                -
                            @endif
                        </form>
                    @else
                        <b class="red-text">เต็มแล้ว</b>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection