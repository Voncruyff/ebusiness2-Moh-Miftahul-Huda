<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $quick = $request->query('quick', 'month'); // day | 7d | 30d | month

        $start = $request->query('start');
        $end   = $request->query('end');

        // ✅ Default: BULAN INI (biar sama seperti dashboard admin)
        if ($start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate   = Carbon::parse($end)->endOfDay();
        } else {
            if ($quick === 'day') {
                $startDate = Carbon::today()->startOfDay();
                $endDate   = Carbon::today()->endOfDay();
            } elseif ($quick === '7d') {
                $startDate = Carbon::today()->subDays(6)->startOfDay();
                $endDate   = Carbon::today()->endOfDay();
            } elseif ($quick === '30d') {
                $startDate = Carbon::today()->subDays(29)->startOfDay();
                $endDate   = Carbon::today()->endOfDay();
            } else {
                // month
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate   = Carbon::now()->endOfDay();
            }
        }

        // ✅ status PAID (dibuat lebih “kebal” kalau ada variasi huruf)
        $paidFilter = function ($q) {
            $q->whereRaw('UPPER(status) = ?', ['PAID']);
        };

        // KPI
        $totalRevenue = (int) DB::table('orders')
            ->when(true, $paidFilter)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $totalOrders = (int) DB::table('orders')
            ->when(true, $paidFilter)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgOrder = $totalOrders > 0 ? (int) round($totalRevenue / $totalOrders) : 0;

        $totalItemsSold = (int) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->when(true, function ($q) use ($paidFilter) {
                $paidFilter($q);
            })
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('order_items.qty');

        // Omzet per hari (range)
        $revenuePerDayRaw = DB::table('orders')
            ->select(DB::raw("DATE(created_at) as d"), DB::raw("SUM(total) as total"))
            ->when(true, $paidFilter)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $map = [];
        foreach ($revenuePerDayRaw as $r) {
            $map[$r->d] = (int) $r->total;
        }

        $labels = [];
        $seriesRevenue = [];
        $cursor = $startDate->copy()->startOfDay();
        $endCursor = $endDate->copy()->startOfDay();

        while ($cursor <= $endCursor) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d M');
            $seriesRevenue[] = $map[$key] ?? 0;
            $cursor->addDay();
        }

        // Top produk (qty)
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'order_items.name',
                DB::raw('SUM(order_items.qty) as qty_sold'),
                DB::raw('SUM(order_items.subtotal) as omzet')
            )
            ->when(true, function ($q) use ($paidFilter) {
                $paidFilter($q);
            })
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('order_items.name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        $barLabels = $topProducts->pluck('name')->map(function ($n) {
            return mb_strlen($n) > 18 ? (mb_substr($n, 0, 18) . '…') : $n;
        })->toArray();

        $barQty = $topProducts->pluck('qty_sold')->map(fn($x) => (int) $x)->toArray();

        return view('dashboard.reports', [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->copy()->startOfDay()->format('Y-m-d'),
            'quick' => $quick,

            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalItemsSold' => $totalItemsSold,
            'avgOrder' => $avgOrder,

            'labels' => $labels,
            'seriesRevenue' => $seriesRevenue,

            'topProducts' => $topProducts,
            'barLabels' => $barLabels,
            'barQty' => $barQty,
        ]);
    }
}
