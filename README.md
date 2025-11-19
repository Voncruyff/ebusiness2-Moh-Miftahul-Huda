# 💼 E-Business 2 - Tugas Semester 5

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**Sistem Point of Sale (POS) berbasis Web**  
Dibangun menggunakan Laravel Framework untuk memenuhi tugas mata kuliah E-Business 2

[📖 Dokumentasi](#-fitur-utama) • [🚀 Instalasi](#-instalasi) • [📸 Screenshots](#-screenshots)

</div>

---

## 📋 Deskripsi Project

Aplikasi **SNV Pos** adalah sistem Point of Sale berbasis web yang dirancang untuk memudahkan manajemen transaksi penjualan. Aplikasi ini memiliki dua role utama: **Admin** dan **User**, dengan fitur-fitur yang disesuaikan untuk masing-masing peran.

### 🎯 Fitur Utama

#### 👨‍💼 Dashboard Admin
- 📊 Monitoring penjualan real-time
- 📈 Grafik analitik penjualan
- 🛍️ Manajemen transaksi
- 📦 Manajemen produk dan inventory
- 👥 Manajemen pelanggan
- 📄 Laporan penjualan
- 📋 Activity log sistem

#### 👤 Dashboard User
- 🏠 Dashboard personal
- 🛒 Riwayat pesanan
- 👤 Manajemen profil
- 📱 Notifikasi pesanan
- 💳 Download invoice
- ℹ️ Bantuan dan support

---

## 🛠️ Tech Stack

- **Framework:** Laravel 12.x
- **PHP Version:** 8.4.0
- **Database:** MySQL
- **Frontend:** Tailwind CSS, Font Awesome
- **Charts:** Chart.js

---

## 🚀 Instalasi

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (optional)

### Langkah-langkah

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/e-business-2.git
   cd e-business-2
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   
   Edit file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=snv_pos
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migration & Seeding**
   ```bash
   php artisan migrate --seed
   ```

6. **Run Application**
   ```bash
   php artisan serve
   ```
   
   Akses aplikasi di: `http://localhost:8000`

---

## 📸 Screenshots

### 🔐 Login Page
Halaman login dengan desain modern dan user-friendly interface.

![Login Page](ss/login.png)

---

### 👨‍💼 Dashboard Admin
Dashboard admin lengkap dengan statistik penjualan, grafik, dan activity log real-time.

![Admin Dashboard](ss/admin.png)

---

### 👤 Dashboard User
Dashboard user dengan tampilan ringkas menampilkan riwayat pesanan dan informasi akun.

![User Dashboard](ss/user.png)

---

### 🧩 Route List
Daftar lengkap route yang tersedia dalam aplikasi (via `php artisan route:list`).

![Route List](ss/php.png)

---

## 👥 User Roles

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| **Admin** | admin@example.com | admin123 | Full Access |
| **User** | user@example.com | user123 | Limited Access |

---

## 📂 Struktur Project

```
e-business-2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Models/
├── resources/
│   └── views/
│       ├── admin.blade.php
│       └── user.blade.php
├── routes/
│   └── web.php
├── database/
│   └── migrations/
└── public/
    └── ss/
```

---

## 📝 Fitur Mendatang

- [ ] Integrasi payment gateway
- [ ] Export laporan ke PDF/Excel
- [ ] Notifikasi real-time
- [ ] Mobile responsive optimization
- [ ] Multi-language support

---

## 👨‍💻 Developer

**Nama:** [Nama Anda]  
**NIM:** [NIM Anda]  
**Mata Kuliah:** E-Business 2 - Semester 5  
**Dosen:** [Nama Dosen]

---

## 📄 License

This project is created for educational purposes as part of E-Business 2 course assignment.

---

## 🙏 Acknowledgments

- Laravel Framework Documentation
- Tailwind CSS
- Font Awesome Icons
- Chart.js Library

---

<div align="center">

**⭐ Jika project ini bermanfaat, berikan star di repository ini! ⭐**

Made with ❤️ for E-Business 2 Course

</div>