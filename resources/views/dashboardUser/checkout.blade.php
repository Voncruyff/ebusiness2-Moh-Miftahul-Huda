<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Checkout - SNV Pos</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * { font-family: 'Inter', sans-serif; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

    :root { --sidebar-w: 288px; }
    .content-with-sidebar { padding-left: var(--sidebar-w); }
    @media (max-width: 1024px) { .content-with-sidebar { padding-left: 0; } }
  </style>
</head>

<body class="bg-gray-50">
<div class="min-h-screen">
  @include('dashboardUser.usersidebar')

  <div class="content-with-sidebar">

    <!-- HEADER -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
      <div class="px-6 lg:px-10">
        <div class="flex h-16 items-center justify-between">

          <div class="flex items-center gap-3">
            {{-- tombol sidebar (mobile) --}}
            <button id="sidebarToggle"
              class="lg:hidden text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-lg">
              <i class="fas fa-bars text-xl"></i>
            </button>

            <div>
              <h1 class="text-base font-extrabold text-gray-800">Checkout</h1>
              <p class="text-xs text-gray-500">Buat pesanan & lanjut pembayaran</p>
            </div>
          </div>

          <a href="{{ route('cart.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>

        </div>
      </div>
    </header>



    @php
      $cart = session()->get('cart', []);

      // ids dipilih dari keranjang: /checkout?ids=1,2,3
      $idsParam = request('ids');
      $selectedIds = [];
      if ($idsParam) {
        $selectedIds = array_values(array_filter(explode(',', $idsParam), fn($x) => trim($x) !== ''));
        $selectedIds = array_map('strval', $selectedIds);
      }

      // kalau ids kosong => checkout semua
      $items = [];
      foreach ($cart as $id => $it) {
        $idStr = (string) $id;
        if (!empty($selectedIds) && !in_array($idStr, $selectedIds, true)) continue;
        $items[$idStr] = $it;
      }

      $subtotal = 0;
      foreach ($items as $it) { $subtotal += ((int)$it['price'] * (int)$it['qty']); }

      // biaya layanan default (bisa 0)
      $serviceFee = 0;

      // addon default (untuk UI)
      $addons = [
        ['key' => 'kantong_plastik', 'name' => 'Kantong Plastik', 'price' => 500],
        ['key' => 'kantong_kain',    'name' => 'Kantong Kain',    'price' => 2000],
      ];
    @endphp

    <!-- MAIN -->
    <main class="px-6 lg:px-10 py-8">

      <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900">Checkout</h2>
        <p class="text-sm text-gray-500">Klik “Buat Pesanan” untuk lanjut ke pembayaran.</p>
      </div>

      @if(empty($cart))
        <div class="rounded-2xl bg-white p-8 shadow border border-gray-100 text-center">
          <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 text-purple-600">
            <i class="fas fa-cart-shopping"></i>
          </div>
          <p class="text-gray-700 font-semibold">Keranjang kosong</p>
          <p class="text-sm text-gray-500 mt-1">Silakan pilih produk dulu.</p>
          <a href="{{ route('user.dashboard') }}"
             class="mt-5 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white btn-primary hover:opacity-95">
            <i class="fas fa-store"></i> Belanja sekarang
          </a>
        </div>

      @elseif(empty($items))
        <div class="rounded-2xl bg-white p-8 shadow border border-gray-100 text-center">
          <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-yellow-50 text-yellow-600">
            <i class="fas fa-triangle-exclamation"></i>
          </div>
          <p class="text-gray-700 font-semibold">Tidak ada item yang dipilih</p>
          <p class="text-sm text-gray-500 mt-1">Kembali ke keranjang dan centang item yang ingin di-checkout.</p>

          <a href="{{ route('cart.index') }}"
             class="mt-5 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white btn-primary hover:opacity-95">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>

      @else

      <!-- FORM CHECKOUT -->
      <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <!-- item yang dipilih -->
        <input type="hidden" name="ids" value="{{ implode(',', array_keys($items)) }}">

        <!-- total addon akan diisi JS -->
        <input type="hidden" id="addonTotalInput" name="addon_total" value="0">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- LEFT -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Informasi Kasir -->
            <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b">
                <p class="text-sm font-bold text-gray-800">Informasi Kasir</p>
                <p class="text-xs text-gray-500">Nama kasir otomatis dari akun yang login.</p>
              </div>

              <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-semibold text-gray-700">Kasir</label>
                  <input
                    type="text"
                    value="{{ Auth::user()->name ?? '' }}"
                    readonly
                    class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-100 px-3 py-2 text-sm outline-none cursor-not-allowed"
                  >
                </div>

                <div class="md:col-span-2">
                  <label class="text-sm font-semibold text-gray-700">Catatan Kasir (opsional)</label>
                  <textarea
                    name="note"
                    rows="3"
                    placeholder="Contoh: bayar cash, customer minta kantong 2..."
                    class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Tambahan (Addon) -->
            <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b">
                <p class="text-sm font-bold text-gray-800">Tambahan</p>
                <p class="text-xs text-gray-500">Opsional (misal kantong).</p>
              </div>

              <div class="p-6 space-y-3">
                @foreach($addons as $a)
                  <label class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 cursor-pointer hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-200"
                        name="addons[{{ $a['key'] }}]"
                        value="{{ $a['price'] }}"
                        data-addon
                      >
                      <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $a['name'] }}</p>
                        <p class="text-xs text-gray-500">Tambah Rp {{ number_format($a['price'], 0, ',', '.') }}</p>
                      </div>
                    </div>

                    <span class="text-sm font-extrabold text-gray-800">
                      Rp {{ number_format($a['price'], 0, ',', '.') }}
                    </span>
                  </label>
                @endforeach

                <div class="pt-2 flex items-center justify-between text-sm">
                  <span class="text-gray-600 font-semibold">Total tambahan</span>
                  <span class="font-extrabold text-gray-900">
                    Rp <span id="addonTotalText">0</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Item Checkout -->
            <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b flex items-center justify-between">
                <p class="text-sm font-bold text-gray-800">Item Checkout</p>
                <p class="text-xs text-gray-500">{{ count($items) }} item</p>
              </div>

              <div class="divide-y">
                @foreach($items as $id => $item)
                  @php
                    $img = $item['image'] ?? null;
                    $total = ((int)$item['price'] * (int)$item['qty']);
                    $unit = $item['unit'] ?? '';
                  @endphp

                  <div class="p-6 flex gap-4">
                    <div class="shrink-0">
                      @if($img)
                        <img
                          src="{{ asset('storage/'.$img) }}"
                          class="h-16 w-16 rounded-xl border bg-gray-50 object-cover"
                          onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                          alt="{{ $item['name'] }}"
                        >
                        <div class="hidden h-16 w-16 rounded-xl bg-gray-100 border flex items-center justify-center text-gray-400">
                          <i class="fas fa-image"></i>
                        </div>
                      @else
                        <div class="h-16 w-16 rounded-xl bg-gray-100 border flex items-center justify-center text-gray-400">
                          <i class="fas fa-image"></i>
                        </div>
                      @endif
                    </div>

                    <div class="min-w-0 flex-1">
                      <p class="font-bold text-gray-900 truncate">{{ $item['name'] }}</p>
                      <p class="text-xs text-gray-500 mt-1">
                        Qty: <span class="font-semibold text-gray-700">{{ $item['qty'] }}</span> {{ $unit }}
                        • Harga: Rp {{ number_format($item['price'], 0, ',', '.') }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        Subtotal: <span class="font-bold text-purple-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                      </p>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

          </div>

          <!-- RIGHT: SUMMARY -->
          <aside class="rounded-2xl bg-white shadow border border-gray-100 h-fit sticky top-24 overflow-hidden">
            <div class="px-6 py-4 border-b">
              <p class="text-sm font-bold text-gray-800">Ringkasan</p>
            </div>

            <div class="px-6 py-5 space-y-4">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold text-gray-800">
                  Rp <span id="subtotalText">{{ number_format($subtotal, 0, ',', '.') }}</span>
                </span>
              </div>

              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Tambahan</span>
                <span class="font-semibold text-gray-800">
                  Rp <span id="addonTotalText2">0</span>
                </span>
              </div>

              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Biaya layanan</span>
                <span class="text-gray-800">
                  Rp <span id="serviceFeeText">{{ number_format($serviceFee, 0, ',', '.') }}</span>
                </span>
              </div>

              <div class="border-t pt-4 flex items-center justify-between">
                <span class="text-gray-600 font-semibold">Total</span>
                <span class="text-xl font-extrabold text-gray-900">
                  Rp <span id="totalText">{{ number_format($subtotal + $serviceFee, 0, ',', '.') }}</span>
                </span>
              </div>

              <button
                type="submit"
                class="w-full rounded-xl px-4 py-3 text-sm font-bold text-white btn-primary hover:opacity-95 transition">
                <i class="fas fa-bag-shopping mr-2"></i> Buat Pesanan
              </button>

              <p class="text-xs text-gray-500">
                Setelah pesanan dibuat, kamu akan diarahkan ke halaman pembayaran.
              </p>
            </div>
          </aside>

        </div>
      </form>

      @endif
    </main>
  </div>
</div>

<script>
  function rupiah(n){
    return new Intl.NumberFormat('id-ID').format(n);
  }

  const addonCheckboxes = document.querySelectorAll('[data-addon]');
  const addonTotalText = document.getElementById('addonTotalText');
  const addonTotalText2 = document.getElementById('addonTotalText2');
  const totalText = document.getElementById('totalText');
  const addonTotalInput = document.getElementById('addonTotalInput');

  // angka dasar dari server
  const baseSubtotal = {{ (int) $subtotal }};
  const serviceFee = {{ (int) $serviceFee }};

  function calcAddonTotal(){
    let t = 0;
    addonCheckboxes.forEach(cb => {
      if (cb.checked) t += parseInt(cb.value || '0', 10);
    });
    return t;
  }

  function refreshTotals(){
    const addonTotal = calcAddonTotal();
    const grand = baseSubtotal + serviceFee + addonTotal;

    if (addonTotalText) addonTotalText.textContent = rupiah(addonTotal);
    if (addonTotalText2) addonTotalText2.textContent = rupiah(addonTotal);
    if (totalText) totalText.textContent = rupiah(grand);
    if (addonTotalInput) addonTotalInput.value = String(addonTotal);
  }

  addonCheckboxes.forEach(cb => cb.addEventListener('change', refreshTotals));

  // initial
  refreshTotals();
</script>

</body>
</html>
