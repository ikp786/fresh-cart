<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected  $fillable = [
        'order_id',
        'product_id',        
        'total_amount',
        'product_name',
        'product_description',
        'product_quantity_phav',
        'product_quantity_half_kg',
        'product_quantity_kg',
        'product_total_quantity',
        'product_phav_amount',
        'product_half_kg_amount',
        'product_kg_amount'
    ];

    function orders()
    {
        return $this->hasOne(Order::class,'id','order_id');
    }
}
