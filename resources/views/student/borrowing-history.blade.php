@extends('layouts.student')

@section('title', 'Borrowing History')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Borrowing History</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @forelse($borrowings as $borrowing)
        <div class="p-6 border-b last:border-0">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-gray-800">{{ $borrowing->borrowing_code }}</h3>
                    <p class="text-sm text-gray-600">Borrowed: {{ $borrowing->borrow_date->format('d M Y') }}</p>
                    <p class="text-sm text-gray-600">Due: {{ $borrowing->due_date->format('d M Y') }}</p>
                    @if($borrowing->return_date)
                    <p class="text-sm text-gray-600">Returned: {{ $borrowing->return_date->format('d M Y') }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 text-sm rounded-full {{ 
                        $borrowing->status === 'returned' ? 'bg-green-100 text-green-800' : 
                        ($borrowing->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')
                    }}">
                        {{ ucfirst($borrowing->status) }}
                    </span>
                    @if($borrowing->total_fine > 0)
                    <p class="text-sm font-semibold text-red-600 mt-2">
                        Fine: Rp {{ number_format($borrowing->total_fine, 0, ',', '.') }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="space-y-2">
                <p class="text-sm font-semibold text-gray-700">Books:</p>
                @foreach($borrowing->items as $item)
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    {{ $item->book->title }}
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-gray-500">
            No borrowing history yet
        </div>
        @endforelse
    </div>

    @if($borrowings->hasPages())
    <div class="mt-6">
        {{ $borrowings->links() }}
    </div>
    @endif
</div>
@endsection