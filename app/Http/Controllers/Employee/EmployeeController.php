<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::when(request('search'), function($query) {
            $search = request('search');
            $query->where('name', 'like', "%{search}%")
                ->orWhere('email', 'like', "%{search}%");
            })
            ->when(request('role'), function($query) {
                $query->where('role', request('role'));
            })
            ->latest()
            ->paginate(20);

        return view('employee.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee.employees.index', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('employee.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        return view('employee.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $validated = $request->validated();

        if($request->filled('password')){
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

        if($request->filled('password')){
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated());
        return back()->with('success', 'Profile updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
