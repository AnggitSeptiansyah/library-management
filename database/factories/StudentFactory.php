<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('######'),
            'nisn' => fake()->unique()->numerify('##########'),
            'fullname' => fake()->name(),
            'current_class' => $this->generateClassName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // Default password
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'join_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Generate a realistic class name.
     */
    private function generateClassName(): string
    {
        $grades = [10, 11, 12];
        $majors = ['IPA', 'IPS'];
        $classes = ['A', 'B', 'C', 'D'];

        $grade = fake()->randomElement($grades);
        $major = fake()->randomElement($majors);
        $class = fake()->randomElement($classes);

        return "{$grade} {$major} {$class}";
    }

    /**
     * Indicate that the student is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the student has graduated.
     */
    public function graduated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'graduated',
            'join_date' => fake()->dateTimeBetween('-5 years', '-1 year'),
        ]);
    }

    /**
     * Indicate that the student has dropped out.
     */
    public function dropout(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'drop out',
        ]);
    }

    /**
     * Indicate specific class.
     */
    public function inClass(string $className): static
    {
        return $this->state(fn (array $attributes) => [
            'current_class' => $className,
        ]);
    }
}