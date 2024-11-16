<?php

namespace App\Http\Controllers;

use App\Models\ReportedUser;
use App\Http\Requests\StoreReportedUserRequest;
use App\Http\Requests\UpdateReportedUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportedUserRequest $request)
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
                'sale_id'       => $credentials['sale_id'],
                'to_user_id'    => $credentials['to_user_id'],
                'user_id'       => $credentials['user_id'],
            ]);

            // if(empty($isReport)){
                ReportedUser::create($credentials);

                $message    = __('Reported user');
                $response = true;
            // }else{
            //     $message    = __('Report made');
            //     $response = true;
            // }

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
    public function show(ReportedUser $reportedUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportedUserRequest $request, ReportedUser $reportedUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportedUser $reportedUser)
    {
        //
    }
}
