<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        ];

        if(!empty(request()->new_password)){
            $rules['current_password'] = 'required|password';
            $rules['new_password'] = 'required|min:8|max:12|string|regex:/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/';
            $rules['confirm_new_password'] =' required|same:new_password';
        }

        return $rules;
    }
}
