<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Koperasi
        User::updateOrCreate(
            ['email' => 'admin@mikrolink.com'],
            [
                'name'     => 'Admin User',
                'password' => bcrypt('password'),
                'role'     => 'Admin Koperasi', // ← tambah ini
            ]
        );

        // Manajer Koperasi
        User::updateOrCreate(
            ['email' => 'manajer@mikrolink.com'],
            [
                'name'     => 'Manajer User',
                'password' => bcrypt('password'),
                'role'     => 'Manajer Koperasi',
            ]
        );

        // Anggota biasa (untuk testing sisi anggota)
        User::updateOrCreate(
            ['email' => 'anggota@mikrolink.com'],
            [
                'name'     => 'Anggota User',
                'password' => bcrypt('password'),
                'role'     => 'user',
            ]
        );
    }
}