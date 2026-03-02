<?php

namespace Database\Seeders;

use App\Models\Borrowing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use Database\Factories\BorrowingItemFactory;
use Illuminate\Support\Facades\Hash;



class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StudentSeeder::class,
            CategorySeeder::class,
            BookSeeder::class,
        ]);
    }
}
