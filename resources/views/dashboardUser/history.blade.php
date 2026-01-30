<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat - SNV Pos</title>

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
            <h1 class="text-lg font-extrabold text-gray-800">History</h1>
          </div>
        </div>
      </header>

      <!-- MAIN -->
      <main class="px-6 lg:px-10 py-8">
        <div class="mb-5">
          <h2 class="text-xl font-extrabold text-gray-900">Riwayat</h2>
          <p class="text-sm text-gray-500">Daftar order yang pernah kamu buat.</p>
        </div>

        <!-- FILTER BAR (sticky biar gak ikut scroll) -->
        <div class="sticky top-16 z-20 mb-4">
          <div class="rounded-2xl bg-white shadow border border-gray-100 p-4">
            <form method="GET" action="{{ route('user.history') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">

              <!-- Search -->
              <div class="lg:col-span-6">
                <label class="text-xs font-semibold text-gray-600">Cari (invoice / nama / hp)</label>
                <div class="relative mt-1">
                  <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-magnifying-glass"></i>
                  </span>
                  <input
                    name="q"
                    value="{{ $q }}"
                    placeholder="Contoh: INV-2026..., Huda, 08xxx"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                  />
                </div>
              </div>

              <!-- Status -->
              <div class="lg:col-span-3">
                <label class="text-xs font-semibold text-gray-600">Status</label>
                <select
                  name="status"
                  class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                >
                  <option value="" {{ $status==='' ? 'selected':'' }}>Semua</option>
                  <option value="UNPAID" {{ $status==='UNPAID' ? 'selected':'' }}>UNPAID</option>
                  <option value="PAID" {{ $status==='PAID' ? 'selected':'' }}>PAID</option>
                </select>
              </div>

              <!-- Sort -->
              <div class="lg:col-span-3">
                <label class="text-xs font-semibold text-gray-600">Urutkan</label>
                <select
                  name="sort"
                  class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                >
                  <option value="new" {{ $sort==='new' ? 'selected':'' }}>Terbaru</option>
                  <option value="old" {{ $sort==='old' ? 'selected':'' }}>Terlama</option>
                </select>
              </div>

              <div class="lg:col-span-12 flex gap-2">
                <button type="submit"
                        class="rounded-xl px-4 py-2 text-sm font-bold text-white btn-primary hover:opacity-95">
                  <i class="fas fa-filter mr-2"></i> Terapkan
                </button>

                <a href="{{ route('user.history') }}"
                   class="rounded-xl px-4 py-2 text-sm font-bold border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                  Reset
                </a>
              </div>
            </form>
          </div>
        </div>

        <!-- LIST -->
        <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b flex items-center justify-between">
            <p class="text-sm font-bold text-gray-800">Daftar Order</p>
            <p class="text-xs text-gray-500">
              {{ $orders->total() }} order
            </p>
          </div>

          @if($orders->count() === 0)
            <div class="p-8 text-center text-gray-500">
              Belum ada riwayat transaksi.
            </div>
          @else
            <div class="divide-y">
              @foreach($orders as $o)
                @php
                  $cnt = (int) ($itemsCount[$o->id] ?? 0);
                  $sumSubtotal = (int) ($itemsSum[$o->id] ?? 0);

                  $badgeClass = $o->status === 'PAID'
                    ? 'bg-green-50 text-green-700 border-green-200'
                    : 'bg-yellow-50 text-yellow-700 border-yellow-200';

                  $method = $o->payment_method ?? '-';
                @endphp

                <div class="p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                  <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                      <p class="font-extrabold text-gray-900">
                        {{ $o->invoice }}
                      </p>

                      <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-bold {{ $badgeClass }}">
                        {{ $o->status }}
                      </span>

                      <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold bg-gray-50 text-gray-700 border-gray-200">
                        {{ strtoupper($method) }}
                      </span>
                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                      <i class="fas fa-calendar mr-1"></i>
                      {{ \Carbon\Carbon::parse($o->created_at)->format('d M Y, H:i') }}
                      <span class="mx-2">•</span>
                      <i class="fas fa-box mr-1"></i> {{ $cnt }} item
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                      Nama: <span class="font-semibold text-gray-700">{{ $o->buyer_name ?? '-' }}</span>
                      <span class="mx-2">•</span>
                      HP: <span class="font-semibold text-gray-700">{{ $o->buyer_phone ?? '-' }}</span>
                    </p>
                  </div>

                  <div class="flex items-center gap-3 justify-between lg:justify-end">
                    <div class="text-right">
                      <p class="text-xs text-gray-500">Total</p>
                      <p class="text-lg font-extrabold text-purple-600 whitespace-nowrap">
                        Rp {{ number_format((int)$o->total, 0, ',', '.') }}
                      </p>
                    </div>

                    <div class="flex gap-2">
                      @if($o->status === 'UNPAID')
                        <a href="{{ route('payment.show', $o->id) }}"
                           class="rounded-xl px-3 py-2 text-sm font-bold text-white btn-primary hover:opacity-95">
                          <i class="fas fa-wallet mr-2"></i> Bayar
                        </a>
                      @else
                        <a href="{{ route('payment.success', $o->id) }}"
                           class="rounded-xl px-3 py-2 text-sm font-bold border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                          <i class="fas fa-receipt mr-2"></i> Detail
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="px-6 py-4 border-t">
              {{ $orders->links() }}
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>
</body>
</html>
