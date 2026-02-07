@extends('layouts.employee')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ auth()->user()->fullname }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Total Books</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_books'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Active Students</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Active Borrowings</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['active_borrowings'] }}</p>
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
    </div>

    <!-- Recent Borrowings -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Recent Borrowings</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Books</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentBorrowings as $borrowing)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('employee.borrowings.show', $borrowing) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $borrowing->borrowing_code }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $borrowing->student->fullname }}</td>
                        <td class="px-6 py-4">{{ $borrowing->items->count() }} book(s)</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $borrowing->borrow_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $borrowing->due_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($borrowing->status === 'borrowed') bg-blue-100 text-blue-800
                                @elseif($borrowing->status === 'returned') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No borrowings found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection