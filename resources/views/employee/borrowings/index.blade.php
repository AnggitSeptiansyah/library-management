@extends('layouts.employee')

@section('title', 'Borrowings')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Borrowings Management</h1>
        <a href="{{ route('employee.borrowings.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Borrowing
            </span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by student name or NIS..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition mr-2">
                    Filter
                </button>
                <a href="{{ route('employee.borrowings.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-xl font-bold">{{ $borrowings->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Borrowed</p>
                    <p class="text-xl font-bold">{{ $borrowings->where('status', 'borrowed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Overdue</p>
                    <p class="text-xl font-bold">{{ $borrowings->where('status', 'overdue')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Returned</p>
                    <p class="text-xl font-bold">{{ $borrowings->where('status', 'returned')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrowings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Books</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($borrowings as $borrowing)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('employee.borrowings.show', $borrowing) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                {{ $borrowing->borrowing_code }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $borrowing->student->fullname }}</div>
                            <div class="text-sm text-gray-500">{{ $borrowing->student->nis }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ $borrowing->items->count() }} book(s)</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="text-gray-900">Borrowed: {{ $borrowing->borrow_date->format('d M Y') }}</div>
                            <div class="text-gray-600">Due: {{ $borrowing->due_date->format('d M Y') }}</div>
                            @if($borrowing->return_date)
                            <div class="text-green-600">Returned: {{ $borrowing->return_date->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ 
                                $borrowing->status === 'returned' ? 'bg-green-100 text-green-800' : 
                                ($borrowing->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')
                            }}">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($borrowing->total_fine > 0)
                            <span class="text-red-600 font-semibold">Rp {{ number_format($borrowing->total_fine, 0, ',', '.') }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('employee.borrowings.show', $borrowing) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            @if($borrowing->status !== 'returned')
                            <a href="{{ route('employee.borrowings.show', $borrowing) }}#return" class="text-green-600 hover:text-green-900">Return</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No borrowings found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($borrowings->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $borrowings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection