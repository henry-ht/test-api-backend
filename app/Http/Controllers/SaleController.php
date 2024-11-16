<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationWhen;
use App\Http\Requests\IndexSaleMessageRequest;
use App\Http\Requests\IndexSaleRequest;
use App\Http\Requests\StoreEndSaleRequest;
use App\Http\Requests\StoreReportedUserSaleRequest;
use App\Http\Requests\StoreSaleMessageRequest;
use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Message;
use App\Models\Product;
use App\Models\ReportedUser;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexSaleRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'search',
            'states',
            'product_id',
            'count_relations',
            'with_relations',
            'items_per_page'
        ]);

        if(Gate::allows('is_super_admin') || Gate::allows('is_admin') ){
            $query = Sale::query();
        }else{
            $query = Sale::where('user_id', Auth::user()->id)->orWhere('sale_user_id', Auth::user()->id);
        }

        if(isset($credentials['search'])){
            $serch = '%'.$credentials['search'].'%';
            $query = $query->where('description', 'LIKE', $serch);
        }

        if(isset($credentials['with_relations'])){
            $query = $query->with($credentials['with_relations']);
        }

        if(isset($credentials['count_relations'])){
            $countRelations = [];

            foreach ($credentials['count_relations'] as $key => $value) {
                switch ($value) {
                    case 'readMessages':
                        $countRelations["messages"] = function ($q) {
                            return $q->where('read', 0);
                        };
                        break;
                    default:
                        $countRelations[] = $value;
                        break;
                }
            }
            $query = $query->withCount($countRelations);
        }

        if(isset($credentials['product_id'])){
            $query = $query->whereHas('products', function ($q) use ($credentials) {
                return $q->where('products.id', $credentials['product_id']);
            });
        }

        if(isset($credentials['states'])){
            $query = $query->whereIn('state', $credentials['states']);
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

    /**
     * Display a listing of the resource.
     */
    public function getMessage(IndexSaleMessageRequest $request, Sale $sale)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'with_relations',
            'from_the_date',
            'items_per_page',
        ]);

        $query = Message::where([
            'messageable_type' => Sale::class,
            'messageable_id'   => $sale->id,
        ])->orderBy('created_at', 'desc');

        Message::where([
            'messageable_type'  => Sale::class,
            'messageable_id'    => $sale->id,
            'read'              => 0
        ])->update([
            'read' => true
        ]);

        if(isset($credentials['with_relations'])){
            $query = $query->with($credentials['with_relations']);
        }

        if(isset($credentials['from_the_date'])){
            $query = $query->where('created_at', '>', $credentials['from_the_date']);
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $message    = __('Save completed.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'products',
            'description',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($credentials['products'][0]);

            if($product->user_id == Auth::user()->id){
                $credentials['user_id']         = Auth::user()->id;
                $credentials['sale_user_id']    = $product->user_id;
                $credentials['state']           = 'negotiation';
                $newSale                        = Sale::create($credentials);
                $newSale->products()->attach($product->id,[
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'description'   => $product->description,
                    'sold_quantity' => $product->quantity,
                ]);

                $response = Sale::find($newSale->id);
                DB::commit();
            }else{
                $message    = __("Unauthorized");
                $status     = 'error';
                $response   = false;
                DB::rollBack();
            }
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
    public function storeMessage(StoreSaleMessageRequest $request, Sale $sale)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'message',
        ]);

        DB::beginTransaction();
        try {
            if($sale->state == "negotiation"){
                $credentials['user_id'] = Auth::user()->id;
                $userNotify = User::findOrFail(($sale->user_id == $credentials['user_id'] ? $sale->sale_user_id : $sale->user_id));

                $credentials['to_user_id']          = $userNotify->id;
                $credentials['messageable_type']    = Sale::class;
                $credentials['messageable_id']      = $sale->id;

                $new = Message::create($credentials);

                NotificationWhen::when($userNotify,[
                    "message"   => $credentials["message"],
                    "sale"      => $sale->load(["products"]),
                    "type"      => "message",
                ]);
                $response = $new->load(['author']);
                DB::commit();
            }else{
                $message    = __("You can't message.");
                $status     = 'error';
                $response   = false;
                DB::rollBack();
            }
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
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
     * Store a newly created resource in storage.
     */
    public function endSale(StoreEndSaleRequest $request, Sale $sale)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        DB::beginTransaction();
        try {
            $sale->state = 'cancelled';
            $sale->save();

            $response = true;
            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
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
     * Store a newly created resource in storage.
     */
    public function reportSale(StoreReportedUserSaleRequest $request, Sale $sale)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'description',
            'sale_id',
            'to_user_id'
        ]);

        DB::beginTransaction();
        try {
            $credentials['user_id'] = Auth::user()->id;

            $isReport = ReportedUser::where([
                'sale_id'       => $sale->id,
                'user_id'       => $credentials['user_id'],
            ])->first();

            if(empty($isReport)){
                $credentials['sale_id'] = $sale->id;
                $credentials['to_user_id'] = $sale->user_id == Auth::user()->id ? $sale->sale_user_id : $sale->user_id;
                ReportedUser::create($credentials);

                $message    = __('Reported user');
                $response = true;
            }else{
                $message    = __('Report made');
                $response = true;
            }

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
    public function show(Sale $sale, IndexSaleRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'count_relations',
            'with_relations',
        ]);

        try {
            if(isset($credentials['with_relations'])){
                $sale = $sale->load($credentials['with_relations']);
            }

            if(isset($credentials['count_relations'])){
                $countRelations = [];

                foreach ($credentials['count_relations'] as $key => $value) {
                    switch ($value) {
                        case 'readMessages':
                            $countRelations["messages"] = function ($q) {
                                return $q->where('read', 0);
                            };
                            break;
                        default:
                            $countRelations[] = $value;
                            break;
                    }
                }
                $sale = $sale->loadCount($countRelations);
            }

            $response = $sale;
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
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        $message    = __('Update completed.');
        $status     = 'success';
        $notify     = true;
        $response   = false;
        $isUpdateState = false;

        $credentials = $request->only([
            'state',
        ]);

        DB::beginTransaction();
        try {

            if($sale->state === "negotiation"){
                $isUpdateState = true;
            }

            if($isUpdateState){
                $sale->fill($credentials)->save();
                $response = $sale->load(['products']);
            }else {
                $message    = __('Not possible to update.');
                $status     = 'error';
            }
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
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $message    = __('Removed.');
        $status     = 'success';
        $notify     = true;

        DB::beginTransaction();
        try {
            if($sale->user_id == Auth::user()->id){

                $response = $sale->delete();

                $message    = __('Successful');
                $status     = 'success';
                $response   = true;

            }else{
                $message    = __('Invalid delete');
                $status     = 'warning';
                $response   = false;
            }
            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('oops!!, not removed, try again or contact with a admin.');
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
}
