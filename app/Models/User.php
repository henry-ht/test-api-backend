<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Broadcasting\PrivateChannel;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // protected $appends = ['days'];

    protected $bitPermission = 9;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'email_verified_at',
        'updated_at',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The channels the user receives notification broadcasts on.
     */
    // public function receivesBroadcastNotificationsOn(): string
    // {
    //     return "notification";
    // }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    public function reportUser(){
        return $this->hasMany(ReportedUser::class);
    }

    // public function getDaysAttribute(){
    //     return $this->created_at->diffInDays(Carbon::now());
    // }
}
