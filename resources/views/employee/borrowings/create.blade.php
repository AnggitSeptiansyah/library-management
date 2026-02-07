@extends('layouts.employee')

@section('title', 'New Borrowing')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('employee.borrowings.index') }}" class="text-indigo-600 hover:text-indigo-800">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Borrowings
            </span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Borrowing</h1>

        <form action="{{ route('employee.borrowings.store') }}" method="POST" x-data="borrowingForm()">
            @csrf

            <div class="space-y-6">
                <!-- Student Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('student_id') border-red-500 @enderror">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->fullname }} ({{ $student->nis }}) - {{ $student->current_class ?? 'No Class' }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Borrow Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Borrow Date <span class="text-red-500">*</span></label>
                    <input type="date" name="borrow_date" value="{{ old('borrow_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('borrow_date') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Due date will be automatically set to 7 days from borrow date</p>
                    @error('borrow_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Books Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Books <span class="text-red-500">*</span></label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto">
                        <div class="mb-3">
                            <input type="text" x-model="searchQuery" placeholder="Search books..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($books as $book)
                            <label class="flex items-start p-3 hover:bg-gray-50 rounded-lg cursor-pointer"
                                   x-show="searchFilter('{{ strtolower($book->title . ' ' . $book->author . ' ' . $book->isbn) }}')">
                                <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" 
                                       {{ (is_array(old('book_ids')) && in_array($book->id, old('book_ids'))) ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900">{{ $book->title }}</div>
                                    <div class="text-sm text-gray-600">by {{ $book->author }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $book->category->name }}</span>
                                        <span class="ml-2">ISBN: {{ $book->isbn }}</span>
                                        <span class="ml-2 {{ $book->available_copies > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Available: {{ $book->available_copies }}/{{ $book->total_copies }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @error('book_ids')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @error('book_ids.*')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-600">
                        <span x-text="selectedCount"></span> book(s) selected
                    </p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional notes..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <!-- Warning Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium mb-1">Important Information:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Student can borrow multiple books but not the same title twice</li>
                                <li>Borrowing period: 7 days</li>
                                <li>Late fine: Rp 500/day (max Rp 50,000)</li>
                                <li>Fine is automatically calculated and updated daily</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('employee.borrowings.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Create Borrowing
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function borrowingForm() {
    return {
        searchQuery: '',
        
        get selectedCount() {
            return document.querySelectorAll('input[name="book_ids[]"]:checked').length;
        },
        
        searchFilter(bookText) {
            if (this.searchQuery === '') return true;
            return bookText.includes(this.searchQuery.toLowerCase());
        }
    }
}
</script>
@endsection