<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class VoterProfileController extends Controller
{
    /**
     * Show the voter profile page
     */
    public function index()
    {
        $voter = Auth::guard('voter')->user();
        return view('dashboards.voter-dashboard.profile', compact('voter'));
    }

    /**
     * Update name and email
     */
    public function updateInfo(Request $request)
    {
        $voter = Auth::guard('voter')->user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'nullable|email|max:255',
        ]);

        $voter->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password — isang beses lang pwede
     */
    public function updatePassword(Request $request)
    {
        $voter = Auth::guard('voter')->user();

        // Kung nagpalit na siya dati, i-block
        if ($voter->password_changed) {
            return back()->withErrors([
                'password' => 'You have already changed your password once. Please contact your administrator if you need a reset.'
            ]);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $voter->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

       $voter->update([
            'password'         => Hash::make($request->password),
            'password_changed' => true,
        ]);

        // I-log ang password change
        \App\Models\VoterActivityLog::create([
            'voter_id'    => $voter->id,
            'action'      => 'password_changed',
            'description' => 'Changed account password.',
        ]);

        return back()->with('success', 'Password updated successfully.');

        // I-log ang pagboto
\App\Models\VoterActivityLog::firstOrCreate(
    [
        'voter_id' => auth('voter')->id(),
        'action'   => 'voted',
        'description' => 'Submitted ballot for election: ' . $election->title,
    ]
);
    }
}