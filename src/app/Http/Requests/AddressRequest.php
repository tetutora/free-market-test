<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'name' => 'required',
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required',
            'building' => 'required',
            'profile_picture' => 'nullable|mimes:jpeg,png|max:2048',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'zipcode.required' => '郵便番号を入力してください',
            'zipcode.regex:/^\d{3}-\d{4}$/' => '○○○-○○○○形式で入力してください',
            'address.required' => '住所を入力してください',
            'building.required' => '建物名を入力してください',
            'profile_picture.mimes:jpeg,png|max:2048' => '拡張子が.jpegもしくは.pngで入力してください'
        ];
    }
}
