<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SNV Pos - Dashboard User</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .dropdown-menu {
            display: none;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        /* Mobile Menu Styles */
        .mobile-menu {
            display: none;
        }
        
        .mobile-menu.show {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Top Navbar - DISESUAIKAN UNTUK USER -->
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg gradient-primary">
                        <i class="fas fa-cash-register text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">SNV Pos</h1>
                        <p class="text-xs text-gray-500">User Portal</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation - MENU USER (BUKAN ADMIN) -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        <i class="fas fa-shopping-bag mr-1"></i> Pesanan Saya
                    </a>
                    <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        <i class="fas fa-user mr-1"></i> Profil
                    </a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        <i class="fas fa-question-circle mr-1"></i> Bantuan
                    </a>
                </div>
                
                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Toggle -->
                    <button id="mobileMenuToggle" class="md:hidden text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Notifications - TERSEDIA UNTUK USER -->
                    <button class="hidden md:block relative rounded-lg p-2 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative hidden md:block">
                        <button id="profileDropdown" class="flex items-center space-x-3 rounded-lg p-2 hover:bg-gray-100">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-gray-600 text-sm"></i>
                        </button>
                        
                        <div id="dropdownMenu" class="dropdown-menu animate-slide-in absolute right-0 mt-2 w-56 rounded-lg bg-white shadow-xl border border-gray-200">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-2">
                                <!-- MENU PROFIL USER (BUKAN ADMIN) -->
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user-circle w-5"></i>
                                    <span>Profile Saya</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-shopping-bag w-5"></i>
                                    <span>Riwayat Pesanan</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog w-5"></i>
                                    <span>Pengaturan Akun</span>
                                </a>
                            </div>
                            <div class="border-t border-gray-100 py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center space-x-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-5"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu - MENU USER VERSI MOBILE -->
            <div id="mobileMenu" class="mobile-menu md:hidden pb-4">
                <div class="space-y-2">
                    <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3 rounded-lg px-4 py-3 text-purple-600 bg-purple-50">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 rounded-lg px-4 py-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-shopping-bag w-5"></i>
                        <span class="font-medium">Pesanan Saya</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 rounded-lg px-4 py-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-user w-5"></i>
                        <span class="font-medium">Profil</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 rounded-lg px-4 py-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-question-circle w-5"></i>
                        <span class="font-medium">Bantuan</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 rounded-lg px-4 py-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-cog w-5"></i>
                        <span class="font-medium">Pengaturan</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="flex w-full items-center space-x-3 rounded-lg px-4 py-3 text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content Area - KONTEN DINAMIS USER -->
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Banner -->
        <div class="mb-8 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 p-8 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                    <p class="text-purple-100">Ini adalah dashboard Anda. Kelola pesanan dan lihat aktivitas terbaru Anda di sini.</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-user-circle text-8xl opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Pesanan -->
            <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Pesanan</p>
                        <h3 class="mt-2 text-3xl font-bold text-gray-800">24</h3>
                        <p class="mt-2 text-sm text-blue-600">
                            <i class="fas fa-shopping-bag"></i> Semua waktu
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100">
                        <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pesanan Aktif -->
            <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pesanan Aktif</p>
                        <h3 class="mt-2 text-3xl font-bold text-gray-800">3</h3>
                        <p class="mt-2 text-sm text-orange-600">
                            <i class="fas fa-clock"></i> Sedang diproses
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-100">
                        <i class="fas fa-box text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>

            <!-- Selesai -->
            <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Selesai</p>
                        <h3 class="mt-2 text-3xl font-bold text-gray-800">21</h3>
                        <p class="mt-2 text-sm text-green-600">
                            <i class="fas fa-check-circle"></i> Pesanan selesai
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                        <i class="fas fa-check-double text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Belanja -->
            <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Belanja</p>
                        <h3 class="mt-2 text-3xl font-bold text-gray-800">Rp 5,2M</h3>
                        <p class="mt-2 text-sm text-purple-600">
                            <i class="fas fa-wallet"></i> Semua waktu
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-purple-100">
                        <i class="fas fa-dollar-sign text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Recent Orders -->
            <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pesanan Terbaru</h3>
                        <p class="text-sm text-gray-500">Riwayat pesanan Anda</p>
                    </div>
                    <a href="#" class="text-sm font-semibold text-purple-600 hover:text-purple-700">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="space-y-4">
                    <!-- Order Item 1 -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                                <i class="fas fa-laptop text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Laptop ASUS ROG</p>
                                <p class="text-sm text-gray-500">#INV-2024-001 • 19 Nov 2024</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">Rp 15.000.000</p>
                            <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Selesai</span>
                        </div>
                    </div>

                    <!-- Order Item 2 -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                                <i class="fas fa-mouse text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Mouse Gaming Logitech</p>
                                <p class="text-sm text-gray-500">#INV-2024-002 • 18 Nov 2024</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">Rp 850.000</p>
                            <span class="inline-block rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">Diproses</span>
                        </div>
                    </div>

                    <!-- Order Item 3 -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                                <i class="fas fa-keyboard text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Keyboard Mechanical</p>
                                <p class="text-sm text-gray-500">#INV-2024-003 • 17 Nov 2024</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">Rp 1.200.000</p>
                            <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Dikirim</span>
                        </div>
                    </div>

                    <!-- Order Item 4 -->
                    <div class="flex items-center justify-between pb-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                                <i class="fas fa-headphones text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Headset Gaming</p>
                                <p class="text-sm text-gray-500">#INV-2024-004 • 16 Nov 2024</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">Rp 950.000</p>
                            <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Selesai</span>
                        </div>
                    </div>
                </div>

                <button class="mt-4 w-full rounded-lg border-2 border-purple-500 px-4 py-3 text-sm font-semibold text-purple-600 hover:bg-purple-50 transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>Lihat Semua Pesanan
                </button>
            </div>

            <!-- Quick Actions & Info -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <button class="w-full flex items-center space-x-3 rounded-lg bg-purple-600 px-4 py-3 text-white hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus-circle"></i>
                            <span class="font-medium">Buat Pesanan Baru</span>
                        </button>
                        <button class="w-full flex items-center space-x-3 rounded-lg border-2 border-gray-200 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-history"></i>
                            <span class="font-medium">Lihat Riwayat</span>
                        </button>
                        <button class="w-full flex items-center space-x-3 rounded-lg border-2 border-gray-200 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-file-invoice"></i>
                            <span class="font-medium">Download Invoice</span>
                        </button>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="rounded-xl bg-gradient-to-br from-purple-50 to-blue-50 p-6 border border-purple-100">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-14 w-14 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-xl">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status Akun</span>
                            <span class="font-semibold text-green-600">Aktif</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Member Sejak</span>
                            <span class="font-semibold text-gray-800">Jan 2024</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="mt-4 block w-full rounded-lg bg-white px-4 py-2 text-center text-sm font-semibold text-purple-600 hover:bg-purple-50 transition-colors">
                        Kelola Profil
                    </a>
                </div>

                <!-- Tips -->
                <div class="rounded-xl bg-blue-50 p-6 border border-blue-100">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                            <i class="fas fa-lightbulb text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">Tips!</h4>
                            <p class="text-sm text-gray-600">Lengkapi profil Anda untuk mendapatkan pengalaman berbelanja yang lebih baik dan notifikasi pesanan yang akurat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg gradient-primary">
                        <i class="fas fa-cash-register text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">SNV Pos</span>
                </div>
                <p class="text-sm text-gray-500">© 2024 SNV Pos. All rights reserved.</p>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-sm text-gray-600 hover:text-purple-600">Syarat & Ketentuan</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-purple-600">Kebijakan Privasi</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-purple-600">Kontak</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        // Profile Dropdown Toggle
        const profileDropdown = document.getElementById('profileDropdown');
        const dropdownMenu = document.getElementById('dropdownMenu');
        
        if (profileDropdown && dropdownMenu) {
            profileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileDropdown.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
        
        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('show');
            });
        }
    </script>
</body>
</html>