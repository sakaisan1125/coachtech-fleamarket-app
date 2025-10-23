@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="profile-area">
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
        <a href="{{ route('mypage.index') }}" 
        class="mypage-tab {{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
        
        <a href="{{ route('mypage.index', ['page' => 'buy']) }}" 
        class="mypage-tab {{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>

        <a href="{{ route('mypage.index', ['page' => 'transactions']) }}" 
        class="mypage-tab {{ $page === 'transactions' ? 'active' : '' }}">
            取引中の商品
            @if ($totalMessageCount > 0)
                <span class="notification-badge">{{ $totalMessageCount }}</span>
            @endif
        </a>
    </div>

    <div class="item-list">
        @forelse ($items as $item)
            @php
                $imgUrl = $item->image_url ?? optional($item->item)->image_url;
                $title = $item->name ?? optional($item->item)->name;
                $messageCount = $messageCounts[$item->id] ?? 0; // メッセージ件数を取得
            @endphp

            <div class="item-card">
                @if ($page === 'transactions')
                    <a href="{{ route('chat.index', ['purchaseId' => $item->id]) }}" class="item-link">
                @else
                    <a href="{{ route('items.show', $item->id) }}" class="item-link">
                @endif
                    <div class="item-image-placeholder">
                        @if ($messageCount > 0)
                            <span class="message-count-badge">{{ $messageCount }}</span>
                        @endif
                        @if ($imgUrl)
                            <img src="{{ $imgUrl }}" alt="商品画像" class="item-image">
                        @else
                            <span class="item-image-text">商品画像</span>
                        @endif

                        @if (!empty($item->is_sold) && $item->is_sold)
                            <span class="sold-badge">SOLD</span>
                        @endif
                    </div>
                    <div class="item-name">{{ $title }}</div>
                </a>
            </div>
        @empty
            @if ($page === 'buy')
                <p>購入した商品はありません。</p>
            @elseif ($page === 'transactions')
                <p>取引中の商品はありません。</p>
            @else
                <p>出品した商品はありません。</p>
            @endif
        @endforelse
    </div>
</div>
@endsection