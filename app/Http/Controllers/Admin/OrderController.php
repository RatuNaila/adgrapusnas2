<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1) Validasi dasar
        $data = $request->validate([
            'user_id'        => ['required', 'exists:users,id'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.book_id'=> ['required', 'integer', 'exists:books,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'note'           => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = (int) $data['user_id'];
        $note   = $data['note'] ?? null;
        $idk    = $request->header('Idempotency-Key'); // opsional

        // 2) Transaksi atomik + kunci stok per buku
        $result = DB::transaction(function () use ($data, $userId, $note, $idk) {
            // a) (Opsional) Idempotency: jika ada idk & order dengan idk itu sudah ada → return existing
            if ($idk) {
                $existing = DB::table('orders')->where('idempotency_key', $idk)->first();
                if ($existing) {
                    // Ambil items dan rakit response
                    $items = DB::table('order_items')->where('order_id', $existing->id)->get();
                    return ['order' => $existing, 'items' => $items, 'reused' => true];
                }
            }

            // b) Ambil & kunci semua buku yang terlibat (urutan konsisten → hindari deadlock)
            $bookIds = collect($data['items'])->pluck('book_id')->unique()->values()->all();
            sort($bookIds);

            $books = Book::whereIn('id', $bookIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // c) Cek stok dan hitung total
            $itemsPayload = [];
            $total = 0;

            foreach ($data['items'] as $row) {
                $bookId = (int) $row['book_id'];
                $qty    = (int) $row['qty'];
                $book   = $books[$bookId] ?? null;

                if (!$book) {
                    throw ValidationException::withMessages([
                        'items' => "Buku ID {$bookId} tidak ditemukan."
                    ]);
                }

                if ($book->stok < $qty) {
                    throw ValidationException::withMessages([
                        "items.{$bookId}" => "Stok buku '{$book->judul}' tidak cukup. Tersedia: {$book->stok}",
                    ]);
                }

                $priceAt  = (int) $book->harga;        // harga saat ini
                $subtotal = $priceAt * $qty;
                $total   += $subtotal;

                $itemsPayload[] = [
                    'book' => $book,
                    'qty'  => $qty,
                    'price_at' => $priceAt,
                    'subtotal' => $subtotal,
                ];
            }

            // d) Buat order
            $orderId = DB::table('orders')->insertGetId([
                'user_id'         => $userId,
                'order_code'      => $this->generateOrderCode(),
                'total'           => $total,
                'status'          => 'pending',      // sesuaikan dengan flow pembayaranmu
                'idempotency_key' => $idk,
                'note'            => $note,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // e) Insert items + kurangi stok
            foreach ($itemsPayload as $it) {
                DB::table('order_items')->insert([
                    'order_id'  => $orderId,
                    'book_id'   => $it['book']->id,
                    'qty'       => $it['qty'],
                    'price_at'  => $it['price_at'],
                    'subtotal'  => $it['subtotal'],
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);

                // stok berkurang
                $it['book']->decrement('stok', $it['qty']);
            }

            // f) Ambil kembali data order beserta items
            $order = DB::table('orders')->where('id', $orderId)->first();
            $items = DB::table('order_items')->where('order_id', $orderId)->get();

            return ['order' => $order, 'items' => $items, 'reused' => false];
        });

        // 3) Rakit response
        $order = $result['order'];
        $items = $result['items'];

        // Ambil judul & stok terbaru untuk setiap item agar respons lengkap
        $booksMap = Book::whereIn('id', $items->pluck('book_id')->all())
            ->get(['id','judul','stok'])
            ->keyBy('id');

        return response()->json([
            'message' => $result['reused']
                ? 'Order sudah ada (idempotent).'
                : 'Order berhasil dibuat.',
            'data' => [
                'order' => [
                    'id'                => $order->id,
                    'order_code'        => $order->order_code,
                    'user_id'           => $order->user_id,
                    'total'             => (int) $order->total,
                    'status'            => $order->status,
                    'payment_status'    => $order->payment_status,
                    'note'              => $order->note,
                    'paid_at'           => $order->paid_at,
                    'created_at'        => $order->created_at,
                    'updated_at'        => $order->updated_at,
                ],
                'items' => $items->map(function ($it) use ($booksMap) {
                    $b = $booksMap[$it->book_id] ?? null;
                    return [
                        'book_id'       => (int) $it->book_id,
                        'judul'         => $b?->judul,
                        'qty'           => (int) $it->qty,
                        'price_at'      => (int) $it->price_at,
                        'subtotal'      => (int) $it->subtotal,
                        'stok_terbaru'  => $b?->stok, // setelah pengurangan
                    ];
                })->values(),
            ],
        ], $result['reused'] ? 200 : 201);
    }

    protected function generateOrderCode(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }
}
