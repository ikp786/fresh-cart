<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'description',
        'status'
    ];

    public function categories()
    {
        return $this->hasOne(Category::class,'id','product_id');
    }

    public function images()
    {
        return $this->hasOne(ProductImage::class);
    }
}