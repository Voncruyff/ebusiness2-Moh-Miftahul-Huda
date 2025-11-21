<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SNV Pos - Produk</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        .modal { display: none; }
        .modal.show { display: flex; }
        .gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    
    @include('layouts.sidebar')
    
    <div class="ml-64 min-h-screen">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Manajemen Produk</h2>
                    <p class="text-sm text-gray-500">Kelola semua produk Anda</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    
                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="p-8">
            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <!-- Filters & Actions -->
            <div class="mb-6 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Search & Filters -->
                    <form method="GET" action="{{ route('admin.products') }}" id="filterForm" class="flex flex-col md:flex-row gap-3 flex-1">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari produk atau SKU..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        
                        <select name="category" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                            @endforeach
                        </select>
                        
                        <select name="status" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        
                        <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </form>
                    
                    <!-- Add Button -->
                    <button onclick="openModal('createModal')" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg">
                        <i class="fas fa-plus mr-2"></i>Tambah Produk
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="rounded-xl bg-white shadow-lg border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Gambar</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">SKU</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama Produk</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Harga Jual</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $product->sku }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $product->formatted_selling_price }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium {{ $product->stock < 10 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $product->stock }} {{ $product->unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->status == 'active')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                        Nonaktif
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editProduct({{ $product->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteProduct({{ $product->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
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
                
                <!-- Pagination -->
                @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800">Tambah Produk Baru</h3>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU/Kode Produk *</label>
                        <input type="text" name="sku" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <input type="text" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Satuan *</label>
                        <select name="unit" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
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
                                <option value="m³">Meter Kubik (m³)</option>
                            </optgroup>
                            <optgroup label="Panjang">
                                <option value="cm">Centimeter (cm)</option>
                                <option value="m">Meter (m)</option>
                                <option value="km">Kilometer (km)</option>
                                <option value="inch">Inch</option>
                            </optgroup>
                            <optgroup label="Luas">
                                <option value="m²">Meter Persegi (m²)</option>
                                <option value="cm²">Centimeter Persegi (cm²)</option>
                                <option value="ha">Hektar (ha)</option>
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
                                <option value="lusin">Lusin (12 pcs)</option>
                                <option value="kodi">Kodi (20 pcs)</option>
                                <option value="rim">Rim (500 lembar)</option>
                                <option value="gross">Gross (144 pcs)</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli *</label>
                        <input type="text" name="purchase_price" id="purchase_price" required placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <input type="hidden" name="purchase_price_raw" id="purchase_price_raw">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual *</label>
                        <input type="text" name="selling_price" id="selling_price" required placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <input type="hidden" name="selling_price_raw" id="selling_price_raw">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal *</label>
                        <input type="number" name="stock" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('createModal')" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800">Edit Produk</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU/Kode Produk *</label>
                        <input type="text" id="edit_sku" name="sku" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                        <input type="text" id="edit_name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <input type="text" id="edit_category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Satuan *</label>
                        <select id="edit_unit" name="unit" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
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
                                <option value="m³">Meter Kubik (m³)</option>
                            </optgroup>
                            <optgroup label="Panjang">
                                <option value="cm">Centimeter (cm)</option>
                                <option value="m">Meter (m)</option>
                                <option value="km">Kilometer (km)</option>
                                <option value="inch">Inch</option>
                            </optgroup>
                            <optgroup label="Luas">
                                <option value="m²">Meter Persegi (m²)</option>
                                <option value="cm²">Centimeter Persegi (cm²)</option>
                                <option value="ha">Hektar (ha)</option>
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
                                <option value="lusin">Lusin (12 pcs)</option>
                                <option value="kodi">Kodi (20 pcs)</option>
                                <option value="rim">Rim (500 lembar)</option>
                                <option value="gross">Gross (144 pcs)</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli *</label>
                        <input type="text" id="edit_purchase_price" name="purchase_price" required placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual *</label>
                        <input type="text" id="edit_selling_price" name="selling_price" required placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok *</label>
                        <input type="number" id="edit_stock" name="stock" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="edit_status" name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="edit_description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah gambar</p>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('editModal')" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Format Rupiah saat mengetik
        function formatRupiah(angka, prefix = '') {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }

        // Auto format untuk Create Modal
        const purchaseInput = document.getElementById('purchase_price');
        const sellingInput = document.getElementById('selling_price');

        if (purchaseInput) {
            purchaseInput.addEventListener('keyup', function(e) {
                let value = this.value.replace(/\./g, '');
                this.value = formatRupiah(value);
            });
        }

        if (sellingInput) {
            sellingInput.addEventListener('keyup', function(e) {
                let value = this.value.replace(/\./g, '');
                this.value = formatRupiah(value);
            });
        }

        // Auto format untuk Edit Modal
        const editPurchaseInput = document.getElementById('edit_purchase_price');
        const editSellingInput = document.getElementById('edit_selling_price');

        if (editPurchaseInput) {
            editPurchaseInput.addEventListener('keyup', function(e) {
                let value = this.value.replace(/\./g, '');
                this.value = formatRupiah(value);
            });
        }

        if (editSellingInput) {
            editSellingInput.addEventListener('keyup', function(e) {
                let value = this.value.replace(/\./g, '');
                this.value = formatRupiah(value);
            });
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        function editProduct(productId) {
            fetch(`/admin/products/${productId}`)
                .then(response => response.json())
                .then(product => {
                    document.getElementById('edit_sku').value = product.sku;
                    document.getElementById('edit_name').value = product.name;
                    document.getElementById('edit_category').value = product.category || '';
                    document.getElementById('edit_unit').value = product.unit;
                    
                    // Format harga dengan titik pemisah ribuan
                    document.getElementById('edit_purchase_price').value = formatRupiah(product.purchase_price.toString());
                    document.getElementById('edit_selling_price').value = formatRupiah(product.selling_price.toString());
                    
                    document.getElementById('edit_stock').value = product.stock;
                    document.getElementById('edit_status').value = product.status;
                    document.getElementById('edit_description').value = product.description || '';
                    
                    document.getElementById('editForm').action = `/admin/products/${productId}`;
                    openModal('editModal');
                });
        }

        function deleteProduct(productId) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                const form = document.getElementById('deleteForm');
                form.action = `/admin/products/${productId}`;
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Convert formatted rupiah back to number before submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Untuk Create Modal
                if (purchaseInput && purchaseInput.value) {
                    purchaseInput.value = purchaseInput.value.replace(/\./g, '');
                }
                if (sellingInput && sellingInput.value) {
                    sellingInput.value = sellingInput.value.replace(/\./g, '');
                }
                
                // Untuk Edit Modal
                if (editPurchaseInput && editPurchaseInput.value) {
                    editPurchaseInput.value = editPurchaseInput.value.replace(/\./g, '');
                }
                if (editSellingInput && editSellingInput.value) {
                    editSellingInput.value = editSellingInput.value.replace(/\./g, '');
                }
            });
        });
    </script>
</body>
</html>