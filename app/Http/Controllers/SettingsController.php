<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $dbSettings = Setting::pluck('value', 'setting_key')->toArray();

        $settings = array_merge([
            'allow_vote_changes' => '0',
            'real_time_results'  => '0',
            'require_2fa'        => '0',
            'require_admin_2fa'  => '0',
            'session_timeout'    => '30',
        ], $dbSettings);

        return view('dashboards.admin-dashboard.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // ✅ Dagdag na require_admin_2fa sa listahan
        $keys = [
            'allow_vote_changes',
            'real_time_results',
            'require_2fa',
            'require_admin_2fa',
            'session_timeout',
        ];

        DB::transaction(function () use ($request, $keys) {
            foreach ($keys as $key) {
                $oldValue = (string) Setting::where('setting_key', $key)->value('value');
                $newValue = (string) $request->input($key, '0');

                Setting::updateOrCreate(
                    ['setting_key' => $key],
                    ['value'       => $newValue]
                );

                // ✅ Log sa AdminActivityLog kung may nagbago
                if ($oldValue !== $newValue) {
                    AdminDashboardController::logAction(
                        'updated_setting',
                        "Setting [{$key}] changed from [{$oldValue}] to [{$newValue}]"
                    );
                }
            }
        });

        Cache::forget('settings');

        return back()->with('success', 'System preferences have been saved successfully!');
    }
}