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
    <div id="timeline" style="height: 180px;"></div>
    <p>ขณะนี้อยู่ในรอบ:
        <b style="font-size: 1.3rem">
            @php
                $status = array();
                foreach (explode('&', config('core.round')) as $round) {
                    $status []= config('static.round')[$round];
                }
            @endphp
            {{ implode(', ', $status) }}
        </b>
    </p>
    <p>
        1. วันที่ 15 - 16 พฤษภาคม 2560 นักเรียนที่ต้องการเลือกชมรมเดิม ยืนยันสิทธิ์<br/>
        2. วันที่ 16 - 19 พฤษภาคม 2560 จัดการคัดเลือกนักเรียนเข้าชมรมที่ต้องมีการคัดเลือก (ออดิชั่น) โดยนักเรียนจะต้องยืนยันหรือปฏิเสธการเข้าชมรมที่นักเรียนผ่านการคัดเลือกภายในระยะเวลาดังกล่าว
        มิฉะนั้นระบบอาจยกเลิกสิทธิหรือจัดนักเรียนเข้าชมรมโดยอัตโนมัติ<br/>
        3. วันที่ 21 พฤษภาคม 2560 ตั้งแต่เวลา 09.00 น. นักเรียนลงทะเบียนเข้าชมรมที่ไม่ต้องคัดเลือก
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
                ['สมาชิกเดิม', 'สมาชิกเดิม', new Date(2017, 4, 15, 8), new Date(2017, 4, 16, 22)],
                ['คัดเลือก', 'คัดเลือก', new Date(2017, 4, 16, 8), new Date(2017, 4, 19, 23)],
                ['ไม่คัดเลือก', 'ไม่คัดเลือก', new Date(2017, 4, 21, 9), new Date(2017, 4, 21, 21)]]);

            var options = {
                timeline: {showRowLabels: false}
            };

            chart.draw(dataTable, options);
        }
    </script>

    <h4>เงื่อนไข</h4>
    <p>
        - แต่ละชมรมจะต้องมีจำนวนนักเรียนม.5 และม.6 ไม่เกิน 80% ของจำนวนนักเรียนสูงสุดที่เปิดรับ<br/>
        - อนุญาตให้นักเรียนใช้สิทธิเข้าชมรมเดิมได้ไม่เกิน 65% ของจำนวนนักเรียนสูงสุดที่เปิดรับ หากนักเรียนต้องการเข้าชมรมเดิมหลังรับนักเรียนเดิมเต็มขีดจำกัดแล้ว
        นักเรียนสามารถลงทะเบียนเสมือนไม่ได้อยู่ชมรมเดิมได้<br/>
        - ชมรมดุริยางค์ ไม่เปิดรับนักเรียนทั่วไป หากนักเรียนสนใจให้ติดต่อ<a href="/contact">ครูที่ปรึกษาชมรม</a>ด้วยตนเอง<br/>
        - ชมรมเชียร์ รับเฉพาะนักเรียนชั้นมัธยมศึกษาปีที่ 4 หรือ 5 ที่ได้รับคัดเลือกหรือเคยเป็นผู้นำเชียร์มาก่อนเท่านั้น หากนักเรียนต้องการเข้าร่วมชมรมให้ติดต่อ<a href="/contact">หัวหน้างานกิจกรรมพัฒนาผู้เรียน</a>
    </p>

    <h4>รายชื่อชมรม</h4>
    <table class="highlight">
        <thead>
        <tr>
            <th>รหัสวิชา</th>
            <th>ชื่อ</th>
            <th>ประเภท</th>
            @if (Request::has('count'))
                <th>
                    จำนวนสมาชิก / จำนวนรับ (ม.4+5+6)
                </th>
            @endif
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
                @if (Request::has('count'))
                    <td>
                        @if ($club->is_audition)
                            <b>
                                {{ $club->countMember() }}
                            </b>
                        @else
                            <b class="@if ($club->isAvailable(true) == 2) green-text @elseif(empty($club->isAvailable(true))) red-text @else amber-text @endif">
                                {{ $club->countMember() }}
                            </b>
                        @endif
                        / {{ $club->max_member }}
                        ({{ $club->members()->where('level', 4)->count() }} + {{ $club->members()->where('level', 5)->count() }} + {{ $club->members()->where('level', 6)->count() }})
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection