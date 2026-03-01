<?php

namespace Database\Factories;

use App\Models\BorrowingItem;
use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BorrowingItem>
 */
class BorrowingItemFactory extends Factory
{
    protected $model = BorrowingItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'borrowing_id' => Borrowing::factory(),
            'book_id' => Book::factory(),
            'status' => 'borrowed',
        ];
    }

    /**
     * Indicate that the item belongs to a specific borrowing.
     */
    public function forBorrowing(Borrowing $borrowing): static
    {
        return $this->state(fn (array $attributes) => [
            'borrowing_id' => $borrowing->id,
            'status' => $borrowing->status,
        ]);
    }

    /**
     * Indicate that the item is for a specific book.
     */
    public function forBook(Book $book): static
    {
        return $this->state(fn (array $attributes) => [
            'book_id' => $book->id,
        ]);
    }

    /**
     * Indicate that the item is borrowed.
     */
    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'borrowed',
        ]);
    }

    /**
     * Indicate that the item has been returned.
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'returned',
        ]);
    }
}