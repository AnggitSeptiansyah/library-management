@extends('layouts.employee')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-4xl mx-auto">
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

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Student</h1>

        <form action="{{ route('employee.students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIS -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIS <span class="text-red-500">*</span></label>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('nis') border-red-500 @enderror">
                    @error('nis')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NISN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
                    <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('nisn') border-red-500 @enderror">
                    @error('nisn')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="fullname" value="{{ old('fullname', $student->fullname) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('fullname') border-red-500 @enderror">
                    @error('fullname')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Class -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Class</label>
                    <input type="text" name="current_class" value="{{ old('current_class', $student->current_class) }}" placeholder="e.g., 10 IPA A"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('current_class') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: Grade Major Class (e.g., 10 IPA A)</p>
                    @error('current_class')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Join Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Join Date <span class="text-red-500">*</span></label>
                    <input type="date" name="join_date" value="{{ old('join_date', $student->join_date->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('join_date') border-red-500 @enderror">
                    @error('join_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="drop out" {{ old('status', $student->status) == 'drop out' ? 'selected' : '' }}>Drop Out</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password (Optional on Edit) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('password') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 @error('address') border-red-500 @enderror">{{ old('address', $student->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('employee.students.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection