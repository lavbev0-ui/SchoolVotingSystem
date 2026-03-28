<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\VoterActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoterDashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $voterId = Auth::guard('voter')->id();

        $active = Election::with(['positions.candidates' => function($q) {
                $q->select('id', 'position_id', 'first_name', 'last_name', 'party', 'photo_path', 'manifesto', 'bio');
            }])
            ->where('is_active', 1)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->select('id', 'title', 'description', 'start_at', 'end_at', 'is_active')
            ->get()
            ->map(function ($election) use ($voterId) {
                $election->has_voted = Vote::where('election_id', $election->id)
                                           ->where('voter_id', $voterId)
                                           ->exists();
                foreach ($election->positions as $position) {
                    $position->candidates->each->makeVisible(['manifesto', 'platform', 'bio']);
                }
                return $election;
            });

        $upcoming = Election::where('is_active', 1)->where('start_at', '>', $now)->get();

        // Ipakita lang ang ended elections kung saan bumoto ang voter
        $ended = Election::where('is_active', 1)
            ->where('end_at', '<', $now)
            ->whereHas('votes', function ($q) use ($voterId) {
                $q->where('voter_id', $voterId);
            })
            ->latest('end_at')
            ->get()
            ->map(function ($election) use ($voterId) {
                $election->has_voted = true; // Laging true — na-filter na ng whereHas

                $election->my_ballot = Vote::where('election_id', $election->id)
                    ->where('voter_id', $voterId)
                    ->with(['candidate', 'position'])
                    ->get()
                    ->map(function ($vote) {
                        return [
                            'position'        => $vote->position?->title ?? '—',
                            'candidate_name'  => $vote->candidate
                                ? $vote->candidate->first_name . ' ' . $vote->candidate->last_name
                                : '—',
                            'candidate_party' => $vote->candidate?->party ?? 'Independent',
                            'photo_path'      => $vote->candidate?->photo_path,
                        ];
                    });

                return $election;
            });

        $showLiveResults = Setting::where('setting_key', 'real_time_results')->value('value') == '1';
        $sessionTimeout  = Setting::where('setting_key', 'session_timeout')->value('value') ?? 30;

        return view('dashboards.voter-dashboard.dashboard', compact(
            'active', 'upcoming', 'ended', 'showLiveResults', 'sessionTimeout'
        ));
    }

    public function show($id)
    {
        $voterId = Auth::guard('voter')->id();

        $election = Election::with(['positions' => function($q) {
            $q->orderBy('id');
        }, 'positions.candidates' => function($q) {
            $q->select('id', 'position_id', 'first_name', 'last_name', 'party', 'photo_path', 'manifesto', 'bio');
        }])->findOrFail($id);

        $allowChanges = Setting::where('setting_key', 'allow_vote_changes')->value('value') == '1';
        $hasVoted     = Vote::where('voter_id', $voterId)->where('election_id', $id)->exists();

        if ($hasVoted && !$allowChanges) {
            return view('dashboards.voter-dashboard.already-voted', compact('election'));
        }

        return view('dashboards.voter-dashboard.vote', compact('election'));
    }

    public function results($id)
    {
        $results = Election::with(['positions.candidates'])->findOrFail($id);

        foreach ($results->positions as $position) {
            foreach ($position->candidates as $candidate) {
                $candidate->votes_count = Vote::where('candidate_id', $candidate->id)
                    ->where('election_id', $results->id)
                    ->where('position_id', $position->id)
                    ->count();
            }
        }

        $showLiveResults = Setting::where('setting_key', 'real_time_results')->value('value') == '1';
        $isEnded = Carbon::parse($results->end_at)->isPast();

        if (!$isEnded && !$showLiveResults) {
            return redirect()->route('voter.dashboard')->with('error', 'Live results viewing is currently restricted.');
        }

        $initialPositions = $results->positions->map(function ($pos) {
            $candidates = $pos->candidates->map(function ($c) {
                return [
                    'id'          => $c->id,
                    'first_name'  => $c->first_name,
                    'last_name'   => $c->last_name,
                    'party'       => $c->party,
                    'votes_count' => $c->votes_count,
                ];
            })->values()->toArray();

            return [
                'id'          => $pos->id,
                'title'       => $pos->title,
                'total_votes' => $pos->candidates->sum('votes_count'),
                'candidates'  => $candidates,
            ];
        })->values()->toArray();

        return view('dashboards.voter-dashboard.results', compact(
            'results', 'initialPositions'
        ));
    }

    public function resultsData($id)
    {
        $election = Election::with(['positions.candidates'])->findOrFail($id);

        $showLiveResults = Setting::where('setting_key', 'real_time_results')->value('value') == '1';
        $isEnded = Carbon::parse($election->end_at)->isPast();

        if (!$isEnded && !$showLiveResults) {
            return response()->json(['error' => 'Restricted'], 403);
        }

        $positions = $election->positions->map(function ($position) use ($election) {
            $candidates = $position->candidates->map(function ($candidate) use ($election, $position) {
                return [
                    'id'          => $candidate->id,
                    'first_name'  => $candidate->first_name,
                    'last_name'   => $candidate->last_name,
                    'party'       => $candidate->party,
                    'votes_count' => Vote::where('candidate_id', $candidate->id)
                        ->where('election_id', $election->id)
                        ->where('position_id', $position->id)
                        ->count(),
                ];
            })->values();

            return [
                'id'          => $position->id,
                'title'       => $position->title,
                'total_votes' => $candidates->sum('votes_count'),
                'candidates'  => $candidates,
            ];
        });

        return response()->json([
            'positions' => $positions,
            'is_ended'  => $isEnded,
        ]);
    }

    public function store(Request $request, $electionId)
    {
        $votes = $request->input('votes');

        if (empty($votes)) {
            session()->flash('error', 'No votes submitted.');
            return response()->json(['redirect' => route('voter.dashboard')]);
        }

        $voterId      = Auth::guard('voter')->id();
        $allowChanges = Setting::where('setting_key', 'allow_vote_changes')->value('value') == '1';

        try {
            DB::beginTransaction();

            $hasVoted = Vote::where('election_id', $electionId)
                            ->where('voter_id', $voterId)
                            ->lockForUpdate()
                            ->exists();

            if ($hasVoted) {
                if ($allowChanges) {
                    Vote::where('voter_id', $voterId)->where('election_id', $electionId)->delete();
                } else {
                    throw new \Exception("Unauthorized: This ballot has already been cast.");
                }
            }

            foreach ($votes as $positionId => $candidateData) {
                $candidateIds = is_array($candidateData) ? $candidateData : [$candidateData];
                foreach ($candidateIds as $candidateId) {
                    if ($candidateId) {
                        Vote::create([
                            'voter_id'     => $voterId,
                            'election_id'  => $electionId,
                            'position_id'  => $positionId,
                            'candidate_id' => $candidateId,
                        ]);
                    }
                }
            }

            $election = Election::find($electionId);
            VoterActivityLog::create([
                'voter_id'    => $voterId,
                'action'      => 'voted',
                'description' => 'Submitted ballot for: ' . ($election?->title ?? 'Election #' . $electionId),
            ]);

            DB::commit();
            session()->flash('success', 'Your official ballot has been recorded successfully.');
            return response()->json(['redirect' => route('voter.dashboard')]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Submission Failed: ' . $e->getMessage());
            return response()->json(['redirect' => route('voter.dashboard')]);
        }
    }
}