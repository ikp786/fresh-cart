<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddToCartRequest extends FormRequest
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
            'product_id'                => 'required|exists:products,id',
            // 'product_quantity_phav'     => 'integer',
            // 'product_quantity_half_kg'  => 'integer',
            // 'product_quantity_kg'       => 'integer',

            'product_quantity_phav' => 'required_if:product_quantity_half_kg,null|required_if:product_quantity_kg,null',
            'product_quantity_half_kg' => 'required_if:product_quantity_phav,null|required_if:product_quantity_kg,null',
            'product_quantity_kg' => 'required_if:product_quantity_phav,null|required_if:product_quantity_half_kg,null',

        ];
    }   

    public function messags()
    {
        return [
            'product_id.required'      => 'Product Id should be required',
            'product_id.exists'        => 'Product Id not found',
            'product_id.exists'        => 'Product Id should be integer'
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