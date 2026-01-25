<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // 1. Total Elections & Active Count
        $totalElections = Election::count();
        $activeElectionsCount = Election::where('status', 'active')->count();

        // 2. Total Votes (Across all elections)
        $totalVotes = Vote::count();

        // 3. Registered Students (Voters)
        $totalVoters = Voter::count();
        $activeVoters = Voter::where('is_active', true)->count();

        // 4. Voter Turnout (Unique voters who have voted / Total active voters)
        // We assume if a voter exists in the 'votes' table, they participated.
        $participatedVoters = Vote::distinct('voter_id')->count('voter_id');
        $turnoutPercentage = $totalVoters > 0 
            ? ($participatedVoters / $totalVoters) * 100 
            : 0;

        // 5. Active Elections List (with vote counts)
        $activeElectionsList = Election::where('status', 'active')
            ->withCount('votes') // counts rows in 'votes' table for this election
            ->latest('start_at')
            ->take(5)
            ->get();

        // 6. Recent Activity Feed (Mixing Votes and Elections)
        // We fetch the latest 3 items from different tables and merge them for the feed
        $latestVotes = Vote::with(['election', 'position'])->latest()->take(3)->get();
        $latestElections = Election::latest()->take(3)->get();
        
        // Merge and sort by date descending
        $recentActivity = $latestVotes->concat($latestElections)
            ->sortByDesc('created_at')
            ->take(5);

        return view('dashboards.admin-dashboard.dashboard', compact(
            'totalElections',
            'activeElectionsCount',
            'totalVotes',
            'totalVoters',
            'activeVoters',
            'turnoutPercentage',
            'activeElectionsList',
            'recentActivity'
        ));
    }
}