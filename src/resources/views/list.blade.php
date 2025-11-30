@php 
    use Carbon\Carbon;
@endphp

@extends('includes.header')
@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        勤怠一覧
    </div>

    <div class="month__nav">
        <a class="nav__button" href="{{ route('attendance.list',[$prevDate->year, $prevDate->month]) }}"><span>←</span>前月</a>

        <p class="current__month">{{ $date->format('Y/m') }}</p>

        <a class="nav__button" href="{{ route('attendance.list',[$nextDate->year, $nextDate->month]) }}">翌月<span>→</span></a>
    </div>

    <table class="attendance__table">
        @foreach ($attendances as $attendance)
            <tr>
                <td class="table__cell">
                    <div class="cell__label">日付</div>
                    <div class="cell__value">
                        <p>{{ Carbon::parse($attendance->date)->isoFormat('M/D(dd)') }}</p>
                    </div>
                </td>

                <td class="table__cell">
                    <div class="cell__label">出勤</div>
                    <div class="cell__value">
                        <p>{{ Carbon::parse($attendance->clock_in)->format('H:i') }}</p>
                    </div>
                </td>

                <td class="table__cell">
                    <div class="cell__label">退勤</div>
                    <div class="cell__value">
                        <p>{{ Carbon::parse($attendance->clock_out)->format('H:i') }}</p>
                    </div>
                </td>

                <td class="table__cell">
                    <div class="cell__label">休憩</div>
                    <div class="cell__value">
                        <p>{{ $attendance->totalBreakTime() }}</p>
                    </div>
                </td>

                <td class="table__cell">
                    <div class="cell__label">合計</div>
                    <div class="cell__value">
                        <p>{{ $attendance->workingHours() }}</p>
                    </div>
                </td>

                <td class="table__cell">
                    <div class="cell__label">詳細</div>
                    <div class="cell__value table__detail">
                        <p>#</p>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection