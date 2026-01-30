<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pembayaran - SNV Pos</title>

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

    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
      <div class="px-6 lg:px-10">
        <div class="flex h-16 items-center justify-between">

          <div>
            <h1 class="text-base font-extrabold text-gray-800">Pembayaran</h1>
            <p class="text-xs text-gray-500">Selesaikan transaksi</p>
          </div>

          <a href="{{ route('user.dashboard') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
            <i class="fas fa-house"></i> Dashboard
          </a>

        </div>
      </div>
    </header>


    <main class="px-6 lg:px-10 py-8">

      @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-red-800">
          <p class="font-semibold mb-1"><i class="fas fa-triangle-exclamation mr-2"></i>Terjadi error</p>
          <ul class="text-sm list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT -->
        <div class="lg:col-span-2 space-y-4">

          <!-- Metode Pembayaran -->
          <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b">
              <p class="text-sm font-bold text-gray-800">Metode Pembayaran</p>
              <p class="text-xs text-gray-500">Pilih metode, lalu konfirmasi pembayaran.</p>
            </div>

            <form id="payForm" method="POST" action="{{ route('payment.pay') }}" class="p-6 space-y-4">
              @csrf
              <input type="hidden" name="order_id" value="{{ $order->id }}">

              <label class="flex items-center justify-between gap-3 rounded-xl border p-4 cursor-pointer hover:bg-gray-50">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">Tunai</p>
                    <p class="text-xs text-gray-500">Input uang diterima, hitung kembalian otomatis.</p>
                  </div>
                </div>
                <input id="pm_cash" type="radio" name="payment_method" value="cash" class="h-4 w-4" checked>
              </label>

              <label class="flex items-center justify-between gap-3 rounded-xl border p-4 cursor-pointer hover:bg-gray-50">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-building-columns"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">Transfer Bank</p>
                    <p class="text-xs text-gray-500">Konfirmasi manual oleh kasir.</p>
                  </div>
                </div>
                <input id="pm_bank" type="radio" name="payment_method" value="bank" class="h-4 w-4">
              </label>

              <label class="flex items-center justify-between gap-3 rounded-xl border p-4 cursor-pointer hover:bg-gray-50">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fas fa-mobile-screen-button"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">E-Wallet / QRIS</p>
                    <p class="text-xs text-gray-500">Konfirmasi manual oleh kasir (offline POS).</p>
                  </div>
                </div>
                <input id="pm_ewallet" type="radio" name="payment_method" value="ewallet" class="h-4 w-4">
              </label>

              <!-- CASH INPUT -->
              <div id="cashBox" class="rounded-xl border bg-gray-50 p-4">
                <p class="text-sm font-semibold text-gray-800 mb-3">Pembayaran Tunai</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="text-xs font-semibold text-gray-600">Total Tagihan</label>
                    <div class="mt-1 rounded-xl bg-white border px-3 py-2 font-extrabold text-gray-900">
                      Rp <span id="totalTagihan">{{ number_format($order->total,0,',','.') }}</span>
                    </div>
                  </div>

                  <div>
                    <label class="text-xs font-semibold text-gray-600">Uang diterima</label>
                    <input
                      id="paidAmount"
                      name="paid_amount"
                      type="number"
                      min="0"
                      placeholder="contoh: 50000"
                      class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                    >
                    <p class="text-xs text-gray-500 mt-1">Isi hanya jika metode = Tunai.</p>
                  </div>

                  <div class="md:col-span-2">
                    <div class="flex items-center justify-between rounded-xl bg-white border px-4 py-3">
                      <span class="text-sm font-semibold text-gray-700">Kembalian</span>
                      <span class="text-lg font-extrabold text-purple-600">
                        Rp <span id="changeAmount">0</span>
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Quick buttons -->
                <div class="mt-3 flex flex-wrap gap-2">
                  @php
                    $quick = [2000,5000,10000,20000,50000,100000];
                  @endphp
                  @foreach($quick as $q)
                    <button type="button"
                      class="rounded-xl border bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100"
                      data-quick="{{ $q }}">
                      +{{ number_format($q,0,',','.') }}
                    </button>
                  @endforeach
                  <button type="button"
                    class="rounded-xl border bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100"
                    id="exactBtn">
                    Pas
                  </button>
                </div>
              </div>

              <button type="submit"
                class="w-full rounded-xl px-4 py-3 text-sm font-bold text-white btn-primary hover:opacity-95 transition">
                <i class="fas fa-check mr-2"></i> Konfirmasi Pembayaran
              </button>
            </form>
          </div>

          <!-- Item Pesanan -->
          <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between">
              <p class="text-sm font-bold text-gray-800">Item Pesanan</p>
              <p class="text-xs text-gray-500">{{ $items->count() }} item</p>
            </div>

            <div class="divide-y">
              @foreach($items as $it)
                <div class="p-6 flex justify-between">
                  <div>
                    <p class="font-semibold text-gray-900">{{ $it->name }}</p>
                    <p class="text-xs text-gray-500">Qty {{ $it->qty }} • Rp {{ number_format($it->price,0,',','.') }}</p>
                  </div>
                  <div class="font-extrabold text-purple-600">
                    Rp {{ number_format($it->subtotal,0,',','.') }}
                  </div>
                </div>
              @endforeach
            </div>
          </div>

        </div>

        <!-- RIGHT -->
        <aside class="rounded-2xl bg-white shadow border border-gray-100 h-fit sticky top-24 overflow-hidden">
          <div class="px-6 py-4 border-b">
            <p class="text-sm font-bold text-gray-800">Ringkasan</p>
          </div>

          <div class="px-6 py-5 space-y-3">
            <p class="text-sm text-gray-700"><b>Invoice:</b> {{ $order->invoice }}</p>
            <p class="text-sm text-gray-700"><b>Status:</b> {{ $order->status }}</p>

            <div class="border-t pt-3 flex justify-between">
              <span class="text-gray-600 font-semibold">Total</span>
              <span class="text-xl font-extrabold text-gray-900">Rp {{ number_format($order->total,0,',','.') }}</span>
            </div>
          </div>
        </aside>

      </div>
    </main>
  </div>
