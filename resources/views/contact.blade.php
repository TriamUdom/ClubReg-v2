@extends('layouts.master')

@section('title', 'ติดต่อ - ระบบทะเบียนชมรม โรงเรียนเตรียมอุดมศึกษา')

@section('style')
    <style>
    </style>
@endsection

@section('main')
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=1586680534936149";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

    <div style="max-width: 500px;text-align: center;">
        <h3 class="center-align">ติดต่อ</h3>

    <p>
        &emsp; หากนักเรียนประสบปัญหาในการลงทะเบียน หรือมีข้อสงสัย สามารถติดต่อหัวหน้างานกิจกรรมพัฒนาผู้เรียน ณ ตึก 50 ปี ฝั่งโรงอาหารโดมทอง หรือติดต่อ <a href="https://www.facebook.com/triamudomclubs/">เพจ TUCMC</a>
    </p>
    <br />
    <div style="margin:auto;text-align: center">
        <div class="fb-page" data-href="https://www.facebook.com/triamudomclubs/" data-tabs="messages" data-height="300" data-small-header="false" data-adapt-container-width="true"
            data-hide-cover="false" data-show-facepile="true">
            <blockquote cite="https://www.facebook.com/triamudomclubs/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/triamudomclubs/">TUCMC</a></blockquote>
        </div>
    </div>
    </div>
   
@endsection