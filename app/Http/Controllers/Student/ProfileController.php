<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdatePasswordRequest;
use App\Http\Requests\Student\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        $student = auth()->guard('student')->user();
        $student->update($request->validated());

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $student = auth()->guard('student')->user();
        $validated = $request->validated();

        // Check apakah password sekarang benar
        if(!Hash::check($validated['password'], $student->password)) {
            return back()->withErrors(['current_password' => 'Current password is not correct']);
        }

        $student->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
