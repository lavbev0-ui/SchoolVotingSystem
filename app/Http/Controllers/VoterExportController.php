<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Support\Facades\Schema;

class VoterExportController extends Controller
{
    public function export()
    {
        $voters = Voter::with(['gradeLevel', 'section'])
            ->orderBy('grade_level_id')
            ->orderBy('last_name')
            ->get();

        $filename = 'voter-list-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($voters) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                '#',
                'Student ID',
                'Last Name',
                'First Name',
                'Middle Name',
                'Grade Level',
                'Section',
                'Email',
                'Password (Hashed)',
                'Status',
                'Registered At',
            ]);

            foreach ($voters as $index => $voter) {
                fputcsv($handle, [
                    $index + 1,
                    $voter->student_id ?? 'N/A',
                    strtoupper($voter->last_name),
                    strtoupper($voter->first_name),
                    strtoupper($voter->middle_name ?? ''),
                    $voter->gradeLevel->name ?? 'N/A',
                    $voter->section->name ?? 'N/A',
                    $voter->email ?? 'N/A',
                    $voter->password ?? 'N/A',
                    $voter->is_active ? 'Active' : 'Inactive',
                    $voter->created_at->format('M d, Y'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}