<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating books...');

        // Get categories
        $fiction = Category::where('name', 'Fiction')->first();
        $nonFiction = Category::where('name', 'Non-Fiction')->first();
        $science = Category::where('name', 'Science')->first();
        $technology = Category::where('name', 'Technology')->first();
        $history = Category::where('name', 'History')->first();
        $mathematics = Category::where('name', 'Mathematics')->first();
        $literature = Category::where('name', 'Literature')->first();
        $business = Category::where('name', 'Business')->first();

        // Create specific popular books
        $popularBooks = [
            // Fiction
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'publisher' => 'Bloomsbury Publishing',
                'publication_year' => 1997,
                'isbn' => '9780747532699',
                'category_id' => $fiction->id,
                'total_copies' => 15,
                'available_copies' => 12,
                'description' => 'The first novel in the Harry Potter series about a young wizard\'s adventures at Hogwarts School of Witchcraft and Wizardry.',
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'publisher' => 'J.B. Lippincott & Co.',
                'publication_year' => 1960,
                'isbn' => '9780061120084',
                'category_id' => $fiction->id,
                'total_copies' => 10,
                'available_copies' => 8,
                'description' => 'A gripping tale of racial injustice and childhood innocence in the American South.',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'publisher' => 'Secker & Warburg',
                'publication_year' => 1949,
                'isbn' => '9780451524935',
                'category_id' => $fiction->id,
                'total_copies' => 12,
                'available_copies' => 10,
                'description' => 'A dystopian social science fiction novel exploring themes of totalitarianism and surveillance.',
            ],
            
            // Technology
            [
                'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author' => 'Robert C. Martin',
                'publisher' => 'Prentice Hall',
                'publication_year' => 2008,
                'isbn' => '9780132350884',
                'category_id' => $technology->id,
                'total_copies' => 8,
                'available_copies' => 5,
                'description' => 'A guide to producing readable, reusable, and refactorable software.',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'David Thomas, Andrew Hunt',
                'publisher' => 'Addison-Wesley',
                'publication_year' => 1999,
                'isbn' => '9780201616224',
                'category_id' => $technology->id,
                'total_copies' => 6,
                'available_copies' => 4,
                'description' => 'A book about software development and programming best practices.',
            ],
            
            // Science
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'publisher' => 'Bantam Books',
                'publication_year' => 1988,
                'isbn' => '9780553380163',
                'category_id' => $science->id,
                'total_copies' => 10,
                'available_copies' => 7,
                'description' => 'A landmark volume in science writing exploring cosmology, black holes, and the nature of time.',
            ],
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'publisher' => 'Harper',
                'publication_year' => 2011,
                'isbn' => '9780062316097',
                'category_id' => $history->id,
                'total_copies' => 12,
                'available_copies' => 9,
                'description' => 'A narrative of humanity\'s creation and evolution.',
            ],
            
            // Business
            [
                'title' => 'Think and Grow Rich',
                'author' => 'Napoleon Hill',
                'publisher' => 'The Ralston Society',
                'publication_year' => 1937,
                'isbn' => '9781585424337',
                'category_id' => $business->id,
                'total_copies' => 8,
                'available_copies' => 6,
                'description' => 'A personal development and self-improvement book.',
            ],
            [
                'title' => 'The Lean Startup',
                'author' => 'Eric Ries',
                'publisher' => 'Crown Business',
                'publication_year' => 2011,
                'isbn' => '9780307887894',
                'category_id' => $business->id,
                'total_copies' => 7,
                'available_copies' => 5,
                'description' => 'A methodology for developing businesses and products.',
            ],
            
            // Mathematics
            [
                'title' => 'Introduction to Linear Algebra',
                'author' => 'Gilbert Strang',
                'publisher' => 'Wellesley-Cambridge Press',
                'publication_year' => 2016,
                'isbn' => '9780980232776',
                'category_id' => $mathematics->id,
                'total_copies' => 10,
                'available_copies' => 8,
                'description' => 'A comprehensive introduction to linear algebra.',
            ],
        ];

        foreach ($popularBooks as $book) {
            Book::create($book);
        }

        // Create 40 more random books using factory
        $categories = Category::all();
        
        foreach ($categories as $category) {
            // 3-5 books per category
            $count = rand(3, 5);
            Book::factory()
                ->count($count)
                ->forCategory($category)
                ->create();
        }

        $this->command->info('✅ Created ' . Book::count() . ' books');
    }
}