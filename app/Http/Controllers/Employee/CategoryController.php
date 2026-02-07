<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreCategoryRequest;
use App\Http\Requests\Employee\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('books')
            ->when(request('search'), function($query){
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->latest()
            ->paginate(20);

        return view('employee.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('employee.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('employee.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('employee.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Cannot delete category with books.');
        }

        $category->delete();

        return redirect()->route('employee.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
