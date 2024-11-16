<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'description',
        'user_id',
        'sale_user_id',
        'state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'id',
        // 'sale_user_id',
        'updated_at',
        // 'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid'          => 'string',
        'user_id'       => 'integer',
        'sale_user_id'   => 'integer',
    ];

    public function products(){
        return $this->belongsToMany(Product::class);
    }

    public function productsWithImages(){
        return $this->belongsToMany(Product::class)->with('images');
    }

    public function productSale(){
        return $this->belongsTo(ProductSale::class);
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable')->with('user')->orderBy('created_at', "ASC");
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return is_numeric($value)
            ? $this->where('id', $value)->firstOrFail()
            : $this->where('uuid', $value)->firstOrFail();
    }

    public function user(){
        return $this->hasOne(User::class)->select('name', 'id', "created_at");
    }

    public function saleUser(){
        return $this->belongsTo(User::class, 'sale_user_id')->select('name', 'id', "created_at");
    }


}
