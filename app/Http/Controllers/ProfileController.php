<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\GetNotificationRequest;
use App\Http\Requests\GetProfileRequest;
use App\Http\Requests\NotificationsMarkAsReadRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Notifications\MessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(GetProfileRequest $request){
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'items_per_page'
        ]);


        // event(new NotifyMe(User::find(Auth::user()->id)));
        // User::where('id', Auth::user()->id)->first()->notify(new MessageNotification('Someone'));

        $response = Auth::user()->load(['role']);

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    public function notification(GetNotificationRequest $request) {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $credentials = $request->only([
            'items_per_page'
        ]);

        $userSesion = Auth::user();

        if (!isset($credentials['items_per_page'])) {
            $credentials['items_per_page'] = 30;
        }

        $response  = [
            "unRead"        => $userSesion->unreadNotifications->count(),
            "notifications" => $userSesion->notifications()->limit($credentials['items_per_page'])->get()
        ];

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    public function notificationCountUnRead() {
        $message    = __('Done.');
        $status     = 'success';
        $notify     = false;

        $userSesion = Auth::user();

        $response  = $userSesion->unreadNotifications->count();

        return response([
            'data'      => $response,
            'status'    => $status,
            'message'   => $message,
            'notify'    => $notify
        ],200);
    }

    function notificationMarkAsRead($id)  {
        $message    = __('Marked as read');
        $status     = 'success';
        $notify     = false;

        try {
            $userSesion = Auth::user();
            $notification = $userSesion->notifications->find($id);
            if(isset($notification)){
                $notification->markAsRead();
                $response  = true;
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

    function notificationsMarkAsRead()  {
        $message    = __('Marked as read');
        $status     = 'success';
        $notify     = false;

        try {
            $userSesion = Auth::user();
            $userSesion->notifications->markAsRead();
            $response  = true;

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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UpdateProfileRequest $request)
    {
        $message    = __('updated.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'name',
            'password_verification'
        ]);

        if (Hash::check(base64_decode($credentials['password_verification']), $user->password)) {
            unset($credentials['password_verification']);

            $user->fill($credentials)->save();

            $message    = __('Successful');
            $status     = 'success';
            $response   = $user;

        }else{
            $message    = __('Invalid credentials');
            $status     = 'warning';
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
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyUserRequest $request, User $user)
    {
        $message    = __('delete.');
        $status     = 'success';
        $notify     = true;

        $credentials = $request->only([
            'password_verification'
        ]);

        if (Hash::check(base64_decode($credentials['password_verification']), $user->password)) {
            unset($credentials['password_verification']);

            $user->delete();
            $message    = __('Successful');
            $status     = 'success';
            $response   = true;

        }else{
            $message    = __('Invalid credentials');
            $status     = 'warning';
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
