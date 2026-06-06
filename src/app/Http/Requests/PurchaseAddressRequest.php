<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseAddressRequest extends FormRequest
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
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'prefecture' => 'required|string|max:100',
            'street_address' => 'nullable|string|max:255',
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
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフン付きで入力してください（例: 100-0001）',
            'prefecture.required' => '都道府県を入力してください',
            'prefecture.max' => '都道府県は100文字以内で入力してください',
            'street_address.max' => '住所は255文字以内で入力してください',
        ];
    }
}
