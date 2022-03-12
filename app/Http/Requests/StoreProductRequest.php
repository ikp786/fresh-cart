<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'price'          => 'required|numeric|digits_between:1,8',
            'category_id'    => 'required|exists:categories,id',
            'description'    => 'required',
            'image'          => 'required|array|min:3',
            'status'         => 'required|In:0,1'
        ];
    }
}
