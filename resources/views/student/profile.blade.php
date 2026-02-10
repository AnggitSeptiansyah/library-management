@extends('layouts.student')

@section('title', 'My Profile')

@section('content')
<div class="px-4 py-6 sm:px-0 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Profile</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Personal Information</h2>
                <form action="{{ route('student.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="fullname" value="{{ old('fullname', $student->fullname) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $student->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">{{ old('address', $student->address) }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Change Password</h2>
                <form action="{{ route('student.profile.password') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Info Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">Student Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">NIS:</span>
                        <span class="font-semibold text-gray-800 ml-2">{{ $student->nis }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">NISN:</span>
                        <span class="font-semibold text-gray-800 ml-2">{{ $student->nisn }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Current Class:</span>
                        <span class="font-semibold text-gray-800 ml-2">{{ $student->current_class ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} ml-2">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection