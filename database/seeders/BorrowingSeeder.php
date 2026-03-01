<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Student;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;

class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating borrowings...');

        $students = Student::where('status', 'active')->get();
        $books = Book::where('available_copies', '>', 0)->get();
        $employees = User::where('role', 'admin')->get();

        if ($students->isEmpty() || $books->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('⚠️  Not enough data to create borrowings. Skipping...');
            return;
        }

        // Create 15 active borrowings (currently borrowed)
        for ($i = 0; $i < 15; $i++) {
            $student = $students->random();
            $employee = $employees->random();
            
            $borrowing = Borrowing::factory()
                ->forStudent($student)
                ->processedBy($employee)
                ->borrowed()
                ->create();

            // Add 1-3 books to this borrowing
            $bookCount = rand(1, 3);
            $borrowedBooks = $books->random(min($bookCount, $books->count()));
            
            foreach ($borrowedBooks as $book) {
                if ($book->available_copies > 0) {
                    BorrowingItem::create([
                        'borrowing_id' => $borrowing->id,
                        'book_id' => $book->id,
                        'status' => 'borrowed',
                    ]);
                    
                    // Decrease available copies
                    $book->decrement('available_copies');
                }
            }
        }

        // Create 8 overdue borrowings
        for ($i = 0; $i < 8; $i++) {
            $student = $students->random();
            $employee = $employees->random();
            $daysOverdue = rand(1, 10);
            
            $borrowing = Borrowing::factory()
                ->forStudent($student)
                ->processedBy($employee)
                ->overdue($daysOverdue)
                ->create();

            // Add 1-2 books
            $bookCount = rand(1, 2);
            $borrowedBooks = $books->random(min($bookCount, $books->count()));
            
            foreach ($borrowedBooks as $book) {
                if ($book->available_copies > 0) {
                    BorrowingItem::create([
                        'borrowing_id' => $borrowing->id,
                        'book_id' => $book->id,
                        'status' => 'overdue',
                    ]);
                    
                    $book->decrement('available_copies');
                }
            }
        }

        // Create 30 returned borrowings (history)
        for ($i = 0; $i < 30; $i++) {
            $student = $students->random();
            $employee = $employees->random();
            $daysLate = rand(0, 5); // 0-5 days late
            
            $borrowing = Borrowing::factory()
                ->forStudent($student)
                ->processedBy($employee)
                ->returned($daysLate)
                ->create();

            // Add 1-3 books
            $bookCount = rand(1, 3);
            $borrowedBooks = $books->random(min($bookCount, $books->count()));
            
            foreach ($borrowedBooks as $book) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $book->id,
                    'status' => 'returned',
                ]);
                
                // Books already returned, no need to update stock
            }
        }

        // Create 5 specific scenario borrowings
        $this->createScenarioBorrowings($students, $books, $employees);

        $this->command->info('✅ Created ' . Borrowing::count() . ' borrowings with ' . BorrowingItem::count() . ' items');
    }

    /**
     * Create specific scenario borrowings for testing.
     */
    private function createScenarioBorrowings($students, $books, $employees): void
    {
        if ($students->isEmpty() || $books->isEmpty() || $employees->isEmpty()) {
            return;
        }

        $student = $students->first();
        $employee = $employees->first();

        // Scenario 1: Borrowing due today
        $borrowing1 = Borrowing::create([
            'borrowing_code' => 'BRW-' . date('Ymd') . '-9991',
            'student_id' => $student->id,
            'processed_by' => $employee->id,
            'borrow_date' => Carbon::today()->subDays(7),
            'due_date' => Carbon::today(),
            'return_date' => null,
            'status' => 'borrowed',
            'total_fine' => 0,
            'notes' => 'Due today - test scenario',
        ]);

        $book = $books->where('available_copies', '>', 0)->first();
        if ($book) {
            BorrowingItem::create([
                'borrowing_id' => $borrowing1->id,
                'book_id' => $book->id,
                'status' => 'borrowed',
            ]);
            $book->decrement('available_copies');
        }

        // Scenario 2: Heavily overdue (30 days)
        if ($students->count() > 1) {
            $borrowing2 = Borrowing::create([
                'borrowing_code' => 'BRW-' . date('Ymd', strtotime('-30 days')) . '-9992',
                'student_id' => $students->skip(1)->first()->id,
                'processed_by' => $employee->id,
                'borrow_date' => Carbon::today()->subDays(37),
                'due_date' => Carbon::today()->subDays(30),
                'return_date' => null,
                'status' => 'overdue',
                'total_fine' => 15000, // 30 days × Rp 500
                'notes' => 'Heavily overdue - test scenario',
            ]);

            $book2 = $books->where('available_copies', '>', 0)->skip(1)->first();
            if ($book2) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing2->id,
                    'book_id' => $book2->id,
                    'status' => 'borrowed',
                ]);
                $book2->decrement('available_copies');
            }
        }

        // Scenario 3: Recently returned on time
        if ($students->count() > 2) {
            $borrowing3 = Borrowing::create([
                'borrowing_code' => 'BRW-' . date('Ymd', strtotime('-10 days')) . '-9993',
                'student_id' => $students->skip(2)->first()->id,
                'processed_by' => $employee->id,
                'borrow_date' => Carbon::today()->subDays(10),
                'due_date' => Carbon::today()->subDays(3),
                'return_date' => Carbon::today()->subDays(3),
                'status' => 'returned',
                'total_fine' => 0,
                'notes' => 'Returned on time - test scenario',
            ]);

            $book3 = $books->skip(2)->first();
            if ($book3) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing3->id,
                    'book_id' => $book3->id,
                    'status' => 'returned',
                ]);
            }
        }
    }
}