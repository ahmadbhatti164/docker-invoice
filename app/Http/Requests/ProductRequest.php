<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'price' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'vat' => 'required',
            'grand_total' => 'required|numeric|min:0',
            'qty' => 'required|numeric|min:0',
        ];

        if(request()->segment(2) == 'create'){
            $rules['user_id'] = 'required';
            $rules['invoice_id'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'This field is required',
            'min' => 'The field must be greater than 0.',
        ];
    }
}
