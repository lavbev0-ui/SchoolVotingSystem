<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminSecurityCode;

class Admin2FAAdminController extends Controller
{
    public function index()
    {
        if (session('admin_2fa_verified')) {
            return redirect()->route('admin.index');
        }

        if (!session('admin_2fa_code')) {
            $this->generateCode();
        }

        return view('auth.admin-2fa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        if ($request->code == session('admin_2fa_code')) {
            session(['admin_2fa_verified' => true]);
            session()->forget('admin_2fa_code');

            // ✅ Log admin 2FA verification
            AdminDashboardController::logAction(
                'admin_2fa_verified',
                'Admin verified 2FA successfully.'
            );

            return redirect()->route('admin.index');
        }

        return back()->with('error', 'The security code you entered is incorrect.');
    }

    public function resend()
    {
        $this->generateCode();
        return back()->with('success', 'A new security code has been sent to your email.');
    }

    private function generateCode()
    {
        $admin = Auth::guard('web')->user();

        if (empty($admin->email)) {
            Log::warning("Admin {$admin->id} has no email for 2FA.");
            return;
        }

        $code = rand(100000, 999999);
        session(['admin_2fa_code' => $code]);

        try {
            Mail::to($admin->email)->send(new AdminSecurityCode($code, $admin->name));
            Log::info("Admin 2FA email sent to {$admin->email}: $code");
        } catch (\Exception $e) {
            Log::error("Admin 2FA Mail Error: " . $e->getMessage());
        }
    }
}