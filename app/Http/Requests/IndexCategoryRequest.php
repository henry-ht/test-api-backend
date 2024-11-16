<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
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
            'search'            => 'sometimes|required|string|min:3|max:100',
            'type'              => 'sometimes|required|array|min:1|max:6',
            'type.*'            => 'required|string|in:color,age,gender,garment type,state of clothes,brand',
            'items_per_page'    => 'sometimes|required|numeric'

        ];
    }
}
