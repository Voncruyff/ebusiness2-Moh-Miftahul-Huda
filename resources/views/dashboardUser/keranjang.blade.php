<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Keranjang - POSin</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * { font-family: 'Inter', sans-serif; }
    .gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
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

      <!-- HEADER (FIX: cuma "Keranjang") -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
        <div class="px-6 lg:px-10">
          <div class="flex h-16 items-center justify-between">
            <h1 class="text-lg font-extrabold text-gray-800">Keranjang</h1>
          </div>
        </div>
      </header>


      <!-- MAIN -->
      <main class="px-6 lg:px-10 py-8">
        <div class="mb-6 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-800">Keranjang</h2>
        </div>

        @php
          $cart = session()->get('cart', []);
        @endphp

        @if(empty($cart))
          <div class="rounded-2xl bg-white p-8 shadow border border-gray-100 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 text-purple-600">
              <i class="fas fa-cart-shopping"></i>
            </div>
            <p class="text-gray-600 font-medium">Keranjang masih kosong.</p>
            <p class="text-sm text-gray-500 mt-1">Yuk pilih produk dulu.</p>
            <a href="{{ route('user.dashboard') }}"
               class="mt-5 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white btn-primary hover:opacity-95">
              <i class="fas fa-store"></i> Belanja sekarang
            </a>
          </div>
        @else
          @php
            $cart = session()->get('cart', []);

            // ✅ FIX stok: ambil stok terbaru dari DB (tanpa "use")
            $productIds = array_map('intval', array_keys($cart));
            $stocks = \App\Models\Product::whereIn('id', $productIds)->pluck('stock', 'id')->toArray();

            $grand = 0;
            foreach ($cart as $it) { $grand += ((int)$it['price'] * (int)$it['qty']); }
          @endphp


          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LIST ITEM -->
            <div class="lg:col-span-2 rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
              <!-- HEADER LIST + PILIH SEMUA -->
              <div class="px-6 py-4 border-b flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-800">
                  Item ({{ count($cart) }})
                </p>

                <label class="inline-flex items-center gap-2 text-sm text-gray-600 select-none">
                  <input
                    id="selectAll"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-200"
                    checked
                  >
                  Pilih semua
                </label>
              </div>

              <div class="divide-y">
                @foreach($cart as $id => $item)
                  @php
                    // ✅ FIX stok per item dari DB (bukan dari session)
                    $stock = (int) ($stocks[(int)$id] ?? 0);
                    $maxQty = $stock > 0 ? $stock : 1;

                    // clamp qty tampilannya biar ga lebih dari stok terbaru
                    $qtyDisplay = (int)($item['qty'] ?? 1);
                    if ($stock > 0 && $qtyDisplay > $maxQty) $qtyDisplay = $maxQty;
                    if ($qtyDisplay < 1) $qtyDisplay = 1;

                    $total = ((int)$item['price'] * $qtyDisplay);

                    $img = $item['image'] ?? null;
                  @endphp

                  <div
                    class="px-6 py-5 flex gap-4 cart-row"
                    data-row-id="{{ $id }}"
                    data-price="{{ (int) $item['price'] }}"
                  >
                    <!-- CHECKBOX -->
                    <div class="pt-1">
                      <input
                        type="checkbox"
                        class="h-5 w-5 rounded border-gray-300 text-purple-600 focus:ring-purple-200"
                        data-select
                        data-id="{{ $id }}"
                        checked
                      >
                    </div>

                    <!-- THUMB -->
                    <div class="shrink-0">
                      @if($img)
                        <img
                          src="{{ asset('storage/' . $img) }}"
                          alt="{{ $item['name'] }}"
                          class="h-16 w-16 rounded-xl object-cover border bg-gray-50"
                          onerror="this.style.display='none'; this.parentElement.querySelector('.img-fallback').classList.remove('hidden');"
                        />
                        <div class="img-fallback hidden h-16 w-16 rounded-xl bg-gray-100 border flex items-center justify-center text-gray-400">
                          <i class="fas fa-image"></i>
                        </div>
                      @else
                        <div class="h-16 w-16 rounded-xl bg-gray-100 border flex items-center justify-center text-gray-400">
                          <i class="fas fa-image"></i>
                        </div>
                      @endif
                    </div>

                    <!-- INFO -->
                    <div class="min-w-0 flex-1">
                      <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                          <p class="font-semibold text-gray-800 truncate">{{ $item['name'] }}</p>
                          <p class="text-xs text-gray-500 mt-1">
                            Stok: <span class="font-semibold">{{ $stock }}</span> {{ $item['unit'] ?? '' }}
                          </p>
                        </div>

                        <button
                          type="button"
                          data-remove
                          data-id="{{ $id }}"
                          class="shrink-0 inline-flex items-center justify-center h-9 w-9 rounded-xl text-red-600 hover:bg-red-50 transition"
                          title="Hapus">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>

                      <div class="mt-3 flex items-center justify-between gap-4 flex-wrap">
                        <div>
                          <p class="text-xs text-gray-500">Harga</p>
                          <p class="font-bold text-gray-800">
                            Rp {{ number_format((int)$item['price'], 0, ',', '.') }}
                          </p>
                        </div>

                        <!-- QTY CONTROL -->
                        <div class="flex items-center gap-2">
                          <button
                            type="button"
                            class="h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition"
                            data-qty-btn="minus"
                            data-id="{{ $id }}">
                            <i class="fas fa-minus text-xs"></i>
                          </button>

                          <input
                            type="number"
                            min="1"
                            max="{{ $maxQty }}"
                            value="{{ $qtyDisplay }}"
                            data-qty
                            data-id="{{ $id }}"
                            class="h-9 w-20 rounded-xl border border-gray-200 px-3 text-center font-semibold text-gray-800"
                          >

                          <button
                            type="button"
                            class="h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition"
                            data-qty-btn="plus"
                            data-id="{{ $id }}">
                            <i class="fas fa-plus text-xs"></i>
                          </button>

                          <span class="text-xs text-gray-400 ml-2">
                            max {{ $maxQty }}
                          </span>
                        </div>

                        <div class="text-right">
                          <p class="text-xs text-gray-500">Subtotal</p>
                          <p class="font-extrabold text-purple-600 whitespace-nowrap">
                            Rp <span id="item-total-{{ $id }}">{{ number_format($total, 0, ',', '.') }}</span>
                          </p>
                        </div>
                      </div>

                      @if($stock <= 0)
                        <p class="mt-2 text-xs text-red-700 bg-red-50 border border-red-100 inline-flex rounded-lg px-2 py-1">
                          Stok habis. Item ini tidak bisa di-checkout.
                        </p>
                      @elseif($qtyDisplay >= $stock)
                        <p class="mt-2 text-xs text-yellow-700 bg-yellow-50 border border-yellow-100 inline-flex rounded-lg px-2 py-1">
                          Qty sudah maksimal stok.
                        </p>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <!-- SUMMARY -->
            <aside class="rounded-2xl bg-white shadow border border-gray-100 h-fit sticky top-24">
              <div class="px-6 py-4 border-b">
                <p class="text-sm font-semibold text-gray-800">Ringkasan</p>
              </div>

              <div class="px-6 py-5 space-y-4">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-600">Subtotal (dipilih)</span>
                  <span class="font-semibold text-gray-800">
                    Rp <span id="grand-total">{{ number_format($grand, 0, ',', '.') }}</span>
                  </span>
                </div>

                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-600">Biaya layanan</span>
                  <span class="text-gray-800">Rp 0</span>
                </div>

                <div class="border-t pt-4 flex items-center justify-between">
                  <span class="text-gray-600 font-semibold">Total</span>
                  <span class="text-xl font-extrabold text-gray-900">
                    Rp <span id="grand-total-2">{{ number_format($grand, 0, ',', '.') }}</span>
                  </span>
                </div>

                <button
                  id="checkoutBtn"
                  type="button"
                  class="w-full rounded-xl px-4 py-3 text-sm font-bold text-white btn-primary hover:opacity-95 transition">
                  <i class="fas fa-credit-card mr-2"></i> Checkout
                </button>

                <p id="selectedHint" class="text-xs text-gray-500">
                  * Pilih item yang ingin di-checkout.
                </p>
              </div>
            </aside>

          </div>
        @endif
      </main>
    </div>
  </div>

  <script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function rupiah(n){ return new Intl.NumberFormat('id-ID').format(n); }

    function toast(msg){
      const t = document.createElement("div");
      t.className = "fixed right-6 bottom-6 z-50 rounded-lg bg-gray-900 text-white px-4 py-3 text-sm shadow-lg";
      t.innerText = msg;
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 1300);
    }

    async function postJson(url, payload){
      const res = await fetch(url, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf, "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => ({}));
      return { res, data };
    }

    function setGrand(grand){
      const el1 = document.getElementById('grand-total');
      const el2 = document.getElementById('grand-total-2');
      if (el1) el1.textContent = rupiah(grand);
      if (el2) el2.textContent = rupiah(grand);
    }

    async function updateQty(id, qty, inputEl){
      const { res, data } = await postJson("{{ route('cart.update') }}", { id, qty });

      if (!res.ok || !data.success) {
        toast("❌ " + (data.message || "Gagal update qty"));
        return;
      }

      if (inputEl) inputEl.value = data.qty;

      const itemTotalEl = document.getElementById('item-total-' + id);
      if (itemTotalEl) itemTotalEl.textContent = rupiah(data.item_total);

      refreshSelectedSummary();
      toast("✅ Qty diperbarui");
    }

    // input qty manual
    document.querySelectorAll('[data-qty]').forEach(input => {
      input.addEventListener('change', async () => {
        const id = input.dataset.id;
        const max = parseInt(input.getAttribute('max') || '0', 10);

        let qty = parseInt(input.value || '1', 10);
        if (qty < 1) qty = 1;
        if (max > 0 && qty > max) qty = max;
        input.value = qty;

        await updateQty(id, qty, input);
      });
    });

    // tombol + / -
    document.querySelectorAll('[data-qty-btn]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        const mode = btn.dataset.qtyBtn;

        const input = document.querySelector(`[data-qty][data-id="${id}"]`);
        if (!input) return;

        const max = parseInt(input.getAttribute('max') || '0', 10);
        let qty = parseInt(input.value || '1', 10);

        qty = (mode === 'plus') ? qty + 1 : qty - 1;
        if (qty < 1) qty = 1;
        if (max > 0 && qty > max) qty = max;

        input.value = qty;
        await updateQty(id, qty, input);
      });
    });

    // hapus item
    document.querySelectorAll('[data-remove]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.dataset.id;

        const { res, data } = await postJson("{{ route('cart.remove') }}", { id });

        if (!res.ok || !data.success) {
          toast("❌ " + (data.message || "Gagal hapus item"));
          return;
        }

        const wrapper = btn.closest('.cart-row');
        if (wrapper) wrapper.remove();

        refreshSelectedSummary();
        toast("🗑️ Item dihapus");

        if (data.empty) location.reload();
      });
    });

    // ===== CHECKLIST LOGIC =====
    function getSelectedIds() {
      return Array.from(document.querySelectorAll('[data-select]:checked'))
        .map(cb => cb.dataset.id);
    }

    function computeSelectedTotal() {
      let total = 0;

      document.querySelectorAll('.cart-row').forEach(row => {
        const id = row.dataset.rowId;
        const checked = document.querySelector(`[data-select][data-id="${id}"]`)?.checked;
        if (!checked) return;

        const price = parseInt(row.dataset.price || '0', 10);
        const qtyInput = document.querySelector(`[data-qty][data-id="${id}"]`);
        const qty = parseInt(qtyInput?.value || '1', 10);

        total += price * qty;
      });

      return total;
    }

    function refreshSelectedSummary() {
      const total = computeSelectedTotal();
      setGrand(total);

      const ids = getSelectedIds();
      const btn = document.getElementById('checkoutBtn');
      const hint = document.getElementById('selectedHint');

      const allCbs = Array.from(document.querySelectorAll('[data-select]'));
      const allChecked = allCbs.length ? allCbs.every(x => x.checked) : false;

      const selectAll = document.getElementById('selectAll');
      if (selectAll) selectAll.checked = allChecked;

      if (btn) {
        btn.disabled = ids.length === 0;
        btn.classList.toggle('opacity-50', ids.length === 0);
        btn.classList.toggle('cursor-not-allowed', ids.length === 0);
      }

      if (hint) {
        hint.textContent = ids.length
          ? `* ${ids.length} item dipilih untuk checkout.`
          : `* Tidak ada item yang dipilih.`;
      }
    }

    // Pilih semua
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
      selectAll.addEventListener('change', () => {
        const checked = selectAll.checked;
        document.querySelectorAll('[data-select]').forEach(cb => cb.checked = checked);
        refreshSelectedSummary();
      });
    }

    // Checkbox per item
    document.querySelectorAll('[data-select]').forEach(cb => {
      cb.addEventListener('change', () => refreshSelectedSummary());
    });

    // Checkout -> pindah ke halaman checkout + bawa ids
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        const ids = getSelectedIds();
        if (!ids.length) {
          toast("⚠️ Pilih minimal 1 item untuk checkout");
          return;
        }

        const url = "/checkout?ids=" + encodeURIComponent(ids.join(','));
        window.location.href = url;
      });
    }

    // initial
    refreshSelectedSummary();
  </script>
</body>
</html>
