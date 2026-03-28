<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class VoterResetPasswordController extends Controller
{
    /**
     * Show reset password form.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('dashboards.voter-dashboard.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Process the password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required'              => 'Kailangan ng bagong password.',
            'password.min'                   => 'Ang password ay dapat hindi bababa sa 8 characters.',
            'password.confirmed'             => 'Hindi magkatugma ang mga password.',
            'password_confirmation.required' => 'Kailangan i-confirm ang password.',
        ]);

        // Hanapin ang token
        $resetRecord = DB::table('voter_password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Hindi valid ang reset link. Subukan muli.']);
        }

        // I-verify ang token
        if (!hash_equals($resetRecord->token, hash('sha256', $request->token))) {
            return back()->withErrors(['email' => 'Hindi valid ang reset link. Subukan muli.']);
        }

        // I-check kung hindi pa expired (60 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('voter_password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Nag-expire na ang reset link. Humingi ng bagong link.']);
        }

        // Hanapin ang voter
        $voter = Voter::where('email', $request->email)->first();

        if (!$voter) {
            return back()->withErrors(['email' => 'Hindi mahanap ang account.']);
        }

        // I-update ang password
        $voter->update([
            'password' => Hash::make($request->password),
        ]);

        // I-delete ang used token
        DB::table('voter_password_resets')->where('email', $request->email)->delete();

        return redirect()->route('voter.login')
            ->with('status', 'Matagumpay na na-reset ang iyong password! Maaari ka nang mag-login.');
    }
}