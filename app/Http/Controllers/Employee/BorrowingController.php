<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\ReturnBorrowingRequest;
use App\Http\Requests\Employee\StoreBorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowings = Borrowing::with(['student', 'items.book', 'processedBy'])
            ->when(request('status'), function($query) {
                $query->where('status', request('status'));
            })
            ->when(request('search'), function($query) {
                $search = request('search');
                $query->whereHas('student', function($q) use ($search){
                    $q->where('name', 'like', "%{search}%")
                    ->orWhere('nis', 'like', "%{search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('employee.borrowings.index', compact('borrowings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::where('status', 'active')->get();
        $books = Book::where('available_copies', '>', 0)->get();
        
        return view('employee.borrowings.create', compact('students', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Lihat apakah semua buku yang tersedia
            $books = Book::whereIn('id', $validated['book_ids'])->get();
            foreach($books as $book){
                if(!$book->isAvailable()){
                    throw new \Exception("Book '{$book->title}' is not available.");
                }
            }

            // Buat record peminjaman buku
            $borrowing = Borrowing::create([
                'student_id' => $validated['student_id'],
                'processed_by' => auth()->id(),
                'borrow_date' => $validated['borrow_date'],
                'due_date' => Carbon::parse($validated['borrow_date'])->addDays(7),
                'status' => 'borrowed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach($validated['book_ids'] as $bookId) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $bookId,
                    'status' => 'borrowed',
                ]);

                $book = Book::find($bookId);
                $book->decrementStock();
            }

            DB::commit();

            return redirect()->route('employee.borrowings.index')
                ->with('success', 'Borrowing created successfully. Code: ' . $borrowing->borrowing_code);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['student', 'items.book', 'processedBy']);
        return view('employee.borrowings.show', compact('borrowing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    
    public function returnBooks(ReturnBorrowingRequest $request, Borrowing $borrowing)
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        try{
            $borrowing->return_date = $validated['return_date'];
            $borrowing->status = 'returned';

            // Menghitung denda
            $borrowing->updateFine();

            // Mengubah semua items menjadi kembali
            foreach($borrowing->items as $item){
                $item->status = 'returned';
                $item->save();

                // Increment book stock
                $item->book->incrementStock();
            }

            $borrowing->save();

            DB::commit();

            return redirect()->route('employee.borrowings.show', $borrowing)
                ->with('success', 'Books returned successfully. Fine: Rp ' . number_format($borrowing->total_fine, 0, ',', '.'));
        } catch(\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
