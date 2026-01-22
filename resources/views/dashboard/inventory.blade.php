<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Inventory - SNV POS</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * { font-family: 'Inter', sans-serif; }
    .btn-primary { background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); }
    .line-clamp-2{
      display:-webkit-box;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      overflow:hidden;
    }
  </style>
</head>

<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

  {{-- SIDEBAR --}}
  @include('dashboard.adminsidebar')

  {{-- MAIN --}}
  <div class="flex-1 flex flex-col overflow-hidden lg:ml-64 transition-all duration-300">

    {{-- HEADER --}}
    <header class="bg-white shadow-sm z-30 sticky top-0">
      <div class="flex items-center justify-between px-6 py-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Inventory</h1>
          <p class="text-sm text-gray-500">Pantau stok, produk menipis, dan restock cepat</p>
        </div>

        <div class="flex items-center gap-4">
          <button class="relative text-gray-600 hover:text-gray-800">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
          </button>

          <img class="h-10 w-10 rounded-full border-2 border-purple-500"
               src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=667eea&color=fff"
               alt="Avatar">
        </div>
      </div>
    </header>

    {{-- MAIN (ini yang scroll) --}}
    <main class="flex-1 overflow-y-auto px-6 pb-6 pt-3">

      {{-- SUCCESS --}}
      @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-green-800 text-sm mb-3">
          <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
      @endif

      {{-- ✅ FILTER BAR STICKY (biar ga ikut scroll) --}}
      <div class="sticky top-0 z-20">
        {{-- bg wrapper biar pas sticky ga tembus --}}
        <div class="bg-gray-50 pt-2 pb-3">
          <div class="bg-white p-5 rounded-xl shadow border border-gray-100">
            <form method="GET" action="{{ route('admin.inventory') }}"
                  class="flex flex-col lg:flex-row gap-3 lg:items-end">

              {{-- search --}}
              <div class="flex-1">
                <label class="text-xs font-semibold text-gray-600">Cari</label>
                <div class="mt-1 relative">
                  <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <input type="text" name="q" value="{{ $q }}"
                         placeholder="Nama / SKU / kategori..."
                         class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-10 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                </div>
              </div>

              {{-- category --}}
              <div class="w-full lg:w-56">
                <label class="text-xs font-semibold text-gray-600">Kategori</label>
                <select name="category"
                        class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                  <option value="">Semua</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
                  @endforeach
                </select>
              </div>

              {{-- stock --}}
              <div class="w-full lg:w-56">
                <label class="text-xs font-semibold text-gray-600">Stok</label>
                <select name="stock"
                        class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                  <option value="" @selected($stock === '')>Semua</option>
                  <option value="low" @selected($stock === 'low')>Menipis (1-5)</option>
                  <option value="out" @selected($stock === 'out')>Habis (0)</option>
                </select>
              </div>

              {{-- sort --}}
              <div class="w-full lg:w-56">
                <label class="text-xs font-semibold text-gray-600">Urutkan</label>
                <select name="sort"
                        class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                  <option value="az" @selected($sort === 'az')>Nama (A-Z)</option>
                  <option value="za" @selected($sort === 'za')>Nama (Z-A)</option>
                  <option value="stock_low" @selected($sort === 'stock_low')>Stok (terendah)</option>
                  <option value="stock_high" @selected($sort === 'stock_high')>Stok (tertinggi)</option>
                </select>
              </div>

              <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2 text-sm font-bold text-white btn-primary hover:opacity-95 transition">
                  <i class="fas fa-filter"></i> Terapkan
                </button>

                <a href="{{ route('admin.inventory') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                  <i class="fas fa-rotate-left"></i>
                </a>
              </div>
            </form>

            <div class="mt-3 text-xs text-gray-500">
              Menampilkan <b class="text-gray-800">{{ $products->count() }}</b> dari
              <b class="text-gray-800">{{ $products->total() }}</b> produk
            </div>
          </div>
        </div>
      </div>

      {{-- ✅ spacer supaya produk ga ketabrak filter sticky --}}
      <div class="h-2"></div>

      {{-- CONTENT --}}
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- LEFT: list --}}
        <div class="lg:col-span-8 space-y-3">
          <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
              <p class="text-sm font-bold text-gray-800">Daftar Produk</p>
              <p class="text-xs text-gray-500">Klik produk untuk lihat detail</p>
            </div>

            <div class="p-4">
              <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($products as $p)
                  @php
                    $stockVal = (int)($p->stock ?? 0);
                    $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                    $badgeText = $stockVal.' '.($p->unit ?? '');
                    if ($stockVal <= 0) { $badgeClass = 'bg-red-100 text-red-700 border-red-200'; $badgeText = 'Habis'; }
                    elseif ($stockVal <= 5) { $badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200'; $badgeText = 'Menipis '.$stockVal; }
                  @endphp

                  <a href="{{ route('admin.inventory', array_merge(request()->query(), ['selected' => $p->id])) }}"
                     class="group rounded-2xl border bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="p-3">
                      <div class="relative bg-gray-50 rounded-2xl overflow-hidden aspect-[3/4]">
                        @if(!empty($p->image))
                          <img src="{{ asset('storage/'.$p->image) }}"
                               class="absolute inset-0 w-full h-full object-contain p-2 transition-transform duration-300 group-hover:scale-105"
                               alt="{{ $p->name }}">
                        @else
                          <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-2xl"></i>
                          </div>
                        @endif

                        <span class="absolute top-2 right-2 inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                          {{ $badgeText }}
                        </span>
                      </div>

                      <h4 class="mt-2 text-sm font-semibold text-gray-800 leading-snug line-clamp-2">
                        {{ $p->name }}
                      </h4>

                      <p class="text-xs text-gray-500 mt-0.5">
                        <i class="fas fa-tag mr-1"></i> {{ $p->category ?? '-' }}
                      </p>

                      <div class="mt-2">
                        <p class="text-[11px] text-gray-500">Harga jual</p>
                        <p class="text-base font-extrabold text-purple-600">
                          Rp {{ number_format((int)($p->selling_price ?? 0),0,',','.') }}
                        </p>
                      </div>
                    </div>
                  </a>
                @empty
                  <div class="col-span-full text-center py-10 text-gray-500">
                    Tidak ada produk ditemukan.
                  </div>
                @endforelse
              </div>
            </div>

            <div class="p-4 border-t">
              {{ $products->links() }}
            </div>
          </div>
        </div>

        {{-- RIGHT: detail --}}
        <aside class="lg:col-span-4">
          <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden lg:sticky lg:top-[88px]">
            <div class="p-4 border-b">
              <p class="text-sm font-bold text-gray-800">Detail Produk</p>
              <p class="text-xs text-gray-500">Informasi dan aksi cepat</p>
            </div>

            <div class="p-4">
              @if(!$selected)
                <div class="text-center text-gray-500 py-10">
                  <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <i class="fas fa-hand-pointer"></i>
                  </div>
                  <p class="font-semibold text-gray-700">Belum ada produk dipilih</p>
                  <p class="text-sm text-gray-500 mt-1">Klik salah satu produk di kiri.</p>
                </div>
              @else
                @include('dashboard._detail', ['selected' => $selected])
              @endif
            </div>
          </div>
        </aside>

      </div>
    </main>
  </div>
</div>
</body>
</html>
