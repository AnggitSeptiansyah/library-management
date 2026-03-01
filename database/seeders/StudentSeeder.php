<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating students...');

        // Create specific test students
        $testStudents = [
            [
                'nis' => '2024001',
                'nisn' => '0051234567',
                'fullname' => 'Ahmad Hidayat',
                'current_class' => '12 IPA A',
                'email' => 'ahmad.hidayat@student.com',
                'password' => Hash::make('password'),
                'phone' => '081234560001',
                'address' => 'Jl. Pendidikan No. 10, Jakarta',
                'join_date' => now()->subYears(2),
                'status' => 'active',
            ],
            [
                'nis' => '2024002',
                'nisn' => '0051234568',
                'fullname' => 'Siti Nurhaliza',
                'current_class' => '12 IPA B',
                'email' => 'siti.nurhaliza@student.com',
                'password' => Hash::make('password'),
                'phone' => '081234560002',
                'address' => 'Jl. Belajar No. 15, Jakarta',
                'join_date' => now()->subYears(2),
                'status' => 'active',
            ],
            [
                'nis' => '2024003',
                'nisn' => '0051234569',
                'fullname' => 'Budi Santoso',
                'current_class' => '11 IPS A',
                'email' => 'budi.santoso@student.com',
                'password' => Hash::make('password'),
                'phone' => '081234560003',
                'address' => 'Jl. Sekolah No. 20, Jakarta',
                'join_date' => now()->subYear(),
                'status' => 'active',
            ],
            [
                'nis' => '2024004',
                'nisn' => '0051234570',
                'fullname' => 'Dewi Lestari',
                'current_class' => '10 IPA A',
                'email' => 'dewi.lestari@student.com',
                'password' => Hash::make('password'),
                'phone' => '081234560004',
                'address' => 'Jl. Pelajar No. 25, Jakarta',
                'join_date' => now()->subMonths(6),
                'status' => 'active',
            ],
        ];

        foreach ($testStudents as $studentData) {
            Student::create($studentData);
        }

        // Create 10 graduated students
        Student::factory()->count(10)->graduated()->create();

        // Create 3 dropout students
        Student::factory()->count(3)->dropout()->create();

        $this->command->info('✅ Created ' . Student::count() . ' students');
    }

    /**
     * Create class history for a student based on their current class.
     */
    
}