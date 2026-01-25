<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Voter;


class ResultController extends Controller
{
    public function index()
    {
        // Get total voters for percentage calculation
        $totalVoters = Voter::where('is_active', true)->count();
        
        // Get elections with vote counts
        $elections = Election::withCount('votes')->latest('end_at')->get();

        return view('dashboards.admin-dashboard.results.index', compact('elections', 'totalVoters'));
    }
}
