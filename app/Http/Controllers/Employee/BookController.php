<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreBookRequest;
use App\Http\Requests\Employee\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')
            ->when(request('search'), function($query){
                $search = request('search');
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            })
            ->when(request('category'), function($query) {
                $query->where('category_id', request('category'));
            })
            ->latest()
            ->paginate(20);
        
        $categories = Category::all();

        return view('employee.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('employee.books.create', compact('categories'));
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        $validated['available_copies'] = $validated['total_copies'];

        if($request->hasFile('cover_image')){
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }

        Book::create($validated);

        return redirect()->route('employee.books.index')
            ->with('success', 'Book added successfully');
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('employee.books.edit', compact('book', 'categories'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();

        // Adjust available copies if total copies changed
        $difference = $validated['total_copies'] - $book->total_copies;
        $validated['available_copies'] = $book->available_copies + $difference;

        if($request->hasFile('cover_image')) {
            if($book->cover_image){
                Storage::disk('public')->delete($book->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }

        $book->update($validated);

        return redirect()->route('employee.books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        if ($book->borrowingItems()->exists()) {
            return back()->with('error', 'Cannot delete book with borrowing history.');
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('employee.books.index')
            ->with('success', 'Book deleted successfully.');
    }

}
