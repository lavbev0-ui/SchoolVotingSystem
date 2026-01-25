<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Admin User
        // We use 'firstOrCreate' so it doesn't crash if you run the seeder twice
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Search by this email
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'), // Default password (change this!)
                'email_verified_at' => now(),
            ]
        );

        // 2. Run External Seeders (Grade Levels)
        $this->call([
            GradeLevelSeeder::class,
            SectionSeeder::class,
            SettingSeeder::class,
        ]);
    }
}