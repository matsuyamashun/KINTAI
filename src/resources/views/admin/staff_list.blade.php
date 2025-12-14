@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="{{ asset('css/staff_list.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__title">
        スタッフ一覧
    </div>

    <table class="attendance__table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($users as $user)

            <tr>
                <td class="table__cell">
                    {{ $user->name }}
                </td>

                <td class="table__cell">
                    {{ $user->email }}
                </td>

                <td class="table__cell">
                    <a href="{{ route('admin.attendance_staff', $user->id) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection