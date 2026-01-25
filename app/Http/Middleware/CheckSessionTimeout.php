<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Only run this check if the user is logged in
        if (Auth::guard('voter')->check()) {
            
            // 2. Get the timeout value from DB (default to 120 mins if missing)
            // We use cache to avoid hitting the DB on every single click
            $timeoutMinutes = cache()->remember('setting_session_timeout', 60, function () {
                return Setting::where('key', 'session_timeout')->value('value') ?? 120;
            });

            $timeoutSeconds = $timeoutMinutes * 60;
            
            // 3. Check Last Activity
            $lastActivity = session('last_activity_time');
            
            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                // TIMEOUT REACHED: Log them out
                Auth::guard('voter')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('voter.login')
                    ->withErrors(['userID' => 'Session timed out due to inactivity.']);
            }

            // 4. Update Last Activity Time
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}