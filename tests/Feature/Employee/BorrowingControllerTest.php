<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Student;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BorrowingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;
    protected $student;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->employee = User::factory()->create(['role' => 'admin']);
        $this->student = Student::factory()->create(['status' => 'active']);
        $this->book = Book::factory()->create([
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
    }

    /** @test */
    public function employee_can_view_borrowings_index()
    {
        Borrowing::factory()->count(3)->create([
            'processed_by' => $this->employee->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.borrowings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.borrowings.index');
        $response->assertViewHas('borrowings');
    }

    /** @test */
    public function employee_can_search_borrowings()
    {
        $student1 = Student::factory()->create(['fullname' => 'John Doe']);
        $student2 = Student::factory()->create(['fullname' => 'Jane Smith']);

        Borrowing::factory()->create([
            'student_id' => $student1->id,
            'processed_by' => $this->employee->id,
        ]);
        Borrowing::factory()->create([
            'student_id' => $student2->id,
            'processed_by' => $this->employee->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.borrowings.index', ['search' => 'John']));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_filter_borrowings_by_status()
    {
        Borrowing::factory()->create([
            'status' => 'borrowed',
            'processed_by' => $this->employee->id,
        ]);
        Borrowing::factory()->create([
            'status' => 'returned',
            'processed_by' => $this->employee->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.borrowings.index', ['status' => 'borrowed']));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_view_create_borrowing_form()
    {
        $response = $this->actingAs($this->employee)
            ->get(route('employee.borrowings.create'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.borrowings.create');
        $response->assertViewHas('students');
        $response->assertViewHas('books');
    }

    /** @test */
    public function employee_can_create_borrowing()
    {
        $borrowData = [
            'student_id' => $this->student->id,
            'borrow_date' => Carbon::today()->format('Y-m-d'),
            'book_ids' => [$this->book->id],
        ];

        $response = $this->actingAs($this->employee)
            ->post(route('employee.borrowings.store'), $borrowData);

        $response->assertRedirect(route('employee.borrowings.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('borrowings', [
            'student_id' => $this->student->id,
            'processed_by' => $this->employee->id,
            'status' => 'borrowed',
        ]);

        // Check book available copies decreased
        $this->book->refresh();
        $this->assertEquals(4, $this->book->available_copies);
    }

    /** @test */
    public function borrowing_creates_due_date_automatically()
    {
        $borrowDate = Carbon::parse('2024-01-01');
        
        $borrowData = [
            'student_id' => $this->student->id,
            'borrow_date' => $borrowDate->format('Y-m-d'),
            'book_ids' => [$this->book->id],
        ];

        $this->actingAs($this->employee)
            ->post(route('employee.borrowings.store'), $borrowData);

        $borrowing = Borrowing::latest()->first();
        
        // Due date should be 7 days from borrow date
        $expectedDueDate = $borrowDate->copy()->addDays(7);
        $this->assertEquals($expectedDueDate->format('Y-m-d'), $borrowing->due_date->format('Y-m-d'));
    }

    /** @test */
    public function borrowing_generates_unique_code()
    {
        $borrowData = [
            'student_id' => $this->student->id,
            'borrow_date' => Carbon::today()->format('Y-m-d'),
            'book_ids' => [$this->book->id],
        ];

        $this->actingAs($this->employee)
            ->post(route('employee.borrowings.store'), $borrowData);

        $borrowing = Borrowing::latest()->first();
        
        $this->assertNotNull($borrowing->borrowing_code);
        $this->assertStringStartsWith('BRW-', $borrowing->borrowing_code);
    }

    /** @test */
    public function borrowing_can_include_multiple_books()
    {
        $book2 = Book::factory()->create([
            'total_copies' => 3,
            'available_copies' => 3,
        ]);

        $borrowData = [
            'student_id' => $this->student->id,
            'borrow_date' => Carbon::today()->format('Y-m-d'),
            'book_ids' => [$this->book->id, $book2->id],
        ];

        $this->actingAs($this->employee)
            ->post(route('employee.borrowings.store'), $borrowData);

        $borrowing = Borrowing::latest()->first();
        
        $this->assertEquals(2, $borrowing->items()->count());
        
        // Check both books' available copies decreased
        $this->book->refresh();
        $book2->refresh();
        $this->assertEquals(4, $this->book->available_copies);
        $this->assertEquals(2, $book2->available_copies);
    }

    /** @test */
    public function cannot_borrow_unavailable_book()
    {
        $unavailableBook = Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 0,
        ]);

        $borrowData = [
            'student_id' => $this->student->id,
            'borrow_date' => Carbon::today()->format('Y-m-d'),
            'book_ids' => [$unavailableBook->id],
        ];

        $response = $this->actingAs($this->employee)
            ->post(route('employee.borrowings.store'), $borrowData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function employee_can_view_borrowing_details()
    {
        $borrowing = Borrowing::factory()->create([
            'student_id' => $this->student->id,
            'processed_by' => $this->employee->id,
        ]);

        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.borrowings.show', $borrowing));

        $response->assertStatus(200);
        $response->assertViewIs('employee.borrowings.show');
        $response->assertViewHas('borrowing', $borrowing);
    }

    /** @test */
    public function employee_can_return_books()
    {
        $borrowing = Borrowing::factory()->create([
            'student_id' => $this->student->id,
            'processed_by' => $this->employee->id,
            'status' => 'borrowed',
            'borrow_date' => Carbon::today()->subDays(3),
            'due_date' => Carbon::today()->addDays(4),
        ]);

        $item = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->employee)
            ->post(route('employee.borrowings.return', $borrowing), [
                'return_date' => Carbon::today()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $borrowing->refresh();
        $this->assertEquals('returned', $borrowing->status);
        $this->assertNotNull($borrowing->return_date);
        
        // Check book available copies increased
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }

    /** @test */
    public function overdue_borrowing_calculates_fine()
    {
        $borrowing = Borrowing::factory()->create([
            'student_id' => $this->student->id,
            'processed_by' => $this->employee->id,
            'status' => 'borrowed',
            'borrow_date' => Carbon::today()->subDays(10),
            'due_date' => Carbon::today()->subDays(3), // 3 days overdue
        ]);

        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->post(route('employee.borrowings.return', $borrowing), [
                'return_date' => Carbon::today()->format('Y-m-d'),
            ]);

        $borrowing->refresh();
        
        // 3 days overdue × Rp 500 = Rp 1500
        $this->assertEquals(1500, $borrowing->total_fine);
    }

    /** @test */
    public function guest_cannot_access_borrowings_management()
    {
        $response = $this->get(route('employee.borrowings.index'));
        $response->assertRedirect(route('employee.login'));
    }
}