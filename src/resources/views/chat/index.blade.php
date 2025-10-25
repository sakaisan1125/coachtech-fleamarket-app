@extends('layouts.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    <div class="sidebar">
        <h2>その他の取引</h2>
        <ul class="transaction-list">
            @foreach ($otherTransactions as $transaction)
                <li>
                    <a href="{{ route('chat.index', ['purchaseId' => $transaction->id]) }}">
                        {{ $transaction->item->name ?? '商品名不明' }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="main-content">
        <div class="chat-header">
            <div class="header-content">
                @if ($otherUserIconUrl === asset('images/default-icon.png'))
                    <div class="user-icon-large default-icon"></div>
                @else
                    <img src="{{ $otherUserIconUrl }}" alt="ユーザーアイコン" class="user-icon-large">
                @endif
                <h2 class="user-name">「{{ $otherUserName }}」さんとの取引画面</h2>

                @if ($isBuyer && !$isCompletedByBuyer)
                    <button class="complete-transaction" id="complete-transaction-button">取引を完了する</button>
                    <div id="complete-transaction-modal-buyer" class="modal" style="display:none">
                        <div class="modal-content">
                            <h2>取引を完了します。</h2>
                            <p>今回の取引相手はどうでしたか？</p>
                            <form action="{{ route('chat.completeTransaction', ['purchaseId' => $purchaseId]) }}" method="POST">
                                @csrf
                                <div class="rating-stars" role="radiogroup" aria-label="取引相手の評価">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                        <label for="star{{ $i }}" aria-label="{{ $i }}点">★</label>
                                    @endfor
                                </div>
                                <button type="submit" class="send-rating">送信する</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            <div class="header-border"></div>
            <div class="product-info">
                <img src="{{ $productImageUrl }}" alt="商品画像" class="product-image">
                <div class="product-details">
                    <h1>{{ $productName }}</h1>
                    <p>{{ $productPrice }}円</p>
                </div>
            </div>
        </div>

        @if ($isSeller && $isCompletedByBuyer && !$purchase->ratings->where('rater_user_id', auth()->id())->first())
            <div id="complete-transaction-modal-seller" class="modal">
                <div class="modal-content">
                    <h2>取引が完了しました。</h2>
                    <p>今回の取引相手はどうでしたか？</p>
                    <form action="{{ route('chat.completeTransaction', ['purchaseId' => $purchaseId]) }}" method="POST">
                        @csrf
                        <div class="rating-stars" role="radiogroup" aria-label="取引相手の評価">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                <label for="star{{ $i }}" aria-label="{{ $i }}点">★</label>
                            @endfor
                        </div>
                        <button type="submit" class="send-rating">送信する</button>
                    </form>
                </div>
            </div>
        @endif

        <div class="chat-messages">
            @foreach ($messages as $message)
                <div class="message {{ $message->sender_id === auth()->id() ? 'my-message' : 'other-message' }}">
                    <div class="message-content">
                        <div class="message-header">
                            @if ($message->sender->profile_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($message->sender->profile_image) }}" alt="ユーザーアイコン" class="message-user-icon">
                            @else
                                <div class="message-user-icon message-default-icon"></div>
                            @endif
                            <p class="message-user-name">{{ $message->sender->name }}</p>
                        </div>
                        <div class="message-body">
                            @if (session('chat.edit_message_id') == $message->id)
                                <form action="{{ route('chat.update', ['purchaseId' => $purchaseId, 'messageId' => $message->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" rows="4" required>{{ old('content', $message->content) }}</textarea>
                                    <button type="submit" class="update-button">更新</button>
                                    <a href="{{ route('chat.index', ['purchaseId' => $purchaseId]) }}" class="cancel-button">キャンセル</a>
                                </form>
                            @else
                                <p>{{ $message->content }}</p>
                                @if ($message->image_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($message->image_path) }}" alt="送信画像" class="message-image">
                                @endif
                            @endif
                        </div>
                        @if ($message->sender_id === auth()->id())
                            <div class="message-actions">
                                <form action="{{ route('chat.index', ['purchaseId' => $purchaseId]) }}" method="GET">
                                    <input type="hidden" name="edit_message_id" value="{{ $message->id }}">
                                    <button type="submit" class="edit-button">編集</button>
                                </form>
                                <form action="{{ route('chat.destroy', ['purchaseId' => $purchaseId, 'messageId' => $message->id]) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button">削除</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <p class="error-text">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data" class="chat-form" id="chat-form">
            @csrf
            <input type="hidden" name="purchase_id" value="{{ $purchaseId }}">
            <textarea id="chat-content" name="content" placeholder="取引メッセージを記入してください" data-purchase-id="{{ $purchaseId }}">{{ old('content') }}</textarea>
            <label for="image-upload" class="image-upload-label">
                <span class="add-image-text">画像を追加</span>
            </label>
            <input type="file" name="image" id="image-upload" class="image-upload-input" accept="image/*">
            <button type="submit" class="send-message">
                <img src="{{ asset('images/send.jpg') }}" alt="送信" class="send-icon">
            </button>
        </form>

        @push('scripts')
        <script>
            (function () {
                const textarea = document.getElementById('chat-content');
                if (!textarea) return;

                const purchaseId = textarea.dataset.purchaseId;
                const KEY = `chat:draft:${purchaseId}`;
                const SENT = @json(session()->has('success'));

                if (SENT) {
                    try { localStorage.removeItem(KEY); } catch (e) {}
                    textarea.value = '';
                } else {
                    const saved = localStorage.getItem(KEY);
                    if (saved && textarea.value.trim() === '') {
                        textarea.value = saved;
                    }
                }

                let t;
                textarea.addEventListener('input', () => {
                    clearTimeout(t);
                    t = setTimeout(() => {
                        localStorage.setItem(KEY, textarea.value);
                    }, 300);
                });
            })();
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const buyerModal = document.getElementById('complete-transaction-modal-buyer');
                const completeBtn = document.getElementById('complete-transaction-button');
                const sellerModal = document.getElementById('complete-transaction-modal-seller');

                if (completeBtn && buyerModal) {
                    completeBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        buyerModal.style.display = 'block';
                    });
                }

                if (sellerModal && {{ $isSeller ? 'true' : 'false' }} && {{ $isCompletedByBuyer ? 'true' : 'false' }}) {
                    sellerModal.style.display = 'block';
                }
            });
        </script>
        @endpush
    </div>
</div>
@endsection