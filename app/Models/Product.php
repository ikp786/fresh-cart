<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'freshfromthefarm',
        'pav_price',
        'half_kg_price',
        'kg_price',
        'category_id',
        'description',
        'status'
    ];

    public function categories()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function images()
    {
        return $this->hasOne(ProductImage::class);
    }

    public function allImages()
    {
        return $this->hasMany(ProductImage::class);
    }
}