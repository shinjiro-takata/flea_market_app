<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:20',
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'prefecture' => 'required|string|max:100',
            'street_address' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '名前を入力してください',
            'name.max' => '名前は20文字以内で入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフン付きで入力してください（例: 100-0001）',
            'prefecture.required' => '都道府県を入力してください',
            'prefecture.max' => '都道府県は100文字以内で入力してください',
            'street_address.max' => '住所は255文字以内で入力してください',
            'profile_image.image' => 'プロフィール画像は画像ファイルである必要があります',
            'profile_image.mimes' => 'プロフィール画像はJPEG、またはPNG形式である必要があります',
            'profile_image.max' => 'プロフィール画像は2MB以下である必要があります',
        ];
    }
}
