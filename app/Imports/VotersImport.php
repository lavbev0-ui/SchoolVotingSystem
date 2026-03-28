<?php

namespace App\Imports;

use App\Models\Voter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class VotersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Ang keys (student_id, first_name, etc.) ay dapat tumugma sa header ng Excel mo
        return new Voter([
            'student_id'   => $row['student_id'],
            'first_name'   => $row['first_name'],
            'middle_name'  => $row['middle_name'] ?? null,
            'last_name'    => $row['last_name'],
            'password'     => Hash::make($row['student_id']), 
            'is_active'    => true,
        ]);
    }
}