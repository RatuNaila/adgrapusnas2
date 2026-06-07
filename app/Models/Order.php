<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // app/Models/Order.php
public function payment() { return $this->hasOne(\App\Models\Payment::class); }

}
