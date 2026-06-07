<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
   /**
     * GET /api/users/{user}/orders
     * Query:
     *  - page, per_page
     *  - q = cari judul buku
     *  - date_from=YYYY-MM-DD, date_to=YYYY-MM-DD
     *  - status=pending|paid|cancelled (opsional)
     */
    public function orders(Request $request, int $user)
    {
        $perPage   = (int) $request->integer('per_page', 10);
        $q         = trim($request->get('q', ''));
        $status    = $request->get('status');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');

        // Ambil orders user + agregasi ringkas
        $orders = DB::table('orders as o')
            ->where('o.user_id', $user)
            ->when($status, fn($qr) => $qr->where('o.status', $status))
            ->when($dateFrom, fn($qr) => $qr->whereDate('o.created_at', '>=', $dateFrom))
            ->when($dateTo, fn($qr) => $qr->whereDate('o.created_at', '<=', $dateTo))
            ->orderByDesc('o.id')
            ->paginate($perPage)
            ->appends($request->query());

        // Ambil items per order
        $itemsByOrder = DB::table('order_items as oi')
            ->select('oi.order_id','oi.book_id','oi.qty','oi.price_at','oi.subtotal','b.judul')
            ->join('books as b','b.id','=','oi.book_id')
            ->whereIn('oi.order_id', collect($orders->items())->pluck('id')->all())
            ->when($q, fn($qr) => $qr->where('b.judul','like',"%{$q}%"))
            ->get()
            ->groupBy('order_id');

        $data = collect($orders->items())->map(function ($o) use ($itemsByOrder) {
            $items = ($itemsByOrder[$o->id] ?? collect())->values();
            return [
                'kind'         => 'order',
                'id'           => $o->id,
                'total'        => (int) $o->total,
                'status'       => $o->status,
                'payment_status'=> $o->payment_status ?? null,
                'paid_at'      => $o->paid_at,
                'note'         => $o->note,
                'created_at'   => $o->created_at,
                'items'        => $items->map(fn($it) => [
                    'book_id'  => (int) $it->book_id,
                    'judul'    => $it->judul,
                    'qty'      => (int) $it->qty,
                    'price_at' => (int) $it->price_at,
                    'subtotal' => (int) $it->subtotal,
                ])->all(),
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/users/{user}/loans
     * Query:
     *  - page, per_page
     *  - status=dipinjam|kembali
     *  - q = judul buku
     *  - date_from, date_to  (berdasar created_at)
     */
    public function loans(Request $request, int $user)
    {
        $perPage  = (int) $request->integer('per_page', 10);
        $q        = trim($request->get('q', ''));
        $status   = $request->get('status'); // dipinjam|kembali
        $from     = $request->get('date_from');
        $to       = $request->get('date_to');

        $loans = Loans::query()
            ->with(['book:id,judul'])
            ->where('user_id', $user)
            ->when($status === 'dipinjam', fn($qr) => $qr->whereNull('tanggal_kembali'))
            ->when($status === 'kembali', fn($qr) => $qr->whereNotNull('tanggal_kembali'))
            ->when($q, fn($qr) => $qr->whereHas('book', fn($b) => $b->where('judul','like',"%{$q}%")))
            ->when($from, fn($qr) => $qr->whereDate('created_at','>=',$from))
            ->when($to, fn($qr) => $qr->whereDate('created_at','<=',$to))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        $data = collect($loans->items())->map(function (Loans $l) {
            return [
                'kind'               => 'loan',
                'id'                 => $l->id,
                'qty'                => (int) $l->qty,
                'status'             => $l->tanggal_kembali ? 'kembali' : 'dipinjam',
                'tanggal_pinjam'     => $l->tanggal_pinjam?->toDateString(),
                'tanggal_jatuh_tempo'=> $l->tanggal_jatuh_tempo?->toDateString(),
                'tanggal_kembali'    => $l->tanggal_kembali?->toIso8601String(),
                'catatan'            => $l->catatan,
                'created_at'         => $l->created_at?->toIso8601String(),
                'book' => [
                    'id'    => $l->book->id,
                    'judul' => $l->book->judul,
                ],
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'page' => $loans->currentPage(),
                'per_page' => $loans->perPage(),
                'total' => $loans->total(),
                'last_page' => $loans->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/users/{user}/transactions
     * Gabungan orders + loans, bisa filter:
     *  - type=orders|loans|all (default: all)
     *  - q, date_from, date_to
     *  - page, per_page
     * Pagination dilakukan di tingkat gabungan (manual paginate).
     */
    public function transactions(Request $request, int $user)
    {
        $perPage  = max(1, (int) $request->integer('per_page', 10));
        $page     = max(1, (int) $request->integer('page', 1));
        $type     = $request->get('type', 'all'); // orders|loans|all
        $q        = trim($request->get('q', ''));
        $from     = $request->get('date_from');
        $to       = $request->get('date_to');

        $collection = collect();

        if ($type === 'all' || $type === 'orders') {
            $oBase = DB::table('orders as o')
                ->where('o.user_id', $user)
                ->when($from, fn($qr) => $qr->whereDate('o.created_at','>=',$from))
                ->when($to, fn($qr) => $qr->whereDate('o.created_at','<=',$to));

            // Filter by judul via join items+books bila ada q
            if ($q !== '') {
                $oBase->join('order_items as oi','oi.order_id','=','o.id')
                      ->join('books as b','b.id','=','oi.book_id')
                      ->where('b.judul','like',"%{$q}%")
                      ->select('o.*')
                      ->distinct();
            }

            $orders = $oBase->orderByDesc('o.id')->limit(1000)->get(); // batasi agar aman

            $orderIds = $orders->pluck('id')->all();
            $items = DB::table('order_items as oi')
                ->select('oi.order_id','oi.book_id','oi.qty','oi.price_at','oi.subtotal','b.judul')
                ->join('books as b','b.id','=','oi.book_id')
                ->whereIn('oi.order_id', $orderIds)
                ->get()
                ->groupBy('order_id');

            foreach ($orders as $o) {
                $collection->push([
                    'ts'           => (string) $o->created_at,
                    'kind'         => 'order',
                    'id'           => $o->id,
                    'total'        => (int) $o->total,
                    'status'       => $o->status,
                    'payment_status'=> $o->payment_status ?? null,
                    'paid_at'      => $o->paid_at,
                    'note'         => $o->note,
                    'created_at'   => $o->created_at,
                    'items'        => ($items[$o->id] ?? collect())->values()->map(fn($it)=>[
                        'book_id'  => (int) $it->book_id,
                        'judul'    => $it->judul,
                        'qty'      => (int) $it->qty,
                        'price_at' => (int) $it->price_at,
                        'subtotal' => (int) $it->subtotal,
                    ])->all(),
                ]);
            }
        }

        if ($type === 'all' || $type === 'loans') {
            $loans = Loans::query()
                ->with(['book:id,judul'])
                ->where('user_id',$user)
                ->when($from, fn($qr)=>$qr->whereDate('created_at','>=',$from))
                ->when($to, fn($qr)=>$qr->whereDate('created_at','<=',$to))
                ->when($q !== '', fn($qr)=>$qr->whereHas('book', fn($b)=>$b->where('judul','like',"%{$q}%")))
                ->orderByDesc('id')
                ->limit(1000)
                ->get();

            foreach ($loans as $l) {
                $collection->push([
                    'ts'                 => (string) $l->created_at,
                    'kind'               => 'loan',
                    'id'                 => $l->id,
                    'qty'                => (int) $l->qty,
                    'status'             => $l->tanggal_kembali ? 'kembali' : 'dipinjam',
                    'tanggal_pinjam'     => $l->tanggal_pinjam?->toDateString(),
                    'tanggal_jatuh_tempo'=> $l->tanggal_jatuh_tempo?->toDateString(),
                    'tanggal_kembali'    => $l->tanggal_kembali?->toIso8601String(),
                    'catatan'            => $l->catatan,
                    'created_at'         => $l->created_at?->toIso8601String(),
                    'book' => [
                        'id'    => $l->book->id,
                        'judul' => $l->book->judul,
                    ],
                ]);
            }
        }

        // gabung, sort desc berdasarkan ts
        $sorted = $collection->sortByDesc('ts')->values();

        // manual paginate
        $total = $sorted->count();
        $slice = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'data' => $slice,
            'meta' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}
