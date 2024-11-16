<?php

namespace App\Http\Controllers;

use App\Helpers\ImagesManager;
use App\Http\Requests\IndexProductRequest;
use App\Http\Requests\ShowProductRequest;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreRatingProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Jobs\ResizeImageJob;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'search',
            'max_price',
            'min_price',
            'with_relations',
            'latitude',
            'longitude',
            'catagory_ids',
            'items_per_page',
            'order_by'
        ]);

        if(Gate::allows('is_super_admin') || Gate::allows('is_admin') ){
            $query = Product::query();
        }else{
            $query = Product::where('user_id', Auth::user()->id);
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

        if(isset($credentials['order_by'])){
            foreach ($credentials['order_by'] as $key => $value) {
                $query = $query->orderBy($value['column'], $value['side']);
            }
        }

        if (!isset($credentials['items_per_page'])) {
            $credentials['items_per_page'] = 30;
        }

        $response   = $query->paginate($credentials['items_per_page']);

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'name',
            'price',
            'description',
            'quantity',
            'longitude',
            'latitude',
            'images',
            'categories'
        ]);

        DB::beginTransaction();
        try {
            $credentials['user_id'] = Auth::user()->id;
            $newProduct = Product::create($credentials);

            $newProduct->categories()->attach($credentials['categories']);

            if(isset($credentials['images'])){
                foreach ($credentials['images'] as $key => $value) {
                    $imgName = ImagesManager::publicSave($value, 'product-'.$newProduct->id, 'products');

                    ResizeImageJob::dispatch(storage_path('app/'.$imgName['path']), storage_path('app/public/products/'.basename($imgName['path'])), null, null, storage_path('app/public/products'), false);

                    ResizeImageJob::dispatch(storage_path('app/'.$imgName['path']), storage_path('app/public/products/640x480/'.basename($imgName['path'])), 640, 480, storage_path('app/public/products/640x480'));

                    $newProduct->images()->create([
                        'resized'       => 'storage/products/640x480/'.basename($imgName['public_path']),
                        'authentic'     => $imgName['public_path'],
                        'path'          => $imgName['path'],
                        'resized_path'  => 'public/products/640x480/'.basename($imgName['public_path']),
                        'order'         => $key+1,
                    ]);
                }
            }

            $response = $newProduct->load(['categories']);
            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
            $status     = 'error';
            $response   = false;
            DB::rollBack();
        }

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function ratingProduct(Product $product, StoreRatingProductRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'value',
        ]);

        DB::beginTransaction();
        try {
            $credentials['user_id'] = Auth::user()->id;
            $product->ratings()->updateOrCreate([
                "rateable_type" => Product::class,
                "rateable_id"   => $product->id,
                "user_id"       => $credentials['user_id'],
            ], [
                "value" => $credentials['value']
            ]);
            $response = true;
            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
            $status     = 'error';
            $response   = false;
            DB::rollBack();
        }

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, ShowProductRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'with_relations',
        ]);

        try {

            if(isset($credentials['with_relations'])){
                $product = $product->load($credentials['with_relations']);
            }

            $response = $product;
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
            $status     = 'error';
            $notify     = true;
            $response   = false;
        }

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $message    = __('updated.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'name',
            'price',
            'description',
            'quantity',
            'longitude',
            'latitude',
            'images',
            'categories'
        ]);
        DB::beginTransaction();
        try {
            $product->fill($credentials)->save();

            if(isset($credentials['categories'])){
                $product->categories()->detach();
                $product->categories()->attach($credentials['categories']);
            }

            if(isset($credentials['images'])){
                foreach ($credentials['images'] as $key => $value) {
                    $imgName = ImagesManager::publicSave($value, 'product-'.$product->id, 'products');

                    ResizeImageJob::dispatch(storage_path('app/'.$imgName['path']), storage_path('app/public/products/'.basename($imgName['path'])), 1280, 720, storage_path('app/public/products'), false);

                    ResizeImageJob::dispatch(storage_path('app/'.$imgName['path']), storage_path('app/public/products/640x480/'.basename($imgName['path'])), 640, 480, storage_path('app/public/products/640x480'));

                    $product->images()->create([
                        'resized'       => 'storage/products/640x480/'.basename($imgName['public_path']),
                        'authentic'     => $imgName['public_path'],
                        'path'          => $imgName['path'],
                        'resized_path'  => 'public/products/640x480/'.basename($imgName['public_path']),
                        'order'         => $key+1,
                    ]);
                }
                foreach ($product->images()->orderBy('id', "ASC")->get() as $key => $value) {
                    $value->order = $key+1;
                    $value->save();
                }
            }

            $response = $product->load(['images', 'categories']);
            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('oops!!, not updated, try again or contact with a admin.');
            $status     = 'error';
            $response   = false;
            DB::rollBack();
        }

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $message    = __('Removed.');
        $status     = 'success';
        $notify     = true;

        try {
            if($product->user_id == Auth::user()->id){
                foreach ($product->images()->get() as $key => $image) {
                    ImagesManager::deleteImg(basename($image->path), 'products');
                    ImagesManager::deleteImg(basename($image->path), 'products/640x480');
                    $image->delete();
                }

                $response = $product->delete();

                $message    = __('Successful');
                $status     = 'success';
                $response   = true;

            }else{
                $message    = __('Invalid delete');
                $status     = 'warning';
                $response   = false;
            }
        } catch (\Throwable $th) {
            $message    = __('oops!!, not removed, try again or contact with a admin.');
            $status     = 'error';
            $response   = false;
        }

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyImage(Product $product, Image $image)
    {
        $message    = __('Removed.');
        $status     = 'success';
        $notify     = true;

        try {
            if($product->user_id == Auth::user()->id){
                $product->images()->where('images.id', $image->id)->firstOrFail();

                ImagesManager::deleteImg(basename($image->path), 'products');
                ImagesManager::deleteImg(basename($image->path), 'products/640x480');

                $image->delete();

                $message    = __('Successful');
                $status     = 'success';
                $response   = true;

            }else{
                $message    = __('Invalid delete');
                $status     = 'warning';
                $response   = false;
            }
        } catch (\Throwable $th) {
            $message    = __('oops!!, not removed, try again or contact with a admin.');
            $status     = 'error';
            $response   = false;
        }

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }
}
