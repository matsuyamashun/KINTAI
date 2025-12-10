@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_login.css') }}" />
@endsection

@section('content')
<div class="content">
    <h2 class="content__title">管理者ログイン</h2>
</div>

<form class="content__form" action="{{ route('admin.login') }}" method="POST">
    @csrf

    <div class="form__group">
        <label>メールアドレス</label>
        <input class="form__email" type="email" name="email">

        @error('email')
        <div class="form__error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form__group">
        <label>パスワード</label>
        <input class="form__password" type="password" name="password">

        @error('password')
        <div class="form__error">{{ $message }}</div>
        @enderror
    </div>

    @if(session('error'))
    <div class="form__error">{{ session('error') }}</div>
    @endif

    <div class="form__group">
        <button class="form__button" type="submit">管理者ログインする</button>
    </div>
</form>
@endsection