@php
    use Carbon\Carbon;

    // 承認待ち申請がある
    $isLocked = isset($pendingRequest);

    // 出勤・退勤の表示値
    $clockIn  = $isLocked
        ? Carbon::parse($pendingRequest->new_clock_in)->format('H:i')
        : Carbon::parse($attendance->clock_in)->format('H:i');

    $clockOut = $isLocked && $pendingRequest->new_clock_out
        ? Carbon::parse($pendingRequest->new_clock_out)->format('H:i')
        : ($attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '');

    if ($isLocked) {
        $breaks = json_decode($pendingRequest->new_breaks, true);
    } else {
        $breaks = $attendance->breaks->map(function($b){
            return [
                'start_time' => Carbon::parse($b->start_time)->format('H:i'),
                'end_time'   => $b->end_time ? Carbon::parse($b->end_time)->format('H:i') : '',
            ];
        })->toArray();
    }

    if (!is_array($breaks)) {
        $breaks = [];
    }

    if (count($breaks) === 0) {
        $breaks[] = ['start_time' => '', 'end_time' => ''];
    }
@endphp

@extends('includes.header')
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

                @error('clock_in')
                    <div class="form__error">{{ $message }}</div>
                @enderror

                @error('clock_out')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            @foreach(($breaks ?? []) as $i => $break)
                <div class="detail__item">
                    <label>休憩 {{ $i + 1 }}</label>

                    <input class="detail__input"
                           type="time"
                           name="breaks[{{ $i }}][start_time]"
                           value="{{ $break['start_time'] }}">

                    <span>&emsp;〜&emsp;</span>

                    <input class="detail__input"
                           type="time"
                           name="breaks[{{ $i }}][end_time]"
                           value="{{ $break['end_time'] }}">
                    @error("breaks.$i.start_time")
                        <div class="form__error">{{ $message }}</div>
                    @enderror

                    @error("breaks.$i.end_time")
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
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