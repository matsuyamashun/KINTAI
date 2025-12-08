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
        <a class="nav__button" href="{{ route('attendance.list', $prevDate->format('Y-m')) }}"><span>←</span>前月</a>

        <p class="current__month">{{ $date->format('Y/m') }}</p>

        <a class="nav__button" href="{{ route('attendance.list',$nextDate->format('Y-m')) }}">翌月<span>→</span></a>
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
        
        <tobody>
            @foreach($attendanceList as $date => $attendance)   

                <tr>   
                    <td class="table__cell">
                        {{ $date }}
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
                            <a href="{{ route('attendance.detail', $attendance->id) }}">詳細</a>
                        @else 
                            --
                        @endif
                    </td>
                </tr>           
            @endforeach
        </tobody>
    </table>
</div>
@endsection