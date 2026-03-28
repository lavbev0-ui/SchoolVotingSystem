<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VoterForgotPasswordController extends Controller
{
    /**
     * Show forgot password form.
     */
    public function showForm()
    {
        return view('dashboards.voter-dashboard.auth.forgot-password');
    }

    /**
     * Send reset link to voter's email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Kailangan ng email address.',
            'email.email'    => 'Hindi valid ang email address.',
        ]);

        $voter = Voter::where('email', $request->email)->first();

        // Para sa security — hindi natin sinasabi kung nandoon o wala ang email
        if (!$voter) {
            return back()->with('status', 'Kung may account kang naka-register sa email na iyan, makatanggap ka ng reset link.');
        }

        // I-delete ang lumang tokens para sa email na ito
        DB::table('voter_password_resets')->where('email', $request->email)->delete();

        // Gumawa ng bagong token
        $token = Str::random(64);

        DB::table('voter_password_resets')->insert([
            'email'      => $request->email,
            'token'      => hash('sha256', $token),
            'created_at' => Carbon::now(),
        ]);

        // I-send ang email
        $resetUrl = route('voter.password.reset.form', [
            'token' => $token,
            'email' => $request->email,
        ]);

        Mail::send('emails.voter-reset-password', [
            'voter'    => $voter,
            'resetUrl' => $resetUrl,
        ], function ($message) use ($voter) {
            $message->to($voter->email)
                    ->subject('Password Reset — Enhance Voting System');
        });

        return back()->with('status', 'Nagpadala na kami ng reset link sa iyong email. Pakitingnan ang iyong inbox o spam folder.');
    }
}