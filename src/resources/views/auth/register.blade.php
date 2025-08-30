@extends('layouts.auth')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="register-wrap">
  <div class="register-title">会員登録</div>
  <form class="register-form" method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    <div class="register-group">
      <label for="name">ユーザー名</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}"  autofocus>
      @error('name') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="register-group">
      <label for="email">メールアドレス</label>
      <input id="email" type="text" name="email" value="{{ old('email') }}">
      @error('email') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="register-group">
      <label for="password">パスワード</label>
      <input id="password" type="password" name="password" autocomplete="new-password">
      @error('password')
      @if($message !== 'パスワードと一致しません。') <div class="form-error">{{ $message }}</div>
      @endif
      @enderror
    </div>

    <div class="register-group">
      <label for="password_confirmation">確認用パスワード</label>
      <input id="password_confirmation" type="password" name="password_confirmation">
      @error('password')
      @if($message === 'パスワードと一致しません。') <div class="form-error">{{ $message }}</div>
      @endif
      @enderror
    </div>

    <button type="submit" class="register-btn">登録する</button>
  </form>
  <div class="register-link">
    <a href="{{ route('login') }}">ログインはこちら</a>
  </div>
</div>
@endsection
