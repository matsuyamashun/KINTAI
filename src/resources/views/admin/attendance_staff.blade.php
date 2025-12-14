@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_staff.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        {{ $user->name }}さんの勤怠
    </div>

    <div class="content__nav">
        <a class="nav__button"
            href="{{ route('admin.attendance_staff', [
               'user' => $user->id,
               'year' => $prevDate->year,
               'month' => $prevDate->month,
           ]) }}">
            ← 前月
        </a>

        <p class="current__month">{{ $date->format('Y/m') }}</p>

        <a class="nav__button"
            href="{{ route('admin.attendance_staff', [
               'user' => $user->id,
               'year' => $nextDate->year,
               'month' => $nextDate->month,
           ]) }}">
            翌日 →
        </a>
    </div>

    <table class="attendance__table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach($attendanceList as $daylabel => $attendance)

            <tr>
                <td class="table__cell">
                    {{ $daylabel }}
                </td>

                <td class="table__cell">
                    {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}
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
                    @if($attendance)
                    <a href="{{ route('admin.attendance_detail', $attendance->id) }}">詳細</a>
                    @else
                    --
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="button__area">
        <a class="csv__button" href="{{ route('admin.attendance_csv', [
            'user'  => $user->id,
            'year'  => $date->year,
            'month' => $date->month,
        ]) }}">
            CSV出力
        </a>
    </div>
</div>

@endsection