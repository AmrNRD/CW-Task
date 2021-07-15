<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuggestedProductsRequest extends FormRequest
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
            'product1' => ['required', 'exists:products,id'],
            'product2' => ['required', 'exists:products,id'],
            'eur_amount' => ['required', 'numeric'],
            'suggestion_date' => ['required', 'date'],
        ];
    }
}
