<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use App\Rate;

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
            'params.amount'       =>  array('required', 'gt:0', 'regex:/^\d*(\.\d{1,2})?$/'), //amount
            'params.currency'     => 'required|exists:currencies,code', //currency
            'params.published_at' => 'required|date', //published at 
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
            'params.amount.required'       => "The <b>amount</b> field is required.",
            'params.currency.required'     => "The <b>currency</b> field is required.",
            'params.published_at.required' => "The <b>date</b> field is required.",

            'params.amount.required'       => "The <b>amount</b> field should be greater than 0.",
            'params.amount.regex'          => 'The <b>amount</b> should only have a maximum of 2 decimal places',

            'params.currency.exists' => "The selected <b>currency</b> does not exist.",

            'params.published_at.date'   => 'The specified <b>date</b> is not in the correct format.',
            'params.published_at.exists' => 'No rates found on the requested date and currency.',

        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if($validator->fails()){

        }else{

            $validator->after(function ($validator) {
                $count = Rate::where(['currency' => $this->input('params.currency'), 'published_at' => $this->input('params.published_at')])->count();
                if ($count == 0) {
                    $validator->errors()->add('rate', 'No rates found on the requested date and currency.');
                }
            });
        }
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
