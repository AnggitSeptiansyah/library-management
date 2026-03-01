<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function superadmin_can_view_employees_index()
    {
        User::factory()->count(3)->create(['role' => 'admin']);

        $response = $this->actingAs($this->superadmin)
            ->get(route('employee.employees.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.employees.index');
        $response->assertViewHas('employees');
    }

    /** @test */
    public function admin_cannot_view_employees_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('employee.employees.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_search_employees()
    {
        User::factory()->create([
            'name' => 'John Employee',
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'Jane Employee',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('employee.employees.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee('John Employee');
        $response->assertDontSee('Jane Employee');
    }

    /** @test */
    public function superadmin_can_filter_employees_by_role()
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($this->superadmin)
            ->get(route('employee.employees.index', ['role' => 'admin']));

        $response->assertStatus(200);
    }

    /** @test */
    public function superadmin_can_view_create_employee_form()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('employee.employees.create'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.employees.create');
    }

    /** @test */
    public function superadmin_can_create_employee()
    {
        $employeeData = [
            'name' => 'newemployee',
            'email' => 'newemployee@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
            'phone' => '08123456789',
            'join_date' => '2024-01-01',
        ];

        $response = $this->actingAs($this->superadmin)
            ->post(route('employee.employees.store'), $employeeData);

        $response->assertRedirect(route('employee.employees.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'name' => 'newemployee',
            'email' => 'newemployee@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Check password is hashed
        $employee = User::where('email', 'newemployee@test.com')->first();
        $this->assertTrue(Hash::check('password', $employee->password));
    }

    /** @test */
    public function superadmin_can_view_edit_employee_form()
    {
        $employee = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->superadmin)
            ->get(route('employee.employees.edit', $employee));

        $response->assertStatus(200);
        $response->assertViewIs('employee.employees.edit');
        $response->assertViewHas('employee', $employee);
    }

    /** @test */
    public function superadmin_can_update_employee()
    {
        $employee = User::factory()->create([
            'name' => 'Old Name',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('employee.employees.update', $employee), [
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => 'superadmin',
                'join_date' => $employee->join_date ? $employee->join_date->format('Y-m-d') : now()->format('Y-m-d'),
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('employee.employees.index'));
        
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'New Name',
            'role' => 'superadmin',
        ]);
    }

    /** @test */
    public function employee_can_view_own_profile()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('employee.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.profile');
        $response->assertViewHas('employee', $this->admin);
    }

    /** @test */
    public function employee_can_update_own_profile()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('employee.profile.update'), [
                'name' => 'updatedname',
                'email' => 'updated@test.com',
                'phone' => '08199999999',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->admin->refresh();
        $this->assertEquals('updatedname', $this->admin->name);
    }

    /** @test */
    public function employee_can_change_password()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('employee.profile.password'), [
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->admin->refresh();
        $this->assertTrue(Hash::check('newpassword', $this->admin->password));
    }

    /** @test */
    public function password_update_requires_confirmation()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('employee.profile.password'), [
                'password' => 'newpassword',
                'password_confirmation' => 'different',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function guest_cannot_access_employee_management()
    {
        $response = $this->get(route('employee.employees.index'));
        $response->assertRedirect(route('employee.login'));
    }
}