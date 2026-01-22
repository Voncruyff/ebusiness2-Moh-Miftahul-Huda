<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Invoice {{ $order->invoice }}</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * { font-family: 'Inter', sans-serif; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    :root { --sidebar-w: 288px; }
    .content-with-sidebar { padding-left: var(--sidebar-w); }
    @media (max-width: 1024px) { .content-with-sidebar { padding-left: 0; } }

    @media print {
      .no-print { display: none !important; }
      .content-with-sidebar { padding-left: 0 !important; }
      body { background: #fff !important; }
      .paper { box-shadow: none !important; border: none !important; }
    }
  </style>
</head>

<body class="bg-gray-50">
<div class="min-h-screen">

  {{-- Sidebar Admin --}}
  <div class="no-print">
    @include('dashboard.adminsidebar')
  </div>

  <div class="content-with-sidebar">
    <header class="no-print sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
      <div class="px-6 lg:px-10">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 shadow-sm">
              <i class="fas fa-file-invoice text-white text-sm"></i>
            </div>
            <div>
              <h1 class="text-sm font-bold text-gray-800">Invoice</h1>
              <p class="text-xs text-gray-500">{{ $order->invoice }}</p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <a href="{{ route('admin.history') }}"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <button onclick="window.print()"
              class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-bold text-white btn-primary hover:opacity-95">
              <i class="fas fa-print"></i> Print
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="px-6 lg:px-10 py-8">
      <div class="max-w-4xl mx-auto">

        {{-- CARD TOP (mirip Pembayaran Berhasil) --}}
        <div class="paper rounded-3xl bg-white shadow border border-gray-100 overflow-hidden">
          <div class="p-6 border-b flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
              <div class="h-11 w-11 rounded-2xl bg-green-50 flex items-center justify-center text-green-600">
                <i class="fas fa-check"></i>
              </div>
              <div>
                <p class="text-lg font-extrabold text-gray-900">Invoice</p>
                <p class="text-sm text-gray-500">
                  Transaksi tersimpan • Status:
                  <span class="font-bold {{ $order->status === 'PAID' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $order->status }}
                  </span>
                </p>
              </div>
            </div>

            <div class="no-print flex items-center gap-2">
              <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-print"></i> Print
              </button>
            </div>
          </div>

          {{-- RECEIPT BODY --}}
          <div class="p-6">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-xs text-gray-500">SNV POS</p>
                <p class="text-xs text-gray-400">Bukti Pembayaran / Receipt</p>
              </div>
              <div class="text-right">
                <p class="text-[11px] text-gray-400">INVOICE</p>
                <p class="text-sm font-extrabold text-gray-900">{{ $order->invoice }}</p>
              </div>
            </div>

            {{-- INFO GRID --}}
            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="rounded-2xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Tanggal</p>
                <p class="text-sm font-bold text-gray-900">
                  {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y • H:i') }}
                </p>
                <div class="mt-3">
                  <p class="text-xs text-gray-500">Metode Pembayaran</p>
                  <p class="text-sm font-bold text-gray-900">{{ ucfirst($order->payment_method ?? '-') }}</p>
                </div>
              </div>

              <div class="rounded-2xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Kasir</p>
                <p class="text-sm font-bold text-gray-900">{{ $order->cashier_name ?? '-' }}</p>
                <div class="mt-3">
                  <p class="text-xs text-gray-500">ID Kasir</p>
                  <p class="text-sm font-bold text-gray-900">#{{ $order->user_id ?? '-' }}</p>
                </div>
              </div>
            </div>

            {{-- ITEMS --}}
            <div class="mt-6">
              <div class="flex items-center justify-between">
                <p class="text-sm font-extrabold text-gray-900">Detail Item</p>
                <p class="text-xs text-gray-500">{{ $items->count() }} item</p>
              </div>

              <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200">
                <table class="min-w-full text-sm">
                  <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-xs font-bold text-gray-600">
                      <th class="px-4 py-3">Item</th>
                      <th class="px-4 py-3 text-center">Qty</th>
                      <th class="px-4 py-3 text-right">Harga</th>
                      <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y">
                    @foreach($items as $it)
                      <tr>
                        <td class="px-4 py-3">
                          <div class="font-semibold text-gray-900">{{ $it->name }}</div>
                          @if(!empty($it->unit))
                            <div class="text-xs text-gray-500">Satuan: {{ $it->unit }}</div>
                          @endif
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">{{ (int)$it->qty }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format((int)$it->price,0,',','.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-600">
                          Rp {{ number_format((int)$it->subtotal,0,',','.') }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            {{-- SUMMARY + PAYMENT --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="rounded-2xl border border-gray-200 p-4">
                <p class="text-sm font-extrabold text-gray-900">Ringkasan</p>
                <div class="mt-3 space-y-2 text-sm">
                  <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format((int)$subtotal,0,',','.') }}</span>
                  </div>
                  <div class="flex justify-between text-gray-600">
                    <span>Biaya layanan</span>
                    <span class="font-semibold text-gray-900">Rp 0</span>
                  </div>
                  <div class="border-t pt-3 flex justify-between items-center">
                    <span class="font-extrabold text-gray-900">TOTAL</span>
                    <span class="font-extrabold text-gray-900">Rp {{ number_format((int)$total,0,',','.') }}</span>
                  </div>
                </div>
              </div>

              <div class="rounded-2xl border border-gray-200 p-4">
                <p class="text-sm font-extrabold text-gray-900">Pembayaran</p>
                <div class="mt-3 space-y-2 text-sm">
                  <div class="flex justify-between text-gray-600">
                    <span>Metode</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($order->payment_method ?? '-') }}</span>
                  </div>
                  <div class="flex justify-between text-gray-600">
                    <span>Uang diterima</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format((int)$paidAmount,0,',','.') }}</span>
                  </div>
                  <div class="flex justify-between text-gray-600">
                    <span>Kembalian</span>
                    <span class="font-semibold {{ $changeAmount > 0 ? 'text-green-600' : 'text-gray-900' }}">
                      Rp {{ number_format((int)$changeAmount,0,',','.') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-500">
              Terima kasih • Simpan struk ini sebagai bukti pembayaran
            </div>

            <div class="no-print mt-6 flex flex-wrap gap-3 justify-center">
              <a href="{{ route('admin.history') }}"
                 class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-list"></i> Kembali ke History
              </a>
            </div>

          </div>
        </div>

      </div>
    </main>
  </div>
</div>
</body>
</html>
