<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\UserNotification;
use Carbon\Carbon;

class NotificationWhen{

    static function when(User $userNotify, Array $data) : bool {
        $response = false;
        $notify = $userNotify->unreadNotifications->where([
            'data.type'     => $data["type"],
            'data.user_id'  => $userNotify->id,
            'data.sale_id'  => $data["sale"]->id,
        ])->where('created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString());

        if(count($notify) == 0 || count($notify) > 5){
            $userNotify->notify(new UserNotification($data, $userNotify));
            $response = true;
        }

        return $response;
    }
}
