<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Produk Tersedia - SNV Pos</title>

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

    .line-clamp-2{
      display:-webkit-box;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      overflow:hidden;
    }
  </style>
</head>

<body class="bg-gray-50">
  <div class="min-h-screen">
    @include('dashboardUser.usersidebar')

    <div class="content-with-sidebar">

      <!-- HEADER (tetap sticky) -->
      <!-- HEADER (sticky) -->
      <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="w-full px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
            
            {{-- LEFT: Judul halaman saja --}}
            <div class="flex items-center gap-3">
              <button id="sidebarToggle"
                class="lg:hidden text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-bars text-xl"></i>
              </button>

              <div>
                <h1 class="text-lg font-extrabold text-gray-800">Produk Tersedia</h1>
                <p class="text-xs text-gray-500">Pilih produk untuk melihat detail</p>
              </div>
            </div>

            {{-- RIGHT --}}
            <div class="flex items-center space-x-3">
              <a href="{{ route('cart.index') }}"
                class="hidden sm:inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-cart-shopping"></i>
                Keranjang
              </a>

              <span class="text-sm font-medium text-gray-700 hidden sm:block">
                {{ Auth::user()->name }}
              </span>

              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
              </div>
            </div>

          </div>
        </div>
      </header>


      <!-- MAIN: dibuat full tinggi layar minus header, supaya yang scroll cuma konten bawah -->
      <main class="w-full px-4 sm:px-6 lg:px-8 pt-6 pb-6 h-[calc(100vh-64px)] flex flex-col">

        <div class="mb-4">
          <h2 class="text-xl font-bold text-gray-800">Produk Tersedia</h2>
          <p class="text-sm text-gray-500">Klik produk untuk lihat detail di kanan</p>
        </div>

        <!-- SEARCH + FILTER + SORT (STICKY: tidak ikut scroll) -->
        <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 pb-3 bg-gray-50">
          <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <!-- Search -->
              <div class="relative w-full lg:max-w-md">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                  <i class="fas fa-magnifying-glass"></i>
                </span>
                <input
                  id="searchInput"
                  type="text"
                  placeholder="Cari produk..."
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-10 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                />
                <button
                  id="clearSearchBtn"
                  type="button"
                  class="hidden absolute inset-y-0 right-2 my-auto h-8 w-8 rounded-lg hover:bg-gray-100 text-gray-500"
                  title="Bersihkan"
                >
                  <i class="fas fa-xmark"></i>
                </button>
              </div>

              <!-- Filter + Sort -->
              <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <!-- Filter kategori -->
                <div class="w-full sm:w-56">
                  <label class="text-xs font-semibold text-gray-600">Filter Kategori</label>
                  <select
                    id="categoryFilter"
                    class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                  >
                    <option value="">Semua kategori</option>
                  </select>
                </div>

                <!-- Sort huruf -->
                <div class="w-full sm:w-56">
                  <label class="text-xs font-semibold text-gray-600">Urutkan</label>
                  <select
                    id="sortSelect"
                    class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                  >
                    <option value="az">Nama (A - Z)</option>
                    <option value="za">Nama (Z - A)</option>
                  </select>
                </div>

                <!-- Info jumlah -->
                <div class="sm:w-auto">
                  <label class="text-xs font-semibold text-gray-600">Hasil</label>
                  <div class="mt-1 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                    <span id="resultCount">0</span> produk
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CONTENT (yang ini yang scroll) -->
        <div class="flex-1 overflow-y-auto">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            <!-- LEFT: PRODUCTS -->
            <div class="lg:col-span-8">
              <div class="rounded-xl bg-white p-4 shadow-lg border border-gray-100">
                <div id="productContainer" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                  <p class="text-gray-500" id="loadingText">Memuat produk...</p>
                </div>
              </div>
            </div>

            <!-- RIGHT: DETAIL PANEL -->
            <aside class="lg:col-span-4">
              <div id="detailPanel" class="rounded-xl bg-white shadow-lg border border-gray-100 overflow-hidden lg:sticky lg:top-24">
                <div class="p-4 border-b">
                  <p class="text-sm font-bold text-gray-800">Detail Produk</p>
                  <p class="text-xs text-gray-500">Klik produk untuk melihat detail</p>
                </div>

                <!-- Empty state -->
                <div id="detailEmpty" class="p-6 text-center text-gray-500">
                  <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <i class="fas fa-hand-pointer"></i>
                  </div>
                  <p class="font-semibold text-gray-700">Belum ada produk dipilih</p>
                  <p class="text-sm text-gray-500 mt-1">Klik salah satu kartu produk di kiri.</p>
                </div>

                <!-- Content -->
                <div id="detailContent" class="hidden p-4">
                  <div class="relative rounded-2xl bg-gray-50 border overflow-hidden aspect-[4/3]">
                    <img id="detailImg" src="" alt="" class="absolute inset-0 w-full h-full object-contain p-3">
                  </div>

                  <div class="mt-3">
                    <p id="detailName" class="text-lg font-extrabold text-gray-900"></p>
                    <p class="text-sm text-gray-500 mt-1">
                      <i class="fas fa-tag mr-1"></i>
                      <span id="detailCategory">-</span>
                    </p>
                  </div>

                  <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border bg-white p-3">
                      <p class="text-xs text-gray-500">Stok</p>
                      <p class="text-sm font-bold text-gray-800">
                        <span id="detailStock">0</span> <span id="detailUnit"></span>
                      </p>
                    </div>
                    <div class="rounded-xl border bg-white p-3">
                      <p class="text-xs text-gray-500">Harga</p>
                      <p class="text-sm font-extrabold text-purple-600">
                        Rp <span id="detailPrice">0</span>
                      </p>
                    </div>
                  </div>

                  <!-- DESKRIPSI -->
                  <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs font-semibold text-gray-700">Deskripsi</p>
                    <p id="detailDesc" class="mt-1 text-sm text-gray-600 leading-relaxed">
                      -
                    </p>
                  </div>

                  <button
                    id="detailAddBtn"
                    type="button"
                    class="mt-4 w-full rounded-xl px-4 py-3 text-sm font-bold text-white btn-primary hover:opacity-95 transition"
                  >
                    <i class="fas fa-cart-plus mr-2"></i> Tambah ke Keranjang
                  </button>

                  <p id="detailHint" class="mt-3 text-xs text-gray-500">
                    * Pastikan stok tersedia.
                  </p>
                </div>
              </div>
            </aside>
          </div>
        </div>

      </main>

    </div>
  </div>

  <script>
    const productContainer = document.getElementById("productContainer");
    const loadingText = document.getElementById("loadingText");
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // UI controls
    const searchInput = document.getElementById("searchInput");
    const clearSearchBtn = document.getElementById("clearSearchBtn");
    const categoryFilter = document.getElementById("categoryFilter");
    const sortSelect = document.getElementById("sortSelect");
    const resultCount = document.getElementById("resultCount");

    // detail panel elements
    const detailEmpty = document.getElementById("detailEmpty");
    const detailContent = document.getElementById("detailContent");
    const detailImg = document.getElementById("detailImg");
    const detailName = document.getElementById("detailName");
    const detailCategory = document.getElementById("detailCategory");
    const detailStock = document.getElementById("detailStock");
    const detailUnit = document.getElementById("detailUnit");
    const detailPrice = document.getElementById("detailPrice");
    const detailDesc = document.getElementById("detailDesc");
    const detailAddBtn = document.getElementById("detailAddBtn");
    const detailHint = document.getElementById("detailHint");

    // store data from SSE
    let allProducts = [];
    let lastDataHash = "";
    let selectedProduct = null;

    function toast(msg){
      const t = document.createElement("div");
      t.className = "fixed right-6 bottom-6 z-50 rounded-lg bg-gray-900 text-white px-4 py-3 text-sm shadow-lg";
      t.innerText = msg;
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 1300);
    }

    async function addToCart(payload) {
      const res = await fetch("{{ route('cart.add') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf, "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok || !data.success) {
        toast("❌ " + (data.message || "Gagal menambahkan"));
        return false;
      }

      toast("✅ Ditambahkan ke keranjang");
      return true;
    }

    // klik tombol tambah dari card (jangan buka detail)
    document.addEventListener("click", async (e) => {
      const btn = e.target.closest("[data-add-cart]");
      if (!btn) return;

      e.preventDefault();
      e.stopPropagation();

      const payload = {
        id: btn.dataset.id,
        name: btn.dataset.name,
        price: btn.dataset.price,
        image: btn.dataset.image || null,
        unit: btn.dataset.unit || null,
        stock: btn.dataset.stock || 0
      };

      await addToCart(payload);
    });

    // klik card produk -> tampilkan detail kanan
    document.addEventListener("click", (e) => {
      const card = e.target.closest("[data-product-card]");
      if (!card) return;

      const id = card.dataset.id;
      const p = allProducts.find(x => String(x.id) === String(id));
      if (!p) return;

      showDetail(p);
    });

    // tombol tambah di panel detail
    detailAddBtn.addEventListener("click", async () => {
      if (!selectedProduct) return;

      const stock = Number(selectedProduct.stock ?? 0);
      if (stock <= 0) {
        toast("❌ Stok habis!");
        return;
      }

      const payload = {
        id: selectedProduct.id,
        name: selectedProduct.name,
        price: selectedProduct.selling_price,
        image: selectedProduct.image || null,
        unit: selectedProduct.unit || null,
        stock: stock
      };

      await addToCart(payload);
    });

    function showDetail(p){
      selectedProduct = p;

      detailEmpty.classList.add("hidden");
      detailContent.classList.remove("hidden");

      detailName.textContent = p.name ?? "-";
      detailCategory.textContent = p.category ?? "-";
      detailStock.textContent = String(p.stock ?? 0);
      detailUnit.textContent = p.unit ?? "";
      detailPrice.textContent = new Intl.NumberFormat("id-ID").format(p.selling_price ?? 0);

      const desc = (p.description ?? "").toString().trim();
      detailDesc.textContent = desc ? desc : "Tidak ada deskripsi.";

      if (p.image) {
        detailImg.src = "/storage/" + p.image;
        detailImg.style.display = "";
        detailImg.onerror = () => {
          detailImg.style.display = "none";
        };
      } else {
        detailImg.src = "";
        detailImg.style.display = "none";
      }

      const stock = Number(p.stock ?? 0);
      if (stock <= 0) {
        detailAddBtn.disabled = true;
        detailAddBtn.classList.add("opacity-50","cursor-not-allowed");
        detailHint.textContent = "* Stok habis.";
      } else {
        detailAddBtn.disabled = false;
        detailAddBtn.classList.remove("opacity-50","cursor-not-allowed");
        detailHint.textContent = "* Klik tombol untuk menambahkan ke keranjang.";
      }
    }

    // ===== Filter/Sort helpers =====
    function normalize(str) {
      return String(str ?? "")
        .toLowerCase()
        .trim();
    }

    function populateCategoryOptions(products) {
      const set = new Set();
      products.forEach(p => {
        const cat = (p.category ?? "").toString().trim();
        if (cat) set.add(cat);
      });

      const current = categoryFilter.value;
      categoryFilter.innerHTML = `<option value="">Semua kategori</option>`;

      Array.from(set)
        .sort((a,b) => a.localeCompare(b, "id", { sensitivity: "base" }))
        .forEach(cat => {
          const opt = document.createElement("option");
          opt.value = cat;
          opt.textContent = cat;
          categoryFilter.appendChild(opt);
        });

      if (current) categoryFilter.value = current;
    }

    function getFilteredSorted(products) {
      const q = normalize(searchInput.value);
      const cat = categoryFilter.value;

      let out = products.filter(p => {
        const name = normalize(p.name);
        const category = (p.category ?? "").toString();
        const matchSearch = !q || name.includes(q) || normalize(category).includes(q);
        const matchCat = !cat || category === cat;
        return matchSearch && matchCat;
      });

      const sortMode = sortSelect.value;
      out.sort((a, b) => {
        const an = (a.name ?? "").toString();
        const bn = (b.name ?? "").toString();
        const cmp = an.localeCompare(bn, "id", { sensitivity: "base" });
        return sortMode === "za" ? -cmp : cmp;
      });

      return out;
    }

    function renderProducts(products) {
      productContainer.innerHTML = "";

      if (!products.length) {
        productContainer.innerHTML = `<p class="text-gray-500">Produk tidak ditemukan</p>`;
        resultCount.textContent = "0";
        return;
      }

      resultCount.textContent = String(products.length);

      products.forEach(item => {
        const stock = Number(item.stock ?? 0);
        const disabled = stock <= 0;

        let badgeClass = "bg-green-100 text-green-700 border-green-200";
        let badgeText = `${stock} ${item.unit ?? ""}`;

        if (stock <= 0) {
          badgeClass = "bg-red-100 text-red-700 border-red-200";
          badgeText = "Habis";
        } else if (stock <= 5) {
          badgeClass = "bg-yellow-100 text-yellow-700 border-yellow-200";
          badgeText = `Menipis ${stock}`;
        }

        const imgHtml = item.image
          ? `
            <div class="relative bg-gray-50 rounded-2xl overflow-hidden aspect-[3/4]">
              <img
                src="/storage/${item.image}"
                alt="${item.name}"
                class="absolute inset-0 w-full h-full object-contain p-2 transition-transform duration-300 group-hover:scale-105"
              >
            </div>
          `
          : `
            <div class="aspect-[3/4] rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400">
              <i class="fas fa-image text-2xl"></i>
            </div>
          `;

        productContainer.insertAdjacentHTML("beforeend", `
          <div
            class="group rounded-2xl border bg-white shadow-sm hover:shadow-md transition overflow-hidden cursor-pointer"
            data-product-card="1"
            data-id="${item.id}"
            title="Klik untuk lihat detail"
          >
            <div class="p-3">

              <div class="relative">
                ${imgHtml}
                <span class="absolute top-2 right-2 inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold ${badgeClass}">
                  ${badgeText}
                </span>
              </div>

              <h4 class="mt-2 text-sm font-semibold text-gray-800 leading-snug line-clamp-2">
                ${item.name}
              </h4>

              <p class="text-xs text-gray-500 mt-0.5">
                <i class="fas fa-tag mr-1"></i> ${item.category ?? "-"}
              </p>

              <div class="mt-2">
                <p class="text-[11px] text-gray-500">Harga</p>
                <p class="text-base font-extrabold text-purple-600">
                  Rp ${new Intl.NumberFormat("id-ID").format(item.selling_price)}
                </p>
              </div>

              <button
                type="button"
                data-add-cart="1"
                data-id="${item.id}"
                data-name="${String(item.name).replace(/"/g, '&quot;')}"
                data-price="${item.selling_price}"
                data-image="${item.image ?? ''}"
                data-unit="${item.unit ?? ''}"
                data-stock="${stock}"
                ${disabled ? 'disabled' : ''}
                class="mt-2 w-full rounded-lg px-3 py-2 text-xs font-bold text-white btn-primary hover:opacity-95 transition
                      ${disabled ? 'opacity-50 cursor-not-allowed' : ''}"
                title="${disabled ? 'Stok habis' : 'Tambah ke keranjang'}"
              >
                <i class="fas ${disabled ? 'fa-circle-xmark' : 'fa-cart-plus'} mr-1"></i>
                ${disabled ? 'Stok Habis' : 'Tambah'}
              </button>

            </div>
          </div>
        `);
      });
    }

    function applyFiltersAndRender() {
      const filtered = getFilteredSorted(allProducts);
      renderProducts(filtered);

      const hasText = (searchInput.value ?? "").trim().length > 0;
      clearSearchBtn.classList.toggle("hidden", !hasText);
    }

    // UI events
    searchInput.addEventListener("input", () => applyFiltersAndRender());
    categoryFilter.addEventListener("change", () => applyFiltersAndRender());
    sortSelect.addEventListener("change", () => applyFiltersAndRender());

    clearSearchBtn.addEventListener("click", () => {
      searchInput.value = "";
      applyFiltersAndRender();
      searchInput.focus();
    });

    // ===== SSE =====
    const sse = new EventSource("/stream-products");

    sse.onmessage = function (event) {
      let data;
      try { data = JSON.parse(event.data); } catch { return; }

      const hash = JSON.stringify(data);
      if (hash === lastDataHash) return;
      lastDataHash = hash;

      if (loadingText) loadingText.remove();

      allProducts = Array.isArray(data) ? data : [];

      populateCategoryOptions(allProducts);
      applyFiltersAndRender();

      // auto pilih produk pertama
      if (!selectedProduct && allProducts.length) {
        showDetail(allProducts[0]);
      } else if (selectedProduct) {
        // refresh detail jika data berubah (stok/harga/desc)
        const fresh = allProducts.find(x => String(x.id) === String(selectedProduct.id));
        if (fresh) showDetail(fresh);
      }
    };

    sse.onerror = () => {};
  </script>

</body>
</html>
