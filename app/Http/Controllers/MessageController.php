<?php

namespace App\Http\Controllers;

use App\Enums\PriorityEnum;
use App\Enums\TypeNotificationEnum;
use App\Helpers\NotificationSave;
use App\Http\Requests\IndexMessageRequest;
use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexMessageRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'message_type',
            'message_id',
            'with_relations',
            'items_per_page'
        ]);

        switch ($credentials['message_type']) {
            case 'sale':
                $credentials['message_type'] = Sale::class;
                break;
        }

        $query = Message::where([
            'messageable_type' => $credentials['message_type'],
            'messageable_id'   => $credentials['message_id'],
        ])->orderBy('created_at', 'desc');

        if(isset($credentials['with_relations'])){
            $query = $query->with($credentials['with_relations']);
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
    public function store(StoreMessageRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'message',
            'message_type',
            'message_id'
        ]);

        try {
            $toComment = true;
            switch ($credentials['message_type']) {
                case 'sales':
                    $credentials['messageable_type'] = Sale::class;
                    $fromModel = Sale::where('uuid', $credentials['message_id'])->firstOrFail();
                    if($fromModel->state != "negotiation"){
                        $toComment = false;
                    }
                    break;
            }

            if($toComment){
                $credentials['messageable_id'] = $fromModel->id;
                $credentials['user_id'] = Auth::user()->id;

                $countMessage = Message::where([
                    "messageable_type"  => $credentials['messageable_type'],
                    "messageable_id"    => $credentials['messageable_id'],
                ])->count();

                $userNotify = User::findOrFail(($fromModel->user_id == $credentials['user_id'] ? $fromModel->user_id : $fromModel->sale_user_id));


                $new = Message::create($credentials);
                $response = $new;
            }else{
                $message    = __("You can't message.");
                $status     = 'success';
                $response   = false;
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
}
