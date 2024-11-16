<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
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
            'max_price'         => 'required_with:min_price|numeric',
            'min_price'         => 'required_with:max_price|numeric',
            'with_avg'          => 'sometimes|required|array|min:1|max:4',
            'with_avg.*'        => 'sometimes|required|in:sumRatings',
            'with_relations'    => 'sometimes|required|array|min:1|max:4',
            'with_relations.*'  => 'sometimes|required|in:categories,images,questions,author',
            'count_relations'   => 'sometimes|required|array|min:1|max:3',
            'count_relations.*' => 'sometimes|required|in:categories,images,questions,sale,ratings',
            'latitude'          => 'sometimes|required|/^[-]?((([0–8]?[0–9])(\.(\d{1,8}))?)|(90(\.0+)?))$/',
            'longitude'         => 'sometimes|required|/^[-]?((((1[0–7][0–9])|([0–9]?[0–9]))(\.(\d{1,8}))?)|180(\.0+)?)$/',
            'catagory_ids'      => 'sometimes|required|array|max:5',
            'catagory_ids.*'    => 'required|exists:categories,id',
            'order_by'          => 'sometimes|required|array|min:1',
            'order_by.*.column' => 'required|in:name,price,created_at',
            'order_by.*.side'   => 'required|in:DESC,ASC',
            'only_mine'         => 'sometimes|required|in:1',
            'items_per_page'    => 'sometimes|required|numeric'
        ];
    }
}
