<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicProductRequest;
use App\Http\Requests\PublicProfileRequest;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Support\Facades\Route;

class PublicController extends Controller
{
    public function products(PublicProductRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'id',
            'search',
            'max_price',
            'min_price',
            'with_relations',
            'count_relations',
            'with_avg',
            'latitude',
            'longitude',
            'catagory_ids',
            'items_per_page',
            'order_by',
            'seller_id'
        ]);


        $query = Product::query();

        if(isset($credentials['id'])){
            $query = $query->where("id", $credentials['id']);
        }

        if(isset($credentials['seller_id'])){
            $query = $query->where("user_id", $credentials['seller_id']);
        }

        if(isset($credentials['search'])){
            $arraySearch = preg_split('/\s+/', $credentials['search']);

            foreach ($arraySearch as $key => $value) {
                $query = $query->where('name', 'LIKE', '%'.$value.'%');
            }
        }

        if(isset($credentials['max_price']) && isset($credentials['min_price'])){
            $query = $query->whereBetween('price', [
                $credentials['min_price'],
                $credentials['max_price'],
            ]);
        }

        if(isset($credentials['catagory_ids'])){
            $query = $query->whereHas('categories', function ($q) use ($credentials) {
                return $q->whereIn('categories.id', $credentials['catagory_ids']);
            });
        }

        if(isset($credentials['with_relations'])){
            $query = $query->with($credentials['with_relations']);
        }

        if(isset($credentials['count_relations'])){
            $countRelations = $credentials['count_relations'];
            $query = $query->withCount($countRelations);
        }

        if(isset($credentials['with_avg'])){
            $query = $query->withAvg("ratings", "value");
        }

        if(isset($credentials['order_by'])){
            foreach ($credentials['order_by'] as $key => $value) {
                $query = $query->orderBy($value['column'], $value['side']);
            }
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

    public function profile(PublicProfileRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'id',
        ]);

        $response = User::where('id', $credentials['id'])
                            ->with(['products'])
                            ->withCount(['products'])
                            ->first();

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }
}
