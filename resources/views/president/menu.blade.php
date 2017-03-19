@extends('layouts.master')

@section('style')
    <style>
        h4 {
            text-align: center
        }

        h1 {
            text-align: center;
            font-size: 4rem;
        }

        body {
            background-color: #009688
        }

        input[type='number'] {
            -moz-appearance: textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .countdownText {
            font-size: 2rem;
        }
    </style>
@endsection

@section('nav')
    @parent
    <div class="white-text teal" style="height:20px"></div>
@endsection

@section('main')
    <div class="z-depth-1 card-panel grey lighten-5" style="max-width:700px;margin:auto">
        @php
            $club = \App\Club::currentPresident();
        @endphp

    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('select').material_select();
        });
    </script>
@endsection
