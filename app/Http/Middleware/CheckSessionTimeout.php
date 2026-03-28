<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('voter')->check()) {

            // Skip timeout check para sa POST requests
            if ($request->isMethod('post')) {
                return $next($request);
            }

            $timeoutMinutes = Cache::remember('setting_session_timeout', 60, function () {
                return (int) Setting::where('setting_key', 'session_timeout')->value('value') ?: 30;
            });

            $timeoutSeconds = $timeoutMinutes * 60;
            $lastActivity   = session('last_activity_time');

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                Auth::guard('voter')->logout();
                session()->forget(['last_activity_time', 'voter_2fa_verified']);
                session()->regenerateToken();

                // ✅ Kung AJAX/fetch request ang nag-expire, ibalik ang JSON
                // para ang frontend ay makapag-redirect nang maayos
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['expired' => true, 'redirect' => route('voter.login')], 401);
                }

                return redirect()->route('voter.login')
                    ->withErrors(['session' => 'Ang iyong session ay nag-expire na. Mangyaring mag-log in muli.']);
            }

            // ✅ I-update ang last_activity_time sa REAL requests lang
            // Hindi sa background AJAX/fetch — para gumana ang session timeout
            if (!$request->ajax() && !$request->wantsJson()) {
                session(['last_activity_time' => time()]);
            }
        }

        return $next($request);
    }
}