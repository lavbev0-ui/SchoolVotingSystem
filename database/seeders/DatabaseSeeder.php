<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GradeLevel;
use App\Models\Section;
use App\Models\Election;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Gumawa o I-update ang Admin Account
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. I-seed ang Grade Levels
        $g7 = GradeLevel::firstOrCreate(['name' => 'Grade 7'], ['category' => 'Junior High']);
        $g8 = GradeLevel::firstOrCreate(['name' => 'Grade 8'], ['category' => 'Junior High']);
        $g9 = GradeLevel::firstOrCreate(['name' => 'Grade 9'], ['category' => 'Junior High']);
        $g10 = GradeLevel::firstOrCreate(['name' => 'Grade 10'], ['category' => 'Junior High']);
        $g11 = GradeLevel::firstOrCreate(['name' => 'Grade 11'], ['category' => 'Senior High']);
        $g12 = GradeLevel::firstOrCreate(['name' => 'Grade 12'], ['category' => 'Senior High']);

        // 3. I-seed ang Sections
        $sections = [
            ['name' => 'Section A', 'grade' => $g7],
            ['name' => 'Section B', 'grade' => $g7],
            ['name' => 'Section A', 'grade' => $g8],
            ['name' => 'Section B', 'grade' => $g8],
            ['name' => 'Section A', 'grade' => $g9],
            ['name' => 'Section B', 'grade' => $g9],
            ['name' => 'Section A', 'grade' => $g10],
            ['name' => 'Section B', 'grade' => $g10],
            ['name' => 'Stem', 'grade' => $g11],
            ['name' => 'Humss', 'grade' => $g11],
            ['name' => 'ICT', 'grade' => $g11],
            ['name' => 'Stem', 'grade' => $g12],
            ['name' => 'Humss', 'grade' => $g12],
            ['name' => 'ICT', 'grade' => $g12],
        ];

        foreach ($sections as $sec) {
            Section::updateOrCreate(
                [
                    'name' => $sec['name'], 
                    'grade_level_id' => $sec['grade']->id
                ],
                [
                    // FIX: Idinagdag ang year_level para hindi na mag-error ang SQL
                    'year_level' => $sec['grade']->name 
                ]
            );
        }

        // 4. Siguraduhin na may Election record
        $election = Election::first() ?? Election::create([
            'user_id' => $admin->id,
            'title' => 'SSLG Election 2026',
            'description' => 'Student School Government Election',
            'start_at' => now(),
            'end_at' => now()->addDays(7),
            'eligibility_type' => 'all', 
            'eligibility_metadata' => null,
        ]);

        // 5. I-run ang Dependent Seeders
        $this->call([
            PositionSeeder::class, 
            VoterSeeder::class,
            CandidateSeeder::class,
        ]);
    }
}