<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_books' => Book::count(),
            'total_students' => Student::where('status', 'active')->count(),
            'active_borrowings' => Borrowing::whereIn('status', ['borrowed', 'overdue'])->count(),
            'total_categories' => Category::count(),
            'overdue_borrowings' => Borrowing::where('status', 'overdue')->count(),
        ];

        $recentBorrowings = Borrowing::with(['student', 'items.book'])
            ->latest()
            ->take(10)
            ->get();

        return view('employee.dashboard', compact('stats', 'recentBorrowings'));

    }
}
