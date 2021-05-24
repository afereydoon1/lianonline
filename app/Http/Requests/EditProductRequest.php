<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProductRequest extends FormRequest
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
        $id = (request()->has('id') ? request()->get('id') : request()->get('product_id'));
        return [
            'title' => 'required',
            'key' => 'required|unique:products,key,' . $id,
            // 'categories' => 'required',
            'price' => 'required|numeric|min:0',
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
