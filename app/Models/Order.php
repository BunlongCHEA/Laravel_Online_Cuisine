<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'cuisine_id',
        'price',
        'quantity',
        'subtotal',
        'userOrder'
    ];
}
