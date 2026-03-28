<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Election; 
use App\Models\Position; 
use App\Models\Candidate;
use App\Models\GradeLevel;
use App\Models\Section;
use App\Models\Voter;
use App\Models\Vote;

class ElectionController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $query = Election::query();
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $elections = $query->withCount(['positions', 'candidates'])
            ->latest()
            ->paginate(10);

        $elections->getCollection()->transform(function ($election) {
            $election->votes_count = Vote::where('election_id', $election->id)
                ->distinct('voter_id')
                ->count('voter_id');
            return $election;
        });

        return view('dashboards.admin-dashboard.elections.index', compact('elections'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('name', 'asc')->get();

        $sections = Section::with('gradeLevel')
            ->orderBy('name', 'asc')
            ->get()
            ->map(fn($s) => [
                'id'             => $s->id,
                'name'           => $s->name,
                'grade_level_id' => $s->grade_level_id,
                'grade_name'     => $s->gradeLevel?->name ?? '',
            ])
            ->values();

        return view('dashboards.admin-dashboard.elections.create', compact('gradeLevels', 'sections'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'                                   => 'required|string|max:255',
            'start_at'                                => 'required|date',
            'end_at'                                  => 'required|date|after:start_at',
            'eligibility_type'                        => 'required|in:all,grade_level,section',
            'positions'                               => 'required|array|min:1',
            'positions.*.title'                       => 'required|string|max:255',
            'positions.*.max_selection'               => 'required|integer|min:1',
            'positions.*.candidates'                  => 'required|array|min:1',
            'positions.*.candidates.*.first_name'     => 'required|string|max:255',
            'positions.*.candidates.*.last_name'      => 'required|string|max:255',
            'positions.*.candidates.*.grade_level_id' => 'required|integer',
            'positions.*.candidates.*.section_id'     => 'required|integer',
            'positions.*.candidates.*.party'          => 'nullable|string|max:255',
            'positions.*.candidates.*.bio'            => 'nullable|string',
            'positions.*.candidates.*.manifesto'      => 'nullable|string',
        ]);

        // ---------------------------------------------------------------
        // STEP 1: I-process muna lahat ng photos BAGO mag-transaction.
        // Kaya hindi na mag-timeout ang DB transaction dahil wala nang
        // HTTP requests sa loob nito.
        // ---------------------------------------------------------------
        $resolvedPhotos = []; // key: "pIndex-cIndex" => photo path or null

        foreach ($request->positions as $pIndex => $pos) {
            foreach ($pos['candidates'] ?? [] as $cIndex => $cand) {
                $photoPath = null;

                if ($request->hasFile("positions.$pIndex.candidates.$cIndex.photo")) {
                    // Regular file upload — mabilis, okay sa labas ng transaction
                    $photoPath = $request
                        ->file("positions.$pIndex.candidates.$cIndex.photo")
                        ->store('candidates', 'public');

                } elseif (!empty($cand['drive_photo_url'])) {
                    // Google Drive download — DITO ang bottleneck dati.
                    // Ngayon nasa labas na ng transaction kaya hindi mag-rollback
                    // ang DB pag nag-timeout.
                    $photoPath = $this->downloadDrivePhoto(
                        $cand['drive_photo_url'],
                        $cand['first_name'] ?? 'candidate'
                    );
                }

                $resolvedPhotos["$pIndex-$cIndex"] = $photoPath;
            }
        }

        // ---------------------------------------------------------------
        // STEP 2: DB transaction — pure database operations na lang,
        // walang HTTP calls, hindi na mag-timeout.
        // ---------------------------------------------------------------
        try {
            return DB::transaction(function () use ($request, $resolvedPhotos) {
                $metadata = null; 
                if ($request->eligibility_type === 'grade_level') {
                    $metadata = ['grades' => array_map('intval', $request->selected_grades ?? [])];
                } elseif ($request->eligibility_type === 'section') {
                    $metadata = ['sections' => array_map('intval', $request->selected_sections ?? [])];
                }

                $election = Election::create([
                    'user_id'              => Auth::id() ?? 1,
                    'title'                => $request->title,
                    'description'          => $request->description,
                    'start_at'             => $request->start_at,
                    'end_at'               => $request->end_at,
                    'eligibility_type'     => $request->eligibility_type,
                    'eligibility_metadata' => $metadata,
                    'is_active'            => 1,
                ]);

                foreach ($request->positions as $pIndex => $pos) {
                    $position = $election->positions()->create([
                        'title'         => $pos['title'],
                        'max_selection' => $pos['max_selection'] ?? 1, 
                        'description'   => $pos['description'] ?? null,
                    ]);

                    if (isset($pos['candidates']) && is_array($pos['candidates'])) {
                        foreach ($pos['candidates'] as $cIndex => $cand) {
                            // Kunin ang naka-resolve na photo path mula sa STEP 1
                            $photoPath = $resolvedPhotos["$pIndex-$cIndex"] ?? null;

                            $position->candidates()->create([
                                'first_name'     => $cand['first_name'],
                                'middle_name'    => $cand['middle_name'] ?? null,
                                'last_name'      => $cand['last_name'],
                                'grade_level_id' => $cand['grade_level_id'], 
                                'section_id'     => $cand['section_id'],
                                'party'          => $cand['party'] ?? 'Independent',
                                'photo_path'     => $photoPath, 
                                'manifesto'      => $cand['manifesto'] ?? 'No platform provided.',
                                'bio'            => $cand['bio'] ?? 'No background provided.',
                            ]);
                        }
                    }
                }

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true]);
                }

                return redirect()->route('admin.elections.index')
                    ->with('success', 'Election deployed successfully!');
            });

        } catch (\Exception $e) {
            Log::error("Election Store Error: " . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    /**
     * SHOW: Para sa LIVE TALLYING — may History Log na.
     */
    public function show(Election $election)
    {
        $election->load(['positions.candidates' => function ($query) {
            $query->withCount('votes'); 
        }]);
        
        $election->total_voters = Voter::count();
        $election->voted_count  = Vote::where('election_id', $election->id)
            ->distinct('voter_id')
            ->count('voter_id');

        $votedVotersList = Vote::with(['voter.gradeLevel', 'voter.section'])
            ->where('election_id', $election->id)
            ->select('voter_id', 'election_id', DB::raw('MIN(created_at) as voted_at'))
            ->groupBy('voter_id', 'election_id')
            ->latest('voted_at')
            ->get()
            ->map(function ($vote) {
                return [
                    'voter_name' => $vote->voter
                        ? $vote->voter->first_name . ' ' . $vote->voter->last_name
                        : 'Unknown',
                    'grade'    => $vote->voter?->gradeLevel?->name ?? '—',
                    'section'  => $vote->voter?->section?->name ?? '—',
                    'voted_at' => $vote->voted_at
                        ? \Carbon\Carbon::parse($vote->voted_at)->format('M d, Y h:i A')
                        : '—',
                ];
            });

        return view('dashboards.admin-dashboard.elections.show', compact('election', 'votedVotersList'));
    }

    /**
     * DESTROY
     */
    public function destroy(Election $election)
    {
        try {
            return DB::transaction(function () use ($election) {
                foreach ($election->positions as $position) {
                    foreach ($position->candidates as $candidate) {
                        if ($candidate->photo_path) {
                            Storage::disk('public')->delete($candidate->photo_path);
                        }
                    }
                }
                $election->delete();
                return redirect()->route('admin.elections.index')
                    ->with('success', 'Election removed successfully.');
            });
        } catch (\Exception $e) {
            Log::error("Election Delete Error: " . $e->getMessage());
            return back()->with('error', 'Failed to delete election.');
        }
    }

    // -----------------------------------------------------------------------
    // HELPER: I-download ang photo mula sa Google Drive
    // Bawasan ang timeout sa 10s (dati 30s) para hindi masyadong matagal
    // -----------------------------------------------------------------------
    private function downloadDrivePhoto(string $url, string $firstName): ?string
    {
        try {
            $fileId = null;

            if (preg_match('/[?&]id=([-\w]{25,})/', $url, $m)) {
                $fileId = $m[1];
            } elseif (preg_match('/\/d\/([-\w]{25,})/', $url, $m)) {
                $fileId = $m[1];
            } elseif (preg_match('/thumbnail\?id=([-\w]{25,})/', $url, $m)) {
                $fileId = $m[1];
            } elseif (preg_match('/([-\w]{25,})/', $url, $m)) {
                $fileId = $m[1];
            }

            if (!$fileId) return null;

            $downloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}";

            $context = stream_context_create([
                'http' => [
                    'method'          => 'GET',
                    'follow_location' => true,
                    'max_redirects'   => 5,
                    'timeout'         => 10, // Bawasan sa 10s (dati 30s)
                    'header'          => implode("\r\n", [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                    ]),
                ],
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $contents = @file_get_contents($downloadUrl, false, $context);

            if ($contents === false || strlen($contents) < 500) {
                Log::warning("ElectionController: Hindi ma-download ang Drive photo: {$downloadUrl}");
                return null;
            }

            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($contents);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];

            if (!isset($extensions[$mimeType])) {
                Log::warning("ElectionController: Invalid image type ({$mimeType}) mula sa Drive");
                return null;
            }

            $ext      = $extensions[$mimeType];
            $filename = 'candidates/' . Str::slug($firstName) . '-' . time() . '.' . $ext;
            Storage::disk('public')->put($filename, $contents);

            return $filename;

        } catch (\Exception $e) {
            Log::error("ElectionController Drive photo error: " . $e->getMessage());
            return null;
        }
    }
}