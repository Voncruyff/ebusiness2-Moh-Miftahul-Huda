<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan - SNV POS</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    * { font-family: 'Inter', sans-serif; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
  </style>
</head>

<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

  @include('dashboard.adminsidebar')

  <div class="flex-1 flex flex-col overflow-hidden lg:ml-64 transition-all duration-300">

    <header class="bg-white shadow-sm z-30 sticky top-0">
      <div class="flex items-center justify-between px-6 py-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Laporan</h1>
          <p class="text-sm text-gray-500">Ringkasan penjualan, produk terlaris, dan grafik omzet</p>
        </div>

        <div class="flex items-center gap-4">
          <img class="h-10 w-10 rounded-full border-2 border-purple-500"
               src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=667eea&color=fff"
               alt="Avatar">
        </div>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-4">

      {{-- FILTER --}}
      <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

          <form method="GET" action="{{ route('admin.reports') }}" class="flex flex-col md:flex-row gap-3 flex-1">
            <div>
              <label class="text-xs font-semibold text-gray-600">Mulai</label>
              <input type="date" name="start" value="{{ $start }}"
                     class="mt-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Sampai</label>
              <input type="date" name="end" value="{{ $end }}"
                     class="mt-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
            </div>

            <div class="flex items-end">
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-bold text-white btn-primary hover:opacity-95 transition">
                <i class="fas fa-filter"></i> Terapkan
              </button>
            </div>
          </form>

          {{-- Quick range --}}
          <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.reports', ['quick' => 'day']) }}"
               class="rounded-lg border bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
              Hari ini
            </a>
            <a href="{{ route('admin.reports', ['quick' => '7d']) }}"
               class="rounded-lg border bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
              7 hari
            </a>
            <a href="{{ route('admin.reports', ['quick' => '30d']) }}"
               class="rounded-lg border bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
              30 hari
            </a>
            <a href="{{ route('admin.reports', ['quick' => 'month']) }}"
               class="rounded-lg border bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
              Bulan ini
            </a>
          </div>
        </div>
      </div>

      {{-- KPI --}}
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
          <p class="text-xs font-semibold text-gray-500">Omzet (PAID)</p>
          <p class="text-xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
          <p class="text-xs font-semibold text-gray-500">Transaksi (PAID)</p>
          <p class="text-xl font-extrabold text-gray-900 mt-1">{{ $totalOrders }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
          <p class="text-xs font-semibold text-gray-500">Item Terjual</p>
          <p class="text-xl font-extrabold text-gray-900 mt-1">{{ $totalItemsSold }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
          <p class="text-xs font-semibold text-gray-500">Rata-rata Transaksi</p>
          <p class="text-xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($avgOrder,0,',','.') }}</p>
        </div>
      </div>

      {{-- Kalau kosong --}}
      @if($totalOrders === 0)
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-yellow-800 text-sm">
          Data PAID kosong di range <b>{{ $start }}</b> s/d <b>{{ $end }}</b>.
          Coba klik quick range <b>Bulan ini</b> atau ubah tanggal.
        </div>
      @endif

      {{-- Charts --}}
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white rounded-xl shadow border border-gray-100 p-5">
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-gray-800">Omzet per Hari</p>
            <p class="text-xs text-gray-500">{{ $start }} s/d {{ $end }}</p>
          </div>
          <div class="h-[280px]">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
          <p class="text-sm font-bold text-gray-800 mb-3">Top Produk Terlaris</p>
          <div class="h-[280px]">
            <canvas id="topProductChart"></canvas>
          </div>
        </div>
      </div>

      {{-- Top products table --}}
      <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
          <p class="text-sm font-bold text-gray-800">Detail Produk Terlaris</p>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
              <tr class="text-left text-xs font-bold text-gray-600">
                <th class="px-6 py-3">Produk</th>
                <th class="px-6 py-3 text-right">Qty</th>
                <th class="px-6 py-3 text-right">Omzet</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($topProducts as $p)
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 font-semibold text-gray-900">{{ $p->name }}</td>
                  <td class="px-6 py-4 text-right font-bold">{{ (int)$p->qty_sold }}</td>
                  <td class="px-6 py-4 text-right font-extrabold">
                    Rp {{ number_format((int)$p->omzet,0,',','.') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                    Belum ada data produk terlaris.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
  const labels = @json($labels);
  const revenueData = @json($seriesRevenue);
  const barLabels = @json($barLabels);
  const barQty = @json($barQty);

  const rupiah = (n) => new Intl.NumberFormat('id-ID').format(n);

  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels,
      datasets: [{ label: 'Omzet', data: revenueData, tension: 0.35 }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        tooltip: { callbacks: { label: (ctx) => `Rp ${rupiah(ctx.parsed.y)}` } },
        legend: { display: false }
      },
      scales: { y: { ticks: { callback: (v) => 'Rp ' + rupiah(v) } } }
    }
  });

  new Chart(document.getElementById('topProductChart'), {
    type: 'bar',
    data: { labels: barLabels, datasets: [{ label: 'Qty', data: barQty }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });
</script>

</body>
</html>
