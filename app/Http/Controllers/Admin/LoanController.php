<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'qty' => ['required','integer','min:1'],
            'catatan' => ['nullable','string','max:1000'],
        ]);


        $userId = $request->user_id;
        $defaultDays = 7; // lama pinjam default (ubah sesuai kebijakan)
        $today = now()->toDateString();
        $due   = now()->addDays($defaultDays)->toDateString();

        DB::transaction(function () use ($book, $userId, $data, $today, $due) {
            // tidak boleh punya pinjaman aktif buku yang sama
            $exist = Loans::where('book_id', $book->id)
                ->where('user_id', $userId)
                ->whereNull('tanggal_kembali')
                ->lockForUpdate()
                ->exists();
            if ($exist) {
                throw ValidationException::withMessages([
                    'qty' => 'Kamu masih memiliki pinjaman aktif untuk buku ini.'
                ]);
            }

            // cek & kurangi stok
            $b = Book::whereKey($book->id)->lockForUpdate()->first();
            if ($b->stok < $data['qty']) {
                throw ValidationException::withMessages([
                    'qty' => "Stok tidak cukup. Stok tersedia: {$b->stok}",
                ]);
            }
            $b->decrement('stok', $data['qty']);

            // buat loan
            Loans::create([
                'book_id' => $b->id,
                'user_id' => $userId,
                'qty' => $data['qty'],
                'tanggal_pinjam' => $today,
                'tanggal_jatuh_tempo' => $due,
                'catatan' => $data['catatan'] ?? null,
            ]);
        });

       return response()->json(['message' => 'Loan created successfully'], 200);
    }
}
