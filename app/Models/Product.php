<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Malhal\Geographical\Geographical;

class Product extends Model
{
    use HasFactory, Geographical, SoftDeletes;

    // public $appends = ['stateOfClothes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'quantity',
        'deleted_by',
        'longitude',
        'latitude',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'updated_at',
        'deleted_by',
        'longitude',
        'latitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id'           => 'integer',
        'price'             => 'decimal:2',
        'ratings_avg_value' => 'decimal:2',
        'quantity'          => 'integer',
        'deleted_by'        => 'integer',
        'longitude'         => 'float',
        'latitude'          => 'float',
    ];

    public function categories(){
        return $this->belongsToMany(Category::class)->withPivot('id');
    }

    // function getStateOfClothesAttribute() {
    // }

    public function sales(){
        return $this->belongsToMany(Sale::class);
    }

    public function categoryProduct(){
        return $this->hasMany(CategoryProduct::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable')->where('father_id', null)->with('children')->orderBy('created_at', 'ASC')->limit(10);
    }

    public function productSale(){
        return $this->belongsTo(ProductSale::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function author(){
        return $this->belongsTo(User::class, 'user_id')->select('name', 'id');
    }

}
