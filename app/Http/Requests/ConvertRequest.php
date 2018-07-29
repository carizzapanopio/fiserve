<?php

namespace App\Http\Requests;

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
            'amount'       =>  array('required','regex:/^\d*(\.\d{1,2})?$/'),
            'currency'     => 'required',
            'published_at' => 'required|date|exists:rates,published_at,currency,'.$this->input('currency'),
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
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         if ($this->somethingElseIsInvalid()) {
    //             $validator->errors()->add('field', 'Something is wrong with this field!');
    //         }
    //     });
    // }
}
