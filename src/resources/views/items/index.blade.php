@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="container">
  {{-- ✅ 検索結果表示エリア --}}
  @if(isset($keyword) && $keyword)
    <div class="search-result-info">
      <p class="search-keyword">「{{ $keyword }}」の検索結果：{{ $items->count() }}件</p>
      <a href="{{ route('items.index', ['tab' => request('tab')]) }}" class="clear-search">✕ 検索をクリア</a>
    </div>
  @endif

  {{-- ✅ 修正：タブリンクに検索キーワードを保持 --}}
  <div class="tab-menu">
    <a href="{{ route('items.index', ['keyword' => $keyword]) }}"
       class="tab {{ $tab !== 'mylist' ? 'active' : '' }}">おすすめ</a>
    <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => $keyword]) }}"
       class="tab {{ $tab === 'mylist' ? 'active' : '' }}">マイリスト</a>
  </div>

  {{-- 商品一覧 --}}
  <div class="item-list">
    @forelse ($items as $item)
      <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}">
          <div class="item-image-placeholder">
            @if ($item->image_url)
              {{-- ✅ アクセサーを使用 --}}
              <img src="{{ $item->image_url }}" alt="商品画像" class="item-image">
            @else
              <span class="item-image-text">商品画像</span>
            @endif
            @if (!empty($item->is_sold) && $item->is_sold)
              <span class="sold-badge">SOLD</span>
            @endif
          </div>
          <div class="item-name">{{ $item->name }}</div>
        </a>
      </div>
    @empty
      {{-- ✅ 修正：適切な空状態メッセージ --}}
      @if(isset($keyword) && $keyword)
        <p class="no-results">「{{ $keyword }}」に一致する商品が見つかりませんでした。</p>
      @else
        @if($tab === 'mylist')
          <p class="no-results">いいねした商品はありません。</p>
        @else
          <p class="no-results">商品はまだありません。</p>
        @endif
      @endif
    @endforelse
  </div>
</div>
@endsection