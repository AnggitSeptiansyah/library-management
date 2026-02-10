@extends('layouts.employee')

@section('title', 'Borrowing Details')

@section('content')
<div class="max-w-6xl mx-auto">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Borrowing Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">{{ $borrowing->borrowing_code }}</h2>
                    <span class="inline-block px-3 py-1 mt-2 text-sm rounded-full {{ 
                        $borrowing->status === 'returned' ? 'bg-green-100 text-green-800' : 
                        ($borrowing->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')
                    }}">
                        {{ ucfirst($borrowing->status) }}
                    </span>
                </div>

                <div class="space-y-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Student</p>
                        <p class="font-semibold text-gray-800">{{ $borrowing->student->fullname }}</p>
                        <p class="text-sm text-gray-500">{{ $borrowing->student->nis }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Borrow Date</p>
                        <p class="font-semibold text-gray-800">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Due Date</p>
                        <p class="font-semibold text-gray-800">{{ $borrowing->due_date->format('d M Y') }}</p>
                        @if($borrowing->isOverdue() && $borrowing->status !== 'returned')
                        <p class="text-sm text-red-600 mt-1">Overdue by {{ now()->diffInDays($borrowing->due_date) }} day(s)</p>
                        @endif
                    </div>
                    @if($borrowing->return_date)
                    <div>
                        <p class="text-sm text-gray-600">Return Date</p>
                        <p class="font-semibold text-gray-800">{{ $borrowing->return_date->format('d M Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Processed By</p>
                        <p class="font-semibold text-gray-800">{{ $borrowing->processedBy->name }}</p>
                    </div>
                    @if($borrowing->total_fine > 0)
                    <div class="p-4 bg-red-50 rounded-lg">
                        <p class="text-sm text-red-600 mb-1">Total Fine</p>
                        <p class="text-2xl font-bold text-red-600">Rp {{ number_format($borrowing->total_fine, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($borrowing->notes)
                    <div>
                        <p class="text-sm text-gray-600">Notes</p>
                        <p class="text-sm text-gray-800">{{ $borrowing->notes }}</p>
                    </div>
                    @endif
                </div>

                @if($borrowing->status !== 'returned')
                <div class="border-t pt-6">
                    <button onclick="document.getElementById('returnModal').classList.remove('hidden')" 
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">
                        Return Books
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Borrowed Books -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800">Borrowed Books ({{ $borrowing->items->count() }})</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($borrowing->items as $item)
                        <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition">
                            @if($item->book->cover_image)
                            <img src="{{ asset('storage/' . $item->book->cover_image) }}" alt="{{ $item->book->title }}" class="w-16 h-20 object-cover rounded mr-4">
                            @else
                            <div class="w-16 h-20 bg-gray-200 rounded mr-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $item->book->title }}</h4>
                                <p class="text-sm text-gray-600">by {{ $item->book->author }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $item->book->publisher }}</p>
                                <div class="mt-2">
                                    <span class="inline-block px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                        {{ $item->book->category->name }}
                                    </span>
                                    <span class="inline-block px-2 py-1 text-xs rounded ml-2 {{ $item->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow mt-6 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            @if(!$borrowing->return_date)
                            <div class="w-0.5 h-full bg-gray-300"></div>
                            @endif
                        </div>
                        <div class="pb-8">
                            <p class="font-semibold text-gray-800">Books Borrowed</p>
                            <p class="text-sm text-gray-600">{{ $borrowing->borrow_date->format('d M Y, H:i') }}</p>
                            <p class="text-sm text-gray-500">by {{ $borrowing->processedBy->fullname }}</p>
                        </div>
                    </div>

                    @if($borrowing->return_date)
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Books Returned</p>
                            <p class="text-sm text-gray-600">{{ $borrowing->return_date->format('d M Y, H:i') }}</p>
                            @if($borrowing->total_fine > 0)
                            <p class="text-sm text-red-600">Fine: Rp {{ number_format($borrowing->total_fine, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $borrowing->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Due for Return</p>
                            <p class="text-sm text-gray-600">{{ $borrowing->due_date->format('d M Y') }}</p>
                            @if($borrowing->isOverdue())
                            <p class="text-sm text-red-600">Overdue by {{ now()->diffInDays($borrowing->due_date) }} day(s)</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Modal -->
@if($borrowing->status !== 'returned')
<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Return Books</h3>
        
        <form action="{{ route('employee.borrowings.return', $borrowing) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Return Date <span class="text-red-500">*</span></label>
                <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            </div>

            @if($borrowing->total_fine > 0)
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg mb-4">
                <p class="text-sm text-red-800 font-medium">Fine Amount</p>
                <p class="text-2xl font-bold text-red-600">Rp {{ number_format($borrowing->total_fine, 0, ',', '.') }}</p>
            </div>
            @endif

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Confirm Return
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection