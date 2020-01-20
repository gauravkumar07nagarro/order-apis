<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreOrderRequest extends FormRequest
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
        $rules =  [
            'origin'        => 'required|array|size:2|orderlatlong',
            'destination'   => 'required|array|size:2|orderlatlong',
        ];

        return $rules;
    }

    /**
     * Custom validation messages in response
     */
    public function messages()
    {
        return [
            'origin.orderlatlong' => trans('order.invalid_origin_lat_long'),
            'destination.orderlatlong' => trans('order.invalid_destination_lat_long')
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['error' => $validator->errors()->all()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
