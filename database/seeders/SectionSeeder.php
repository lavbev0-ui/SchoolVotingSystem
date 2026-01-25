<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GradeLevel; // Ensure you have this model import

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the sections for each specific Grade Level
        $sectionsData = [
            // Elementary (Often named after Flowers, Trees, or Heroes)
            'Grade 1' => ['Sampaguita', 'Rosal', 'Ilang-ilang'],
            'Grade 2' => ['Narra', 'Molave', 'Acacia'],
            'Grade 3' => ['Rizal', 'Bonifacio', 'Mabini'],
            'Grade 4' => ['Section A', 'Section B', 'Section C'],
            'Grade 5' => ['Section A', 'Section B', 'Section C'],
            'Grade 6' => ['Section A', 'Section B', 'Section C'],

            // Junior High School (Often named after Gems, Virtues, or Scientists)
            'Grade 7' => ['Diamond', 'Ruby', 'Emerald'],
            'Grade 8' => ['Newton', 'Einstein', 'Galileo'],
            'Grade 9' => ['Hope', 'Charity', 'Faith'],
            'Grade 10' => ['Section 1', 'Section 2', 'Section 3'],

            // Senior High School (Usually named by Strand)
            'Grade 11' => ['STEM A', 'STEM B', 'ABM A', 'HUMSS A', 'ICT A'],
            'Grade 12' => ['STEM A', 'ABM A', 'HUMSS A', 'TVL A'],
        ];

        foreach ($sectionsData as $gradeName => $sections) {
            // 1. Find the Grade Level ID from the database
            $gradeLevel = GradeLevel::where('name', $gradeName)->first();

            // 2. Only insert if the Grade Level exists
            if ($gradeLevel) {
                foreach ($sections as $sectionName) {
                    DB::table('sections')->updateOrInsert(
                        [
                            'grade_level_id' => $gradeLevel->id, 
                            'name' => $sectionName
                        ],
                        [
                            'created_at' => now(), 
                            'updated_at' => now()
                        ]
                    );
                }
            }
        }
    }
}