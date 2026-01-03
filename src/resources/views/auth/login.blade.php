@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
@endsection

@section('content')
<div class="content">
    <h2 class="content__title">ログイン</h2>
</div>

<form class="content__form" action="{{ route('login') }}" method="POST">
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
        <button class="form__button" type="submit">ログインする</button>
        <p class="form__link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </p>
    </div>
</form>
@endsection