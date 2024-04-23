<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
			"email" => "required|email|exists:users,email",
			"password" => "required|min:8|max:12|string|regex:/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/"
		];
		
	}
	// Messages
	public function messages(){
		return [
			'required' => "This field is required.",
			'phone' => "Please enter the valid phone number. i.e +125xxxxx...",
			'min' => "This field must be at least :min",
			'max' => "The field may not be greater than :max.",
			'exists' => "These credentials do not match our records.",
			'regex' => 'Password must be at least 8 characters long contain a number',
		];
	}
}
