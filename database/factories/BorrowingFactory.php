<?php

namespace Database\Factories;

use App\Models\Borrowing;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $borrowDate = fake()->dateTimeBetween('-30 days', 'now');
        $dueDate = Carbon::parse($borrowDate)->addDays(7);

        return [
            'borrowing_code' => $this->generateBorrowingCode(),
            'student_id' => Student::factory(),
            'processed_by' => User::factory(),
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'status' => 'borrowed',
            'total_fine' => 0,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Generate a unique borrowing code.
     */
    private function generateBorrowingCode(): string
    {
        $date = date('Ymd');
        $random = str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
        return "BRW-{$date}-{$random}";
    }

    /**
     * Indicate that the borrowing is for a specific student.
     */
    public function forStudent(Student $student): static
    {
        return $this->state(fn (array $attributes) => [
            'student_id' => $student->id,
        ]);
    }

    /**
     * Indicate that the borrowing was processed by a specific employee.
     */
    public function processedBy(User $employee): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_by' => $employee->id,
        ]);
    }

    /**
     * Indicate that the borrowing is borrowed status.
     */
    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'borrowed',
            'return_date' => null,
        ]);
    }

    /**
     * Indicate that the borrowing is overdue.
     */
    public function overdue(int $daysOverdue = 3): static
    {
        $borrowDate = Carbon::now()->subDays(7 + $daysOverdue);
        $dueDate = Carbon::parse($borrowDate)->addDays(7);
        $fine = min($daysOverdue * 500, 50000);

        return $this->state(fn (array $attributes) => [
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'status' => 'overdue',
            'return_date' => null,
            'total_fine' => $fine,
        ]);
    }

    /**
     * Indicate that the borrowing has been returned.
     */
    public function returned(int $daysLate = 0): static
    {
        $borrowDate = fake()->dateTimeBetween('-30 days', '-10 days');
        $dueDate = Carbon::parse($borrowDate)->addDays(7);
        $returnDate = Carbon::parse($dueDate)->addDays($daysLate);
        $fine = $daysLate > 0 ? min($daysLate * 500, 50000) : 0;

        return $this->state(fn (array $attributes) => [
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'return_date' => $returnDate,
            'status' => 'returned',
            'total_fine' => $fine,
        ]);
    }

    /**
     * Indicate specific dates.
     */
    public function withDates(string $borrowDate, ?string $dueDate = null, ?string $returnDate = null): static
    {
        $borrow = Carbon::parse($borrowDate);
        $due = $dueDate ? Carbon::parse($dueDate) : $borrow->copy()->addDays(7);

        return $this->state(fn (array $attributes) => [
            'borrow_date' => $borrow,
            'due_date' => $due,
            'return_date' => $returnDate ? Carbon::parse($returnDate) : null,
        ]);
    }

    /**
     * Indicate with fine.
     */
    public function withFine(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'total_fine' => $amount,
        ]);
    }
}