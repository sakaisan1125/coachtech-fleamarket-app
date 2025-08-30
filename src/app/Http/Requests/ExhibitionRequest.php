<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check(); // 認証済みのみ許可
    }

    public function rules()
    {
        return [
            'name'        => 'required',
            'description' => 'required|max:255',
            'image'       => 'required|image|mimes:jpeg,png|max:4096',
            'category_id' => 'required|array',
            'category_id.*' => 'integer|exists:categories,id',
            'condition'   => 'required|string',
            'price'       => 'required|integer|min:0',
            'brand'       => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '商品名',
            'description' => '商品説明',
            'image' => '商品画像',
            'category_id' => '商品のカテゴリー',
            'condition' => '商品の状態',
            'price' => '商品価格',
            'brand' => 'ブランド',
        ];
    }
}
