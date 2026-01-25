<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    // Display the settings
    public function index()
    {
        // Fetch all settings as a collection keyed by the 'key' column for easy access
        $settings = Setting::all()->keyBy('key');

        return view('dashboards.admin-dashboard.settings.index', compact('settings'));
    }

    // Update the settings
    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            // Update the setting if it exists in our DB
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        // Handle unchecked checkboxes (boolean fields)
        // If a checkbox is unchecked, it isn't sent in the request, so we must manually set it to 0
        $booleans = ['allow_vote_changes', 'real_time_results', 'require_2fa'];
        foreach ($booleans as $key) {
            if (!$request->has($key)) {
                Setting::where('key', $key)->update(['value' => '0']);
            }
        }

        cache()->forget('setting_session_timeout');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}