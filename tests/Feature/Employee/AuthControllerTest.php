<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function employee_can_view_login_page()
    {
        $response = $this->get(route('employee.login'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.auth.login');
    }

    /** @test */
    public function employee_can_login_with_valid_credentials()
    {
        $employee = User::factory()->create([
            'email' => 'test@employee.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('employee.login'), [
            'email' => 'test@employee.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($employee);
    }

    /** @test */
    public function employee_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@employee.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('employee.login'), [
            'email' => 'test@employee.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function employee_cannot_login_with_student_credentials()
    {
        $student = \App\Models\Student::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('employee.login'), [
            'email' => 'student@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function employee_can_logout()
    {
        $employee = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($employee)
            ->post(route('employee.logout'));

        $response->assertRedirect(route('employee.login'));
        $this->assertGuest();
    }

    /** @test */
    public function guest_cannot_access_employee_dashboard()
    {
        $response = $this->get(route('employee.dashboard'));

        $response->assertRedirect(route('employee.login'));
    }

    /** @test */
    public function logged_in_employee_cannot_access_login_page()
    {
        $employee = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($employee)
            ->get(route('employee.login'));

        $response->assertRedirect(route('employee.dashboard'));
    }

    /** @test */
    public function validation_errors_are_displayed_on_login_form()
    {
        $response = $this->post(route('employee.login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }
}