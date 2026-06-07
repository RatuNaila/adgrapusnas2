<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
        public function create(Request $request, \App\Models\Order $order)
    {
        $data = $request->validate([
            'method' => ['nullable','string','max:30'],
        ]);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'Order sudah dibayar.',
                'data' => $this->serialize($order->load('payment')),
            ], 200);
        }

        $payment = DB::transaction(function () use ($order, $data) {
            // upsert 1:1 payment
            $p = Payment::firstOrNew(['order_id' => $order->id]);
            $p->fill([
                'amount' => (int) $order->total,
                'method' => $data['method'] ?? 'mock',
                'status' => 'pending',
                'reference' => $p->reference ?: 'PMT-'.str()->upper(str()->random(12)),
            ]);
            $p->save();

            return $p;
        });

        return response()->json([
            'message' => 'Payment intent dibuat.',
            'data' => [
                'order' => $this->serialize($order->fresh('payment')),
                'payment' => [
                    'id' => $payment->id,
                    'amount' => (int) $payment->amount,
                    'method' => $payment->method,
                    'status' => $payment->status,
                    'reference' => $payment->reference,
                ],
            ],
        ], 201);
    }

    /**
     * POST /api/orders/{order}/payments/confirm
     * Konfirmasi pembayaran (mock sukses).
     * Body: { "reference": "PMT-XXXX" } (opsional, hanya validasi ringan)
     */
    public function confirm(Request $request, \App\Models\Order $order)
    {
        $data = $request->validate([
            'reference' => ['nullable','string','max:100'],
        ]);

        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) {
            throw ValidationException::withMessages(['payment' => 'Tidak ada payment intent untuk order ini.']);
        }
        if ($payment->status === 'paid') {
            return response()->json([
                'message' => 'Pembayaran sudah dikonfirmasi.',
                'data' => $this->serialize($order->load('payment')),
            ], 200);
        }

        // (opsional) validasi reference harus sama
        if (!empty($data['reference']) && $payment->reference !== $data['reference']) {
            throw ValidationException::withMessages(['reference' => 'Reference tidak cocok.']);
        }

        DB::transaction(function () use ($order, $payment, $request) {
            // Tandai payment paid
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'raw_payload' => json_encode([
                    'mock' => true,
                    'by' => 'confirm endpoint',
                    'headers' => $request->headers->all(),
                ]),
            ]);

            // Tandai order paid
            DB::table('orders')->where('id', $order->id)->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Pembayaran berhasil.',
            'data' => $this->serialize($order->fresh('payment')),
        ], 200);
    }

    /**
     * POST /api/orders/{order}/payments/cancel
     * Batalkan pembayaran + (opsional) kembalikan stok (karena sebelumnya stok berkurang saat order).
     */
    public function cancel(Request $request, \App\Models\Order $order)
    {
        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment && $payment->status === 'paid') {
            return response()->json(['message' => 'Order sudah paid; tidak bisa cancel.'], 422);
        }

        DB::transaction(function () use ($order, $payment) {
            // Kembalikan stok item order (karena kita kurangi saat order dibuat)
            $items = DB::table('order_items')->where('order_id', $order->id)->get();
            foreach ($items as $it) {
                Book::where('id', $it->book_id)->lockForUpdate()->first()?->increment('stok', (int)$it->qty);
            }

            // Update order & payment
            DB::table('orders')->where('id', $order->id)->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
                'updated_at' => now(),
            ]);

            if ($payment) {
                $payment->update(['status' => 'cancelled']);
            }
        });

        return response()->json(['message' => 'Order dibatalkan & stok dikembalikan.'], 200);
    }

    private function serialize($order)
    {
        $items = DB::table('order_items')->where('order_id', $order->id)->get();
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'total' => (int) $order->total,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'paid_at' => $order->paid_at,
            'note' => $order->note ?? null,
            'items' => $items->map(fn($it) => [
                'book_id' => (int) $it->book_id,
                'qty' => (int) $it->qty,
                'price_at' => (int) $it->price_at,
                'subtotal' => (int) $it->subtotal,
            ])->values(),
            'payment' => optional($order->payment, function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => (int) $p->amount,
                    'method' => $p->method,
                    'status' => $p->status,
                    'reference' => $p->reference,
                    'paid_at' => $p->paid_at,
                ];
            }),
        ];
    }
}
