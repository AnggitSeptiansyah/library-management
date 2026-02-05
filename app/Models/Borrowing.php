<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_code',
        'student_id',
        'processed_by',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'total_fine',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function isOverdue(): bool
    {
        if($this->status === 'returned'){
            return false;
        }

        return Carbon::now()->startOfDay()->greaterThan($this->due_date);
    }

    public function calculateFine()
    {
        if(!$this->isOverdue()){
            return 0;
        }

        $today = Carbon::now()->startOfDay();
        $dueDate = Carbon::parse($this->due_date);
        $overdueDays = $today->diffInDays($dueDate);

        $dailyFine = 500;
        $maxFine = 50000;

        $totalFine = min($overdueDays * $dailyFine, $maxFine);

        return $totalFine;
    }

    public function updateFine()
    {
        $this->total_fine = $this->calculateFine();

        if($this->isOverdue() && $this->status !== 'returned'){
            $this->status = 'overdue';
        }

        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($borrowing) {
            if(empty($borrowing->borrowing_code)) {
                $borrowing->borrowing_code = 'BRW-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', date('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
