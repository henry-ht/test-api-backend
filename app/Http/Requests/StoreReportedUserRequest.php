<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReportedUserRequest extends FormRequest
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
            'description'   => 'nullable|string|min:3|max:250',
            'sale_id'       => 'required|'.(is_numeric($this->sale_id) ? 'integer' : 'uuid').'|exists:sales,'.(is_numeric($this->sale_id) ? 'id' : 'uuid'),
            'to_user_id'    => 'required|integer|exists:users,id',
        ];
    }
}
