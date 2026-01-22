<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminHistoryController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));          // UNPAID/PAID/...
        $method = trim((string) $request->query('payment_method', ''));  // cash/bank/ewallet
        $sort   = trim((string) $request->query('sort', 'new'));         // new/old

        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.*',
                'users.name as cashier_name',   // anggap user = kasir
                'users.email as cashier_email'
            )
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('orders.invoice', 'like', "%{$q}%")
                        ->orWhere('orders.buyer_name', 'like', "%{$q}%")
                        ->orWhere('orders.buyer_phone', 'like', "%{$q}%")
                        ->orWhere('users.name', 'like', "%{$q}%")
                        ->orWhere('users.email', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn($qb) => $qb->where('orders.status', $status))
            ->when($method !== '', fn($qb) => $qb->where('orders.payment_method', $method))
            ->orderBy('orders.created_at', $sort === 'old' ? 'asc' : 'desc')
            ->paginate(12)
            ->withQueryString();

        $orderIds = $orders->pluck('id')->all();

        $itemsCount = [];
        if (!empty($orderIds)) {
            $itemsCount = DB::table('order_items')
                ->select('order_id', DB::raw('SUM(qty) as qty_sum'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('order_id')
                ->pluck('qty_sum', 'order_id')
                ->toArray();
        }

        return view('dashboard.history', [
            'orders' => $orders,
            'itemsCount' => $itemsCount,
            'q' => $q,
            'status' => $status,
            'method' => $method,
            'sort' => $sort,
        ]);
    }
}
