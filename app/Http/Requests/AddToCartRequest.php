<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

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
    //         'product_quantity_phav' => 'required_without:product_quantity_half_kg,product_quantity_kg',
    // 'product_quantity_half_kg' => 'required_without:product_quantity_phav,product_quantity_kg',
    // 'product_quantity_kg' => 'required_without:product_quantity_phav,product_quantity_half_kg',

            'product_quantity_phav' => Rule::requiredIf(!$this->product_quantity_half_kg && !$this->product_quantity_kg),
            'product_quantity_half_kg' => Rule::requiredIf(!$this->product_quantity_phav && !$this->product_quantity_kg),
            'product_quantity_kg' => Rule::requiredIf(!$this->product_quantity_phav && !$this->product_quantity_half_kg),
            // 'number_2' => Rule::requiredIf(!$request->number_1 && !$request->number_3);
            // 'number_3' => Rule::requiredIf(!$request->number_1 && !$request->number_2);

            // 'product_quantity_phav' => 'required_if:product_quantity_half_kg,null|required_if:product_quantity_kg,null',
            // 'product_quantity_half_kg' => 'required_if:product_quantity_phav,null|required_if:product_quantity_kg,null',
            // 'product_quantity_kg' => 'required_if:product_quantity_phav,null|required_if:product_quantity_half_kg,null',

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
