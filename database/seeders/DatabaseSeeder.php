<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Admin - hanya dibuat jika belum ada
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // cek berdasarkan email
            [
                'name' => 'Huda (Admin)',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // ✅ User biasa - hanya dibuat jika belum ada
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Huda (User)',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]

        );
    }
}
