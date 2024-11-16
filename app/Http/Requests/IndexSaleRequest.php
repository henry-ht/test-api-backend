<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSaleRequest extends FormRequest
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
            'search'                    => 'sometimes|required|string|min:3|max:100',
            'product_id'                => 'sometimes|required|numeric|exists:products,id',
            'count_relations'      => 'sometimes|required|array|min:1|max:3',
            'count_relations.*'    => 'sometimes|required|in:products,saleUser,productsWithImages,messages,readMessages',
            'with_relations'            => 'sometimes|required|array|min:1|max:3',
            'with_relations.*'          => 'sometimes|required|in:products,saleUser,productsWithImages,messages',
            'items_per_page'            => 'sometimes|required|numeric',
            'states'                    => 'sometimes|required|array|min:1',
            'states.*'                  => 'sometimes|required|in:negotiation,follow_up,pending,in_progress,on_hold,closed_lost,closed_won,cancelled,archived'
        ];
    }
}
