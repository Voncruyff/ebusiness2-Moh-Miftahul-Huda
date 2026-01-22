<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pembayaran Berhasil - SNV Pos</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * { font-family: 'Inter', sans-serif; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

    @keyframes pop {
      0% { transform: scale(.7); opacity: 0; }
      70% { transform: scale(1.05); opacity: 1; }
      100% { transform: scale(1); }
    }
    @keyframes float {
      0%,100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
    }

    /* Print styling */
    @media print {
      body { background: #fff !important; }
      .no-print { display: none !important; }
      .print-card { box-shadow: none !important; border: none !important; }
      .print-wrap { padding: 0 !important; }
      .print-receipt { border: 1px solid #e5e7eb !important; }
    }
  </style>
</head>

<body class="bg-gray-50">
  @php
    // Pastikan di controller success() kamu kirim $items:
    // $items = $order->items()->get();
    // return view('dashboardUser.payment_success', compact('order','items'));

    $paidAt = $order->paid_at ? \Carbon\Carbon::parse($order->paid_at) : null;
    $dateStr = $paidAt ? $paidAt->format('d M Y') : '-';
    $timeStr = $paidAt ? $paidAt->format('H:i') : '-';

    $method = $order->payment_method ?? '-';
    $methodLabel = match($method) {
      'cash' => 'Tunai',
      'bank' => 'Transfer Bank',
      'ewallet' => 'E-Wallet',
      default => strtoupper((string)$method),
    };

    $subtotal = (int) ($order->subtotal ?? 0);
    $serviceFee = (int) ($order->service_fee ?? 0);
    $total = (int) ($order->total ?? 0);

    $paidAmount = (int) ($order->paid_amount ?? 0);
    $changeAmount = (int) ($order->change_amount ?? 0);

    $cashierName = auth()->user()->name ?? '-';
    $cashierId = auth()->id() ?? null;
  @endphp

  <div class="min-h-screen flex items-center justify-center px-6 py-10 print-wrap">
    <div class="w-full max-w-3xl rounded-3xl bg-white shadow-xl border border-gray-100 overflow-hidden print-card">

      <!-- TOP BAR -->
      <div class="px-8 py-6 border-b bg-white">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-4">
            <div class="h-14 w-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center"
                 style="animation: pop .45s ease-out;">
              <i class="fas fa-check text-2xl"></i>
            </div>

            <div>
              <h1 class="text-2xl font-extrabold text-gray-900" style="animation: pop .55s ease-out;">
                Pembayaran Berhasil
              </h1>
              <p class="text-sm text-gray-500 mt-1" style="animation: pop .65s ease-out;">
                Transaksi tersimpan • Status: <span class="font-bold text-green-600">{{ $order->status }}</span>
              </p>
            </div>
          </div>

          <!-- ACTION BUTTONS -->
          <div class="no-print flex items-center gap-2">
            <button
              id="downloadBtn"
              type="button"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
              <i class="fas fa-download"></i> Download
            </button>

            <button
              onclick="window.print()"
              type="button"
              class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white btn-primary hover:opacity-95 transition">
              <i class="fas fa-print"></i> Print
            </button>
          </div>
        </div>
      </div>

      <!-- RECEIPT / DETAIL -->
      <div class="p-8">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-6 print-receipt">
          <!-- Header receipt -->
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
              <p class="text-sm font-extrabold text-gray-900">SNV POS</p>
              <p class="text-xs text-gray-500">Bukti Pembayaran / Receipt</p>
            </div>

            <div class="text-left sm:text-right">
              <p class="text-xs text-gray-500">INVOICE</p>
              <p class="text-lg font-extrabold tracking-wide text-gray-900">
                {{ $order->invoice }}
              </p>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl bg-white border border-gray-100 p-4">
              <p class="text-xs text-gray-500">Tanggal</p>
              <p class="text-sm font-bold text-gray-900">{{ $dateStr }} • {{ $timeStr }}</p>

              <p class="text-xs text-gray-500 mt-3">Metode Pembayaran</p>
              <p class="text-sm font-bold text-gray-900">{{ $methodLabel }}</p>
            </div>

            <div class="rounded-xl bg-white border border-gray-100 p-4">
              <p class="text-xs text-gray-500">Kasir</p>
              <p class="text-sm font-bold text-gray-900">
                {{ $cashierName }}
              </p>

              <p class="text-xs text-gray-500 mt-3">ID Kasir</p>
              <p class="text-sm font-semibold text-gray-700">
                {{ $cashierId ? '#'.$cashierId : '-' }}
              </p>
            </div>
          </div>

          <!-- Items -->
          <div class="mt-6">
            <div class="flex items-center justify-between">
              <p class="text-sm font-extrabold text-gray-900">Detail Item</p>
              <p class="text-xs text-gray-500">{{ isset($items) ? $items->count() : 0 }} item</p>
            </div>

            <div class="mt-3 overflow-hidden rounded-xl border border-gray-200 bg-white">
              <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                  <tr class="text-left text-xs font-bold text-gray-600">
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3 w-24">Qty</th>
                    <th class="px-4 py-3 w-36 text-right">Harga</th>
                    <th class="px-4 py-3 w-40 text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  @if(isset($items) && $items->count())
                    @foreach($items as $it)
                      <tr>
                        <td class="px-4 py-3">
                          <p class="font-semibold text-gray-900">{{ $it->name }}</p>
                          <p class="text-xs text-gray-500">
                            {{ $it->unit ? 'Satuan: '.$it->unit : '' }}
                          </p>
                        </td>
                        <td class="px-4 py-3">
                          <span class="font-bold text-gray-900">{{ $it->qty }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          Rp {{ number_format($it->price,0,',','.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-extrabold text-purple-700">
                          Rp {{ number_format($it->subtotal,0,',','.') }}
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                        Item tidak tersedia.
                      </td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>

          <!-- Totals -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl bg-white border border-gray-100 p-4">
              <p class="text-sm font-extrabold text-gray-900">Ringkasan</p>

              <div class="mt-3 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Subtotal</span>
                  <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal,0,',','.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Biaya layanan</span>
                  <span class="font-semibold text-gray-900">Rp {{ number_format($serviceFee,0,',','.') }}</span>
                </div>

                <div class="border-t pt-3 flex items-center justify-between">
                  <span class="text-gray-900 font-extrabold">TOTAL</span>
                  <span class="text-lg font-extrabold text-gray-900">Rp {{ number_format($total,0,',','.') }}</span>
                </div>
              </div>
            </div>

            <div class="rounded-xl bg-white border border-gray-100 p-4">
              <p class="text-sm font-extrabold text-gray-900">Pembayaran</p>

              <div class="mt-3 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Metode</span>
                  <span class="font-semibold text-gray-900">{{ $methodLabel }}</span>
                </div>

                @if(($order->payment_method ?? '') === 'cash')
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Uang diterima</span>
                    <span class="font-extrabold text-gray-900">Rp {{ number_format($paidAmount,0,',','.') }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Kembalian</span>
                    <span class="font-extrabold text-green-600">Rp {{ number_format($changeAmount,0,',','.') }}</span>
                  </div>
                @else
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Dibayar</span>
                    <span class="font-extrabold text-gray-900">Rp {{ number_format($total,0,',','.') }}</span>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">
                    * Untuk metode non-tunai, dianggap lunas setelah kasir konfirmasi.
                  </p>
                @endif
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
              Terima kasih • Simpan struk ini sebagai bukti pembayaran
            </p>
            <p class="mt-2 text-[11px] text-gray-400" style="animation: float 1.6s ease-in-out infinite;">
              ✅ Siap untuk transaksi berikutnya
            </p>
          </div>
        </div>

        <!-- Bottom actions -->
        <div class="no-print mt-7 grid grid-cols-1 sm:grid-cols-3 gap-3">
          <a href="{{ route('user.dashboard') }}"
             class="rounded-2xl px-4 py-3 text-sm font-bold text-white btn-primary hover:opacity-95 transition text-center">
            <i class="fas fa-plus mr-2"></i> Transaksi Baru
          </a>

          <a href="{{ route('cart.index') }}"
             class="rounded-2xl px-4 py-3 text-sm font-bold border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition text-center">
            <i class="fas fa-cart-shopping mr-2"></i> Keranjang
          </a>

          <a href="{{ route('payment.show', $order) }}"
             class="rounded-2xl px-4 py-3 text-sm font-bold border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition text-center">
            <i class="fas fa-receipt mr-2"></i> Lihat Detail
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Download receipt as HTML file (tanpa backend)
    function downloadHTML(filename, html) {
      const blob = new Blob([html], { type: "text/html;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }

    const downloadBtn = document.getElementById("downloadBtn");
    if (downloadBtn) {
      downloadBtn.addEventListener("click", () => {
        // Ambil bagian receipt aja
        const receipt = document.querySelector(".print-receipt");
        const title = document.title || "receipt";
        const html = `
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>${title}</title>
<style>
  body { font-family: Arial, sans-serif; padding: 16px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
  th { background: #f5f5f5; text-align: left; }
</style>
</head>
<body>
${receipt ? receipt.outerHTML : "<p>Receipt tidak ditemukan.</p>"}
</body>
</html>`.trim();

        downloadHTML("SNVPOS-RECEIPT-{{ $order->invoice }}.html", html);
      });
    }
  </script>
</body>
</html>
