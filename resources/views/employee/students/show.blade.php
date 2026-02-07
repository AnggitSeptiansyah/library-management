@extends('layouts.employee')

@section('title', 'Student Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('employee.students.index') }}" class="text-indigo-600 hover:text-indigo-800">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Students
            </span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-indigo-100 rounded-full mx-auto flex items-center justify-center mb-4">
                        <span class="text-3xl font-bold text-indigo-600">{{ substr($student->fullname, 0, 2) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $student->fullname }}</h2>
                    <p class="text-gray-600">{{ $student->email }}</p>
                    <span class="inline-block px-3 py-1 mt-2 text-sm rounded-full {{ 
                        $student->status === 'active' ? 'bg-green-100 text-green-800' : 
                        ($student->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')
                    }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">NIS</p>
                        <p class="font-semibold text-gray-800">{{ $student->nis }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">NISN</p>
                        <p class="font-semibold text-gray-800">{{ $student->nisn }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Current Class</p>
                        <p class="font-semibold text-gray-800">{{ $student->current_class ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-semibold text-gray-800">{{ $student->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Join Date</p>
                        <p class="font-semibold text-gray-800">{{ $student->join_date->format('d M Y') }}</p>
                    </div>
                    @if($student->address)
                    <div>
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="font-semibold text-gray-800">{{ $student->address }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex gap-2">
                    <a href="{{ route('employee.students.edit', $student) }}" class="flex-1 bg-indigo-600 text-white text-center py-2 rounded-lg hover:bg-indigo-700 transition">
                        Edit
                    </a>
                    <form action="{{ route('employee.students.destroy', $student) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Borrowing History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800">Borrowing History</h3>
                </div>
                <div class="p-6">
                    @forelse($student->borrowings as $borrowing)
                    <div class="mb-6 pb-6 border-b last:border-0">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $borrowing->borrowing_code }}</h4>
                                <p class="text-sm text-gray-600">Borrowed: {{ $borrowing->borrow_date->format('d M Y') }}</p>
                                <p class="text-sm text-gray-600">Due: {{ $borrowing->due_date->format('d M Y') }}</p>
                                @if($borrowing->return_date)
                                <p class="text-sm text-gray-600">Returned: {{ $borrowing->return_date->format('d M Y') }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-xs rounded-full {{ 
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
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-gray-700">{{ $item->book->title }}</span>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded {{ $item->status === 'returned' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        @if($borrowing->status !== 'returned')
                        <div class="mt-4">
                            <a href="{{ route('employee.borrowings.show', $borrowing) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                View Details →
                            </a>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No borrowing history
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection