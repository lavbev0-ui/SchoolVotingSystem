<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Voter;

class VoterAuthController extends Controller
{
    /**
     * 1. Show Student Login Form
     */
    public function create()
    {
        return view('dashboards.voter-dashboard.auth.login');
    }

    /**
     * 2. Process Student Login
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        $key = 'voter-login:' . $request->ip();

        // ✅ Check kung naka-lockout na
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'student_id' => "Too many login attempts. Please try again in {$minutes} minute(s).",
            ])->onlyInput('student_id');
        }

        $credentials = [
            'student_id' => $request->student_id,
            'password'   => $request->password,
        ];

        if (Auth::guard('voter')->attempt($credentials, $request->boolean('remember'))) {

            $user = Auth::guard('voter')->user();

            // Block deactivated accounts
            if (isset($user->is_active) && $user->is_active == 0) {
                Auth::guard('voter')->logout();
                RateLimiter::hit($key, 300);
                return back()->withErrors(['student_id' => 'Your account has been deactivated.']);
            }

            // ✅ Clear rate limiter on successful login
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->route('voter.dashboard');
        }

        // ✅ Increment attempt counter — 300 seconds = 5 minutes lockout
        RateLimiter::hit($key, 300);

        $attempts  = RateLimiter::attempts($key);
        $remaining = max(0, 5 - $attempts);

        if ($remaining > 0) {
            return back()->withErrors([
                'student_id' => "Invalid credentials. {$remaining} attempt(s) remaining before lockout.",
            ])->onlyInput('student_id');
        }

        return back()->withErrors([
            'student_id' => 'Too many login attempts. Please try again in 5 minutes.',
        ])->onlyInput('student_id');
    }

    /**
     * 3. Student Logout
     */
    public function destroy(Request $request)
    {
        Auth::guard('voter')->logout();

        session()->forget([
            'last_activity_time',
            'voter_2fa_verified',
        ]);

        session()->regenerateToken();

        return redirect()->route('voter.login');
    }
}