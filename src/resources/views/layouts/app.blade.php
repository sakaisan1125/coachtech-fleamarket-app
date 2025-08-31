<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>フリマアプリ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @yield('css')
</head>
<body>

  <header class="header">
      <div class="header__left">
        <a href="{{ route('items.index') }}">
          <img src="{{ asset('images/coachtech-logo.svg') }}" alt="COACHTECH ロゴ">
        </a>
      </div>
      <div class="header__center">
        <form action="{{ route('items.index') }}" method="GET" class="search-form">
          <input type="text" 
                 name="keyword" 
                 value="{{ request('keyword') }}" 
                 placeholder="なにをお探しですか？"
                 class="search-input">
          @if(request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
          @endif
        </form>
      </div>
      <div class="header__right">
        @auth
          <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">ログアウト</button>
          </form>
        @endauth
        @guest
          <a href="{{ route('login') }}" class="login-btn">ログイン</a>
        @endguest
        <a href="{{ route('mypage.index') }}" class="mypage-btn">マイページ</a>
        <a href="{{ route('items.create') }}" class="item-create-btn">出品</a>
      </div>
  </header>

  <main class="container">
    @yield('content')
  </main>

</body>
</html>