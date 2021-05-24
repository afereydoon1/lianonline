<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'title' => 'required',
            'key' => 'required|unique:products,key',
            // 'categories' => 'required',
            'price' => 'required',
            'marketer_percent' => 'numeric|min:0|max:60',
            'creator_id' => 'required|numeric',
            'discounted_price' => 'required_if:has_discount,1|numeric|min:0',
            'type' => 'required',
            'file' => 'required_if:type,file',
            'weight' => 'required_if:type,physical|numeric|min:0',
            'total' => 'required_if:type,physical|numeric|min:0',
            'description' => 'required',
        ];
    }
}
