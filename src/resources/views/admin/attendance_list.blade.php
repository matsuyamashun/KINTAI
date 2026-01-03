@php
use Carbon\carbon;
@endphp

@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        {{ $date->format('Y年m月d日') }}の勤怠一覧
    </div>

    <div class="day__nav">
        <a class="nav__button" href="{{ route('admin.attendance_list', $yesterday) }}"><span class="span__item">←</span>前日</a>

        <p class="current__month">{{ $date->format('Y/m/d') }}</p>

        <a class="nav__button" href="{{ route('admin.attendance_list', $tomorrow) }}">翌日<span class="span__item">→</span></a>
    </div>

    <table class="attendance__table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tobody>
            @foreach($attendances as $attendance)

            <tr>
                <td class="table__cell">
                    {{ $attendance->user->name }}
                </td>

                <td class="table__cell">
                    {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                </td>

                <td class="table__cell">
                    {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}
                </td>

                <td class="table__cell">
                    {{ $attendance ? $attendance->totalBreakTime() : '' }}
                </td>

                <td class="table__cell">
                    {{ $attendance ? $attendance->workingHours() : '' }}
                </td>

                <td class="table__cell">
                    <a href="{{ route('admin.attendance_detail', $attendance->id) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tobody>
    </table>
</div>
@endsection