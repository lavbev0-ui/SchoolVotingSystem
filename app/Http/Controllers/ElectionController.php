<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Election; 
use App\Models\Position; 
use App\Models\Candidate;
use App\Models\GradeLevel;
use App\Models\Section;
use App\Models\Voter;

class ElectionController extends Controller
{
    public function index()
    {
        $totalVoters = Voter::where('is_active', true)->count();

        $elections = Election::query()
            ->withCount(['votes', 'positions', 'candidates'])
            ->latest('start_at')
            ->paginate(10);

        return view('dashboards.admin-dashboard.elections.index', compact('elections', 'totalVoters'));
    }
    
    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        $sections = Section::select('id', 'name', 'grade_level_id')->orderBy('name')->get();

        return view('dashboards.admin-dashboard.elections.create', compact(
            'gradeLevels',
            'sections'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'eligibility_type' => 'required|string|in:all,grade_level,section',

            'selected_grades' => 'nullable|array',
            'selected_grades.*' => 'integer|exists:grade_levels,id',
            'selected_sections' => 'nullable|array',
            'selected_sections.*' => 'integer|exists:sections,id',

            'positions' => 'required|array',
            'positions.*.title' => 'required|string',
            'positions.*.description' => 'nullable|string',
            'positions.*.max_selection' => 'required|integer|min:1',
            'positions.*.candidates' => 'nullable|array',
            'positions.*.candidates.*.name' => 'required|string',
            'positions.*.candidates.*.grade_level_id' => 'nullable|integer|exists:grade_levels,id',
            'positions.*.candidates.*.section_id' => 'nullable|integer|exists:sections,id',
            'positions.*.candidates.*.bio' => 'nullable|string',
            'positions.*.candidates.*.party' => 'nullable|string',
            'positions.*.candidates.*.platform' => 'nullable|string',
        ]);

        $values = []; // Default to empty array
        
        if ($request->eligibility_type === 'grade_level') {
            // FIX: Force strings ["7", "8"] into integers [7, 8]
            $rawValues = $request->selected_grades ?? [];
            $values = array_map('intval', $rawValues); 
            
        } elseif ($request->eligibility_type === 'section') {
            // FIX: Force strings to integers here too
            $rawValues = $request->selected_sections ?? [];
            $values = array_map('intval', $rawValues);
        }

