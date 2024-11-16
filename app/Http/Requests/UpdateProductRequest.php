<?php

namespace App\Http\Requests;

use App\Rules\IsHtmlRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->product->user_id == Auth::user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'                      => 'sometimes|required|string|min:3|max:250',
            'price'                     => 'sometimes|required|numeric|gte:0',
            'description'               => ['nullable','string', new IsHtmlRule()],
            'quantity'                  => 'sometimes|required|numeric|gt:0',
            'latitude'                  => 'sometimes|required|/^[-]?((([0–8]?[0–9])(\.(\d{1,8}))?)|(90(\.0+)?))$/',
            'longitude'                 => 'sometimes|required|/^[-]?((((1[0–7][0–9])|([0–9]?[0–9]))(\.(\d{1,8}))?)|180(\.0+)?)$/',
            'images'                    => 'sometimes|required|array|min:1|max:5',
            'images.*'                  => 'sometimes|required|base64image|base64mimes:jpeg,png,gif,jpg',
            'categories'                => 'sometimes|required|array|min:1|max:6',
            'categories.*'              => 'sometimes|required|distinct|exists:categories,id',
        ];
    }
}
