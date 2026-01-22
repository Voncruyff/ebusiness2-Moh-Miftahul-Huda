<style>
  /* fallback kalau gradient-primary belum ada */
  .gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

  /* Animasi untuk sidebar */
  .menu-item { position: relative; overflow: hidden; }

  .menu-item::before{
    content:'';
    position:absolute; left:0; top:0; height:100%; width:4px;
    background: linear-gradient(to bottom, #8b5cf6, #3b82f6);
    transform: scaleY(0);
    transition: transform .3s ease;
  }
  .menu-item:hover::before,
  .menu-item.active::before{ transform: scaleY(1); }

  .icon-wrapper{ transition: all .3s ease; }
  .menu-item:hover .icon-wrapper{ transform: scale(1.2) rotate(5deg); }

  .badge-notification { animation: pulse-glow 2s infinite; }
  @keyframes pulse-glow{
    0%,100%{ box-shadow:0 0 5px rgba(239,68,68,.5); }
    50%{ box-shadow:0 0 15px rgba(239,68,68,.8); }
  }

  .profile-card{ transition: all .3s ease; cursor:pointer; }
  .profile-card:hover{
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(139,92,246,.3);
  }

  .profile-menu{ max-height:0; overflow:hidden; transition: max-height .3s ease; }
  .profile-menu.show{ max-height:220px; }

  .profile-menu-item{
    opacity:0; transform: translateX(-20px);
    transition: all .3s ease;
  }
  .profile-menu.show .profile-menu-item{ opacity:1; transform: translateX(0); }
  .profile-menu-item:nth-child(1){ transition-delay:.1s; }
  .profile-menu-item:nth-child(2){ transition-delay:.2s; }

  .custom-scrollbar::-webkit-scrollbar{ width:6px; }
  .custom-scrollbar::-webkit-scrollbar-track{ background:#f1f1f1; border-radius:10px; }
  .custom-scrollbar::-webkit-scrollbar-thumb{
    background: linear-gradient(to bottom, #8b5cf6, #3b82f6);
    border-radius:10px;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover{
    background: linear-gradient(to bottom, #7c3aed, #2563eb);
  }

  .logo-pulse{ animation: logo-pulse-animation 3s infinite; }
  @keyframes logo-pulse-animation{
    0%,100%{ box-shadow:0 4px 15px rgba(139,92,246,.4); }
    50%{ box-shadow:0 4px 25px rgba(139,92,246,.6); }
  }

  .rotate-180{ transform: rotate(180deg); }

  .ripple{
    position:absolute; border-radius:50%;
    background: rgba(255,255,255,.6);
    transform: scale(0);
    animation: ripple-animation .6s ease-out;
    pointer-events:none;
  }
  @keyframes ripple-animation{ to{ transform: scale(4); opacity:0; } }

  /* Disabled menu */
  .menu-disabled{
    opacity: .55;
    cursor: not-allowed;
    user-select: none;
    background: rgba(243,244,246,.7); /* gray-100 */
  }
  .menu-disabled::before{ display:none; } /* biar ga ada garis active */
</style>

<!-- Sidebar -->
<aside id="sidebar"
  class="fixed left-0 top-0 z-40 h-screen w-64 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out">

  <div class="flex h-full flex-col">

    <!-- Logo Header -->
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5 bg-gradient-to-r from-purple-50 via-blue-50 to-purple-50">
      <div class="flex items-center space-x-3">
        <div class="logo-pulse flex h-10 w-10 items-center justify-center rounded-lg gradient-primary shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-110">
          <i class="fas fa-cash-register text-white text-lg"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">SNV Pos</h1>
          <p class="text-xs text-gray-500 font-medium">Admin Panel</p>
        </div>
      </div>

      <!-- Close Button (Mobile) -->
      <button id="closeSidebar" class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-6 custom-scrollbar">
      <div class="space-y-2">

        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
          class="menu-item group flex items-center space-x-3 rounded-xl px-4 py-3 transition-all duration-300
          {{ request()->routeIs('admin.dashboard') 
           ? 'active bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/30' 
           : 'text-gray-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-blue-50' }}">
          <div class="icon-wrapper {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-purple-500' }}">
            <i class="fas fa-house text-lg"></i>
          </div>
          <span class="font-semibold flex-1">Dashboard</span>
          @if(request()->routeIs('admin.dashboard'))
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
          @endif
        </a>

        <!-- Produk (satu-satunya fitur yang ready) -->
        <a href="{{ route('admin.products.index') }}"
          class="menu-item group flex items-center space-x-3 rounded-xl px-4 py-3 transition-all duration-300
          {{ request()->routeIs('admin.products.*')
              ? 'active bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/30'
              : 'text-gray-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-blue-50' }}">

            <div class="icon-wrapper {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-blue-500' }}">
                <i class="fas fa-box text-lg"></i>
            </div>

            <span class="font-semibold flex-1">Produk</span>

            @if (request()->routeIs('admin.products.*'))
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>


        <!-- History -->
        <a href="{{ route('admin.history') }}"
          class="menu-item group flex items-center space-x-3 rounded-xl px-4 py-3
                  transition-all duration-300
                  {{ request()->routeIs('admin.history') 
                      ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white shadow-lg' 
                      : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">

          <div class="icon-wrapper">
            <i class="fas fa-clock-rotate-left text-lg"></i>
          </div>

          <span class="font-semibold flex-1">History</span>
        </a>


        <!-- Laporan (DISABLED dulu) -->
        {{-- Laporan --}}
        <a href="{{ route('admin.reports') }}"
          class="menu-item group flex items-center space-x-3 rounded-xl px-4 py-3 transition-all duration-300
                  {{ request()->routeIs('admin.reports') 
                      ? 'bg-indigo-50 text-indigo-700 font-bold' 
                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800' }}">

          <div class="icon-wrapper text-indigo-500">
            <i class="fas fa-chart-line text-lg"></i>
          </div>

          <span class="font-semibold flex-1">Laporan</span>
        </a>


        <!-- Inventory (DISABLED dulu) -->
        {{-- Inventory --}}
        <a href="{{ route('admin.inventory') }}"
          class="menu-item group flex items-center space-x-3 rounded-xl px-4 py-3 transition-all duration-300
                  {{ request()->routeIs('admin.inventory') 
                      ? 'bg-yellow-50 text-yellow-700 font-bold' 
                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800' }}">

          <div class="icon-wrapper text-yellow-500">
            <i class="fas fa-warehouse text-lg"></i>
          </div>

          <span class="font-semibold flex-1">Inventory</span>
        </a>


      </div>

      <div class="my-6 border-t border-gray-200"></div>

      <!-- Profile -->
      <div class="mt-4">
        <div id="profileCard"
          class="profile-card rounded-xl bg-gradient-to-br from-purple-100 via-blue-100 to-purple-100 p-4 mb-3 border-2 border-purple-200 hover:border-purple-400">
          <div class="flex items-center space-x-3">
            <div class="relative">
              <div class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                {{ substr(Auth::user()->name, 0, 1) }}
              </div>
              <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
              <p class="text-xs text-gray-600 truncate">{{ Auth::user()->email }}</p>
            </div>
            <div>
              <i id="profileArrow" class="fas fa-chevron-down text-purple-600 text-sm transition-transform duration-300"></i>
            </div>
          </div>
        </div>

        <div id="profileMenu" class="profile-menu space-y-1">
          <a href="{{ route('profile.edit') }}"
            class="profile-menu-item group flex items-center space-x-3 rounded-lg px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-300">
            <div class="w-8 h-8 rounded-lg bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center transition-colors">
              <i class="fas fa-user-circle text-purple-600"></i>
            </div>
            <span class="font-medium flex-1">Profile Saya</span>
            <i class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="profile-menu-item group flex w-full items-center space-x-3 rounded-lg px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-all duration-300">
              <div class="w-8 h-8 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center transition-colors">
                <i class="fas fa-right-from-bracket text-red-600"></i>
              </div>
              <span class="font-medium flex-1 text-left">Logout</span>
              <i class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </button>
          </form>
        </div>
      </div>

    </nav>
  </div>
</aside>

<!-- Overlay mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

<script>
  // Profile menu toggle
  const profileCard = document.getElementById('profileCard');
  const profileMenu = document.getElementById('profileMenu');
  const profileArrow = document.getElementById('profileArrow');

  if (profileCard) {
    profileCard.addEventListener('click', () => {
      profileMenu.classList.toggle('show');
      profileArrow.classList.toggle('rotate-180');
    });
  }

  // Sidebar mobile toggle
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const closeSidebarBtn = document.getElementById('closeSidebar');
  const sidebarToggleBtn = document.getElementById('sidebarToggle'); // optional button

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
  }
  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebarOverlay.classList.remove('hidden');
  }

  if (window.innerWidth < 1024) sidebar.classList.add('-translate-x-full');

  if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
  if (sidebarToggleBtn) sidebarToggleBtn.addEventListener('click', () => {
    sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
  });
  if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

  // Ripple effect (skip disabled)
  document.querySelectorAll('.menu-item, .profile-menu-item').forEach(item => {
    item.addEventListener('click', function(e) {
      if (this.classList.contains('menu-disabled')) {
        e.preventDefault();
        return;
      }
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top  = (e.clientY - rect.top  - size / 2) + 'px';
      ripple.classList.add('ripple');
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    });
  });
</script>
