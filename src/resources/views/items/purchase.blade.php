@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<form action="/purchase/{{ $item->id }}" method="POST">
  @csrf
  
  {{-- 追加：住所データを送信するためのhiddenフィールド --}}
  <input type="hidden" name="address" value="{{ $user->address }}">
  
  <div class="purchase-container">
    <div class="purchase-main">
        <div class="item-header">    
            <div class="item-image-placeholder">
                @if ($item->image_url)
                    {{-- ✅ 修正：アクセサーを使用 --}}
                    <img src="{{ $item->image_url }}" alt="商品画像" class="item-image">
                @else
                    <span class="item-image-text">商品画像</span>
                @endif
            </div>
            <div class="item-info">
                <div class="item-name">{{ $item->name }}</div>
                <div class="item-price">￥{{ number_format($item->price) }}</div>
            </div>
        </div>
      <hr>
      
      {{-- 🔧 追加：バリデーションエラー表示 --}}
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
      
      <div class="payment-area">
        <label>支払い方法</label>
        <select name="payment_method" id="payment_method" class="payment-select">
          <option value="" hidden disabled selected>選択してください</option>
          <!-- <option value="convenience">コンビニ支払い</option> -->
          <option value="konbini">コンビニ支払い</option>
          <option value="card">カード支払い</option>
        </select>
      </div>
      <hr>
      <div class="address-area">
        <div class="address-label-row">
            <label>配送先</label>
            <a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="address-edit-link">変更する</a>
        </div>
        <div class="address-info">
          〒{{ $user->zipcode }}<br>
          {{ $user->address }}
          @if($user->building)
          <br>{{ $user->building }}
          @endif
        </div>
      </div>
      <hr>
    </div>
    <div class="purchase-summary">
      <div class="summary-table">
        <div class="summary-row">
          <div class="summary-label">商品代金</div>
          <div class="summary-value">￥{{ number_format($item->price) }}</div>
        </div>
        <div class="summary-row">
          <div class="summary-label">支払い方法</div>
          <div class="summary-value" id="summary-payment-method">-</div>
        </div>
      </div>
      <button type="submit" class="purchase-btn">購入する</button>
    </div>
  </div>
</form>

{{-- 🔧 追加：支払い方法の動的表示 --}}
<script>
document.getElementById('payment_method').addEventListener('change', function() {
    const summaryElement = document.getElementById('summary-payment-method');
    const selectedOption = this.options[this.selectedIndex];
    summaryElement.textContent = selectedOption.text;
});
</script>
@endsection