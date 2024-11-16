<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'value',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'rateable_type',
        'rateable_id',
        'updated_at',
    ];

    public function rateable()
    {
        return $this->morphTo();
    }

    public function user(){
        return $this->hasOne(User::class);
    }

    public function author(){
        return $this->belongsTo(User::class, 'user_id')->select('name', 'id');
    }
}
