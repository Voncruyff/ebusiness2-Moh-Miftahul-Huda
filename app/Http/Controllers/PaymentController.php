<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show(Order $order)
    {
        // security: pastikan order milik kasir/user yang login (kalau user_id dipakai)
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        // kalau sudah PAID -> langsung ke success
        if ($order->status === 'PAID') {
            return redirect()->route('payment.success', $order);
        }

        $items = $order->items()->get();
        return view('dashboardUser.payment', compact('order', 'items'));
    }

    public function pay(Request $request)
    {
        $data = $request->validate([
            'order_id'        => ['required', 'integer', 'exists:orders,id'],
            'payment_method'  => ['required', 'in:cash,bank,ewallet'],
            'paid_amount'     => ['nullable', 'integer', 'min:0'],
        ]);

        // Ambil order + items
        $order = Order::with('items')->findOrFail($data['order_id']);

        // security
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        // cegah bayar ulang
        if ($order->status === 'PAID') {
            return redirect()->route('payment.success', $order);
        }

        $method = $data['payment_method'];

        $paidAmount = 0;
        $change = 0;

        if ($method === 'cash') {
            $paidAmount = (int) ($data['paid_amount'] ?? 0);

            if ($paidAmount < (int) $order->total) {
                return back()->withErrors([
                    'paid_amount' => 'Uang diterima kurang dari total tagihan.'
                ])->withInput();
            }

            $change = $paidAmount - (int) $order->total;
        } else {
            // bank/ewallet: dianggap lunas saat kasir klik bayar (manual confirm)
            $paidAmount = (int) $order->total;
            $change = 0;
        }

        try {
            DB::transaction(function () use ($order, $method, $paidAmount, $change) {

                // lock order row biar aman dari double submit
                $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

                if (!$lockedOrder) {
                    throw new \Exception('Order tidak ditemukan.');
                }

                // kalau sudah PAID, stop
                if ($lockedOrder->status === 'PAID') {
                    return;
                }

                // ===== POTONG STOK =====
                // Sesuaikan nama kolom stok kamu: stock / stok
                foreach ($order->items as $it) {
                    if (!$it->product_id) continue;

                    $product = Product::where('id', $it->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) continue;

                    // GANTI 'stock' kalau kolommu namanya 'stok'
                    $currentStock = (int) ($product->stock ?? 0);

                    if ($currentStock < (int) $it->qty) {
                        throw new \Exception("Stok produk '{$product->name}' tidak cukup.");
                    }

                    $product->decrement('stock', (int) $it->qty);
                }

                // ===== UPDATE ORDER =====
                $lockedOrder->payment_method = $method;
                $lockedOrder->paid_amount = $paidAmount;
                $lockedOrder->change_amount = $change;
                $lockedOrder->paid_at = now();
                $lockedOrder->status = 'PAID';
                $lockedOrder->save();
            });

        } catch (\Throwable $e) {
            return back()->withErrors([
                'payment' => $e->getMessage(),
            ])->withInput();
        }

        return redirect()
            ->route('payment.success', $order)
            ->with('success', 'Pembayaran berhasil. Order sudah LUNAS.');
    }

    public function success(Order $order)
    {
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $items = $order->items()->get();
        return view('dashboardUser.payment_success', compact('order', 'items'));
    }
}
