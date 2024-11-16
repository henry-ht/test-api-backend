<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShowProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->product->user_id == Auth::user()->id || (Gate::allows('is_super_admin') || Gate::allows('is_admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'with_relations'    => 'sometimes|required|array|min:1|max:4',
            'with_relations.*'  => 'sometimes|required|in:categories,images,questions,sale',
        ];
    }
}
