<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'phone_no' => 'required|phone',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'is_admin' => 'required',
        ];

        if(request()->segment(2) == 'create'){
            $rules['password'] = 'required|min:8|max:12|string|regex:/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'This field is required',
            'email' => 'This field must be an email',
            'regex' => 'Password must be at least 8 characters long contain a number',
            'max' => 'The field may not be greater than :max characters.',
            'phone' => "Please enter the valid phone number. i.e +125xxxxx...",
        ];
    }
}
