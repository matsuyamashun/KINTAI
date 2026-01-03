@php
use Carbon\Carbon;
use App\Models\StampCorrectionRequest;
@endphp

@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_correction.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        申請一覧
    </div>

    <div class="tabs">
        <a class="tab__pending" href="{{ route('admin.correction_list', ['tab' => StampCorrectionRequest::STATUS_PENDING]) }}">承認待ち</a>
        <a class="tsb__approved" href="{{ route('admin.correction_list', ['tab' => StampCorrectionRequest::STATUS_APPROVED]) }}">承認済み</a>
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
                    @if($request->status === StampCorrectionRequest::STATUS_PENDING)
                    承認待ち
                    @elseif($request->status === StampCorrectionRequest::STATUS_APPROVED)
                    承認済み
                    @endif
                </td>

                <td>{{ $request->attendance->user->name }}</td>

                <td>{{ Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>

                <td>{{ $request->note }}</td>

                <td>{{ $request->created_at->format('Y/m/d') }}</td>

                <td>
                    <a class="attendance__detail" href="{{ route('admin.correction_detail', $request->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection