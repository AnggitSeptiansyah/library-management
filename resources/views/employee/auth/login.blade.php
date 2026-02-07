<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Library Management</h1>
            <p class="text-gray-600 mt-2">Employee Login</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('employee.login') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email Address
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-200">
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('student.login') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Login as Student
            </a>
        </div>
    </div>
</body>
</html>