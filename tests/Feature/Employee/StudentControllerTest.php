<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function employee_can_view_students_index()
    {
        Student::factory()->count(5)->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.students.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.students.index');
        $response->assertViewHas('students');
    }

    /** @test */
    public function employee_can_search_students()
    {
        Student::factory()->create(['fullname' => 'John Doe']);
        Student::factory()->create(['fullname' => 'Jane Smith']);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.students.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    /** @test */
    public function employee_can_filter_students_by_status()
    {
        Student::factory()->create(['status' => 'active']);
        Student::factory()->create(['status' => 'graduated']);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.students.index', ['status' => 'active']));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_view_create_student_form()
    {
        $response = $this->actingAs($this->employee)
            ->get(route('employee.students.create'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.students.create');
    }

    /** @test */
    public function employee_can_create_student_with_valid_data()
    {
        $studentData = [
            'nis' => '12345',
            'nisn' => '1234567890',
            'fullname' => 'Test Student',
            'email' => 'student@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '08123456789',
            'current_class' => '10 IPA A',
            'join_date' => '2024-01-01',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->employee)
            ->post(route('employee.students.store'), $studentData);

        $response->assertRedirect(route('employee.students.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('students', [
            'nis' => '12345',
            'fullname' => 'Test Student',
            'email' => 'student@test.com',
        ]);

        // Check password is hashed
        $student = Student::where('nis', '12345')->first();
        $this->assertTrue(Hash::check('password', $student->password));
    }

    /** @test */
    public function student_creation_requires_unique_nis_and_nisn()
    {
        $existing = Student::factory()->create([
            'nis' => '12345',
            'nisn' => '1234567890',
        ]);

        $response = $this->actingAs($this->employee)
            ->post(route('employee.students.store'), [
                'nis' => '12345', // Duplicate
                'nisn' => '0987654321',
                'fullname' => 'Test Student',
                'email' => 'new@test.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'join_date' => '2024-01-01',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors('nis');
    }

    /** @test */
    public function employee_can_view_student_details()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.students.show', $student));

        $response->assertStatus(200);
        $response->assertViewIs('employee.students.show');
        $response->assertViewHas('student', $student);
    }

    /** @test */
    public function employee_can_view_edit_student_form()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.students.edit', $student));

        $response->assertStatus(200);
        $response->assertViewIs('employee.students.edit');
        $response->assertViewHas('student', $student);
    }

    /** @test */
    public function employee_can_update_student()
    {
        $student = Student::factory()->create([
            'fullname' => 'Old Name',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->employee)
            ->put(route('employee.students.update', $student), [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'fullname' => 'New Name',
                'email' => $student->email,
                'phone' => $student->phone,
                'current_class' => $student->current_class,
                'join_date' => $student->join_date->format('Y-m-d'),
                'status' => 'graduated',
            ]);

        $response->assertRedirect(route('employee.students.index'));
        
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'fullname' => 'New Name',
            'status' => 'graduated',
        ]);
    }

    /** @test */
    public function employee_can_update_student_password()
    {
        $student = Student::factory()->create();
        $oldPassword = $student->password;

        $response = $this->actingAs($this->employee)
            ->put(route('employee.students.update', $student), [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'fullname' => $student->fullname,
                'email' => $student->email,
                'join_date' => $student->join_date->format('Y-m-d'),
                'status' => $student->status,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertRedirect(route('employee.students.index'));
        
        $student->refresh();
        $this->assertNotEquals($oldPassword, $student->password);
        $this->assertTrue(Hash::check('newpassword', $student->password));
    }

    /** @test */
    public function employee_can_update_student_status()
    {
        $student = Student::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->employee)
            ->patch(route('employee.students.update-status', $student), [
                'status' => 'graduated',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'graduated',
        ]);
    }

    /** @test */
    public function employee_can_delete_student_without_borrowings()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.students.destroy', $student));

        $response->assertRedirect(route('employee.students.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    /** @test */
    public function employee_cannot_delete_student_with_borrowings()
    {
        $student = Student::factory()->create();
        $borrowing = \App\Models\Borrowing::factory()->create([
            'student_id' => $student->id,
            'processed_by' => $this->employee->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.students.destroy', $student));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    /** @test */
    public function guest_cannot_access_students_management()
    {
        $response = $this->get(route('employee.students.index'));
        $response->assertRedirect(route('employee.login'));
    }
}