@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, {{ auth('student')->user()->fullname }}!</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Currently Borrowed</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['active_borrowings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Total Borrowed</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_borrowings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Overdue</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['overdue_borrowings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Total Fines</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($stats['total_fines'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Borrowings -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Currently Borrowed Books</h2>
        </div>
        <div class="p-6">
            @forelse($activeBorrowings as $borrowing)
            <div class="mb-6 pb-6 border-b last:border-0">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-sm font-semibold text-gray-600">{{ $borrowing->borrowing_code }}</span>
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $borrowing->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($borrowing->status) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Due: {{ $borrowing->due_date->format('d M Y') }}</p>
                        @if($borrowing->total_fine > 0)
                        <p class="text-sm font-semibold text-red-600">Fine: Rp {{ number_format($borrowing->total_fine, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($borrowing->items as $item)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-gray-700">{{ $item->book->title }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-8">No active borrowings</p>
            @endforelse
        </div>
    </div>
</div>
@endsection