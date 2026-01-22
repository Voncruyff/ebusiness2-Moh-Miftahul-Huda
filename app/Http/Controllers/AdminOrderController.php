<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function show($order)
    {
        $orderData = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.*',
                'users.name as cashier_name',
                'users.email as cashier_email'
            )
            ->where('orders.id', $order)
            ->first();

        abort_if(!$orderData, 404);

        $items = DB::table('order_items')
            ->where('order_id', $order)
            ->orderBy('id', 'asc')
            ->get();

        // Hitung subtotal dari items (kalau kolom subtotal sudah ada, ini tetap aman)
        $subtotal = (int) $items->sum('subtotal');
        $total    = (int) ($orderData->total ?? $subtotal);

        // OPTIONAL: kalau kamu punya kolom paid_amount / change_amount di orders
        $paidAmount   = (int) ($orderData->paid_amount ?? $total);
        $changeAmount = (int) ($orderData->change_amount ?? max(0, $paidAmount - $total));

        return view('dashboard.invoice_admin', [
            'order' => $orderData,
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $total,
            'paidAmount' => $paidAmount,
            'changeAmount' => $changeAmount,
        ]);
    }
}
