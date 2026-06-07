<?php

namespace App\Models;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loans extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id','user_id','qty','tanggal_pinjam','tanggal_jatuh_tempo','tanggal_kembali','catatan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_kembali' => 'datetime',
    ];

    public function book() { return $this->belongsTo(Book::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getStatusAttribute(): string {
        return $this->tanggal_kembali ? 'kembali' : 'dipinjam';
    }
}
