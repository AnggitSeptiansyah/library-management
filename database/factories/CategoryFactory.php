<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['name' => 'Fiction', 'description' => 'Fictional stories and novels'],
            ['name' => 'Non-Fiction', 'description' => 'Real-world topics and factual content'],
            ['name' => 'Science', 'description' => 'Scientific books and research'],
            ['name' => 'Technology', 'description' => 'Technology and computer science books'],
            ['name' => 'History', 'description' => 'Historical books and biographies'],
            ['name' => 'Mathematics', 'description' => 'Mathematics and statistics books'],
            ['name' => 'Literature', 'description' => 'Classic and modern literature'],
            ['name' => 'Biography', 'description' => 'Life stories and memoirs'],
            ['name' => 'Self-Help', 'description' => 'Personal development and motivation'],
            ['name' => 'Religion', 'description' => 'Religious and spiritual books'],
            ['name' => 'Philosophy', 'description' => 'Philosophical texts and thoughts'],
            ['name' => 'Psychology', 'description' => 'Psychology and mental health'],
            ['name' => 'Business', 'description' => 'Business and economics books'],
            ['name' => 'Arts', 'description' => 'Art, music, and culture'],
            ['name' => 'Reference', 'description' => 'Dictionaries, encyclopedias, and reference materials'],
        ];

        $category = fake()->unique()->randomElement($categories);

        return [
            'name' => $category['name'],
            'description' => $category['description'],
        ];
    }

    /**
     * Indicate category without description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}