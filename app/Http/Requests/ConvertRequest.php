<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class ConvertRequest extends FormRequest
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
            'params.amount'       =>  array('required','regex:/^\d*(\.\d{1,2})?$/'), //amount
            'params.currency'     => 'required', //currency
            'params.published_at' => 'required|date|exists:rates,published_at,currency,'.$this->input('params.currency'), //published at
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'regex'  => 'The :attribute should only have a maximum of 2 decimal places',
            'date'   => 'The specified date is not in the correct format.',
            'exists' => 'No rates found on the requested date.',
        ];
    }

    /**
     * Return the error messages for the defined validation rules.
     *
     * @return array
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
   
}
