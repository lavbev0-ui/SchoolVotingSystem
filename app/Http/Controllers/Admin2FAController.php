<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\VoterSecurityCode;

class Admin2FAController extends Controller
{
    public function index()
    {
        if (session('voter_2fa_verified')) {
            return redirect()->route('voter.dashboard');
        }

        if (!session('voter_2fa_code')) {
            $this->generateCode();
        }

        return view('dashboards.voter-dashboard.auth.admin-2fa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $voter = Auth::guard('voter')->user();

        // ✅ Kung may phone number — i-verify via Twilio Verify API
        if (empty($voter->email) && !empty($voter->phone_number)) {
            $result = $this->verifyTwilioCode($voter->phone_number, $request->code);

            if ($result) {
                session(['voter_2fa_verified' => true]);
                session()->forget('voter_2fa_code');
                return redirect()->route('voter.dashboard');
            }

            return back()->with('error', 'The security code you entered is incorrect.');
        }

        // ✅ Kung email — gamitin ang session code
        if ($request->code == session('voter_2fa_code')) {
            session(['voter_2fa_verified' => true]);
            session()->forget('voter_2fa_code');
            return redirect()->route('voter.dashboard');
        }

        return back()->with('error', 'The security code you entered is incorrect.');
    }

    public function resend()
    {
        $this->generateCode();
        $voter = Auth::guard('voter')->user();

        if ($voter->email) {
            return back()->with('success', 'The new security code has been sent to your email.');
        } else {
            return back()->with('success', 'The new security code has been sent to your phone number.');
        }
    }

    private function generateCode()
    {
        $voter = Auth::guard('voter')->user();

        // ✅ Kung may email — gamitin ang email + session code
        if (!empty($voter->email)) {
            $code = rand(100000, 999999);
            session(['voter_2fa_code' => $code]);

            try {
                Mail::to($voter->email)->send(new VoterSecurityCode($code, $voter->first_name));
                Log::info("2FA email sent to {$voter->email}: $code");
            } catch (\Exception $e) {
                Log::error("Mail Error: " . $e->getMessage());
            }

        // ✅ Kung walang email pero may phone — gamitin ang Twilio Verify
        } elseif (!empty($voter->phone_number)) {
            session(['voter_2fa_code' => 'twilio_verify']);
            $this->sendTwilioVerify($voter->phone_number);

        } else {
            Log::warning("Voter {$voter->id} has no email or phone number for 2FA.");
        }
    }

    // ✅ Send OTP via Twilio Verify API
    private function sendTwilioVerify(string $phoneNumber): void
    {
        $sid       = config('services.twilio.sid');
        $token     = config('services.twilio.token');
        $verifySid = config('services.twilio.verify_sid');
        $formatted = $this->formatPhNumber($phoneNumber);

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->withoutVerifying()
                ->asForm()
                ->post("https://verify.twilio.com/v2/Services/{$verifySid}/Verifications", [
                    'To'      => $formatted,
                    'Channel' => 'sms',
                ]);

            if ($response->successful()) {
                Log::info("Twilio Verify OTP sent to {$formatted}");
            } else {
                Log::error("Twilio Verify Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Twilio Verify Exception: " . $e->getMessage());
        }
    }

    // ✅ Check OTP via Twilio Verify API
    private function verifyTwilioCode(string $phoneNumber, string $code): bool
    {
        $sid       = config('services.twilio.sid');
        $token     = config('services.twilio.token');
        $verifySid = config('services.twilio.verify_sid');
        $formatted = $this->formatPhNumber($phoneNumber);

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->withoutVerifying()
                ->asForm()
                ->post("https://verify.twilio.com/v2/Services/{$verifySid}/VerificationCheck", [
                    'To'   => $formatted,
                    'Code' => $code,
                ]);

            $body = $response->json();
            Log::info("Twilio Verify Check: " . json_encode($body));

            return isset($body['status']) && $body['status'] === 'approved';
        } catch (\Exception $e) {
            Log::error("Twilio Verify Check Exception: " . $e->getMessage());
            return false;
        }
    }

    private function formatPhNumber(string $number): string
    {
        $number = preg_replace('/\D/', '', $number);

        if (str_starts_with($number, '09') && strlen($number) === 11) return '+63' . substr($number, 1);
        if (str_starts_with($number, '9') && strlen($number) === 10)  return '+63' . $number;
        if (str_starts_with($number, '63'))                            return '+' . $number;

        return '+' . $number;
    }
}
