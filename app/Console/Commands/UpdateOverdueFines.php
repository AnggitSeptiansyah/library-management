<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;

class UpdateOverdueFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrowings:update-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update fines for overdue borrowings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 
        $overdueBorrowings = Borrowing::whereIn('status', ['borrowed', 'overdue'])
            ->where('due_date', '<', now()->startOfDay())
            ->get();
        
        $count = 0;
        foreach($overdueBorrowings as $borrowing){
            $borrowing->updateFine();
            $count++;
        }

        $this->infO("Updated {$count} overdue borrowings");
        return 0;
    }

}
