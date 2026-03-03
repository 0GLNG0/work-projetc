<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
public function run(): void
    {
        // 1. Panggil Seeder Lokasi (Rumus Excel Liter/Detik)
        $this->call([
            LokasiSeeder::class,
        ]);

        // 2. Buat User secara MANUAL (Tanpa Factory/Fake) [cite: 2026-02-12]
        \App\Models\User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'), // Jangan lupa kasih password [cite: 2026-02-12]
            ]
        );
    }
}
