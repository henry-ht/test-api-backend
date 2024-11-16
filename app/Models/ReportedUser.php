<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'sale_id',
        'to_user_id',
        'description',
        'state'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'sale_user_id',
        'updated_at',
        // 'user_id',
    ];

    public function user(){
        return $this->hasOne(User::class)->select('name', 'id', "created_at");
    }

    public function reportUser(){
        return $this->belongsTo(User::class, 'to_user_id')->select('name', 'id', "created_at");
    }
}
