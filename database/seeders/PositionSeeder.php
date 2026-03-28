<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Election;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('positions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Siguraduhin na may election record muna
        $election = Election::first() ?? Election::create([
            'title' => 'SSLG Election 2026',
            'status' => 'active',
            'description' => 'Student School Government Election'
        ]);

        $positions = [
            ['id' => 5, 'title' => 'President'],
            ['id' => 6, 'title' => 'Vice-President'],
        ];

        foreach ($positions as $pos) {
            DB::table('positions')->insert([
                'id' => $pos['id'],
                'title' => $pos['title'],
                'election_id' => $election->id, // FIX: Idinagdag ang election_id
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}