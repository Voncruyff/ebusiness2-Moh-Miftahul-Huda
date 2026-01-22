<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminInventoryController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $stock    = trim((string) $request->query('stock', '')); // '' | low | out
        $sort     = trim((string) $request->query('sort', 'az')); // az | za | stock_low | stock_high

        $productsQuery = DB::table('products')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->when($category !== '', function ($qb) use ($category) {
                $qb->where('category', $category);
            })
            ->when($stock === 'low', function ($qb) {
                $qb->where('stock', '>', 0)->where('stock', '<=', 5);
            })
            ->when($stock === 'out', function ($qb) {
                $qb->where('stock', '<=', 0);
            })
            ->when(true, function ($qb) use ($sort) {
                if ($sort === 'za') return $qb->orderBy('name', 'desc');
                if ($sort === 'stock_low') return $qb->orderBy('stock', 'asc');
                if ($sort === 'stock_high') return $qb->orderBy('stock', 'desc');
                return $qb->orderBy('name', 'asc');
            });

        $products = $productsQuery
            ->paginate(12)
            ->withQueryString();

        $categories = DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Selected product logic
        $selectedId = $request->query('selected');
        $selected = null;

        if ($selectedId) {
            $selected = DB::table('products')->where('id', $selectedId)->first();
        }

        // fallback: pilih produk pertama di halaman (kalau ada)
        if (!$selected && $products->count() > 0) {
            $selected = DB::table('products')->where('id', $products->first()->id)->first();
        }

        return view('dashboard.inventory', [
            'products'   => $products,
            'categories' => $categories,
            'selected'   => $selected,
            'q'          => $q,
            'category'   => $category,
            'stock'      => $stock,
            'sort'       => $sort,
        ]);
    }

    /**
     * Endpoint untuk HTMX (biar klik produk ga reload / ga kedip)
     * Return partial blade detail.
     */
    public function detail(Request $request, $id)
    {
        $selected = DB::table('products')->where('id', $id)->first();

        if (!$selected) {
            // kalau produk tidak ditemukan, return panel kosong (lebih aman buat HTMX)
            return response()->view('dashboard.inventory_partials.detail-empty', [], 404);
        }

        return view('dashboard.inventory_partials.detail', [
            'selected' => $selected,
        ]);
    }

    public function restock(Request $request, $product)
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        DB::table('products')
            ->where('id', $product)
            ->increment('stock', (int) $request->qty);

        // Jika restock dilakukan via HTMX dan kamu mau refresh panel detail,
        // kamu bisa return partial juga. Tapi default: tetap redirect back.
        return back()->with('success', 'Stok berhasil ditambahkan.');
    }
}
