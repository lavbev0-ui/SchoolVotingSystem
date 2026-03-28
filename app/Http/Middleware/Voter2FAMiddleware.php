<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class Voter2FAMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. I-check muna kung ang user ay login bilang Voter.
        // Kung hindi sila login, hayaan silang dumaan (para sa guest routes).
        if (!Auth::guard('voter')->check()) {
            return $next($request);
        }

        // 2. I-check kung naka-ON ang 2FA sa System Settings.
        $require2fa = Setting::where('setting_key', 'require_2fa')->value('value') == '1';

        // 3. Kung kailangan ng 2FA pero wala pang 'voter_2fa_verified' sa session.
        if ($require2fa && !session()->has('voter_2fa_verified')) {
            
            // 4. EXCEPTION: Huwag i-redirect kung ang request ay papunta na sa 2FA routes.
            // Ito ang "Loop Stopper" para hindi bumalik sa admin login.
            if ($request->is('vote/security*') || $request->routeIs('voter.2fa.*')) {
                return $next($request);
            }

            // 5. I-redirect ang student sa verification page.
            return redirect()->route('voter.2fa.index');
        }

        return $next($request);
    }
}