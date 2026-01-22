<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // kamu sudah punya view checkout blade
        return view('dashboardUser.checkout');
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // ids dipilih (string "1,2,3")
        $idsParam = (string) $request->input('ids', '');
        $selectedIds = [];
        if ($idsParam !== '') {
            $selectedIds = array_values(array_filter(explode(',', $idsParam), fn($x) => trim($x) !== ''));
            $selectedIds = array_map('strval', $selectedIds);
        }

        // filter item berdasarkan selected ids (kalau kosong -> semua)
        $items = [];
        foreach ($cart as $id => $it) {
            $idStr = (string) $id;
            if (!empty($selectedIds) && !in_array($idStr, $selectedIds, true)) continue;
            $items[$idStr] = $it;
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada item yang dipilih.');
        }

        // addon_total dari form (kantong dll)
        $addonTotal = (int) $request->input('addon_total', 0);
        if ($addonTotal < 0) $addonTotal = 0;

        $serviceFee = 0;

        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += ((int) $it['price'] * (int) $it['qty']);
        }

        $total = $subtotal + $serviceFee + $addonTotal;

        // invoice unik
        $invoice = 'INV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        // ===== CREATE ORDER =====
        $order = Order::create([
            'user_id'        => auth()->id(),
            'invoice'        => $invoice,

            // di POS: ini kasir, bukan pembeli
            'buyer_name'     => auth()->user()->name ?? null,
            'buyer_phone'    => null,
            'note'           => $request->input('note'),

            'subtotal'       => $subtotal,
            'service_fee'    => $serviceFee,
            'total'          => $total,

            'status'         => 'UNPAID',
            'payment_method' => null,
        ]);

        // ===== CREATE ORDER ITEMS =====
        foreach ($items as $productId => $it) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => is_numeric($productId) ? (int)$productId : null,

                'name'       => (string) ($it['name'] ?? '-'),
                'price'      => (int) ($it['price'] ?? 0),
                'qty'        => (int) ($it['qty'] ?? 1),
                'unit'       => $it['unit'] ?? null,
                'image'      => $it['image'] ?? null,
                'subtotal'   => ((int) ($it['price'] ?? 0) * (int) ($it['qty'] ?? 1)),
            ]);
        }

        // OPTIONAL: hapus hanya item yg diproses dari session cart
        foreach (array_keys($items) as $pid) {
            unset($cart[(string)$pid]);
        }
        session()->put('cart', $cart);

        // redirect ke halaman payment berdasarkan order
        return redirect()->route('payment.show', $order->id);
    }
}
