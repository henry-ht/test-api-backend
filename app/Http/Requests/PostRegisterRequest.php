<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class PostRegisterRequest extends FormRequest
{
    /**
     * Indicates whether validation should stop after the first rule failure.
     *
     * @var bool
     */

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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|min:5|max:120',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:7|string|confirmed',
            'grant_type'    => 'required|string|in:register_user',
            'client_secret' => 'required|exists:oauth_clients,secret',
            'client_id'     => 'required|exists:oauth_clients,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        foreach ($validator->errors()->keys() as $key => $value) {
            switch ($value) {
                case 'client_secret':
                case 'grant_type':
                case 'client_id':
                    throw new HttpResponseException(response()->json([
                                'status'    => 'error',
                                'message'   => __('It seems that your data is not correct, please try again'),
                                'data'      => false,
                            ], 422));
                    break;

                default:
                    throw (new ValidationException($validator))
                                ->errorBag($this->errorBag)
                                ->redirectTo($this->getRedirectUrl());
                    break;
            }
        }
    }
}
