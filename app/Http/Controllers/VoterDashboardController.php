<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Election;
use App\Models\Vote; 

class VoterDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->guard('voter')->user();
        
        $gradeId = $user->grade_level_id;
        $sectionId = $user->section_id;

        // 1. Fetch Elections (Your existing logic)
        $elections = \App\Models\Election::query()
            ->whereIn('status', ['active', 'upcoming', 'completed'])
            ->where(function($query) use ($gradeId, $sectionId) {
                $query->where('eligibility_type', 'all')
                ->orWhere(function($q) use ($gradeId) {
                    $q->where('eligibility_type', 'grade-level')
                      ->where(function($sub) use ($gradeId) {
                          $sub->whereJsonContains('eligibility_metadata->grades', (string)$gradeId)
                              ->orWhereJsonContains('eligibility_metadata->grades', (int)$gradeId);
                      });
                })
                ->orWhere(function($q) use ($sectionId) {
                    $q->where('eligibility_type', 'section')
                      ->where(function($sub) use ($sectionId) {
                          $sub->whereJsonContains('eligibility_metadata->sections', (string)$sectionId)
                              ->orWhereJsonContains('eligibility_metadata->sections', (int)$sectionId);
                      });
                });
            })
            ->latest()
            ->get();

        // 2. [NEW] Get IDs of elections where the user has already voted
        // We pluck 'election_id' to get a simple array like [1, 3, 5]
        $votedElectionIds = \App\Models\Vote::where('voter_id', $user->id)
            ->distinct()
            ->pluck('election_id')
            ->toArray();

        // Pass $votedElectionIds to the view
        return view('dashboards.voter-dashboard.dashboard', compact('elections', 'votedElectionIds'));
    }

    public function show($id)
    {
        $voter = Auth::guard('voter')->user();
        $election = Election::with(['positions.candidates'])->findOrFail($id);

        // --- 1. CHECK IF VOTER HAS ALREADY VOTED ---
        // We check this first to fail fast
        $hasVoted = \App\Models\Vote::where('voter_id', $voter->id)
            ->whereHas('candidate.position', function ($query) use ($id) {
                $query->where('election_id', $id);
            })
            ->exists();

        if ($hasVoted) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'You have already voted in this election.');
        }

        // --- 2. CHECK ELIGIBILITY (The Fix) ---
        $isEligible = false;

        // If election is for everyone, they are eligible
        if ($election->eligibility_type === 'all') {
            $isEligible = true;
        } 
        // If election is for specific grades
        elseif ($election->eligibility_type === 'grade-level') {
            // Decode the JSON metadata (e.g. {"grades": ["11", "12"]})
            $metadata = is_string($election->eligibility_metadata) 
                ? json_decode($election->eligibility_metadata, true) 
                : $election->eligibility_metadata;

            $allowedGrades = $metadata['grades'] ?? [];
            
            // Check if user's grade ID is in the array
            if (in_array((string)$voter->grade_level_id, $allowedGrades)) {
                $isEligible = true;
            }
        }
        // If election is for specific sections
        elseif ($election->eligibility_type === 'section') {
            $metadata = is_string($election->eligibility_metadata) 
                ? json_decode($election->eligibility_metadata, true) 
                : $election->eligibility_metadata;

            $allowedSections = $metadata['sections'] ?? [];
            
            if (in_array((string)$voter->section_id, $allowedSections)) {
                $isEligible = true;
            }
        }

        // If they failed the checks above, kick them out
        if (!$isEligible) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'Access Denied: You are not eligible for this election.');
        }

        return view('dashboards.voter-dashboard.vote', compact('election'));
    }


    public function store(Request $request)
    {
        $voter = Auth::guard('voter')->user();

        $request->validate([
            'votes' => 'required|array',
            'election_id' => 'required|integer|exists:elections,id',
        ]);

        $electionId = $request->input('election_id');
        $election = Election::findOrFail($electionId);

        // --- 1. FIX: CORRECT ELIGIBILITY CHECK ---
        // We need to replicate the logic from 'index'/'show', not use undefined columns.
        $isEligible = false;

        if ($election->eligibility_type === 'all') {
            $isEligible = true;
        } else {
            // Decode metadata
            $metadata = is_string($election->eligibility_metadata) 
                ? json_decode($election->eligibility_metadata, true) 
                : $election->eligibility_metadata;

            if ($election->eligibility_type === 'grade-level') {
                $allowed = $metadata['grades'] ?? [];
                if (in_array((string)$voter->grade_level_id, $allowed)) $isEligible = true;
            } elseif ($election->eligibility_type === 'section') {
                $allowed = $metadata['sections'] ?? [];
                if (in_array((string)$voter->section_id, $allowed)) $isEligible = true;
            }
        }

        if (!$isEligible) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'Error: You are not eligible for this election.');
        }

        // --- 2. EXISTING CHECK: HAS VOTED? ---
        $hasVoted = Vote::where('voter_id', $voter->id)
            ->where('election_id', $electionId) // Optimized check
            ->exists();

        if ($hasVoted) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'Error: You have already voted in this election.');
        }

        // --- 3. FIX: SAVE ALL REQUIRED FIELDS ---
        DB::transaction(function () use ($request, $voter, $electionId) {
            // $positionId comes from the array key in the form loop
            foreach ($request->votes as $positionId => $candidateInput) {
                
                // Normalize input: Ensure we treat everything as an array 
                // (Single radio button = string/int, Checkboxes = array)
                $candidateIds = is_array($candidateInput) ? $candidateInput : [$candidateInput];

                foreach ($candidateIds as $candidateId) {
                    Vote::create([
                        'voter_id'     => $voter->id,
                        'election_id'  => $electionId,  // <--- WAS MISSING
                        'position_id'  => $positionId,  // <--- WAS MISSING
                        'candidate_id' => $candidateId,
                    ]);
                }
            }
        });

        return redirect()->route('voter.dashboard')
            ->with('success', 'Vote submitted successfully!');
    }

    public function results($id)
    {
        // 1. Fetch Election with the deep relationship structure
        $election = Election::with([
            // Load Positions, and inside Positions load Candidates, and inside Candidates count Votes
            'positions.candidates' => function ($query) {
                $query->withCount('votes'); // This creates the 'votes_count' attribute
            }
        ])->findOrFail($id);

        return view('dashboards.voter-dashboard.results', compact('election'));
    }
}