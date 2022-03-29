<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


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
        return [
            'payment_method'        => 'required|In:Cod,Online',
            'order_id'              => 'required',
            'order_amount'          => 'required',
            'name'                  => 'required',
            'mobile'                => 'required',
            'email'                 => 'required|email',
            'address'               => 'required',
            'pincode'               => 'required|integer',
            'address_type'          => 'required'
        ];
    }


    public function messags()
    {
        return [
            'payment_method.required'        => 'Payment method should be required',            
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'ResponseCode'      => 200,
            'Status'            => False,
            'Message'           => $validator->errors()->first()
        ]));
    }
}
