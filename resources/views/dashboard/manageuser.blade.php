<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>POSin - Manage User</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * { font-family: 'Inter', sans-serif; }
    .hover-scale{ transition: transform .2s ease; }
    .hover-scale:hover{ transform: translateY(-2px); }
    .gradient-primary{ background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); }
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
            <h2 class="text-2xl font-bold text-gray-800">Manage User</h2>
            <p class="text-sm text-gray-500">
              Kelola akun kasir & admin — {{ auth()->user()->name ?? 'Admin' }}
            </p>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
          </div>
        </div>
      </div>
    </header>

    <main class="p-6 lg:p-8">

      {{-- ALERT --}}
      @if(session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span class="font-semibold">{{ session('success') }}</span>
          </div>
        </div>
      @endif

      @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span class="font-semibold">{{ session('error') }}</span>
          </div>
        </div>
      @endif

      @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
          <div class="font-semibold mb-2">Ada error:</div>
          <ul class="list-disc ms-5 text-sm">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- KONTROL --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <div class="hover-scale rounded-2xl bg-white p-6 shadow-lg border border-gray-100 lg:col-span-2">
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kontrol</p>
              <h3 class="mt-1 text-xl font-extrabold text-gray-900">Cari • Filter • Urutkan</h3>
              <p class="mt-1 text-xs text-gray-500">Default: admin otomatis di atas</p>
            </div>

            <button onclick="openCreate()"
              class="rounded-xl px-4 py-2 text-sm font-bold text-white bg-gray-900 hover:bg-gray-800">
              <i class="fa-solid fa-user-plus mr-2"></i> Tambah
            </button>
          </div>

          <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
              <label class="text-xs font-semibold text-gray-600">Search</label>
              <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input
                  name="q"
                  value="{{ $q ?? '' }}"
                  placeholder="Cari nama / email..."
                  class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm outline-none
                         focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
                />
              </div>
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Filter Role</label>
              <select name="role"
                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                       focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                <option value="">Semua</option>
                <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>admin</option>
                <option value="user"  {{ ($role ?? '') === 'user' ? 'selected' : '' }}>user</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Urutkan</label>
              <div class="flex gap-2 mt-1">
                <select name="sort"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                         focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                  <option value="rank" {{ ($sort ?? '') === 'rank' ? 'selected' : '' }}>Admin di atas</option>
                  <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Nama</option>
                  <option value="email" {{ ($sort ?? '') === 'email' ? 'selected' : '' }}>Email</option>
                  <option value="role" {{ ($sort ?? '') === 'role' ? 'selected' : '' }}>Role</option>
                  <option value="created" {{ ($sort ?? '') === 'created' ? 'selected' : '' }}>Tanggal dibuat</option>
                </select>

                <select name="dir"
                  class="w-28 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                         focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
                  <option value="asc"  {{ ($dir ?? '') === 'asc' ? 'selected' : '' }}>ASC</option>
                  <option value="desc" {{ ($dir ?? '') === 'desc' ? 'selected' : '' }}>DESC</option>
                </select>
              </div>
            </div>

            <div class="md:col-span-4 flex items-center justify-between pt-2">
              <p class="text-xs text-gray-500">
                Menampilkan: <b>{{ $users->total() }}</b> user
              </p>

              <div class="flex gap-2">
                <a href="{{ route('admin.users.index') }}"
                  class="rounded-xl px-4 py-2 text-sm font-semibold border border-gray-200 hover:bg-gray-50">
                  Reset
                </a>
                <button
                  class="rounded-xl px-4 py-2 text-sm font-bold text-white bg-purple-600 hover:bg-purple-700">
                  Terapkan
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="hover-scale rounded-2xl bg-white p-6 shadow-lg border border-gray-100">
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tips</p>
          <h3 class="mt-1 text-lg font-extrabold text-gray-900">Aksi Cepat</h3>

          <ul class="mt-4 space-y-3 text-sm text-gray-700">
            <li class="flex items-center gap-3">
              <span class="h-8 w-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <i class="fa-solid fa-user-shield"></i>
              </span>
              Promote/Demote role
            </li>
            <li class="flex items-center gap-3">
              <span class="h-8 w-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <i class="fa-solid fa-pen"></i>
              </span>
              Edit user + reset password
            </li>
            <li class="flex items-center gap-3">
              <span class="h-8 w-8 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                <i class="fa-solid fa-trash"></i>
              </span>
              Hapus user (kecuali akun sendiri)
            </li>
          </ul>
        </div>

      </div>

      {{-- TABLE --}}
      <div class="rounded-2xl bg-white p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-extrabold text-gray-900">Daftar User</h3>
            <p class="text-sm text-gray-500">Admin otomatis muncul di atas</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Email</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Role</th>
                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
              @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                  <td class="py-4 text-sm font-semibold text-gray-800">
                    <div class="flex items-center gap-3">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                      </div>
                      <div>
                        <div class="flex items-center gap-2">
                          <span>{{ $u->name }}</span>
                          @if(auth()->id() === $u->id)
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-purple-100 text-purple-700">YOU</span>
                          @endif
                        </div>
                        <p class="text-xs text-gray-500">ID: {{ $u->id }}</p>
                      </div>
                    </div>
                  </td>

                  <td class="py-4 text-sm text-gray-600">{{ $u->email }}</td>

                  <td class="py-4">
                    @if($u->role === 'admin')
                      <span class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                        <i class="fa-solid fa-crown"></i> ADMIN
                      </span>
                    @else
                      <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                        <i class="fa-solid fa-user"></i> USER
                      </span>
                    @endif
                  </td>

                  <td class="py-4">
                    <div class="flex flex-wrap gap-2">

                      {{-- Promote / Demote --}}
                      <form method="POST" action="{{ route('admin.users.role', $u) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="role" value="{{ $u->role === 'admin' ? 'user' : 'admin' }}">

                        <button
                          class="rounded-xl px-3 py-2 text-xs font-bold text-white
                            {{ $u->role === 'admin' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }}"
                          onclick="return confirm('Ubah role {{ $u->name }} jadi {{ $u->role === 'admin' ? 'user' : 'admin' }}?')"
                          {{ auth()->id() === $u->id ? 'disabled' : '' }}
                          style="{{ auth()->id() === $u->id ? 'opacity:.5;cursor:not-allowed;' : '' }}"
                        >
                          <i class="fa-solid fa-user-shield mr-1"></i>
                          {{ $u->role === 'admin' ? 'Jadikan User' : 'Jadikan Admin' }}
                        </button>
                      </form>

                      {{-- Edit --}}
                      <button
                        class="rounded-xl px-3 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700"
                        onclick="openEdit({{ $u->id }}, @js($u->name), @js($u->email), @js($u->role))"
                      >
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                      </button>

                      {{-- Delete --}}
                      <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                            onsubmit="return confirm('Hapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button
                          class="rounded-xl px-3 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700"
                          {{ auth()->id() === $u->id ? 'disabled' : '' }}
                          style="{{ auth()->id() === $u->id ? 'opacity:.5;cursor:not-allowed;' : '' }}"
                        >
                          <i class="fa-solid fa-trash mr-1"></i> Hapus
                        </button>
                      </form>

                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="py-6 text-center text-sm text-gray-500">Belum ada user.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-5">
          {{ $users->links() }}
        </div>
      </div>

    </main>
  </div>

  {{-- MODAL CREATE --}}
  <div id="modalCreate" class="fixed inset-0 bg-black/40 hidden items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="text-lg font-extrabold text-gray-900">Tambah User</h2>
        <button type="button" onclick="closeCreate()" class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-600">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-4">
        @csrf

        <div>
          <label class="text-sm font-semibold text-gray-700">Nama</label>
          <input
            name="name"
            required
            placeholder="Contoh: John Cena"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
          >
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Email</label>
          <input
            type="email"
            name="email"
            required
            placeholder="contoh@email.com"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
          >
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Role</label>
          <select
            name="role"
            required
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
          >
            <option value="user">user</option>
            <option value="admin">admin</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Password</label>
          <input
            type="password"
            name="password"
            minlength="8"
            required
            placeholder="Minimal 8 karakter"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300"
          >
        </div>

        <div class="pt-2 flex justify-end gap-2">
          <button type="button" onclick="closeCreate()"
            class="rounded-xl px-4 py-2 text-sm font-semibold border border-gray-200 hover:bg-gray-50">
            Batal
          </button>
          <button
            class="rounded-xl px-4 py-2 text-sm font-bold text-white bg-gray-900 hover:bg-gray-800">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL EDIT --}}
  <div id="modalEdit" class="fixed inset-0 bg-black/40 hidden items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="text-lg font-extrabold text-gray-900">Edit User</h2>
        <button type="button" onclick="closeEdit()" class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-600">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form id="editForm" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label class="text-sm font-semibold text-gray-700">Nama</label>
          <input id="editName" name="name" required
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Email</label>
          <input id="editEmail" type="email" name="email" required
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Role</label>
          <select id="editRole" name="role" required
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
            <option value="user">user</option>
            <option value="admin">admin</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Password baru (opsional)</label>
          <input type="password" name="password" minlength="8"
            placeholder="Kosongkan jika tidak diganti"
            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none
                   focus:ring-2 focus:ring-purple-200 focus:border-purple-300">
        </div>

        <div class="pt-2 flex justify-end gap-2">
          <button type="button" onclick="closeEdit()"
            class="rounded-xl px-4 py-2 text-sm font-semibold border border-gray-200 hover:bg-gray-50">
            Batal
          </button>
          <button
            class="rounded-xl px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modalCreate = document.getElementById('modalCreate');
    const modalEdit   = document.getElementById('modalEdit');

    function openCreate(){ modalCreate.classList.remove('hidden'); modalCreate.classList.add('flex'); }
    function closeCreate(){ modalCreate.classList.add('hidden'); modalCreate.classList.remove('flex'); }

    function openEdit(id, name, email, role){
      const form = document.getElementById('editForm');
      form.action = `{{ url('admin/users') }}/${id}`;

      document.getElementById('editName').value = name;
      document.getElementById('editEmail').value = email;
      document.getElementById('editRole').value = role;

      modalEdit.classList.remove('hidden'); modalEdit.classList.add('flex');
    }
    function closeEdit(){ modalEdit.classList.add('hidden'); modalEdit.classList.remove('flex'); }

    // close modal click backdrop + ESC
    function bindModalClose(modalId, closeFnName){
      const modal = document.getElementById(modalId);
      if (!modal) return;

      modal.addEventListener('click', (e) => {
        if (e.target === modal) window[closeFnName]();
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) window[closeFnName]();
      });
    }

    bindModalClose('modalCreate', 'closeCreate');
    bindModalClose('modalEdit', 'closeEdit');

    // toggle sidebar mobile
    document.addEventListener('DOMContentLoaded', function () {
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
