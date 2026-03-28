<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. LINISIN ang table bago mag-seed para iwas duplicates
        // Gagamit tayo ng statement para i-reset pati ang ID count
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Candidate::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $voters = Voter::limit(5)->get();

        if ($voters->count() < 3) {
            $this->command->error('Error: Kailangan mo ng hindi bababa sa 3 Voters sa database!');
            return;
        }

        // 2. Sample Candidates (Siguraduhing 'platform' ay laging may laman)
        Candidate::create([
            'voter_id'       => $voters[0]->id,
            'position_id'    => 5, 
            'grade_level_id' => 6, // FIX: Para lumabas sa Grade 6 dashboard
            'section_id'     => 13,
            'first_name'     => 'JUAN',
            'last_name'      => 'DELA CRUZ',
            'party'          => 'MAKABAYAN',
            'platform'       => 'SERBISYONG TAPAT PARA SA LAHAT. TRANSPARENCY SA PONDO NG SSLG.',
            'bio'            => 'Isang lider na may prinsipyo.',
        ]);

        Candidate::create([
            'voter_id'       => $voters[1]->id,
            'position_id'    => 5,
            'grade_level_id' => 6, // FIX
            'section_id'     => 12,
            'first_name'     => 'MARIA',
            'last_name'      => 'CLARA',
            'party'          => 'PRO-STUDENT',
            'platform'       => 'BOSES NG KABATAAN, PAKINGGAN. PAGPAPALAWAK NG STUDENT LOUNGE.',
            'bio'            => 'Makatao at matulungin.',
        ]);

        Candidate::create([
            'voter_id'       => $voters[2]->id,
            'position_id'    => 6, 
            'grade_level_id' => 6, // FIX
            'section_id'     => 14,
            'first_name'     => 'JOSE',
            'last_name'      => 'RIZAL',
            'party'          => 'MAKABAYAN',
            'platform'       => 'KARUNUNGAN ANG SUSI SA PAG-UNLAD. FREE SCHOOL SUPPLIES PARA SA LAHAT.',
            'bio'            => 'Matalino at masipag.',
        ]);

        $this->command->info('Success: Candidates seeded and linked to Grade 6!');
    }
}