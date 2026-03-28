<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use App\Models\GradeLevel;
use App\Models\Setting;
use App\Models\VoterActivityLog;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // ✅ Log admin dashboard access
        self::logAction('viewed_dashboard', 'Accessed the admin dashboard.');

        $stats = $this->calculateStats();
        return view('dashboards.admin-dashboard.dashboard', [
            'stats' => $stats,
            ...$stats
        ]);
    }

    public function getLiveStats()
    {
        return response()->json($this->calculateStats());
    }

    // ✅ Static helper — pwedeng tawagan kahit saan sa app
    public static function logAction(string $action, string $description = ''): void
    {
        try {
            AdminActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silent fail — hindi dapat mag-block ng request
        }
    }

    private function calculateStats(): array
    {
        $now = now();

        $totalRegistered = Schema::hasTable('voters') ? Voter::count() : 0;

        $activeElectionIds = collect();
        if (Schema::hasTable('elections')) {
            $activeElectionIds = Election::where('is_active', 1)
                ->where('start_at', '<=', $now)
                ->where('end_at', '>=', $now)
                ->pluck('id');
        }

        $activeElectionsCount = $activeElectionIds->count();

        $totalBallotsCast  = 0;
        $perElectionVoters = [];

        if (Schema::hasTable('votes') && $activeElectionIds->isNotEmpty()) {
            foreach ($activeElectionIds as $electionId) {
                $count = Vote::where('election_id', $electionId)
                    ->distinct('voter_id')
                    ->count('voter_id');
                $totalBallotsCast += $count;
                $perElectionVoters[$electionId] = $count;
            }
        }

        $avgTurnoutPercentage = 0;
        if ($activeElectionsCount > 0 && $totalRegistered > 0) {
            $avgTurnoutPercentage = ($totalBallotsCast / ($activeElectionsCount * $totalRegistered)) * 100;
        }

        $activeElections = collect();
        if ($activeElectionIds->isNotEmpty()) {
            $elections = Election::whereIn('id', $activeElectionIds)
                ->with(['positions.candidates'])
                ->get();

            $activeElections = $elections->map(function ($election) use ($totalRegistered, $perElectionVoters) {
                $uniqueVoters    = $perElectionVoters[$election->id] ?? 0;
                $electionTurnout = $totalRegistered > 0
                    ? round(($uniqueVoters / $totalRegistered) * 100, 1)
                    : 0;

                $positions = $election->positions->map(function ($position) use ($election) {
                    $candidates = $position->candidates->map(function ($candidate) use ($election, $position) {
                        return [
                            'id'          => $candidate->id,
                            'first_name'  => $candidate->first_name,
                            'last_name'   => $candidate->last_name,
                            'full_name'   => $candidate->full_name,
                            'party'       => $candidate->party,
                            'photo_path'  => $candidate->photo_path,
                            'votes_count' => Vote::where('candidate_id', $candidate->id)
                                ->where('election_id', $election->id)
                                ->where('position_id', $position->id)
                                ->count(),
                        ];
                    });

                    return [
                        'id'          => $position->id,
                        'title'       => $position->title,
                        'total_votes' => $candidates->sum('votes_count'),
                        'candidates'  => $candidates,
                    ];
                });

                return [
                    'id'                  => $election->id,
                    'title'               => $election->title,
                    'unique_voters_count' => $uniqueVoters,
                    'turnout_percentage'  => $electionTurnout,
                    'total_registered'    => $totalRegistered,
                    'positions'           => $positions,
                ];
            });
        }

        $gradeBreakdown = collect();
        if (Schema::hasTable('grade_levels')) {
            $gradeBreakdown = GradeLevel::select('id', 'name')
                ->withCount(['voters as total_students'])
                ->get()
                ->map(function ($grade) use ($activeElectionIds) {
                    $votedCount = 0;
                    if ($activeElectionIds->isNotEmpty()) {
                        $votedCount = Voter::where('grade_level_id', $grade->id)
                            ->whereHas('votes', function ($q) use ($activeElectionIds) {
                                $q->whereIn('election_id', $activeElectionIds);
                            })
                            ->count();
                    }
                    return [
                        'id'             => $grade->id,
                        'name'           => $grade->name,
                        'total_students' => $grade->total_students,
                        'voted_count'    => $votedCount,
                    ];
                });
        }

        $recentActivity = Schema::hasTable('votes')
            ? Vote::with(['voter', 'election'])
                ->select('voter_id', 'election_id', DB::raw('MAX(created_at) as created_at'))
                ->groupBy('voter_id', 'election_id')
                ->latest('created_at')
                ->take(5)
                ->get()
                ->map(function ($act) {
                    $act->time_ago = $act->created_at->diffForHumans();
                    return $act;
                })
            : collect();

        $votedVotersList = collect();
        if (Schema::hasTable('voter_activity_logs')) {
            $votedVotersList = VoterActivityLog::with(['voter.gradeLevel', 'voter.section'])
                ->latest()
                ->take(100)
                ->get()
                ->map(function ($log) {
                    return [
                        'voter_name'  => $log->voter
                            ? $log->voter->first_name . ' ' . $log->voter->last_name
                            : 'Unknown',
                        'grade'       => $log->voter?->gradeLevel?->name ?? '—',
                        'section'     => $log->voter?->section?->name ?? '—',
                        'action'      => $log->action,
                        'description' => $log->description ?? '—',
                        'election_id' => null,
                        'voted_at'    => $log->created_at->format('M d, Y h:i A'),
                    ];
                });
        }

        // ✅ Admin Activity Log
        $adminActivityLogs = collect();
        if (Schema::hasTable('admin_activity_logs')) {
            $adminActivityLogs = AdminActivityLog::with('user')
                ->latest()
                ->take(50)
                ->get()
                ->map(function ($log) {
                    return [
                        'admin_name'  => $log->user?->name ?? 'Unknown Admin',
                        'action'      => $log->action,
                        'description' => $log->description ?? '—',
                        'ip_address'  => $log->ip_address ?? '—',
                        'logged_at'   => $log->created_at->format('M d, Y h:i A'),
                    ];
                });
        }

        $showLiveResults = false;
        if (Schema::hasTable('settings')) {
            $showLiveResults = Setting::where('setting_key', 'real_time_results')->value('value') == '1';
        }

        return [
            'totalRegistered'      => $totalRegistered,
            'votesRecorded'        => $totalBallotsCast,
            'turnoutPercentage'    => (float) min($avgTurnoutPercentage, 100),
            'activeElectionsCount' => $activeElectionsCount,
            'activeElections'      => $activeElections,
            'gradeBreakdown'       => $gradeBreakdown,
            'recentActivity'       => $recentActivity,
            'votedVotersList'      => $votedVotersList,
            'adminActivityLogs'    => $adminActivityLogs,
            'showLiveResults'      => $showLiveResults,
            'serverTime'           => $now->format('M d, Y | h:i A'),
        ];
    }
}