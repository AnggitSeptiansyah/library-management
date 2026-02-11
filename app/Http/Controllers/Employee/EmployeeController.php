<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::when(request('search'), function($query) {
                $search = request('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when(request('role'), function($query) {
                $query->where('role', request('role'));
            })
            ->latest()
            ->paginate(20);

        return view('employee.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employee.employees.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('employee.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(User $employee)
    {
        return view('employee.employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $validated = $request->validated();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->route('employee.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function profile()
    {
        $employee = auth()->user();
        return view('employee.profile', compact('employee'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $employee = auth()->user();
        $validated = $request->validated();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}