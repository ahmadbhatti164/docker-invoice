<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'invoice_number' => 'required',
            'invoice_date' => 'required|date',
            'total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'vat' => 'required',
            'grand_total' => 'required|numeric|min:0',
            'currency_id' => 'required',
            'billing_address' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
        ];

        if(request()->segment(2) == 'create'){
            $rules['vendor_id'] = 'required';
            $rules['user_id'] = 'required';
            $rules['pdf_file'] = 'required|mimes:pdf';
            $rules['html_file'] = 'required|mimes:html';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'This field is required',
            'min' => 'The field must be greater than 0.',
            'pdf_file.max' =>'This file may not be grater than 3MB.',
            'html_file.max' =>'This file may not be grater than 3MB.',
            'pdf_file.mimes' =>'This file must be in .pdf format.',
            'html_file.mimes' =>'This file must be in .html format.',
        ];
    }
}
