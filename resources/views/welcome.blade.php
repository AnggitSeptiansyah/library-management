<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-white mb-8">Library Management System</h1>
        <p class="text-xl text-white mb-12">Welcome to our digital library portal</p>
        
        <div class="flex gap-6 justify-center">
            <a href="{{ route('employee.login') }}" 
               class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-lg">
                Employee Login
            </a>
            <a href="{{ route('student.login') }}" 
               class="bg-indigo-600 text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-indigo-700 transition shadow-lg">
                Student Login
            </a>
        </div>
    </div>
</body>
</html>