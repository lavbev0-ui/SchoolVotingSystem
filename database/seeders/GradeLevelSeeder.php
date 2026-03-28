<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        // Linisin ang table para i-reset ang IDs
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('grade_levels')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $levels = [
            ['id' => 1, 'name' => 'Grade 7',  'category' => 'Junior High School'],
            ['id' => 2, 'name' => 'Grade 8',  'category' => 'Junior High School'],
            ['id' => 3, 'name' => 'Grade 9',  'category' => 'Junior High School'],
            ['id' => 4, 'name' => 'Grade 10', 'category' => 'Junior High School'],
            ['id' => 5, 'name' => 'Grade 11', 'category' => 'Senior High School'],
            ['id' => 6, 'name' => 'Grade 12', 'category' => 'Senior High School'],
        ];

        foreach ($levels as $index => $level) {
            DB::table('grade_levels')->insert([
                'id'         => $level['id'],
                'name'       => $level['name'],
                'category'   => $level['category'],
                'order'      => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}