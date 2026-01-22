<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SNV Pos - Dashboard Admin</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    * { font-family: 'Inter', sans-serif; }
    .hover-scale{ transition: transform .2s ease; }
    .hover-scale:hover{ transform: translateY(-2px); }
    .gradient-primary{ background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); }
    .gradient-success{ background: linear-gradient(135deg,#f093fb 0%,#f5576c 100%); }
    .gradient-info{ background: linear-gradient(135deg,#4facfe 0%,#00f2fe 100%); }
    .gradient-warning{ background: linear-gradient(135deg,#fa709a 0%,#fee140 100%); }
  </style>
</head>

<body class="bg-gray-50">

  @include('dashboard.adminsidebar')

  <div class="min-h-screen lg:ml-64 ml-0">
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
      <div class="flex items-center justify-between px-6 lg:px-8 py-4">
        <div class="flex items-center space-x-4">
          <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-bars text-xl"></i>
          </button>

          <div>
            <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
            <p class="text-sm text-gray-500">
              Selamat datang kembali, {{ auth()->user()->name ?? 'Admin' }}!
            </p>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <button class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-red-500"></span>
          </button>
          <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
          </div>
        </div>
      </div>
    </header>

    <main class="p-6 lg:p-8">

      {{-- STATS --}}
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">

        {{-- Income --}}
        <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pemasukan Bulan Ini</p>
              <h3 class="mt-2 text-2xl font-extrabold text-gray-900">
                Rp {{ number_format($incomeThisMonth ?? 0, 0, ',', '.') }}
              </h3>
              <p class="mt-2 text-xs text-gray-500">Hanya status <b>PAID</b></p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-primary">
              <i class="fas fa-dollar-sign text-2xl text-white"></i>
            </div>
          </div>
        </div>

        {{-- Transactions --}}
        <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Transaksi Bulan Ini</p>
              <h3 class="mt-2 text-2xl font-extrabold text-gray-900">{{ number_format($trxThisMonth ?? 0) }}</h3>

              <div class="mt-2 text-xs text-gray-600 space-y-1">
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-green-600">PAID</span>
                  <span class="font-bold">{{ number_format($paidCount ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-yellow-600">UNPAID</span>
                  <span class="font-bold">{{ number_format($unpaidCount ?? 0) }}</span>
                </div>
              </div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-success">
              <i class="fas fa-shopping-bag text-2xl text-white"></i>
            </div>
          </div>
        </div>

        {{-- Sold --}}
        <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Produk Terjual Bulan Ini</p>
              <h3 class="mt-2 text-2xl font-extrabold text-gray-900">{{ number_format($soldThisMonth ?? 0) }}</h3>
              <p class="mt-2 text-xs text-gray-500">Qty dari order <b>PAID</b></p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-info">
              <i class="fas fa-box text-2xl text-white"></i>
            </div>
          </div>
        </div>

        {{-- Cashiers --}}
        <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Jumlah Kasir</p>
              <h3 class="mt-2 text-2xl font-extrabold text-gray-900">{{ number_format($cashiersCount ?? 0) }}</h3>
              <p class="mt-2 text-xs text-gray-500">Role: <b>user</b></p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-warning">
              <i class="fas fa-users text-2xl text-white"></i>
            </div>
          </div>
        </div>

      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- sales chart --}}
        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800">Grafik Penjualan (7 Hari)</h3>
            <p class="text-sm text-gray-500">Total order PAID per hari</p>
          </div>
          <div class="h-72">
            <canvas id="salesChart"></canvas>
          </div>
        </div>

        {{-- low stock --}}
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Stok Menipis</h3>
            <p class="text-sm text-gray-500">5 produk stok terendah</p>
          </div>

          <div class="space-y-3">
            @forelse($lowStock as $p)
              <div class="flex items-center justify-between rounded-xl border border-gray-100 p-3">
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-gray-800 truncate">{{ $p->name ?? 'Produk' }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ $p->category ?? '-' }}</p>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-500">Stok</p>
                  <p class="text-sm font-extrabold {{ ((int)($p->stock ?? 0) <= 5) ? 'text-red-600' : 'text-gray-800' }}">
                    {{ (int)($p->stock ?? 0) }}
                  </p>
                </div>
              </div>
            @empty
              <p class="text-sm text-gray-500">Belum ada data produk.</p>
            @endforelse
          </div>
        </div>

      </div>

      {{-- top products --}}
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Produk Terlaris (Bulan Ini)</h3>
            <p class="text-sm text-gray-500">Top 5 berdasarkan qty (PAID)</p>
          </div>
          <div class="h-72">
            <canvas id="topProductChart"></canvas>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100">
          <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Ringkasan Cepat</h3>
            <p class="text-sm text-gray-500">Info paling kepake</p>
          </div>

          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Pemasukan bulan ini</span>
              <span class="font-extrabold text-gray-900">Rp {{ number_format($incomeThisMonth ?? 0,0,',','.') }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">PAID</span>
              <span class="font-extrabold text-gray-900">{{ number_format($paidCount ?? 0) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">UNPAID</span>
              <span class="font-extrabold text-gray-900">{{ number_format($unpaidCount ?? 0) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Produk terjual (qty)</span>
              <span class="font-extrabold text-gray-900">{{ number_format($soldThisMonth ?? 0) }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- recent orders --}}
      <div class="mt-6 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
        <div class="mb-6">
          <h3 class="text-lg font-bold text-gray-800">Transaksi Terbaru</h3>
          <p class="text-sm text-gray-500">10 transaksi terakhir</p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Invoice</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Kasir</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Total</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Status</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
              @forelse($recentOrders as $o)
                <tr class="hover:bg-gray-50">
                  <td class="py-4 text-sm font-semibold text-gray-800">#{{ $o->invoice }}</td>
                  <td class="py-4 text-sm text-gray-600">{{ $o->user?->name ?? '-' }}</td>
                  <td class="py-4 text-sm font-extrabold text-gray-900">
                    Rp {{ number_format($o->total ?? 0, 0, ',', '.') }}
                  </td>
                  <td class="py-4">
                    @if($o->status === 'PAID')
                      <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">PAID</span>
                    @else
                      <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">UNPAID</span>
                    @endif
                  </td>
                  <td class="py-4 text-sm text-gray-600">{{ optional($o->created_at)->format('d M Y H:i') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-6 text-center text-sm text-gray-500">Belum ada transaksi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n || 0);

      const salesLabels = @json($chartLabels ?? []);
      const salesData   = @json($chartData ?? []);

      const salesCanvas = document.getElementById('salesChart');
      if (salesCanvas && typeof Chart !== 'undefined') {
        new Chart(salesCanvas.getContext('2d'), {
          type: 'line',
          data: {
            labels: salesLabels,
            datasets: [{
              label: 'Total Penjualan',
              data: salesData,
              tension: 0.35,
              fill: true,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              tooltip: { callbacks: { label: (ctx) => rupiah(ctx.parsed.y) } }
            },
            scales: {
              y: { ticks: { callback: (value) => rupiah(value) } }
            }
          }
        });
      }

      const topLabels = @json($topLabels ?? []);
      const topQty    = @json($topQty ?? []);

      const topCanvas = document.getElementById('topProductChart');
      if (topCanvas && typeof Chart !== 'undefined') {
        new Chart(topCanvas.getContext('2d'), {
          type: 'bar',
          data: {
            labels: topLabels,
            datasets: [{ label: 'Qty Terjual', data: topQty }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
          }
        });
      }

      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebarElement = document.getElementById('sidebar');
      if (sidebarToggle && sidebarElement) {
        sidebarToggle.addEventListener('click', () => {
          sidebarElement.classList.toggle('-translate-x-full');
        });
      }
    });
  </script>
</body>
</html>
