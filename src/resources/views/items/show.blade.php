@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="item-detail-container">

  {{-- 画像エリア --}}
  <div class="item-detail-image">
    @if ($item->image_url)
      {{-- ✅ アクセサーを使用 --}}
      <img src="{{ $item->image_url }}" alt="商品画像">
    @else
      <div class="no-image">画像がありません</div>
    @endif
  </div>

  {{-- 詳細情報エリア --}}
  <div class="item-detail-info">
    {{-- 商品名・ブランド --}}
    <h2 class="item-title">{{ $item->name }}</h2>
    <div class="item-brand">{{ $item->brand }}</div>

    {{-- 価格 --}}
    <div class="item-price">
      ￥{{ number_format($item->price) }} <span class="tax-in">（税込）</span>
    </div>

    {{-- いいね・コメントアイコン等 --}}
    @auth
      <div class="like-comment-area">
        <div class="like-area">
          @if (auth()->user()->likes->where('item_id', $item->id)->count())
            <form action="{{ route('items.unlike', $item) }}" method="POST" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="like-btn liked">★</button>
            </form>
          @else
            <form action="{{ route('items.like', $item) }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="like-btn">☆</button>
            </form>
          @endif
          <span class="like-count">{{ $item->likes->count() }}</span>
        </div>
        
        {{-- コメント数表示 --}}
        <div class="comment-area">
          <span class="comment-icon">💬</span>
          <span class="comment-count">{{ $item->comments->count() }}</span>
        </div>
      </div>
    @endauth

    {{-- 未ログインの場合 --}}
    @guest
      <div class="like-comment-area">
        <div class="like-area">
          <span class="like-btn disabled" title="ログインしてください">☆</span>
          <span class="like-count">{{ $item->likes->count() }}</span>
        </div>
        
        {{-- コメント数表示 --}}
        <div class="comment-area">
          <span class="comment-icon">💬</span>
          <span class="comment-count">{{ $item->comments->count() }}</span>
        </div>
      </div>
    @endguest

    {{-- 購入ボタン --}}
    @auth
        @if ($item->user_id === auth()->id())
            {{-- ✅ 自分の商品の場合 --}}
            <span class="buy-btn disabled">自分の商品です</span>
        @elseif ($item->is_sold)
            {{-- ✅ 購入済みの場合 --}}
            <span class="buy-btn disabled sold">SOLD</span>
        @else
            {{-- ✅ 購入可能 --}}
            <a href="{{ route('purchase.show', $item->id) }}" class="buy-btn">購入手続きへ</a>
        @endif
    @else
        {{-- ✅ 未ログインの場合 --}}
        <a href="{{ route('login') }}" class="buy-btn">購入手続きへ</a>
    @endauth

    {{-- 商品説明 --}}
    <div class="item-section">
      <div class="section-title">商品説明</div>
      <div class="item-description">
        <!-- <div>カラー：{{ $item->color ?? '未設定' }}</div> -->
        <!-- <div>{{ $item->condition }}</div> -->
        <div>{!! nl2br(e($item->description)) !!}</div>
        <!-- <div>購入後、即発送いたします。</div> -->
      </div>
    </div>

    {{-- 商品の情報 --}}
    <div class="item-section">
      <div class="section-title">商品の情報</div>
      <div class="category-list">
        <span class="category-label">カテゴリー</span>
        @foreach($item->categories as $category)
        <span class="category-badge">{{ $category->name }}</span>
        @endforeach
      </div>
      <div class="item-condition">
        <span class="item-condition-label">商品の状態</span>
        <span class="item-condition-badge">{{ $item->condition }}</span>
      </div>
    </div>

    {{-- コメント欄 --}}
    <div class="item-section">
      <div class="section-title">コメント ({{ $item->comments->count() }})</div>
       {{-- 既存のコメント表示 --}}
      @forelse($item->comments as $comment)
        <div class="comment">
          @if ($comment->user->profile_image)
            <img src="{{ Storage::url($comment->user->profile_image) }}" class="avatar" alt="プロフィール画像">
          @else
            <span class="avatar"></span>
          @endif
          <span class="username">{{ $comment->user->name }}</span>
          <br>
          <div class="comment-box">{!! nl2br(e($comment->content)) !!}</div>
        </div>
      @empty
        <div class="no-comments">こちらにコメントが入ります。</div>
      @endforelse
      {{-- コメント投稿フォーム --}}
      @auth
        {{-- ✅ 成功メッセージの表示 --}}
        @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        <form action="{{ route('comments.store', $item) }}" method="POST">
          @csrf
          <div class="item-comment">商品へのコメント</div>
          
          {{-- ✅ バリデーションエラーの表示 --}}
          @error('content')
            <div class="error-message">{{ $message }}</div>
          @enderror
          
          {{-- ✅ 入力値保持とエラー時のスタイル適用 --}}
          <textarea name="content" 
                    rows="10" 
                    class="comment-textarea @error('content') error @enderror" 
                    >{{ old('content') }}</textarea>
          <br>
          <button type="submit" class="comment-btn">コメントを送信する</button>
        </form>
      @else
        <div class="login-required">
          <a href="{{ route('login') }}">コメントを送信する</a>
        </div>
      @endauth
    </div>
  </div>
</div>
@endsection