<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalCopies = fake()->numberBetween(1, 10);
        
        return [
            'title' => fake()->unique()->sentence(fake()->numberBetween(2, 6)),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'publication_year' => fake()->year(),
            'isbn' => fake()->unique()->isbn13(),
            'category_id' => Category::factory(),
            'total_copies' => $totalCopies,
            'available_copies' => $totalCopies, // Initially all available
            'description' => fake()->paragraph(),
            'cover_image' => null, // Will be set manually if needed
        ];
    }

    /**
     * Indicate that the book has a specific category.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Indicate that the book is unavailable.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_copies' => 0,
        ]);
    }

    /**
     * Indicate that the book has limited copies.
     */
    public function limited(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_copies' => 1,
            'available_copies' => 1,
        ]);
    }

    /**
     * Indicate that the book has many copies.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_copies' => fake()->numberBetween(10, 50),
            'available_copies' => fake()->numberBetween(5, 50),
        ]);
    }

    /**
     * Indicate specific copies.
     */
    public function withCopies(int $total, int $available = null): static
    {
        return $this->state(fn (array $attributes) => [
            'total_copies' => $total,
            'available_copies' => $available ?? $total,
        ]);
    }

    /**
     * Indicate that the book has a description.
     */
    public function withDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->paragraphs(3, true),
        ]);
    }

    /**
     * Indicate that the book has no description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}