<?php

namespace App\Http\Requests;

use App\Rules\IsHtmlRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|min:3|max:250',
            'price'         => 'required|numeric|gte:0',
            'description'   => ['nullable','string', new IsHtmlRule()],
            'quantity'      => 'required|numeric|gt:0',
            'latitude'      => 'sometimes|required|/^[-]?((([0–8]?[0–9])(\.(\d{1,8}))?)|(90(\.0+)?))$/',
            'longitude'     => 'sometimes|required|/^[-]?((((1[0–7][0–9])|([0–9]?[0–9]))(\.(\d{1,8}))?)|180(\.0+)?)$/',
            'categories'    => 'required|array|min:4|max:6',
            'categories.*'  => 'required|distinct|exists:categories,id',
            'images'        => 'sometimes|required|array|min:1|max:5',
            'images.*'      => 'sometimes|required|max:1000|image|mimes:jpeg,png,gif,jpg',
        ];
    }
}
