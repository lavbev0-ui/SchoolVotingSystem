<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'allow_vote_changes',
                'label' => 'Allow Vote Changes',
                'value' => '0', // Disabled
                'type' => 'boolean'
            ],
            [
                'key' => 'real_time_results',
                'label' => 'Real-time Results',
                'value' => '0', // Disabled
                'type' => 'boolean'
            ],
            [
                'key' => 'require_2fa',
                'label' => 'Two-Factor Authentication',
                'value' => '1', // Enabled
                'type' => 'boolean'
            ],
            [
                'key' => 'session_timeout',
                'label' => 'Session Timeout',
                'value' => '30', // 30 minutes
                'type' => 'number'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}