<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $appends = [
        'authentic_url',
        'resize_url'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'resized',
        'authentic',
        'path',
        'resized_path',
        'order'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'imageable_type',
        'imageable_id',
        'path',
        'resized_path',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getAuthenticUrlAttribute()
    {
        return asset($this->authentic);
    }

    public function getResizeUrlAttribute()
    {
        return asset($this->resized);
    }
}
