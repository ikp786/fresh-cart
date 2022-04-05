<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_number',
        'order_amount',
        'deliver_charge',
        'user_id',
        'driver_id',
        'txn_id',
        'payment_method',
        'payment_status',
        'order_delivery_status',
        'driver_payment_type'
    ];

    function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    function drivers()
    {
        return $this->belongsTo(User::class,'driver_id','id');
    }

    function addresses()
    {
        return $this->hasOne(Address::class,'order_id','id');
    }

    function orderProduct()
    {
        return $this->hasOne(OrderProduct::class,'order_id','id');
    }
}
