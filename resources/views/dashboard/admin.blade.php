<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SNV Pos - Dashboard Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .hover-scale {
            transition: transform 0.2s ease;
        }
        
        .hover-scale:hover {
            transform: translateY(-2px);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-success {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .gradient-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .activity-line {
            position: relative;
        }
        
        .activity-line::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #e5e7eb;
        }
        
        .activity-item:last-child .activity-line::before {
            display: none;
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
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Include Sidebar -->
    @include('layouts.sidebar')
    
    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-8 py-4">
                <div class="flex items-center space-x-4">
                    <!-- Hamburger Toggle Button -->
                    <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
                        <p class="text-sm text-gray-500">Selamat datang kembali, {{ Auth::user()->name }}!</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    
                    <!-- User Avatar (Simple) -->
                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <main class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Card 1 -->
                <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-800">Rp 45,2M</h3>
                            <p class="mt-2 text-sm text-green-600">
                                <i class="fas fa-arrow-up"></i> 12.5% dari bulan lalu
                            </p>
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-primary">
                            <i class="fas fa-dollar-sign text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-800">1,234</h3>
                            <p class="mt-2 text-sm text-green-600">
                                <i class="fas fa-arrow-up"></i> 8.2% dari bulan lalu
                            </p>
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-success">
                            <i class="fas fa-shopping-bag text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3 -->
                <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Produk Terjual</p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-800">3,456</h3>
                            <p class="mt-2 text-sm text-green-600">
                                <i class="fas fa-arrow-up"></i> 15.8% dari bulan lalu
                            </p>
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-info">
                            <i class="fas fa-box text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Card 4 -->
                <div class="hover-scale rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pelanggan Baru</p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-800">234</h3>
                            <p class="mt-2 text-sm text-green-600">
                                <i class="fas fa-arrow-up"></i> 5.3% dari bulan lalu
                            </p>
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full gradient-warning">
                            <i class="fas fa-users text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts and Activity Row -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Sales Chart -->
                <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Grafik Penjualan</h3>
                            <p class="text-sm text-gray-500">Penjualan 7 hari terakhir</p>
                        </div>
                        <select class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-purple-500 focus:outline-none">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>Bulan Ini</option>
                        </select>
                    </div>
                    <div class="h-64">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
                
                <!-- Activity Feed -->
                <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Activity Log</h3>
                        <p class="text-sm text-gray-500">Aktivitas terbaru sistem</p>
                    </div>
                    
                    <div class="space-y-6 max-h-96 overflow-y-auto">
                        <!-- Activity Item 1 -->
                        <div class="activity-item activity-line relative flex">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-800">Transaksi Berhasil</p>
                                <p class="text-xs text-gray-500">Invoice #INV-2024-001 telah dibayar</p>
                                <span class="mt-1 inline-block text-xs text-gray-400">2 menit yang lalu</span>
                            </div>
                        </div>
                        
                        <!-- Activity Item 2 -->
                        <div class="activity-item activity-line relative flex">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                                <i class="fas fa-box text-blue-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-800">Produk Ditambahkan</p>
                                <p class="text-xs text-gray-500">Produk "Laptop ASUS ROG" ditambahkan</p>
                                <span class="mt-1 inline-block text-xs text-gray-400">15 menit yang lalu</span>
                            </div>
                        </div>
                        
                        <!-- Activity Item 3 -->
                        <div class="activity-item activity-line relative flex">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-purple-100">
                                <i class="fas fa-user-plus text-purple-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-800">Pelanggan Baru</p>
                                <p class="text-xs text-gray-500">Budi Santoso telah terdaftar</p>
                                <span class="mt-1 inline-block text-xs text-gray-400">1 jam yang lalu</span>
                            </div>
                        </div>
                        
                        <!-- Activity Item 4 -->
                        <div class="activity-item activity-line relative flex">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-800">Stok Menipis</p>
                                <p class="text-xs text-gray-500">Produk "Mouse Gaming" stok tersisa 5</p>
                                <span class="mt-1 inline-block text-xs text-gray-400">2 jam yang lalu</span>
                            </div>
                        </div>
                        
                        <!-- Activity Item 5 -->
                        <div class="activity-item activity-line relative flex">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                                <i class="fas fa-times text-red-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-800">Pembayaran Gagal</p>
                                <p class="text-xs text-gray-500">Invoice #INV-2024-002 pembayaran ditolak</p>
                                <span class="mt-1 inline-block text-xs text-gray-400">3 jam yang lalu</span>
                            </div>
                        </div>
                        
                        <!-- Activity Item 6 -->
                        <div class="activity-item relative flex">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100">
                                <i class="fas fa-file-invoice text-indigo-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-800">Laporan Dibuat</p>
                                <p class="text-xs text-gray-500">Laporan penjualan bulan Oktober</p>
                                <span class="mt-1 inline-block text-xs text-gray-400">5 jam yang lalu</span>
                            </div>
                        </div>
                    </div>
                    
                    <button class="mt-6 w-full rounded-lg border-2 border-purple-500 px-4 py-2 text-sm font-semibold text-purple-600 hover:bg-purple-50 transition-colors">
                        Lihat Semua Activity
                    </button>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="mt-6 rounded-xl bg-white p-6 shadow-lg border border-gray-100">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Transaksi Terbaru</h3>
                        <p class="text-sm text-gray-500">10 transaksi terakhir</p>
                    </div>
                    <button class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Transaksi Baru
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Invoice</th>
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Pelanggan</th>
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Produk</th>
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Total</th>
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                                <th class="pb-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 text-sm font-medium text-gray-800">#INV-2024-001</td>
                                <td class="py-4 text-sm text-gray-600">Budi Santoso</td>
                                <td class="py-4 text-sm text-gray-600">Laptop ASUS ROG</td>
                                <td class="py-4 text-sm font-semibold text-gray-800">Rp 15.000.000</td>
                                <td class="py-4">
                                    <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Lunas</span>
                                </td>
                                <td class="py-4 text-sm text-gray-600">19 Nov 2024</td>
                                <td class="py-4">
                                    <button class="text-purple-600 hover:text-purple-700">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 text-sm font-medium text-gray-800">#INV-2024-002</td>
                                <td class="py-4 text-sm text-gray-600">Siti Aminah</td>
                                <td class="py-4 text-sm text-gray-600">Mouse Gaming</td>
                                <td class="py-4 text-sm font-semibold text-gray-800">Rp 350.000</td>
                                <td class="py-4">
                                    <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Pending</span>
                                </td>
                                <td class="py-4 text-sm text-gray-600">19 Nov 2024</td>
                                <td class="py-4">
                                    <button class="text-purple-600 hover:text-purple-700">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 text-sm font-medium text-gray-800">#INV-2024-003</td>
                                <td class="py-4 text-sm text-gray-600">Ahmad Fauzi</td>
                                <td class="py-4 text-sm text-gray-600">Keyboard Mechanical</td>
                                <td class="py-4 text-sm font-semibold text-gray-800">Rp 1.200.000</td>
                                <td class="py-4">
                                    <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Lunas</span>
                                </td>
                                <td class="py-4 text-sm text-gray-600">18 Nov 2024</td>
                                <td class="py-4">
                                    <button class="text-purple-600 hover:text-purple-700">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Tunggu hingga DOM dan Chart.js siap
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan Chart.js sudah loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js belum loaded');
                return;
            }
            
            // Pastikan canvas element ada
            const canvas = document.getElementById('salesChart');
            if (!canvas) {
                console.error('Canvas salesChart tidak ditemukan');
                return;
            }
            
            // Sales Chart
            const ctx = canvas.getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['13 Nov', '14 Nov', '15 Nov', '16 Nov', '17 Nov', '18 Nov', '19 Nov'],
                    datasets: [{
                        label: 'Penjualan (Juta Rupiah)',
                        data: [5.2, 6.8, 5.9, 7.5, 6.2, 8.1, 7.3],
                        borderColor: 'rgb(147, 51, 234)',
                        backgroundColor: 'rgba(147, 51, 234, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(147, 51, 234)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'Penjualan: Rp ' + context.parsed.y + ' Juta';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value + 'M';
                                },
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
            
            console.log('Chart berhasil dibuat');
        });
        
        // Sidebar Toggle for Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarElement = document.getElementById('sidebar'); // GANTI NAMA VARIABEL
        
        if (sidebarToggle && sidebarElement) {
            sidebarToggle.addEventListener('click', function() {
                sidebarElement.classList.toggle('-translate-x-full'); // GUNAKAN NAMA BARU
            });
        }
    </script>
</body>
</html>