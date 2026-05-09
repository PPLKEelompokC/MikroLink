<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@mikrolink.test'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin', // ← tambah ini
            ]
        );
        // Admin Koperasi
        User::updateOrCreate(
            ['email' => 'admin@mikrolink.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin', // ← tambah ini
            ]
        );

        // Manajer Koperasi
        User::updateOrCreate(
            ['email' => 'manajer@mikrolink.com'],
            [
                'name' => 'Manajer',
                'password' => bcrypt('password'),
                'role' => 'manager',
            ]
        );

        // Anggota biasa (untuk testing sisi anggota)
        User::updateOrCreate(
            ['email' => 'anggota@mikrolink.com'],
            [
                'name' => 'Anggota',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        $this->call(LoanSeeder::class);
        $this->call(NeracaKeuanganSeeder::class);
    }
}