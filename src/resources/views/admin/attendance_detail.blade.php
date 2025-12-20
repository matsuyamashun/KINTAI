@php
use Carbon\Carbon;
@endphp

@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        勤怠詳細
    </div>

    <form action="{{ route('admin.attendance_detail', $request->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="attendance__detail">

            <div class="detail__item">
                <label>名前</label>
                <div>{{ $attendance->user->name }}</div>
            </div>

            <div class="detail__item">
                <label>日付</label>
                <div>{{ Carbon::parse($attendance->date)->isoFormat('Y年M月D日') }}</div>
            </div>

            <div class="detail__item">
                <label>出勤・退勤</label>
                <input class="detail__input" type="time" name="clock_in" value="{{ $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '' }}">

                <span>&emsp;〜&emsp;</span>

                <input class="detail__input" type="time" name="clock_out" value="{{ $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '' }}">
            </div>

            @error('clock_in')
                <div class="form__error">{{ $message }}</div>
            @enderror

            @error('clock_out')
                <div class="form__error">{{ $message }}</div>
            @enderror

            <!-- $indexは整数で複数回の休憩カウント -->
            @foreach($attendance->breaks as $index => $break)
                <div class="detail__item">
                    <label>休憩{{ $index + 1 }}</label>
                    <!-- 既存データですよを伝える -->
                    <input type="hidden" name="breaks[{{ $index }}][id]" value="{{ $break->id }}">

                    <input class="detail__input" type="time" name="breaks[{{ $index }}][start_time]" value="{{ $break->start_time ? Carbon::parse($break->start_time)->format('H:i') : '' }}">

                    <span>&emsp;〜&emsp;</span>

                    <input class="detail__input" type="time" name="breaks[{{ $index }}][end_time]" value="{{ $break->end_time ? Carbon::parse($break->end_time)->format('H:i') : '' }}">
                </div>

                @error("breaks.$index.start_time")
                    <div class="form__error">{{ $message }}</div>
                @enderror

                @error("breaks.$index.end_time")
                    <div class="form__error">{{ $message }}</div>
                @enderror
            @endforeach

            <div class="detail__item">
                <label>備考</label>
                <textarea class="detail__textarea" name="note">{{ old('note', $pendingRequest->note ?? $attendance->note) }}</textarea>

                @error('note')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="button__area">
            <button class="form__button" type="submit">
                修正
            </button>
        </div>
    </form>
</div>
@endsection