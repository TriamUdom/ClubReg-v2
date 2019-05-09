@extends('layouts.master')

@section('style')
    <style>
        td {
            padding: 10px 5px;
        }

        h4 {
            margin-top: 2rem;
            font-size: 2rem
        }
    </style>
@endsection

@section('main')
    <h3 class="center-align">รายละเอียดการลงทะเบียน</h3>

    <h4>การรับลงทะเบียนในรอบต่างๆ</h4>
    <div id="timeline" style="height: 300px;"></div>
    <p>
        ระดับชั้นมัธยมศึกษาปีที่ 4 :<br />
        1.	วันที่ 17 – 20 พฤษภาคม ลงทะเบียนเรียนชมรมที่ไม่มีการออดิชั่น หรือ ลงชื่อแจ้งความประสงค์จะออดิชั่น<br />
        2.	วันที่ 21 – 24 พฤษภาคม ดำเนินการออดิชั่น<br />
        3.	วันที่ 25 พฤษภาคม นักเรียนทำการตอบรับการออดิชั่น<br />
        4.	วันที่ 26 พฤษภาคม นักเรียนที่ไม่ผ่านการออดิชั่น ลงทะเบียนเรียนชมรมที่ไม่มีการออดิชั่น<br />
        <br />
        ระดับชั้นมัธยมศึกษาปีที่ 5 และ 6:<br />
        1.	วันที่ 16 พฤษภาคม นักเรียนยืนยันสิทธิ์ลงทะเบียนชมรมเดิม<br />
        2.	วันที่ 17 – 20 พฤษภาคม ลงทะเบียนเรียนชมรมที่ไม่มีการออดิชั่น หรือ ลงชื่อแจ้งความประสงค์จะออดิชั่น<br />
        3.	วันที่ 21 – 24 พฤษภาคม ดำเนินการออดิชั่น<br />
        4.	วันที่ 25 พฤษภาคม นักเรียนทำการตอบรับการออดิชั่น<br />
        5.	วันที่ 26 พฤษภาคม นักเรียนที่ไม่ผ่านการออดิชั่น ลงทะเบียนเรียนชมรมที่ไม่มีการออดิชั่น<br />
    </p>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['timeline'], 'language': 'th'});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var container = document.getElementById('timeline');
            var chart = new google.visualization.Timeline(container);
            var dataTable = new google.visualization.DataTable();

            dataTable.addColumn({type: 'string', id: 'ประเภท'});
            dataTable.addColumn({type: 'string', id: 'ประเภท'});
            dataTable.addColumn({type: 'date', id: 'เริ่ม'});
            dataTable.addColumn({type: 'date', id: 'สิ้นสุด'});
            dataTable.addRows([
                ['สมาชิกเดิม', 'สมาชิกเดิม', new Date(2019, 4, 16, 8), new Date(2019, 4, 16, 22)],
                ['ลงทะเบียน', 'ลงทะเบียน', new Date(2019, 4, 17, 8), new Date(2019, 4, 20, 23)],
                ['คัดเลือก', 'คัดเลือก', new Date(2019, 4, 21, 8), new Date(2019, 4, 24, 23)],
                ['ตอบรับการคัดเลือก', 'ตอบรับการคัดเลือก', new Date(2019, 4, 25, 8), new Date(2019, 4, 25, 23)],
                ['เก็บตก', 'เก็บตก', new Date(2019, 4, 25, 10), new Date(2019, 4, 26, 21)]]);

            var options = {
                timeline: {showRowLabels: false}
            };

            chart.draw(dataTable, options);
        }
    </script>

    <h4>เงื่อนไข</h4>
    <p>
        - ไม่อนุญาตให้นักเรียนที่อยู่ระหว่างลาพักการเรียนเข้าใช้ระบบ<br />
        - ชมรมที่มีการคัดเลือก แบ่งอัตราส่วนนักเรียนเป็นนักเรียนเก่าร้อยละ 50 และนักเรียนใหม่ร้อยละ 50<br />
        - ชมรมที่ไม่มีการคัดเลือก แบ่งอัตราส่วนนักเรียนเป็นนักเรียนเก่าร้อยละ 45 นักเรียนชั้นมัธยมศึกษาปีที่ 4 ร้อยละ 35 และนักเรียนที่ไม่ผ่านการคัดเลือก ร้อยละ 20<br />

    </p>

    <h4>รายชื่อชมรม</h4>
    <table class="highlight">
        <thead>
        <tr>
            <th>รหัสวิชา</th>
            <th>ชื่อ</th>
            <th>ประเภท</th>
            <th>
                จำนวนสมาชิก / จำนวนรับ
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Club::all() as $club)
            <tr>
                <td>{{ $club->id }}</td>
                <td title="{{ $club->english_name }}">{{ $club->name }}</td>
                <td>
                    @if (!$club->is_active)
                        <span class="red-text">ไม่เปิดรับ</span>
                    @elseif ($club->is_audition)
                        <span class="cyan-text">คัดเลือก</span>
                    @else
                        <span class="light-green-text">ไม่คัดเลือก</span>
                    @endif
                </td>
                <td>
                    @if ($club->is_audition)
                        <b class="@if ($club->isAvailable()) green-text @else red-text @endif">
                            @else
                                <b class="@if ($club->isAvailable(true) == 2) green-text @elseif(empty($club->isAvailable(true))) red-text @else amber-text @endif">
                                    @endif
                                    {{ $club->countMember() }}
                                </b>

                                / {{ $club->max_member }}
                                @if (Request::has('count'))
                                    ({{ $club->members()->where('level', 4)->count() }} + {{ $club->members()->where('level', 5)->count() }} + {{ $club->members()->where('level', 6)->count() }})
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection