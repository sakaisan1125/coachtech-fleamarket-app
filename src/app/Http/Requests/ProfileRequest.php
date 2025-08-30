<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 必要なら認可処理
    }

    public function rules()
    {
        return [
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'name' => 'required|string|max:20',
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'profile_image.image' => '画像ファイルを選択してください',
            'profile_image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
            'profile_image.max' => '画像サイズは2MB以下にしてください',
            'name.required' => 'ユーザー名は必須です',
            'name.max' => 'ユーザー名は20文字以内で入力してください',
            'zipcode.required' => '郵便番号は必須です',
            'zipcode.regex' => '郵便番号は「123-4567」の形式で入力してください',
            'address.required' => '住所は必須です',
            'building.max' => '建物名は255文字以内で入力してください',
        ];
    }
}
