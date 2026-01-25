<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoterAuthController extends Controller
{
    // 1. Show Login Form
    public function create()
    {
        // FIX #1: Correct folder path (dashboards vs dashboard)
        return view('dashboards.voter-dashboard.auth.login');
    }

    // 2. Process Login
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'userID'   => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('voter')->attempt($credentials, $request->boolean('remember'))) {
            
            if (Auth::guard('voter')->user()->is_active == 0) {
                Auth::guard('voter')->logout();
                return back()->withErrors(['userID' => 'Your account has been deactivated.']);
            }

            $request->session()->regenerate();

            // FIX #2: Correct route name matches web.php
            return redirect()->route('voter.dashboard');
        }

        return back()->withErrors([
            'userID' => 'The provided credentials do not match our records.',
        ])->onlyInput('userID');
    }

    // 3. Logout
    public function destroy(Request $request)
    {
        Auth::guard('voter')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // FIX #3: Correct route name matches web.php
        return redirect()->route('voter.login');
    }
}