<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('employee.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if(Auth::attempt($credentials, $request->filled('remember'))){
            $request->session()->regenerate();
            return redirect()->intended(route('employee.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('employee.login');

    }
}
