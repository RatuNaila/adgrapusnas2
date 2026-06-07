<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
     public function show(Request $request)
    {
        $cart = $this->resolveCart($request->user_id); // auth only
        return $this->cartResponse($cart);
    }

    public function addItem(Request $request)
    {
        $data = $request->validate([
            'book_id' => ['required','exists:books,id'],
            'qty'     => ['required','integer','min:1'],
        ]);

        $cart = $this->resolveCart($request->user_id);
        $book = Book::findOrFail($data['book_id']);

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'book_id' => $book->id,
        ]);

        if ($item->exists) {
            $item->qty += (int) $data['qty'];
        } else {
            $item->qty = (int) $data['qty'];
            $item->price_at = (int) $book->harga; // snapshot harga
        }

        $item->save();
        return $this->cartResponse($cart);
    }

    public function updateItem(Request $request, CartItem $item)
    {
        $cart = $this->resolveCart($request->user_id);
        if ($item->cart_id !== $cart->id) return response()->json(['message'=>'Item tidak ditemukan'], 404);

        $data = $request->validate(['qty' => ['required','integer','min:1']]);
        $item->qty = (int) $data['qty'];
        $item->save();

        return $this->cartResponse($cart);
    }

    public function removeItem(Request $request, CartItem $item)
    {
        $cart = $this->resolveCart($request->user_id);
        if ($item->cart_id !== $cart->id) return response()->json(['message'=>'Item tidak ditemukan'], 404);

        $item->delete();
        return $this->cartResponse($cart);
    }

    public function clear(Request $request)
    {
        $cart = $this->resolveCart($request->user_id);
        $cart->items()->delete();
        return $this->cartResponse($cart);
    }

    // ================= Helpers =================
    protected function resolveCart($user): Cart
    {
        $userId = $user;
        abort_if(!$userId, 401, 'Unauthenticated.');
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    protected function cartResponse(Cart $cart)
    {
        $cart->load(['items.book:id,judul,penulis,foto,isbn,harga']);

        $items = $cart->items->map(function (CartItem $it) {
            $subtotal = (int)($it->price_at ?? 0) * (int)$it->qty;
            return [
                'id'       => $it->id,
                'book_id'  => $it->book_id,
                'qty'      => (int) $it->qty,
                'price_at' => (int) ($it->price_at ?? 0),
                'subtotal' => $subtotal,
                'book'     => [
                    'judul'   => $it->book->judul ?? null,
                    'penulis' => $it->book->penulis ?? null,
                    'isbn'    => $it->book->isbn ?? null,
                    'foto'    => $it->book->foto ?? null,
                    'harga'   => (int) ($it->book->harga ?? 0),
                ],
            ];
        });

        return response()->json([
            'cart' => [
                'id'      => $cart->id,
                'user_id' => $cart->user_id,
                'note'    => $cart->note,
                'items'   => $items,
                'total'   => (int) $items->sum('subtotal'),
            ]
        ]);
    }
}
