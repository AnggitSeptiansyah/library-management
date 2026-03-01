<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating categories...');

        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Fictional stories, novels, and imaginative literature',
            ],
            [
                'name' => 'Non-Fiction',
                'description' => 'Real-world topics, factual content, and true stories',
            ],
            [
                'name' => 'Science',
                'description' => 'Scientific books, research, and discoveries',
            ],
            [
                'name' => 'Technology',
                'description' => 'Technology, computer science, and programming books',
            ],
            [
                'name' => 'History',
                'description' => 'Historical books, events, and biographies',
            ],
            [
                'name' => 'Mathematics',
                'description' => 'Mathematics, statistics, and mathematical theories',
            ],
            [
                'name' => 'Literature',
                'description' => 'Classic and modern literature, poetry, and drama',
            ],
            [
                'name' => 'Biography',
                'description' => 'Life stories, memoirs, and autobiographies',
            ],
            [
                'name' => 'Self-Help',
                'description' => 'Personal development, motivation, and self-improvement',
            ],
            [
                'name' => 'Religion',
                'description' => 'Religious texts, spiritual books, and theology',
            ],
            [
                'name' => 'Philosophy',
                'description' => 'Philosophical texts, thoughts, and theories',
            ],
            [
                'name' => 'Psychology',
                'description' => 'Psychology, mental health, and behavioral science',
            ],
            [
                'name' => 'Business',
                'description' => 'Business, economics, and entrepreneurship books',
            ],
            [
                'name' => 'Arts',
                'description' => 'Art, music, culture, and creative expression',
            ],
            [
                'name' => 'Reference',
                'description' => 'Dictionaries, encyclopedias, and reference materials',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ Created ' . Category::count() . ' categories');
    }
}