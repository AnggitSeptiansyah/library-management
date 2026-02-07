<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\AuthController as EmployeeAuthController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\BookController;
use App\Http\Controllers\Employee\CategoryController;
use App\Http\Controllers\Employee\BorrowingController;
use App\Http\Controllers\Employee\StudentController as EmployeeStudentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ProfileController;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Employee Routes
Route::prefix('employee')->name('employee.')->group(function () {
    // Auth routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [EmployeeAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [EmployeeAuthController::class, 'login']);
    });

    Route::middleware(['auth', 'employee'])->group(function () {
        Route::post('logout', [EmployeeAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        
        // Books Management
        Route::resource('books', BookController::class);
        
        // Categories Management
        Route::resource('categories', CategoryController::class)->except(['show']);
        
        // Students Management
        Route::resource('students', EmployeeStudentController::class);
        Route::patch('students/{student}/status', [EmployeeStudentController::class, 'updateStatus'])->name('students.update-status');
        
        // Borrowings Management
        Route::resource('borrowings', BorrowingController::class)->except(['edit', 'update', 'destroy']);
        Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'returnBooks'])->name('borrowings.return');
        
        // Employee Management (Super Admin Only)
        Route::middleware('superadmin')->group(function () {
            Route::resource('employees', EmployeeController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        });
        
        // Profile
        Route::get('profile', [EmployeeController::class, 'profile'])->name('profile');
        Route::patch('profile', [EmployeeController::class, 'updateProfile'])->name('profile.update');
    });
});

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    // Auth routes
    Route::middleware('guest:student')->group(function () {
        Route::get('login', [StudentAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [StudentAuthController::class, 'login']);
    });

    Route::middleware(['auth:student', 'student'])->group(function () {
        Route::post('logout', [StudentAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        
        // Catalog
        Route::get('catalog', [StudentDashboardController::class, 'catalog'])->name('catalog');
        
        // Borrowing History
        Route::get('borrowings', [StudentDashboardController::class, 'borrowingHistory'])->name('borrowings');
        
        // Profile
        Route::get('profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});