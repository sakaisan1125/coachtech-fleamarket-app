<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'フリマアプリ')</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @yield('css')
</head>
<body>
  <header class="header">
    <div class="header__left">
      <a href="{{ route('items.index') }}">
          <img src="{{ asset('images/coachtech-logo.svg') }}" alt="COACHTECH ロゴ">
      </a>
    </div>
  </header>
  <main>
    @yield('content')
  </main>
</body>
</html>
