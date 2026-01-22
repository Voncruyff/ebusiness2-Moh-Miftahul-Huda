<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    private string $cookieKey = 'snv_cart';

    private function cartGet(Request $request): array
    {
        // prioritas session (kalau ada)
        $cart = session()->get('cart');
        if (is_array($cart)) return $cart;

        // fallback cookie
        $raw = $request->cookie($this->cookieKey);
        if (!$raw) return [];

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function cartSave(array $cart)
    {
        // simpan ke session (biar cepat dipakai)
        session()->put('cart', $cart);

        // simpan ke cookie (biar gak hilang saat logout)
        // 30 hari = 60*24*30 menit
        Cookie::queue($this->cookieKey, json_encode($cart), 60 * 24 * 30);
    }

    public function index(Request $request)
    {
        $cart = $this->cartGet($request);
        return view('dashboardUser.keranjang', compact('cart'));
    }

    public function add(Request $request)
    {
        $cart = $this->cartGet($request);
        $id = (string) $request->id;

        $stock = (int) ($request->stock ?? 0);

        if ($stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok habis!',
            ], 422);
        }

        if (isset($cart[$id])) {
            if (($cart[$id]['qty'] ?? 0) >= $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Qty melebihi stok!',
                ], 422);
            }

            $cart[$id]['qty']++;

            if (empty($cart[$id]['image']) && !empty($request->image)) $cart[$id]['image'] = $request->image;
            if (empty($cart[$id]['unit'])  && !empty($request->unit))  $cart[$id]['unit']  = $request->unit;
            if (empty($cart[$id]['stock']) && $stock > 0)              $cart[$id]['stock'] = $stock;

        } else {
            $cart[$id] = [
                'id'    => $id,
                'name'  => $request->name,
                'price' => (int) $request->price,
                'qty'   => 1,
                'image' => $request->image,
                'unit'  => $request->unit,
                'stock' => $stock,
            ];
        }

        $this->cartSave($cart);

        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
        ]);
    }

    public function update(Request $request)
    {
        $cart = $this->cartGet($request);
        $id = (string) $request->id;

        if (!isset($cart[$id])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $qty = (int) $request->qty;
        if ($qty < 1) $qty = 1;

        $stock = (int) ($cart[$id]['stock'] ?? 0);
        if ($stock > 0 && $qty > $stock) $qty = $stock;

        $cart[$id]['qty'] = $qty;
        $this->cartSave($cart);

        $itemTotal = $cart[$id]['qty'] * $cart[$id]['price'];
        $grand = collect($cart)->sum(fn($i) => $i['qty'] * $i['price']);

        return response()->json([
            'success' => true,
            'qty' => $cart[$id]['qty'],
            'item_total' => $itemTotal,
            'grand_total' => $grand,
        ]);
    }

    public function remove(Request $request)
    {
        $cart = $this->cartGet($request);
        $id = (string) $request->id;

        if (isset($cart[$id])) unset($cart[$id]);

        $this->cartSave($cart);

        $grand = collect($cart)->sum(fn($i) => $i['qty'] * $i['price']);

        return response()->json([
            'success' => true,
            'grand_total' => $grand,
            'empty' => empty($cart),
        ]);
    }

    // opsional: tombol "kosongkan keranjang"
    public function clear()
    {
        session()->forget('cart');
        Cookie::queue(Cookie::forget($this->cookieKey));

        return response()->json(['success' => true]);
    }
}
