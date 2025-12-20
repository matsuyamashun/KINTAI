@php
use Carbon\Carbon;
@endphp

@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{ asset('css/correction_detail.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">勤怠詳細</div>

    <div class="attendance__detail">

        <div class="detail__item">
            <label>名前</label>
            <div>{{ $request->attendance->user->name }}</div>
        </div>

        <div class="detail__item">
            <label>日付</label>
            <div>{{ Carbon::parse($request->date)->isoFormat('Y年M月D日') }}</div>
        </div>

        <div class="detail__item">
            <label>出勤・退勤</label>
            <div>{{Carbon::parse($request->new_clock_in)->format('H:i') }}</div>

            <span>&emsp;〜&emsp;</span>

            <div>{{ Carbon::parse($request->new_clock_out)->format('H:i') }}</div>
        </div>

        @foreach($breaks as $index => $break)
            <div class="detail__item">
                <label>休憩{{ $index +1 }}</label>
                <div>{{ $break['start_time'] }}</div>

                <span>&emsp;〜&emsp;</span>

                <div>{{ $break['end_time'] }}</div>
            </div>
        @endforeach

        <div class="detail__item">
            <label>備考</label>
            <div>{{ $request->note }}</div>
        </div>
    </div>

    @if ($request->status === 'approved')
        <div class="button__area">
            <button class="approved__button">
                承認済み
            </button>
        </div>
    @else
        <form action="{{ route('admin.correction_approve', $request->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="button__area">
                <button class="correction__button" type="submit">
                    承認
                </button>
            </div>
    @endif
</div>
@endsection