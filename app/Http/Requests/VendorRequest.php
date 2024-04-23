<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_no' => 'required',
            'address' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'This field is required',
            'email' => 'This field must be an email',
        ];
    }
}
