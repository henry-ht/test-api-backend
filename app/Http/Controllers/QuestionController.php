<?php

namespace App\Http\Controllers;

use App\Enums\PriorityEnum;
use App\Enums\TypeNotificationEnum;
use App\Helpers\NotificationSave;
use App\Http\Requests\IndexQuestionRequest;
use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Product;
use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexQuestionRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'question_type',
            'question_id',
            'items_per_page'
        ]);

        if(Gate::allows('is_super_admin') || Gate::allows('is_admin') ){
            $query = Question::where('father_id', null)->with(['children', 'product' => function ($q) {
                $q->with(['images']);
            }])->orderBy('created_at', 'DESC');
        }else{
            $query = Question::where('father_id', null)
                                ->whereHas('product', function ($q) {
                                    $q->where('user_id', Auth::user()->id);
                                })->with(['children', 'product' => function ($q) {
                                    $q->with(['images']);
                                }])->orderBy('created_at', 'DESC');
        }

        if(isset($credentials['question_type']) && isset($credentials['question_id'])){
            switch ($credentials['question_type']) {
                case 'products':
                    $credentials['question_type'] = Product::class;
                    break;
            }

            $query = $query->where([
                'questionable_type' => $credentials['question_type'],
                'questionable_id'   => $credentials['question_id'],
            ]);
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
    public function store(StoreQuestionRequest $request)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'comment',
            'question_type',
            'question_id'
        ]);

        // try {
            switch ($credentials['question_type']) {
                case 'products':
                    $credentials['questionable_type'] = Product::class;
                    $fromModel = Product::findOrFail($credentials['question_id']);
                    break;
            }
            $credentials['questionable_id'] = $fromModel->id;
            $credentials['user_id'] = Auth::user()->id;

            $userNotify = User::findOrFail($fromModel->user_id);

            $newQuestion = Question::create($credentials);

            NotificationSave::save($userNotify, array(
                "title"     => __('You have a question'),
                "message"   => __('A user has a question regarding your publication.').' '.$fromModel->name,
                "icon"      => null,
                "modelId"   => $fromModel->id,
                "action"    => "Question",
                "type"      => TypeNotificationEnum::Question,
            ), 'notifications');

            $response = $newQuestion->load(['children', 'product']);
        // } catch (\Throwable $th) {
        //     $message    = __('Ops! Try again or contact an admin.');
        //     $status     = 'error';
        //     $response   = false;
        // }

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
    public function storeAnswer(StoreQuestionRequest $request, Question $question)
    {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'comment',
            'question_type',
            'question_id'
        ]);

        try {
            switch ($credentials['question_type']) {
                case 'products':
                    $credentials['questionable_type'] = Product::class;
                    $fromModel = Product::findOrFail($credentials['question_id']);
                    break;
            }

            $credentials['questionable_id'] = $fromModel->id;
            $credentials['user_id'] = Auth::user()->id;
            $credentials['father_id'] = $question->id;

            if($fromModel->user_id == Auth::user()->id && $question->loadCount(['children'])->children_count == 0){

                $userNotify = User::findOrFail($question->user_id);

                Question::create($credentials);

                NotificationSave::save($userNotify, array(
                    "title"     => __('You were answered'),
                    "message"   => __('Of the publication: :name', [ 'name' => $fromModel->name]),
                    "icon"      => null,
                    "modelId"   => $fromModel->id,
                    "modelType" => Question::class,
                    "action"    => "Answer",
                ));

                $response = $question->load(['children', 'product']);
            }else{
                $message    = __('Unauthorized');
                $status     = 'error';
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

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateQuestionRequest $request, Question $question)
    // {
    //     $message    = __('updated.');
    //     $status     = 'success';
    //     $notify     = true;

    //     $credentials = $request->only([
    //         'comment',
    //     ]);

    //     try {
    //         $question->fill($credentials)->save();

    //     } catch (\Throwable $th) {
    //         $message    = __('oops!!, not updated, try again or contact with a admin.');
    //         $status     = 'error';
    //         $response   = false;
    //     }

    //     return response([
    //         'data'      => $response,
    //         'status'    => $status,
    //         'message'   => $message,
    //         'notify'    => $notify
    //     ],200);
    // }
}
