<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
        .modal { display:none; }
        .modal.show { display:flex; }
    </style>
</head>

<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('dashboard.adminsidebar')


    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-64 transition-all duration-300">

        {{-- HEADER --}}
        <header class="bg-white shadow-sm z-10">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Produk</h1>
                    <p class="text-sm text-gray-500">Kelola seluruh produk Anda</p>
                </div>

                <div class="flex items-center gap-4">
                    <button class="relative text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                    </button>

                    <img class="h-10 w-10 rounded-full border-2 border-purple-500"
                         src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=667eea&color=fff"
                         alt="Avatar">
                </div>
            </div>
        </header>

        {{-- MAIN --}}
        <main class="flex-1 overflow-y-auto p-6">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-300 flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <span class="text-green-900 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- FILTER & ACTIONS --}}
            <div class="bg-white p-6 rounded-xl shadow mb-6">
                <div class="flex flex-wrap gap-4 justify-between">

                    {{-- SEARCH & FILTERS --}}
                    
                    <form method="GET" action="{{ route('admin.products.index')

 }}"
                          id="filterForm"
                          class="flex flex-col md:flex-row gap-3 flex-1">

                        {{-- SEARCH --}}
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari produk atau SKU..."
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        {{-- FILTER KATEGORI --}}
                        <select name="category"
                                onchange="document.getElementById('filterForm').submit()"
                                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>

                        {{-- FILTER STATUS --}}
                        <select name="status"
                                onchange="document.getElementById('filterForm').submit()"
                                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>

                        <button type="submit"
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </form>

                    {{-- BUTTON TAMBAH --}}
                    <button onclick="openModal('createModal')"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow">
                        <i class="fas fa-plus mr-2"></i>Tambah Produk
                    </button>

                </div>
            </div>

            {{-- TABLE LIST PRODUK --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Gambar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">SKU</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Harga Jual</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Stok</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Aksi</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}"
                                             class="h-12 w-12 rounded object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $product->sku }}</td>

                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Illuminate\Support\Str::limit($product->description, 50) }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">{{ $product->category ?? '-' }}</td>

                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium {{ $product->stock < 10 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $product->stock }} {{ $product->unit }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($product->status === 'active')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editProduct({{ $product->id }})"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button onclick="deleteProduct({{ $product->id }})"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-box-open text-4xl mb-3"></i>
                                    <p class="text-lg font-medium">Belum ada produk</p>
                                    <p class="text-sm">Tambahkan produk pertama Anda sekarang!</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($products->hasPages())
                    <div class="p-4 border-t">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>
</div>

{{-- MODAL TAMBAH PRODUK --}}
<div id="createModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden">

        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Tambah Produk Baru</h2>
            <button onclick="closeModal('createModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST"
              action="{{ route('admin.products.store') }}"
              enctype="multipart/form-data"
              class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                    <input type="text" name="sku" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                    <input type="text" name="name" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" name="category"
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                    <select name="unit" required
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="">Pilih Satuan</option>

                        <optgroup label="Unit Umum">
                            <option value="pcs">Pcs (Pieces)</option>
                            <option value="unit">Unit</option>
                            <option value="buah">Buah</option>
                            <option value="item">Item</option>
                            <option value="set">Set</option>
                            <option value="pack">Pack</option>
                        </optgroup>

                        <optgroup label="Berat">
                            <option value="g">Gram (g)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="mg">Miligram (mg)</option>
                            <option value="ton">Ton</option>
                        </optgroup>

                        <optgroup label="Volume">
                            <option value="L">Liter (L)</option>
                            <option value="ml">Mililiter (ml)</option>
                            <option value="m3">Meter Kubik (m³)</option>
                        </optgroup>

                        <optgroup label="Panjang">
                            <option value="cm">Centimeter (cm)</option>
                            <option value="m">Meter (m)</option>
                            <option value="km">Kilometer (km)</option>
                            <option value="inch">Inch</option>
                        </optgroup>

                        <optgroup label="Kemasan">
                            <option value="botol">Botol</option>
                            <option value="kaleng">Kaleng</option>
                            <option value="kotak">Kotak</option>
                            <option value="sachet">Sachet</option>
                            <option value="karung">Karung</option>
                            <option value="barrel">Barrel</option>
                        </optgroup>

                        <optgroup label="Kuantitas Khusus">
                            <option value="lusin">Lusin</option>
                            <option value="kodi">Kodi</option>
                            <option value="rim">Rim</option>
                            <option value="gross">Gross</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli *</label>
                    <input type="text" id="purchase_price" name="purchase_price" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual *</label>
                    <input type="text" id="selling_price" name="selling_price" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                    <input type="number" name="stock" min="0" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="closeModal('createModal')"
                        class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PRODUK --}}
