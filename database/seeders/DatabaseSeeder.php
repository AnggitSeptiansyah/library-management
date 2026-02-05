<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;



class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@library.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1',
            'join_date' => now(),
            'is_active' => true,
        ]);

        // Create Admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567891',
            'address' => 'Jl. Admin No. 2',
            'join_date' => now(),
            'is_active' => true,
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Fiksi', 'description' => 'Buku-buku fiksi'],
            ['name' => 'Non-Fiksi', 'description' => 'Buku-buku non-fiksi'],
            ['name' => 'Sains', 'description' => 'Buku-buku sains'],
            ['name' => 'Teknologi', 'description' => 'Buku-buku teknologi'],
            ['name' => 'Sejarah', 'description' => 'Buku-buku sejarah'],
            ['name' => 'Biografi', 'description' => 'Buku-buku biografi'],
            ['name' => 'Pendidikan', 'description' => 'Buku-buku pendidikan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

    }
}
