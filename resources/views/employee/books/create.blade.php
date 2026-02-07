@extends('layouts.employee')

@section('title', 'Add New Book')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('employee.books.index') }}" class="text-indigo-600 hover:text-indigo-800">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Books
            </span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Add New Book</h1>

        <form action="{{ route('employee.books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ISBN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ISBN <span class="text-red-500">*</span></label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('isbn') border-red-500 @enderror">
                    @error('isbn')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Author <span class="text-red-500">*</span></label>
                    <input type="text" name="author" value="{{ old('author') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('author') border-red-500 @enderror">
                    @error('author')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publisher -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Publisher <span class="text-red-500">*</span></label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('publisher') border-red-500 @enderror">
                    @error('publisher')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Edition -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Edition</label>
                    <input type="text" name="edition" value="{{ old('edition') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('edition') border-red-500 @enderror">
                    @error('edition')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publication Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Publication Year</label>
                    <input type="number" name="publication_year" value="{{ old('publication_year') }}" min="1900" max="{{ date('Y') + 1 }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('publication_year') border-red-500 @enderror">
                    @error('publication_year')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Pages -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Pages</label>
                    <input type="number" name="total_pages" value="{{ old('total_pages') }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('total_pages') border-red-500 @enderror">
                    @error('total_pages')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Copies -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Copies <span class="text-red-500">*</span></label>
                    <input type="number" name="total_copies" value="{{ old('total_copies', 1) }}" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('total_copies') border-red-500 @enderror">
                    @error('total_copies')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cover Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('cover_image') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Max size: 2MB</p>
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('employee.books.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Add Book
                </button>
            </div>
        </form>
    </div>
</div>
@endsection