<div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden">

        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Edit Produk</h2>
            <button onclick="closeModal('editModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                    <input type="text" id="edit_sku" name="sku" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                    <input type="text" id="edit_name" name="name" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" id="edit_category" name="category"
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                    <select id="edit_unit" name="unit" required
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="">Pilih Satuan</option>

                        <optgroup label="Unit Umum">
                            <option value="pcs">Pcs (Pieces)</option>
                            <option value="unit">Unit</option>
                            <option value="buah">Buah</option>
                            <option value="item">Item</option>
                            <option value="set">Set</option>
                            <option value="pack">Pack</option>
                        </optgroup>

                        <optgroup label="Berat">
                            <option value="g">Gram (g)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="mg">Miligram (mg)</option>
                            <option value="ton">Ton</option>
                        </optgroup>

                        <optgroup label="Volume">
                            <option value="L">Liter (L)</option>
                            <option value="ml">Mililiter (ml)</option>
                            <option value="m3">Meter Kubik (m³)</option>
                        </optgroup>

                        <optgroup label="Panjang">
                            <option value="cm">Centimeter (cm)</option>
                            <option value="m">Meter (m)</option>
                            <option value="km">Kilometer (km)</option>
                            <option value="inch">Inch</option>
                        </optgroup>

                        <optgroup label="Kemasan">
                            <option value="botol">Botol</option>
                            <option value="kaleng">Kaleng</option>
                            <option value="kotak">Kotak</option>
                            <option value="sachet">Sachet</option>
                            <option value="karung">Karung</option>
                            <option value="barrel">Barrel</option>
                        </optgroup>

                        <optgroup label="Kuantitas Khusus">
                            <option value="lusin">Lusin</option>
                            <option value="kodi">Kodi</option>
                            <option value="rim">Rim</option>
                            <option value="gross">Gross</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli *</label>
                    <input type="text" id="edit_purchase_price" name="purchase_price" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual *</label>
                    <input type="text" id="edit_selling_price" name="selling_price" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                    <input type="number" id="edit_stock" name="stock" min="0" required
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select id="edit_status" name="status" required
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="3"
                          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah gambar</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="closeModal('editModal')"
                        class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

{{-- FORM DELETE HIDDEN --}}
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Buka/Tutup Modal
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Klik luar modal untuk menutup
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Edit Produk TANPA FORMAT
    function editProduct(productId) {
        fetch(`/admin/products/${productId}`)
            .then(response => response.json())
            .then(product => {

                document.getElementById('edit_sku').value       = product.sku;
                document.getElementById('edit_name').value      = product.name;
                document.getElementById('edit_category').value  = product.category ?? '';
                document.getElementById('edit_unit').value      = product.unit;

                // langsung isi angka mentah (TIDAK FORMAT)
                document.getElementById('edit_purchase_price').value = product.purchase_price;
                document.getElementById('edit_selling_price').value  = product.selling_price;

                document.getElementById('edit_stock').value     = product.stock;
                document.getElementById('edit_status').value    = product.status;
                document.getElementById('edit_description').value = product.description ?? '';

                const form = document.getElementById('editForm');
                form.action = `/admin/products/${productId}`;

                openModal('editModal');
            });
    }

    // Delete Produk
    function deleteProduct(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/products/${productId}`;
            form.submit();
        }
    }
</script>


</body>
</html>