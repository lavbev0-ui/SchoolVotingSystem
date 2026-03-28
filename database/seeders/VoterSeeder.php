<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class VoterSeeder extends Seeder
{
    public function run(): void
    {
        // Linisin ang table para iwas duplicate voters
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Voter::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $voters = [
            [
                'first_name' => 'STUDENT',
                'last_name' => 'ONE',
                // FINAL FIX: Ginamit ang student_id para tumugma sa final Migration at Model
                'student_id' => '2026001', 
                'email' => 'student1@example.com', 
                'password' => Hash::make('password123'),
                'grade_level_id' => 6,
                'is_active' => true,
            ],
            [
                'first_name' => 'STUDENT',
                'last_name' => 'TWO',
                'student_id' => '2026002',
                'email' => 'student2@example.com',
                'password' => Hash::make('password123'),
                'grade_level_id' => 6,
                'is_active' => true,
            ],
            [
                'first_name' => 'STUDENT',
                'last_name' => 'THREE',
                'student_id' => '2026003',
                'email' => 'student3@example.com',
                'password' => Hash::make('password123'),
                'grade_level_id' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($voters as $voter) {
            Voter::create($voter);
        }
        
        $this->command->info('Success: 3 Voters seeded using student_id column!');
    }
}