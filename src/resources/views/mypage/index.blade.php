@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="profile-area">
        {{-- ✅ 修正：プロフィール画像を表示 --}}
        <div class="profile-icon">
            @if (Auth::user()->profile_image)
                <img src="{{ \Illuminate\Support\Facades\Storage::url(Auth::user()->profile_image) }}" 
                     alt="プロフィール画像" class="profile-avatar">
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-username">{{ Auth::user()->name ?? 'ユーザー名' }}</div>
        </div>
        <a href="{{ route('mypage.profile.edit') }}" class="profile-edit-btn">プロフィールを編集</a>
    </div>

    <div class="mypage-tab-menu">
        {{-- 出品商品タブ --}}
        <a href="{{ route('mypage.index') }}" 
           class="mypage-tab {{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
        
        {{-- 購入商品タブ --}}
        <a href="{{ route('mypage.index', ['page' => 'buy']) }}" 
           class="mypage-tab {{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="item-list">
        @forelse ($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">
                    <div class="item-image-placeholder">
                        @if ($item->image_url)
                            {{-- ✅ 修正：アクセサーを使用 --}}
                            <img src="{{ $item->image_url }}" alt="商品画像" class="item-image">
                        @else
                            <span class="item-image-text">商品画像</span>
                        @endif
                        {{-- ✅ 修正：商品が売り切れの場合のバッジ --}}
                        @if (!empty($item->is_sold) && $item->is_sold)
                            <span class="sold-badge">SOLD</span>
                        @endif
                    </div>
                    <div class="item-name">{{ $item->name }}</div>
                </a>
            </div>
        @empty
            {{-- タブに応じてメッセージを変更 --}}
            @if ($page === 'buy')
                <p>購入した商品はありません。</p>
            @else
                <p>出品した商品はありません。</p>
            @endif
        @endforelse
    </div>
</div>
@endsection