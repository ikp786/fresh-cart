<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'           => 'required',
            'pav_price'      => 'required|numeric|digits_between:1,8',
            'half_kg_price'  => 'required|numeric|digits_between:1,8',
            'kg_price'       => 'required|numeric|digits_between:1,8',
            'category_id'    => 'required|exists:categories,id',
            'description'    => 'required',
            'image'          => 'array',
            'status'         => 'required|In:0,1'
        ];
    }
}
