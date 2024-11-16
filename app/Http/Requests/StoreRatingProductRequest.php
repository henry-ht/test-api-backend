<?php

namespace App\Http\Requests;

use App\Enums\SaleStateEnum;
use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreRatingProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Sale::where('user_id', Auth::user()->id)
                    ->where("state", 'closed_won')
                    ->whereHas('products', function ($q) {
                        return $q->where('product_sale.product_id', $this->product->id);
                    })
                    ->count();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'value' => 'required|numeric|between:0,5'
        ];
    }
}
