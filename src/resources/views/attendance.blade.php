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

    @php
        use App\Models\Attendance;
    @endphp

    <div class="attendance__button">
        @if($status === Attendance::STATUS_OFF)
            <form action="{{ route('attendance.start') }}" method="POST">
                @csrf
                <button class="button__clock">出勤</button>
            </form>
        @endif

        @if($status === Attendance::STATUS_WORKING)
            <form action="{{ route('attendance.end') }}" method="POST">
                @csrf
                <button class="button__clock">退勤</button>
            </form>

            <form action="{{ route('break.start') }}" method="POST">
                @csrf
                <button class="button__break">休憩入</button>
            </form>
        @endif

        @if($status === Attendance::STATUS_BREAK)
            <form action="{{ route('break.end') }}" method="POST">
                @csrf
                <button class="button__break">休憩戻</button>
            </form>
        @endif

        @if($status === Attendance::STATUS_DONE)
            <p class="end__message">お疲れ様でした。</p>
        @endif
    </div>

</div>

@endsection