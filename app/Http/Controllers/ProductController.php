<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Product::distinct()->pluck('category')->filter();

        return view('dashboard.products', compact('products', 'categories'));

    }

    /**
     * Halaman create produk
     */
    public function create()
    {
        return view('fitur.products-create');
    }

    /**
     * Store produk baru
     */
    public function store(Request $request)
    {
        // FIX format harga (hapus titik)
        $request->merge([
            'purchase_price' => str_replace('.', '', $request->purchase_price),
            'selling_price'  => str_replace('.', '', $request->selling_price),
        ]);

        $validated = $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');

    }

    /**
     * Tampilkan detail produk (dipakai modal)
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update produk
     */
    public function update(Request $request, Product $product)
    {
        // FIX format harga
        $request->merge([
            'purchase_price' => str_replace('.', '', $request->purchase_price),
            'selling_price'  => str_replace('.', '', $request->selling_price),
        ]);

        $validated = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
    ->with('success', 'Produk berhasil diupdate!');

    }

    /**
     * Hapus produk
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');

    }

    public function streamProducts()
{
    return response()->stream(function () {
        while (true) {
            $products = Product::active()->orderBy('created_at', 'desc')->get();

            echo "data: " . json_encode($products) . "\n\n";
            ob_flush();
            flush();
            sleep(1);
        }
    }, 200, [
        "Content-Type" => "text/event-stream",
        "Cache-Control" => "no-cache",
        "Connection" => "keep-alive",
    ]);
}


}
