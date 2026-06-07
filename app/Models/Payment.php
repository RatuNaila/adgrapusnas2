<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
        use HasFactory;

    protected $fillable = [
        'order_id','amount','method','status','reference','paid_at','raw_payload',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(\App\Models\Order::class); }
}
