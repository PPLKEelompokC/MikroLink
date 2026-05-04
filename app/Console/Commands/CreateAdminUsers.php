<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUsers extends Command
{
    protected $signature = 'app:create-admin-users';

    protected $description = 'Create Admin, Manager, and Super Admin users';

    public function handle(): int
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@mikrolink.test',
                'password' => 'password',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@mikrolink.test',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@mikrolink.test',
                'password' => 'password',
                'role' => 'manager',
            ],
        ];

        foreach ($users as $userData) {
            $existing = User::where('email', $userData['email'])->first();

            if ($existing) {
                $this->warn("User {$userData['email']} already exists, skipping...");

                continue;
            }

            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'],
            ]);

            $this->info("Created {$userData['role']} user: {$userData['email']} (password: {$userData['password']})");
        }

        return Command::SUCCESS;
    }
}
