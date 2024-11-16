<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexMessageRequest extends FormRequest
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
            'message_type'      => 'required|string|in:sale',
            'message_id'        => 'required|numeric|exists:messages,messageable_id',
            'with_relations'    => 'sometimes|required|array|min:1|max:1',
            'with_relations.*'  => 'sometimes|required|in:author',
            'items_per_page'    => 'sometimes|required|numeric'
        ];
    }
}
