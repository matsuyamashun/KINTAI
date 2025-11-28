@extends('includes.header')
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}" />
@endsection

@section('content')

<div class="content">
    <div class="attendance__status">
        {{ $status }}
    </div>

    <div class="attendance__date">
        {{ $todayDate }}
    </div>

    <div class="attendance__time">
        {{ $currentTime}}
    </div>

    <div class="attendance__button">
        @if($status === '勤務外')
            <form action="{{ route('attendance.start') }}" method="POST">
                @csrf
                <button class="button__clock">出勤</button>
            </form>
        @endif

        @if($status === '出勤中')
            <form action="{{ route('attendance.end') }}" method="POST">
                @csrf
                <button class="button__clock">退勤</button>
            </form>

            <form action="{{ route('break.start') }}" method="POST">
                @csrf
                <button class="button__break">休憩入</button>
            </form>
        @endif

        @if($status === '休憩中')
            <form action="{{ route('break.end') }}" method="POST">
                @csrf
                <button class="button__break">休憩戻</button>
            </form>
        @endif

        @if($status === '退勤済')
            <p class="end__message">お疲れ様でした。</p>
        @endif
    </div>

</div>

@endsection