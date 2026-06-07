<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'judul',
        'penulis',
        'harga',
        'isbn',
        'stok',
        'kategori',
        'deskripsi',
        'foto'
    ];

    public function loans() { return $this->hasMany(\App\Models\Loan::class); }

}
