@php $s = (int)($selected->stock ?? 0); @endphp

<div class="space-y-3">
  <div class="relative rounded-2xl bg-gray-50 border overflow-hidden aspect-[4/3] max-h-48">
    @if(!empty($selected->image))
      <img src="{{ asset('storage/'.$selected->image) }}"
           class="absolute inset-0 w-full h-full object-contain p-2"
           alt="{{ $selected->name }}">
    @else
      <div class="absolute inset-0 flex items-center justify-center text-gray-400">
        <i class="fas fa-image text-2xl"></i>
      </div>
    @endif
  </div>

  <div>
    <p class="text-base font-extrabold text-gray-900 leading-tight">{{ $selected->name }}</p>
    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-barcode mr-1"></i> {{ $selected->sku ?? '-' }}</p>
    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-tag mr-1"></i> {{ $selected->category ?? '-' }}</p>
  </div>

  <div class="grid grid-cols-2 gap-2">
    <div class="rounded-xl border bg-white p-3">
      <p class="text-[11px] text-gray-500">Stok</p>
      <p class="text-sm font-bold text-gray-800">{{ $s }} {{ $selected->unit ?? '' }}</p>
      @if($s <= 0)
        <p class="text-xs font-semibold text-red-600 mt-1">Habis</p>
      @elseif($s <= 5)
        <p class="text-xs font-semibold text-yellow-600 mt-1">Menipis</p>
      @else
        <p class="text-xs font-semibold text-green-600 mt-1">Aman</p>
      @endif
    </div>

    <div class="rounded-xl border bg-white p-3">
      <p class="text-[11px] text-gray-500">Harga</p>
      <p class="text-sm font-extrabold text-purple-600">
        Rp {{ number_format((int)($selected->selling_price ?? 0),0,',','.') }}
      </p>
    </div>
  </div>

  <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
    <p class="text-xs font-semibold text-gray-700">Deskripsi</p>
    <p class="mt-1 text-sm text-gray-600 leading-relaxed">
      {{ trim((string)($selected->description ?? '')) !== '' ? $selected->description : 'Tidak ada deskripsi.' }}
    </p>
  </div>

  <form method="POST" action="{{ route('admin.inventory.restock', $selected->id) }}" class="pt-1">
    @csrf
    <label class="text-xs font-semibold text-gray-600">Tambah stok</label>
    <div class="mt-1 flex gap-2">
      <input type="number" name="qty" min="1" value="1"
             class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
      <button type="submit"
              class="whitespace-nowrap inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-bold text-white btn-primary hover:opacity-95">
        <i class="fas fa-plus"></i> Restock
      </button>
    </div>
    @error('qty')
      <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
    @enderror
  </form>

  <div class="grid grid-cols-2 gap-2 pt-1">
    <a href="{{ route('admin.products.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
      <i class="fas fa-box"></i> Produk
    </a>

    <a href="{{ route('admin.products.index', ['search' => $selected->sku ?? $selected->name]) }}"
       class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
      <i class="fas fa-pen"></i> Edit
    </a>
  </div>
</div>
