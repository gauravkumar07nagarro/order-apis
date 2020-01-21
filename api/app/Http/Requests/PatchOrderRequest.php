<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;


class PatchOrderRequest extends FormRequest
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
           'status' => 'required|string|in:TAKEN',
           'id'     => 'required|integer'
        ];
    }

    /**
     * Add route parameters in validation rules
     *
     * @return array
     */

    public function validationData(){
        return array_merge($this->all(),$this->route()->parameters());
    }

    /**
     * Overriding failed validation response
     * @return Exception
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['error' => $validator->errors()->first()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
