<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating employees...');

        // Create Super Admin
        User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@library.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '081234567890',
            'address' => 'Jl. Perpustakaan No. 1, Jakarta',
            'join_date' => now()->subYears(5),
            'is_active' => true,
        ]);

        // Create Admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567891',
            'address' => 'Jl. Perpustakaan No. 2, Jakarta',
            'join_date' => now()->subYears(3),
            'is_active' => true,
        ]);

        // Create additional admin staff
        User::create([
            'name' => 'librarian1',
            'email' => 'budi@library.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567892',
            'address' => 'Jl. Pustaka No. 10, Jakarta',
            'join_date' => now()->subYears(2),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'librarian2',
            'email' => 'siti@library.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567893',
            'address' => 'Jl. Buku No. 5, Jakarta',
            'join_date' => now()->subYear(),
            'is_active' => true,
        ]);

        // Create 5 more random employees
        User::factory()->count(5)->create();

        // Create 1 inactive employee
        User::factory()->inactive()->create();

        $this->command->info('✅ Created ' . User::count() . ' employees');
    }
}