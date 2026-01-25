<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use App\Models\User;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VoterController extends Controller
{
    public function index()
    {
        $voters = Voter::latest()->paginate(10);
        return view('dashboards.admin-dashboard.voters.index', compact('voters')); 
    }

    public function create()
    {
        // Fetch all grades
        $gradeLevels = GradeLevel::all();
        
        // Fetch all sections (we need 'id', 'name', and 'grade_level_id' for the JS filter)
        $sections = Section::all(['id', 'name', 'grade_level_id']);

        return view('dashboards.admin-dashboard.voters.create', compact('gradeLevels', 'sections'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'userID'      => 'required|string|unique:voters,userID',
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'section_id' => 'required|exists:sections,id',
            'email'       => 'nullable|email|unique:voters,email',
            'password'    => 'required|string|min:6',

            'photo_path'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('photo_path')) {
            $path = $request->file('photo_path')->store('voters', 'public'); 
            $validated['photo_path'] = $path;
        }

        $validated['password'] = Hash::make($request->password);

        Voter::create($validated);

        return redirect()->route('dashboard.voters.index')
                        ->with('success', 'Voter registered successfully.');
    }

    public function edit($id)
    {
        $voter = Voter::findOrFail($id);

        return view('dashboards.admin-dashboard.voters.edit', [
            'voter' => $voter,
            'gradeLevels' => GradeLevel::all(),
            'sections' => Section::all(),
        ]);
    }


    /**
     * Update the specified voter in storage.
     */
    public function update(Request $request, $id)
    {
        $voter = Voter::findOrFail($id);

        // 1. Validation
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:255'],
            'middle_name'    => ['nullable', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'grade_level_id' => ['required', 'exists:grade_levels,id'],
            'section_id'     => ['required', 'exists:sections,id'],
            
            // Allow the CURRENT user to keep their own email, but prevent duplicates otherwise
            'email'          => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($voter->id)],
            
            // Password is optional. If provided, min 6 chars.
            'password'       => ['nullable', 'string', 'min:6'],
            
            // Image validation (Max 5MB)
            'photo_path'     => ['nullable', 'image', 'max:5120'], 
        ]);

        // 2. Handle Image Upload
        if ($request->hasFile('photo_path')) {
            // Delete old image to save storage space
            if ($voter->photo_path && Storage::disk('public')->exists($voter->photo_path)) {
                Storage::disk('public')->delete($voter->photo_path);
            }

            // Store new image
            $path = $request->file('photo_path')->store('profile-photos', 'public');
            $voter->photo_path = $path;
        }

        // 3. Update Basic Fields
        // Note: We deliberately do NOT update 'userID' to ensure the Student ID is immutable.
        $voter->first_name = $validated['first_name'];
        $voter->middle_name = $validated['middle_name'];
        $voter->last_name = $validated['last_name'];
        $voter->grade_level_id = $validated['grade_level_id'];
        $voter->section_id = $validated['section_id'];
        $voter->email = $validated['email'];

        // 4. Update Password (Only if user typed a new one)
        if (!empty($validated['password'])) {
            $voter->password = Hash::make($validated['password']);
        }

        // 5. Save and Redirect
        $voter->save();

        return redirect()->route('dashboard.voters.index')
            ->with('success', 'Voter updated successfully.');
    }

    public function show(string $id)
    {
        // Eager load relationships to avoid N+1 queries
        $voter = Voter::with(['gradeLevel', 'section'])->findOrFail($id);

        return view('dashboards.admin-dashboard.voters.show', compact('voter'));
    }

    public function destroy(Voter $voter)
    {
        $voter->delete();
        return redirect()->route('dashboard.voters.index')->with('success', 'Voter deleted successfully.');
    }
}