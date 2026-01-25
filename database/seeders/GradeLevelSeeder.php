<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            
            ['name' => 'Grade 1',  'category' => 'Elementary'],
            ['name' => 'Grade 2',  'category' => 'Elementary'],
            ['name' => 'Grade 3',  'category' => 'Elementary'],
            ['name' => 'Grade 4',  'category' => 'Elementary'],
            ['name' => 'Grade 5',  'category' => 'Elementary'],
            ['name' => 'Grade 6',  'category' => 'Elementary'],

            ['name' => 'Grade 7',  'category' => 'Junior High School'],
            ['name' => 'Grade 8',  'category' => 'Junior High School'],
            ['name' => 'Grade 9',  'category' => 'Junior High School'],
            ['name' => 'Grade 10', 'category' => 'Junior High School'],

            ['name' => 'Grade 11', 'category' => 'Senior High School'],
            ['name' => 'Grade 12', 'category' => 'Senior High School'],
        ];

        foreach ($levels as $index => $level) {
            DB::table('grade_levels')->updateOrInsert(
                ['name' => $level['name'], 'category' => $level['category']],
                [
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}