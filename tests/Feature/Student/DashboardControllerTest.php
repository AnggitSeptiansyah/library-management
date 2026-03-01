<?php

namespace Tests\Feature\Student;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = Student::factory()->create(['status' => 'active']);
    }

    /** @test */
    public function student_can_view_dashboard()
    {
        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('student.dashboard');
        $response->assertViewHas(['stats', 'activeBorrowings']);
    }

    /** @test */
    public function dashboard_shows_correct_statistics()
    {
        $employee = User::factory()->create(['role' => 'admin']);
        
        // Create borrowings
        Borrowing::factory()->count(2)->create([
            'student_id' => $this->student->id,
            'processed_by' => $employee->id,
            'status' => 'borrowed',
        ]);
        
        Borrowing::factory()->create([
            'student_id' => $this->student->id,
            'processed_by' => $employee->id,
            'status' => 'returned',
        ]);

        $overdueView = Borrowing::factory()->create([
            'student_id' => $this->student->id,
            'processed_by' => $employee->id,
            'status' => 'overdue',
            'total_fine' => 1500,
        ]);

        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.dashboard'));

        $stats = $response->viewData('stats');
        
        $this->assertEquals(2, $stats['active_borrowings']);
        $this->assertEquals(4, $stats['total_borrowings']);
        $this->assertEquals(1, $stats['overdue_borrowings']);
        $this->assertEquals(1500, $stats['total_fines']);
    }

    /** @test */
    public function student_can_view_catalog()
    {
        Book::factory()->count(10)->create();

        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.catalog'));

        $response->assertStatus(200);
        $response->assertViewIs('student.catalog');
        $response->assertViewHas('books');
    }

    /** @test */
    public function student_can_search_books_in_catalog()
    {
        Book::factory()->create(['title' => 'Laravel Programming']);
        Book::factory()->create(['title' => 'PHP Basics']);

        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.catalog', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Programming');
        $response->assertDontSee('PHP Basics');
    }

    /** @test */
    public function student_can_view_borrowing_history()
    {
        $employee = User::factory()->create(['role' => 'admin']);
        
        Borrowing::factory()->count(3)->create([
            'student_id' => $this->student->id,
            'processed_by' => $employee->id,
        ]);

        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.borrowings'));

        $response->assertStatus(200);
        $response->assertViewIs('student.borrowing-history');
        $response->assertViewHas('borrowings');
    }

    /** @test */
    public function student_can_view_profile()
    {
        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('student.profile');
        $response->assertViewHas('student', $this->student);
    }

    /** @test */
    public function student_can_update_profile()
    {
        $response = $this->actingAs($this->student, 'student')
            ->patch(route('student.profile.update'), [
                'fullname' => 'Updated Name',
                'email' => 'updated@test.com',
                'phone' => '08199999999',
                'address' => 'New Address',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->student->refresh();
        $this->assertEquals('Updated Name', $this->student->fullname);
        $this->assertEquals('updated@test.com', $this->student->email);
    }

    /** @test */
    public function student_can_change_password()
    {
        $response = $this->actingAs($this->student, 'student')
            ->patch(route('student.profile.password'), [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->student->refresh();
        $this->assertTrue(Hash::check('newpassword', $this->student->password));
    }

    /** @test */
    public function password_change_requires_current_password()
    {
        $response = $this->actingAs($this->student, 'student')
            ->patch(route('student.profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    /** @test */
    public function employee_cannot_access_student_portal()
    {
        $employee = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($employee)
            ->get(route('student.dashboard'));

        $response->assertStatus(403);
    }
}