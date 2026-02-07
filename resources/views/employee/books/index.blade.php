@extends('layouts.employee')

@section('title', 'Books')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Books Management</h1>
        <a href="{{ route('employee.books.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Book
            </span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by title, author, or ISBN..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition mr-2">
                    Filter
                </button>
                <a href="{{ route('employee.books.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($books as $book)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-12 h-16 object-cover rounded mr-4">
                                @else
                                    <div class="w-12 h-16 bg-gray-200 rounded mr-4 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $book->title }}</div>
                                    <div class="text-sm text-gray-500">by {{ $book->author }}</div>
                                    <div class="text-sm text-gray-500">{{ $book->publisher }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $book->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                Available: <span class="font-semibold {{ $book->available_copies > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $book->available_copies }}</span>
                            </div>
                            <div class="text-sm text-gray-500">Total: {{ $book->total_copies }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $book->isbn }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('employee.books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('employee.books.destroy', $book) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this book?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No books found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($books->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $books->links() }}
        </div>
        @endif
    </div>
</div>
@endsection