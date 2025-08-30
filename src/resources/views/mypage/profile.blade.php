@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2 class="profile-title">プロフィール設定</h2>

    <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="profile-image-area">
            @if ($user->profile_image)
                {{-- ✅ 修正：Storage::url()を使用 --}}
                <img src="{{ Storage::url($user->profile_image) }}" class="profile-avatar" alt="プロフィール画像">
            @else
                <span class="profile-avatar profile-avatar-default"></span>
            @endif
            <label class="image-upload-btn">
                画像を選択する
                <input type="file" name="profile_image" accept="image/jpeg,image/png" style="display:none;">
            </label>
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
            @error('name')
                <span class="error-message" style="color:#d00; font-size:13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="zipcode">郵便番号</label>
            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $user->zipcode) }}">
            @error('zipcode')
                <span class="error-message" style="color:#d00; font-size:13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
            @error('address')
                <span class="error-message" style="color:#d00; font-size:13px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
            @error('building')
                <span class="error-message" style="color:#d00; font-size:13px;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="profile-update-btn">更新する</button>
    </form>
</div>
@endsection
