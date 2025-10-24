@component('mail::message')
# 取引完了のお知らせ

{{ $seller->name }} 様

以下の商品について、購入者（{{ $buyer->name }}）が取引を完了し、評価を送信しました。

**商品名**：{{ $item->name ?? '商品' }}  
**購入ID**：#{{ $purchase->id }}

@component('mail::button', ['url' => route('chat.index', ['purchaseId' => $purchase->id])])
取引メッセージを確認する
@endcomponent

引き続き {{ config('app.name') }} をご利用ください。
@endcomponent