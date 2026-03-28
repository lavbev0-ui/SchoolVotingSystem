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
                'setting_key' => 'allow_vote_changes',
                'value' => 'false',
                'label' => 'Allow Vote Changes',
                'type' => 'boolean'
            ],
            // Dagdagan mo dito kung may iba ka pang settings...
        ];

        foreach ($settings as $setting) {
            // DAPAT 'setting_key' ang nakalagay sa dalawang ito:
            Setting::updateOrCreate(
                ['setting_key' => $setting['setting_key']], 
                $setting
            );
        }
    }
}