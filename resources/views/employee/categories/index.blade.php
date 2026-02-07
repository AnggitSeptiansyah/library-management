@extends('layouts.employee')

@section('title', 'Categories')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Categories Management</h1>
        <a href="{{ route('employee.categories.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Category
            </span>
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search categories..."
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('employee.categories.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Books Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $category->description ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ $category->books_count }} books
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('employee.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('employee.categories.destroy', $category) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection