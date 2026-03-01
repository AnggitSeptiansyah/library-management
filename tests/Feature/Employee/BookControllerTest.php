<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->employee = User::factory()->create(['role' => 'admin']);
        $this->category = Category::factory()->create();
        
        Storage::fake('public');
    }

    /** @test */
    public function employee_can_view_books_index()
    {
        Book::factory()->count(5)->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.books.index');
        $response->assertViewHas('books');
    }

    /** @test */
    public function employee_can_search_books()
    {
        Book::factory()->create(['title' => 'Laravel Programming']);
        Book::factory()->create(['title' => 'PHP Basics']);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.books.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Programming');
        $response->assertDontSee('PHP Basics');
    }

    /** @test */
    public function employee_can_filter_books_by_category()
    {
        $category1 = Category::factory()->create(['name' => 'Programming']);
        $category2 = Category::factory()->create(['name' => 'Fiction']);

        Book::factory()->create(['category_id' => $category1->id]);
        Book::factory()->create(['category_id' => $category2->id]);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.books.index', ['category' => $category1->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_view_create_book_form()
    {
        $response = $this->actingAs($this->employee)
            ->get(route('employee.books.create'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.books.create');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function employee_can_create_book_with_valid_data()
    {
        $bookData = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'publication_year' => 2024,
            'isbn' => '1234567890123',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'description' => 'Test description',
        ];

        $response = $this->actingAs($this->employee)
            ->post(route('employee.books.store'), $bookData);

        $response->assertRedirect(route('employee.books.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'available_copies' => 5,
        ]);
    }

    /** @test */
    public function employee_can_create_book_with_image()
    {
        $image = UploadedFile::fake()->image('book-cover.jpg');

        $bookData = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'publication_year' => 2024,
            'isbn' => '1234567890123',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'cover_image' => $image,
        ];

        $response = $this->actingAs($this->employee)
            ->post(route('employee.books.store'), $bookData);

        $response->assertRedirect(route('employee.books.index'));
        
        $book = Book::where('title', 'Test Book')->first();
        $this->assertNotNull($book->cover_image);
        Storage::disk('public')->assertExists($book->cover_image);
    }

    /** @test */
    public function book_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->employee)
            ->post(route('employee.books.store'), []);

        $response->assertSessionHasErrors([
            'title', 'author', 'publisher', 'isbn', 
            'category_id', 'total_copies'
        ]);
    }

    /** @test */
    public function employee_can_view_edit_book_form()
    {
        $book = Book::factory()->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.books.edit', $book));

        $response->assertStatus(200);
        $response->assertViewIs('employee.books.edit');
        $response->assertViewHas('book', $book);
        $response->assertViewHas('categories');
    }

    /** @test */
    public function employee_can_update_book()
    {
        $book = Book::factory()->create([
            'title' => 'Old Title',
            'total_copies' => 5,
            'available_copies' => 5,
        ]);

        $response = $this->actingAs($this->employee)
            ->put(route('employee.books.update', $book), [
                'title' => 'New Title',
                'author' => $book->author,
                'publisher' => $book->publisher,
                'publication_year' => $book->publication_year,
                'isbn' => $book->isbn,
                'category_id' => $book->category_id,
                'total_copies' => 10,
            ]);

        $response->assertRedirect(route('employee.books.index'));
        
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'New Title',
            'total_copies' => 10,
            'available_copies' => 10, // Should increase by 5
        ]);
    }

    /** @test */
    public function employee_can_delete_book_without_borrowings()
    {
        $book = Book::factory()->create();

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.books.destroy', $book));

        $response->assertRedirect(route('employee.books.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /** @test */
    public function employee_cannot_delete_book_with_borrowings()
    {
        $book = Book::factory()->create();
        $student = \App\Models\Student::factory()->create();
        $borrowing = \App\Models\Borrowing::factory()->create([
            'student_id' => $student->id,
            'processed_by' => $this->employee->id,
        ]);
        
        \App\Models\BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.books.destroy', $book));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    /** @test */
    public function guest_cannot_access_books_management()
    {
        $response = $this->get(route('employee.books.index'));
        $response->assertRedirect(route('employee.login'));

        $response = $this->get(route('employee.books.create'));
        $response->assertRedirect(route('employee.login'));
    }
}