<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを実行する権限があるかを判定
     */
    public function authorize()
    {
        return auth()->check(); // ログインユーザーのみ許可
    }

    /**
     * バリデーションルールを定義
     */
    public function rules()
    {
        return [
            'content' => 'required|string|max:255',
        ];
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages()
    {
        return [
            'content.required' => 'コメント内容を入力してください。',
            'content.string'   => 'コメント内容は文字列で入力してください。',
            'content.max'      => 'コメント内容は255文字以内で入力してください。',
        ];
    }
}