        $election = Election::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'bio' => $request->bio,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'eligibility_type' => $request->eligibility_type,
            'eligibility_metadata' => $values, // Now saving real integers!
        ]);

        foreach ($request->positions as $pIndex => $pos) {
            $position = $election->positions()->create([
                'title' => $pos['title'],
                'description' => $pos['description'] ?? null,
                'max_selection' => $pos['max_selection'],
            ]);

            if (!empty($pos['candidates'])) {
                foreach ($pos['candidates'] as $cIndex => $cand) {

                    $photoPath = null;
                    if ($request->hasFile("positions.$pIndex.candidates.$cIndex.photo")) {
                        $photoPath = $request->file("positions.$pIndex.candidates.$cIndex.photo")
                            ->store('candidates', 'public'); 
                    }

                    $position->candidates()->create([
                        'name' => $cand['name'],
                        'grade_level_id' => $cand['grade_level_id'] ?? null,
                        'section_id' => $cand['section_id'] ?? null,
                        'bio' => $cand['bio'] ?? null,
                        'party' => $cand['party'] ?? null,
                        'platform' => $cand['platform'] ?? null, 
                        'photo_path' => $photoPath,
                    ]);
                }
            }
        }

        return redirect()
            ->route('dashboard.elections.index')
            ->with('success', 'Election created successfully!');
    }

    public function show(Election $election)
    {
        if ($election->user_id !== auth()->id()) {
            abort(403);
        }

        $election->load('positions.candidates');

        return view('dashboards.admin-dashboard.elections.show', compact('election'));
    }

    public function edit(Election $election)
    {
        if ($election->user_id !== auth()->id()) {
            abort(403);
        }
            $election->load(['positions.candidates']); 
            
            // Get master lists for dropdowns
            $gradeLevels = GradeLevel::all(); 
            $sections = Section::all();

            return view('dashboards.admin-dashboard.elections.edit', compact('election', 'gradeLevels', 'sections'));
        }

    public function update(Request $request, Election $election)
    {
        $data = json_decode($request->input('election_data'), true);

        if (!$data) {
            return back()->withErrors(['error' => 'Invalid data format received.']);
        }

        DB::transaction(function () use ($election, $data) {

            /* -------------------------------
            | A. Normalize Eligibility Metadata
            |-------------------------------*/
            $metadata = null;

            if ($data['eligibilityType'] === 'grade_level') {
                $metadata = [
                    'grades' => array_map('intval', $data['selectedGrades'] ?? [])
                ];
            }

            if ($data['eligibilityType'] === 'section') {
                $metadata = [
                    'sections' => array_map('intval', $data['selectedSections'] ?? [])
                ];
            }

            /* -------------------------------
            | B. Update Election
            |-------------------------------*/
            $election->update([
                'title' => $data['title'],
                'bio' => $data['description'] ?? null,
                'start_at' => $data['startDate']
                    ? \Carbon\Carbon::parse($data['startDate'])
                    : null,
                'end_at' => $data['endDate']
                    ? \Carbon\Carbon::parse($data['endDate'])
                    : null,
                'eligibility_type' => $data['eligibilityType'],
                'eligibility_metadata' => $metadata,
            ]);

            /* -------------------------------
            | C. Sync Positions
            |-------------------------------*/
            $existingPositionIds = [];

            foreach ($data['positions'] as $pos) {

                $position = is_numeric($pos['id'])
                    ? $election->positions()->find($pos['id'])
                    : null;

                if (!$position) {
                    $position = $election->positions()->create([
                        'title' => $pos['name'],
                        'description' => $pos['description'] ?? null,
                        'max_selection' => $pos['maxSelections'],
                    ]);
                } else {
                    $position->update([
                        'title' => $pos['name'],
                        'description' => $pos['description'] ?? null,
                        'max_selection' => $pos['maxSelections'],
                    ]);
                }

                $existingPositionIds[] = $position->id;

                /* -------------------------------
                | D. Sync Candidates
                |-------------------------------*/
                $incomingCandidates = $data['candidates'][$pos['id']] ?? [];
                $this->syncCandidates($position, $incomingCandidates);
            }

            /* -------------------------------
            | E. Remove Deleted Positions
            |-------------------------------*/
            $election->positions()
                ->whereNotIn('id', $existingPositionIds)
                ->delete();
        });

        return redirect()
            ->route('dashboard.elections.index')
            ->with('success', 'Election updated successfully.');
    }

    protected function syncCandidates(Position $position, array $candidates)
    {
        $existingIds = [];

        foreach ($candidates as $cand) {

            $candidate = is_numeric($cand['id'])
                ? $position->candidates()->find($cand['id'])
                : null;

            if (!$candidate) {
                $candidate = $position->candidates()->create([
                    'name' => $cand['name'],
                    'grade_level_id' => $cand['grade_level_id'] ?? null,
                    'section_id' => $cand['section_id'] ?? null,
                    'party' => $cand['party'] ?? null,
                    'platform' => $cand['platform'] ?? null,
                ]);
            } else {
                $candidate->update([
                    'name' => $cand['name'],
                    'grade_level_id' => $cand['grade_level_id'] ?? null,
                    'section_id' => $cand['section_id'] ?? null,
                    'party' => $cand['party'] ?? null,
                    'platform' => $cand['platform'] ?? null,
                ]);
            }

            $existingIds[] = $candidate->id;
        }

        // Delete removed candidates
        $position->candidates()
            ->whereNotIn('id', $existingIds)
            ->delete();
    }

    public function destroy(Election $election)
    {

        $election->delete();

        return redirect()->route('dashboards.admin-dashboard.elections.index')
            ->with('status', 'Election deleted successfully.');
    }
}