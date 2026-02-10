<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->guard('student')->user();

        $stats = [
            'active_borrowings' => $student->activeBorrowings()->count(),
            'total_borrowings' => $student->borrowings()->count(),
            'overdue_borrowings' => $student->borrowings()->where('status', 'overdue')->count(),
            'total_fines' => $student->borrowings()->where('status', 'overdue')->sum('total_fine'),
        ];

        $activeBorrowings = $student->borrowings()
            ->with(['items.book'])
            ->whereIn('status', ['borrowed', 'overdue'])
            ->latest()
            ->get();
        
        return view('student.dashboard', compact('stats', 'activeBorrowings'));
    }

    public function catalog()
    {
        $books = Book::with('category')
            ->where('available_copies', '>', 0)
            ->when(request('search'), function($query) {
                $search = request('search');
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            })
            ->when(request('category'), function($query){
                $query->where('category_id', request('category'));
            })
            ->paginate(12);;
        
        return view('student.catalog', compact('books'));
    }

    public function borrowingHistory()
    {
        $student = auth()->guard('student')->user();

        $borrowings = $student->borrowings()
            ->with(['items.book'])
            ->latest()
            ->paginate(20);
        
        return view('student.borrowing-history', compact('borrowings'));
    }

    public function profile() 
    {
        $student = auth()->guard('student')->user();
        
        return view('student.profile', compact('student'));
    }
}
