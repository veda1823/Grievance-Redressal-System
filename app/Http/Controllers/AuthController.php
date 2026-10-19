<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the student registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register a new student.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful!');
    }

    /**
     * Show the login form for students and staff.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the dedicated staff login form.
     */
    public function showStaffLogin()
    {
        return view('auth.staff-login');
    }

    /**
     * Authenticate and redirect users according to their role.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (Auth::user()->isStaff()) {
            return redirect()->route('staff.dashboard')
                ->with('success', 'Welcome to the staff dashboard.');
        }

        if (Auth::user()->isStudent()) {
            return redirect()->route('dashboard')
                ->with('success', 'You are logged in.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Your account does not have an authorized role.',
        ])->onlyInput('email');
    }

    /**
     * Authenticate a staff member and redirect to the staff workspace.
     */
    public function staffLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'role' => 'staff'], $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided staff credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('staff.dashboard')
            ->with('success', 'Welcome to the staff dashboard.');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
