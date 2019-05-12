@extends('layouts.master')

@section('main')
    <form action="/president/settings" method="POST">
        @php
            $club = \App\Club::currentPresident();
        @endphp
        {{ csrf_field() }}
        <h4>แก้ไขข้อมูลของ{{ $club->name }}</h4>
        @if (count($errors) > 0)
            <div class="sector red darken-2 white-text">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <h5>ประธานครูที่ปรึกษา</h5>
        <div class="row">
            <div class="input-field col s6 m3 l2">
                <input id="iAdTitle" name="adviser_title" type="text" class="validate" required length="50" value="{{ $club->adviser_title }}" placeholder="ใช้คำเต็ม"/>
                <label for="iAdTitle">คำนำหน้าชื่อ</label>
            </div>
            <div class="input-field col s6 m3 l4">
                <input id="iAdFname" name="adviser_fname" type="text" class="validate" required length="80" value="{{ $club->adviser_fname }}"/>
                <label for="iAdFname">ชื่อ</label>
            </div>
            <div class="input-field col s6 m3 l4">
                <input id="iAdLname" name="adviser_lname" type="text" class="validate" length="80" value="{{ $club->adviser_lname }}"/>
                <label for="iAdLname">นามสกุล</label>
            </div>
            <div class="input-field col s6 m3 l2">
                <input id="iAdPhone" name="adviser_phone" type="text" class="validate" length="10" value="{{ $club->adviser_phone }}"/>
                <label for="iAdPhone">โทรศัพท์</label>
            </div>
        </div>

        <h5>นักเรียนประธานชมรม</h5>
        <div class="row">
            <div class="input-field col s6 m3 l2">
                <input id="iPrTitle" name="president_title" type="text" class="validate" required length="50" value="{{ $club->president_title }}" placeholder="ใช้คำเต็ม"/>
                <label for="iPrTitle">คำนำหน้าชื่อ</label>
            </div>
            <div class="input-field col s6 m3 l4">
                <input id="iPrFname" name="president_fname" type="text" class="validate" required length="80" value="{{ $club->president_fname }}"/>
                <label for="iPrFname">ชื่อ</label>
            </div>
            <div class="input-field col s6 m3 l4">
                <input id="iPrLname" name="president_lname" type="text" class="validate" required length="80" value="{{ $club->president_lname }}"/>
                <label for="iPrLname">นามสกุล</label>
            </div>
            <div class="input-field col s6 m3 l2">
                <input id="iPrPhone" name="president_phone" type="text" class="validate" length="10" value="{{ $club->president_phone }}"/>
                <label for="iPrPhone">โทรศัพท์</label>
            </div>
        </div>

        <br/>

        @if ($club->is_audition)
            <div class="row">
                <div class="input-field col s12">
                    <input id="iAuditionLoc" name="audition_location" type="text" class="validate" length="150" value="{{ $club->audition_location }}"/>
                    <label for="iAuditionLoc">สถานที่คัดเลือก</label>
                </div>

                <div class="input-field col s12">
                    <input id="iAuditionTime" name="audition_time" type="text" class="validate" length="150" value="{{ $club->audition_time }}"/>
                    <label for="iAuditionTime">เวลาคัดเลือก</label>
                </div>

                <div class="input-field col s12">
                    <input id="iAuditionInst" name="audition_instruction" type="text" class="validate" length="150" value="{{ $club->audition_instruction }}"/>
                    <label for="iAuditionInst">เวลาคัดเลือก</label>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="input-field col s12">
                <input id="iLocation" name="location" type="text" class="validate" required length="150" value="{{ $club->location }}"/>
                <label for="iLocation">สถานที่จัดการเรียนการสอน</label>
            </div>
        </div>

        @if ($club->is_active)
            <div class="row">
                <div class="input-field col s12">
                    <textarea name="description" id="iContent" class="materialize-textarea" style="min-height:5rem"
                              placeholder="แนะนำชมรม และวิธีการคัดเลือก (ถ้ามี)">{{ $club->description }}</textarea>
                    <label for="iContent">คำแนะนำชมรม</label>
                </div>
            </div>
        @endif

        <button type="submit" class="btn waves-effect blue fullwidth" style="margin-left:10px">บันทึก</button>
        <br/><br />
    </form>

@endsection