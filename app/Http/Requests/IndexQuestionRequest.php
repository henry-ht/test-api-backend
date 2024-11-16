<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexQuestionRequest extends FormRequest
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
            'question_type'     => 'required_with:question_id|string|in:products',
            'question_id'       => 'required_with:question_type|numeric|exists:questions,questionable_id',
            'items_per_page'    => 'sometimes|required|numeric'
        ];
    }
}
