<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VoterController extends Controller
{
    private function getIdColumn()
    {
        return Schema::hasColumn('voters', 'student_id') ? 'student_id' : 'userID';
    }

    public function index(Request $request)
    {
        $search    = $request->input('search');
        $gradeId   = $request->input('grade_id');
        $sectionId = $request->input('section_id');
        $idColumn  = $this->getIdColumn();

        $voters = Voter::with(['gradeLevel', 'section'])
            ->when($search, function ($query, $search) use ($idColumn) {
                return $query->where(function ($q) use ($search, $idColumn) {
                    $q->where($idColumn, 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($gradeId, function ($query, $gradeId) {
                return $query->where('grade_level_id', $gradeId);
            })
            ->when($sectionId, function ($query, $sectionId) {
                return $query->where('section_id', $sectionId);
            })
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->paginate(15);

        $gradeLevels = GradeLevel::orderBy('name', 'asc')->get();
        $sections    = $gradeId
            ? Section::where('grade_level_id', $gradeId)->orderBy('name', 'asc')->get()
            : Section::orderBy('name', 'asc')->get();

        return view('dashboards.admin-dashboard.voters.index', compact('voters', 'search', 'gradeLevels', 'sections', 'gradeId', 'sectionId'));
    }

    public function getSections($gradeLevelId)
    {
        return response()->json(Section::where('grade_level_id', $gradeLevelId)->orderBy('name')->get());
    }

    public function downloadTemplate()
    {
        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['student_id','first_name','middle_name','last_name','email','phone_number','password','grade_level','section','photo_url']);
            fputcsv($handle, ['20261001','Juan','Santos','Dela Cruz','juan@example.com','09171234567','password123','Grade 12','STEM','']);
            fclose($handle);
        };
        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="voter_import_template.csv"',
        ]);
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('name', 'asc')->get();
        $sections    = Section::orderBy('name', 'asc')->get();
        return view('dashboards.admin-dashboard.voters.create', compact('gradeLevels', 'sections'));
    }

    public function store(Request $request)
    {
        $idColumn  = $this->getIdColumn();
        $validated = $request->validate([
            'student_id'     => ["required", "string", "unique:voters,{$idColumn}"],
            'email'          => "nullable|email|max:255|unique:voters,email",
            'phone_number'   => 'nullable|string|max:15',
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'section_id'     => 'required|exists:sections,id',
            'password'       => 'required|string|min:8',
            'photo_path'     => 'nullable|image|max:5120',
        ]);

        $data = [
            'first_name'       => $validated['first_name'],
            'middle_name'      => $validated['middle_name'] ?? null,
            'last_name'        => $validated['last_name'],
            'email'            => $validated['email'] ?? null,
            'phone_number'     => $this->formatPhoneNumber($validated['phone_number'] ?? ''),
            'grade_level_id'   => $validated['grade_level_id'],
            'section_id'       => $validated['section_id'],
            'password'         => Hash::make($validated['password']),
            $idColumn          => $validated['student_id'],
            'is_active'        => 1,
            'password_changed' => false,
        ];

        if ($request->hasFile('photo_path')) {
            $data['photo_path'] = $request->file('photo_path')->store('voters', 'public');
        }

        $voter = Voter::create($data);
        AdminDashboardController::logAction('created_voter', 'Created voter: ' . $voter->first_name . ' ' . $voter->last_name . ' (' . $voter->student_id . ')');
        return redirect()->route('admin.voters.index')->with('success', "Voter registered successfully.");
    }

    private function resolveVoterColumns(array $headers): array
    {
        $patterns = [
            'student_id'   => ['/student.?id/i', '/lrn/i', '/id.?number/i', '/student.?number/i'],
            'first_name'   => ['/first.?name/i', '/fname/i', '/given.?name/i', '/pangalan/i'],
            'middle_name'  => ['/middle.?name/i', '/mname/i', '/gitnang/i'],
            'last_name'    => ['/last.?name/i', '/lname/i', '/surname/i', '/apelyido/i', '/family.?name/i'],
            'email'        => ['/^email$/i', '/email.?addr/i', '/e.?mail/i'],
            'phone_number' => ['/phone/i', '/mobile/i', '/contact/i', '/numero/i', '/cp.?number/i'],
            'password'     => ['/password/i', '/pass/i'],
            'grade_level'  => ['/grade.?level/i', '/year.?level/i', '/grade$/i', '/year$/i', '/taon/i'],
            'section'      => ['/^section$/i', '/section.{0,10}strand/i', '/strand/i', '/section/i', '/klase/i'],
            'photo_url'    => ['/photo/i', '/picture/i', '/larawan/i', '/image/i', '/pic/i', '/drive/i'],
        ];

        $map = [];
        foreach ($patterns as $field => $regexList) {
            foreach ($regexList as $regex) {
                $idx = array_search(true, array_map(fn($h) => (bool) preg_match($regex, $h), $headers));
                if ($idx !== false) {
                    $map[$field] = $idx;
                    break;
                }
            }
        }
        return $map;
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt,xlsx,xls|max:10240']);

        try {
            $file   = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');

            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);

            $rawHeaders = fgetcsv($handle);
            if (!$rawHeaders) {
                return back()->with('error', 'CSV file is empty or unreadable.');
            }

            $headers = array_map(fn($h) => trim($h), $rawHeaders);
            $colMap  = $this->resolveVoterColumns($headers);

            $get = function (array $row, string $field) use ($colMap): string {
                return isset($colMap[$field]) && isset($row[$colMap[$field]])
                    ? trim($row[$colMap[$field]])
                    : '';
            };

            $gradeLevels = GradeLevel::all();
            $sections    = Section::all();
            $imported = 0;
            $skipped  = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) { $skipped++; continue; }

                $studentId = $get($row, 'student_id');
                if (empty($studentId)) { $skipped++; continue; }
                if (Voter::where('student_id', $studentId)->exists()) { $skipped++; continue; }

                $email = $get($row, 'email') ?: null;
                if ($email && Voter::where('email', $email)->exists()) { $skipped++; continue; }

                $gradeLevelId = null;
                $gradeRaw = $get($row, 'grade_level');
                if ($gradeRaw) {
                    $gradeNorm = strtolower(trim($gradeRaw));
                    $gradeObj  = $gradeLevels->first(fn($g) => strtolower(trim($g->name)) === $gradeNorm);
                    if (!$gradeObj) {
                        preg_match('/(\d+)/', $gradeRaw, $m);
                        $gradeNum = $m[1] ?? null;
                        if ($gradeNum) {
                            $gradeObj = $gradeLevels->first(fn($g) => (bool) preg_match('/\b' . $gradeNum . '\b/', $g->name));
                        }
                    }
                    $gradeLevelId = $gradeObj?->id;
                }

                $sectionId = null;
                $sectionRaw = $get($row, 'section');
                if ($sectionRaw) {
                    $sectionClean = trim(preg_replace('/\s*\(.*?\)/', '', $sectionRaw));
                    $sectionNorm  = strtolower($sectionClean);

                    $sectionObj = $sections->first(fn($s) =>
                        strtolower(trim($s->name)) === $sectionNorm &&
                        (!$gradeLevelId || $s->grade_level_id == $gradeLevelId)
                    );
                    if (!$sectionObj) {
                        $sectionObj = $sections->first(fn($s) =>
                            str_contains($sectionNorm, strtolower(trim($s->name))) &&
                            (!$gradeLevelId || $s->grade_level_id == $gradeLevelId)
                        );
                    }
                    if (!$sectionObj) {
                        $sectionObj = $sections->first(fn($s) => strtolower(trim($s->name)) === $sectionNorm);
                    }
                    $sectionId = $sectionObj?->id;
                }

                $password  = $get($row, 'password') ?: $studentId;
                $photoPath = null;
                $photoUrl  = $get($row, 'photo_url');

                if ($photoUrl && str_starts_with($photoUrl, 'http')) {
                    try {
                        $extension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $filename  = 'voters/' . $studentId . '_' . time() . '.' . $extension;
                        Storage::disk('public')->put($filename, Http::timeout(10)->get($photoUrl)->body());
                        $photoPath = $filename;
                    } catch (\Exception $e) {}
                }

                Voter::create([
                    'student_id'       => $studentId,
                    'first_name'       => $get($row, 'first_name'),
                    'middle_name'      => $get($row, 'middle_name') ?: null,
                    'last_name'        => $get($row, 'last_name'),
                    'email'            => $email,
                    'phone_number'     => $this->formatPhoneNumber($get($row, 'phone_number')),
                    'password'         => Hash::make($password),
                    'grade_level_id'   => $gradeLevelId,
                    'section_id'       => $sectionId,
                    'photo_path'       => $photoPath,
                    'is_active'        => true,
                    'password_changed' => false,
                ]);
                $imported++;
            }

            fclose($handle);
            AdminDashboardController::logAction('imported_voters', "Imported {$imported} voters, skipped {$skipped}.");
            return back()->with('success', "Import complete! {$imported} voters added, {$skipped} skipped.");

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $voter = Voter::with(['gradeLevel', 'section', 'votes.candidate.position'])->findOrFail($id);
        return view('dashboards.admin-dashboard.voters.show', compact('voter'));
    }

    public function edit($id)
    {
        $voter       = Voter::findOrFail($id);
        $gradeLevels = GradeLevel::orderBy('name', 'asc')->get();
        $sections    = Section::orderBy('name', 'asc')->get();
        return view('dashboards.admin-dashboard.voters.edit', compact('voter', 'gradeLevels', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $voter    = Voter::findOrFail($id);
        $idColumn = $this->getIdColumn();
        $validated = $request->validate([
            'student_id'     => ['required', 'string', Rule::unique('voters', $idColumn)->ignore($voter->id)],
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => ['nullable', 'email', 'max:255', Rule::unique('voters', 'email')->ignore($voter->id)],
            'phone_number'   => 'nullable|string|max:15',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'section_id'     => 'required|exists:sections,id',
            'is_active'      => 'required|boolean',
            'password'       => 'nullable|string|min:8',
            'photo_path'     => 'nullable|image|max:5120',
        ]);
        $voter->{$idColumn}    = $validated['student_id'];
        $voter->first_name     = $validated['first_name'];
        $voter->middle_name    = $validated['middle_name'] ?? null;
        $voter->last_name      = $validated['last_name'];
        $voter->email          = $validated['email'] ?? null;
        $voter->phone_number   = $this->formatPhoneNumber($validated['phone_number'] ?? '');
        $voter->grade_level_id = $validated['grade_level_id'];
        $voter->section_id     = $validated['section_id'];
        $voter->is_active      = $validated['is_active'];
        if ($request->filled('password')) {
            $voter->password         = Hash::make($validated['password']);
            $voter->password_changed = false;
        }
        if ($request->hasFile('photo_path')) {
            if ($voter->photo_path) Storage::disk('public')->delete($voter->photo_path);
            $voter->photo_path = $request->file('photo_path')->store('voters', 'public');
        }
        $voter->save();
        AdminDashboardController::logAction('updated_voter', 'Updated voter: ' . $voter->first_name . ' ' . $voter->last_name . ' (' . $voter->student_id . ')');
        return redirect()->route('admin.voters.index')->with('success', 'Voter updated successfully!');
    }

    public function destroy($id)
    {
        $voter = Voter::findOrFail($id);
        AdminDashboardController::logAction('deleted_voter', 'Deleted voter: ' . $voter->first_name . ' ' . $voter->last_name . ' (' . $voter->student_id . ')');
        if ($voter->photo_path) Storage::disk('public')->delete($voter->photo_path);
        $voter->delete();
        return redirect()->route('admin.voters.index')->with('success', 'Voter record purged.');
    }

    public function resetPassword(Voter $voter)
    {
        $idColumn = $this->getIdColumn();
        $voter->update(['password' => Hash::make($voter->{$idColumn}), 'password_changed' => false]);
        AdminDashboardController::logAction('reset_voter_password', 'Reset password of voter: ' . $voter->first_name . ' ' . $voter->last_name . ' (' . $voter->student_id . ')');
        return back()->with('success', "Password reset. Default password is now the voter's Student ID.");
    }

    private function formatPhoneNumber(string $number): ?string
    {
        $number = preg_replace('/\D/', '', $number);
        if (empty($number)) return null;
        if (str_starts_with($number, '09') && strlen($number) === 11) return '+63' . substr($number, 1);
        if (str_starts_with($number, '9')  && strlen($number) === 10)  return '+63' . $number;
        if (str_starts_with($number, '63'))                             return '+' . $number;
        return '+' . $number;
    }
}