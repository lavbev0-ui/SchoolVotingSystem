<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Position;
use App\Models\Election;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CandidateImportController extends Controller
{
    /**
     * Ipakita ang import form.
     */
    public function showForm()
    {
        $elections = Election::with('positions')->latest()->get();
        return view('admin.candidates.import', compact('elections'));
    }

    /**
     * I-process ang CSV upload at i-import ang candidates.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file'    => 'required|file|mimes:csv,txt|max:2048',
            'election_id' => 'required|exists:elections,id',
        ]);

        $election = Election::with('positions')->findOrFail($request->election_id);
        $file     = $request->file('csv_file');
        $path     = $file->getRealPath();

        // I-read ang CSV rows
        $rows   = array_map('str_getcsv', file($path));
        $header = array_shift($rows);

        // I-normalize ang headers
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            if (empty(array_filter($row))) continue;

            if (count($row) !== count($header)) {
                $errors[] = "Row " . ($index + 2) . ": Hindi tugma ang bilang ng columns, na-skip.";
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);

            // --- POSITION ---
            $positionTitle = trim($data['position'] ?? '');
            if (empty($positionTitle)) {
                $errors[] = "Row " . ($index + 2) . ": Walang position, na-skip.";
                $skipped++;
                continue;
            }

            $position = $election->positions()
                ->whereRaw('LOWER(title) = ?', [strtolower($positionTitle)])
                ->first();

            if (!$position) {
                $position = Position::create([
                    'election_id' => $election->id,
                    'title'       => $positionTitle,
                    'max_votes'   => 1,
                ]);
            }

            // --- FULL NAME ---
            $fullName  = trim($data['full name'] ?? $data['full_name'] ?? '');
            $nameParts = $this->parseName($fullName);

            if (empty($nameParts['first_name']) && empty($nameParts['last_name'])) {
                $errors[] = "Row " . ($index + 2) . ": Walang pangalan, na-skip.";
                $skipped++;
                continue;
            }

            // --- YEAR & SECTION ---
            $yearSection            = trim($data['year & section'] ?? $data['year_section'] ?? '');
            [$gradeLevel, $section] = $this->parseYearSection($yearSection);

            // --- PHOTO (Google Drive link) ---
            $photoPath = null;
            $photoUrl  = trim($data['photo'] ?? '');
            if (!empty($photoUrl)) {
                $photoPath = $this->downloadPhoto($photoUrl, $nameParts['first_name']);
            }

            // --- PARTY ---
            $party = trim(
                $data['party'] ??
                $data['party / organization'] ??
                $data['party/organization'] ??
                $data['organization'] ??
                'Independent'
            );
            if (empty($party)) $party = 'Independent';

            // --- SAVE CANDIDATE ---
            try {
                Candidate::create([
                    'position_id'    => $position->id,
                    'first_name'     => $nameParts['first_name'],
                    'last_name'      => $nameParts['last_name'],
                    'grade_level_id' => $gradeLevel?->id,
                    'section_id'     => $section?->id,
                    'party'          => $party,
                    'bio'            => trim($data['short bio'] ?? $data['bio'] ?? ''),
                    'photo_path'     => $photoPath,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                $skipped++;
            }
        }

        return back()->with([
            'success'       => "Import tapos na! {$imported} candidates ang na-import, {$skipped} ang na-skip.",
            'import_errors' => $errors,
        ]);
    }

    // -----------------------------------------------------------------------
    // HELPER METHODS
    // -----------------------------------------------------------------------

    private function parseName(string $fullName): array
    {
        if (str_contains($fullName, ',')) {
            [$last, $first] = explode(',', $fullName, 2);
            return [
                'first_name' => trim($first),
                'last_name'  => trim($last),
            ];
        }

        $parts = explode(' ', trim($fullName));
        return [
            'first_name' => trim($parts[0] ?? ''),
            'last_name'  => trim(implode(' ', array_slice($parts, 1))),
        ];
    }

    private function parseYearSection(string $yearSection): array
    {
        $gradeLevel = null;
        $section    = null;

        if (empty(trim($yearSection))) {
            return [$gradeLevel, $section];
        }

        preg_match('/(\d+)/', $yearSection, $gradeMatch);
        $gradeNumber = $gradeMatch[1] ?? null;

        if ($gradeNumber) {
            $gradeLevel = GradeLevel::whereRaw('LOWER(name) LIKE ?', ["%{$gradeNumber}%"])->first();
        }

        if (str_contains($yearSection, '-')) {
            $sectionName = trim(substr($yearSection, strpos($yearSection, '-') + 1));
            if (!empty($sectionName) && $gradeLevel) {
                // Exact match muna
                $section = Section::whereRaw('LOWER(name) = ?', [strtolower($sectionName)])
                    ->where('grade_level_id', $gradeLevel->id)
                    ->first();

                // Partial match kung walang exact
                if (!$section) {
                    $section = Section::whereRaw('LOWER(name) LIKE ?', [strtolower("%{$sectionName}%")])
                        ->where('grade_level_id', $gradeLevel->id)
                        ->first();
                }
            }
        }

        return [$gradeLevel, $section];
    }

    /**
     * UPDATED: I-download ang photo mula sa Google Drive.
     * Gumagana na sa public Drive folders.
     */
    private function downloadPhoto(string $url, string $firstName): ?string
    {
        try {
            if (str_contains($url, 'drive.google.com')) {
                $fileId = null;

                // Format: ?id=FILE_ID o &id=FILE_ID
                if (preg_match('/[?&]id=([-\w]{25,})/', $url, $matches)) {
                    $fileId = $matches[1];
                }
                // Format: /d/FILE_ID/
                elseif (preg_match('/\/d\/([-\w]{25,})/', $url, $matches)) {
                    $fileId = $matches[1];
                }
                // Fallback: hanapin ang mahabang alphanumeric string
                elseif (preg_match('/([-\w]{25,})/', $url, $matches)) {
                    $fileId = $matches[1];
                }

                if (!$fileId) {
                    Log::warning("CandidateImport: Hindi ma-extract ang Drive file ID mula sa: {$url}");
                    return null;
                }

                $url = "https://drive.google.com/uc?export=download&id={$fileId}";
            }

            // I-set ang proper headers para sa Google Drive download
            $context = stream_context_create([
                'http' => [
                    'method'          => 'GET',
                    'follow_location' => true,
                    'max_redirects'   => 5,
                    'timeout'         => 30,
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

            $contents = @file_get_contents($url, false, $context);

            if ($contents === false || strlen($contents) < 500) {
                Log::warning("CandidateImport: Hindi ma-download ang photo mula sa: {$url}");
                return null;
            }

            // I-validate kung valid image talaga
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($contents);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];

            if (!isset($extensions[$mimeType])) {
                Log::warning("CandidateImport: Invalid image type ({$mimeType}) mula sa: {$url}");
                return null;
            }

            $ext      = $extensions[$mimeType];
            $filename = 'candidates/' . Str::slug($firstName) . '-' . time() . '.' . $ext;
            Storage::disk('public')->put($filename, $contents);

            return $filename;

        } catch (\Exception $e) {
            Log::error("CandidateImport photo error: " . $e->getMessage());
            return null;
        }
    }
}