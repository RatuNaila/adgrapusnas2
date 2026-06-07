<?php

namespace App\Models;

use App\Models\Book;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id','book_id','qty','price_at'];
    public function cart(): BelongsTo { return $this->belongsTo(Cart::class); }
    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
}
