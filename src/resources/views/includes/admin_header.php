<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KINTAI</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_header.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="{{ route('attendance') }}">
                    <img src="{{ asset('images/COACHTECHヘッダーロゴ.png')}}" alt="logo">
                </a>
            </div>

            <div class="header__nav">
                <a class="header__nav__item" href="#">勤怠</a>
                <a class="header__nav__item" href="#">勤怠一覧</a>
                <a class="header__nav__item" href="#">申請</a>
                <form class="header__nav__item" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="form__button">ログアウト</button>
                </form>
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>