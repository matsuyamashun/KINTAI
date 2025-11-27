@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('content')
<div class="content">
    <h2 class="content__title">会員登録</h2>
</div>

<form class="content__form" action="{{ route('register') }}" method="POST">
    @csrf

    <div class="form__group">
        <label>名前</label>
            <input class="form__name" type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <div class="form__error">{{ $message }}</div>
            @enderror
    </div>

    <div class="form__group">
        <label>メールアドレス</label>
        <input class="form__email" type="email" name="email" value="{{ old('email') }}">
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

    <div class="form__group">
        <label>パスワード確認</label>
        <input class="form__password" type="password" name="password_confirmation">
    </div>

    <div class="form__group">
        <button class="form__button" type="submit">登録する</button>
        <p class="form__link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>
    </div>
</form>
@endsection