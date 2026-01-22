<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth   = Carbon::now()->endOfMonth();

        // ===== Income bulan ini (hanya PAID) =====
        $incomeThisMonth = Order::where('status', 'PAID')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$startMonth, $endMonth])
            ->sum('total');

        // ===== Total transaksi bulan ini (semua status) =====
        $trxThisMonth = Order::whereBetween('created_at', [$startMonth, $endMonth])->count();

        // ===== Split PAID & UNPAID =====
        $paidCount = Order::whereBetween('created_at', [$startMonth, $endMonth])
            ->where('status', 'PAID')
            ->count();

        // ✅ Lebih aman: semua yg bukan PAID dianggap UNPAID
        $unpaidCount = Order::whereBetween('created_at', [$startMonth, $endMonth])
            ->where('status', '!=', 'PAID')
            ->count();

        // ===== Produk terjual bulan ini (qty) - hanya PAID =====
        $soldThisMonth = OrderItem::whereHas('order', function ($q) use ($startMonth, $endMonth) {
                $q->where('status', 'PAID')
                  ->whereNotNull('paid_at')
                  ->whereBetween('paid_at', [$startMonth, $endMonth]);
            })
            ->sum('qty');

        // ===== jumlah user kasir (role user) =====
        $cashiersCount = User::where('role', 'user')->count();

        // ===== Produk stok menipis (5 stok terendah) =====
        $lowStock = Product::orderBy('stock', 'asc')->take(5)->get();

        // ===== Chart penjualan 7 hari terakhir (PAID) =====
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::now()->subDays($i)->format('Y-m-d'));

        $salesByDay = Order::selectRaw('DATE(paid_at) as day, SUM(total) as total')
            ->where('status', 'PAID')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = $days->map(fn ($d) => Carbon::parse($d)->format('d M'))->toArray();
        $chartData   = $days->map(fn ($d) => (int) ($salesByDay[$d] ?? 0))->toArray();

        // ===== Top 5 produk terjual bulan ini (PAID) =====
        $topProducts = OrderItem::select('name', DB::raw('SUM(qty) as qty'))
            ->whereHas('order', function ($q) use ($startMonth, $endMonth) {
                $q->where('status', 'PAID')
                  ->whereNotNull('paid_at')
                  ->whereBetween('paid_at', [$startMonth, $endMonth]);
            })
            ->groupBy('name')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        $topLabels = $topProducts->pluck('name')->toArray();
        $topQty    = $topProducts->pluck('qty')->map(fn ($x) => (int) $x)->toArray();

        // ===== Transaksi terbaru =====
        $recentOrders = Order::with('user')->latest()->take(10)->get();


        return view('dashboard.admin', compact(
            'incomeThisMonth',
            'trxThisMonth',
            'paidCount',
            'unpaidCount',
            'soldThisMonth',
            'cashiersCount',
            'lowStock',
            'chartLabels',
            'chartData',
            'topLabels',
            'topQty',
            'recentOrders'
        ));
    }
}
