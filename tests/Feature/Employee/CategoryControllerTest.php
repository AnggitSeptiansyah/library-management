<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function employee_can_view_categories_index()
    {
        Category::factory()->count(5)->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.categories.index');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function employee_can_search_categories()
    {
        Category::factory()->create(['name' => 'Fiction']);
        Category::factory()->create(['name' => 'Science']);

        $response = $this->actingAs($this->employee)
            ->get(route('employee.categories.index', ['search' => 'Fiction']));

        $response->assertStatus(200);
        $response->assertSee('Fiction');
        $response->assertDontSee('Science');
    }

    /** @test */
    public function employee_can_view_create_category_form()
    {
        $response = $this->actingAs($this->employee)
            ->get(route('employee.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.categories.create');
    }

    /** @test */
    public function employee_can_create_category()
    {
        $response = $this->actingAs($this->employee)
            ->post(route('employee.categories.store'), [
                'name' => 'Test Category',
                'description' => 'Test description',
            ]);

        $response->assertRedirect(route('employee.categories.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
        ]);
    }

    /** @test */
    public function category_name_must_be_unique()
    {
        Category::factory()->create(['name' => 'Existing Category']);

        $response = $this->actingAs($this->employee)
            ->post(route('employee.categories.store'), [
                'name' => 'Existing Category',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function employee_can_view_edit_category_form()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->employee)
            ->get(route('employee.categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewIs('employee.categories.edit');
        $response->assertViewHas('category', $category);
    }

    /** @test */
    public function employee_can_update_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->employee)
            ->put(route('employee.categories.update', $category), [
                'name' => 'New Name',
                'description' => 'New description',
            ]);

        $response->assertRedirect(route('employee.categories.index'));
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    /** @test */
    public function employee_can_delete_category_without_books()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.categories.destroy', $category));

        $response->assertRedirect(route('employee.categories.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function employee_cannot_delete_category_with_books()
    {
        $category = Category::factory()->create();
        Book::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function guest_cannot_access_categories_management()
    {
        $response = $this->get(route('employee.categories.index'));
        $response->assertRedirect(route('employee.login'));
    }
}