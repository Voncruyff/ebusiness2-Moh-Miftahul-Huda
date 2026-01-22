<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', '')); // UNPAID / PAID / ""
        $sort = trim((string) $request->query('sort', 'new'));  // new / old

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('invoice', 'like', '%' . $q . '%')
                        ->orWhere('buyer_name', 'like', '%' . $q . '%')
                        ->orWhere('buyer_phone', 'like', '%' . $q . '%');
                });
            })
            ->when($status !== '', function ($qb) use ($status) {
                $qb->where('status', $status);
            })
            ->orderBy('created_at', $sort === 'old' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString();

        // ambil jumlah item per order (biar gak N+1)
        $orderIds = $orders->pluck('id')->all();

        $itemsCount = [];
        $itemsSum = [];

        if (!empty($orderIds)) {
            $itemsCount = DB::table('order_items')
                ->select('order_id', DB::raw('COUNT(*) as cnt'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('order_id')
                ->pluck('cnt', 'order_id')
                ->toArray();

            $itemsSum = DB::table('order_items')
                ->select('order_id', DB::raw('SUM(subtotal) as sum_subtotal'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('order_id')
                ->pluck('sum_subtotal', 'order_id')
                ->toArray();
        }

        return view('dashboardUser.history', [
            'orders' => $orders,
            'itemsCount' => $itemsCount,
            'itemsSum' => $itemsSum,
            'q' => $q,
            'status' => $status,
            'sort' => $sort,
        ]);
    }
}
