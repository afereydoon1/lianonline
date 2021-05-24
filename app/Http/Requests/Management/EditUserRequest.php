<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
        $id = (request()->has('id') ? request()->get('id') : request()->get('user_id'));
        return [
            'username' => 'unique:users,username,' . $id,
            'email' => 'unique:users,email,' . $id,
            'mobile' => 'required|unique:users,mobile,' . $id,
        ];
    }
}
