<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',        
        'product_name',        
        'product_quantity_kg',
        'product_quantity_half_kg',
        'product_quantity_phav',
        'product_total_quantity',
        'product_phav_amount',
        'product_half_kg_amount',
        'product_kg_amount',
        'total_amount'
    ];
}