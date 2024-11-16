<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRegisterRequest;
use App\Http\Requests\StorePasswordResetRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function register(PostRegisterRequest $request){
        $message    = __('Whoops! Something went wrong.');
        $status     = 'warning';
        $response   = false;
        $notify     = true;

        $credentials = $request->only([
            'name',
            'email',
            'identification_number',
            'phone',
            'birthday',
            'accept_terms',
            // 'role_id',
            'password',
            // 'client_secret',
            // 'client_id',
            // 'password_confirmation',
            // ''
        ]);

        DB::beginTransaction();
        try {
            $role = Role::where('name', 'app_user')->first();
            $password = base64_decode($credentials['password']);
            $credentials['role_id']     = $role->id;
            $credentials['name']        = Str::lower($credentials['name']);
            $credentials['password']    = Hash::make($password);

            $newUser   = User::create($credentials);

            if (!$newUser->hasVerifiedEmail()) {
                $newUser->markEmailAsVerified();
            }

            $message    = __('Successful registration');
            $status     = 'success';
            $response   = true;

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
            'notify'    => $notify,
        ],200);

    }

    public function destroy(){
        $message    = __('Logoff complete');
        $status     = 'success';
        $data       = false;
        $notify     = true;

        DB::beginTransaction();
        try {
            $user   = Auth::user();
            $tokens =  $user->tokens->pluck('id');
            Token::whereIn('id', $tokens)->update(['revoked'=> true]);
            RefreshToken::whereIn('access_token_id', $tokens)->update(['revoked' => true]);

            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
            $status     = 'error';
            $data       = false;
            DB::rollBack();
        }

        return response([
            'status'    => $status,
            'notify'    => $notify,
            'data'      => $data,
            'message'   => $message,
        ],200);
    }

    function startChangePw(StorePasswordResetRequest $request) {
        $message    = __('Reset Password');
        $status     = 'success';
        $data       = false;
        $notify     = true;

        $credentials = $request->only([
            'email',
        ]);

        DB::beginTransaction();
        try {
            $sendResetLink =  Password::sendResetLink($credentials);

            switch ($sendResetLink) {
                case Password::RESET_LINK_SENT:
                    $message    = ['message' => [__('Complete the process by clicking on the confirmation email.')]];
                    $status     = 'success';
                    break;

                case Password::INVALID_USER:
                    $message    = ['message' => [__(':item not found.', 'User')]];
                    $status     = 'error';
                    break;

                case Password::RESET_THROTTLED:
                    $message    = ['message' => [__('Wait 60 minutes to request a new password change')]];
                    $status     = 'error';
                    break;
            }

            $data       = $sendResetLink;
            DB::commit();
        } catch (\Throwable $th) {
            $message    = __('Ops! Try again or contact an admin.');
            $status     = 'error';
            $data       = false;
            DB::rollBack();
        }

        return response([
            'status'    => $status,
            'notify'    => $notify,
            'data'      => $data,
            'message'   => $message,
        ],200);
    }

}
