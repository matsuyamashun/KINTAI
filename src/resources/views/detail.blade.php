@php
use Carbon\Carbon;
@endphp

@extends('layouts.header')
@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">勤怠詳細</div>

    <form action="{{ route('attendance.detail', $attendance->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <fieldset {{ $isLocked ? 'disabled' : '' }}>
            <div class="attendance__detail">

                <div class="detail__item">
                    <label>名前</label>
                    <div>{{ $attendance->user->name }}</div>
                </div>

                <div class="detail__item">
                    <label>日付</label>
                    <div>{{ Carbon::parse($attendance->date)->isoFormat('Y/M/D(dd)') }}</div>
                </div>

                <div class="detail__item">
                    <label>出勤・退勤</label>

                    <input class="detail__input"
                        type="time"
                        name="clock_in"
                        value="{{ $clockIn }}">

                    <span>&emsp;〜&emsp;</span>

                    <input class="detail__input"
                        type="time"
                        name="clock_out"
                        value="{{ $clockOut }}">
                </div>

                @error('clock_in')
                <div class="form__error">{{ $message }}</div>
                @enderror

                @error('clock_out')
                <div class="form__error">{{ $message }}</div>
                @enderror

                @foreach($breaks as $index => $break)
                <div class="detail__item">
                    <label>休憩 {{ $index + 1 }}</label>

                    <input class="detail__input"
                        type="time"
                        name="breaks[{{ $index }}][start_time]"
                        value="{{ $break['start_time'] }}">

                    <span>&emsp;〜&emsp;</span>

                    <input class="detail__input"
                        type="time"
                        name="breaks[{{ $index }}][end_time]"
                        value="{{ $break['end_time'] }}">
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
        </fieldset>

        @if(!$isLocked)
        <div class="button__area">
            <button class="correction__button"
                type="submit"
                {{ $isLocked ? 'disabled' : '' }}>
                修正
            </button>
        </div>
        @endif

        @if($isLocked)
        <div class="form__lock">
            ※承認待ちのため修正はできません。
        </div>
        @endif
    </form>
</div>
@endsection