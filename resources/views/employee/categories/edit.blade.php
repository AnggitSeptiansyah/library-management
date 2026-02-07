management/resources/views/employee/categories/edit.blade.php << 'EOF'
@extends('layouts.employee')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('employee.categories.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Category</h1>

        <form action="{{ route('employee.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <a href="{{ route('employee.categories.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update Category</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
