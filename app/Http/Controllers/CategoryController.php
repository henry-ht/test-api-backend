<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCategoryRequest;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCategoryRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'search',
            'type',
            'items_per_page'
        ]);

        $query = Category::query();

        if (isset($credentials['type'])) {
            $query = Category::whereIn('type', $credentials['type']);
        }

        if(isset($credentials['search'])){
            $serch = '%'.$credentials['search'].'%';
            $query = $query->where('name', 'LIKE', $serch)
                                    ->orWhere('description', 'LIKE', $serch);
        }


        if (!isset($credentials['items_per_page'])) {
            $credentials['items_per_page'] = 30;
        }

        $response       = $query->paginate($credentials['items_per_page']);

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    public function getCategory()
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $response = [
            'age'               => Category::where('type' ,'age')->get(),
            'gender'            => Category::where('type' ,'gender')->get(),
            'garment_type'      => Category::where('type' ,'garment_type')->get(),
            'state_of_clothes'  => Category::where('type' ,'state_of_clothes')->get(),
            'brand'             => Category::where('type' ,'brand')->get(),
            'color'             => Category::where('type' ,'color')->get(),
        ];

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }
}
