<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_image' => 'required|image',
            // 'service_main_image' => 'required|image',
            // 'service_banner_image' => 'required|image',
            'service_title' => 'required',
            'service_card_description' => 'required',
            // 'service_description' => 'required',
            'service_price' => 'required',
            'service_alt_text' => 'required',
        ];
    }

     protected function failedValidation(Validator $validator)
        {
            $firstError = $validator->errors()->all()[0];

            throw new ValidationException($validator, redirect()->back()->withInput()->withErrors($validator->errors())->with('error_message', $firstError));
        }
}
