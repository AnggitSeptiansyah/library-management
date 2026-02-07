@extends('layouts.student')

@section('title', 'Book Catalog')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Book Catalog</h1>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search books by title, author, or ISBN..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Search
                </button>
                @if(request('search') || request('category'))
                <a href="{{ route('student.catalog') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($books as $book)
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
            @else
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            <div class="p-4">
                <h3 class="font-bold text-gray-800 mb-1 line-clamp-2">{{ $book->title }}</h3>
                <p class="text-sm text-gray-600 mb-2">by {{ $book->author }}</p>
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 mb-2">
                    {{ $book->category->name }}
                </span>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-sm {{ $book->available_copies > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ $book->available_copies > 0 ? 'Available' : 'Not Available' }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $book->available_copies }}/{{ $book->total_copies }} copies</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500">No books found</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
    <div class="mt-6">
        {{ $books->links() }}
    </div>
    @endif
</div>
@endsection