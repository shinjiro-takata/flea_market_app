<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:5120'],
            'category' => ['required', 'integer'],
            'brand_name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0', 'max:99999999'],
        ];
    }
}
