<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'father_id',
        'deleted_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'father_id'     => 'integer',
        'deleted_by'    => 'integer',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function categoryProduct(){
        return $this->hasMany(CategoryProduct::class);
    }

    public function category()
    {
        return $this->hasMany(Category::class, 'father_id');
    }

    public function children()
    {
        return $this->category()->with('children');
    }
}
