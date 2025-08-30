@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection

@section('content')
    <div class="exhibition-container">
        <h2>商品の出品</h2>

        {{-- エラーメッセージ --}}
        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="color:red;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 商品画像セクション --}}
            <div class="form-group image-upload-group">
                <label for="image" class="section-label">商品画像</label>
                <div class="image-upload-container">
                    <label class="image-drop-area" for="image" id="imageDropArea">
                        <span class="image-select-btn" id="imageDropText">画像を選択する</span>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg" style="display: none;">
                    </label>
                    {{-- アップロード済み画像のプレビュー --}}
                    <div id="imagePreview" style="display: none;">
                        <img id="previewImg" src="" alt="プレビュー" style="max-width: 100%; max-height: 150px; border-radius: 6px;">
                        <button type="button" id="removeImage" style="margin-top: 8px; background: #ff5c5c; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">画像を削除</button>
                    </div>
                </div>
            </div>

            <h3 class="section-title">商品の詳細</h3>

            {{-- カテゴリー選択 --}}
            <div class="form-group">
                <label class="section-label">カテゴリー</label>
                <div class="category-list">
                    @foreach ($categories as $category)
                        <label class="category-tag" data-category="{{ $category->id }}">
                            <input type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                {{ (collect(old('category_id'))->contains($category->id)) ? 'checked' : '' }}>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 商品の状態 --}}
            <div class="form-group">
                <label for="condition" class="section-label">商品の状態</label>
                <select name="condition" id="condition">
                    <option value="" disabled selected hidden>選択してください</option>
                    <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                    <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
                </select>
            </div>

            {{-- 商品名と説明セクション --}}
            <h3 class="section-title">商品名と説明</h3>

            {{-- 商品名 --}}
            <div class="form-group">
                <label for="name" class="section-label">商品名</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" maxlength="255">
            </div>

            {{-- ブランド名 --}}
            <div class="form-group">
                <label for="brand" class="section-label">ブランド名</label>
                <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
            </div>

            {{-- 商品説明 --}}
            <div class="form-group">
                <label for="description" class="section-label">商品の説明</label>
                <textarea name="description" id="description" rows="4" maxlength="255">{{ old('description') }}</textarea>
            </div>

            {{-- 販売価格 --}}
            <div class="form-group">
                <label for="price" class="section-label">販売価格</label>
                <div class="price-input-container">
                    <span class="price-prefix">¥</span>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" placeholder="">
                </div>
            </div>

            <button type="submit" class="exhibition-btn">出品する</button>
        </form>
    </div>

    {{-- JavaScript for image upload --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imageDropArea = document.getElementById('imageDropArea');
            const imageDropText = document.getElementById('imageDropText');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImage');

            // ファイル選択時の処理
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imageDropArea.style.display = 'none';
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // 画像削除ボタンの処理
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imageDropArea.style.display = 'flex';
                imagePreview.style.display = 'none';
                previewImg.src = '';
            });

            // ドラッグ&ドロップ対応
            imageDropArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                imageDropArea.style.background = '#f0f0f0';
            });

            imageDropArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                imageDropArea.style.background = '#fff';
            });

            imageDropArea.addEventListener('drop', function(e) {
                e.preventDefault();
                imageDropArea.style.background = '#fff';

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        imageInput.files = files;
                        const event = new Event('change', { bubbles: true });
                        imageInput.dispatchEvent(event);
                    }
                }
            });
        });
    </script>
@endsection