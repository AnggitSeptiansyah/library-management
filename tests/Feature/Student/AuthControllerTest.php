<?php

namespace Tests\Feature\Student;

use Tests\TestCase;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_can_view_login_page()
    {
        $response = $this->get(route('student.login'));

        $response->assertStatus(200);
        $response->assertViewIs('student.auth.login');
    }

    /** @test */
    public function student_can_login_with_valid_credentials()
    {
        $student = Student::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $response = $this->post(route('student.login'), [
            'email' => 'student@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student, 'student');
    }

    /** @test */
    public function student_cannot_login_with_invalid_credentials()
    {
        Student::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('student.login'), [
            'email' => 'student@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('student');
    }

    /** @test */
    public function student_cannot_login_with_employee_credentials()
    {
        $employee = User::factory()->create([
            'email' => 'employee@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('student.login'), [
            'email' => 'employee@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('student');
    }

    /** @test */
    public function inactive_student_cannot_login()
    {
        $student = Student::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
            'status' => 'graduated',
        ]);

        $response = $this->post(route('student.login'), [
            'email' => 'student@test.com',
            'password' => 'password',
        ]);

        // Depending on your implementation, adjust assertion
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function student_can_logout()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student, 'student')
            ->post(route('student.logout'));

        $response->assertRedirect(route('student.login'));
        $this->assertGuest('student');
    }

    /** @test */
    public function guest_cannot_access_student_dashboard()
    {
        $response = $this->get(route('student.dashboard'));

        $response->assertRedirect(route('student.login'));
    }

    /** @test */
    public function logged_in_student_cannot_access_login_page()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student, 'student')
            ->get(route('student.login'));

        $response->assertRedirect(route('student.dashboard'));
    }

    /** @test */
    public function validation_errors_are_displayed_on_login_form()
    {
        $response = $this->post(route('student.login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }
}