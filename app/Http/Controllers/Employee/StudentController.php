<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreStudentRequest;
use App\Http\Requests\Employee\UpdateStudentRequest;
use App\Http\Requests\Employee\UpdateStudentStatusRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::when(request('search'), function($query) {
            $search = request('search');
            $query->where('name', 'like', "%{search}%")
                ->orWhere('nis', 'like', "%{search}%")
                ->orWhere('nisn', 'like', "%{search}%");
        })
        ->when(request('status'), function($query) {
            $query->where('status', request('status'));
        })
        ->latest()
        ->paginate(20);


        return view('employee.students.index', compact('students'));
    }

    public function create()
    {
        return view('employee.students.create');
    }

    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        Student::create($validated);

        return redirect()->route('employee.students.index')
                ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load('borrowings.items.book');
        return view('employee.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('employee.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $validated = $request->validated();
        if($request->filled('password')){
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $student->update($validated);

        return redirect()->route('employee.students.index')
                ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->borrowings()->exists()) {
            return back()->with('error', 'Cannot delete student with borrowing history.');
        }

        $student->delete();
        return redirect()->route('employee.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function updateStatus(UpdateStudentStatusRequest $request, Student $student)
    {
        $student->update($request->validated());

        return back()->with('success', 'Student status updated successfully.');
    }

}
