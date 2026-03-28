<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class Admin2FAMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('web')->check()) {
            return $next($request);
        }

        $admin = Auth::guard('web')->user();

        // ✅ Kung walang email ang admin, hindi pwedeng mag-2FA — hayaan dumaan
        if (empty($admin->email)) {
            return $next($request);
        }

        $require2fa = Setting::where('setting_key', 'require_admin_2fa')->value('value') == '1';

        if ($require2fa && !session()->has('admin_2fa_verified')) {
            if ($request->is('admin/2fa*') || $request->routeIs('admin.2fa.*')) {
                return $next($request);
            }

            // ✅ I-generate ang code at i-redirect sa verification page
            return redirect()->route('admin.2fa.index');
        }

        return $next($request);
    }
}