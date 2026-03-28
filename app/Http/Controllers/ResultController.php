<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Voter;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    /**
     * Ipinapakita ang listahan ng lahat ng eleksyon (Election History).
     */
    public function index(Request $request)
    {
        $totalVoters = Voter::count();

        $completedElections = Election::whereIn('status', ['active', 'completed'])
            ->orWhere('end_at', '<=', now())
            ->latest('created_at')
            ->paginate(10);

        $completedElections->getCollection()->transform(function ($election) {
            $election->unique_votes_count = Vote::where('election_id', $election->id)
                ->distinct('voter_id')
                ->count('voter_id');
            return $election;
        });

        return view('dashboards.admin-dashboard.results.index', compact(
            'completedElections',
            'totalVoters'
        ));
    }

    /**
     * DEDICATED PAGE: Ipinapakita ang resulta na naka-grupo base sa Partylist.
     */
    public function show(Election $election)
    {
        // ✅ FIX: Gamitin ang withCount na may election_id filter
        // para tama ang bilang kahit may multiple elections ang isang candidate
        $election->load(['positions.candidates']);

        foreach ($election->positions as $position) {
            foreach ($position->candidates as $candidate) {
                $candidate->votes_count = Vote::where('candidate_id', $candidate->id)
                    ->where('election_id', $election->id)
                    ->where('position_id', $position->id)
                    ->count();
            }
        }

        // I-flat ang lahat ng candidates mula sa lahat ng positions
        $allCandidates = collect();
        foreach ($election->positions as $position) {
            foreach ($position->candidates as $candidate) {
                $candidate->pos_label = $position->title;
                $candidate->pos_order = $position->id;
                $allCandidates->push($candidate);
            }
        }

        // I-group ang candidates base sa 'party' field
        $groupedByParty = $allCandidates->groupBy(function ($item) {
            return $item->party ?? 'Independent';
        });

        // Turnout
        $totalUniqueVoters = Vote::where('election_id', $election->id)
            ->distinct('voter_id')
            ->count('voter_id');

        // Winners list
        $winners = $this->calculateWinners($election);

        return view('dashboards.admin-dashboard.results.show', compact(
            'election',
            'totalUniqueVoters',
            'groupedByParty',
            'winners'
        ));
    }

    /**
     * Helper function para makuha ang mga nanalo bawat posisyon.
     */
    private function calculateWinners($election)
    {
        $winnersList = [];
        foreach ($election->positions as $position) {
            $maxVotes = $position->candidates->max('votes_count');

            $candidatesWithMax = $position->candidates
                ->where('votes_count', $maxVotes)
                ->where('votes_count', '>', 0);

            if ($candidatesWithMax->count() > 1) {
                $names = $candidatesWithMax->map(fn($c) => $c->first_name . ' ' . $c->last_name . ' (TIE)')->join(', ');
                $winnersList[] = [
                    'position' => $position->title,
                    'name'     => $names,
                    'votes'    => $maxVotes,
                ];
            } elseif ($candidatesWithMax->count() === 1) {
                $winner = $candidatesWithMax->first();
                $winnersList[] = [
                    'position' => $position->title,
                    'name'     => $winner->first_name . ' ' . $winner->last_name,
                    'votes'    => $maxVotes,
                ];
            }
        }
        return $winnersList;
    }
}