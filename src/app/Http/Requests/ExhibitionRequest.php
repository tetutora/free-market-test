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
            'name' => 'required',
            'brand_name' => 'required',
            'description' => 'required|max:255',
            'image' => 'required|file|mimes:jpeg,jpg,png',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|string',
            'price' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return[
            'name.required' => '商品名を入力してください',
            'brand_name.required' => 'ブランド名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明を:max文字以内で入力してください',
            'image.required' => '商品の画像を選択してください',
            'image.mimes:jpeg,jpg,png' => '商品の画像は.jpegもしくは.pngとしてください',
            'category_id.required' => '商品のカテゴリーを選択してください',
            'status.required' => '商品の状態を選択してください',
            'price.required' => '商品の価格を入力してください',
            'price.numeric' => '商品の価格は数値型で入力してください',
            'price.min:1' => '商品の価格は:min円以上でで入力してください',
        ];
    }
}