</div>

<script>
  const total = {{ (int) $order->total }};
  const paid = document.getElementById('paidAmount');
  const changeEl = document.getElementById('changeAmount');
  const cashBox = document.getElementById('cashBox');

  function rupiah(n){
    return new Intl.NumberFormat('id-ID').format(n);
  }

  function recalcChange(){
    const p = parseInt(paid.value || '0', 10);
    const ch = Math.max(0, p - total);
    changeEl.textContent = rupiah(ch);
  }

  function getSelectedMethod(){
    return document.querySelector('input[name="payment_method"]:checked')?.value;
  }

  function toggleCashBox(){
    const method = getSelectedMethod();
    const isCash = method === 'cash';
    cashBox.classList.toggle('hidden', !isCash);

    // kalau bukan cash, kosongkan input supaya tidak mengganggu validasi
    if (!isCash) {
      paid.value = '';
      changeEl.textContent = '0';
    }
  }

  // change method
  document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener('change', toggleCashBox);
  });

  // input change
  paid.addEventListener('input', recalcChange);

  // quick buttons
  document.querySelectorAll('[data-quick]').forEach(btn => {
    btn.addEventListener('click', () => {
      const add = parseInt(btn.dataset.quick || '0', 10);
      const cur = parseInt(paid.value || '0', 10);
      paid.value = String(cur + add);
      recalcChange();
    });
  });

  // exact
  document.getElementById('exactBtn').addEventListener('click', () => {
    paid.value = String(total);
    recalcChange();
  });

  // initial
  toggleCashBox();
  recalcChange();
</script>
</body>
</html>
