<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // 🧩 Tambahkan ini
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Buat akun ADMIN default
        User::factory()->create([
            'name' => 'Huda (Admin)',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // 🟢 gunakan Hash::make()
            'role' => 'admin',
        ]);

        // ✅ Buat akun USER biasa
        User::factory()->create([
            'name' => 'Huda (User)',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'), // 🟢 juga pakai Hash::make()
            'role' => 'user',
        ]);
    }
}
