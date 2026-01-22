<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>History Transaksi</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * { font-family: 'Inter', sans-serif; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

    /* ✅ sticky filter card tepat di bawah header */
    .filterbar {
      position: sticky;
      top: 72px; /* kira2 tinggi header */
      z-index: 20;
    }
  </style>
</head>

<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

  {{-- SIDEBAR --}}
  @include('dashboard.adminsidebar')

  {{-- MAIN CONTENT (SAMA KAYAK PRODUK) --}}
  <div class="flex-1 flex flex-col overflow-hidden lg:ml-64 transition-all duration-300">

    {{-- HEADER (SAMA KAYAK PRODUK) --}}
    <header class="bg-white shadow-sm z-30 sticky top-0">
      <div class="flex items-center justify-between px-6 py-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">History Transaksi</h1>
          <p class="text-sm text-gray-500">Lihat seluruh riwayat transaksi dan invoice</p>
        </div>

        <div class="flex items-center gap-4">
          <button class="relative text-gray-600 hover:text-gray-800">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              3
            </span>
          </button>

          <img class="h-10 w-10 rounded-full border-2 border-purple-500"
               src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=667eea&color=fff"
               alt="Avatar">
        </div>
      </div>
    </header>

    {{-- MAIN SCROLL AREA --}}
    <main class="flex-1 overflow-y-auto p-6 space-y-3">

      {{-- ✅ FILTER + TABLE DIGABUNG (biar thead nempel) --}}
      <div class="filterbar">
        <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">

          {{-- FILTER (lebih rapat) --}}
          <div class="p-5 border-b">
            <div class="mb-3">
              <p class="text-sm font-bold text-gray-800">Cari Invoice</p>
              <p class="text-xs text-gray-500">Cari cepat transaksi berdasarkan invoice, pelanggan, atau kasir.</p>
            </div>

            <form method="GET" action="{{ route('admin.history') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-3">
              <div class="lg:col-span-5">
                <label class="text-xs font-semibold text-gray-600">Search</label>
                <div class="mt-1 relative">
                  <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="INV-xxxx / nama / telepon / kasir"
                    class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                  />
                </div>
              </div>

              <div class="lg:col-span-2">
                <label class="text-xs font-semibold text-gray-600">Status</label>
                <select
                  name="status"
                  class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                >
                  <option value="">Semua</option>
                  <option value="PAID" @selected($status === 'PAID')>PAID</option>
                  <option value="UNPAID" @selected($status === 'UNPAID')>UNPAID</option>
                </select>
              </div>

              <div class="lg:col-span-2">
                <label class="text-xs font-semibold text-gray-600">Metode</label>
                <select
                  name="payment_method"
                  class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                >
                  <option value="">Semua</option>
                  <option value="cash" @selected($method === 'cash')>Cash</option>
                  <option value="bank" @selected($method === 'bank')>Bank</option>
                  <option value="ewallet" @selected($method === 'ewallet')>E-Wallet</option>
                </select>
              </div>

              <div class="lg:col-span-2">
                <label class="text-xs font-semibold text-gray-600">Urutkan</label>
                <select
                  name="sort"
                  class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                >
                  <option value="new" @selected($sort === 'new')>Terbaru</option>
                  <option value="old" @selected($sort === 'old')>Terlama</option>
                </select>
              </div>

              <div class="lg:col-span-1 flex items-end">
                <button
                  type="submit"
                  class="w-full inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-bold text-white btn-primary hover:opacity-95 transition"
                >
                  <i class="fas fa-filter"></i>
                </button>
              </div>

              <div class="lg:col-span-12 flex justify-between items-center pt-1">
                <div class="text-xs text-gray-500">
                  Total data: <b class="text-gray-800">{{ $orders->total() }}</b>
                </div>

                <a
                  href="{{ route('admin.history') }}"
                  class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                >
                  <i class="fas fa-rotate-left"></i> Reset
                </a>
              </div>
            </form>
          </div>

          {{-- TABLE (thead nempel & sticky) --}}
          <div class="overflow-x-auto max-h-[62vh] overflow-y-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50 border-b sticky top-0 z-10">
                <tr class="text-left text-xs font-bold text-gray-600">
                  <th class="px-6 py-3">Waktu</th>
                  <th class="px-6 py-3">Invoice</th>
                  <th class="px-6 py-3">Kasir</th>
                  <th class="px-6 py-3 text-center">Item</th>
                  <th class="px-6 py-3">Metode</th>
                  <th class="px-6 py-3 text-center">Status</th>
                  <th class="px-6 py-3 text-right">Total</th>
                  <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
              </thead>

              <tbody class="divide-y">
                @forelse($orders as $o)
                  @php
                    $badge = match($o->status){
                      'PAID' => 'bg-green-50 text-green-700 border-green-200',
                      default => 'bg-red-50 text-red-700 border-red-200',
                    };
                    $itemQty = $itemsCount[$o->id] ?? 0;
                  @endphp

                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                      {{ \Carbon\Carbon::parse($o->created_at)->format('d M Y H:i') }}
                    </td>

                    <td class="px-6 py-4">
                      <div class="font-extrabold text-gray-900">{{ $o->invoice }}</div>
                      <div class="text-xs text-gray-500">Order ID: {{ $o->id }}</div>
                    </td>

                    <td class="px-6 py-4">
                      <div class="font-semibold text-gray-900">{{ $o->cashier_name ?? '-' }}</div>
                      <div class="text-xs text-gray-500">{{ $o->cashier_email ?? '-' }}</div>
                    </td>

                    <td class="px-6 py-4 text-center">
                      <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold bg-gray-50 text-gray-700 border-gray-200">
                        {{ $itemQty }}
                      </span>
                    </td>

                    <td class="px-6 py-4 text-gray-700">
                      {{ $o->payment_method ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-center">
                      <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $badge }}">
                        {{ $o->status }}
                      </span>
                    </td>

                    <td class="px-6 py-4 text-right font-extrabold text-gray-900 whitespace-nowrap">
                      Rp {{ number_format((int)$o->total,0,',','.') }}
                    </td>

                    <td class="px-6 py-4 text-right whitespace-nowrap">
                      <a
                        href="{{ route('admin.orders.show', $o->id) }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100"
                      >
                        <i class="fas fa-eye"></i> Detail
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                      Tidak ada transaksi ditemukan.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- PAGINATION (di luar scroll tabel) --}}
          <div class="px-6 py-4 border-t">
            {{ $orders->links() }}
          </div>

        </div>
      </div>

    </main>
  </div>
</div>
</body>
</html>
