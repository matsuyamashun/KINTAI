あ
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="form__button">ログアウト</button>
</form>