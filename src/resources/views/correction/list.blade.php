@php
    use Carbon\Carbon;
@endphp

@extends('includes.header')
@section('css')
<link rel="stylesheet" href="{{ asset('css/correction_list.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        申請一覧
    </div>

    <div class="tabs">
        <a class="tab__pending" href="{{ route('correction.list', ['tab' => 'pending']) }}">承認待ち</a>
        <a class="tsb__approved" href="{{ route('correction.list', ['tab' => 'approved']) }}">承認済み</a>
    </div>

    <table class="attendance__table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>
                        @if($request->status === 'pending')
                            承認待ち
                        @elseif($request->status === 'approved')
                            承認済み
                        @endif
                    </td>

                    <td>{{ $request->attendance->user->name }}</td>

                    <td>{{ Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>

                    <td>{{ $request->note }}</td>

                    <td>{{ $request->created_at->format('Y/m/d') }}</td>

                    <td>
                        <a class="attendance__detail" href="{{ route('attendance.detail', $request->attendance_id) }}">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection