<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'name',
        'mobile',
        'email',
        'pincode',
        'address'
    ];
}
