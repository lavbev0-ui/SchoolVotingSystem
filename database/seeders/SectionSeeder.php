<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GradeLevel;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectionsData = [
            'Grade 7' => ['Diamond', 'Ruby', 'Emerald'],
            'Grade 8' => ['Newton', 'Einstein', 'Galileo'],
            'Grade 9' => ['Hope', 'Charity', 'Faith'],
            'Grade 10' => ['Section 1', 'Section 2', 'Section 3'],
            'Grade 11' => ['STEM A', 'STEM B', 'ABM A', 'HUMSS A', 'ICT A'],
            'Grade 12' => ['STEM A', 'ABM A', 'HUMSS A', 'TVL A'],
        ];

        foreach ($sectionsData as $gradeName => $sections) {
            // Hinahanap ang ID ng Grade Level base sa pangalan (hal. 'Grade 7')
            $gradeLevel = GradeLevel::where('name', $gradeName)->first();

            if ($gradeLevel) {
                foreach ($sections as $sectionName) {
                    // Ginagamit ang updateOrInsert base sa Grade ID at Section Name
                    // para maiwasan ang dobleng entries sa dropdown
                    DB::table('sections')->updateOrInsert(
                        [
                            'grade_level_id' => $gradeLevel->id, 
                            'name'           => $sectionName
                        ],
                        [
                            // Tinatanggal ang 'year_level' column kung wala ito sa database
                            // para iwas sa "Column not found" error
                            'created_at' => now(), 
                            'updated_at' => now()
                        ]
                    );
                }
            }
        }
    }
}