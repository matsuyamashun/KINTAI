@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify_email.css') }}" />
@endsection

@section('content')
<div class="content">
    <div class="content__email">
        <p>登録していただいたメールアドレスに認証メールを送付しました。</br>メール認証を完了してください。</p>

     <a href="http://localhost:8025" class="email__link">
        認証はこちらから
     </a>

     <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        <button class="email__button" type="submit">
            認証メールを再送する
        </button>
     </form>
    </div>
</div>
@endsection